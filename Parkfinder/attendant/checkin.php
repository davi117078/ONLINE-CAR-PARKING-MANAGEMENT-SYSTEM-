<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('attendant');
require_once __DIR__ . '/../src/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_code = trim($_POST['booking_code']);

    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_code = ?");
    $stmt->execute([$booking_code]);
    $booking = $stmt->fetch();

    if ($booking) {
        if ($booking['status'] === 'active') {
            $error = "This booking is already active!";
        } elseif ($booking['status'] === 'completed') {
            $error = "This booking has already been completed!";
        } else {
            $update = $pdo->prepare("UPDATE bookings SET status='active', checkin_time=NOW() WHERE booking_code=?");
            if ($update->execute([$booking_code])) {
                $message = "Vehicle successfully checked in.";
            } else {
                $error = "Failed to update booking. Try again.";
            }
        }
    } else {
        $error = "Booking not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In - ParkFinder</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #0062ff, #003d99);
            color: #fff;
            padding: 20px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            font-size: 1.4rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: background 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main {
            flex-grow: 1;
            padding: 30px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        h4 {
            text-align: center;
            margin-bottom: 20px;
            color: #003d99;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        .text-muted {
            color: #6c757d;
            font-size: 0.9rem;
            text-align: center;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                flex-direction: row;
                justify-content: space-around;
            }

            .main {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h2><i class="fa-solid fa-square-parking"></i> ParkFinder</h2>
            <a href="attendant_dashboard.php"><i class="fa-solid fa-gauge-high me-2"></i> Dashboard</a>
            <a href="checkin.php" class="active"><i class="fa-solid fa-sign-in-alt me-2"></i> Check In</a>
            <a href="checkout.php"><i class="fa-solid fa-sign-out-alt me-2"></i> Check Out</a>
            <a href="view_bookings.php"><i class="fa-solid fa-list me-2"></i> Bookings</a>
        </div>
        <a href="../public/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
    </div>

    <!-- Main Section -->
    <div class="main">
        <div class="card">
            <h4><i class="fa-solid fa-sign-in-alt me-2"></i> Vehicle Check-In</h4>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <label><i class="fa-solid fa-ticket me-1"></i> Booking Code</label>
                <input type="text" name="booking_code" placeholder="Enter booking code" required>
                <button type="submit">
                    <i class="fa-solid fa-check me-1"></i> Confirm Check-In
                </button>
            </form>

            <div class="text-muted">
                <i class="fa-solid fa-circle-info me-1"></i>
                Enter a valid booking code to check in a vehicle.
            </div>
        </div>
    </div>

</body>

</html>
