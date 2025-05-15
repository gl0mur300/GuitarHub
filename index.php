<?php
session_start();
include 'db_connect.php';

function slugify($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electric Guitars Collection</title>
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
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-header {
            background-color: var(--dark-card);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            width: 100%;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
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

        .username {
            color: var(--primary);
            font-weight: 600;
            margin-right: 1rem;
        }

        .guitar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
            flex: 1;
        }

        .guitar-card {
            background-color: var(--dark-card);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: row;
            height: 600px;
        }

        .guitar-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .card-image {
            width: 60%;
            height: 100%;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .guitar-card:hover .card-image img {
            transform: scale(1.05);
        }

        .card-content {
            width: 40%;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-content h3 {
            margin-top: 0;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .excerpt {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .artist-preview {
            display: flex;
            align-items: center;
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid var(--dark-accent);
        }

        .artist-preview img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }

        .artist-preview span {
            font-weight: 600;
        }

        .partners {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        .partners h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .partners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .partner-card {
            background-color: var(--dark-card);
            padding: 1.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
            height: 100px;
        }

        .partner-card:hover {
            transform: scale(1.05);
        }

        .partner-card img {
            max-width: 100%;
            max-height: 70px;
            filter: grayscale(100%) brightness(0.8);
            transition: filter 0.3s ease;
        }

        .partner-card:hover img {
            filter: grayscale(0%) brightness(1);
        }

        .main-footer {
            background-color: var(--dark-card);
            color: var(--text-secondary);
            text-align: center;
            padding: 1.5rem;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                padding: 1rem;
            }

            .logo {
                margin-bottom: 1rem;
                font-size: 2rem;
            }

            .nav-menu {
                width: 100%;
                justify-content: space-around;
            }

            .nav-item {
                margin: 0;
            }

            .guitar-grid {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .guitar-card {
                flex-direction: column;
                height: auto;
            }

            .card-image,
            .card-content {
                width: 100%;
            }

            .card-image {
                height: 250px;
            }
        }
    </style>
</head>
<body class="dark-theme">
    <header class="main-header">
        <nav class="navbar">
            <div class="logo">GuitarHub</div>
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

    <main class="guitar-grid">
        <?php
        $result = $conn->query("SELECT * FROM guitars");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $guitar_image = 'images/' . $row['image'] . '?v=' . time();
                $artist_image = 'images/artists/' . slugify($row['artist']) . '.jpg?v=' . time();

                if (!file_exists('images/' . $row['image'])) {
                    $guitar_image = 'images/no-image.jpg?v=' . time();
                }

                if (!file_exists('images/artists/' . slugify($row['artist']) . '.jpg')) {
                    $artist_image = 'images/artists/no-avatar.jpg?v=' . time();
                }
                ?>
                <article class="guitar-card" onclick="window.location='guitar.php?id=<?= $row['id'] ?>'">
                    <div class="card-image">
                        <img src="<?= $guitar_image ?>" alt="<?= $row['name'] ?>">
                    </div>
                    <div class="card-content">
                        <h3><?= $row['name'] ?></h3>
                        <p class="excerpt"><?= $row['description'] ?></p>
                        <div class="artist-preview">
                            <img src="<?= $artist_image ?>" alt="<?= $row['artist'] ?>">
                            <span><?= $row['artist'] ?></span>
                        </div>
                    </div>
                </article>
                <?php
            }
        } else {
            echo '<p class="no-guitars">Гитары не найдены в базе данных</p>';
        }
        ?>
    </main>

    <section class="partners">
        <h2>Где купить</h2>
        <div class="partners-grid">
            <a href="https://www.thomann.de" target="_blank" class="partner-card">
                <img src="images/partners/thomann.png" alt="Thomann">
            </a>
            <a href="https://www.sweetwater.com" target="_blank" class="partner-card">
                <img src="images/partners/sweetwater.png" alt="Sweetwater">
            </a>
            <a href="https://www.muztorg.ru" target="_blank" class="partner-card">
                <img src="images/partners/muztorg.png" alt="Музторг">
            </a>
        </div>
    </section>

    <footer class="main-footer">
        <p>© <?= date('Y') ?> GuitarHub. Все права защищены.</p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
