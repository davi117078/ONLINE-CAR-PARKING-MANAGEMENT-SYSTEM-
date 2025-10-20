<?php
require_once __DIR__ . '/../src/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);

  // Check if email exists
  $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    $error = "No account found with that email address.";
  } else {
    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store token and expiry in DB
    $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
    $update->execute([$token, $expires, $user['id']]);

    // Build reset link
    $resetLink = sprintf(
      "%s://%s%s/reset_password.php?token=%s",
      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
      $_SERVER['HTTP_HOST'],
      dirname($_SERVER['PHP_SELF']),
      $token
    );

    // === Email Setup ===
    $subject = "Password Reset - ParkFinder";
    $message = "
      <h2>Hi " . htmlspecialchars($user['name']) . ",</h2>
      <p>We received a request to reset your ParkFinder password.</p>
      <p>Click the link below to reset your password (valid for 1 hour):</p>
      <p><a href='$resetLink' style='background:#007bff;color:#fff;padding:10px 15px;border-radius:8px;text-decoration:none;'>Reset Password</a></p>
      <br>
      <p>If you didn’t request this, please ignore this email.</p>
      <p>– ParkFinder Team</p>
    ";

    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: ParkFinder <no-reply@parkfinder.com>" . "\r\n";

    // Send email (ensure mail() is configured on your server)
    if (mail($email, $subject, $message, $headers)) {
      $success = "A password reset link has been sent to your email.";
    } else {
      $error = "Failed to send reset email. Please try again later.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - ParkFinder</title>
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
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
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

    .form-control {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 1rem;
      margin-bottom: 1rem;
      transition: border-color 0.3s;
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

    a {
      color: #007bff;
      text-decoration: none;
      font-size: 0.9rem;
    }

    a:hover {
      text-decoration: underline;
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
    <h2><i class="fa-solid fa-envelope"></i> Forgot Password</h2>
    <p>Enter your email address to reset your password</p>

    <?php if ($error): ?>
      <div class="error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="email" name="email" class="form-control" required placeholder="Enter your registered email">
      <button type="submit" class="btn-primary">Send Reset Link</button>
    </form>

    <p><a href="login.php"><i class="fa-solid fa-arrow-left"></i> Back to Login</a></p>
  </div>

  <footer>
    <i class="fa-solid fa-car me-1"></i> © <?= date('Y'); ?> ParkFinder | Smart Parking System
  </footer>

</body>
</html>
