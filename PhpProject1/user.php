<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>User Page</title>
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
  
  <!-- Decorative background circle behind cookie -->
  <circle cx="35" cy="40" r="24" fill="#d4a5a5" opacity="0.12"/>
  
  <!-- Cookie with chocolate chips -->
  <g transform="translate(10, 15)" filter="url(#shadow)">
    <!-- Cookie base - light brown with slight gradient -->
    <circle cx="25" cy="25" r="16" fill="#d4a574"/>
    <circle cx="25" cy="25" r="16" fill="url(#textGradient)" opacity="0.15"/>
    
    <!-- Cookie texture/bumpy edge -->
    <circle cx="25" cy="25" r="16" fill="none" stroke="#c49563" stroke-width="1" opacity="0.6"/>
    <circle cx="20" cy="14" r="2" fill="#c49563" opacity="0.4"/>
    <circle cx="35" cy="18" r="1.5" fill="#c49563" opacity="0.4"/>
    <circle cx="32" cy="32" r="2" fill="#c49563" opacity="0.4"/>
    
    <!-- Chocolate chips with depth - spread out naturally -->
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
    
    <!-- Highlight for dimension -->
    <circle cx="22" cy="18" r="4" fill="#fff" opacity="0.25"/>
  </g>
  
  <!-- Main text: Sweet -->
  <text x="70" y="50" font-family="Georgia, 'Times New Roman', serif" 
        font-size="36" font-weight="700" letter-spacing="0.5" filter="url(#shadow)" fill="url(#textGradient)">Sweet</text>
  
  <!-- Main text: Crumb (with the b!) -->
  <text x="185" y="50" font-family="Georgia, 'Times New Roman', serif" 
        font-size="36" font-weight="700" letter-spacing="0.5" filter="url(#shadow)" fill="#d4a5a5">Crumb</text>
  
  <!-- Decorative underline swoosh -->
  <path d="M 72 55 Q 140 58, 240 55" stroke="#e9bdbd" stroke-width="1.5" fill="none" opacity="0.5"/>
  
  <!-- Tagline with icon -->
  <g transform="translate(70, 60)">
    <!-- Small wheat icon -->
    <g transform="scale(0.7)" opacity="0.6">
      <line x1="4" y1="0" x2="4" y2="12" stroke="#d4a5a5" stroke-width="1.2"/>
      <ellipse cx="4" cy="2" rx="2.5" ry="1.8" fill="#d4a5a5"/>
      <ellipse cx="4" cy="5" rx="2.8" ry="2" fill="#d4a5a5"/>
      <ellipse cx="4" cy="8" rx="2.5" ry="1.8" fill="#d4a5a5"/>
    </g>
    
    <text x="12" y="9" font-family="'Segoe UI', Arial, sans-serif" 
          font-size="11" fill="#d4a5a5" letter-spacing="3" font-weight="500" opacity="0.9">
      HEALTHY BAKERY
    </text>
  </g>
  
  <!-- Decorative dots accent -->
  <circle cx="285" cy="25" r="2" fill="#e9bdbd" opacity="0.4"/>
  <circle cx="293" cy="28" r="1.5" fill="#d4a5a5" opacity="0.4"/>
  <circle cx="300" cy="25" r="2.5" fill="#e9bec2" opacity="0.4"/>
</svg>
    </header>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
        <div class="breadcrumb-container">
            <span class="breadcrumb-item">
                <a href="Home.html">Home</a>
            </span>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-item active">User Dashboard</span>
        </div>
    </nav>
    
<div class="container">

  <!-- Welcome -->
  <div class="card" style="display:flex; justify-content:space-between; align-items:center;">
    <div class="welcome">Welcome Sara! </div>
    <a href="Home.html" class="link">Sign out</a>
  </div>

  <!-- User Information -->
  <div class="card grid">
    <img src="images/sara.jpg" alt="User Photo" class="avatar">

    <div>
      <div class="section-title">My Information</div>

      <div class="info-row">
        <div class="info-label">Full Name:</div>
        <div>Sara Ahmed</div>
      </div>

      

      <div class="info-row">
        <div class="info-label">Email:</div>
        <div>sara@email.com</div>
      </div>


      
    </div>
  </div>

  <!-- My Recipes Summary -->
  <div class="card" style="display:flex; justify-content:space-between; align-items:center;">
    <a href="Myrecipe.html" class="btn btn-primary">My Recipes</a>
    <div class="muted">
      Total recipes: <strong>1</strong> | Total likes: <strong>40</strong>
    </div>
  </div>

  <!-- All Available Recipes -->
  <div class="card">
    <div class="section-title">All Available Recipes</div>

    <div style="display:flex; gap:12px; margin-bottom:1rem;">
      <select >
        <option>All Categories</option>
        <option>Lactose Free Cakes</option>
        <option>Sugar Free Desserts</option>
        <option>Healthy Bakery</option>
      </select>
      <button class="btn btn-primary" disabled>Filter</button>
    </div>

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
        <tr>
          <td><a href="ViewRecipe.html">Lactose Free Chocolate Cake</a></td>
          <td><img src="images/ch cake.jpg" class="recipe-photo"></td>
          <td class="creator">
            <img src="images/maya.jpg" class="creator-photo">
            Maya khalid
          </td>
          <td>40</td>
          <td>Lactose Free Cakes</td>
        </tr>

        <tr>
          <td><a href="ViewRecipe.html">Pumpkin pie</a></td>
          <td><img src="images/pie.jpg" class="recipe-photo"></td>
          <td class="creator">
            <img src="images/fahad.jpg" class="creator-photo">
            Fahad Alotaibi
          </td>
          <td>25</td>
          <td>Sugar Free Desserts</td>
        </tr>

        <tr>
          <td><a href="ViewRecipe.html">Birthday cookie</a></td>
          <td><img src="images/cookie.jpg" class="recipe-photo"></td>
          <td class="creator">
            <img src="images/raneem.jpg" class="creator-photo">
            Raneem Saleh
          </td>
          <td>20</td>
          <td>Healthy Bakery</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Favourite Recipes -->
  <div class="card">
    <div class="section-title">My Favourite Recipes </div>

    <table>
      <thead>
        <tr>
          <th>Recipe</th>
          <th>Photo</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td><a href="ViewRecipe.html">Birthday cookie</a></td>
          <td><img src="images/cookie.jpg" class="recipe-photo"></td>
          <td><a href="#">Remove</a></td>
        </tr>

        <tr>
          <td><a href="ViewRecipe.html">Lactose Free Chocolate Cake</a></td>
          <td><img src="images/ch cake.jpg" class="recipe-photo"></td>
          <td><a href="#">Remove</a></td>
        </tr>
      </tbody>
    </table>
  </div>

</div>
 <footer>
        <p>&copy; 2026 SweetCrumb. Baking dreams come true.</p>

        <div class="social-links">
            <a href="https://instagram.com/sweetcrumb" class="social" aria-label="Instagram">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                   fill="currentColor" viewBox="0 0 24 24">
                <path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 5a5 5 0 100 10 5 5 0 000-10zm6-.8a1 1 0 100 2 1 1 0 000-2z"/>
              </svg>
            </a>

            <a href="https://twitter.com/sweetcrumb" class="social" aria-label="X">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                   fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.6 2.3h2.4l-5.2 6 6.1 8h-4.8l-3.6-4.8-4.1 4.8H3.8l5.6-6.4L3.6 2.3H8l3.2 4.4z"/>
              </svg>
            </a>

            <a href="mailto:SweetCrumb@gmail.com" class="social" aria-label="Email">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                   fill="currentColor" viewBox="0 0 24 24">
                <path d="M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm0 2l8 5 8-5"/>
              </svg>
            </a>

            <a href="tel:+966550886681" class="social" aria-label="Phone">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                   fill="currentColor" viewBox="0 0 24 24">
                <path d="M6.6 10.8a15 15 0 006.6 6.6l2.2-2.2a1 1 0 011.1-.2c1.2.5 2.5.8 3.9.8a1 1 0 011 1V20a1 1 0 01-1 1C12.4 21 3 11.6 3 1a1 1 0 011-1h3.2a1 1 0 011 1c0 1.4.3 2.7.8 3.9.1.4 0 .8-.2 1.1l-2.2 2.2z"/>
              </svg>
            </a>
        </div>
    </footer>
</body>
</html>