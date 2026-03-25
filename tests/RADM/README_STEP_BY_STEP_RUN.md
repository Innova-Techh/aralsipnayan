# RADM Step-by-Step Run Guide

This guide is focused only on running the workflow end-to-end:
1. Prepare database table
2. Generate simulated detections and labels
3. Generate confusion matrix chart

Use this if you want the fastest path to a real chart output.

## 1) Open terminal at project root

Expected folder:
C:/Users/USER/Herd/aralsipnayan

You can check by running:
pwd

## 2) Run Laravel migration for labels table

Command:
php artisan migrate

Expected result:
- Migration succeeds
- Table radm_detection_labels exists in your database

If migration fails:
- Verify MySQL is running
- Verify Laravel .env DB settings are correct

## 3) Optional: Install Python packages (only if imports fail)

Command:
python -m pip install -r tests/RADM/requirements-radm-testing.txt

Expected result:
- mysql-connector-python, numpy, matplotlib, seaborn, scikit-learn installed

Skip this step if your scripts already run successfully in your current Python environment.

## 4) Generate a large simulated dataset in both tables

Command:
python tests/RADM/simulate_radm_test_data.py --records 1000 --clear-existing-sim

Expected result:
- Script inserts synthetic rows in radm_detections
- Script inserts matching rows in radm_detection_labels
- Terminal prints inserted count and class distribution

Notes:
- Use --records 2000 or higher for bigger charts
- Use --actual-positive-rate and --label-noise-rate to shape FP and FN behavior

Example:
python tests/RADM/simulate_radm_test_data.py --records 3000 --actual-positive-rate 0.40 --label-noise-rate 0.20 --clear-existing-sim

## 5) Run confusion matrix analysis and generate images

Command:
python tests/RADM/radm_confusion_matrix_analysis.py --per-difficulty

What this does:
- Runs RADM confusion matrix analyzer
- Produces chart files and metrics JSON

Expected result:
- Terminal prints confusion matrix values (TN, FP, FN, TP)
- Terminal prints metrics (accuracy, precision, recall, specificity, F1)
- Output files saved in tests/RADM/visualizations

## 6) Generate with threshold mode (optional)

Command:
python tests/RADM/radm_confusion_matrix_analysis.py --use-threshold --threshold 0.60 --per-difficulty

Use this when:
- You want prediction rule to be rai_score >= threshold
- You want per-difficulty reports

## 7) Where to find generated charts

Folder:
tests/RADM/visualizations

Typical files:
- radm_confusion_matrix_YYYYMMDD_HHMMSS.png
- radm_metrics_YYYYMMDD_HHMMSS.png
- radm_metrics_YYYYMMDD_HHMMSS.json

## 8) Useful seeding modes

A) Large baseline dataset
Command:
python tests/RADM/simulate_radm_test_data.py --records 1000 --clear-existing-sim

B) More false positives and false negatives
Command:
python tests/RADM/simulate_radm_test_data.py --records 1000 --label-noise-rate 0.30 --clear-existing-sim

C) Higher positive class proportion
Command:
python tests/RADM/simulate_radm_test_data.py --records 1000 --actual-positive-rate 0.55 --clear-existing-sim

D) Preview generation settings without writing
Command:
python tests/RADM/simulate_radm_test_data.py --records 1000 --dry-run

## 9) Verify rows were inserted

SQL:
SELECT COUNT(*) AS total_labels FROM radm_detection_labels;

SQL:
SELECT COUNT(*) AS total_detections FROM radm_detections;

SQL sample check:
SELECT d.id, d.intervention_triggered, d.rai_score, l.actual_random_answering, l.label_source
FROM radm_detections d
JOIN radm_detection_labels l ON l.detection_id = d.id
WHERE d.session_id LIKE 'sim_radm_%'
ORDER BY d.id DESC
LIMIT 20;

## 10) Troubleshooting

Issue: No labeled detections found
- Cause: Simulated data was not inserted yet
- Fix: Run simulate_radm_test_data.py without --dry-run

Issue: Access denied for MySQL
- Cause: Wrong DB credentials
- Fix: Pass credentials to script:
  python tests/RADM/simulate_radm_test_data.py --host localhost --port 3306 --database aralsipnayandb --user root --password YOUR_PASSWORD
  python tests/RADM/radm_confusion_matrix_analysis.py --host localhost --port 3306 --database aralsipnayandb --user root --password YOUR_PASSWORD

Issue: No chart files generated
- Cause: Analysis failed or script exited early
- Fix: Re-run command and check terminal error lines; verify package install and DB connection

## 11) Recommended first real run

Run in this order:
1. php artisan migrate
2. Optional: python -m pip install -r tests/RADM/requirements-radm-testing.txt
3. python tests/RADM/simulate_radm_test_data.py --records 1500 --clear-existing-sim
4. python tests/RADM/radm_confusion_matrix_analysis.py --per-difficulty
5. Open tests/RADM/visualizations and inspect confusion matrix PNG
