<?php

session_start();
require_once("../config/db.php");

$email = $_POST['email'];
$password = $_POST['password'];


/* 1️⃣ Check if user is blocked */

$sql = "SELECT * FROM blockeduser WHERE emailAddress = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
header("Location: ../login.php?error=blocked");
exit();
}


/* 2️⃣ Check if user exists */

$sql = "SELECT * FROM user WHERE emailAddress = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
header("Location: ../login.php?error=email");
exit();
}

$user = $result->fetch_assoc();


/* 3️⃣ Verify password */

if(!password_verify($password, $user['password'])){
header("Location: ../login.php?error=password");
exit();
}


/* 4️⃣ Login successful */

$_SESSION['userID'] = $user['id'];
$_SESSION['userType'] = $user['userType'];


/* 5️⃣ Redirect based on user type */

if($user['userType'] == "admin"){
header("Location: ../admin.php");
}else{
header("Location: ../user.php");
}

exit();

?>