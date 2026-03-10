<?php

session_start();
require_once("../config/db.php");

$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$email = $_POST['email'];
$password = $_POST['password'];

// 1️⃣ check if email already exists in User table

$sql = "SELECT * FROM user WHERE emailAddress = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
header("Location: ../signup.php?error=emailExists");
exit();
}

// 2️⃣ check if email exists in BlockedUser table

$sql = "SELECT * FROM blockeduser WHERE emailAddress = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
header("Location: ../signup.php?error=blocked");
exit();
}



// 3️⃣ hash password

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);



// 4️⃣ handle photo upload

if($_FILES['profile_image']['name'] != ""){

$photoName = time() . "_" . $_FILES['photo']['name'];

move_uploaded_file(
$_FILES['profile_image']['tmp_name'],
"../images/" . $photoName
);

}

else{

$photoName = "default-user.jpg";

}



// 5️⃣ insert user into database

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $photoName);
$stmt->execute();



// 6️⃣ get inserted user id

$userID = $conn->lastInsertId();



// 7️⃣ create session variables

$_SESSION['userID'] = $userID;
$_SESSION['userType'] = "user";



// 8️⃣ redirect to user page

header("Location: ../user.php");
exit();

?>