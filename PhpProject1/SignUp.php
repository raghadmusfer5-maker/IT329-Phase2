 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - SweetCrumb</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head> <!-- Dhay testing pull and push-->

<body>

    <?php require_once 'includes/header.php'; ?>

 <!-- Breadcrumb Navigation -->
   <nav class="breadcrumb">
       <div class="breadcrumb-container">
           <span class="breadcrumb-item">
               <a href="index.php">Home</a>
           </span>
           <span class="breadcrumb-separator">›</span>
           <span class="breadcrumb-item active">Sign Up</span>
       </div>
   </nav>

<main>
    <div class="form-container">
        <h2>Create Account</h2>
        <p class="subtitle">Join SweetCrumb Bakery</p>

        <form id="signupForm" action="process/signup_process.php" method="post" enctype="multipart/form-data">

            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="first_name"  placeholder="Enter your first name." required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" id="lastName" name="last_name" placeholder="Enter your last name." required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" name="email" placeholder="name@example.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password." required>
            </div>

            <div class="form-group">
                <label>Profile Image (optional)</label>
                <input type="file" id="profileImage" name="profile_image" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary btn-full">Sign Up</button>
        </form>
<?php
if(isset($_GET['error'])){

if($_GET['error'] == "emailExists"){
echo "<p style='color:#8b6f47;'>This email is already registered.</p>";
}

if($_GET['error'] == "blocked"){
echo "<p style='color:#8b6f47;'>This email belongs to a blocked user.</p>";
}

}
?>
        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>
</main>
  
 <?php require_once 'includes/footer.php'; ?>
 
</body>
</html>