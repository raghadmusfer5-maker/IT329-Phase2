<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SweetCrumb</title>
    <link rel="stylesheet" href="style.css">
     <script src="script.js" defer></script>
</head>
<body>
    
    <?php require_once 'includes/header.php'; ?>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
        <div class="breadcrumb-container">
            <span class="breadcrumb-item">
                <a href="index.php">Home</a>
            </span>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-item active">Login</span>
        </div>
    </nav>


    <main>
        <div class="form-container">
            <h2>Welcome Back</h2>
            <p class="subtitle">Log in to your SweetCrumb account</p>

            <form id="loginForm" action="process/login_process.php" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="name@example.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="login-buttons">
                    <button type="submit" class="btn btn-primary btn-full">Log In</button>
                    
                </div>
            </form>
            <?php
if(isset($_GET['error'])){

if($_GET['error'] == "blocked"){
echo "<p style='color:#8b6f47;'>Your account is blocked.</p>";
}

if($_GET['error'] == "email"){
echo "<p style='color:#8b6f47;'>Email not found.</p>";
}

if($_GET['error'] == "password"){
echo "<p style='color:#8b6f47;'>Incorrect password.</p>";
}

}
?>

            <div class="form-footer">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </main>

     <?php require_once 'includes/footer.php'; ?>
</body>
</html>
