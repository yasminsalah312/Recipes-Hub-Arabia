<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['recipe_id'])) {
    echo "❌ لم يتم تحديد الوصفة.";
    exit();
}

$username = $_SESSION['username'];
$recipe_id = intval($_POST['recipe_id']);

$check = $conn->prepare("SELECT id FROM favorites WHERE username=? AND recipe_id=?");
$check->bind_param("si", $username, $recipe_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $delete = $conn->prepare("DELETE FROM favorites WHERE username=? AND recipe_id=?");
    $delete->bind_param("si", $username, $recipe_id);
    $delete->execute();

    header("Location: home.php?msg=removed");
    exit();
} else {
    $insert = $conn->prepare("INSERT INTO favorites (username, recipe_id, created_at) VALUES (?, ?, NOW())");
    $insert->bind_param("si", $username, $recipe_id);
    $insert->execute();

    header("Location: home.php?msg=added");
    exit();
}
?>
