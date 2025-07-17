<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate device listing processing
    // In a real application, you would save the data to a database here
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Your Phone - ELEX</title>
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
            const form = document.querySelector('.listing-form form');
            const popup = document.getElementById('success-popup');
            const closePopup = document.getElementById('close-popup');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                let isValid = true;
                const model = form.querySelector('input[name="model"]').value.trim();
                const price = form.querySelector('input[name="price"]').value.trim();
                const condition = form.querySelector('select[name="condition"]').value;
                const description = form.querySelector('textarea[name="description"]').value.trim();
                const modelError = document.getElementById('model-error');
                const priceError = document.getElementById('price-error');
                const conditionError = document.getElementById('condition-error');
                const descriptionError = document.getElementById('description-error');

                if (!model) {
                    modelError.textContent = 'Phone model is required.';
                    modelError.style.display = 'block';
                    isValid = false;
                } else {
                    modelError.style.display = 'none';
                }

                if (!price || isNaN(price) || price <= 0) {
                    priceError.textContent = 'Valid positive price is required.';
                    priceError.style.display = 'block';
                    isValid = false;
                } else {
                    priceError.style.display = 'none';
                }

                if (!condition) {
                    conditionError.textContent = 'Please select a condition.';
                    conditionError.style.display = 'block';
                    isValid = false;
                } else {
                    conditionError.style.display = 'none';
                }

                if (!description || description.length < 10) {
                    descriptionError.textContent = 'Description must be at least 10 characters.';
                    descriptionError.style.display = 'block';
                    isValid = false;
                } else {
                    descriptionError.style.display = 'none';
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
                            // Show popup
                            popup.style.display = 'flex';
                            // Auto-redirect after 2 seconds
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

            // Close popup on click
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
                <li><a href="browse.php" class="nav-link">Browse</a></li>
                <li><a href="list_phone.php" class="nav-link active">List Phone</a></li>
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
        <section class="listing-container">
            <div class="listing-form">
                <h2>List Your Phone</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="model">Phone Model</label>
                        <input type="text" name="model" id="model" placeholder="e.g., iPhone 12" required>
                        <span class="error" id="model-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (NRS)</label>
                        <input type="number" name="price" id="price" placeholder="e.g., 50000" required>
                        <span class="error" id="price-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="condition">Condition</label>
                        <select name="condition" id="condition" required>
                            <option value="">Select Condition</option>
                            <option value="new">New</option>
                            <option value="used">Used</option>
                            <option value="refurbished">Refurbished</option>
                        </select>
                        <span class="error" id="condition-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" placeholder="Describe your phone..." required></textarea>
                        <span class="error" id="description-error"></span>
                    </div>
                    <button type="submit" class="submit-btn">List Phone</button>
                </form>
            </div>
            <div id="success-popup" class="popup" style="display: none;">
                <div class="popup-content">
                    <span id="close-popup" class="close-popup">&times;</span>
                    <h3>Device Listed Successfully</h3>
                    <p>Your phone has been listed on ELEX. Redirecting to home page...</p>
                </div>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>