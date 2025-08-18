# BKT Integration Setup Guide

## Prerequisites

1. **Python Dependencies** - Install required Python packages:
   ```bash
   pip install mysql-connector-python
   ```

2. **Database Setup** - Run the database tables creation script that you provided.

3. **Sample Data** - Seed the database with sample questions:
   ```bash
   php artisan db:seed --class=QuestionsSeeder
   ```

## Files Created/Modified

### 1. Controller Integration
- **File**: `app/Http/Controllers/AssessmentController.php`
- **Purpose**: Handles assessment flow, integrates with BKT Python script
- **Key Methods**:
  - `index()` - Shows assessment page with real mastery data
  - `startAssessment()` - Initializes BKT assessment session
  - `submitAnswer()` - Records answers and updates BKT in real-time
  - `completeAssessment()` - Finalizes session and applies mastery updates

### 2. Python Integration Script
- **File**: `public/algorithm/bkt_integration.py`
- **Purpose**: Bridges Laravel and your BKT algorithm
- **Actions Supported**:
  - `start_assessment` - Creates new assessment session
  - `record_answer` - Updates BKT after each question
  - `complete_session` - Finalizes session and applies difficulty adjustments

### 3. Frontend Assessment Interface
- **File**: `resources/views/student/assessments.blade.php`
- **Features**:
  - Real-time mastery display from database
  - Interactive question interface
  - Progress tracking with BKT updates
  - Results display with mastery changes

### 4. Routes
- **File**: `routes/web.php`
- **New Endpoints**:
  - `GET /assessments` - Assessment dashboard
  - `POST /assessments/start` - Start assessment
  - `POST /assessments/submit-answer` - Submit answer
  - `POST /assessments/complete` - Complete assessment

### 5. Sample Questions
- **File**: `database/seeders/QuestionsSeeder.php`
- **Purpose**: Provides sample questions for testing the BKT system

## How It Works

### 1. Assessment Flow
1. Student clicks "Start Assessment" for a competency
2. System calls `bkt_integration.py` to initialize session
3. BKT algorithm selects 15 questions based on current mastery
4. Questions are presented one by one
5. Each answer updates BKT mastery in real-time
6. After completion, mastery level and difficulty are adjusted

### 2. BKT Integration
- **Diagnostic Assessment**: For first-time users per competency
- **Adaptive Assessment**: Uses current mastery to select difficulty
- **Real-time Updates**: BKT probability updated after each question
- **Cooldown System**: Prevents immediate question repetition
- **Topic Diversity**: Ensures varied question selection

### 3. Data Flow
```
Laravel Controller -> Python BKT Script -> Database -> Back to Laravel
```

## Testing the Integration

1. **Login as Student**: Use the seeded student account
2. **Navigate to Assessments**: `/assessments`
3. **Start Assessment**: Click on any competency card
4. **Complete Assessment**: Answer the questions and see BKT in action
5. **Check Results**: Observe mastery changes and level adjustments

## Configuration

### Database Connection (bkt_integration.py)
```python
DatabaseManager(
    host='localhost',
    user='root', 
    password='',
    database='aralsipnayan'
)
```

### Python Path (AssessmentController.php)
The controller uses:
```php
$scriptPath = public_path('algorithm/bkt_integration.py');
```

Make sure Python is accessible via the `python` command in your system PATH.

## Troubleshooting

1. **Python Import Errors**: Ensure `bkt.py` is in the same directory as `bkt_integration.py`
2. **Database Connection**: Verify MySQL credentials in the integration script
3. **No Questions**: Run the questions seeder to populate sample data
4. **Permission Issues**: Ensure PHP can execute Python scripts

## Next Steps

1. **Add More Questions**: Expand the questions database for each competency
2. **Enhanced UI**: Add more interactive question types (drag-drop, etc.)
3. **Analytics Dashboard**: Create teacher/admin views of student progress
4. **Performance Optimization**: Cache question pools and optimize database queries
5. **Mobile Responsiveness**: Ensure the assessment interface works on mobile devices

The BKT algorithm is now fully integrated with your Laravel application and ready for testing!
# BKT Integration Setup Guide

## Prerequisites

1. **Python Dependencies** - Install required Python packages:
   ```bash
   pip install mysql-connector-python
   ```

2. **Database Setup** - Run the database tables creation script that you provided.

3. **Sample Data** - Seed the database with sample questions:
   ```bash
   php artisan db:seed --class=QuestionsSeeder
   ```

## Files Created/Modified

### 1. Controller Integration
- **File**: `app/Http/Controllers/AssessmentController.php`
- **Purpose**: Handles assessment flow, integrates with BKT Python script
- **Key Methods**:
  - `index()` - Shows assessment page with real mastery data
  - `startAssessment()` - Initializes BKT assessment session
  - `submitAnswer()` - Records answers and updates BKT in real-time
  - `completeAssessment()` - Finalizes session and applies mastery updates

### 2. Python Integration Script
- **File**: `public/algorithm/bkt_integration.py`
- **Purpose**: Bridges Laravel and your BKT algorithm
- **Actions Supported**:
  - `start_assessment` - Creates new assessment session
  - `record_answer` - Updates BKT after each question
  - `complete_session` - Finalizes session and applies difficulty adjustments

### 3. Frontend Assessment Interface
- **File**: `resources/views/student/assessments.blade.php`
- **Features**:
  - Real-time mastery display from database
  - Interactive question interface
  - Progress tracking with BKT updates
  - Results display with mastery changes

### 4. Routes
- **File**: `routes/web.php`
- **New Endpoints**:
  - `GET /assessments` - Assessment dashboard
  - `POST /assessments/start` - Start assessment
  - `POST /assessments/submit-answer` - Submit answer
  - `POST /assessments/complete` - Complete assessment

### 5. Sample Questions
- **File**: `database/seeders/QuestionsSeeder.php`
- **Purpose**: Provides sample questions for testing the BKT system

## How It Works

### 1. Assessment Flow
1. Student clicks "Start Assessment" for a competency
2. System calls `bkt_integration.py` to initialize session
3. BKT algorithm selects 15 questions based on current mastery
4. Questions are presented one by one
5. Each answer updates BKT mastery in real-time
6. After completion, mastery level and difficulty are adjusted

### 2. BKT Integration
- **Diagnostic Assessment**: For first-time users per competency
- **Adaptive Assessment**: Uses current mastery to select difficulty
- **Real-time Updates**: BKT probability updated after each question
- **Cooldown System**: Prevents immediate question repetition
- **Topic Diversity**: Ensures varied question selection

### 3. Data Flow
```
Laravel Controller -> Python BKT Script -> Database -> Back to Laravel
```

## Testing the Integration

1. **Login as Student**: Use the seeded student account
2. **Navigate to Assessments**: `/assessments`
3. **Start Assessment**: Click on any competency card
4. **Complete Assessment**: Answer the questions and see BKT in action
5. **Check Results**: Observe mastery changes and level adjustments

## Configuration

### Database Connection (bkt_integration.py)
```python
DatabaseManager(
    host='localhost',
    user='root', 
    password='',
    database='aralsipnayan'
)
```

### Python Path (AssessmentController.php)
The controller uses:
```php
$scriptPath = public_path('algorithm/bkt_integration.py');
```

Make sure Python is accessible via the `python` command in your system PATH.

## Troubleshooting

1. **Python Import Errors**: Ensure `bkt.py` is in the same directory as `bkt_integration.py`
2. **Database Connection**: Verify MySQL credentials in the integration script
3. **No Questions**: Run the questions seeder to populate sample data
4. **Permission Issues**: Ensure PHP can execute Python scripts

## Next Steps

1. **Add More Questions**: Expand the questions database for each competency
2. **Enhanced UI**: Add more interactive question types (drag-drop, etc.)
3. **Analytics Dashboard**: Create teacher/admin views of student progress
4. **Performance Optimization**: Cache question pools and optimize database queries
5. **Mobile Responsiveness**: Ensure the assessment interface works on mobile devices

The BKT algorithm is now fully integrated with your Laravel application and ready for testing!

<!-- ADDITIONAL GUIDE -->
<!-- 1. Install python -->
<!-- 2. Install python mysql connector -->
<!-- 3. Change path of python in simple_bkt.py -->