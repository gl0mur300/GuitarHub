<?php
session_start();
include 'db_connect.php';

function slugify($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

$guitar_id = $_GET['id'];
$guitar = $conn->query("SELECT * FROM guitars WHERE id = $guitar_id")->fetch_assoc();

$guitar_image = 'images/' . $guitar['image'];
$artist_image = 'images/artists/' . slugify($guitar['artist']) . '.jpg';
$sample_audio = !empty($guitar['sample']) ? 'audio/' . $guitar['sample'] : '';

if (!file_exists($guitar_image)) {
    $guitar_image = 'images/no-image.jpg';
}

if (!file_exists($artist_image)) {
    $artist_image = 'images/artists/no-avatar.jpg';
}

if (!empty($sample_audio) && !file_exists($sample_audio)) {
    $sample_audio = '';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $guitar['name'] ?> | GuitarHub</title>
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
            zoom: 80%;
        }

        .page-wrapper {
            max-width: 100%;
            margin: 0 auto;
            background-color: var(--dark-bg);
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
            font-size: 3.2rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            margin-left: 1.5rem;
        }

        .nav-item a {
            color: var(--text);
            text-decoration: none;
            transition: color 0.3s;
        }

        .nav-item a:hover {
            color: var(--primary);
        }

        .guitar-detail {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .guitar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .guitar-header h1 {
            color: var(--primary);
            margin: 0;
            font-size: 2.5rem;
        }

        .back-button {
            color: var(--text);
            cursor: pointer;
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .back-button:hover {
            color: var(--primary);
        }

        .guitar-content {
            display: flex;
            gap: 3rem;
        }

        .gallery {
            flex: 1;
        }

        .gallery img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .audio-player {
            margin-top: 1.5rem;
            background-color: var(--dark-card);
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .audio-player audio {
            width: 100%;
            outline: none;
        }

        .guitar-info {
            flex: 1;
        }

        .description {
            margin-bottom: 3rem;
        }

        .description h3 {
            color: var(--primary);
            font-size: 1.8rem;
            margin-top: 0;
            margin-bottom: 1rem;
        }

        .description p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .artist-info h3 {
            color: var(--primary);
            font-size: 1.8rem;
            margin-top: 0;
            margin-bottom: 1.5rem;
        }

        .artist-card {
            display: flex;
            align-items: center;
            background-color: var(--dark-card);
            padding: 1.5rem;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .artist-card:hover {
            transform: translateY(-5px);
        }

        .artist-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 2rem;
            border: 3px solid var(--primary);
        }

        .artist-details h4 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--text);
        }

        .btn-more {
            background-color: var(--primary);
            color: var(--text);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 1rem;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn-more:hover {
            background-color: #d62c39;
        }

        .main-footer {
            background-color: var(--dark-card);
            color: var(--text-secondary);
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .guitar-content {
                flex-direction: column;
            }

            .guitar-header h1 {
                font-size: 2rem;
            }

            .artist-card {
                flex-direction: column;
                text-align: center;
            }

            .artist-card img {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body class="dark-theme">
<div class="page-wrapper">
    <header class="main-header">
        <nav class="navbar">
            <a href="index.php" class="logo">GuitarHub</a>
            <ul class="nav-menu">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a href="profile.php">Профиль</a></li>
                    <li class="nav-item"><a href="logout.php">Выход</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="login.php">Войти</a></li>
                    <li class="nav-item"><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="guitar-detail">
        <div class="guitar-header">
            <h1><?= $guitar['name'] ?></h1>
            <div class="back-button" onclick="window.history.back()">← Назад</div>
        </div>

        <div class="guitar-content">
            <div class="gallery">
                <img src="<?= $guitar_image ?>" alt="<?= $guitar['name'] ?>">
            </div>

            <div class="guitar-info">
                <div class="description">
                    <h3>Описание</h3>
                    <p><?= $guitar['description'] ?></p>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    $audio_path = 'audio/' . $guitar['id'] . '.mp3';
                    if (file_exists($audio_path)) : ?>
                        <div class="description">
                            <h3>Аудио пример</h3>
                            <audio controls style="width: 100%; margin-top: 1rem;">
                                <source src="<?= $audio_path ?>" type="audio/mpeg">
                                Ваш браузер не поддерживает аудиоплеер.
                            </audio>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="description">
                        <h3>Аудио пример</h3>
                        <p style="color: var(--text-secondary); font-size: 1.1rem;">
                            Войдите в аккаунт, чтобы прослушать аудио.
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($guitar['history'])): ?>
                    <div class="description">
                        <h3>История</h3>
                        <p><?= nl2br($guitar['history']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($guitar['features'])): ?>
                    <div class="description">
                        <h3>Технические особенности</h3>
                        <ul style="padding-left: 1.2rem; color: var(--text-secondary); font-size: 1.1rem;">
                            <?php foreach (explode("\n", $guitar['features']) as $feature): ?>
                                <li><?= htmlspecialchars(trim($feature)) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="artist-info">
                    <h3>Известный исполнитель</h3>
                    <div class="artist-card">
                        <img src="<?= $artist_image ?>" alt="<?= $guitar['artist'] ?>">
                        <div class="artist-details">
                            <h4><?= $guitar['artist'] ?></h4>
                            <button class="btn-more" onclick="window.location.href='artist.php?name=<?= urlencode($guitar['artist']) ?>'">Подробнее</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <p>© <?= date('Y') ?> GuitarHub. Все права защищены.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                if(img.src.indexOf('?') === -1) {
                    img.src = img.src + '?v=' + Date.now();
                }
            });
        });
    </script>
</div>
</body>
</html>

