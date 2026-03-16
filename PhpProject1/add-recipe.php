<?php
session_start();
require_once("config/db.php");
 
// if not logged in, go to login page
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
 
// get all categories from the database to show in the dropdown
$catSQL  = "SELECT id, categoryName FROM RecipeCategory";
$catResult = $conn->query($catSQL);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Recipe</title>
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
      <span class="breadcrumb-item"><a href="Myrecipes.php">My Recipes</a></span>
      <span class="breadcrumb-separator">›</span>
      <span class="breadcrumb-item active">Add Recipe</span>
    </div>
  </nav>
 
  <div class="page-head">
    <h2>Add New Recipe</h2>
    <p class="small">Here you can add a new recipe. All fields are required except the video.</p>
  </div>
 
  <div class="page">
 
    <!-- the form sends data to add_recipe_process.php using POST -->
    <!-- enctype is needed because we are uploading files (photo/video) -->
    <form id="addForm" action="process/add_recipe_process.php" method="POST" enctype="multipart/form-data">
 
      <label>Recipe Name *</label>
      <input type="text" name="name" placeholder="Enter your recipe name." required>
 
      <label>Category *</label>
      <select name="categoryID" required>
        <option value="" disabled selected>Choose</option>
 
        <?php while ($cat = $catResult->fetch_assoc()) { ?>
          <!-- each option's value is the category ID from the database -->
          <option value="<?php echo $cat['id']; ?>">
            <?php echo $cat['categoryName']; ?>
          </option>
        <?php } ?>
 
      </select>
 
      <label>Description *</label>
      <textarea name="description" rows="4" placeholder="Describe your recipe in a few sentences." required></textarea>
 
      <label>Photo *</label>
      <input type="file" name="photo" accept="image/*" required>
 
      <div class="box">
        <h3>Ingredients *</h3>
        <div id="ingredientsContainer"></div>
        <button type="button" class="btn btn-primary" id="addIngredientBtn">+ Add Ingredient</button>
        <div class="smaller">Each ingredient should have name + quantity.</div>
      </div>
 
      <div class="box">
        <h3>Instructions *</h3>
        <div id="stepsContainer"></div>
        <button type="button" class="btn btn-primary" id="addStepBtn">+ Add Step</button>
        <div class="smaller">Add as many steps as you need.</div>
      </div>
 
      <div class="box">
        <h3>Video (Optional)</h3>
        <label>Upload Video</label>
        <input type="file" name="video" accept="video/*">
      </div>
 
      <div class="inline">
        <button class="btn btn-primary" type="submit">Save Recipe</button>
        <button class="btn btn-brown" type="button" id="cancelBtn">Cancel</button>
      </div>
 
    </form>
  </div>
 
  <?php require_once 'includes/footer.php'; ?>
 
<script>
  var ingredientsContainer = document.getElementById("ingredientsContainer");
  var stepsContainer = document.getElementById("stepsContainer");
 
  function refreshRemove(container) {
    var rows = container.children;
    for (var i = 0; i < rows.length; i++) {
      var removeBtn = rows[i].querySelector(".remove-btn");
      if (removeBtn) {
        if (i === 0) {
          removeBtn.style.display = "none";
        } else {
          removeBtn.style.display = "inline-block";
        }
      }
    }
  }
 
  function addIngredient(name, qty) {
    if (name === undefined) name = "";
    if (qty === undefined) qty = "";
 
    var row = document.createElement("div");
    row.className = "form-row";
 
    var nameInput = document.createElement("input");
    nameInput.type = "text";
    nameInput.placeholder = "Ingredient name";
    nameInput.value = name;
    nameInput.required = true;
    // name attribute is an array so PHP can read all ingredients
    nameInput.name = "ingredientName[]";
 
    var qtyInput = document.createElement("input");
    qtyInput.type = "text";
    qtyInput.placeholder = "Quantity (e.g., 2 cups)";
    qtyInput.value = qty;
    qtyInput.required = true;
    // same here, array so PHP gets all quantities
    qtyInput.name = "ingredientQty[]";
 
    var removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.className = "remove-btn";
    removeBtn.textContent = "Remove";
    removeBtn.onclick = function() {
      row.remove();
      refreshRemove(ingredientsContainer);
    };
 
    row.appendChild(nameInput);
    row.appendChild(qtyInput);
    row.appendChild(removeBtn);
    ingredientsContainer.appendChild(row);
    refreshRemove(ingredientsContainer);
  }
 
  function addStep(text) {
    if (text === undefined) text = "";
 
    var row = document.createElement("div");
    row.className = "form-row onecol";
 
    var textInput = document.createElement("input");
    textInput.type = "text";
    textInput.placeholder = "Step (e.g., Mix ingredients...)";
    textInput.value = text;
    textInput.required = true;
    // array so PHP gets all steps
    textInput.name = "steps[]";
 
    var removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.className = "remove-btn";
    removeBtn.textContent = "Remove";
    removeBtn.onclick = function() {
      row.remove();
      refreshRemove(stepsContainer);
    };
 
    row.appendChild(textInput);
    row.appendChild(removeBtn);
    stepsContainer.appendChild(row);
    refreshRemove(stepsContainer);
  }
 
  // start with one ingredient row and one step row
  addIngredient();
  addStep();
 
  document.getElementById("addIngredientBtn").onclick = function() { addIngredient(); };
  document.getElementById("addStepBtn").onclick = function() { addStep(); };
  document.getElementById("cancelBtn").onclick = function() { window.location.href = "Myrecipes.php"; };
</script>
 
</body>
</html>