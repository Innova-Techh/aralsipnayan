#!/usr/bin/env python3
"""
RADM confusion matrix analysis script.

This script evaluates existing RADM detections against ground-truth labels from
radm_detection_labels without modifying production RADM logic.
"""

import argparse
import json
import os
import sys
from datetime import datetime

import mysql.connector
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.metrics import confusion_matrix


class RadmConfusionMatrixAnalyzer:
    def __init__(self, host, database, user, password, port):
        self.connection = None
        self.host = host
        self.database = database
        self.user = user
        self.password = password
        self.port = port

    def connect(self):
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                database=self.database,
                user=self.user,
                password=self.password,
                port=self.port,
                charset="utf8mb4",
            )
            print("Connected to database.")
        except mysql.connector.Error as exc:
            print(f"Database connection failed: {exc}")
            sys.exit(1)

    def close(self):
        if self.connection:
            self.connection.close()

    def fetch_labeled_detections(self):
        query = """
            SELECT
                d.id,
                d.difficulty_level,
                d.rai_score,
                d.intervention_triggered,
                l.actual_random_answering
            FROM radm_detections d
            INNER JOIN radm_detection_labels l
                ON l.detection_id = d.id
            WHERE l.actual_random_answering IS NOT NULL
            ORDER BY d.created_at ASC
        """
        cursor = self.connection.cursor(dictionary=True)
        cursor.execute(query)
        rows = cursor.fetchall()
        cursor.close()
        return rows

    @staticmethod
    def _safe_div(num, den):
        return float(num) / float(den) if den else 0.0

    def build_predictions(self, rows, use_threshold=False, threshold=0.60):
        y_true = []
        y_pred = []
        difficulty = []

        for row in rows:
            actual = 1 if row["actual_random_answering"] else 0
            if use_threshold:
                pred = 1 if float(row["rai_score"]) >= threshold else 0
            else:
                pred = 1 if row["intervention_triggered"] else 0

            y_true.append(actual)
            y_pred.append(pred)
            difficulty.append(row["difficulty_level"] or "unknown")

        return y_true, y_pred, difficulty

    def compute_metrics(self, y_true, y_pred):
        cm = confusion_matrix(y_true, y_pred, labels=[0, 1])
        tn, fp, fn, tp = cm.ravel()

        accuracy = self._safe_div(tp + tn, tp + tn + fp + fn)
        precision = self._safe_div(tp, tp + fp)
        recall = self._safe_div(tp, tp + fn)
        specificity = self._safe_div(tn, tn + fp)
        f1 = self._safe_div(2 * precision * recall, precision + recall)
        fpr = self._safe_div(fp, fp + tn)
        fnr = self._safe_div(fn, fn + tp)

        return {
            "confusion_matrix": {
                "tn": int(tn),
                "fp": int(fp),
                "fn": int(fn),
                "tp": int(tp),
            },
            "metrics": {
                "accuracy": accuracy,
                "precision": precision,
                "recall": recall,
                "specificity": specificity,
                "f1": f1,
                "fpr": fpr,
                "fnr": fnr,
            },
            "support": {
                "total": int(len(y_true)),
                "actual_positive": int(sum(y_true)),
                "actual_negative": int(len(y_true) - sum(y_true)),
                "pred_positive": int(sum(y_pred)),
                "pred_negative": int(len(y_pred) - sum(y_pred)),
            },
        }

    def print_report(self, result, title):
        cm = result["confusion_matrix"]
        m = result["metrics"]
        s = result["support"]

        print("\n" + "=" * 64)
        print(title)
        print("=" * 64)
        print(f"Total labeled detections: {s['total']}")
        print(f"Actual positives: {s['actual_positive']}")
        print(f"Actual negatives: {s['actual_negative']}")
        print(f"Predicted positives: {s['pred_positive']}")
        print(f"Predicted negatives: {s['pred_negative']}")

        print("\nConfusion Matrix")
        print(f"TN: {cm['tn']}  FP: {cm['fp']}")
        print(f"FN: {cm['fn']}  TP: {cm['tp']}")

        print("\nMetrics")
        print(f"Accuracy:    {m['accuracy']:.4f}")
        print(f"Precision:   {m['precision']:.4f}")
        print(f"Recall:      {m['recall']:.4f}")
        print(f"Specificity: {m['specificity']:.4f}")
        print(f"F1:          {m['f1']:.4f}")
        print(f"FPR:         {m['fpr']:.4f}")
        print(f"FNR:         {m['fnr']:.4f}")

    def save_confusion_heatmap(self, result, output_path, title):
        cm = result["confusion_matrix"]
        data = np.array([
            [cm["tn"], cm["fp"]],
            [cm["fn"], cm["tp"]],
        ])

        plt.figure(figsize=(7, 6))
        sns.heatmap(
            data,
            annot=True,
            fmt="d",
            cmap="Blues",
            xticklabels=["Predicted Negative", "Predicted Positive"],
            yticklabels=["Actual Negative", "Actual Positive"],
            cbar_kws={"label": "Count"},
        )
        plt.title(title)
        plt.xlabel("Prediction")
        plt.ylabel("Ground Truth")
        plt.tight_layout()
        plt.savefig(output_path, dpi=300, bbox_inches="tight")
        plt.close()

    def save_metrics_bar_chart(self, result, output_path, title):
        m = result["metrics"]
        names = ["Accuracy", "Precision", "Recall", "F1"]
        values = [m["accuracy"], m["precision"], m["recall"], m["f1"]]

        colors = ["#2E7D32" if v >= 0.70 else "#F9A825" if v >= 0.50 else "#C62828" for v in values]

        plt.figure(figsize=(9, 5))
        bars = plt.bar(names, values, color=colors)
        for bar, value in zip(bars, values):
            plt.text(
                bar.get_x() + bar.get_width() / 2,
                value + 0.01,
                f"{value:.3f}",
                ha="center",
                va="bottom",
            )

        plt.ylim(0, 1.05)
        plt.ylabel("Score")
        plt.title(title)
        plt.grid(axis="y", alpha=0.3)
        plt.tight_layout()
        plt.savefig(output_path, dpi=300, bbox_inches="tight")
        plt.close()


def parse_args():
    parser = argparse.ArgumentParser(description="RADM confusion matrix evaluator")
    parser.add_argument("--host", default="localhost", help="MySQL host")
    parser.add_argument("--port", type=int, default=3306, help="MySQL port")
    parser.add_argument("--database", default="aralsipnayandb", help="MySQL database name")
    parser.add_argument("--user", default="root", help="MySQL username")
    parser.add_argument("--password", default="", help="MySQL password")

    parser.add_argument(
        "--use-threshold",
        action="store_true",
        help="Use rai_score threshold instead of intervention_triggered as prediction",
    )
    parser.add_argument(
        "--threshold",
        type=float,
        default=0.60,
        help="Threshold for rai_score when --use-threshold is provided",
    )
    parser.add_argument(
        "--per-difficulty",
        action="store_true",
        help="Also print per-difficulty confusion matrix metrics",
    )
    parser.add_argument(
        "--output-dir",
        default=os.path.join("tests", "RADM", "visualizations"),
        help="Directory to save charts and JSON results",
    )
    return parser.parse_args()


def main():
    args = parse_args()

    analyzer = RadmConfusionMatrixAnalyzer(
        host=args.host,
        database=args.database,
        user=args.user,
        password=args.password,
        port=args.port,
    )

    analyzer.connect()
    rows = analyzer.fetch_labeled_detections()

    if not rows:
        print("No labeled RADM detections found. Insert rows into radm_detection_labels first.")
        analyzer.close()
        return

    y_true, y_pred, difficulties = analyzer.build_predictions(
        rows,
        use_threshold=args.use_threshold,
        threshold=args.threshold,
    )

    result = analyzer.compute_metrics(y_true, y_pred)

    title_mode = (
        f"RADM Confusion Matrix (rai_score >= {args.threshold:.2f})"
        if args.use_threshold
        else "RADM Confusion Matrix (intervention_triggered)"
    )

    analyzer.print_report(result, title_mode)

    if args.per_difficulty:
        unique_difficulties = sorted(set(difficulties))
        for level in unique_difficulties:
            idx = [i for i, d in enumerate(difficulties) if d == level]
            d_true = [y_true[i] for i in idx]
            d_pred = [y_pred[i] for i in idx]

            # confusion_matrix with labels=[0,1] remains stable even if one class is sparse
            d_result = analyzer.compute_metrics(d_true, d_pred)
            analyzer.print_report(d_result, f"Per-Difficulty: {level}")

    os.makedirs(args.output_dir, exist_ok=True)
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")

    heatmap_path = os.path.join(args.output_dir, f"radm_confusion_matrix_{timestamp}.png")
    metrics_path = os.path.join(args.output_dir, f"radm_metrics_{timestamp}.png")
    json_path = os.path.join(args.output_dir, f"radm_metrics_{timestamp}.json")

    analyzer.save_confusion_heatmap(result, heatmap_path, title_mode)
    analyzer.save_metrics_bar_chart(result, metrics_path, "RADM Performance Metrics")

    with open(json_path, "w", encoding="utf-8") as f:
        json.dump(
            {
                "mode": "threshold" if args.use_threshold else "intervention_triggered",
                "threshold": args.threshold if args.use_threshold else None,
                "result": result,
            },
            f,
            indent=2,
        )

    print("\nSaved outputs:")
    print(f"- {heatmap_path}")
    print(f"- {metrics_path}")
    print(f"- {json_path}")

    analyzer.close()


if __name__ == "__main__":
    main()
