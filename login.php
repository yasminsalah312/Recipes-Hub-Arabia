<?php
session_start();
include('db.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($fetched_id, $fetched_username, $fetched_email, $hashedPassword);
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['id'] = $fetched_id;
            $_SESSION['username'] = $fetched_username;
            $_SESSION['email'] = $fetched_email;
            header("Location: home.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login | Recipe Hub Arabia</title>
  <link rel="stylesheet" href="Style.css">
</head>
<body>
  <div class="form-container">
    <h2> Login</h2>
    <?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Create Account</a></p>
  </div>
</body>
</html>
