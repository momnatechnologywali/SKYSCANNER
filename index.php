<?php
// index.php
// Homepage with search options and auth links
session_start();
include 'db.php';
 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyCompare - Flight & Hotel Search</title>
    <style>
        /* Internal CSS - Professional, modern, responsive design inspired by Skyscanner */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; color: #333; }
        header { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        nav { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff6b6b; }
        .nav-links { display: flex; list-style: none; gap: 2rem; }
        .nav-links a { text-decoration: none; color: #333; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #ff6b6b; }
        .auth-btn { background: #ff6b6b; color: white; padding: 0.5rem 1rem; border-radius: 20px; text-decoration: none; transition: transform 0.3s; }
        .auth-btn:hover { transform: scale(1.05); }
        .main-hero { text-align: center; padding: 4rem 2rem; color: white; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .hero p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
        .search-container { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; padding: 2rem; max-width: 800px; margin: 0 auto 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .search-tabs { display: flex; justify-content: center; margin-bottom: 1.5rem; }
        .tab { padding: 0.8rem 1.5rem; background: #f8f9fa; border: none; border-radius: 25px 25px 0 0; cursor: pointer; transition: all 0.3s; margin: 0 0.5rem; font-weight: 500; }
        .tab.active { background: #ff6b6b; color: white; }
        .search-form { display: none; }
        .search-form.active { display: block; }
        .form-group { display: flex; gap: 1rem; margin-bottom: 1rem; align-items: center; }
        .form-group input, .form-group select { flex: 1; padding: 0.8rem; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; }
        .search-btn { background: linear-gradient(135deg, #ff6b6b, #ff8e8e); color: white; border: none; padding: 1rem 2rem; border-radius: 25px; font-size: 1.1rem; cursor: pointer; transition: transform 0.3s; width: 100%; }
        .search-btn:hover { transform: scale(1.02); }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; max-width: 1200px; margin: 4rem auto; padding: 0 2rem; }
        .feature-card { background: rgba(255,255,255,0.95); padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .feature-card:hover { transform: translateY(-5px); }
        .feature-icon { font-size: 3rem; margin-bottom: 1rem; }
        footer { background: rgba(0,0,0,0.8); color: white; text-align: center; padding: 2rem; }
        @media (max-width: 768px) { .hero h1 { font-size: 2.5rem; } .form-group { flex-direction: column; } .nav-links { gap: 1rem; } }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">SkyCompare</div>
            <ul class="nav-links">
                <li><a href="#flights">Flights</a></li>
                <li><a href="#hotels">Hotels</a></li>
                <?php if (isset($user)): ?>
                    <li>Welcome, <?= htmlspecialchars($user['username']) ?> | <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Signup</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
 
    <section class="main-hero">
        <h1>Find Your Perfect Trip</h1>
        <p>Compare flights and hotels from top providers at unbeatable prices.</p>
        <div class="search-container">
            <div class="search-tabs">
                <button class="tab active" onclick="showTab('flight')">Flights</button>
                <button class="tab" onclick="showTab('hotel')">Hotels</button>
            </div>
            <form class="search-form active" id="flight-form" action="search.php" method="GET">
                <input type="hidden" name="type" value="flight">
                <div class="form-group">
                    <input type="text" name="departure" placeholder="From (e.g., New York)" required>
                    <input type="text" name="arrival" placeholder="To (e.g., Los Angeles)" required>
                </div>
                <div class="form-group">
                    <input type="date" name="departure_date" required>
                    <input type="date" name="arrival_date" required>
                </div>
                <div class="form-group">
                    <select name="passengers">
                        <option value="1">1 Passenger</option>
                        <option value="2">2 Passengers</option>
                    </select>
                    <button type="submit" class="search-btn">Search Flights</button>
                </div>
            </form>
            <form class="search-form" id="hotel-form" action="search.php" method="GET">
                <input type="hidden" name="type" value="hotel">
                <div class="form-group">
                    <input type="text" name="city" placeholder="City (e.g., Paris)" required>
                </div>
                <div class="form-group">
                    <input type="date" name="checkin" required>
                    <input type="date" name="checkout" required>
                </div>
                <div class="form-group">
                    <select name="rooms">
                        <option value="1">1 Room</option>
                        <option value="2">2 Rooms</option>
                    </select>
                    <button type="submit" class="search-btn">Search Hotels</button>
                </div>
            </form>
        </div>
    </section>
 
    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">✈️</div>
            <h3>Cheap Flights</h3>
            <p>Compare prices from 1000+ airlines.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🏨</div>
            <h3>Best Hotels</h3>
            <p>Millions of properties worldwide.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Secure Booking</h3>
            <p>Redirect to trusted providers.</p>
        </div>
    </section>
 
    <footer>
        <p>&copy; 2025 SkyCompare. All rights reserved.</p>
    </footer>
 
    <script>
        // Internal JS for tab switching and redirection
        function showTab(tabType) {
            document.querySelectorAll('.search-form').forEach(form => form.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabType + '-form').classList.add('active');
            event.target.classList.add('active');
        }
 
        // Example JS redirection (used in other pages too)
        function redirectToBooking(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
