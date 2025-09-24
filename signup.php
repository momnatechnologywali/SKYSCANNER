<?php
// signup.php
// User signup page
session_start();
include 'db.php';
 
if ($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
 
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $error = "Signup failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - SkyCompare</title>
    <style>
        /* Internal CSS - Consistent with index */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-container { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 2rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 1.5rem; color: #333; }
        .form-group { margin-bottom: 1rem; }
        input { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; }
        button { width: 100%; background: #ff6b6b; color: white; border: none; padding: 1rem; border-radius: 10px; cursor: pointer; transition: transform 0.3s; }
        button:hover { transform: scale(1.02); }
        .error { color: #ff6b6b; text-align: center; margin-bottom: 1rem; }
        .login-link { text-align: center; margin-top: 1rem; }
        .login-link a { color: #ff6b6b; text-decoration: none; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Signup</h2>
        <?php if (isset($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Signup</button>
        </form>
        <div class="login-link">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>
