# BKT Algorithm Implementation - AralSipnayan

## Overview
This document describes the updated Bayesian Knowledge Tracing (BKT) algorithm implementation for the AralSipnayan adaptive learning system.

## Formulas Implemented

### 1. Mastery Score Calculation
```
Mastery Score = (weightAccuracy × Accuracy) + (weightBKT × BKT score)
```
- **weightAccuracy**: 55%
- **weightBKT**: 45%
- **Accuracy**: total_correct_answers / total_questions

### 2. BKT Update Formulas

#### For Correct Answers:
```
P(Ln+1) = P(Ln)⋅(1−P(S))⋅ftime⋅fdifficulty / [P(Ln)⋅(1−P(S))+(1−P(Ln))⋅P(G)]
```

#### For Incorrect Answers:
```
P(Ln+1) = P(Ln)⋅P(S) / [P(Ln)⋅P(S)+(1−P(Ln))⋅(1−P(G))⋅ftime⋅fdifficulty]
```

Where:
- **P(Ln)**: prior mastery before the question
- **P(S)**: slip probability (0.1000)
- **P(G)**: guess probability (0.2500)
- **ftime**: time factor
- **fdifficulty**: difficulty factor

### 3. BKT Parameters
```python
'prior_knowledge': 0.1000,  # P(L0)
'learn_rate': 0.3000,       # P(T)
'slip_rate': 0.1000,        # P(S)
'guess_rate': 0.2500        # P(G)
```

### 4. Difficulty Factors
- **beginner**: 0.8
- **intermediate**: 1.0
- **advanced**: 1.2

### 5. Time Factor (ftime)
```
ftime = (Σ(timescore) / total_questions)
```

### 6. Time Score Calculation
```
normalized_time = response_time / max_allowed_time
```

**Time Score Chart:**
- **1.0** if correct AND normalized time ≤ 0.5 (fast + correct)
- **0.8** if correct AND normalized time ≤ 0.8 (medium speed + correct)
- **0.6** if correct AND normalized time > 0.8 (slow + correct)
- **0.2** if incorrect AND normalized time ≤ 0.5 (fast + wrong)
- **0.1** if incorrect AND normalized time > 0.5 (slow + wrong)
- **0.0** if timeout

### 7. Max Allowed Time per Difficulty
- **beginner**: 30 seconds
- **intermediate**: 45 seconds
- **advanced**: 60 seconds

### 8. Total Questions per Difficulty
- **beginner**: 15 questions
- **intermediate**: 20 questions
- **advanced**: 25 questions

### 9. Difficulty Level Thresholds
- **beginner**: 75 and below
- **intermediate**: 76 to 84
- **advanced**: 85 to 100

## Database Schema Updates

### student_mastery table
- Added: `current_ftime_factor` (decimal 5,4) - stores current f-time factor for BKT calculations

### question_responses table
- Added: `ftime_factor` (decimal 5,4) - stores the f-time factor used for this response

## Key Methods

### `calculate_time_score(response_time, max_allowed_time, is_correct)`
Calculates time score based on normalized time and correctness.

### `calculate_ftime_factor(cumulative_time_score, total_questions_answered)`
Calculates the f-time factor for BKT formula.

### `calculate_bkt_update(prior_prob, is_correct, params, time_factor, difficulty_factor)`
Updates BKT probability using the correct formulas for correct/incorrect answers.

### `calculate_mastery_score(accuracy_score, bkt_score)`
Calculates final mastery score using 55% accuracy + 45% BKT weighting.

### `determine_difficulty_level(mastery_score)`
Determines difficulty level based on mastery score thresholds.

### `record_diagnostic_answer(session_id, question_id, user_answer, response_time, is_correct)`
Records answer with proper BKT calculations and database updates.

## Testing
Run the test script to verify all calculations:
```bash
python test_bkt_algorithm.py
```

This will test all the formulas and verify they produce correct results according to the specifications.