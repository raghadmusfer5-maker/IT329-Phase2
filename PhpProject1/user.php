<?php

session_start();
require_once("config/db.php");

// requirement 5 & 6a: check logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// requirement 6a: check user type is "user", not admin
if ($_SESSION['userType'] != "user") {
    header("Location: login.php?error=notUser");
    exit();
}

$userID = $_SESSION['userID'];

// requirement 6b: get user info from database
$sql = "SELECT * FROM User WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// requirement 6c: get total number of recipes for this user
$countSQL = "SELECT COUNT(*) AS totalRecipes FROM Recipe WHERE userID = ?";
$countStmt = $conn->prepare($countSQL);
$countStmt->bind_param("i", $userID);
$countStmt->execute();
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();
$totalRecipes = $countRow['totalRecipes'];

// requirement 6c: get total number of likes for all recipes by this user
$likesSQL = "SELECT COUNT(Likes.userID) AS totalLikes
             FROM Likes
             JOIN Recipe ON Likes.recipeID = Recipe.id
             WHERE Recipe.userID = ?";
$likesStmt = $conn->prepare($likesSQL);
$likesStmt->bind_param("i", $userID);
$likesStmt->execute();
$likesResult = $likesStmt->get_result();
$likesRow = $likesResult->fetch_assoc();
$totalLikes = $likesRow['totalLikes'];

// requirement 6d: get all categories from the database for the filter form
$catSQL = "SELECT id, categoryName FROM RecipeCategory";
$catResult = $conn->query($catSQL);
$categories = [];
while ($cat = $catResult->fetch_assoc()) {
    $categories[] = $cat;
}

// requirement 6d/6e: decide if GET or POST, then get recipes accordingly
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['categoryID']) && $_POST['categoryID'] != "") {
    // POST with a specific category selected: filter by it
    $selectedCategoryID = $_POST['categoryID'];

    $recipeSQL = "SELECT Recipe.id, Recipe.name, Recipe.photoFileName AS recipePhoto,
                         User.firstName, User.lastName, User.photoFileName AS userPhoto,
                         RecipeCategory.categoryName,
                         COUNT(Likes.userID) AS totalLikes
                  FROM Recipe
                  JOIN User ON Recipe.userID = User.id
                  JOIN RecipeCategory ON Recipe.categoryID = RecipeCategory.id
                  LEFT JOIN Likes ON Recipe.id = Likes.recipeID
                  WHERE Recipe.categoryID = ?
                  GROUP BY Recipe.id";
    $recipeStmt = $conn->prepare($recipeSQL);
    $recipeStmt->bind_param("i", $selectedCategoryID);
    $recipeStmt->execute();
    $recipes = $recipeStmt->get_result();

} else {
    // GET or POST with "All Categories" selected: show all recipes
    $selectedCategoryID = "";

    $recipeSQL = "SELECT Recipe.id, Recipe.name, Recipe.photoFileName AS recipePhoto,
                         User.firstName, User.lastName, User.photoFileName AS userPhoto,
                         RecipeCategory.categoryName,
                         COUNT(Likes.userID) AS totalLikes
                  FROM Recipe
                  JOIN User ON Recipe.userID = User.id
                  JOIN RecipeCategory ON Recipe.categoryID = RecipeCategory.id
                  LEFT JOIN Likes ON Recipe.id = Likes.recipeID
                  GROUP BY Recipe.id";
    $recipes = $conn->query($recipeSQL);
}

// requirement 6f: get this user's favourite recipes from the database
$favSQL = "SELECT Recipe.id, Recipe.name, Recipe.photoFileName
           FROM Favourites
           JOIN Recipe ON Favourites.recipeID = Recipe.id
           WHERE Favourites.userID = ?";
$favStmt = $conn->prepare($favSQL);
$favStmt->bind_param("i", $userID);
$favStmt->execute();
$favourites = $favStmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - SweetCrumb</title>
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
    <span class="breadcrumb-item active">User Dashboard</span>
  </div>
</nav>

<div class="container">

  <!-- requirement 6b: welcome note with user's first name from database -->
  <div class="card" style="display:flex; justify-content:space-between; align-items:center;">
    <div class="welcome">Welcome <?php echo $user['firstName']; ?>!</div>
    <a href="process/logout.php" class="link">Sign out</a>
  </div>

  <!-- requirement 6b: user information and photo from database -->
  <div class="card grid">

    <?php if (!empty($user['photoFileName'])) { ?>
      <img src="images/<?php echo $user['photoFileName']; ?>" alt="User Photo" class="avatar">
    <?php } else { ?>
      <!-- default avatar if no photo -->
      <img src="images/default_avatar.png" alt="User Photo" class="avatar">
    <?php } ?>

    <div>
      <div class="section-title">My Information</div>

      <div class="info-row">
        <div class="info-label">Full Name:</div>
        <div><?php echo $user['firstName'] . " " . $user['lastName']; ?></div>
      </div>

      <div class="info-row">
        <div class="info-label">Email:</div>
        <div><?php echo $user['emailAddress']; ?></div>
      </div>
    </div>
  </div>

  <!-- requirement 6c: link to my recipes, show total recipes and total likes from database -->
  <div class="card" style="display:flex; justify-content:space-between; align-items:center;">
    <a href="Myrecipes.php" class="btn btn-primary">My Recipes</a>
    <div class="muted">
      Total recipes: <strong><?php echo $totalRecipes; ?></strong>
      &nbsp;|&nbsp;
      Total likes: <strong><?php echo $totalLikes; ?></strong>
    </div>
  </div>

  <!-- requirement 6d + 6e: filter form and recipe table -->
  <div class="card">
    <div class="section-title">All Available Recipes</div>

    <!-- requirement 6d: filter form — sends POST to same page (user.php) -->
    <form method="POST" action="user.php" style="display:flex; gap:12px; margin-bottom:1rem;">
      <select name="categoryID">
        <!-- "All Categories" has value="" and is selectable — handled in PHP below -->
        <option value="" <?php if ($selectedCategoryID === "") echo "selected"; ?>>All Categories</option>
        <?php foreach ($categories as $cat) { ?>
          <option value="<?php echo $cat['id']; ?>"
            <?php if ($selectedCategoryID == $cat['id']) echo "selected"; ?>>
            <?php echo $cat['categoryName']; ?>
          </option>
        <?php } ?>
      </select>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- requirement 6e: recipes table -->
    <?php if ($recipes->num_rows == 0) { ?>

      <!-- requirement 6e: no recipes message when filter returns nothing -->
      <p style="text-align:center; padding:2rem; color:#a0a0a0;">
        No recipes found in this category.
      </p>

    <?php } else { ?>

      <table>
        <thead>
          <tr>
            <th>Recipe</th>
            <th>Photo</th>
            <th>Creator</th>
            <th>Likes</th>
            <th>Category</th>
          </tr>
        </thead>
        <tbody>

          <?php while ($recipe = $recipes->fetch_assoc()) { ?>
            <tr>
              <!-- requirement 6e: recipe name is a code-generated link to ViewRecipe.php with the recipe id -->
              <td>
                <a href="ViewRecipe.php?id=<?php echo $recipe['id']; ?>">
                  <?php echo $recipe['name']; ?>
                </a>
              </td>

              <td>
                <?php if (!empty($recipe['recipePhoto'])) { ?>
                  <img src="images/<?php echo $recipe['recipePhoto']; ?>"
                       class="recipe-photo"
                       onerror="this.style.display='none'">
                <?php } ?>
              </td>

              <td>
                <div style="display:flex; align-items:center; gap:8px;">
                  <img src="images/<?php echo $recipe['userPhoto']; ?>" class="creator-photo">
                  <span><?php echo $recipe['firstName'] . " " . $recipe['lastName']; ?></span>
                </div>
              </td>

              <!-- requirement 6e: total likes counted from the database -->
              <td><?php echo $recipe['totalLikes']; ?></td>

              <td><?php echo $recipe['categoryName']; ?></td>
            </tr>
          <?php } ?>

        </tbody>
      </table>

    <?php } ?>
  </div>

  <!-- requirement 6f: favourites from the database -->
  <div class="card">
    <div class="section-title">My Favourite Recipes</div>

    <?php if ($favourites->num_rows == 0) { ?>

      <!-- requirement 6f: message when no favourites -->
      <p style="text-align:center; padding:2rem; color:#a0a0a0;">
        You have no favourite recipes yet.
      </p>

    <?php } else { ?>

      <table>
        <thead>
          <tr>
            <th>Recipe</th>
            <th>Photo</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>

          <?php while ($fav = $favourites->fetch_assoc()) { ?>
            <tr>
              <!-- requirement 6f: recipe name is a code-generated link to ViewRecipe.php -->
              <td>
                <a href="ViewRecipe.php?id=<?php echo $fav['id']; ?>">
                  <?php echo $fav['name']; ?>
                </a>
              </td>

              <td>
                <?php if (!empty($fav['photoFileName'])) { ?>
                  <img src="images/<?php echo $fav['photoFileName']; ?>"
                       class="recipe-photo"
                       onerror="this.style.display='none'">
                <?php } ?>
              </td>

              <!-- requirement 6f: remove link goes to a PHP page that deletes from favourites -->
              <td>
                <a href="process/remove_favourite.php?recipeID=<?php echo $fav['id']; ?>"
                   style="color:red;"
                   onclick="return confirm('Remove this recipe from favourites?');">
                  Remove
                </a>
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