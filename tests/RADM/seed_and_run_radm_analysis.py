#!/usr/bin/env python3
"""
Seed radm_detection_labels and run RADM confusion matrix analysis in one command.

This utility does not modify RADM scoring logic. It only:
1) Inserts labels into radm_detection_labels for unlabeled detections
2) Runs tests/RADM/radm_confusion_matrix_analysis.py
"""

import argparse
import os
import random
import subprocess
import sys
from datetime import datetime

import mysql.connector


def parse_args():
    parser = argparse.ArgumentParser(
        description="Seed RADM labels and run confusion matrix analysis",
    )

    # Database options
    parser.add_argument("--host", default="localhost", help="MySQL host")
    parser.add_argument("--port", type=int, default=3306, help="MySQL port")
    parser.add_argument("--database", default="aralsipnayandb", help="MySQL database")
    parser.add_argument("--user", default="root", help="MySQL username")
    parser.add_argument("--password", default="", help="MySQL password")

    # Seeding behavior
    parser.add_argument(
        "--strategy",
        choices=["copy_prediction", "simulate_noise"],
        default="simulate_noise",
        help=(
            "copy_prediction: actual label equals intervention_triggered; "
            "simulate_noise: start from prediction then flip labels using noise rate"
        ),
    )
    parser.add_argument(
        "--noise-rate",
        type=float,
        default=0.20,
        help="Label flip probability for simulate_noise strategy (0.0 to 1.0)",
    )
    parser.add_argument(
        "--limit",
        type=int,
        default=0,
        help="Maximum number of unlabeled detections to seed (0 means all unlabeled)",
    )
    parser.add_argument(
        "--seed",
        type=int,
        default=42,
        help="Random seed for reproducible simulate_noise labels",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Preview what would be inserted without writing to database",
    )

    # Analysis options
    parser.add_argument(
        "--use-threshold",
        action="store_true",
        help="Use rai_score threshold mode in analysis script",
    )
    parser.add_argument(
        "--threshold",
        type=float,
        default=0.60,
        help="RAI threshold when --use-threshold is enabled",
    )
    parser.add_argument(
        "--per-difficulty",
        action="store_true",
        help="Include per-difficulty reports",
    )
    parser.add_argument(
        "--output-dir",
        default=os.path.join("tests", "RADM", "visualizations"),
        help="Output directory for generated charts and metrics JSON",
    )

    return parser.parse_args()


def connect_db(args):
    return mysql.connector.connect(
        host=args.host,
        port=args.port,
        database=args.database,
        user=args.user,
        password=args.password,
        charset="utf8mb4",
    )


def fetch_unlabeled_detections(connection, limit):
    sql = """
        SELECT
            d.id,
            d.intervention_triggered,
            d.rai_score
        FROM radm_detections d
        LEFT JOIN radm_detection_labels l ON l.detection_id = d.id
        WHERE l.id IS NULL
        ORDER BY d.created_at ASC
    """
    if limit and limit > 0:
        sql += " LIMIT %s"

    cursor = connection.cursor(dictionary=True)
    if limit and limit > 0:
        cursor.execute(sql, (limit,))
    else:
        cursor.execute(sql)

    rows = cursor.fetchall()
    cursor.close()
    return rows


def build_label(prediction, strategy, noise_rate):
    pred = 1 if prediction else 0

    if strategy == "copy_prediction":
        return pred

    # simulate_noise strategy: copy prediction then flip with probability noise_rate
    if random.random() < noise_rate:
        return 1 - pred
    return pred


def seed_labels(connection, rows, strategy, noise_rate, dry_run):
    timestamp = datetime.now()

    insert_sql = """
        INSERT INTO radm_detection_labels
        (detection_id, actual_random_answering, label_source, labeled_by, labeled_at, notes, created_at, updated_at)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """

    inserted = 0
    preview = []

    cursor = connection.cursor()

    for row in rows:
        detection_id = row["id"]
        prediction = bool(row["intervention_triggered"])
        actual = build_label(prediction, strategy, noise_rate)

        label_source = "seed_script"
        labeled_by = None
        notes = (
            f"auto-seeded ({strategy}), noise_rate={noise_rate:.2f}, "
            f"prediction={int(prediction)}, rai={float(row['rai_score']):.4f}"
        )

        preview.append((detection_id, actual, notes))

        if not dry_run:
            cursor.execute(
                insert_sql,
                (detection_id, actual, label_source, labeled_by, timestamp, notes, timestamp, timestamp),
            )

        inserted += 1

    if not dry_run:
        connection.commit()

    cursor.close()
    return inserted, preview


def run_analysis(args):
    cmd = [
        sys.executable,
        os.path.join("tests", "RADM", "radm_confusion_matrix_analysis.py"),
        "--host",
        args.host,
        "--port",
        str(args.port),
        "--database",
        args.database,
        "--user",
        args.user,
        "--password",
        args.password,
        "--output-dir",
        args.output_dir,
    ]

    if args.use_threshold:
        cmd.extend(["--use-threshold", "--threshold", str(args.threshold)])

    if args.per_difficulty:
        cmd.append("--per-difficulty")

    print("\nRunning analysis command:")
    print(" ".join(cmd))

    result = subprocess.run(cmd, check=False)
    return result.returncode


def main():
    args = parse_args()

    if not (0.0 <= args.noise_rate <= 1.0):
        print("--noise-rate must be between 0.0 and 1.0")
        sys.exit(1)

    random.seed(args.seed)

    try:
        conn = connect_db(args)
    except mysql.connector.Error as exc:
        print(f"Database connection failed: {exc}")
        sys.exit(1)

    try:
        rows = fetch_unlabeled_detections(conn, args.limit)
        if not rows:
            print("No unlabeled detections found. Proceeding directly to analysis.")
        else:
            print(f"Found {len(rows)} unlabeled detections.")
            inserted, preview = seed_labels(
                conn,
                rows,
                strategy=args.strategy,
                noise_rate=args.noise_rate,
                dry_run=args.dry_run,
            )

            print(f"Prepared labels: {inserted}")
            print("Preview (first 10):")
            for item in preview[:10]:
                detection_id, actual, notes = item
                print(f"- detection_id={detection_id}, actual_random_answering={actual}, notes={notes}")

            if args.dry_run:
                print("Dry run enabled: no rows inserted.")

        if args.dry_run:
            print("Skipping analysis because --dry-run was set.")
            return

        code = run_analysis(args)
        if code != 0:
            print(f"Analysis script failed with exit code {code}.")
            sys.exit(code)

        print("\nDone. Confusion matrix artifacts were generated in output directory.")

    finally:
        conn.close()


if __name__ == "__main__":
    main()
