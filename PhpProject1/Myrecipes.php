<?php
session_start();
require_once("config/db.php");
 
// if not logged in, go to login page
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
 
$userID = $_SESSION['userID'];
 
// get all recipes that belong to this user
// also count how many likes each recipe has
$sql = "SELECT Recipe.id, Recipe.name, Recipe.photoFileName, Recipe.videoFilePath,
               COUNT(Likes.userID) AS totalLikes
        FROM Recipe
        LEFT JOIN Likes ON Recipe.id = Likes.recipeID
        WHERE Recipe.userID = ?
        GROUP BY Recipe.id";
 
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Recipes</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
 
<header>
  <svg xmlns="http://www.w3.org/2000/svg" width="320" height="80" viewBox="0 0 320 80">
  <defs>
    <linearGradient id="textGradient" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#e9bdbd;stop-opacity:1" />
      <stop offset="50%" style="stop-color:#d4a5a5;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#e9bec2;stop-opacity:1" />
    </linearGradient>
    <filter id="shadow">
      <feDropShadow dx="1" dy="2" stdDeviation="2" flood-opacity="0.3" flood-color="#8b6f47"/>
    </filter>
  </defs>
  <circle cx="35" cy="40" r="24" fill="#d4a5a5" opacity="0.12"/>
  <g transform="translate(10, 15)" filter="url(#shadow)">
    <circle cx="25" cy="25" r="16" fill="#d4a574"/>
    <circle cx="25" cy="25" r="16" fill="url(#textGradient)" opacity="0.15"/>
    <circle cx="25" cy="25" r="16" fill="none" stroke="#c49563" stroke-width="1" opacity="0.6"/>
    <circle cx="20" cy="14" r="2" fill="#c49563" opacity="0.4"/>
    <circle cx="35" cy="18" r="1.5" fill="#c49563" opacity="0.4"/>
    <circle cx="32" cy="32" r="2" fill="#c49563" opacity="0.4"/>
    <ellipse cx="18" cy="20" rx="2.5" ry="2.8" fill="#5d3a1a"/>
    <ellipse cx="18" cy="20" rx="1.5" ry="1.8" fill="#4a2d15" opacity="0.7"/>
    <ellipse cx="31" cy="21" rx="2.2" ry="2.5" fill="#5d3a1a"/>
    <ellipse cx="31" cy="21" rx="1.3" ry="1.5" fill="#4a2d15" opacity="0.7"/>
    <ellipse cx="23" cy="31" rx="2.4" ry="2.7" fill="#5d3a1a"/>
    <ellipse cx="23" cy="31" rx="1.4" ry="1.7" fill="#4a2d15" opacity="0.7"/>
    <ellipse cx="15" cy="28" rx="1.8" ry="2" fill="#5d3a1a"/>
    <ellipse cx="15" cy="28" rx="1" ry="1.2" fill="#4a2d15" opacity="0.7"/>
    <ellipse cx="33" cy="31" rx="2" ry="2.3" fill="#5d3a1a"/>
    <ellipse cx="33" cy="31" rx="1.2" ry="1.4" fill="#4a2d15" opacity="0.7"/>
    <ellipse cx="26" cy="17" rx="1.9" ry="2.1" fill="#5d3a1a"/>
    <ellipse cx="26" cy="17" rx="1.1" ry="1.3" fill="#4a2d15" opacity="0.7"/>
    <circle cx="22" cy="18" r="4" fill="#fff" opacity="0.25"/>
  </g>
  <text x="70" y="50" font-family="Georgia, 'Times New Roman', serif" font-size="36" font-weight="700" letter-spacing="0.5" filter="url(#shadow)" fill="url(#textGradient)">Sweet</text>
  <text x="185" y="50" font-family="Georgia, 'Times New Roman', serif" font-size="36" font-weight="700" letter-spacing="0.5" filter="url(#shadow)" fill="#d4a5a5">Crumb</text>
  <path d="M 72 55 Q 140 58, 240 55" stroke="#e9bdbd" stroke-width="1.5" fill="none" opacity="0.5"/>
  <g transform="translate(70, 60)">
    <g transform="scale(0.7)" opacity="0.6">
      <line x1="4" y1="0" x2="4" y2="12" stroke="#d4a5a5" stroke-width="1.2"/>
      <ellipse cx="4" cy="2" rx="2.5" ry="1.8" fill="#d4a5a5"/>
      <ellipse cx="4" cy="5" rx="2.8" ry="2" fill="#d4a5a5"/>
      <ellipse cx="4" cy="8" rx="2.5" ry="1.8" fill="#d4a5a5"/>
    </g>
    <text x="12" y="9" font-family="'Segoe UI', Arial, sans-serif" font-size="11" fill="#d4a5a5" letter-spacing="3" font-weight="500" opacity="0.9">HEALTHY BAKERY</text>
  </g>
  <circle cx="285" cy="25" r="2" fill="#e9bdbd" opacity="0.4"/>
  <circle cx="293" cy="28" r="1.5" fill="#d4a5a5" opacity="0.4"/>
  <circle cx="300" cy="25" r="2.5" fill="#e9bec2" opacity="0.4"/>
</svg>
</header>
 
<!-- Breadcrumb Navigation -->
<nav class="breadcrumb">
  <div class="breadcrumb-container">
    <span class="breadcrumb-item"><a href="index.php">Home</a></span>
    <span class="breadcrumb-separator">›</span>
    <span class="breadcrumb-item"><a href="user.php">User Dashboard</a></span>
    <span class="breadcrumb-separator">›</span>
    <span class="breadcrumb-item active">My Recipes</span>
  </div>
</nav>
 
<div class="container">
 
  <div class="card" style="display:flex; justify-content:space-between;">
    <div class="section-title">My Recipes</div>
    <a href="add-recipe.php" class="btn btn-primary">Add New Recipe</a>
  </div>
 
  <div class="card">
 
    <?php if ($result->num_rows == 0) { ?>
 
      <!-- show this message if the user has no recipes -->
      <p style="text-align:center; padding:2rem; color:#a0a0a0;">
        You haven't added any recipes yet.
      </p>
 
    <?php } else { ?>
 
      <table>
        <thead>
          <tr>
            <th>Recipe</th>
            <th>Ingredients</th>
            <th>Instructions</th>
            <th>Video</th>
            <th>Likes</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
 
        <tbody>
 
          <?php while ($recipe = $result->fetch_assoc()) { ?>
 
            <?php
            // get ingredients for this recipe
            $ingSQL = "SELECT ingredientName, ingredientQuantity FROM Ingredients WHERE recipeID = ?";
            $ingStmt = $conn->prepare($ingSQL);
            $ingStmt->bind_param("i", $recipe['id']);
            $ingStmt->execute();
            $ingredients = $ingStmt->get_result();
 
            // get instructions for this recipe
            $insSQL = "SELECT step FROM Instructions WHERE recipeID = ? ORDER BY stepOrder ASC";
            $insStmt = $conn->prepare($insSQL);
            $insStmt->bind_param("i", $recipe['id']);
            $insStmt->execute();
            $instructions = $insStmt->get_result();
            ?>
 
            <tr>
 
              <!-- recipe name and photo are links to the view recipe page -->
              <td>
                <a href="ViewRecipe.php?id=<?php echo $recipe['id']; ?>">
                  <img src="images/<?php echo $recipe['photoFileName']; ?>" class="recipe-photo"><br>
                  <?php echo $recipe['name']; ?>
                </a>
              </td>
 
              <!-- ingredients list -->
              <td>
                <ul>
                  <?php while ($ing = $ingredients->fetch_assoc()) { ?>
                    <li><?php echo $ing['ingredientQuantity'] . " " . $ing['ingredientName']; ?></li>
                  <?php } ?>
                </ul>
              </td>
 
              <!-- instructions list -->
              <td>
                <ol>
                  <?php while ($ins = $instructions->fetch_assoc()) { ?>
                    <li><?php echo $ins['step']; ?></li>
                  <?php } ?>
                </ol>
              </td>
 
              <!-- video link -->
              <td>
                <?php if (!empty($recipe['videoFilePath'])) { ?>
                  <a href="<?php echo $recipe['videoFilePath']; ?>" target="_blank">Watch video</a>
                <?php } else { ?>
                  No video
                <?php } ?>
              </td>
 
              <!-- total likes count from database -->
              <td><?php echo $recipe['totalLikes']; ?></td>
 
              <!-- edit link goes to edit recipe page with the recipe id -->
              <td>
                <a href="edit-recipe.php?id=<?php echo $recipe['id']; ?>">Edit</a>
              </td>
 
              <!-- delete link goes to delete page with the recipe id -->
              <td>
                <a href="process/delete_recipe.php?id=<?php echo $recipe['id']; ?>"
                   onclick="return confirm('Are you sure you want to delete this recipe?');"
                   style="color:red;">Delete</a>
              </td>
 
            </tr>
 
          <?php } ?>
 
        </tbody>
      </table>
 
    <?php } ?>
 
  </div>
 
</div>
 
<footer>
  <p>&copy; 2026 SweetCrumb. Baking dreams come true.</p>
  <div class="social-links">
    <a href="https://instagram.com/sweetcrumb" class="social" aria-label="Instagram">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
        <path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 5a5 5 0 100 10 5 5 0 000-10zm6-.8a1 1 0 100 2 1 1 0 000-2z"/>
      </svg>
    </a>
    <a href="https://twitter.com/sweetcrumb" class="social" aria-label="X">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
        <path d="M19.6 2.3h2.4l-5.2 6 6.1 8h-4.8l-3.6-4.8-4.1 4.8H3.8l5.6-6.4L3.6 2.3H8l3.2 4.4z"/>
      </svg>
    </a>
    <a href="mailto:SweetCrumb@gmail.com" class="social" aria-label="Email">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
        <path d="M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm0 2l8 5 8-5"/>
      </svg>
    </a>
    <a href="tel:+966550886681" class="social" aria-label="Phone">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
        <path d="M6.6 10.8a15 15 0 006.6 6.6l2.2-2.2a1 1 0 011.1-.2c1.2.5 2.5.8 3.9.8a1 1 0 011 1V20a1 1 0 01-1 1C12.4 21 3 11.6 3 1a1 1 0 011-1h3.2a1 1 0 011 1c0 1.4.3 2.7.8 3.9.1.4 0 .8-.2 1.1l-2.2 2.2z"/>
      </svg>
    </a>
  </div>
</footer>
 
</body>
</html>
 