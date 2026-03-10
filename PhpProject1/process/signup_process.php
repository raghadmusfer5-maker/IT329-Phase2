<?php

session_start();
require_once("../config/db.php");

$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$email = $_POST['email'];
$password = $_POST['password'];


// 1️⃣ check if email already exists in user table

$sql = "SELECT * FROM user WHERE emailAddress = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
header("Location: ../signup.php?error=emailExists");
exit();
}


// 2️⃣ check blocked users

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


// 4️⃣ handle image upload

if(isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] != ""){

$photoName = time() . "_" . $_FILES['profile_image']['name'];

move_uploaded_file(
$_FILES['profile_image']['tmp_name'],
"../images/" . $photoName
);

}else{

$photoName = "default-user.jpg";

}


// 5️⃣ insert new user

$sql = "INSERT INTO user
(userType, firstName, lastName, emailAddress, password, photoFileName)
VALUES ('user', ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $photoName);
$stmt->execute();


// 6️⃣ get inserted ID

$userID = $conn->insert_id;


// 7️⃣ create session

$_SESSION['userID'] = $userID;
$_SESSION['userType'] = "user";


// 8️⃣ redirect

header("Location: ../user.php");
exit();

?>