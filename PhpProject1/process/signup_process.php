<?php

session_start();
require_once("../config/db.php");

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];

// 1️⃣ check if email already exists in User table

$sql = "SELECT * FROM User WHERE emailAddress = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);

if($stmt->rowCount() > 0){

header("Location: ../signup.php?error=emailExists");
exit();

}

// 2️⃣ check if email exists in BlockedUser table

$sql = "SELECT * FROM BlockedUser WHERE emailAddress = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);

if($stmt->rowCount() > 0){

header("Location: ../signup.php?error=blocked");
exit();

}



// 3️⃣ hash password

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);



// 4️⃣ handle photo upload

if($_FILES['photo']['name'] != ""){

$photoName = time() . "_" . $_FILES['photo']['name'];

move_uploaded_file(
$_FILES['photo']['tmp_name'],
"../images/users/" . $photoName
);

}

else{

$photoName = "default-user.jpg";

}



// 5️⃣ insert user into database

$sql = "INSERT INTO User
(userType, firstName, lastName, emailAddress, password, photoFileName)
VALUES ('user', ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->execute([
$firstName,
$lastName,
$email,
$hashedPassword,
$photoName
]);



// 6️⃣ get inserted user id

$userID = $conn->lastInsertId();



// 7️⃣ create session variables

$_SESSION['userID'] = $userID;
$_SESSION['userType'] = "user";



// 8️⃣ redirect to user page

header("Location: ../userPage.php");
exit();

?>