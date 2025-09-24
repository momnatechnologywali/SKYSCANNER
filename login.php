<?php
// login.php
// User login page
session_start();
include 'db.php';
 
if ($_POST) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
 
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
 
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SkyCompare</title>
    <style>
        /* Same as signup CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-container { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 2rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 1.5rem; color: #333; }
        .form-group { margin-bottom: 1rem; }
        input { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; }
        button { width: 100%; background: #ff6b6b; color: white; border: none; padding: 1rem; border-radius: 10px; cursor: pointer; transition: transform 0.3s; }
        button:hover { transform: scale(1.02); }
        .error { color: #ff6b6b; text-align: center; margin-bottom: 1rem; }
        .signup-link { text-align: center; margin-top: 1rem; }
        .signup-link a { color: #ff6b6b; text-decoration: none; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="signup-link">
            <a href="signup.php">Don't have an account? Signup</a>
        </div>
    </div>
</body>
</html>
