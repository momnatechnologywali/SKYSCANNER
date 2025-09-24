<?php
// search.php
// Search results with filters and sorting
session_start();
include 'db.php';
 
if (!isset($_GET['type'])) {
    header('Location: index.php');
    exit;
}
 
$type = $_GET['type'];
$results = [];
$params = [];
 
if ($type === 'flight') {
    $query = "SELECT * FROM flights WHERE 1=1";
    if (isset($_GET['departure'])) {
        $query .= " AND departure_city LIKE ?";
        $params[] = '%' . $_GET['departure'] . '%';
    }
    if (isset($_GET['arrival'])) {
        $query .= " AND arrival_city LIKE ?";
        $params[] = '%' . $_GET['arrival'] . '%';
    }
    if (isset($_GET['departure_date'])) {
        $query .= " AND departure_date = ?";
        $params[] = $_GET['departure_date'];
    }
    if (isset($_GET['arrival_date'])) {
        $query .= " AND arrival_date = ?";
        $params[] = $_GET['arrival_date'];
    }
} else {
    $query = "SELECT * FROM hotels WHERE 1=1";
    if (isset($_GET['city'])) {
        $query .= " AND city LIKE ?";
        $params[] = '%' . $_GET['city'] . '%';
    }
    if (isset($_GET['checkin'])) {
        $query .= " AND checkin_date = ?";
        $params[] = $_GET['checkin'];
    }
    if (isset($_GET['checkout'])) {
        $query .= " AND checkout_date = ?";
        $params[] = $_GET['checkout'];
    }
}
 
// Sorting
$sort = $_GET['sort'] ?? 'price_asc';
switch ($sort) {
    case 'price_asc': $query .= " ORDER BY price ASC"; break;
    case 'price_desc': $query .= " ORDER BY price DESC"; break;
    case 'duration_asc': $query .= " ORDER BY duration ASC"; break;
    case 'rating_desc': $query .= " ORDER BY rating DESC"; break;
    default: $query .= " ORDER BY price ASC";
}
 
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll();
 
// Save search if logged in
if (isset($_SESSION['user_id'])) {
    $search_data = json_encode($_GET);
    $stmt = $pdo->prepare("INSERT INTO saved_searches (user_id, search_type, search_data) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $type, $search_data]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - SkyCompare</title>
    <style>
        /* Internal CSS - Results page with filters */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: white; padding: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; }
        .logo { font-weight: bold; color: #ff6b6b; }
        .back-btn { background: #ff6b6b; color: white; padding: 0.5rem 1rem; border-radius: 5px; text-decoration: none; }
        .filters { background: white; padding: 1rem; margin: 1rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .filter-group { display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .filter-group select, .filter-group input { padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; }
        .results { max-width: 1200px; margin: 0 auto; padding: 1rem; }
        .result-card { background: white; margin-bottom: 1rem; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .result-info h3 { margin-bottom: 0.5rem; }
        .price { font-size: 1.5rem; font-weight: bold; color: #ff6b6b; }
        .book-btn { background: #ff6b6b; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; transition: transform 0.3s; }
        .book-btn:hover { transform: scale(1.05); }
        .no-results { text-align: center; padding: 2rem; color: #666; }
        @media (max-width: 768px) { .result-card { flex-direction: column; gap: 1rem; text-align: center; } }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">SkyCompare</div>
            <a href="index.php" class="back-btn">Back to Home</a>
        </nav>
    </header>
 
    <div class="filters">
        <form method="GET">
            <input type="hidden" name="type" value="<?= $type ?>">
            <?php foreach ($_GET as $key => $value): if ($key !== 'sort'): ?>
                <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endif; endforeach; ?>
            <div class="filter-group">
                <!-- Price range filter (simplified) -->
                <input type="number" name="min_price" placeholder="Min Price" value="<?= $_GET['min_price'] ?? '' ?>">
                <input type="number" name="max_price" placeholder="Max Price" value="<?= $_GET['max_price'] ?? '' ?>">
                <!-- Other filters like airlines, stops, etc., can be added similarly -->
                <select name="sort">
                    <option value="price_asc" <?= ($sort === 'price_asc') ? 'selected' : '' ?>>Cheapest</option>
                    <option value="duration_asc" <?= ($sort === 'duration_asc') ? 'selected' : '' ?>>Fastest</option>
                    <option value="rating_desc" <?= ($sort === 'rating_desc') ? 'selected' : '' ?>>Best Rated</option>
                </select>
                <button type="submit">Filter</button>
            </div>
        </form>
    </div>
 
    <div class="results">
        <?php if (empty($results)): ?>
            <div class="no-results">No results found. Try broadening your search.</div>
        <?php else: ?>
            <?php foreach ($results as $item): ?>
                <div class="result-card">
                    <div class="result-info">
                        <h3><?= htmlspecialchars($type === 'flight' ? $item['airline'] . ' - ' . $item['departure_city'] . ' to ' . $item['arrival_city'] : $item['hotel_name'] . ' in ' . $item['city']) ?></h3>
                        <?php if ($type === 'flight'): ?>
                            <p>Date: <?= $item['departure_date'] ?> | Duration: <?= $item['duration'] ?> min | Stops: <?= $item['stops'] ?></p>
                        <?php else: ?>
                            <p>Dates: <?= $item['checkin_date'] ?> - <?= $item['checkout_date'] ?> | Rating: <?= $item['rating'] ?> ⭐</p>
                        <?php endif; ?>
                        <p class="price">$<?= $item['price'] ?> - <?= $item['provider'] ?></p>
                    </div>
                    <button class="book-btn" onclick="redirectToBooking('<?= $item['booking_url'] ?>')">Book Now</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
 
    <script>
        // Internal JS for redirection
        function redirectToBooking(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
