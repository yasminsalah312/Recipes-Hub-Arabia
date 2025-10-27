<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Logout | Recipe Hub Arabia</title>
  <link rel="stylesheet" href="Style.css">
</head>
<body>
  <div class="form-container">
    <h2>Confirm Logout</h2>
    <p>Are you sure you want to log out?</p>
    <form method="POST">
      <button type="submit">Logout</button>
    </form>
    <p><a href="home.php">← Back to Home</a></p>
  </div>
</body>
</html>
