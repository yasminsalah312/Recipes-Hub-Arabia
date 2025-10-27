<?php
session_start();
include('db.php');
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];

// Get user info
$stmt = $conn->prepare("SELECT username, email FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get user's own recipes
$stmt2 = $conn->prepare("SELECT * FROM recipes WHERE username = ? ORDER BY id DESC");
$stmt2->bind_param("s", $username);
$stmt2->execute();
$myRecipes = $stmt2->get_result();

// Get favorite recipes
$stmt3 = $conn->prepare("SELECT recipes.* FROM recipes INNER JOIN favorites ON recipes.id = favorites.recipe_id WHERE favorites.username = ? ORDER BY recipes.id DESC");
$stmt3->bind_param("s", $username);
$stmt3->execute();
$favorites = $stmt3->get_result();

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'my-recipes';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Profile | Recipe Hub Arabia</title>
  <link rel="stylesheet" href="Style.css">
</head>
<body>
  <header>
    <h1>🍽 Recipe Hub Arabia</h1>
    <nav>
      <a href="home.php">Home</a> |
      <a href="add_recipe.php">Add Recipe</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="profile-container">
    <div class="profile-header">
      <h2>👤 Profile</h2>
      <div class="profile-info">
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
      </div>
    </div>

    <div class="profile-links">
      <a class="btn" href="add_recipe.php"> Add New Recipe</a>
      <a class="btn" href="logout.php"> Logout</a>
    </div>

    <div class="tabs">
      <a class="tab-btn <?= $tab==='my-recipes' ? 'active' : '' ?>" href="?tab=my-recipes"> My Recipes</a>
      <a class="tab-btn <?= $tab==='favorites' ? 'active' : '' ?>" href="?tab=favorites"> Favorites</a>
    </div>

    <?php if($tab==='my-recipes'): ?>
      <div>
        <?php if($myRecipes->num_rows>0): while($r=$myRecipes->fetch_assoc()): ?>
          <div class="recipe-card">
            <div style="flex:1">
              <h4><?= htmlspecialchars($r['title']) ?></h4>
              <p><strong>Country:</strong> <?= htmlspecialchars($r['country']) ?></p>
              <p><?= nl2br(htmlspecialchars($r['ingredients'])) ?></p>
              <p><?= nl2br(htmlspecialchars($r['steps'])) ?></p>
            </div>
            <?php if(!empty($r['image']) && file_exists('uploads/'.$r['image'])): ?>
              <div style="flex:0 0 250px">
                <img src="uploads/<?= htmlspecialchars($r['image']) ?>" alt="" style="width:100%;border-radius:8px">
              </div>
            <?php endif; ?>
          </div>
        <?php endwhile; else: ?>
          <p>No recipes yet.</p>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div>
        <?php if($favorites->num_rows>0): while($f=$favorites->fetch_assoc()): ?>
          <div class="recipe-card">
            <div style="flex:1">
              <h4><?= htmlspecialchars($f['title']) ?></h4>
              <p><strong>Country:</strong> <?= htmlspecialchars($f['country']) ?></p>
              <p><?= nl2br(htmlspecialchars($f['ingredients'])) ?></p>
              <p><?= nl2br(htmlspecialchars($f['steps'])) ?></p>
            </div>
            <?php if(!empty($f['image']) && file_exists('uploads/'.$f['image'])): ?>
              <div style="flex:0 0 250px">
                <img src="uploads/<?= htmlspecialchars($f['image']) ?>" alt="" style="width:100%;border-radius:8px">
              </div>
            <?php endif; ?>
          </div>
        <?php endwhile; else: ?>
          <p>No favorite recipes yet.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </main>
</body>
</html>
