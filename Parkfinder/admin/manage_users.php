<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../src/db.php';

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: manage_users.php?msg=exists");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $role]);
    header("Location: manage_users.php?msg=added");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    header("Location: manage_users.php?msg=deleted");
    exit;
}

// Fetch all users
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users - ParkFinder</title>
<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        margin: 0;
        background: #f2f3f7;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        background: linear-gradient(180deg, #007bff, #0047a3);
        color: white;
        padding: 20px;
        min-height: 100vh;
    }

    .sidebar h2 {
        text-align: center;
        font-size: 1.4em;
        margin-bottom: 30px;
    }

    .sidebar a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .sidebar a:hover, .sidebar a.active {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Main area */
    .main {
        flex-grow: 1;
        padding: 20px;
    }

    .main h3 {
        margin-bottom: 20px;
    }

    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn {
        padding: 8px 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #a71d2a;
    }

    .btn-warning {
        background-color: #ffc107;
        color: black;
    }

    .btn-warning:hover {
        background-color: #d39e00;
    }

    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 5px;
        overflow: hidden;
    }

    table th, table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table thead {
        background-color: #007bff;
        color: white;
    }

    table tr:hover {
        background: #f1f1f1;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8em;
        color: white;
    }

    .bg-secondary {
        background-color: gray;
    }

    /* Alerts */
    .alert {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        max-width: 90%;
    }

    .modal-header {
        font-weight: bold;
        margin-bottom: 10px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 8px;
    }

    .modal-footer {
        text-align: right;
        margin-top: 10px;
    }

    input, select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
</style>
</head>

<body>

<div class="sidebar">
    <h2>ParkFinder Admin</h2>
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <a href="manage_slots.php">🅿️ Manage Slots</a>
    <a href="manage_users.php" class="active">👥 Manage Users</a>
    <a href="view_bookings.php">📋 View Bookings</a>
    <a href="reports.php">📊 Reports</a>
    <a href="../public/logout.php">🚪 Logout</a>
</div>

<div class="main">
    <div class="topbar">
        <h3>Manage Users</h3>
        <button class="btn btn-primary" onclick="openModal()">+ Add New User</button>
    </div>

    <?php if (isset($_GET['msg'])): 
        $messages = [
            'added' => 'User added successfully!',
            'deleted' => 'User deleted successfully!',
            'exists' => 'Email already exists!'
        ]; ?>
        <div class="alert alert-info"><?= htmlspecialchars($messages[$_GET['msg']] ?? 'Action completed!') ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['id']) ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($u['role'])) ?></span></td>
                <td>
                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <div class="modal-header">Add New User</div>
        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Role</label>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="attendant">Attendant</option>
                <option value="driver">Driver</option>
            </select>

            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                <button type="submit" name="add_user" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('userModal').classList.add('active');
}
function closeModal() {
    document.getElementById('userModal').classList.remove('active');
}
</script>

</body>
</html>
