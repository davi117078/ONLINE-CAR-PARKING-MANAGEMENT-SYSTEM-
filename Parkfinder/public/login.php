<?php
require_once __DIR__ . '/../src/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  if (login_user($email, $password)) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
      header("Location: ../admin/admin_dashboard.php");
    } elseif ($role === 'attendant') {
      header("Location: ../attendant/attendant_dashboard.php");
    } else {
      header("Location: driver_dashboard.php");
    }
    exit();
  } else {
    $error = "Invalid email or password!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - ParkFinder</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    /* ===== Base Styles ===== */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: url('../public/assets/images/background3.png') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #333;
    }

    /* ===== Card Container ===== */
    .card {
      background: rgba(255, 255, 255, 0.95);
      padding: 2.5rem;
      border-radius: 15px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
      max-width: 420px;
      width: 90%;
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

    /* ===== Header Section ===== */
    .card .header {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .card .header i {
      color: #007bff;
      font-size: 2.8rem;
      margin-bottom: 0.5rem;
    }

    .card .header h2 {
      color: #007bff;
      font-weight: 700;
      font-size: 1.6rem;
    }

    .card .header p {
      color: #666;
      font-size: 0.95rem;
    }

    /* ===== Form Controls ===== */
    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .input-group {
      position: relative;
    }

    .input-group i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #007bff;
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
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
      outline: none;
    }

    /* ===== Buttons ===== */
    .btn-primary {
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 12px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }

    .btn-primary i {
      margin-right: 6px;
    }

    .btn-primary:hover {
      background: #0056b3;
      transform: translateY(-2px);
    }

    /* ===== Error Message ===== */
    .error {
      color: #dc3545;
      text-align: center;
      font-size: 0.9rem;
      margin-bottom: 10px;
    }

    /* ===== Links & Footer ===== */
    .text-center {
      text-align: center;
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    .text-center a {
      color: #007bff;
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
    <div class="header">
      <i class="fa-solid fa-square-parking"></i>
      <h2>Welcome to ParkFinder</h2>
      <p>Login to manage your parking</p>
    </div>

    <?php if ($error): ?>
      <div class="error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" class="form-control" required placeholder="Enter your email">
      </div>

      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="password" class="form-control" required placeholder="Enter your password">
      </div>

      <button type="submit" class="btn-primary">
        <i class="fa-solid fa-right-to-bracket"></i> Login
      </button>
    </form>

    <div class="text-center">
      <p>Don’t have an account? <a href="register.php">Register</a></p>
      <p><a href="forgot_password.php"><small>Forgot Password?</small></a></p>
    </div>
  </div>

  <footer>
    <i class="fa-solid fa-car me-1"></i> © <?= date('Y'); ?> ParkFinder | Smart Parking System
  </footer>

</body>

</html>
