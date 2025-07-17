<?php
session_start();
require 'config.php';

// Get featured phones (newest 3)
$featured_query = "SELECT p.*, u.username FROM phones p JOIN users u ON p.user_id = u.id WHERE p.status = 'approved' ORDER BY p.created_at DESC LIMIT 3";
$featured_phones = $pdo->query($featured_query)->fetchAll();

// Get stats
$total_phones = $pdo->query("SELECT COUNT(*) FROM phones WHERE status = 'approved'")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_exchanges = $pdo->query("SELECT COUNT(*) FROM exchange_requests")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELEX - Your Phone Marketplace</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.0/vanilla-tilt.min.js"></script>
    <script>
        $(document).ready(function() {
            // Navbar hamburger toggle
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

            // Scroll animations
            $(window).scroll(function() {
                $('.section').each(function() {
                    const top = $(this).offset().top;
                    const windowBottom = $(window).scrollTop() + $(window).height();
                    if (top < windowBottom - 50) {
                        $(this).addClass('visible');
                    }
                });
            });

            // Trigger scroll animations on load
            $(window).scroll();

            // Carousel for reviews
            let currentSlide = 0;
            const slides = $('.review-carousel .review-card');
            const totalSlides = slides.length;

            function showSlide(index) {
                slides.hide().css('opacity', 0);
                slides.eq(index).show().animate({ opacity: 1 }, 500);
            }

            $('.carousel-prev').click(function() {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                showSlide(currentSlide);
            });

            $('.carousel-next').click(function() {
                currentSlide = (currentSlide + 1) % totalSlides;
                showSlide(currentSlide);
            });

            showSlide(currentSlide);

            // Initialize Vanilla Tilt for cards
            VanillaTilt.init(document.querySelectorAll('.phone-card, .review-card, .benefit-card'), {
                max: 15,
                speed: 400,
                glare: true,
                'max-glare': 0.3
            });

            // Animated counters
            $('.counter').each(function() {
                const $this = $(this);
                const countTo = $this.attr('data-count');
                $({ countNum: $this.text() }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });

            // Newsletter form submission
            $('.newsletter-form').submit(function(e) {
                e.preventDefault();
                const email = $('#newsletter-email').val();
                if (email) {
                    $('.newsletter-result').text('Thank you for subscribing!').css('color', '#10b981');
                    $('#newsletter-email').val('');
                } else {
                    $('.newsletter-result').text('Please enter a valid email.').css('color', '#dc2626');
                }
            });
        });
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <h1>ELEX</h1>
        </div>
        <nav class="navbar">
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link active">Home</a></li>
                <li><a href="about.php" class="nav-link">About</a></li>
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
        <section class="hero">
            <h2>Discover Your Dream Phone</h2>
            <p>Buy, sell, or exchange with ELEX’s trusted platform.</p>
            <div class="hero-actions">
                <a href="browse.php" class="hero-btn">Browse Phones</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="list_phone.php" class="hero-btn secondary">List Your Phone</a>
                <?php else: ?>
                    <a href="register.php" class="hero-btn secondary">Get Started</a>
                <?php endif; ?>
            </div>
        </section>
        <section class="section about-section">
            <h2>About ELEX</h2>
            <p>We’re your go-to platform for buying, selling, and exchanging mobile phones. With verified listings, secure transactions, and a seamless exchange system, we make finding your next phone a breeze.</p>
            <a href="about.php" class="btn">Learn More</a>
        </section>
        <section class="section featured">
            <h2>Featured Phones</h2>
            <div class="phone-list">
                <?php foreach ($featured_phones as $phone): ?>
                    <div class="phone-card">
                        <img src="https://via.placeholder.com/300x200?text=<?php echo urlencode($phone['brand'] . '+' . $phone['model']); ?>" alt="<?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?>">
                        <h3><?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?></h3>
                        <p class="price">$<?php echo number_format($phone['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars($phone['description'] ?: 'No description available.'); ?></p>
                        <p class="listed-by">Listed by: <?php echo htmlspecialchars($phone['username']); ?></p>
                        <div class="actions">
                            <?php if ($phone['is_exchange']): ?>
                                <a href="exchange.php?phone_id=<?php echo $phone['id']; ?>" class="btn exchange-btn">Request Exchange</a>
                            <?php endif; ?>
                            <a href="#" class="btn buy-btn">Buy Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="browse.php" class="btn view-all">View All Phones</a>
        </section>
        <section class="section benefits">
            <h2>Why Choose ELEX?</h2>
            <div class="benefits-list">
                <div class="benefit-card">
                    <i class="fas fa-check-circle"></i>
                    <h4>Verified Listings</h4>
                    <p>All phones are approved by our team for authenticity.</p>
                </div>
                <div class="benefit-card">
                    <i class="fas fa-lock"></i>
                    <h4>Secure Transactions</h4>
                    <p>Shop with confidence through our trusted platform.</p>
                </div>
                <div class="benefit-card">
                    <i class="fas fa-exchange-alt"></i>
                    <h4>Seamless Exchanges</h4>
                    <p>Trade your phone effortlessly with our exchange system.</p>
                </div>
            </div>
        </section>
        <section class="section reviews">
            <h2>What Our Users Say</h2>
            <div class="review-carousel">
                <div class="review-card">
                    <p class="review-text">"Sold my old phone in days! The exchange feature is amazing."</p>
                    <p class="review-author">— Koshish.</p>
                </div>
                <div class="review-card">
                    <p class="review-text">"Found a great deal on my dream phone. Super easy to use!"</p>
                    <p class="review-author">— Michael T.</p>
                </div>
                <div class="review-card">
                    <p class="review-text">"Trusted platform with excellent support. Highly recommend!"</p>
                    <p class="review-author">— Emily R.</p>
                </div>
                <button class="carousel-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="carousel-next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </section>
        <section class="section stats">
            <h2>Our Community</h2>
            <div class="stats-list">
                <div class="stat-card">
                    <h4><span class="counter" data-count="<?php echo $total_phones; ?>">0</span>+</h4>
                    <p>Phones Listed</p>
                </div>
                <div class="stat-card">
                    <h4><span class="counter" data-count="<?php echo $total_users; ?>">0</span>+</h4>
                    <p>Happy Users</p>
                </div>
                <div class="stat-card">
                    <h4><span class="counter" data-count="<?php echo $total_exchanges; ?>">0</span>+</h4>
                    <p>Exchanges Made</p>
                </div>
            </div>
        </section>
        <section class="section newsletter">
            <h2>Stay Updated</h2>
            <p>Join our newsletter for the latest deals and updates.</p>
            <form class="newsletter-form">
                <input type="email" id="newsletter-email" placeholder="Enter your email">
                <button type="submit">Subscribe</button>
            </form>
            <p class="newsletter-result"></p>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>