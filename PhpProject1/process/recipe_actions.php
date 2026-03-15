<?php
session_start();
require_once("../config/db.php");
 
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit();
}
 
$recipeID = intval($_POST['recipeID'] ?? 0);
$userID   = $_SESSION['userID'];
$action   = $_POST['action'] ?? '';
 
if ($recipeID === 0) {
    header("Location: ../index.php");
    exit();
}
 
// ── Add Comment ───────────────────────────────────────────────────────────────
if ($action === 'comment') {
    $comment = trim($_POST['comment'] ?? '');
    if ($comment !== '') {
        $stmt = $conn->prepare("INSERT INTO comment (recipeID, userID, comment, date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $recipeID, $userID, $comment);
        $stmt->execute();
        $stmt->close();
    }
}
 
// ── Add Like ──────────────────────────────────────────────────────────────────
elseif ($action === 'like') {
    $check = $conn->prepare("SELECT 1 FROM likes WHERE userID = ? AND recipeID = ?");
    $check->bind_param("ii", $userID, $recipeID);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
    $check->close();
 
    if (!$exists) {
        $stmt = $conn->prepare("INSERT INTO likes (userID, recipeID) VALUES (?, ?)");
        $stmt->bind_param("ii", $userID, $recipeID);
        $stmt->execute();
        $stmt->close();
    }
}
 
// ── Add Favourite ─────────────────────────────────────────────────────────────
elseif ($action === 'favourite') {
    $check = $conn->prepare("SELECT 1 FROM favourites WHERE userID = ? AND recipeID = ?");
    $check->bind_param("ii", $userID, $recipeID);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
    $check->close();
 
    if (!$exists) {
        $stmt = $conn->prepare("INSERT INTO favourites (userID, recipeID) VALUES (?, ?)");
        $stmt->bind_param("ii", $userID, $recipeID);
        $stmt->execute();
        $stmt->close();
    }
}
 
// ── Add Report ────────────────────────────────────────────────────────────────
elseif ($action === 'report') {
    $check = $conn->prepare("SELECT 1 FROM report WHERE userID = ? AND recipeID = ?");
    $check->bind_param("ii", $userID, $recipeID);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
    $check->close();
 
    if (!$exists) {
        $stmt = $conn->prepare("INSERT INTO report (userID, recipeID) VALUES (?, ?)");
        $stmt->bind_param("ii", $userID, $recipeID);
        $stmt->execute();
        $stmt->close();
    }
}
 
$conn->close();
ob_start();
header("Location: ../ViewRecipe.php?id=" . $recipeID);
ob_end_flush();
exit();
?>