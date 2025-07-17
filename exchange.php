<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=exchange.php?phone_id=" . urlencode($_GET['phone_id']));
    exit;
}

if (!isset($_GET['phone_id'])) {
    header("Location: browse.php");
    exit;
}

$phone_id = intval($_GET['phone_id']);
$stmt = $pdo->prepare("SELECT p.*, u.username FROM phones p JOIN users u ON p.user_id = u.id WHERE p.id = ? AND p.status = 'approved' AND p.is_exchange = 1");
$stmt->execute([$phone_id]);
$phone = $stmt->fetch();

if (!$phone) {
    header("Location: browse.php");
    exit;
}

// Get user's listed phones for exchange
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, brand, model FROM phones WHERE user_id = ? AND status = 'approved'");
$stmt->execute([$user_id]);
$user_phones = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offered_phone_id = !empty($_POST['offered_phone']) ? intval($_POST['offered_phone']) : null;
    $custom_offer = trim($_POST['custom_offer'] ?? '');

    if (!$offered_phone_id && empty($custom_offer)) {
        $error = "Please select a phone or provide a custom offer.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO exchange_requests (phone_id, requester_id, offered_phone_id, custom_offer) VALUES (?, ?, ?, ?)");
            $stmt->execute([$phone_id, $user_id, $offered_phone_id, $custom_offer]);
            $success = "Exchange request submitted successfully!";
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
    <title>Exchange Phone - ELEX</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <h2>Request an Exchange</h2>
            <p>Propose an exchange for <?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?>.</p>
        </section>
        <section class="exchange-container">
            <div class="phone-details">
                <h3><?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?></h3>
                <img src="https://via.placeholder.com/300x200?text=<?php echo urlencode($phone['brand'] . '+' . $phone['model']); ?>" alt="<?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?>">
                <p class="price">$<?php echo number_format($phone['price'], 2); ?></p>
                <p><?php echo htmlspecialchars($phone['description'] ?: 'No description available.'); ?></p>
                <p class="listed-by">Listed by: <?php echo htmlspecialchars($phone['username']); ?></p>
            </div>
            <div class="exchange-form-container">
                <h3>Exchange Offer</h3>
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <p class="success"><?php echo htmlspecialchars($success); ?></p>
                <?php endif; ?>
                <form method="POST" class="exchange-form">
                    <label>
                        Select Your Phone (Optional):
                        <select name="offered_phone">
                            <option value="">None</option>
                            <?php foreach ($user_phones as $user_phone): ?>
                                <option value="<?php echo $user_phone['id']; ?>">
                                    <?php echo htmlspecialchars($user_phone['brand'] . ' ' . $user_phone['model']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        Custom Offer (Optional):
                        <textarea name="custom_offer" placeholder="Describe your exchange offer (e.g., phone details, cash offer, etc.)" rows="5"></textarea>
                    </label>
                    <button type="submit" class="exchange-submit">Submit Exchange Request</button>
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

            $('.exchange-form').on('submit', function(e) {
                if (!confirm('Are you sure you want to submit this exchange request?')) {
                    e.preventDefault();
                }
            });

            // Animate form on load
            $('.exchange-form-container').css('opacity', 0).animate({ opacity: 1 }, 600);
        });
    </script>
</body>
</html>