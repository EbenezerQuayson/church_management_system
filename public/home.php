<?php
// Public Homepage - No authentication required
session_start();
require_once __DIR__ . '/../config/config.php';

// Get church settings from database if available
$db = null;
$church_name = 'The Methodist Church Ghana';
$church_motto = 'Your Kingdom Come';
$primary_color = '#003DA5';
$secondary_color = '#CC0000';
$accent_color = '#F4C43F';

try {
    require_once(__DIR__ . '/../config/database.php');
    $db = Database::getInstance();
    $settings = $db->fetchAll("SELECT * FROM settings");
    
    foreach ($settings as $setting) {
        if ($setting['setting_key'] === 'church_name') $church_name = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_motto') $church_motto = $setting['setting_value'];
        if ($setting['setting_key'] === 'primary_color') $primary_color = $setting['setting_value'];
        if ($setting['setting_key'] === 'secondary_color') $secondary_color = $setting['setting_value'];
        if ($setting['setting_key'] === 'accent_color') $accent_color = $setting['setting_value'];
    }
} catch (Exception $e) {
    // Database not available, use defaults
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($church_name); ?> - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/assets/css/homepage.css">
    <style>
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --secondary-color: <?php echo $secondary_color; ?>;
            --accent-color: <?php echo $accent_color; ?>;
        }
    </style>
</head>
<body>
    <!-- Public Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light public-navbar">
        <div class="container-lg">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL;?>/public/home.php">
                <img src="<?php echo BASE_URL?>/assets/images/methodist-logo.png" alt="Methodist Church Logo" class="me-2" style="height: 50px;">
                <div>
                    <div class="brand-name"><?php echo htmlspecialchars($church_name); ?></div>
                    <small class="brand-motto"><?php echo htmlspecialchars($church_motto); ?></small>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#programs">Programs</a></li>
                    <li class="nav-item"><a class="nav-link" href="#ministries">Ministries</a></li>
                    <li class="nav-item"><a class="nav-link" href="#news">News</a></li>
                    <li class="nav-item ms-2"><a class="btn btn-primary btn-sm" href="<?php echo BASE_URL;?>/public/login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container-lg h-100 d-flex align-items-center justify-content-center">
            <div class="hero-content text-center text-white">
                <img src="<?php echo BASE_URL; ?>/assets/images/methodist-logo.png" alt="Methodist Church Logo" class="hero-logo mb-4">
                <h1 class="hero-title mb-3"><?php echo htmlspecialchars($church_name); ?></h1>
                <p class="hero-subtitle mb-4"><?php echo htmlspecialchars($church_motto); ?></p>
                <p class="hero-tagline mb-5"><?php echo htmlspecialchars($church_tagline); ?></p>
                <div class="hero-buttons">
                    <a href="#programs" class="btn btn-light btn-lg me-3 mb-2">
                        <i class="fas fa-arrow-down me-2"></i>Learn More
                    </a>
                    <a href="<?php echo BASE_URL;?>/public/register.php" class="btn btn-warning btn-lg mb-2">
                        <i class="fas fa-user-plus me-2"></i>Join Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section py-5">
        <div class="container-lg">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="about-image-wrapper">
                        <img src="/placeholder.svg?height=400&width=500" alt="Church Sanctuary" class="img-fluid rounded-3">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title mb-3">
                        <i class="fas fa-church me-2"></i>About Our Church
                    </h2>
                    <p class="lead">The Methodist Church Ghana is a thriving community of believers dedicated to spreading God's word and serving others with compassion and integrity.</p>
                    <p>Our mission is to make disciples of Jesus Christ for the transformation of the world. We believe in:</p>
                    <ul class="about-list">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Living out our faith in daily action</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Serving the poor and marginalized</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Building strong community connections</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Growing spiritually through worship and study</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="programs-section py-5 bg-light">
        <div class="container-lg">
            <div class="text-center mb-5">
                <h2 class="section-title mb-3">
                    <i class="fas fa-calendar-alt me-2"></i>Our Programs & Services
                </h2>
                <p class="section-subtitle">Join us for worship and fellowship throughout the week</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-music"></i>
                        </div>
                        <h4>Sunday Worship</h4>
                        <p class="text-muted mb-2">8:00 AM & 10:00 AM</p>
                        <p class="small">Experience uplifting worship and inspiring messages in a welcoming atmosphere.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4>Bible Study</h4>
                        <p class="text-muted mb-2">Wednesday 7:00 PM</p>
                        <p class="small">Deepen your understanding of Scripture in a supportive community.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-children"></i>
                        </div>
                        <h4>Youth Ministry</h4>
                        <p class="text-muted mb-2">Saturdays 5:00 PM</p>
                        <p class="small">Fun, faith-building activities for young people ages 13-25.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-praying-hands"></i>
                        </div>
                        <h4>Prayer Meeting</h4>
                        <p class="text-muted mb-2">Thursday 6:00 PM</p>
                        <p class="small">Come together to intercede for our church and community.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ministries Section -->
    <section id="ministries" class="ministries-section py-5">
        <div class="container-lg">
            <div class="text-center mb-5">
                <h2 class="section-title mb-3">
                    <i class="fas fa-hands-helping me-2"></i>Our Ministries
                </h2>
                <p class="section-subtitle">Find your place to serve and grow</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="ministry-card">
                        <div class="ministry-header">
                            <img src="/placeholder.svg?height=200&width=400" alt="Music Ministry" class="img-fluid">
                            <div class="ministry-overlay">
                                <i class="fas fa-music"></i>
                            </div>
                        </div>
                        <div class="ministry-body">
                            <h4>Music Ministry</h4>
                            <p class="text-muted small mb-3">Our talented musicians lead worship and create meaningful spiritual experiences.</p>
                            <a href="#" class="btn btn-sm btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="ministry-card">
                        <div class="ministry-header">
                            <img src="/placeholder.svg?height=200&width=400" alt="Outreach Ministry" class="img-fluid">
                            <div class="ministry-overlay">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                        </div>
                        <div class="ministry-body">
                            <h4>Community Outreach</h4>
                            <p class="text-muted small mb-3">We serve our community through charity work and social justice initiatives.</p>
                            <a href="#" class="btn btn-sm btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="ministry-card">
                        <div class="ministry-header">
                            <img src="/placeholder.svg?height=200&width=400" alt="Youth Ministry" class="img-fluid">
                            <div class="ministry-overlay">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="ministry-body">
                            <h4>Youth & Children</h4>
                            <p class="text-muted small mb-3">Age-appropriate programs for children and teens to grow in faith together.</p>
                            <a href="#" class="btn btn-sm btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News & Announcements Section -->
    <section id="news" class="news-section py-5 bg-light">
        <div class="container-lg">
            <div class="text-center mb-5">
                <h2 class="section-title mb-3">
                    <i class="fas fa-newspaper me-2"></i>Latest News & Events
                </h2>
                <p class="section-subtitle">Stay updated with what's happening at our church</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-date">Dec 25</div>
                        <h5 class="news-title">Christmas Celebration Service</h5>
                        <p class="text-muted small mb-3">Join us for a special Christmas worship service celebrating the birth of Christ with music, prayers, and fellowship.</p>
                        <a href="#" class="btn btn-sm btn-primary">Read More</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-date">Jan 5</div>
                        <h5 class="news-title">New Year Prayers & Fasting</h5>
                        <p class="text-muted small mb-3">Begin the new year with us in prayer and fasting. Let's seek God's guidance and blessings for 2024.</p>
                        <a href="#" class="btn btn-sm btn-primary">Read More</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-date">Jan 12</div>
                        <h5 class="news-title">Community Food Drive</h5>
                        <p class="text-muted small mb-3">Participate in our community food drive to help provide nutritious meals to families in need.</p>
                        <a href="#" class="btn btn-sm btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call-to-Action Section -->
    <section class="cta-section py-5">
        <div class="container-lg">
            <div class="cta-card">
                <div class="cta-content">
                    <h2 class="text-white mb-3">Ready to Join Our Community?</h2>
                    <p class="text-white-50 mb-4">Whether you're looking for spiritual growth, community service, or a welcoming family, there's a place for you here.</p>
                    <div class="cta-buttons">
                        <a href="/public/register.php" class="btn btn-light btn-lg me-3 mb-2">
                            <i class="fas fa-user-plus me-2"></i>Register
                        </a>
                        <a href="/public/login.php" class="btn btn-outline-light btn-lg mb-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Public Footer -->
    <footer class="public-footer">
        <div class="container-lg">
            <div class="row g-5 mb-5">
                <div class="col-md-6 col-lg-3">
                    <div class="footer-section">
                        <h5 class="footer-title">
                            <img src="<?php echo BASE_URL ?>/assets/images/methodist-logo.png" alt="Logo" style="height: 30px;" class="me-2">
                            The Methodist Church
                        </h5>
                        <p class="text-muted small">Your Kingdom Come - Growing in faith, serving our community, and living Christ's love.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="footer-section">
                        <h5 class="footer-title">Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="#home">Home</a></li>
                            <li><a href="#about">About Us</a></li>
                            <li><a href="#programs">Programs</a></li>
                            <li><a href="#ministries">Ministries</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="footer-section">
                        <h5 class="footer-title">Contact Us</h5>
                        <ul class="footer-links small">
                            <li><i class="fas fa-map-marker-alt me-2"></i>123 Church Street, City, Country</li>
                            <li><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</li>
                            <li><i class="fas fa-envelope me-2"></i>info@methodistchurch.org</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="footer-section">
                        <h5 class="footer-title">Follow Us</h5>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted small mb-0">&copy; 2025 The Methodist Church Ghana. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted small mb-0">
                        <a href="#" class="text-decoration-none">Privacy Policy</a> | 
                        <a href="#" class="text-decoration-none">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
