<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch cart items
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT d.device_id, d.device_name, d.brand FROM cart_items c JOIN devices d ON c.device_id = d.device_id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - ELEX</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }
        .content {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #1e40af;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .cart-item {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <main class="content">
        <h2>Your Cart</h2>
        <?php if (empty($cart_items)): ?>
            <p>Cart is empty.</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <p><?php echo htmlspecialchars($item['device_name'] . ' (' . $item['brand'] . ')'); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>