# RADM Confusion Matrix Evaluation (Non-Intrusive)

This setup evaluates your existing RADM detections **without changing the current RADM process**.

It adds only:
1. A new table for ground-truth labels (`radm_detection_labels`)
2. A standalone Python analysis script
3. Visualization output (confusion matrix heatmap + metrics chart)

## Why a Separate Table

Your current `radm_detections` table stores model outputs (`intervention_triggered`, `rai_score`, flags).

To compute false positives and false negatives, you also need **actual labels** (ground truth).
That is what `radm_detection_labels` provides.

## Data Definitions

- Predicted positive: RADM says random-answering is detected (`intervention_triggered = 1`), or `rai_score >= threshold`
- Actual positive: Reviewer/teacher label says random-answering truly happened (`actual_random_answering = 1`)

## Confusion Matrix

Let:
- TP = true positives
- FP = false positives
- TN = true negatives
- FN = false negatives

Matrix layout:

|                | Predicted Positive | Predicted Negative |
|----------------|--------------------|--------------------|
| Actual Positive| TP                 | FN                 |
| Actual Negative| FP                 | TN                 |

## Core Formulas

- Accuracy:
  $$
  \text{Accuracy} = \frac{TP + TN}{TP + TN + FP + FN}
  $$

- Precision (Positive Predictive Value):
  $$
  \text{Precision} = \frac{TP}{TP + FP}
  $$

- Recall (Sensitivity / TPR):
  $$
  \text{Recall} = \frac{TP}{TP + FN}
  $$

- Specificity (TNR):
  $$
  \text{Specificity} = \frac{TN}{TN + FP}
  $$

- F1 score:
  $$
  F1 = 2 \cdot \frac{\text{Precision} \cdot \text{Recall}}{\text{Precision} + \text{Recall}}
  $$

- False Positive Rate:
  $$
  \text{FPR} = \frac{FP}{FP + TN}
  $$

- False Negative Rate:
  $$
  \text{FNR} = \frac{FN}{FN + TP}
  $$

## Setup Steps

1. Run migration:

```bash
php artisan migrate
```

2. Install Python packages for this test only:

```bash
pip install -r tests/RADM/requirements-radm-testing.txt
```

3. Add labels into `radm_detection_labels`.

Example SQL:

```sql
INSERT INTO radm_detection_labels
(detection_id, actual_random_answering, label_source, labeled_by, labeled_at, notes, created_at, updated_at)
VALUES
(101, 1, 'manual', 1, NOW(), 'Observed random clicks and rushed timing', NOW(), NOW()),
(102, 0, 'manual', 1, NOW(), 'Careful solving behavior', NOW(), NOW());
```

## Run the Evaluator

Default mode (uses current production decision):

```bash
python tests/RADM/radm_confusion_matrix_analysis.py
```

Custom threshold mode (uses `rai_score >= threshold`):

```bash
python tests/RADM/radm_confusion_matrix_analysis.py --use-threshold --threshold 0.60
```

Per-difficulty report:

```bash
python tests/RADM/radm_confusion_matrix_analysis.py --per-difficulty
```

## One-Command Seed + Analyze

If you want to auto-seed unlabeled detections and immediately generate confusion-matrix outputs:

```bash
python tests/RADM/seed_and_run_radm_analysis.py
```

Useful options:

```bash
# Preview labels only (no insert, no charts)
python tests/RADM/seed_and_run_radm_analysis.py --dry-run

# Seed from prediction exactly (actual = intervention_triggered)
python tests/RADM/seed_and_run_radm_analysis.py --strategy copy_prediction

# Seed with simulated label noise (default)
python tests/RADM/seed_and_run_radm_analysis.py --strategy simulate_noise --noise-rate 0.20

# Seed only latest 50 unlabeled detections and run threshold-based analysis
python tests/RADM/seed_and_run_radm_analysis.py --limit 50 --use-threshold --threshold 0.60 --per-difficulty
```

Notes:
- `copy_prediction` is useful for pipeline testing but will produce near-perfect metrics by design.
- `simulate_noise` is useful for demo/testing when manual labels are not yet available.
- For real evaluation quality, use manual labels.

## Outputs

Saved in `tests/RADM/visualizations/`:
- `radm_confusion_matrix_<timestamp>.png`
- `radm_metrics_<timestamp>.png`
- `radm_metrics_<timestamp>.json`

## Notes

- This is evaluation-only. Existing RADM logic and routes are untouched.
- Quality of FP/FN analysis depends on the quality and consistency of your manual labels.
- If labels are highly imbalanced, also monitor precision-recall behavior in addition to accuracy.
