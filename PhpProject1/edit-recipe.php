<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: Myrecipes.php");
    exit();
}

$userID = $_SESSION['userID'];
$recipeID = (int)$_GET['id'];

/* get recipe info and make sure it belongs to the logged-in user */
$sql = "SELECT id, name, description, categoryID, photoFileName, videoFilePath
        FROM Recipe
        WHERE id = ? AND userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipeID, $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: Myrecipes.php");
    exit();
}

$recipe = $result->fetch_assoc();

/* get all categories */
$catSQL = "SELECT id, categoryName FROM RecipeCategory";
$catResult = $conn->query($catSQL);

/* get ingredients */
$ingredients = [];
$ingSQL = "SELECT ingredientName, ingredientQuantity
           FROM Ingredients
           WHERE recipeID = ?";
$ingStmt = $conn->prepare($ingSQL);
$ingStmt->bind_param("i", $recipeID);
$ingStmt->execute();
$ingResult = $ingStmt->get_result();

while ($row = $ingResult->fetch_assoc()) {
    $ingredients[] = $row;
}

/* get instructions */
$steps = [];
$stepSQL = "SELECT step
            FROM Instructions
            WHERE recipeID = ?
            ORDER BY stepOrder ASC";
$stepStmt = $conn->prepare($stepSQL);
$stepStmt->bind_param("i", $recipeID);
$stepStmt->execute();
$stepResult = $stepStmt->get_result();

while ($row = $stepResult->fetch_assoc()) {
    $steps[] = $row['step'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'includes/header.php'; ?>

    <nav class="breadcrumb">
    <div class="breadcrumb-container">
      <span class="breadcrumb-item"><a href="index.php">Home</a></span>
      <span class="breadcrumb-separator">›</span>
      <span class="breadcrumb-item"><a href="user.php">User Dashboard</a></span>
      <span class="breadcrumb-separator">›</span>
      <span class="breadcrumb-item"><a href="Myrecipes.php">My Recipes</a></span>
      <span class="breadcrumb-separator">›</span>
      <span class="breadcrumb-item active">Edit Recipe</span>
    </div>
  </nav>
    
<div class="page-head">
    <h2>Edit Recipe</h2>
    <p class="small">Update your recipe information below.</p>
</div>

<div class="page">
    <form action="process/update_recipe_process.php" method="POST" enctype="multipart/form-data">
        
        <!-- hidden recipe id -->
        <input type="hidden" name="recipeID" value="<?php echo $recipe['id']; ?>">

        <label>Recipe Name *</label>
        <input
            type="text"
            name="name"
            value="<?php echo htmlspecialchars($recipe['name']); ?>"
            required
        >

        <label>Category *</label>
        <select name="categoryID" required>
            <option value="" disabled>Choose</option>
            <?php while ($cat = $catResult->fetch_assoc()) { ?>
                <option value="<?php echo $cat['id']; ?>"
                    <?php if ($cat['id'] == $recipe['categoryID']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($cat['categoryName']); ?>
                </option>
            <?php } ?>
        </select>

        <label>Description *</label>
        <textarea name="description" rows="4" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>

        <div class="box">
            <h3>Recipe Photo</h3>

            <?php if (!empty($recipe['photoFileName'])) { ?>
                <p class="photo-hint">Current Photo:</p>
                <img
                    src="images/<?php echo htmlspecialchars($recipe['photoFileName']); ?>"
                    alt="Current Photo"
                    style="width:220px; height:140px; object-fit:cover; border-radius:10px; margin-bottom:12px;"
                >
            <?php } ?>

            <label>Upload New Photo</label>
            <input type="file" name="photo" accept="image/*">
            <p class="photo-hint">If you do not upload a new photo, the old photo will remain.</p>
        </div>

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

            <?php if (!empty($recipe['videoFilePath'])) { ?>
                <p class="photo-hint">
                    Current Video:
                    <a href="<?php echo htmlspecialchars($recipe['videoFilePath']); ?>" target="_blank">Open current video</a>
                </p>
            <?php } else { ?>
                <p class="photo-hint">No current video.</p>
            <?php } ?>

            <label>Upload New Video</label>
            <input type="file" name="video" accept="video/*">

            <label>Or Video URL</label>
            <input
                type="url"
                name="videoURL"
                placeholder="https://..."
                value="<?php echo (!empty($recipe['videoFilePath']) && filter_var($recipe['videoFilePath'], FILTER_VALIDATE_URL)) ? htmlspecialchars($recipe['videoFilePath']) : ''; ?>"
            >

            <p class="photo-hint">If you do not upload a new video or enter a new URL, the old video remains.</p>
        </div>

        <div class="inline">
            <button class="btn btn-primary" type="submit">Update Recipe</button>
            <a href="Myrecipes.php" class="btn btn-brown">Cancel</a>
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
                removeBtn.style.display = rows.length === 1 ? "none" : "inline-block";
            }
        }
    }

    function addIngredient(name = "", qty = "") {
        var row = document.createElement("div");
        row.className = "form-row";

        var nameInput = document.createElement("input");
        nameInput.type = "text";
        nameInput.name = "ingredientName[]";
        nameInput.placeholder = "Ingredient name";
        nameInput.value = name;
        nameInput.required = true;

        var qtyInput = document.createElement("input");
        qtyInput.type = "text";
        qtyInput.name = "ingredientQty[]";
        qtyInput.placeholder = "Quantity";
        qtyInput.value = qty;
        qtyInput.required = true;

        var removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.className = "remove-btn";
        removeBtn.textContent = "Remove";
        removeBtn.onclick = function () {
            row.remove();
            refreshRemove(ingredientsContainer);
        };

        row.appendChild(nameInput);
        row.appendChild(qtyInput);
        row.appendChild(removeBtn);
        ingredientsContainer.appendChild(row);
        refreshRemove(ingredientsContainer);
    }

    function addStep(text = "") {
        var row = document.createElement("div");
        row.className = "form-row onecol";

        var stepInput = document.createElement("input");
        stepInput.type = "text";
        stepInput.name = "step[]";
        stepInput.placeholder = "Write the step";
        stepInput.value = text;
        stepInput.required = true;

        var removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.className = "remove-btn";
        removeBtn.textContent = "Remove";
        removeBtn.onclick = function () {
            row.remove();
            refreshRemove(stepsContainer);
        };

        row.appendChild(stepInput);
        row.appendChild(removeBtn);
        stepsContainer.appendChild(row);
        refreshRemove(stepsContainer);
    }

    <?php if (!empty($ingredients)) { ?>
        <?php foreach ($ingredients as $ing) { ?>
            addIngredient(
                <?php echo json_encode($ing['ingredientName']); ?>,
                <?php echo json_encode($ing['ingredientQuantity']); ?>
            );
        <?php } ?>
    <?php } else { ?>
        addIngredient();
    <?php } ?>

    <?php if (!empty($steps)) { ?>
        <?php foreach ($steps as $step) { ?>
            addStep(<?php echo json_encode($step); ?>);
        <?php } ?>
    <?php } else { ?>
        addStep();
    <?php } ?>

    document.getElementById("addIngredientBtn").onclick = function () {
        addIngredient();
    };

    document.getElementById("addStepBtn").onclick = function () {
        addStep();
    };
</script>

</body>
</html>