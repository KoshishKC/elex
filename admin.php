<?php
session_start();
require 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_phone']) && isset($_POST['phone_id'])) {
        $phone_id = $_POST['phone_id'];
        $stmt = $pdo->prepare("UPDATE phones SET status = 'approved' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$phone_id]);
    }
    if (isset($_POST['reject_phone']) && isset($_POST['phone_id'])) {
        $phone_id = $_POST['phone_id'];
        $stmt = $pdo->prepare("UPDATE phones SET status = 'rejected' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$phone_id]);
    }
    if (isset($_POST['approve_exchange']) && isset($_POST['exchange_id'])) {
        $exchange_id = $_POST['exchange_id'];
        $stmt = $pdo->prepare("UPDATE exchange_requests SET status = 'accepted' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$exchange_id]);
    }
    if (isset($_POST['reject_exchange']) && isset($_POST['exchange_id'])) {
        $exchange_id = $_POST['exchange_id'];
        $stmt = $pdo->prepare("UPDATE exchange_requests SET status = 'rejected' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$exchange_id]);
    }
    header("Location: admin.php");
    exit();
}

// Fetch pending phones
$pending_phones_query = "SELECT p.*, u.username FROM phones p JOIN users u ON p.user_id = u.id WHERE p.status = 'pending' ORDER BY p.created_at DESC";
$pending_phones = $pdo->query($pending_phones_query)->fetchAll();

// Fetch pending exchange requests
$pending_exchanges_query = "SELECT er.*, p.brand, p.model, u.username AS requester FROM exchange_requests er JOIN phones p ON er.phone_id = p.id JOIN users u ON er.user_id = u.id WHERE er.status = 'pending' ORDER BY er.created_at DESC";
$pending_exchanges = $pdo->query($pending_exchanges_query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ELEX</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo img {
            width: 50px;
            height: 50px;
        }
        .logo h1 {
            font-size: 1.5rem;
            color: #1f2937;
            margin: 0;
        }
        .nav-links {
            display: flex;
            gap: 20px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-links li a {
            color: #1f2937;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease;
        }
        .nav-links li a:hover, .nav-links li a.active {
            color: #1e40af;
        }
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }
        .hamburger span {
            width: 25px;
            height: 3px;
            background-color: #1f2937;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                right: 20px;
                background-color: #ffffff;
                flex-direction: column;
                padding: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                z-index: 10;
            }
            .nav-links.active {
                display: flex;
            }
            .hamburger {
                display: flex;
            }
        }
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .section {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .section h2 {
            color: #1e40af;
            margin-bottom: 20px;
        }
        .item-list {
            display: grid;
            gap: 15px;
        }
        .item-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }
        .item-card .details {
            flex-grow: 1;
        }
        .item-card h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #1f2937;
        }
        .item-card p {
            margin: 5px 0;
            color: #4b5563;
        }
        .item-card .price {
            color: #10b981;
        }
        .item-card .actions {
            display: flex;
            gap: 10px;
        }
        .item-card .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .item-card .approve-btn {
            background-color: #10b981;
            color: #ffffff;
        }
        .item-card .approve-btn:hover {
            background-color: #059669;
        }
        .item-card .reject-btn {
            background-color: #dc2626;
            color: #ffffff;
        }
        .item-card .reject-btn:hover {
            background-color: #b91c1c;
        }
    </style>
    <script>
        $(document).ready(function() {
            // Navbar hamburger toggle
            $('.hamburger').click(function() {
                $(this).toggleClass('active');
                $('.nav-links').toggleClass('active');
            });

            $('.nav-link').click(function() {
                if ($(window).width() <= 768) {
                    $('.nav-links').removeClass('active');
                    $('.hamburger').removeClass('active');
                }
            });
        });
    </script>
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
                <?php endif; ?>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <li><a href="admin.php" class="nav-link active">Admin Panel</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <div class="admin-container">
            <section class="section">
                <h2>Pending Phone Listings</h2>
                <div class="item-list">
                    <?php if (empty($pending_phones)): ?>
                        <p>No pending phone listings.</p>
                    <?php else: ?>
                        <?php foreach ($pending_phones as $phone): ?>
                            <div class="item-card">
                                <div class="details">
                                    <h3><?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?></h3>
                                    <p class="price">₨<?php echo number_format($phone['price'], 2); ?></p>
                                    <p>Listed by: <?php echo htmlspecialchars($phone['username']); ?></p>
                                    <p>Created: <?php echo htmlspecialchars($phone['created_at']); ?></p>
                                </div>
                                <div class="actions">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="phone_id" value="<?php echo $phone['id']; ?>">
                                        <button type="submit" name="approve_phone" class="btn approve-btn">Approve</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="phone_id" value="<?php echo $phone['id']; ?>">
                                        <button type="submit" name="reject_phone" class="btn reject-btn">Reject</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
            <section class="section">
                <h2>Pending Exchange Requests</h2>
                <div class="item-list">
                    <?php if (empty($pending_exchanges)): ?>
                        <p>No pending exchange requests.</p>
                    <?php else: ?>
                        <?php foreach ($pending_exchanges as $exchange): ?>
                            <div class="item-card">
                                <div class="details">
                                    <h3>Exchange for <?php echo htmlspecialchars($exchange['brand'] . ' ' . $exchange['model']); ?></h3>
                                    <p>Offered: <?php echo htmlspecialchars($exchange['offered_phone']); ?></p>
                                    <p>Requester: <?php echo htmlspecialchars($exchange['requester']); ?></p>
                                    <p>Created: <?php echo htmlspecialchars($exchange['created_at']); ?></p>
                                </div>
                                <div class="actions">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="exchange_id" value="<?php echo $exchange['id']; ?>">
                                        <button type="submit" name="approve_exchange" class="btn approve-btn">Approve</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="exchange_id" value="<?php echo $exchange['id']; ?>">
                                        <button type="submit" name="reject_exchange" class="btn reject-btn">Reject</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>