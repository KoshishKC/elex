<?php
session_start();
require 'config.php';

// Pagination settings
$phones_per_page = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $phones_per_page;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : '';

// Bogus data for exchange phones
$brands = ['Apple', 'Samsung', 'Xiaomi', 'Vivo', 'Oppo', 'Realme', 'OnePlus', 'Motorola', 'Google', 'Honor', 'Huawei', 'Sony', 'Nokia', 'Transsion', 'LG', 'Asus', 'Lenovo', 'ZTE', 'TCL', 'Nothing', 'Poco', 'iQOO', 'Redmi', 'Vertu'];
$models = ['Pro', 'Plus', 'Ultra', 'Lite', 'Max', 'Edge', 'Note', 'X', 'S', 'Z', 'Prime', 'Active', 'Flex'];
$conditions = ['scratch in the back', 'faulty speaker', 'cracked screen', 'worn battery', 'scuffed edges', 'sticky buttons', 'faded display', 'damaged camera', 'loose charging port', 'minor dents'];
$image_urls = [
    'images/iphone_14_pro.jpg',
    'images/galaxy_z_fold4.png',
    'images/xiaomi_13_pro.jpg',
    'images/vivo_v29.png',
    'images/oppo_find_x7_ultra.png',
    'images/realme_11_pro_plus.jpg',
    'images/oneplus_12r.png',
    'images/motorola_edge_50_pro.png',
    'images/google_pixel_8_pro.jpg',
    'images/huawei_pura_70_ultra.png',
    'images/sony_xperia.png',
    'images/nokia_xr21.png',
];

function getRandomElement($array) {
    return $array[array_rand($array)];
}

function generateBogusPhones($count) {
    global $brands, $models, $conditions, $image_urls;
    $phones = [];
    for ($i = 0; $i < $count; $i++) {
        $brand = getRandomElement($brands);
        $model = getRandomElement($models);
        $full_model = $brand . ' ' . $model;
        $price = rand(50000, 130000);
        $condition = getRandomElement($conditions);
        $description = "Used phone with $condition. " . ($i % 2 == 0 ? 'Slight wear on case.' : 'Tested working with minor issues.');
        $image_url = $image_urls[array_rand($image_urls)];
        $phones[] = [
            'id' => $i + 1,
            'user_id' => 1,
            'brand' => $brand,
            'model' => $full_model,
            'price' => $price,
            'description' => $description,
            'is_exchange' => 1,
            'status' => 'approved',
            'image_url' => $image_url,
            'username' => 'TestUser' . rand(1, 100),
            'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
        ];
    }
    return $phones;
}

// Generate 50 bogus phones for exchange
$bogus_phones = generateBogusPhones(50);

// Merge with database query if needed, but for now use bogus data
$phones = $bogus_phones;

// Filter phones based on search and filters
if ($search || $brand || $min_price !== '' || $max_price !== '') {
    $phones = array_filter($phones, function($phone) use ($search, $brand, $min_price, $max_price) {
        $match = true;
        if ($search) {
            $search = strtolower($search);
            $match = stripos(strtolower($phone['brand'] . ' ' . $phone['model'] . ' ' . $phone['description']), $search) !== false;
        }
        if ($brand && $match) $match = strtolower($phone['brand']) === strtolower($brand);
        if ($min_price !== '' && $match) $match = $phone['price'] >= $min_price;
        if ($max_price !== '' && $match) $match = $phone['price'] <= $max_price;
        return $match && $phone['is_exchange'] == 1 && $phone['status'] == 'approved';
    });
}

// Pagination
$total_phones = count($phones);
$total_pages = ceil($total_phones / $phones_per_page);
$phones = array_slice($phones, $offset, $phones_per_page);

// Get unique brands for filter
$brands = array_unique(array_column($bogus_phones, 'brand'));
sort($brands);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Phones - ELEX</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.0/vanilla-tilt.min.js"></script>
    <script>
        $(document).ready(function() {
            // Navbar hamburger toggle
            $('.hamburger').click(function() {
                $(this).toggleClass('active');
                $('.nav-links').toggleClass('active').slideToggle(300);
            });

            $('.nav-link').click(function() {
                if ($(window).width() <= 768) {
                    $('.nav-links').removeClass('active').slideUp(300);
                    $('.hamburger').removeClass('active');
                }
            });

            // Scroll animations
            $(window).scroll(function() {
                $('.section').each(function() {
                    if ($(this).offset().top < $(window).scrollTop() + $(window).height() * 0.75) {
                        $(this).addClass('visible');
                    }
                });
            });
            $('.section').first().addClass('visible');

            // Initialize Vanilla Tilt for phone cards
            VanillaTilt.init(document.querySelectorAll(".phone-card"), {
                max: 15,
                speed: 400,
                glare: true,
                "max-glare": 0.2
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
        <section class="browse-container section">
            <div class="search-filter">
                <form method="GET" action="browse.php" class="filter-form">
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Search by brand, model, or description" value="<?php echo htmlspecialchars($search); ?>" class="form-input">
                    </div>
                    <div class="form-group">
                        <select name="brand" class="form-select">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?php echo htmlspecialchars($b); ?>" <?php echo $brand === $b ? 'selected' : ''; ?>><?php echo htmlspecialchars($b); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group price-group">
                        <input type="number" name="min_price" placeholder="Min Price" value="<?php echo htmlspecialchars($min_price); ?>" step="0.01" class="form-input price-input">
                        <input type="number" name="max_price" placeholder="Max Price" value="<?php echo htmlspecialchars($max_price); ?>" step="0.01" class="form-input price-input">
                    </div>
                    <button type="submit" class="filter-btn"><i class="fas fa-filter"></i> Filter</button>
                </form>
            </div>
            <div class="phone-list">
                <?php if (empty($phones)): ?>
                    <p class="no-results">No phones found matching your criteria.</p>
                <?php else: ?>
                    <?php foreach ($phones as $phone): ?>
                        <div class="phone-card" data-tilt>
                            <img src="<?php echo htmlspecialchars($phone['image_url']); ?>" alt="<?php echo htmlspecialchars($phone['model']); ?>">
                            <h3><?php echo htmlspecialchars($phone['model']); ?></h3>
                            <p class="price">NRS <?php echo number_format($phone['price'], 2); ?></p>
                            <p><?php echo htmlspecialchars($phone['description']); ?></p>
                            <p class="listed-by">Listed by: <?php echo htmlspecialchars($phone['username']); ?> on <?php echo htmlspecialchars($phone['created_at']); ?></p>
                            <div class="actions">
                                <a href="esewa_payment.php?id=<?php echo $phone['id']; ?>&model=<?php echo urlencode($phone['model']); ?>&price=<?php echo $phone['price']; ?>" class="btn buy-btn">Buy</a>
                                <a href="exchange_offer.php?id=<?php echo $phone['id']; ?>" class="btn exchange-btn">Exchange</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&brand=<?php echo urlencode($brand); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>" class="<?php echo $page === $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>