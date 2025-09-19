This guide shows how to run the Python bot to automatically answer DIAGNOSTIC and ASSESSMENTS for one or many student accounts concurrently.

### 1) Prerequisites
- Python 3.10+ installed
- Install deps:
```
pip install requests mysql-connector-python
```
- Laravel app running locally (via Apache/XAMPP or `php artisan serve`)
- Database seeded with students. If needed:
```
php artisan migrate:fresh --seed
```
The seeder creates sections A/B/C with 5 students each (`studenta1..a5`, `studentb1..b5`, `studentc1..c5`) with password `123`.

### 2) Bot script
Use the Python script in the repo ambot.py

### 3) Base URL
By default the bot uses `http://127.0.0.1:8000`. just change it if it is different, edit the script’s `base_url` default or pass it where supported.

### 4) Commands
- Single student regular quiz (not multi-user like diagnostic)
  - Prerequisites:
    - Student must have completed the diagnostic
    - Log in with the target student
    - Finish onboarding (if not yet finished)
    - Go to the assessments page
  - Run:
```bash
python ambot.py quiz STUDENT_USERNAME 123 number_algebra [ACCURACY] [SPEED]
```
  - Examples:
```bash
python ambot.py quiz studenta1 123 number_algebra 0.8 0.4
python ambot.py quiz studenta2 123 measurement_geometry 0.7 0.5
```

- Single student diagnostic:
```
python ambot.py diagnostic STUDENT_USERNAME 123 number_algebra [ACCURACY] [SPEED]
```
Examples:
```
python ambot.py diagnostic studenta1 123 number_algebra 0.8 0.4
python ambot.py diagnostic studenta2 123 measurement_geometry 0.7 0.5
```

- Multiple students concurrently (diagnostic only):
```
python ambot.py multi studenta1,studentb1,studentc1 123 number_algebra [ACCURACY] [SPEED] [WORKERS]
```
Examples:
```
python ambot.py multi studenta1,studentb1,studentc1 123 number_algebra
python ambot.py multi studenta1,studentb1,studentc1 123 number_algebra 0.7 0.5 3
```
Notes:
- ACCURACY: 0.0–1.0 (probability the bot answers correctly). Default 0.7
- SPEED: 0.0–1.0 (fraction of max allowed time used). Default 0.5
- WORKERS: number of parallel sessions. Default min(3, users)




### 6) Troubleshooting
- "Failed to detect current question from quiz page"
  - Ensure the quiz Blade template exposes the current question id. Add anywhere in `resources/views/student/quiz.blade.php`:
  ```html
  <div id="current-question" data-question-id="{{ $question->question_id }}"></div>
  ```
  - The bot scrapes `data-question-id` or inline JS containing `question_id`.

  - or just refresh db

- HTTP 419 / CSRF issues
  - Ensure your layout includes the CSRF meta tag:
  ```html
  <meta name="csrf-token" content="{{ csrf_token() }}">
  ```
  - clear browser cookies if needed.

  - or just refresh db

- Wrong base URL
  - If using XAMPP at `http://localhost/aralsipnayan`, update the script’s `base_url` to that value.

  - or just refresh db

- Database mismatch
  - The bot connects directly to MySQL. Confirm its DB settings in the script match your `.env`. Ensure questions and assessments exist.

  - or just refresh db

- Concurrency
  - If your local server is slow, reduce `[WORKERS]` to 2–3.

  - or just refresh db

### 7) Useful seed data
Students created by `StudentSectionsSeeder` (password `123`):
- A: `studenta1` ... `studenta5`
- B: `studentb1` ... `studentb5`
- C: `studentc1` ... `studentc5`

### 8) Tips
- The diagnostic flow navigates to the assessments page, opens the competency category, then the diagnostic quiz route and auto-answers.
- Accuracy and speed knobs help simulate different student behaviors.


