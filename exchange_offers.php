<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$target_phone_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $stmt = $pdo->prepare("INSERT INTO phones (user_id, brand, model, price, description, is_exchange, status) VALUES (?, 'User', 'Custom', 0, ?, 1, 'pending')");
    $stmt->execute([$_SESSION['user_id'], $description]);
    header("Location: browse.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer for Exchange - ELEX</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo img {
            width: 50px;
            height: 50px;
        }
        .logo h1 {
            font-size: 1.5rem;
            color: #1f2937;
            margin: 0;
        }
        .nav-links {
            display: flex;
            gap: 20px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-links li a {
            color: #1f2937;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease;
        }
        .nav-links li a:hover, .nav-links li a.active {
            color: #1e40af;
        }
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }
        .hamburger span {
            width: 25px;
            height: 3px;
            background-color: #1f2937;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                right: 20px;
                background-color: #ffffff;
                flex-direction: column;
                padding: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                z-index: 10;
            }
            .nav-links.active {
                display: flex;
            }
            .hamburger {
                display: flex;
            }
        }
        .exchange-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .exchange-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .exchange-form label {
            display: block;
            margin-bottom: 15px;
            text-align: left;
            font-size: 1rem;
            color: #4b5563;
        }
        .exchange-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            height: 150px;
            resize: vertical;
        }
        .exchange-form button {
            background-color: #10b981;
            color: #ffffff;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .exchange-form button:hover {
            background-color: #059669;
        }
    </style>
    <script>
        $(document).ready(function() {
            // Navbar hamburger toggle
            $('.hamburger').click(function() {
                $(this).toggleClass('active');
                $('.nav-links').toggleClass('active');
            });

            $('.nav-link').click(function() {
                if ($(window).width() <= 768) {
                    $('.nav-links').removeClass('active');
                    $('.hamburger').removeClass('active');
                }
            });
        });
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="https://via.placeholder.com/50?text=MM" alt="ELEX Logo">
            <h1>ELEX</h1>
        </div>
        <nav class="navbar">
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="about.php" class="nav-link">About</a></li>
                <li><a href="browse.php" class="nav-link">Browse</a></li>
                <li><a href="list_phone.php" class="nav-link">List Phone</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li><a href="register.php" class="nav-link">Register</a></li>
                    <li><a href="login.php" class="nav-link">Login</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <li><a href="admin.php" class="nav-link">Admin Panel</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <section class="exchange-container">
            <div class="exchange-form">
                <h2>Offer Your Device for Exchange</h2>
                <p>Exchange for: <?php echo $target_phone_id ? 'Phone ID ' . htmlspecialchars($target_phone_id) : 'Selected Phone'; ?></p>
                <form method="POST">
                    <label for="description">Description of Your Device:
                        <textarea name="description" id="description" required placeholder="e.g., Good condition, minor scratch, works fine..."></textarea>
                    </label>
                    <button type="submit">Submit Offer</button>
                </form>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>