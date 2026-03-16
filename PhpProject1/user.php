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

<?php require_once 'includes/header.php'; ?>

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

<?php require_once 'includes/footer.php'; ?>

</body>
</html>