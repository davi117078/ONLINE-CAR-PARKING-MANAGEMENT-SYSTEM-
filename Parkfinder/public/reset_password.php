<?php
require_once __DIR__ . '/../src/db.php';

// Ensure token is present
$token = $_GET['token'] ?? '';
if (!$token) {
  die("Invalid or missing reset token.");
}

// Fetch user by token
$stmt = $pdo->prepare("SELECT id, email, reset_token_expires FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify token validity
if (!$user || strtotime($user['reset_token_expires']) < time()) {
  die("This password reset link has expired or is invalid.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = trim($_POST['password']);
  $confirm = trim($_POST['confirm_password']);

  if (strlen($password) < 6) {
    $error = "Password must be at least 6 characters long.";
  } elseif ($password !== $confirm) {
    $error = "Passwords do not match.";
  } else {
    // Hash and update password
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
    $update->execute([$hashed, $user['id']]);

    // Redirect to login with success message
    header("Location: login.php?reset=success");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - ParkFinder</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../public/assets/images/background3.png') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card {
      background: rgba(255, 255, 255, 0.95);
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.25);
      max-width: 420px;
      width: 90%;
      text-align: center;
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      color: #007bff;
      margin-bottom: 10px;
    }

    p {
      color: #666;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .form-control {
      padding: 12px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 1rem;
      transition: border 0.3s;
    }

    .form-control:focus {
      border-color: #007bff;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    .btn-primary {
      background: #007bff;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 10px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      transition: background 0.3s;
    }

    .btn-primary:hover {
      background: #0056b3;
    }

    .error {
      color: #dc3545;
      font-size: 0.9rem;
    }

    .success {
      color: #28a745;
      font-size: 0.9rem;
    }

    footer {
      position: fixed;
      bottom: 10px;
      width: 100%;
      text-align: center;
      color: white;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

  <div class="card">
    <h2><i class="fa-solid fa-lock"></i> Reset Password</h2>
    <p>Enter a new password for your ParkFinder account</p>

    <?php if ($error): ?>
      <div class="error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="password" name="password" class="form-control" required placeholder="New password">
      <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm password">
      <button type="submit" class="btn-primary">Update Password</button>
    </form>
  </div>

  <footer>
    <i class="fa-solid fa-car me-1"></i> © <?= date('Y'); ?> ParkFinder | Smart Parking System
  </footer>

</body>
</html>
