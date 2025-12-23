# Random Answering Detection Model (RADM)

## Overview

The **Random Answering Detection Model (RADM)** is a behavioral analytics model designed to identify cases where learners answer assessment items without sufficient cognitive engagement, such as rapid guessing or careless clicking. The model does **not penalize learners**; instead, it triggers a **gentle intervention** (e.g., reminder modal) to encourage thoughtful participation.

RADM relies only on **observable assessment behavior** (time, accuracy, and mastery estimates), allowing detection without accessing source code. This makes the model suitable for ethical classroom use and system-level evaluation.

RADM is evaluated **after every 5 consecutive answered questions** and continues iterating throughout the quiz session.

---

## RADM Indicators

RADM is composed of three binary indicators:

* **Time Behavior Indicator (T)**
* **Accuracy Indicator (A)**
* **Bayesian Knowledge Tracing Contradiction Indicator (B)**

Each indicator outputs:

* `1` → behavior flagged
* `0` → behavior not flagged

---

## Expected Response Time (texp)

Expected response time (`texp`) represents the **typical cognitive processing time** required to answer a question of a given difficulty. It is **not the maximum allowed time**.

This mapping is anchored on empirical findings by **Kennette & McGuckin (2025)**, who reported an average of **39 seconds per multiple-choice question** in online assessments. This baseline is adjusted using difficulty-based scaling factors.

### Expected Time Mapping Table

| Difficulty   | Scaling Factor | Baseline   | Expected Time (texp) | Threshold (0.4 x texp) |
| ------------ | -------------- | ---------- | -------------------- | ---------------------- |
| Beginner     | 0.6 x          | 39 seconds | 23 seconds           | 9.2 seconds            |
| Intermediate | 0.8 x          | 39 seconds | 31 seconds           | 12.4 seconds           |
| Advanced     | 1.0 x          | 39 seconds | 39 seconds           | 15.6 seconds           |

---

## Indicator Formulas

### 1. Time Behavior Indicator (T)

This indicator checks whether the learner answers **significantly faster** than the expected cognitive processing time.

**Inputs:**

* ti = response time for question i
* n = number of questions (n = 5)
* texp = expected response time

**Average Response Time:**

avg_time = (t1 + t2 + t3 + t4 + t5) / n

**Time Flag Rule:**

T = 1 if avg_time < 0.4 x texp
T = 0 otherwise

---

### 2. Accuracy Indicator (A)

This indicator evaluates whether correctness is **unusually low** over the same question window.

**Inputs:**

* c = number of correct answers
* n = total answered questions (n = 5)

**Accuracy Formula:**

accuracy = c / n

**Accuracy Flag Rule:**

A = 1 if accuracy < 0.30
A = 0 otherwise

---

### 3. Bayesian Knowledge Tracing (BKT) Contradiction Indicator (B)

This indicator detects contradictions between **predicted mastery** and **observed performance**.

**Inputs:**

* PK = final BKT mastery probability after answering the question
* w = number of consecutive incorrect answers

**BKT Flag Rule:**

B = 1 if PK >= 0.70 AND w >= 3
B = 0 otherwise

This condition suggests non-deliberate responding rather than lack of understanding.

---

## Random Answering Index (RAI)

The three indicators are combined into a single normalized score.

**RAI Formula:**

RAI = (0.35 x T) + (0.35 x A) + (0.30 x B)

### Decision Rule

If RAI >= 0.60 → **Trigger intervention modal**

This ensures that **at least two indicators** must be active, reducing false positives.

---

## Sample Calculation (Matches the PDF)

### Sample Scenario

* Difficulty: Intermediate
* Expected response time: texp = 31 seconds
* Window size: 5 questions

---

### Step 1: Time Behavior Indicator (T)

**Response times (seconds):**

| Question | Time |
| -------- | ---- |
| Q1       | 9    |
| Q2       | 10   |
| Q3       | 11   |
| Q4       | 8    |
| Q5       | 9    |

**Average time:**

avg_time = (9 + 10 + 11 + 8 + 9) / 5
avg_time = 47 / 5
avg_time = 9.4 seconds

**Threshold calculation:**

0.4 x texp = 0.4 x 31 = 12.4 seconds

**Decision:**

9.4 < 12.4 → **T = 1**

---

### Step 2: Accuracy Indicator (A)

**Results:**

| Question | Result  |
| -------- | ------- |
| Q1       | Wrong   |
| Q2       | Correct |
| Q3       | Wrong   |
| Q4       | Wrong   |
| Q5       | Wrong   |

**Accuracy calculation:**

accuracy = 1 / 5 = 0.20

**Decision:**

0.20 < 0.30 → **A = 1**

---

### Step 3: BKT Contradiction Indicator (B)

**Given:**

* PK = 0.82
* Consecutive wrong answers (w) = 3

**Decision:**

PK >= 0.70 AND w >= 3 → **B = 1**

---

### Step 4: Random Answering Index (RAI)

RAI = (0.35 x 1) + (0.35 x 1) + (0.30 x 1)
RAI = 0.35 + 0.35 + 0.30
RAI = **1.00**

---

### Final Decision

RAI >= 0.60 → **Intervention Triggered**

A supportive modal is displayed to encourage focused participation.

---

## Design Principles

* Evaluates **patterns**, not single responses
* Protects fast and struggling learners
* Uses mastery prediction to reduce false positives
* Fully explainable and auditable

---

## Reference

Kennette, L. N., & McGuckin, D. (2025). *Best Practice for Online Tests: How Long Do Students Actually Need?* Teaching and Learning Inquiry, 13. [https://doi.org/10.20343/teachlearninqu.13.35](https://doi.org/10.20343/teachlearninqu.13.35)

---