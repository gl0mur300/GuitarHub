<?php
session_start();
include 'db_connect.php';

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Валидация
    if(mb_strlen($username) < 3) {
        $errors[] = 'Имя пользователя должно быть не менее 3 символов';
    }
    if(mb_strlen($password) < 6) {
        $errors[] = 'Пароль должен быть не менее 6 символов';
    }
    
    // Проверка email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный адрес электронной почты';
    }
    
    if(empty($errors)) {
        // Проверка существования пользователя или email
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $errors[] = 'Пользователь с таким именем или почтой уже существует';
        } else {
            // Регистрация
            $hashed_password = md5($password);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit();
            } else {
                $errors[] = 'Ошибка при регистрации. Попробуйте позже.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | GuitarHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Roboto+Mono:wght@300;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-bg: #121212;
            --dark-card: #1e1e1e;
            --dark-accent: #2a2a2a;
            --primary: #e63946;
            --text: #f1faee;
            --text-secondary: #b8b8b8;
        }

        body.dark-theme {
            background-color: var(--dark-bg);
            color: var(--text);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .auth-container {
            background-color: var(--dark-card);
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .auth-form h2 {
            color: var(--primary);
            margin-top: 0;
            text-align: center;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            background-color: var(--dark-accent);
            border: 1px solid #333;
            border-radius: 4px;
            color: var(--text);
            font-size: 1rem;
            box-sizing: border-box;
        }

        .btn-accent {
            background-color: var(--primary);
            color: var(--text);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn-accent:hover {
            background-color: #d62c39;
        }

        .form-footer {
            margin-top: 1.5rem;
            text-align: center;
            color: var(--text-secondary);
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .error-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem 0;
            color: var(--primary);
            text-align: center;
        }

        .error-list li {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body class="dark-theme">
    <div class="auth-container">
        <form class="auth-form" method="POST">
            <h2>Регистрация</h2>
            
            <?php if(!empty($errors)): ?>
                <ul class="error-list">
                    <?php foreach($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Электронная почта</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-accent">Зарегистрироваться</button>
            
            <div class="form-footer">
                Уже есть аккаунт? <a href="login.php">Войти</a>
            </div>
        </form>
    </div>
</body>
</html>
