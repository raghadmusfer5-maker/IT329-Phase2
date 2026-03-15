<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once(__DIR__ . "/config/db.php");
 
// ── 11a. Check admin ──────────────────────────────────────────────────────────
if (!isset($_SESSION['userID']) || $_SESSION['userType'] != "admin") {
    header("Location: login.php?error=Access+denied.+Admins+only.");
    exit();
}
 
// ── 11b. Get admin info ───────────────────────────────────────────────────────
$adminID = $_SESSION['userID'];
 
$stmt = $conn->prepare("SELECT firstName, lastName, emailAddress FROM user WHERE id = ?");
$stmt->bind_param("i", $adminID);
$stmt->execute();
$stmt->bind_result($firstName, $lastName, $adminEmail);
$stmt->fetch();
$stmt->close();
 
$adminName = $firstName ;
 
// ── 11c. Get all recipe reports ───────────────────────────────────────────────
// recipe columns: id, userID, categoryID, name, photoFileName
// report columns: id, userID, recipeID
$reportsResult = $conn->query("
    SELECT 
        r.id                AS reportID,
        r.recipeID,
        rec.name            AS recipeTitle,
        rec.photoFileName   AS recipePhoto,
        u.id                AS creatorID,
        u.firstName,
        u.lastName,
        u.photoFileName     AS creatorPhoto
    FROM report r
    JOIN recipe rec ON r.recipeID = rec.id
    JOIN user u     ON rec.userID = u.id
    ORDER BY r.id DESC
");
 
// ── 11d. Get blocked users ────────────────────────────────────────────────────
// blockeduser columns: id, firstName, lastName, emailAddress
$blockedResult = $conn->query("
    SELECT firstName, lastName, emailAddress 
    FROM blockeduser 
    ORDER BY firstName
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SweetCrumb - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-page">
 
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
            <ellipse cx="18" cy="20" rx="2.5" ry="2.8" fill="#5d3a1a"/>
            <ellipse cx="31" cy="21" rx="2.2" ry="2.5" fill="#5d3a1a"/>
            <ellipse cx="23" cy="31" rx="2.4" ry="2.7" fill="#5d3a1a"/>
            <ellipse cx="15" cy="28" rx="1.8" ry="2"   fill="#5d3a1a"/>
            <ellipse cx="33" cy="31" rx="2"   ry="2.3" fill="#5d3a1a"/>
            <ellipse cx="26" cy="17" rx="1.9" ry="2.1" fill="#5d3a1a"/>
            <circle cx="22" cy="18" r="4" fill="#fff" opacity="0.25"/>
          </g>
          <text x="70" y="50" font-family="Georgia,'Times New Roman',serif"
                font-size="36" font-weight="700" letter-spacing="0.5"
                filter="url(#shadow)" fill="url(#textGradient)">Sweet</text>
          <text x="185" y="50" font-family="Georgia,'Times New Roman',serif"
                font-size="36" font-weight="700" letter-spacing="0.5"
                filter="url(#shadow)" fill="#d4a5a5">Crumb</text>
          <path d="M 72 55 Q 140 58, 240 55" stroke="#e9bdbd" stroke-width="1.5" fill="none" opacity="0.5"/>
          <g transform="translate(70, 60)">
            <text x="12" y="9" font-family="'Segoe UI',Arial,sans-serif"
                  font-size="11" fill="#d4a5a5" letter-spacing="3" font-weight="500" opacity="0.9">
              HEALTHY BAKERY
            </text>
          </g>
        </svg>
    </header>
 
    <nav class="breadcrumb">
        <div class="breadcrumb-container">
            <span class="breadcrumb-item"><a href="index.php">Home</a></span>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-item active">Admin Dashboard</span>
        </div>
    </nav>
 
    <main>
        <div class="container">
 
            <!-- Welcome + Sign out -->
            <div class="card" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="welcome">Welcome <?php echo htmlspecialchars($adminName); ?>!</div>
                <a href="process/logout.php" class="link">Sign out</a>
            </div>
 
            <!-- 11b. Admin Information -->
            <div class="card">
                <div class="section-title">My Information</div>
                <div class="info-row">
                    <div class="info-label">Full Name:</div>
                    <div><?php echo htmlspecialchars($adminName); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div><?php echo htmlspecialchars($adminEmail); ?></div>
                </div>
            </div>
 
            <!-- 11c. Reported Recipes -->
            <div class="card admin-table">
                <div class="section-title">Reported Recipes</div>
 
                <?php if ($reportsResult && $reportsResult->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Recipe Name</th>
                            <th>Recipe Creator</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $reportsResult->fetch_assoc()):
                        $creatorFullName = $row['firstName'] . ' ' . $row['lastName'];
                    ?>
                        <tr>
                            <td>
                                <a href="ViewRecipe.php?id=<?php echo $row['recipeID']; ?>">
                                    <?php echo htmlspecialchars($row['recipeTitle']); ?>
                                </a>
                                <?php if (!empty($row['recipePhoto'])): ?>
                                    <img src="images/<?php echo htmlspecialchars($row['recipePhoto']); ?>"
                                         class="recipe-photo" alt="recipe">
                                <?php endif; ?>
                            </td>
 
                            <td class="creator">
                                <?php if (!empty($row['creatorPhoto'])): ?>
                                    <img src="images/<?php echo htmlspecialchars($row['creatorPhoto']); ?>"
                                         class="creator-photo" alt="creator">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($creatorFullName); ?>
                            </td>
 
                            <td>
                                <form method="POST" action="process/handle_report.php">
                                    <input type="hidden" name="recipeID"  value="<?php echo $row['recipeID'];  ?>">
                                    <input type="hidden" name="creatorID" value="<?php echo $row['creatorID']; ?>">
                                    <input type="hidden" name="reportID"  value="<?php echo $row['reportID'];  ?>">
 
                                    <label style="display:flex; align-items:center; gap:10px;">
                                        <input type="radio" name="action" value="dismiss" checked>
                                        Dismiss Report
                                    </label>
                                    <br>
                                    <label style="display:flex; align-items:center; gap:10px;">
                                        <input type="radio" name="action" value="block">
                                        Block User
                                    </label>
                                    <br><br>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
 
                <?php else: ?>
                    <p>No reported recipes at the moment.</p>
                <?php endif; ?>
            </div>
 
            <!-- 11d. Blocked Users -->
            <div class="card">
                <div class="section-title">Blocked Users List</div>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email Address</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($blockedResult && $blockedResult->num_rows > 0):
                        while ($b = $blockedResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($b['firstName'] . ' ' . $b['lastName']); ?></td>
                            <td><?php echo htmlspecialchars($b['emailAddress']); ?></td>
                        </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="2">No blocked users.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
 
        </div>
    </main>
 
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
<?php $conn->close(); ?>