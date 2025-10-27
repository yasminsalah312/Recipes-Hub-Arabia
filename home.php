<?php
session_start();
include('db.php');

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Handle favorite action (add/remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recipe_id']) && $username) {
    $recipe_id = intval($_POST['recipe_id']);

    // Check if already in favorites
    $check = $conn->prepare("SELECT id FROM favorites WHERE username = ? AND recipe_id = ?");
    $check->bind_param("si", $username, $recipe_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // Remove from favorites
        $delete = $conn->prepare("DELETE FROM favorites WHERE username = ? AND recipe_id = ?");
        $delete->bind_param("si", $username, $recipe_id);
        $delete->execute();
        $msg = "Removed from favorites ";
    } else {
        // Add to favorites
        $insert = $conn->prepare("INSERT INTO favorites (username, recipe_id, created_at) VALUES (?, ?, NOW())");
        $insert->bind_param("si", $username, $recipe_id);
        $insert->execute();
        $msg = "Added to favorites ";
    }

    // Redirect to home with message
    header("Location: home.php?msg=" . urlencode($msg));
    exit();
}

// Fetch all recipes (newest first)
$stmt = $conn->prepare("SELECT id, country, username, title, created_at, ingredients, steps, image FROM recipes ORDER BY id DESC");
$stmt->execute();
$recipes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Recipe Hub Arabia | Home</title>
  <link rel="stylesheet" href="Style.css">
  <style>
    .flash-message {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 15px 25px;
      border-radius: 10px;
      font-size: 18px;
      text-align: center;
      z-index: 9999;
      opacity: 1;
      animation: fadeOut 2s forwards 0.5s;
    }

    @keyframes fadeOut {
      0% { opacity: 1; }
      90% { opacity: 0.3; }
      100% { opacity: 0; display: none; }
    }
  </style>
</head>
<body>
  <header>
    <h1>🍽 Recipe Hub Arabia</h1>
    <nav>
      <?php if($username): ?>
        <a href="profile.php">My Profile</a> |
        <a href="add_recipe.php">Add Recipe</a> |
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </header>

  <?php if(isset($_GET['msg'])): ?>
    <div class="flash-message"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <main class="posts-wrapper">
    <div class="posts-container">
      <?php while($r = $recipes->fetch_assoc()): ?>
      <article class="recipe-card">
        <div class="left">
          <?php if(!empty($r['image']) && file_exists('uploads/'.$r['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['title']) ?>">
          <?php else: ?>
            <img src="https://via.placeholder.com/220x140?text=No+Image" alt="no image">
          <?php endif; ?>
        </div>

        <div class="right">
          <div class="header"><?= htmlspecialchars($r['username']) ?> | <?= htmlspecialchars($r['country']) ?></div>
          <h3><?= htmlspecialchars($r['title']) ?></h3>
          <div class="caption"><p> <?= nl2br(htmlspecialchars($r['ingredients'])) ?></p></div>
          <div class="details">
            <p><strong>Steps:</strong> <?= nl2br(htmlspecialchars($r['steps'])) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($r['created_at']) ?></p>
          </div>

          <?php if($username): ?>
          <div style="margin-top:10px;">
            <form method="POST" action="home.php">
              <input type="hidden" name="recipe_id" value="<?= $r['id'] ?>">
              <button type="submit"> Favorite</button>
            </form>
          </div>
          <?php endif; ?>
        </div>
      </article>
      <?php endwhile; ?>
    </div>
  </main>
</body>
</html>
