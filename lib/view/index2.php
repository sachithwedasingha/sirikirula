<?php
// lib/view/index2.php
session_start();

// Only allow logged-in users with Customer role
if (empty($_SESSION['user']) || empty($_SESSION['usertype'])) {
    header('Location: /'); // redirect to login page (adjust if your login path differs)
    exit;
}

// Accept 'Customer' (case-insensitive)
if (strtolower($_SESSION['usertype']) !== 'customer') {
    // Optional: send Admins to admin page
    header('Location: /lib/view/index.php');
    exit;
}

$userId = htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
$role = htmlspecialchars($_SESSION['usertype'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard - Customer</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background:#fffbe6; }
    .card { background:#fff; padding:20px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); max-width:900px; margin:auto; text-align:center; }
    img.logo { max-width:200px; height:auto; display:block; margin: 0 auto 12px; }
    .meta { color:#666; margin-bottom:18px; }
    .btn { display:inline-block; padding:8px 14px; border-radius:6px; background:#28a745; color:#fff; text-decoration:none; }
  </style>
</head>
<body>
  <div class="card">
    <!-- Local uploaded file used as logo (exact path you provided) -->
    <img src="/mnt/data/05968ca8-b8bc-4fc2-a73e-d00a04c4625d.png" alt="Logo" class="logo">

    <h1>Welcome, Customer</h1>
    <p class="meta">User ID: <strong><?= $userId ?></strong> &middot; Role: <strong><?= $role ?></strong></p>

    <p>This is the customer dashboard page (lib/view/index2.php). Only users with the Customer role can access this page.</p>

    <p>
      <a class="btn" href="/lib/view/logout.php">Logout</a>
    </p>
  </div>
</body>
</html>
