<?php
session_start();
require_once("../config/db.php");
 
// if not logged in, go to login page
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit();
}
 
$userID = $_SESSION['userID'];
 
// get the basic recipe info from the form
$name        = $_POST['name'];
$categoryID  = $_POST['categoryID'];
$description = $_POST['description'];
 
// --- handle the photo upload ---
$photoFileName = null;
 
if (!empty($_FILES['photo']['name'])) {
 
    // get the file extension (e.g. jpg, png)
    $photoExt      = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
 
    // create a unique file name so photos don't overwrite each other
    $photoFileName = "recipe_" . time() . "." . $photoExt;
 
    // move the uploaded photo from the temp folder to our images folder
    move_uploaded_file($_FILES['photo']['tmp_name'], "../images/" . $photoFileName);
}
 
// --- handle the video upload (optional) ---
$videoFilePath = null;
 
if (!empty($_FILES['video']['name'])) {
 
    $videoExt      = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
    $videoFileName = "recipe_video_" . time() . "." . $videoExt;
 
    move_uploaded_file($_FILES['video']['tmp_name'], "../images/" . $videoFileName);
 
    $videoFilePath = "images/" . $videoFileName;
}
 
// --- insert the recipe into the Recipe table ---
$sql  = "INSERT INTO Recipe (userID, categoryID, name, description, photoFileName, videoFilePath)
         VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissss", $userID, $categoryID, $name, $description, $photoFileName, $videoFilePath);
$stmt->execute();
 
// get the ID of the recipe we just inserted (we need it for ingredients and instructions)
$recipeID = $conn->insert_id;
 
// --- insert each ingredient ---
// $_POST['ingredientName'] is an array of all ingredient names the user typed
// $_POST['ingredientQty'] is an array of all quantities
 
$ingredientNames = $_POST['ingredientName'];
$ingredientQtys  = $_POST['ingredientQty'];
 
for ($i = 0; $i < count($ingredientNames); $i++) {
 
    // skip empty ingredient rows
    if (!empty($ingredientNames[$i])) {
 
        $ingSQL  = "INSERT INTO Ingredients (recipeID, ingredientName, ingredientQuantity) VALUES (?, ?, ?)";
        $ingStmt = $conn->prepare($ingSQL);
        $ingStmt->bind_param("iss", $recipeID, $ingredientNames[$i], $ingredientQtys[$i]);
        $ingStmt->execute();
    }
}
 
// --- insert each instruction step ---
// $_POST['steps'] is an array of all steps the user typed
 
$steps = $_POST['steps'];
 
for ($i = 0; $i < count($steps); $i++) {
 
    // skip empty step rows
    if (!empty($steps[$i])) {
 
        // stepOrder is i+1 so it starts from 1 not 0
        $stepOrder = $i + 1;
 
        $insSQL  = "INSERT INTO Instructions (recipeID, step, stepOrder) VALUES (?, ?, ?)";
        $insStmt = $conn->prepare($insSQL);
        $insStmt->bind_param("isi", $recipeID, $steps[$i], $stepOrder);
        $insStmt->execute();
    }
}
 
// all done! go back to my recipes page
header("Location: ../Myrecipes.php");
exit();
?>