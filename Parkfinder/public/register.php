<?php
require_once __DIR__ . '/../src/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = 'driver'; // default role

  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);

  if ($stmt->fetch()) {
    $message = "Email is already registered!";
  } else {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $password, $role])) {
      $message = "Registration successful! You can now log in.";
    } else {
      $message = "Something went wrong. Try again.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - ParkFinder</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: url('../public/assets/images/background3.png') no-repeat center center/cover;;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #333;
    }

    .card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 2.5rem;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
      width: 90%;
      max-width: 420px;
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-15px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      text-align: center;
      margin-bottom: 0.5rem;
      color: #6610f2;
      font-weight: 700;
    }

    p.subtitle {
      text-align: center;
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 1.2rem;
    }

    .input-group {
      position: relative;
      margin-bottom: 1rem;
    }

    .input-group i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #6610f2;
      font-size: 1.1rem;
    }

    .form-control {
      width: 100%;
      padding: 12px 12px 12px 38px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 1rem;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus {
      border-color: #6610f2;
      box-shadow: 0 0 5px rgba(102, 16, 242, 0.3);
      outline: none;
    }

    .btn-success {
      background: #28a745;
      border: none;
      color: #fff;
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      font-size: 1rem;
      transition: background 0.3s, transform 0.2s;
    }

    .btn-success:hover {
      background: #218838;
      transform: translateY(-2px);
    }

    .message {
      text-align: center;
      margin-bottom: 10px;
      font-weight: 500;
    }

    .message.success {
      color: #28a745;
    }

    .message.error {
      color: #dc3545;
    }

    .text-center {
      text-align: center;
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    .text-center a {
      color: #6610f2;
      text-decoration: none;
    }

    .text-center a:hover {
      text-decoration: underline;
    }

    footer {
      position: fixed;
      bottom: 10px;
      width: 100%;
      text-align: center;
      color: #fff;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>

  <div class="card">
    <div class="text-center mb-3">
      <div style="text-align:center;">
        <i class="fa-solid fa-user-plus" style="font-size:2.8rem; color:#28a745; margin-bottom:0.5rem;"></i>
      </div>
      <h2>Create Account</h2>
      <p class="subtitle">Register to start reserving parking slots</p>
    </div>

    <?php if ($message): ?>
      <div class="message <?= strpos($message, 'successful') !== false ? 'success' : 'error' ?>">
        <i class="fa-solid <?= strpos($message, 'successful') !== false ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="name" class="form-control" required placeholder="Enter full name">
      </div>
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" class="form-control" required placeholder="Enter email">
      </div>
      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="password" class="form-control" required placeholder="Create password">
      </div>

      <button type="submit" class="btn-success">
        <a href="login.php"><i class="fa-solid fa-user-plus"></i></a>Register
      </button>
    </form>

    <div class="text-center">
      <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>

  <footer>
    <i class="fa-solid fa-car me-1"></i> © <?= date('Y'); ?> ParkFinder | Smart Parking System
  </footer>

</body>

</html>
