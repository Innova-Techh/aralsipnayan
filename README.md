# 📘 AralSipnayan

**AralSipnayan** is an intelligent mathematics assessment tool designed for elementary students (Grades 3 and 4). The system dynamically evaluates students' understanding of mathematical concepts through adaptive testing, fair question distribution, and personalized tracking of learning progress.

## 🚀 Key Features

- 🧠 **Adaptive Questioning**  
  Uses **Bayesian Knowledge Tracing (BKT)** to estimate a student's mastery level in real time and select questions that match their current understanding.
  
- 🎲 **Fair and Randomized Question Sets**  
  Integrates the **Fisher–Yates Shuffling Algorithm** to ensure unbiased and non-repetitive question sequences during each session.

- 🧾 **Rule-Based Question Management**  
  Employs a **rule-based algorithm** to manage the logic of the assessment flow—tracking whether questions have already been shown or shuffled, and ensuring that no duplicates are presented within a session.

## ⚙️ Core Algorithms and Models

- **Fisher–Yates Shuffle**  
  Ensures each student receives a unique, unbiased order of questions every time.

- **Bayesian Knowledge Tracing**  
  Tracks student proficiency across math skills and dynamically adapts the difficulty of subsequent questions.

- **Rule-Based Logic**  
  Manages question state (e.g., shown, unshown, shuffled) to prevent repetition and maintain assessment integrity.

## 🎯 Educational Objectives

AralSipnayan is developed to support:

- Personalized and adaptive learning in mathematics  
- Data-driven intervention strategies by educators  
- Real-time insight into learner progress and concept mastery

---

## 🔧 Installation & Setup

### 🔑 Setting Up Laravel `.env` and `APP_KEY`

Follow these steps after cloning the project:

#### 1. Copy `.env` Example File
```bash
cp .env.example .env
```

#### 2. Edit `.env` File
Open `.env` and set your local database configuration:

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

#### 3. Generate Laravel APP_KEY
```bash
php artisan key:generate
```

#### 4. Clear and Cache Config (Recommended)
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

## 🗄️ Database Setup

### Importing the SQL File into phpMyAdmin

#### 1. Open phpMyAdmin
Usually accessible at:
```
http://localhost/phpmyadmin
```

#### 2. Import the Database
- Create a new database named `aralsipnayan_db`
- Import the `aralsipnayandb.sql` file from the `db` folder
- Click on "Import" tab in phpMyAdmin
- Choose the SQL file and click "Go"

## 🚀 Running the Application

After completing the setup steps above:

1. **Start your local server:**
   ```bash
   php artisan serve
   ```

2. **Access the application:**
   ```
   http://localhost:8000
   ```

## 📋 Requirements

- PHP >= 8.0
- Laravel Framework
- MySQL Database
- Composer
- XAMPP/WAMP (for local development)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).