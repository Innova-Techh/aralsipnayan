# 📘 AralSipnayan

**AralSipnayan** is an intelligent gamified mathematics assessment tool designed for Grade 6 elementary students. The system dynamically evaluates students' understanding of mathematical concepts through adaptive testing, fair question distribution, and personalized tracking of learning progress aligned with the Department of Education's MATATAG curriculum.


## 🎯 Project Overview

AralSipnayan addresses the significant gap in mathematics proficiency among Filipino students, as highlighted by the 2022 PISA results where only 16% of Filipino 15-year-olds reached basic proficiency in math compared to the OECD average of 69%. The system serves as a supplemental tool for the MATATAG curriculum, focusing on three core learning competencies:

- **Number and Algebra**
- **Measurement and Geometry** 
- **Data and Probability**

## 🚀 Key Features

### 🧠 **Adaptive Assessment Technology**
- **Bayesian Knowledge Tracing (BKT)** - Estimates student mastery levels in real-time and adapts question difficulty based on performance and response time
- **Enhanced BKT Formula** - Incorporates time response factors with scoring weights (Beginner: 0.8, Intermediate: 1.0, Advanced: 1.2)
- **Dynamic Difficulty Progression** - Students progress through three proficiency levels based on DepEd's Mean Percentage Score thresholds:
  - Beginner: 0-49 (Not Proficient + Low Proficient)
  - Intermediate: 50-74 (Nearly Proficient) 
  - Advanced: 75-100 (Proficient + Highly Proficient)

### 🎲 **Fair and Randomized Question Distribution**
- **Fisher-Yates Shuffling Algorithm** - Ensures unbiased and non-repetitive question sequences for each assessment session
- **SmartShuffle System** - Guarantees no two students receive identical question sets
- **NAT-Aligned Question Bank** - Questions validated by DepEd experts to ensure curriculum alignment

### 🧾 **Intelligent Question Management**
- **Rule-Based Algorithm (CogniSeq)** - Manages question cooldowns and prevents repetition using spaced repetition principles
- **Adaptive Question Selection** - Filters available questions based on student mastery level and previous responses
- **Diagnostic Assessments** - Initial 3-phase diagnostic exams for accurate student placement

### 🎮 **Gamification Elements**
- **Points and Progress Tracking** - Motivates students through achievement-based progression
- **Level-Based Advancement** - Students advance through difficulty levels based on mastery thresholds
- **Achievement Badges** - Recognition system for milestones and consistent performance
- **Reduced Math Anxiety** - Game-like interface designed to decrease mathematical anxiety prevalent among Filipino learners

## ⚙️ Core Technology Stack

### **Frontend Development**
- **Laravel v10** - Modern PHP web application framework for system architecture
- **Tailwind CSS v4.1** - Utility-first CSS framework for responsive UI design
- **HTML5, CSS3, JavaScript (ES6+)** - Standard web technologies for dynamic interfaces
- **DepEd Color Palette** - Official Department of Education branding compliance

### **Backend & Algorithms**
- **PHP v8.3.20** - Server-side scripting for Laravel backend logic
- **Python v3.13.3** - Implementation of core algorithms:
  - Fisher-Yates Shuffling Algorithm
  - Bayesian Knowledge Tracing Model
  - Rule-Based Question Sequencing
- **MySQL v8.0** - Relational database for user data and performance tracking

### **Development & Deployment**
- **XAMPP v8.2.12** - Local development environment (Apache, MySQL, PHP)
- **Heroku** - Cloud deployment with PHP & Python buildpacks
- **Git v2.49.0 & GitHub v3.10.1** - Version control and repository management
- **Visual Studio Code v1.100** - Primary development environment
- **Google Colab** - Algorithm development and testing environment
- **Firefox Developer Edition** - Responsive design testing and debugging

## 🏫 Educational Implementation

### **Institutional Focus**
- **Primary Testing Site**: Pembo Elementary School
- **Target Demographic**: Grade 6 elementary students
- **Curriculum Alignment**: DepEd MATATAG framework
- **Assessment Standards**: National Achievement Test (NAT) compatibility

### **User Management System**
- **Student Profiles** - Secure login, progress tracking, and performance analytics
- **Teacher Dashboard** - Classroom management, custom assessment creation, progress monitoring
- **Admin Panel** - System-wide user management, teacher/admin account administration

### **Assessment Process**
1. **Initial Diagnostic** - 3-phase assessment for accurate level placement
2. **Adaptive Testing** - BKT-driven question selection based on real-time mastery evaluation
3. **Progress Tracking** - Continuous monitoring with difficulty level adjustments
4. **Spaced Repetition** - Rule-based cooldown system for reinforced learning

## 🔧 Installation & Setup

### **Prerequisites**
- PHP >= 8.0
- Python >= 3.13
- MySQL >= 8.0
- Composer
- XAMPP/WAMP (for local development)

### **Python Dependencies**
```bash
pip install mysql-connector-python
```

### **Laravel Setup**

#### 1. Clone and Configure Environment
```bash
git clone [repository-url]
cd aralsipnayan
cp .env.example .env
```

#### 2. Environment Configuration
Edit `.env` file with your database settings:
```env
APP_NAME=AralSipnayan
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aralsipnayandb
DB_USERNAME=root
DB_PASSWORD=
```

#### 3. Generate Application Key
```bash
php artisan key:generate
```

#### 4. Clear and Cache Configuration
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### **Database Setup**

#### 1. Create Database
- Open phpMyAdmin: `http://localhost/phpmyadmin`
- Create new database: `aralsipnayandb`


#### 2. Import Database Structure
- Import `aralsipnayandb.sql` from the `dbfolder` directory
- Alternatively, run migrations: `php artisan migrate`

#### 3. Seed Sample Data
```bash
php artisan db:seed --class=QuestionsSeeder
```

### **Algorithm Integration**
Ensure Python scripts are accessible:
- Place BKT algorithms in `public/algorithm/` directory
- Verify Python path configuration in `AssessmentController.php`
- Test database connectivity for Python integration

## 🚀 Running the Application

### **Start Local Development Server**
```bash
php artisan serve
```

### **Access Application**
```
http://localhost:8000
```

### **Default Login Credentials**
- **Student**: student1 / password
- **Teacher**: teacher1 / password  
- **Admin**: admin1 / password

## 📊 System Architecture

### **Assessment Flow**
1. **Student Registration/Login** → **Diagnostic Assessment** → **Level Classification**
2. **Adaptive Question Selection** → **Real-time BKT Updates** → **Progress Evaluation**
3. **Difficulty Adjustment** → **Spaced Repetition Application** → **Performance Analytics**

### **Algorithm Integration**
```
Laravel Controller ↔ Python BKT Scripts ↔ MySQL Database ↔ Frontend Interface
```

### **Data Processing Pipeline**
- **Input**: Student responses + response time
- **Processing**: BKT probability calculation + Fisher-Yates shuffling
- **Output**: Adaptive question selection + mastery level updates

## 🎓 Educational Objectives

### **Primary Goals**
- **Reduce Mathematics Anxiety** - Gamified approach to decrease fear-based learning barriers
- **Improve Student Engagement** - Interactive assessment experiences tailored to individual learning needs
- **Enhance Mathematical Proficiency** - Personalized learning paths based on real-time mastery assessment
- **Support Curriculum Alignment** - Direct integration with DepEd MATATAG learning competencies

### **Research Impact**
- **Educational Technology Advancement** - Bridges gap between technology and mathematics pedagogy
- **Filipino Learner-Specific Solutions** - Addresses cultural and educational context of Philippine mathematics education
- **Assessment Innovation** - Demonstrates effectiveness of adaptive, gamified assessment tools
- **Teacher Professional Development** - Provides data-driven insights for instructional improvements

## 📈 Assessment Methodology

### **Bayesian Knowledge Tracing Implementation**
The system uses an enhanced BKT model incorporating:
- **Base Accuracy Calculation**: (Correct answers / Total questions)
- **Time Factor Integration**: Normalized response time weighting
- **Difficulty Weighting**: Level-specific multipliers for comprehensive mastery scoring

### **Fisher-Yates Shuffle Integration**
- **Unbiased Randomization**: Mathematical guarantee of fair question distribution
- **Reproducible Sequences**: Seed-based generation for testing consistency
- **Database Integration**: Real-time question pool management

### **Rule-Based Question Management**
- **Cooldown Systems**: Prevents immediate question repetition
- **Mastery-Based Filtering**: Selects appropriate difficulty questions
- **Topic Diversity**: Ensures balanced coverage across learning competencies

## 📋 System Requirements

### **Minimum Hardware**
- **Processor**: Dual-core 2.0 GHz or equivalent
- **RAM**: 4 GB minimum, 8 GB recommended
- **Storage**: 2 GB available space
- **Network**: Stable internet connection for deployment

### **Software Dependencies**
- **Web Server**: Apache 2.4+ or Nginx
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Programming Languages**: PHP 8.3+, Python 3.13+
- **Browser Support**: Modern browsers (Chrome 90+, Firefox 88+, Safari 14+)

## 🔄 Study Timeline & Scope

### **Development Period**
- **Start Date**: May 12, 2025
- **End Date**: May 1, 2026
- **Geographic Scope**: Pembo Elementary School, Philippines
- **Academic Focus**: Grade 6 Mathematics Education

### **Research Limitations**
- **Curriculum Specificity**: Limited to DepEd MATATAG framework
- **Geographic Constraints**: Results applicable primarily to Philippine educational context
- **Subject Area**: Focus exclusively on mathematics competencies
- **Grade Level**: Designed specifically for Grade 6 elementary students

## 🤝 Contributors

**Development Team**: Ejay Buscato, Krissa Beringuel, Joshua Fernandez, Sean Sicat

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🎯 Future Enhancements

### **Planned Features**
- **Multi-Grade Support** - Expansion to other elementary grade levels
- **Subject Area Extension** - Implementation for Science and English curricula
- **Advanced Analytics** - Enhanced teacher reporting and student progress visualization
- **Mobile Application** - Native iOS and Android applications for broader accessibility
- **Offline Capability** - Assessment functionality without internet connectivity

### **Research Extensions**
- **Longitudinal Studies** - Long-term impact assessment on student mathematical achievement
- **Cross-Cultural Validation** - Testing effectiveness in diverse educational contexts
- **Teacher Training Programs** - Professional development modules for effective implementation
- **Parent Engagement Tools** - Home-based learning support and progress monitoring

---

**AralSipnayan** represents a significant advancement in educational technology for Philippine mathematics education, combining cutting-edge algorithms with proven pedagogical principles to create an engaging, effective, and culturally appropriate learning assessment tool.
