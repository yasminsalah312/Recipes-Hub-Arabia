<?php
session_start();
include('db.php');

if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; 
$email = $_SESSION['email'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $country = $_POST['country'];
    $ingredients = $_POST['ingredients'];
    $steps = $_POST['steps'];

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image_name);
        $image = $image_name;
    }

    $stmt = $conn->prepare("INSERT INTO recipes (country, username, title, created_at, ingredients, steps, image) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ssssss", $country, $username, $title, $ingredients, $steps, $image);

    if ($stmt->execute()) {
        $message = "✅ Recipe added successfully!";
    } else {
        $message = "❌ An error occurred while saving the recipe.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Recipe | Recipe Hub Arabia</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <div class="container add-recipe-form">
        <h2>🍳 Add Your Recipe</h2>
        <?php if ($message != '') echo "<p class='msg'>$message</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Recipe title" required>

            <select name="country" required>
                <option value="">Select cuisine</option>
                <option value="Egyptian">Egyptian Cuisine</option>
                <option value="Saudi">Saudi Cuisine</option>
                <option value="Syrian">Syrian Cuisine</option>
                <option value="Lebanese">Lebanese Cuisine</option>
                <option value="Moroccan">Moroccan Cuisine</option>
            </select>

            <textarea name="ingredients" placeholder="Ingredients..." required></textarea>
            <textarea name="steps" placeholder="Preparation steps..." required></textarea>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Publish Recipe</button>
        </form>

        <p><a href="home.php">← Back to Home</a></p>
    </div>
</body>
</html>