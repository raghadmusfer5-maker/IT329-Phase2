<!DOCTYPE html>
<html lang="en"> <!--jana-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SweetCrumb - Share Your Sweet Creations</title>
    <link rel="stylesheet" href="style.css">
</head>   <!-- Dhay testing pull and push -->
<body>
    
    <?php require_once 'includes/header.php'; ?>

    <main>
        <div class="hero-section">
            <h1>Welcome to SweetCrumb</h1>
            <p>Celebrate the art of clean and healthy baking. Share your recipes, discover healthy desserts, and join a community passionate about mindful sweetness!</p>
            
            <div class="cta-buttons">
                <a href="login.php" class="btn btn-primary btn-large">Log In</a>
            </div>
            
            <p class="new-user-text">New user? <a href="signup.php" class="signup-link">Create a new account</a></p>

            <div class="features">
                <div class="feature">
                    <h3>Share Recipes</h3>
                    <p>Upload your favorite baking creations</p>
                </div>
                <div class="feature">
                    <h3>Save Favorites</h3>
                    <p>Bookmark recipes you love</p>
                </div>
                <div class="feature">
                    <h3>Connect</h3>
                    <p>Comment and interact with bakers</p>
                </div>
            </div>
        </div>
    </main>

   <?php require_once 'includes/footer.php'; ?>
    
</body>
</html>