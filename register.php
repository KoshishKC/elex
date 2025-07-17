<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $terms = isset($_POST['terms']);

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!$terms) {
        $error = "You must agree to the terms and conditions.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Username or email already exists.";
            } else {
                $password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $password]);
                header("Location: login.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Mobile Market</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function validateForm() {
            const password = document.querySelector('input[name="password"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const terms = document.querySelector('input[name="terms"]').checked;
            const errorDiv = document.querySelector('.error');

            if (password.length < 6) {
                errorDiv.textContent = "Password must be at least 6 characters.";
                return false;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errorDiv.textContent = "Invalid email format.";
                return false;
            }
            if (!terms) {
                errorDiv.textContent = "You must agree to the terms and conditions.";
                return false;
            }
            return true;
        }

        $(document).ready(function() {
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
        });
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="https://via.placeholder.com/50?text=MM" alt="Mobile Market Logo">
            <h1>Mobile Market</h1>
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
                <li><a href="register.php" class="nav-link active">Register</a></li>
                <li><a href="login.php" class="nav-link">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="auth-container">
            <div class="auth-card">
                <h2>Register</h2>
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form method="POST" class="auth-form" onsubmit="return validateForm()">
                    <label>
                        Username:
                        <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </label>
                    <label>
                        Email:
                        <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </label>
                    <label>
                        Password:
                        <input type="password" name="password" required>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="terms"> I agree to the <a href="#">Terms and Conditions</a>
                    </label>
                    <button type="submit">Register</button>
                    <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
                </form>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>