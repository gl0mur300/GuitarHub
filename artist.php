<?php
session_start();
include 'db_connect.php';

// Проверка авторизации
if(!isset($_SESSION['user_id'])) {
    // Добавим редирект с указанием артиста, чтобы вернуться на эту страницу после логина
    $artist_name = $_GET['name'] ?? '';
    header("Location: login.php?redirect=artist&name=" . urlencode($artist_name));
    exit();
}

$artist_name = $_GET['name'] ?? '';
if(empty($artist_name)) {
    // Выводим ошибку, если имя артиста не передано
    echo "Ошибка: имя артиста не передано.";
    exit();
}

// Получение информации об артисте из БД
$stmt = $conn->prepare("SELECT * FROM artists WHERE name = ?");
$stmt->bind_param("s", $artist_name);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    // Артист не найден в базе данных, выводим ошибку
    echo "Артист не найден.";
    exit();
}

$artist = $result->fetch_assoc();

// Получение гитар, связанных с артистом
$stmt = $conn->prepare("SELECT g.name FROM guitars g 
                       JOIN artist_guitars ag ON g.id = ag.guitar_id 
                       JOIN artists a ON ag.artist_id = a.id 
                       WHERE a.name = ?");
$stmt->bind_param("s", $artist_name);
$stmt->execute();
$guitars_result = $stmt->get_result();
$guitars = [];
while($row = $guitars_result->fetch_assoc()) {
    $guitars[] = $row['name'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($artist['name']) ?> | GuitarHub</title>
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
            line-height: 1.6;
        }

        .main-header {
            background-color: var(--dark-card);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
        }

        .artist-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .artist-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .artist-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 2rem;
            border: 3px solid var(--primary);
        }

        .artist-info {
            flex: 1;
        }

        .artist-name {
            color: var(--primary);
            margin: 0;
            font-size: 2.5rem;
        }

        .artist-bio {
            color: var(--text-secondary);
            margin-top: 1rem;
            line-height: 1.6;
        }

        .artist-section {
            margin-top: 3rem;
        }

        .section-title {
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 0.5rem;
        }

        .guitar-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .guitar-item {
            background-color: var(--dark-card);
            padding: 1.5rem;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .guitar-item:hover {
            transform: translateY(-5px);
        }

        .back-button {
            display: inline-block;
            margin-top: 2rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .artist-header {
                flex-direction: column;
                text-align: center;
            }
            
            .artist-image {
                margin-right: 0;
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body class="dark-theme">
    <header class="main-header">
        <nav class="navbar">
            <a href="index.php" class="logo">GuitarHub</a>
            <ul class="nav-menu">
                <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item"><span class="username"><?= htmlspecialchars($_SESSION['username']) ?></span></li>
                    <li class="nav-item"><a href="logout.php">Выход</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="login.php">Войти</a></li>
                    <li class="nav-item"><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="artist-container">
        <div class="artist-header">
            <img src="images/artists/<?= htmlspecialchars($artist['image']) ?>" alt="<?= htmlspecialchars($artist['name']) ?>" class="artist-image">
            <div class="artist-info">
                <h1 class="artist-name"><?= htmlspecialchars($artist['name']) ?></h1>
                <p class="artist-bio"><?= htmlspecialchars($artist['bio']) ?></p>
            </div>
        </div>

    

        <a href="index.php" class="back-button">← Назад к гитарам</a>
    </div>

    <footer class="main-footer">
        <p>© <?= date('Y') ?> GuitarHub. Все права защищены.</p>
    </footer>
</body>
</html>
