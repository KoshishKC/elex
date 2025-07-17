<?php
session_start();
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ELEX
</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.0/vanilla-tilt.min.js"></script>
    <style>
        .why-choose-us {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background-color: #f8fafc;
            padding: 40px 20px;
            border-radius: 12px;
            margin: 20px auto;
            max-width: 1000px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .why-choose-us h3 {
            font-size: 2.2rem;
            color: #1e40af;
            margin-bottom: 20px;
        }
        .why-choose-us ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            width: 100%;
        }
        .why-choose-us li {
            display: flex;
            align-items: center;
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        .why-choose-us li:hover {
            transform: translateY(-3px);
        }
        .why-choose-us li img {
            width: 40px;
            height: 40px;
            margin-right: 15px;
            object-fit: cover;
            border-radius: 4px;
        }
        .why-choose-us li span {
            font-size: 1.1rem;
            color: #1f2937;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="https://via.placeholder.com/50?text=MM" alt="ELEX
 Logo">
            <h1>ELEX
</h1>
        </div>
        <nav class="navbar">
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="about.php" class="nav-link active">About</a></li>
                <li><a href="browse.php" class="nav-link">Browse</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="list_phone.php" class="nav-link">List Phone</a></li>
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
        <section class="hero about-hero">
            <div class="hero-overlay">
                <h2>About ELEX
</h2>
                <p>Your trusted platform for buying, selling, and exchanging mobile phones.</p>
            </div>
        </section>
        <section class="about-content section">
            <div class="about-card">
                <img src="https://via.placeholder.com/400x200?text=Mission+Image" alt="Mission Image" class="about-image">
                <h3>Our Mission</h3>
                <p>At ELEX
, we aim to create a seamless and secure platform for mobile phone enthusiasts to buy, sell, and exchange devices with ease. Our goal is to connect people with their dream phones while promoting sustainability through exchanges.</p>
            </div>
            <div class="why-choose-us">
                <h3>Why Choose Us?</h3>
                <ul>
                    <li>
                        <img src="https://via.placeholder.com/40?text=Checkmark" alt="Verified Listings Icon">
                        <span>Verified Listings: All phones are approved by our admin team for authenticity.</span>
                    </li>
                    <li>
                        <img src="https://via.placeholder.com/40?text=Lock" alt="Secure Transactions Icon">
                        <span>Secure Transactions: Buy with confidence through our trusted platform.</span>
                    </li>
                    <li>
                        <img src="https://via.placeholder.com/40?text=Swap" alt="Exchange Options Icon">
                        <span>Exchange Options: Trade your old phone for a new one effortlessly.</span>
                    </li>
                </ul>
            </div>
            <div class="about-card">
                <img src="https://via.placeholder.com/400x200?text=Contact+Image" alt="Contact Image" class="about-image">
                <h3>Contact Us</h3>
                <p>Email: <a href="mailto:support@ELEX.com">support@ELEX.com</a></p>
                <p>Phone: +977 9852272824</p>
                <p>Maitighar, Nepal</p>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
    <script>
        $(document).ready(function() {
            // Hamburger menu toggle
            $('.hamburger').click(function() {
                $(this).toggleClass('active');
                $('.nav-links').slideToggle(300);
            });

            $('.nav-link').click(function() {
                if ($(window).width() <= 768) {
                    $('.nav-links').slideUp(300);
                    $('.hamburger').removeClass('active');
                }
            });

            // Scroll animation for sections
            $(window).scroll(function() {
                $('.section').each(function() {
                    const top = $(this).offset().top;
                    const windowBottom = $(window).scrollTop() + $(window).height();
                    if (top < windowBottom - 50) {
                        $(this).addClass('visible');
                    }
                });
            });

            $(window).scroll();

            // Hover animation for about cards and why-choose-us items
            $('.about-card, .why-choose-us li').hover(
                function() {
                    $(this).css({
                        'transform': 'translateY(-5px)',
                        'box-shadow': '0 8px 24px rgba(37, 99, 235, 0.3)'
                    });
                },
                function() {
                    $(this).css({
                        'transform': 'translateY(0)',
                        'box-shadow': '0 4px 12px rgba(0, 0, 0, 0.1)'
                    });
                }
            );
        });
    </script>
</body>
</html>