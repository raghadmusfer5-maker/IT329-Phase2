<?php
session_start();
require_once("../config/db.php");
 
// ── Only admins ───────────────────────────────────────────────────────────────
if (!isset($_SESSION['userID']) || $_SESSION['userType'] != "admin") {
    header("Location: ../login.php?error=Access+denied.");
    exit();
}
 
// ── Collect POST data ─────────────────────────────────────────────────────────
$recipeID  = intval($_POST['recipeID']  ?? 0);
$creatorID = intval($_POST['creatorID'] ?? 0);
$reportID  = intval($_POST['reportID']  ?? 0);
$action    = $_POST['action']           ?? 'dismiss';
 
if ($recipeID === 0 || $reportID === 0) {
    header("Location: ../admin.php");
    exit();
}
 
// ── If action = block ─────────────────────────────────────────────────────────
if ($action === 'block' && $creatorID > 0) {
 
    // 1. Get creator info before deleting
    $stmt = $conn->prepare("SELECT firstName, lastName, emailAddress FROM user WHERE id = ?");
    $stmt->bind_param("i", $creatorID);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName, $email);
    $stmt->fetch();
    $stmt->close();
 
    // 2. Get all recipe IDs belonging to this user
    // recipe table: id, userID, name, photoFileName
    $recipeIDs = [];
    $r = $conn->prepare("SELECT id FROM recipe WHERE userID = ?");
    $r->bind_param("i", $creatorID);
    $r->execute();
    $res = $r->get_result();
    while ($row = $res->fetch_assoc()) {
        $recipeIDs[] = intval($row['id']);
    }
    $r->close();
 
    // 3. Delete all associated data for each recipe
    foreach ($recipeIDs as $rid) {
 
        // Delete recipe image file from server
        $imgStmt = $conn->prepare("SELECT photoFileName FROM recipe WHERE id = ?");
        $imgStmt->bind_param("i", $rid);
        $imgStmt->execute();
        $imgStmt->bind_result($photoFile);
        $imgStmt->fetch();
        $imgStmt->close();
        if (!empty($photoFile) && $photoFile !== 'default-user.jpg'
            && file_exists("../images/" . $photoFile)) {
            unlink("../images/" . $photoFile);
        }
 
        // Delete from related tables: comment, likes, favourites, report, ingredients, instructions
        foreach (['comment', 'likes', 'favourites', 'report', 'ingredients', 'instructions'] as $table) {
            $del = $conn->prepare("DELETE FROM $table WHERE recipeID = ?");
            $del->bind_param("i", $rid);
            $del->execute();
            $del->close();
        }
 
        // Delete the recipe itself
        $delR = $conn->prepare("DELETE FROM recipe WHERE id = ?");
        $delR->bind_param("i", $rid);
        $delR->execute();
        $delR->close();
    }
 
    // 4. Add user to blockeduser table
    $ins = $conn->prepare(
        "INSERT INTO blockeduser (firstName, lastName, emailAddress) VALUES (?, ?, ?)"
    );
    $ins->bind_param("sss", $firstName, $lastName, $email);
    $ins->execute();
    $ins->close();
 
    // 5. Delete the user from user table
    $delU = $conn->prepare("DELETE FROM user WHERE id = ?");
    $delU->bind_param("i", $creatorID);
    $delU->execute();
    $delU->close();
 
} else {
    // ── action = dismiss: just delete the report ──────────────────────────────
    $delRep = $conn->prepare("DELETE FROM report WHERE id = ?");
    $delRep->bind_param("i", $reportID);
    $delRep->execute();
    $delRep->close();
}
 
$conn->close();
 
// ── Redirect back to admin page ───────────────────────────────────────────────
header("Location: ../admin.php");
exit();
?>