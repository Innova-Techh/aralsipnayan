# AralSipnayan BKT Algorithm - Complete Calculation Guide

## Overview
This document provides the complete mathematical framework for the AralSipnayan adaptive learning system, combining Bayesian Knowledge Tracing (BKT) with time-based performance assessment for Grade 6 mathematics education.

## Core Parameters

### BKT Parameters
```
P(L0) = Initial Knowledge = 0.15
P(T) = Learning Rate =  0.65
P(S) = Slip Rate = 0.003
P(G) = Guess Rate = 0.008 
```

### Time Limits by Difficulty
```
Beginner: 30 seconds
Intermediate: 45 seconds
Advanced: 60 seconds
```

### Mastery Score Weights
```
Accuracy Weight = 55%
BKT Weight = 45%
```

## Step-by-Step Calculation Process

### 1. Time Score Calculation
For each question response:

```
normalized_time = response_time / max_allowed_time

Time Score Rules:
- 1.0 if correct AND normalized_time ≤ 0.5 (fast + correct)
- 0.8 if correct AND normalized_time ≤ 0.8 (medium + correct)
- 0.6 if correct AND normalized_time > 0.8 (slow + correct)
- 0.2 if incorrect AND normalized_time ≤ 0.5 (fast + wrong)
- 0.1 if incorrect AND normalized_time > 0.5 (slow + wrong)
- 0.0 if timeout
```

### 2. BKT Knowledge State Update
```
Performance Given Knowledge State:
For any response, there are two scenarios:
Student has mastered the concept (probability = P(Ln-1))
Student has not mastered the concept (probability = 1-P(Ln-1))
```

#### For Correct Answers (Academic Equation 3):
```
Numerator = P(Ln-1) × (1 - P(S))
Denominator = P(Ln-1) × (1 - P(S)) + (1 - P(Ln-1)) × P(G)
Posterior = Numerator / Denominator

Where:
P(correct|learned) = 1 - P(S) = 1 - 0.003 = 0.997
P(correct|not_learned) = P(G) = 0.008

With Learning Enhancement:
P(Ln) = Posterior + (1 - Posterior) × P(T)
```

#### For Incorrect Answers (Academic Equation 4):
```
Numerator = P(Ln-1) × P(S)
Denominator = P(Ln-1) × P(S) + (1 - P(Ln-1)) × (1 - P(G))
Posterior = Numerator / Denominator

Where:
P(incorrect|learned) = P(S) = 0.003
P(incorrect|not_learned) = 1 - P(G) = 1 - 0.008 = 0.992

No Learning (Standard BKT):
P(Ln) = Posterior
```

### 3. Final Mastery Score
After all questions:

```
Accuracy = correct_answers / total_questions
Average_Time_Factor = sum(all_time_scores) / total_questions
Final_BKT = P(Ln_final)

Mastery_Score = (0.55 × Accuracy) + (0.45 × Final_BKT × Average_Time_Factor)
Final_Percentage = Mastery_Score × 100
```

## Example Calculations

### Example 1: Correct Answer

**Setup:**
- Current BKT: P(L2) = 0.15 (15%)
- Question 3: Student answers CORRECTLY in 12 seconds (max 30s)
- Normalized time: 12/30 = 0.40

**Step 1: Time Score**
```
Correct + normalized_time (0.40) ≤ 0.5
Time Score = 1.0 (fast + correct)
```

**Step 2: BKT Update (Correct Answer)**
```
Numerator = P(L2) × (1 - P(S))
Numerator = 0.15 × (1 - 0.003) = 0.15 × 0.997 = 0.14955

Denominator = P(L2) × (1 - P(S)) + (1 - P(L2)) × P(G)
Denominator = 0.15 × 0.997 + 0.85 × 0.008 = 0.14955 + 0.0068 = 0.15635

Posterior = 0.14955 / 0.15635 = 0.9565
```

**Step 3: Learning Enhancement**
```
P(L3) = Posterior + (1 - Posterior) × P(T)
P(L3) = 0.9565 + (1 - 0.9565) × 0.65
P(L3) = 0.9565 + 0.0435 × 0.65 = 0.9565 + 0.0283 = 0.9848
```

**Result:** BKT increased from 15% to 98.5%

### Example 2: Incorrect Answer

**Setup:**
- Current BKT: P(L3) = 0.9848 (98.5%)
- Question 4: Student answers INCORRECTLY in 28 seconds (max 30s)
- Normalized time: 28/30 = 0.93

**Step 1: Time Score**
```
Incorrect + normalized_time (0.93) > 0.5
Time Score = 0.1 (slow + wrong)
```

**Step 2: BKT Update (Incorrect Answer)**
```
Numerator = P(L3) × P(S)
Numerator = 0.9848 × 0.003 = 0.002954

Denominator = P(L3) × P(S) + (1 - P(L3)) × (1 - P(G))
Denominator = 0.9848 × 0.003 + 0.0152 × 0.992 = 0.002954 + 0.015078 = 0.018032

Posterior = 0.002954 / 0.018032 = 0.1638
```

**Step 3: No Learning Enhancement**
```
P(L4) = Posterior = 0.1638
```

**Result:** BKT decreased from 98.5% to 16.4%

## Complete 5-Question Example

**Student Performance:**
1. Q1: Incorrect, 25s/30s → BKT: 0.15 → 0.043, Time Score: 0.1
2. Q2: Correct, 15s/30s → BKT: 0.043 → 0.890, Time Score: 1.0
3. Q3: Correct, 12s/30s → BKT: 0.890 → 0.981, Time Score: 1.0
4. Q4: Incorrect, 28s/30s → BKT: 0.981 → 0.243, Time Score: 0.1
5. Q5: Correct, 20s/30s → BKT: 0.243 → 0.971, Time Score: 0.8

**Final Calculations:**
```
Accuracy = 3/5 = 0.60 (60%)
Average_Time_Factor = (0.1 + 1.0 + 1.0 + 0.1 + 0.8) / 5 = 0.60
Final_BKT = 0.971 (97.1%)

Mastery_Score = (0.55 × 0.60) + (0.45 × 0.971 × 0.60)
Mastery_Score = 0.33 + 0.262 = 0.592
Final_Percentage = 59.2%

Classification: Beginner Level (≤75%)
```

## Key Educational Insights

**BKT Behavior:**
- Increases with correct answers (shows learning)
- Decreases with incorrect answers (shows slip evidence)
- More responsive to early correct answers when knowledge is low
- Provides continuous knowledge state estimation

**Time Factor Impact:**
- Rewards fast, accurate responses
- Penalizes slow, incorrect responses
- Maintains educational validity by considering response quality

**Mastery Score Benefits:**
- Combines accuracy with knowledge progression
- Accounts for response time and quality
- Provides more nuanced assessment than simple percentage scores

## Implementation Notes

- Each question updates BKT sequentially (L0 → L1 → L2 → ... → Ln)
- Time scores are calculated independently for each question
- Final mastery score integrates all performance dimensions
- Classification thresholds: ≤75% (Beginner), 76-84% (Intermediate), ≥85% (Advanced)

This framework provides educationally sound, mathematically rigorous assessment that adapts to individual student learning patterns while maintaining measurement validity.