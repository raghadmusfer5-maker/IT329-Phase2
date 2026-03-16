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
 
<?php require_once 'includes/header.php'; ?>
 
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
 
<?php require_once 'includes/footer.php'; ?>
 
</body>
</html>
 
