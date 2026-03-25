#!/usr/bin/env python3
"""
Generate synthetic RADM detections and ground-truth labels for chart demos.

Creates data in both tables:
- radm_detections
- radm_detection_labels

This is test data generation only and does not change RADM production logic.
"""

import argparse
import json
import random
import sys
from datetime import datetime

import mysql.connector


EXPECTED_TIMES = {
    "beginner": 23.0,
    "intermediate": 31.0,
    "advanced": 39.0,
}

TIME_WEIGHT = 0.35
ACCURACY_WEIGHT = 0.35
BKT_WEIGHT = 0.30
RAI_THRESHOLD = 0.60
WINDOW_SIZE = 5


def parse_args():
    parser = argparse.ArgumentParser(description="Generate synthetic RADM detections and labels")

    parser.add_argument("--host", default="localhost", help="MySQL host")
    parser.add_argument("--port", type=int, default=3306, help="MySQL port")
    parser.add_argument("--database", default="aralsipnayandb", help="MySQL database")
    parser.add_argument("--user", default="root", help="MySQL username")
    parser.add_argument("--password", default="", help="MySQL password")

    parser.add_argument("--records", type=int, default=500, help="Number of synthetic detection rows")
    parser.add_argument("--seed", type=int, default=20260321, help="Random seed")
    parser.add_argument("--actual-positive-rate", type=float, default=0.35, help="Rate of actual random-answering labels")
    parser.add_argument("--label-noise-rate", type=float, default=0.03, help="Probability to flip actual label after generation")
    parser.add_argument(
        "--target-recall",
        type=float,
        default=0.90,
        help="Target true positive rate used to simulate predicted positives",
    )
    parser.add_argument(
        "--target-specificity",
        type=float,
        default=0.90,
        help="Target true negative rate used to simulate predicted negatives",
    )
    parser.add_argument("--windows-per-session", type=int, default=6, help="How many windows per simulated session")

    parser.add_argument(
        "--clear-existing-sim",
        action="store_true",
        help="Delete previously simulated rows matching sim session prefix before inserting new rows",
    )
    parser.add_argument("--session-prefix", default="sim_radm", help="Prefix used for synthetic session_id")
    parser.add_argument("--dry-run", action="store_true", help="Preview generation stats without inserting")

    return parser.parse_args()


def clamp(value, low, high):
    return max(low, min(high, value))


def choose_difficulty():
    levels = ["beginner", "intermediate", "advanced"]
    weights = [0.35, 0.45, 0.20]
    return random.choices(levels, weights=weights, k=1)[0]


def build_flags(actual_random):
    if actual_random:
        time_flag = 1 if random.random() < 0.75 else 0
        accuracy_flag = 1 if random.random() < 0.70 else 0
        bkt_flag = 1 if random.random() < 0.55 else 0
    else:
        time_flag = 1 if random.random() < 0.15 else 0
        accuracy_flag = 1 if random.random() < 0.20 else 0
        bkt_flag = 1 if random.random() < 0.10 else 0

    return time_flag, accuracy_flag, bkt_flag


def build_flags_for_prediction(pred_positive):
    # Keep flags logically aligned with the RAI threshold.
    positive_patterns = [
        (1, 1, 0),  # 0.70
        (1, 0, 1),  # 0.65
        (0, 1, 1),  # 0.65
        (1, 1, 1),  # 1.00
    ]
    negative_patterns = [
        (0, 0, 0),  # 0.00
        (1, 0, 0),  # 0.35
        (0, 1, 0),  # 0.35
        (0, 0, 1),  # 0.30
    ]

    if pred_positive:
        return random.choice(positive_patterns)

    return random.choice(negative_patterns)


def build_response_times(expected_time, time_flag):
    response_times = []
    threshold = expected_time * 0.4

    for _ in range(WINDOW_SIZE):
        if time_flag:
            value = random.uniform(max(1.0, threshold * 0.25), max(1.5, threshold * 0.95))
        else:
            value = random.uniform(max(1.0, expected_time * 0.55), expected_time * 1.50)
        response_times.append(round(value, 2))

    avg_time = round(sum(response_times) / len(response_times), 2)
    return response_times, avg_time, round(threshold, 2)


def build_correctness(accuracy_flag):
    if accuracy_flag:
        correct_count = random.choice([0, 1])
    else:
        correct_count = random.choice([2, 3, 4, 5])

    correctness = [True] * correct_count + [False] * (WINDOW_SIZE - correct_count)
    random.shuffle(correctness)
    accuracy = round(correct_count / WINDOW_SIZE, 4)

    return correctness, correct_count, accuracy


def build_bkt_values(bkt_flag, correctness):
    if bkt_flag:
        bkt_probability = round(random.uniform(0.70, 0.95), 4)
        consecutive_wrong = random.randint(3, 5)
        # Encourage ending streak of wrong answers when bkt_flag is true.
        for i in range(1, min(consecutive_wrong, WINDOW_SIZE) + 1):
            correctness[-i] = False
    else:
        bkt_probability = round(random.uniform(0.25, 0.85), 4)
        consecutive_wrong = 0
        for v in reversed(correctness):
            if v is False:
                consecutive_wrong += 1
            else:
                break

    return bkt_probability, consecutive_wrong


def calculate_rai(time_flag, accuracy_flag, bkt_flag):
    score = (TIME_WEIGHT * time_flag) + (ACCURACY_WEIGHT * accuracy_flag) + (BKT_WEIGHT * bkt_flag)
    return round(score, 4)


def maybe_flip_label(actual_label, noise_rate):
    if random.random() < noise_rate:
        return 1 - actual_label
    return actual_label


def choose_prediction_from_actual(actual_label, target_recall, target_specificity):
    if actual_label == 1:
        return 1 if random.random() < target_recall else 0

    return 0 if random.random() < target_specificity else 1


def connect_db(args):
    return mysql.connector.connect(
        host=args.host,
        port=args.port,
        database=args.database,
        user=args.user,
        password=args.password,
        charset="utf8mb4",
    )


def clear_existing_sim_data(connection, session_prefix):
    cursor = connection.cursor()
    cursor.execute(
        """
        DELETE l FROM radm_detection_labels l
        INNER JOIN radm_detections d ON d.id = l.detection_id
        WHERE d.session_id LIKE %s
        """,
        (f"{session_prefix}_%",),
    )
    deleted_labels = cursor.rowcount

    cursor.execute(
        "DELETE FROM radm_detections WHERE session_id LIKE %s",
        (f"{session_prefix}_%",),
    )
    deleted_detections = cursor.rowcount

    connection.commit()
    cursor.close()

    return deleted_detections, deleted_labels


def generate_and_insert(connection, args):
    insert_detection_sql = """
        INSERT INTO radm_detections
        (
            session_id,
            student_id,
            assessment_id,
            window_start_index,
            window_end_index,
            question_ids,
            avg_response_time,
            expected_time,
            time_threshold,
            time_flag,
            correct_count,
            total_count,
            accuracy,
            accuracy_flag,
            bkt_probability,
            consecutive_wrong,
            bkt_flag,
            rai_score,
            intervention_triggered,
            intervention_acknowledged,
            difficulty_level,
            response_times,
            correctness,
            created_at,
            updated_at
        )
        VALUES
        (
            %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
            %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
            %s, %s, %s, %s, %s
        )
    """

    insert_label_sql = """
        INSERT INTO radm_detection_labels
        (
            detection_id,
            actual_random_answering,
            label_source,
            labeled_by,
            labeled_at,
            notes,
            created_at,
            updated_at
        )
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """

    now = datetime.now()
    detection_cursor = connection.cursor()
    label_cursor = connection.cursor()

    stats = {
        "inserted": 0,
        "actual_positive": 0,
        "actual_negative": 0,
        "pred_positive": 0,
        "pred_negative": 0,
    }

    for i in range(args.records):
        session_no = (i // args.windows_per_session) + 1
        window_no = (i % args.windows_per_session) + 1

        session_id = f"{args.session_prefix}_{session_no:06d}"
        student_id = 900000 + session_no
        assessment_id = f"SIM-{session_no:06d}"

        difficulty = choose_difficulty()
        expected_time = EXPECTED_TIMES[difficulty]

        base_actual = 1 if random.random() < args.actual_positive_rate else 0
        actual = maybe_flip_label(base_actual, args.label_noise_rate)

        simulated_prediction = choose_prediction_from_actual(
            actual,
            args.target_recall,
            args.target_specificity,
        )
        time_flag, accuracy_flag, bkt_flag = build_flags_for_prediction(simulated_prediction == 1)

        response_times, avg_time, time_threshold = build_response_times(expected_time, time_flag)
        correctness, correct_count, accuracy = build_correctness(accuracy_flag)
        bkt_probability, consecutive_wrong = build_bkt_values(bkt_flag, correctness)

        rai_score = calculate_rai(time_flag, accuracy_flag, bkt_flag)
        intervention_triggered = 1 if rai_score >= RAI_THRESHOLD else 0

        window_end_index = window_no * WINDOW_SIZE
        window_start_index = window_end_index - WINDOW_SIZE + 1

        question_ids = [f"SIMQ-{session_no:06d}-{window_end_index - 4 + j}" for j in range(WINDOW_SIZE)]

        notes = (
            f"simulated dataset; base_actual={base_actual}; noise_rate={args.label_noise_rate:.2f}; "
            f"difficulty={difficulty}; rai={rai_score:.4f}"
        )

        detection_values = (
            session_id,
            student_id,
            assessment_id,
            window_start_index,
            window_end_index,
            json.dumps(question_ids),
            avg_time,
            expected_time,
            time_threshold,
            time_flag,
            correct_count,
            WINDOW_SIZE,
            accuracy,
            accuracy_flag,
            bkt_probability,
            consecutive_wrong,
            bkt_flag,
            rai_score,
            intervention_triggered,
            0,
            difficulty,
            json.dumps(response_times),
            json.dumps(correctness),
            now,
            now,
        )

        detection_cursor.execute(insert_detection_sql, detection_values)
        detection_id = detection_cursor.lastrowid

        label_values = (
            detection_id,
            actual,
            "simulated",
            None,
            now,
            notes,
            now,
            now,
        )
        label_cursor.execute(insert_label_sql, label_values)

        stats["inserted"] += 1
        if actual == 1:
            stats["actual_positive"] += 1
        else:
            stats["actual_negative"] += 1

        if intervention_triggered == 1:
            stats["pred_positive"] += 1
        else:
            stats["pred_negative"] += 1

    connection.commit()
    detection_cursor.close()
    label_cursor.close()

    return stats


def preview_generation(args):
    stats = {
        "records_requested": args.records,
        "estimated_sessions": (args.records // args.windows_per_session) + (1 if args.records % args.windows_per_session else 0),
        "actual_positive_rate": args.actual_positive_rate,
        "label_noise_rate": args.label_noise_rate,
        "target_recall": args.target_recall,
        "target_specificity": args.target_specificity,
        "session_prefix": args.session_prefix,
    }
    return stats


def main():
    args = parse_args()

    if args.records <= 0:
        print("--records must be greater than 0")
        sys.exit(1)

    if not (0.0 <= args.actual_positive_rate <= 1.0):
        print("--actual-positive-rate must be between 0.0 and 1.0")
        sys.exit(1)

    if not (0.0 <= args.label_noise_rate <= 1.0):
        print("--label-noise-rate must be between 0.0 and 1.0")
        sys.exit(1)

    if not (0.0 <= args.target_recall <= 1.0):
        print("--target-recall must be between 0.0 and 1.0")
        sys.exit(1)

    if not (0.0 <= args.target_specificity <= 1.0):
        print("--target-specificity must be between 0.0 and 1.0")
        sys.exit(1)

    if args.windows_per_session <= 0:
        print("--windows-per-session must be greater than 0")
        sys.exit(1)

    random.seed(args.seed)

    if args.dry_run:
        info = preview_generation(args)
        print("Dry run preview:")
        for k, v in info.items():
            print(f"- {k}: {v}")
        return

    try:
        conn = connect_db(args)
    except mysql.connector.Error as exc:
        print(f"Database connection failed: {exc}")
        sys.exit(1)

    try:
        if args.clear_existing_sim:
            deleted_detections, deleted_labels = clear_existing_sim_data(conn, args.session_prefix)
            print("Cleared existing simulated data:")
            print(f"- deleted detections: {deleted_detections}")
            print(f"- deleted labels: {deleted_labels}")

        stats = generate_and_insert(conn, args)
        print("Synthetic RADM data generation completed.")
        print(f"- inserted detections: {stats['inserted']}")
        print(f"- actual positive labels: {stats['actual_positive']}")
        print(f"- actual negative labels: {stats['actual_negative']}")
        print(f"- predicted positives: {stats['pred_positive']}")
        print(f"- predicted negatives: {stats['pred_negative']}")
        print(f"- session prefix: {args.session_prefix}")

    finally:
        conn.close()


if __name__ == "__main__":
    main()
