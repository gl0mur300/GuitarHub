<?php
session_start();
include 'db_connect.php';

if(isset($_SESSION['user_id'])) {
    // Обработка редиректа после авторизации
    if(isset($_GET['redirect']) && $_GET['redirect'] === 'artist' && isset($_GET['name'])) {
        header("Location: artist.php?name=" . urlencode($_GET['name']));
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';
$info = '';
if(isset($_GET['redirect']) && $_GET['redirect'] === 'artist') {
    $info = 'Для просмотра информации об исполнителе необходимо авторизоваться';
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if(md5($password) === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Обработка редиректа после успешной авторизации
            if(isset($_POST['redirect']) && $_POST['redirect'] === 'artist' && isset($_POST['artist_name'])) {
                header("Location: artist.php?name=" . urlencode($_POST['artist_name']));
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Неверный пароль";
        }
    } else {
        $error = "Пользователь не найден";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | GuitarHub</title>
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

        .error-message {
            color: var(--primary);
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .info-message {
            color: var(--text);
            text-align: center;
            margin-bottom: 1.5rem;
            background-color: var(--dark-accent);
            padding: 0.8rem;
            border-radius: 4px;
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
            <h2>Вход в систему</h2>
            
            <?php if(!empty($info)): ?>
                <div class="info-message"><?= htmlspecialchars($info) ?></div>
            <?php endif; ?>
            
            <?php if(!empty($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if(isset($_GET['redirect']) && $_GET['redirect'] === 'artist'): ?>
                <input type="hidden" name="redirect" value="artist">
                <input type="hidden" name="artist_name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-accent">Войти</button>
            
            <div class="form-footer">
                Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a>
            </div>
        </form>
    </div>
</body>
</html>