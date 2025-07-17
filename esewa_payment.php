<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get phone details from query parameters
$phone_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$phone_model = isset($_GET['model']) ? urldecode($_GET['model']) : 'Unknown Model';
$phone_price = isset($_GET['price']) && is_numeric($_GET['price']) ? (float)$_GET['price'] : 0;

// For demo purposes, assume payment is processed without actual eSewa integration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate payment processing
    // In a real application, you would integrate with eSewa's API here
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa Payment - ELEX</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar hamburger toggle
            const hamburger = document.querySelector('.hamburger');
            const navLinks = document.querySelector('.nav-links');
            const links = document.querySelectorAll('.nav-link');

            hamburger.addEventListener('click', function() {
                this.classList.toggle('active');
                navLinks.classList.toggle('active');
                navLinks.style.display = navLinks.classList.contains('active') ? 'flex' : 'none';
            });

            links.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        navLinks.classList.remove('active');
                        navLinks.style.display = 'none';
                        hamburger.classList.remove('active');
                    }
                });
            });

            // Form validation and submission
            const form = document.querySelector('.payment-form form');
            const popup = document.getElementById('success-popup');
            const closePopup = document.getElementById('close-popup');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                let isValid = true;
                const mobile = form.querySelector('input[name="mobile"]').value.trim();
                const pin = form.querySelector('input[name="pin"]').value.trim();
                const mobileError = document.getElementById('mobile-error');
                const pinError = document.getElementById('pin-error');

                if (!mobile || !/^\d{10}$/.test(mobile)) {
                    mobileError.textContent = 'Valid 10-digit mobile number is required.';
                    mobileError.style.display = 'block';
                    isValid = false;
                } else {
                    mobileError.style.display = 'none';
                }

                if (!pin || pin.length < 4) {
                    pinError.textContent = 'PIN must be at least 4 characters.';
                    pinError.style.display = 'block';
                    isValid = false;
                } else {
                    pinError.style.display = 'none';
                }

                if (isValid) {
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            popup.style.display = 'flex';
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 2000);
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    } catch (error) {
                        alert('An error occurred. Please try again.');
                    }
                }
            });

            if (closePopup) {
                closePopup.addEventListener('click', function() {
                    popup.style.display = 'none';
                    window.location.href = 'index.php';
                });
            }
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
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="about.php" class="nav-link">About</a></li>
                <li><a href="browse.php" class="nav-link active">Browse</a></li>
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
        <section class="payment-container">
            <div class="payment-form">
                <h2>eSewa Payment</h2>
                <div class="payment-details">
                    <p><strong>Paying for:</strong> <?php echo htmlspecialchars($phone_model); ?></p>
                    <p><strong>Amount:</strong> NRS <?php echo number_format($phone_price, 2); ?></p>
                </div>
                <form method="POST">
                    <div class="form-group">
                        <label for="mobile">eSewa Mobile Number</label>
                        <input type="text" name="mobile" id="mobile" placeholder="e.g., 98xxxxxxxx" required>
                        <span class="error" id="mobile-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="pin">eSewa PIN</label>
                        <input type="password" name="pin" id="pin" placeholder="Enter your PIN" required>
                        <span class="error" id="pin-error"></span>
                    </div>
                    <button type="submit" class="submit-btn">Confirm Payment</button>
                </form>
            </div>
            <div id="success-popup" class="popup" style="display: none;">
                <div class="popup-content">
                    <span id="close-popup" class="close-popup">×</span>
                    <h3>Payment Successful</h3>
                    <p>Your payment for <?php echo htmlspecialchars($phone_model); ?> has been processed. Redirecting to home page...</p>
                </div>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>