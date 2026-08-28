<?php require 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MoGraphics Clothing</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header id="header">
  <nav>
    <div class="nav-left">
      <a href="index.php">Home</a>
      <a href="shop.php">Shop</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>
    </div>

    <a class="logo" href="index.php">MoGraphics</a>

    <div class="nav-right">
      <a href="login.php" aria-label="Profile"><i class="bi bi-person"></i></a>
      <a href="cart.php" aria-label="Cart"><i class="bi bi-bag"></i></a>
    </div>
  </nav>
</header>

<section class="hero">
  <video autoplay muted loop playsinline>
    <source src="assets/video/hero.mp4" type="video/mp4">
  </video>
  <div class="overlay"></div>
  <div class="hero-copy">
    <p>MO GRAPHICS / NEW SEASON</p>
    <h1>WEAR THE<br>GRAPHIC.</h1>
    <span>Modern streetwear for bold, confident lifestyles.</span>
    <a class="pill light" href="shop.php">Explore Collection</a>
  </div>
</section>

<section class="intro" id="about">
  <div class="running-text">MOGRAPHICS CREATES MODERN FASHION FOR BOLD AND CONFIDENT LIFESTYLES</div>
  <div class="intro-grid">
    <div>
      <span class="num">01.</span>
      <h2>DESIGNED<br>TO BE SEEN.</h2>
    </div>
    <div class="editorial-box"><img src="assets/images/Knit.jpg" alt="MoGraphics editorial fashion"></div>
    <p>MoGraphics combines graphic culture, clean silhouettes and everyday comfort into clothing that stands out.</p>
  </div>
</section>

<section class="cyan">
  <div class="section-head">
    <div>
      <span class="num">02.</span>
      <h2>SHOP BY<br>SIGNATURE STYLE</h2>
    </div>
    <a class="pill lime" href="shop.php">Explore Collection</a>
  </div>

  <div class="cards">
    <a href="shop.php?category=1" class="card">
      <div class="product-art"><img src="assets/images/Shirts.png" alt="MoGraphics graphic tees"></div>
      <h3>GRAPHIC TEES</h3><p>Everyday statements.</p>
    </a>
    <a href="shop.php?category=2" class="card">
      <div class="product-art second"><img src="assets/images/Hoodies.jpg" alt="MoGraphics hoodies"></div>
      <h3>HOODIES</h3><p>Heavyweight comfort.</p>
    </a>
    <a href="shop.php?category=3" class="card">
      <div class="product-art third"><img src="assets/images/Jackets.jpg" alt="MoGraphics outerwear"></div>
      <h3>OUTERWEAR</h3><p>Built for the season.</p>
    </a>
  </div>
</section>

<section class="curate">
  <div class="model-placeholder"><img src="assets/images/Curated.jpg" alt="MoGraphics editorial model"></div>
  <div>
    <span class="num">03.</span>
    <h2>HOW WE CURATE<br>YOUR LOOK</h2>
    <div class="lime-panel">
      <div><b>01.</b><span>Graphic-led silhouettes</span></div>
      <div><b>02.</b><span>Clean colour stories</span></div>
      <div><b>03.</b><span>Seasonal essentials</span></div>
    </div>
  </div>
</section>

<section class="drops">
  <div class="section-head">
    <div><span class="num">04.</span><h2>THIS SEASON'S DROPS</h2></div>
    <a href="shop.php">Shop all ↗</a>
  </div>
  <div class="cards">
    <?php
    $q=$pdo->query("SELECT p.product_id,p.name,p.image,MIN(v.price) price
                    FROM products p LEFT JOIN product_variants v ON p.product_id=v.product_id
                    WHERE p.is_active=1 GROUP BY p.product_id LIMIT 4");
    foreach($q as $p):
    ?>
    <a class="product-card" href="product.php?id=<?= $p['product_id'] ?>">
      <div class="product-photo">
        <?php if($p['image']): ?><img src="<?= htmlspecialchars($p['image']) ?>" alt=""><?php endif; ?>
      </div>
      <div class="product-row"><h3><?= htmlspecialchars($p['name']) ?></h3><span>R <?= number_format($p['price'],2) ?></span></div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<footer id="contact">
  <div><h2>MoGraphics</h2><p>Modern fashion. Bold graphics.</p></div>
  <div><b>Explore</b><a href="shop.php">Shop</a><a href="login.php">Account</a><a href="cart.php">Cart</a></div>
  <div><b>Contact</b><p>info@mographics.co.za</p><p>South Africa</p></div>
</footer>

<script src="assets/js/app.js"></script>
</body>
</html>