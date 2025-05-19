<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: 8px;
            padding-left: 40px;
        }

        .input-group-text {
            background: none;
            border: none;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .btn-login {
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            padding: 10px;
        }

        .btn-login:hover {
            background: #007bff;
            color: white;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h3 class="text-center text-primary mb-4">Admin Login</h3>
    
    <form action="proses_login.php" method="POST">
        <div class="mb-3 position-relative">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" name="username" placeholder="Username" required>
        </div>

        <div class="mb-3 position-relative">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-login">Login</button>
    </form>
</div>

</body>
</html>
