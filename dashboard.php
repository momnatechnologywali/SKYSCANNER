<?php
// dashboard.php
// User dashboard for saved searches and history
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
$saved_searches = $pdo->prepare("SELECT * FROM saved_searches WHERE user_id = ? ORDER BY created_at DESC");
$saved_searches->execute([$user_id]);
$saved = $saved_searches->fetchAll();
 
$history = $pdo->prepare("SELECT bh.*, f.airline, h.hotel_name FROM bookings_history bh 
                          LEFT JOIN flights f ON bh.item_type = 'flight' AND bh.item_id = f.id 
                          LEFT JOIN hotels h ON bh.item_type = 'hotel' AND bh.item_id = h.id 
                          WHERE bh.user_id = ? ORDER BY bh.booking_date DESC");
$history->execute([$user_id]);
$bookings = $history->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SkyCompare</title>
    <style>
        /* Internal CSS - Dashboard style */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: white; padding: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; }
        .logo { font-weight: bold; color: #ff6b6b; }
        .back-btn { background: #ff6b6b; color: white; padding: 0.5rem 1rem; border-radius: 5px; text-decoration: none; }
        .dashboard { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .section { background: white; margin-bottom: 2rem; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 1rem; color: #333; }
        .item { padding: 1rem; border-bottom: 1px solid #eee; }
        .item:last-child { border-bottom: none; }
        .search-data { background: #f8f9fa; padding: 0.5rem; border-radius: 5px; font-size: 0.9rem; }
        @media (max-width: 768px) { .dashboard { padding: 0 0.5rem; } }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">SkyCompare Dashboard</div>
            <a href="index.php" class="back-btn">Home</a>
        </nav>
    </header>
 
    <div class="dashboard">
        <div class="section">
            <h2>Saved Searches</h2>
            <?php if (empty($saved)): ?>
                <p>No saved searches yet.</p>
            <?php else: ?>
                <?php foreach ($saved as $search): ?>
                    <div class="item">
                        <p>Type: <?= ucfirst($search['search_type']) ?> | Date: <?= $search['created_at'] ?></p>
                        <div class="search-data"><?= htmlspecialchars($search['search_data']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
 
        <div class="section">
            <h2>Booking History</h2>
            <?php if (empty($bookings)): ?>
                <p>No bookings yet.</p>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="item">
                        <p>Type: <?= ucfirst($booking['item_type']) ?> | Date: <?= $booking['booking_date'] ?> | Status: <?= $booking['status'] ?></p>
                        <?php if ($booking['item_type'] === 'flight' && $booking['airline']): ?>
                            <p>Flight: <?= htmlspecialchars($booking['airline']) ?></p>
                        <?php elseif ($booking['item_type'] === 'hotel' && $booking['hotel_name']): ?>
                            <p>Hotel: <?= htmlspecialchars($booking['hotel_name']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
