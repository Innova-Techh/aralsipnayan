<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            font-size: 1.1rem;
        }

        .split-left {
            background: linear-gradient(135deg, #003c8f, #1976d2);
            height: 100vh;
        }

        .split-right {
            background: #f7f7f7;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #fff;
            padding: 60px 50px;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 460px;
        }

        .btn-login {
            background-color: #0d47a1;
            color: white;
            font-size: 1.1rem;
            padding: 12px;
        }

        .form-label {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .form-control {
            border: 1px solid #ccc !important;
            box-shadow: none;
            font-size: 1.1rem;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #0d47a1 !important;
            box-shadow: 0 0 0 0.15rem rgba(13, 71, 161, 0.25);
        }

        .login-box h2 {
            font-size: 2rem;
        }

        .login-box p {
            font-size: 1rem;
        }

        .login-box h5 {
            font-size: 1.4rem;
        }
    </style>
</head>
<body>

<div class="row g-0">
    <div class="col-md-6 split-left d-none d-md-block"></div>
    <div class="col-md-6 split-right">
        <div class="login-box text-center">
            <h2 class="fw-bold text-primary mb-1">Aral<span class="text-dark">Sipnayan</span></h2>
            <p class="text-muted mb-4">Math learning made fun!</p>
            <h5 class="fw-semibold mb-4">Student Login</h5>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-3 text-start">
                    <label for="student_number" class="form-label">Student Number</label>
                    <input type="text" class="form-control" name="student_number" id="student_number" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-login w-100">Login</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>