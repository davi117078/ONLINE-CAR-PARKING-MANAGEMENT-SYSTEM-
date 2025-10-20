<?php
require_once __DIR__ . '/../src/auth.php';
$user = current_user();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>ParkFinder - Smart Parking System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    /* --- Global Styles & Variables --- */
:root {
    --primary-color: #007bff; /* A modern, vibrant blue */
    --dark-color: #343a40;
    --light-color: #f8f9fa;
    --text-color: #333;
    --white-color: #fff;
    --grey-color: #6c757d;
    --border-radius: 8px;
    --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    line-height: 1.6;
    color: var(--text-color);
    background-color: var(--white-color);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

a {
    text-decoration: none;
    color: var(--primary-color);
    transition: color 0.3s ease;
}

a:hover {
    color: #0056b3;
}

h1, h2, h4, h5 {
    margin-top: 0;
    font-weight: 600;
}

i {
    margin-right: 8px; /* Adds space between icons and text */
}

/* --- Navbar --- */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 5%;
    background-color: var(--primary-color);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar-brand {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark-color);
}

.nav-links a {
    margin-left: 20px;
    color: var(--dark-color);
    font-weight: 600;
    padding: 8px 12px;
    border-radius: var(--border-radius);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.nav-links a:hover {
    background-color: var(--light-color);
    color: var(--primary-color);
}

/* --- Hero Section --- */
.hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4rem 5%;
    background: url('../public/assets/images/background3.png') no-repeat center center/cover;
    min-height: 80vh;
}

.hero-text {
    max-width: 50%;
}

.hero-text h1 {
    font-size: 3rem;
    font-weight: 700;
    color: var(--light-color);
    line-height: 1.2;
    margin-bottom: 1rem;
}

.hero-text p {
    font-size: 1.1rem;
    color: var(--light-color);
    margin-bottom: 2rem;
}

.hero img {
    max-width: 45%;
    height: auto;
}

/* --- Buttons --- */
.btn-primary, .btn-light {
    display: inline-block;
    padding: 12px 24px;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background-color: var(--primary-color);
    color: var(--white-color);
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.btn-light {
    background-color: var(--white-color);
    color: var(--primary-color);
}

.btn-light:hover {
    background-color: var(--light-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}


/* --- General Section Styling --- */
.section {
    padding: 4rem 5%;
    text-align: center;
}

/* Alternate background color for sections to create visual separation */
.section:nth-of-type(even) {
    background-color: var(--light-color);
}

.section-title {
    font-size: 2.5rem;
    margin-bottom: 3rem;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background-color: var(--primary-color);
    border-radius: 2px;
}

/* --- Features Section --- */
.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    text-align: left;
}

.feature-card {
    background-color: var(--white-color);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.feature-icon {
    font-size: 2rem;
    width: 60px;
    height: 60px;
    background-color: rgba(0, 123, 255, 0.1);
    color: var(--primary-color);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1.5rem;
}

.feature-icon i {
    margin: 0;
}

.feature-card h4 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.feature-card p {
    color: var(--grey-color);
    margin: 0;
}

/* --- How It Works Section --- */
.steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.step-card {
    position: relative;
    padding: 2rem;
}

.step-icon {
    width: 50px;
    height: 50px;
    background-color: var(--primary-color);
    color: var(--white-color);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 auto 1.5rem auto;
    border: 4px solid var(--white-color); /* for sections with a colored background */
    box-shadow: 0 0 0 4px var(--primary-color);
}

.step-card h5 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

/* --- Testimonial Section --- */
.testimonial {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem;
    background-color: var(--white-color);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}

.testimonial .rating .fa-star {
    color: #ffc107; /* Gold color for stars */
    margin: 0 2px;
}

.testimonial p {
    font-size: 1.1rem;
    color: var(--grey-color);
    margin: 1rem 0;
}

.testimonial strong {
    font-weight: 600;
    color: var(--dark-color);
}

/* --- Call to Action (CTA) Section --- */
.cta {
    background-color: var(--primary-color);
    color: var(--white-color);
    padding: 4rem 5%;
    text-align: center;
}

.cta h2 {
    font-size: 2.2rem;
    margin-bottom: 1rem;
}

.cta p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

/* --- Footer --- */
footer {
    background-color: var(--dark-color);
    color: var(--light-color);
    text-align: center;
    padding: 1.5rem 5%;
}

/* --- Responsive Design --- */
@media (max-width: 992px) {
    .hero {
        flex-direction: column;
        text-align: center;
        padding: 3rem 5%;
    }
    .hero-text {
        max-width: 100%;
        margin-bottom: 2rem;
    }
    .hero img {
        max-width: 70%;
    }
}

@media (max-width: 768px) {
    body { font-size: 16px; }
    .hero-text h1 { font-size: 2.5rem; }
    .section-title { font-size: 2rem; }

    .navbar {
        flex-direction: column;
        padding: 1rem;
    }
    .navbar-brand {
        margin-bottom: 1rem;
    }
    .nav-links a {
        margin: 0 10px;
    }

    .features, .steps {
        grid-template-columns: 1fr; /* Stack cards vertically */
    }

    .feature-card {
        text-align: center;
    }
    .feature-icon {
        margin-left: auto;
        margin-right: auto;
    }
}

@media (max-width: 480px) {
    .nav-links {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    .nav-links a {
        margin: 5px 0;
        width: 80%;
        text-align: center;
    }
    .hero-text h1 {
        font-size: 2rem;
    }
    .hero-text p {
        font-size: 1rem;
    }
}
  </style>
</head>

<body>

  <nav class="navbar">
    <a href="index.php" class="navbar-brand"><i class="fa-solid fa-car-side"></i> ParkFinder</a>
    <div class="nav-links">
      <?php if ($user): ?>
        <a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      <?php else: ?>
        <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
        <a href="register.php"><i class="fa-solid fa-user-plus"></i> Register</a>
      <?php endif; ?>
    </div>
  </nav>

  <section class="hero">
    <div class="hero-text">
      <h1><i class="fa-solid fa-square-parking"></i> Find & Reserve Parking Instantly</h1>
      <p>Search for parking slots by branch, time, or price — reserve your spot easily with ParkFinder.</p>
      <a href="search.php" class="btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Find Parking</a>
    </div>
    <img src="assets/images/parking.svg" alt="Parking Illustration" onerror="this.style.display='none'">
  </section>

  <section class="section">
    <h2 class="section-title">Why Choose ParkFinder?</h2>
    <div class="features">
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
        <h4>Fast & Easy</h4>
        <p>Reserve your parking in just a few clicks with our user-friendly system.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-shield-alt"></i></div>
        <h4>Secure</h4>
        <p>We ensure data safety and reliable operations for all users.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-location-dot"></i></div>
        <h4>Smart Search</h4>
        <p>Find nearby parking with real-time slot availability.</p>
      </div>
    </div>
  </section>

  <section class="section">
    <h2 class="section-title">How It Works</h2>
    <div class="steps">
      <div class="step-card">
        <div class="step-icon">1</div>
        <h5>Create Account</h5>
        <p>Sign up and start reserving your parking spots easily.</p>
      </div>
      <div class="step-card">
        <div class="step-icon">2</div>
        <h5>Search & Book</h5>
        <p>Choose location and time that fits your schedule.</p>
      </div>
      <div class="step-card">
        <div class="step-icon">3</div>
        <h5>Park Easily</h5>
        <p>Check in upon arrival and enjoy stress-free parking.</p>
      </div>
    </div>
  </section>

  <section class="section">
    <h2 class="section-title">What Our Users Say</h2>
    <div class="testimonial">
      <div class="rating mb-2">
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
      </div>
      <p><em>"ParkFinder made parking stress-free. I can reserve before leaving home!"</em></p>
      <strong>- Jane W, Nairobi</strong>
    </div>
  </section>

  <section class="cta">
    <h2>Ready for Hassle-Free Parking?</h2>
    <p>Join thousands who trust ParkFinder every day.</p>
    <?php if (!$user): ?>
      <a href="register.php" class="btn-light"><i class="fa-solid fa-user-plus"></i> Get Started</a>
    <?php else: ?>
      <a href="dashboard.php" class="btn-light"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <?php endif; ?>
  </section>

  <footer>
    &copy; <?= date('Y'); ?> ParkFinder — Smart Parking Made Easy <i class="fa-solid fa-car"></i>
  </footer>
</body>
</html>
