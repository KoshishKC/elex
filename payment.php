<?php
session_start();
require 'config.php'; // PDO connection

$phone_id = isset($_GET['phone_id']) ? intval($_GET['phone_id']) : null;

// Validate phone_id and fetch phone details
$phone = null;
if ($phone_id) {
    $stmt = $pdo->prepare("SELECT brand, model, price FROM phones WHERE id = ? AND status = 'approved'");
    $stmt->execute([$phone_id]);
    $phone = $stmt->fetch();
    if (!$phone) {
        die('Invalid phone ID.');
    }
} else {
    die('Phone ID is required.');
}

// Handle form submission for fake payment
$payment_success = false;
$esewa_id = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $esewa_id = trim($_POST['esewa_id']);
    if (!empty($esewa_id)) {
        $payment_success = true;
        // Optional: Log fake transaction to database
        /*
        $stmt = $pdo->prepare("INSERT INTO payments (phone_id, user_id, esewa_id, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$phone_id, $_SESSION['user_id'] ?? null, $esewa_id]);
        */
    } else {
        $error = 'Please enter an eSewa Transaction ID.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - ELEX</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .payment-box {
            max-width: 400px;
            margin: 60px auto;
            padding: 30px;
            background: #f3f4f6;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .payment-box input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .payment-box button {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
        }
        .payment-box button:hover {
            background-color: #0f766e;
        }
        .success-message {
            color: #10b981;
            font-size: 18px;
            margin-top: 20px;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #10b981;
        }
        .error {
            color: #dc2626;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <header>
        <h1 style="text-align: center; margin-top: 40px;">ELEX Payment</h1>
    </header>
    <main>
        <div class="payment-box">
            <?php if ($payment_success): ?>
                <h2>Payment Successful</h2>
                <p class="success-message">Thank you! Your payment for <strong><?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?></strong> (ID: <strong><?php echo htmlspecialchars($esewa_id); ?></strong>) has been recorded.</p>
                <a href="browse.php" class="back-btn">← Back to Browse</a>
            <?php else: ?>
                <h2>Pay for <?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?></h2>
                <p>Price: $<?php echo number_format($phone['price'], 2); ?></p>
                <p>Enter your eSewa Transaction ID to confirm payment.</p>
                <?php if ($error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="text" name="esewa_id" placeholder="eSewa Transaction ID" required>
                    <button type="submit">Pay Now</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>