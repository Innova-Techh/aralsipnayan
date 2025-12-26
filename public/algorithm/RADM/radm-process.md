# 📘 RADM Independent Evaluation Process (Read-Only)

## Overview

This document describes an **independent Random Answering Detection Model (RADM)** designed to operate **separately from the core quiz logic**.
The RADM process **does not alter** existing assessment workflows. Instead, it **reads quiz session data from the database**, performs behavioral analysis, and determines whether an intervention modal should be displayed to the learner.

This separation ensures:

* Minimal coupling with existing code
* Safe integration into live assessments
* Easy auditing and explainability
* Compatibility with agentic AI systems

---

## High-Level Process Flow

1. Detect the **current active quiz session**
2. Retrieve the **latest answered questions**
3. Extract RADM-related indicators from responses
4. Compute Time, Accuracy, and BKT flags
5. Compute the Random Answering Index (RAI)
6. Decide whether to show the intervention modal

---

## 1. Detecting the Current Quiz Session

### Table: `assessment_sessions`

The RADM process first identifies the learner’s **active quiz session**.

**Purpose:**
Ensure RADM evaluates only responses belonging to the current assessment attempt.

**Example fields used:**

* `id` – assessment session identifier
* `user_id` – learner identifier
* `status` – active / completed
* `created_at`

**Logic:**

* Select the most recent session
* Ensure status is `active`
* Use `assessment_sessions.id` as the session scope for all queries

---

## 2. Retrieving Question Responses

### Table: `question_responses`

Once the session is identified, RADM retrieves the **latest answered questions** linked to that session.

Each record provides the necessary data for RADM computation.

### Fields Used for RADM

| Indicator | Column Name     | Description                               |
| --------- | --------------- | ----------------------------------------- |
| Time      | `response_time` | Number of seconds spent answering         |
| Accuracy  | `is_correct`    | Boolean (true / false)                    |
| BKT       | `final_bkt`     | Final post-answer BKT mastery probability |

---

## 3. Evaluation Window

RADM is evaluated **only after a minimum of 5 consecutive answered questions**.

**Reason:**

* Avoid false positives
* Detect consistent behavior patterns
* Ensure fairness to learners

If fewer than 5 responses exist, RADM does not execute.

---

## 4. Time Behavior Indicator (T)

### Computation

1. Compute average response time:

```
avg_time = (t1 + t2 + t3 + t4 + t5) / 5
```

2. Compare against expected response time (`T_exp`) based on difficulty.

### Time Flag Rule

```
If avg_time < 0.4 X T_exp
→ Time Flag (T) = 1
Else
→ T = 0
```

This detects **rapid answering behavior** inconsistent with thoughtful engagement.

---

## 5. Accuracy Indicator (A)

### Computation

1. Count correct answers:

```
accuracy = correct_answers / 5
```

### Accuracy Flag Rule

```
If accuracy < 0.30
→ Accuracy Flag (A) = 1
Else
→ A = 0
```

Low accuracy combined with rapid answering is a strong indicator of random behavior.

---

## 6. BKT Consistency Indicator (B)

### Source

The `final_bkt` field already represents the **post-answer probability of mastery**, computed elsewhere.

### BKT Flag Rule

```
If final_bkt >= 0.70 AND consecutive wrong answers >= 3
→ BKT Flag (B) = 1
Else
→ B = 0
```

This captures **knowledge–performance inconsistency**, where mastery is high but performance deteriorates.

---

## 7. Random Answering Index (RAI)

### Formula

```
RAI = (0.35 X T) + (0.35 X A) + (0.30 X B)
```

### Weight Justification

* Time (0.35): Detects disengagement speed
* Accuracy (0.35): Measures outcome reliability
* BKT (0.30): Captures cognitive inconsistency

Weights sum to **1.00**, ensuring balanced contribution.

---

## 8. Decision Threshold

### Intervention Rule

```
If RAI >= 0.60
→ Trigger Intervention Modal
Else
→ Continue Quiz Normally
```

This threshold requires **multiple indicators** to activate, reducing false positives.

---

## 9. Sample Calculation (End-to-End)

### Sample Data (Last 5 Questions)

| Q | Time (s) | Correct | final_bkt |
| - | -------- | ------- | --------- |
| 1 | 10       | false   | 0.78      |
| 2 | 9        | false   | 0.76      |
| 3 | 11       | false   | 0.75      |
| 4 | 12       | true    | 0.74      |
| 5 | 8        | false   | 0.73      |

### Step 1: Time

```
avg_time = (10 + 9 + 11 + 12 + 8) / 5 = 10
T_exp = 30
0.4 X 30 = 12

10 < 12 → T = 1
```

### Step 2: Accuracy

```
accuracy = 1 / 5 = 0.20
0.20 < 0.30 → A = 1
```

### Step 3: BKT

```
final_bkt >= 0.70 AND 3 consecutive wrong answers
→ B = 1
```

### Step 4: RAI

```
RAI = (0.35 X 1) + (0.35 X 1) + (0.30 X 1)
RAI = 1.00
```

### Final Decision

```
RAI >= 0.60 → SHOW MODAL
```

---

## 10. Output Behavior

* RADM **does not modify responses**
* RADM **does not block submissions**
* RADM only returns a **boolean decision**
* UI decides whether to show a modal

---

