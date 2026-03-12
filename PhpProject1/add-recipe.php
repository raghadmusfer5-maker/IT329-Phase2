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