<?php
// No PHP logic needed for footer; purely for presentation
?>

<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>ELEX</h3>
            <p>Your one-stop shop for buying, selling, and exchanging mobile phones.</p>
        </div>
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="list_phone.php">List Phone</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <li><a href="admin.php">Admin Panel</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Contact Us</h4>
            <p>Email: <a href="mailto:support@ELEX.com">support@ELEX.com</a></p>
            <p>Phone: +977 9852275234</p>
        </div>
        <div class="footer-section">
            <h4>Stay Connected</h4>
            <div class="social-icons">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            </div>
            <h4>Newsletter</h4>
            <form class="newsletter-form">
                <input type="email" placeholder="Enter your email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> ELEX. All rights reserved.</p>
    </div>
</footer>