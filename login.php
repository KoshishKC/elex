<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            header("Location: $redirect");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mobile Market</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <li><a href="register.php" class="nav-link">Register</a></li>
                <li><a href="login.php" class="nav-link active">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="auth-container">
            <div class="auth-card">
                <h2>Login</h2>
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form method="POST" class="auth-form">
                    <label>
                        Username:
                        <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </label>
                    <label>
                        Password:
                        <input type="password" name="password" required>
                    </label>
                    <button type="submit">Login</button>
                    <p class="auth-link">Forgot your password? <a href="#">Reset Password</a></p>
                    <p class="auth-link">Don't have an account? <a href="register.php">Register here</a></p>
                </form>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
    <script>
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
</body>
</html>