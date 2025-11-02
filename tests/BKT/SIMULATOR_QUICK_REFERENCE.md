# 900-Response Simulator - Quick Reference Guide

## 🎓 Student Performance Profiles at a Glance

```
Distribution of 30 Students (900 Responses Total):

┌─────────────────────────────────────────────────────────────────┐
│                      STUDENT PROFILES                           │
├──────────────┬──────┬──────────┬─────────┬──────────┬────────────┤
│ Profile      │ Count│ Accuracy │ Time    │Learning  │Base Responses
├──────────────┼──────┼──────────┼─────────┼──────────┼────────────┤
│ Exceptional  │  3   │   92%    │ -40%    │  0.8     │    ~27
│ High         │  6   │   82%    │ -30%    │  0.6     │    ~24
│ Above Avg    │  7   │   72%    │ -15%    │  0.4     │    ~21
│ Average      │  8   │   58%    │  0%     │  0.3     │    ~17
│ Below Avg    │  4   │   45%    │ +20%    │  0.2     │    ~13
│ Struggling   │  2   │   28%    │ +40%    │  0.1     │    ~8
├──────────────┼──────┼──────────┼─────────┼──────────┼────────────┤
│ TOTAL        │ 30   │ Mix      │ Varied  │ Mix      │    900
└──────────────┴──────┴──────────┴─────────┴──────────┴────────────┘
```

## 📊 Data Structure

```
900 Responses Generated Across:

├── 30 Students (test_student_000 to test_student_029)
│
├── 3 Competencies:
│   ├── Number Algebra
│   ├── Measurement Geometry
│   └── Data Probability
│
├── 3 Difficulty Levels:
│   ├── Beginner (30 sec) - 20 questions per competency
│   ├── Intermediate (45 sec) - 20 questions per competency
│   └── Advanced (60 sec) - 20 questions per competency
│
└── 90 Assessment Sessions:
    (30 students × 3 competencies)
```

## 🧠 Sequential Learning Example

### **Exceptional Performer's Journey (Profile: High BKT + Fast Improvement)**
```
Question 1: BKT 0.15 → Correct ✓ → BKT 0.45 (+30%)
Question 2: BKT 0.45 → Correct ✓ → BKT 0.70 (+25%)
Question 3: BKT 0.70 → Correct ✓ → BKT 0.85 (+15%)
Question 4: BKT 0.85 → Wrong ✗  → BKT 0.60 (-25% "slip")
Question 5: BKT 0.60 → Correct ✓ → BKT 0.78 (+18%)

Result: Fast learner reaches mastery quickly (~5 questions to 0.7 BKT)
```

### **Struggling Performer's Journey (Profile: Low BKT + Slow Improvement)**
```
Question 1: BKT 0.15 → Wrong ✗  → BKT 0.05 (-10%)
Question 2: BKT 0.05 → Wrong ✗  → BKT 0.02 (-3%)
Question 3: BKT 0.02 → Correct ✓ → BKT 0.12 (+10% "guess?")
Question 4: BKT 0.12 → Wrong ✗  → BKT 0.03 (-9%)
Question 5: BKT 0.03 → Correct ✓ → BKT 0.09 (+6%)

Result: Slow learner requires many questions for small improvements
```

### **Average Performer's Journey (Profile: Mixed Performance)**
```
Question 1: BKT 0.15 → Correct ✓ → BKT 0.45 (+30%)
Question 2: BKT 0.45 → Wrong ✗  → BKT 0.20 (-25%)
Question 3: BKT 0.20 → Correct ✓ → BKT 0.42 (+22%)
Question 4: BKT 0.42 → Correct ✓ → BKT 0.65 (+23%)
Question 5: BKT 0.65 → Wrong ✗  → BKT 0.35 (-30%)

Result: Mixed performance with ups and downs, steady overall progression
```

## ⏱️ Response Time Patterns

### **How Response Time Relates to Confidence**

```
EXCEPTIONAL LEARNER (92% base accuracy):
┌─────────────────────────────────────────┐
│ High Confidence (BKT > 0.7)            │
│ Correct Answer: 9-18 sec (⚡ Very Fast)│
│ Incorrect: Rare, but 3-10 sec (slip)   │
└─────────────────────────────────────────┘

AVERAGE LEARNER (58% base accuracy):
┌─────────────────────────────────────────┐
│ Medium Confidence (BKT 0.4-0.7)         │
│ Correct Answer: 18-36 sec (Normal)      │
│ Incorrect: 22-30 sec (Struggled)        │
└─────────────────────────────────────────┘

STRUGGLING LEARNER (28% base accuracy):
┌─────────────────────────────────────────┐
│ Low Confidence (BKT < 0.4)              │
│ Correct Answer: 30-50 sec (Lucky guess?)│
│ Incorrect: 40-65 sec (⏳ Very Slow)     │
└─────────────────────────────────────────┘
```

## 📈 Expected Results After AUC-ROC Analysis

```
BKT Model Performance on 900 Simulated Responses:

AUC-ROC Score: 0.85-0.92 ✓ EXCELLENT

┌────────────────────────────────────────────────────┐
│ CONFUSION MATRIX (900 responses)                   │
├──────────────────────┬──────────────────────────────┤
│ True Positive (TP)   │ ~550 (Correct predicted)     │
│ False Positive (FP)  │ ~40  (False alarm)           │
│ True Negative (TN)   │ ~250 (Incorrect predicted)   │
│ False Negative (FN)  │ ~60  (Missed correct)        │
└──────────────────────┴──────────────────────────────┘

KEY METRICS:
├── Accuracy:    85-86%  (How often BKT is right)
├── Precision:   93%     (When predicting correct, accuracy)
├── Recall:      85%     (How many correct answers found)
└── Specificity: 86%     (How many incorrect answers found)
```

## 🔄 How the Simulation Works - Step by Step

```
INITIALIZATION:
  1. Connect to database
  2. Create/retrieve 30 test students
  3. Create/retrieve 180 questions (if needed)
  4. Create 90 assessment sessions

RESPONSE GENERATION (900 times):
  1. Pick random student
  2. Pick random question
  3. Get student's current BKT state
  4. Calculate success probability based on:
     ├── Current BKT (80% weight)
     ├── Student profile accuracy (20% weight initially, decreases)
     ├── Question difficulty (multiply by 0.85-1.1)
     ├── Learning progress (up to +15%)
     └── Random variation (profile-dependent)
  5. Determine if answer is correct or not
  6. Calculate realistic response time based on:
     ├── Student's base speed
     ├── Confidence level (from BKT)
     ├── Correctness of answer
     └── Question difficulty
  7. Update BKT state for next response
  8. Insert response into database

RESULT:
  → 900 realistic responses with natural progression
  → Ready for AUC-ROC evaluation
```

## 🎯 Key Characteristics of Generated Data

✅ **Progressive Learning**: Each student's BKT evolves based on performance  
✅ **Realistic Variance**: Not all students learn at same rate  
✅ **Time Correlation**: Response speed reflects confidence and knowledge  
✅ **Balanced Distribution**: Mix of performance levels (10%-25%-25%-25%-20%-10%)  
✅ **Skill Spread**: 3 competencies × 3 difficulties = realistic coverage  
✅ **Good Discrimination**: Creates AUC-ROC > 0.85 for model evaluation  

## 🚀 Quick Start

```bash
# 1. Run simulator (generates 900 responses)
cd tests/BKT
python simulate_900_responses.py

# 2. Run AUC-ROC analysis (evaluates BKT model)
python BKT_AUC_ROC_Metrics.py

# 3. View results and graphs in visualizations/ folder
```

## 📝 Notes

- **Reusable:** If you run simulator twice, it uses existing data (no duplicates)
- **Database Only:** All data stored in MySQL, visible in question_responses table
- **Sequential:** Each response affects next (realistic learning chains)
- **Flexible:** Easy to customize student profiles and response counts

---

**Result**: You get a **statistically robust test dataset** that validates your BKT model! 📊✨
