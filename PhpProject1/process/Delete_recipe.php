<?php
session_start();
require_once("../config/db.php");
 
// if not logged in, go to login page
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit();
}
 
$userID   = $_SESSION['userID'];
$recipeID = $_GET['id'];
 
// first, get the photo and video file names so we can delete them from the server
$sql  = "SELECT photoFileName, videoFilePath FROM Recipe WHERE id = ? AND userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipeID, $userID);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
 
// if recipe not found or doesn't belong to this user, just redirect
if (!$recipe) {
    header("Location: ../Myrecipes.php");
    exit();
}
 
// delete ingredients
$conn->query("DELETE FROM Ingredients WHERE recipeID = $recipeID");
 
// delete instructions
$conn->query("DELETE FROM Instructions WHERE recipeID = $recipeID");
 
// delete comments
$conn->query("DELETE FROM Comment WHERE recipeID = $recipeID");
 
// delete likes
$conn->query("DELETE FROM Likes WHERE recipeID = $recipeID");
 
// delete from favourites
$conn->query("DELETE FROM Favourites WHERE recipeID = $recipeID");
 
// delete reports
$conn->query("DELETE FROM Report WHERE recipeID = $recipeID");
 
// now delete the recipe itself
$stmt = $conn->prepare("DELETE FROM Recipe WHERE id = ? AND userID = ?");
$stmt->bind_param("ii", $recipeID, $userID);
$stmt->execute();
 
// delete the photo file from the server if it exists
if (!empty($recipe['photoFileName'])) {
    $photoPath = "../images/" . $recipe['photoFileName'];
    if (file_exists($photoPath)) {
        unlink($photoPath);
    }
}
 
// delete the video file from the server if it exists
if (!empty($recipe['videoFilePath'])) {
    $videoPath = "../" . $recipe['videoFilePath'];
    if (file_exists($videoPath)) {
        unlink($videoPath);
    }
}
 
// go back to my recipes page
header("Location: ../Myrecipes.php");
exit();
?>