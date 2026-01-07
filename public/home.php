<?php
// Public Homepage - No authentication required
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Ministry.php';
require_once __DIR__ . '/../app/models/HomepageMinistry.php';
require_once __DIR__ . '/../app/models/Program.php';



$ministry = new Ministry();
$homepageMinistry = new HomepageMinistry();

$programModal = new Program();

$homepagePrograms = $programModal->getHomepagePrograms();
$homepageMinistries = $homepageMinistry->getAllForHomepage();




// Get church settings from database if available
$db = null;
$church_name = 'The Methodist Church Ghana';
$church_motto = 'Your Kingdom Come';
$primary_color = '#003DA5';
$secondary_color = '#CC0000';
$accent_color = '#F4C43F';
$church_tagline = 'Growing in faith, serving our community, and living Christ\'s love.';
$church_address = '123 Church Street, City, Country';
$church_email = 'info@church.com';
$church_phone = '+1 (555) 123-4567';
$church_facebook = '#';
$church_instagram = '#';
$church_tiktok = '#';
$church_youtube = '#';
$church_x = '#';
$church_about_img = BASE_URL . '/assets/images/church_sanctuary.jpg';
$church_about_text = 'The Methodist Church Ghana is a thriving community of believers dedicated to spreading God\'s word and serving others with compassion and integrity.
Our mission is to make disciples of Jesus Christ for the transformation of the world. We believe in:
1. Living out our faith in daily action
2. Serving the poor and marginalized 
3. Building strong community connections
4.Growing spiritually through worship and study';
$church_logo = BASE_URL . '/assets/images/methodist-logo.png';


try {
    require_once(__DIR__ . '/../config/database.php');
    $db = Database::getInstance();
    $settings = $db->fetchAll("SELECT * FROM settings");
    
    foreach ($settings as $setting) {
        if ($setting['setting_key'] === 'church_name') $church_name = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_motto') $church_motto = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_tagline') $church_tagline = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_logo') {
            if($setting['setting_value'] != null ){
            $church_logo = BASE_URL . '/assets/images/' . $setting['setting_value'];
            } else{
                $church_logo = BASE_URL . '/assets/images/methodist-logo.png';
            }
        }
        if ($setting['setting_key'] === 'primary_color') $primary_color = $setting['setting_value'];
        if ($setting['setting_key'] === 'secondary_color') $secondary_color = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_address') $church_address = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_email') $church_email = $setting['setting_value'];
        if ($setting['setting_key'] === 'church_phone') $church_phone = $setting['setting_value'];
        if ($setting['setting_key'] === 'homepage_social_facebook') $church_facebook = $setting['setting_value'];
        if ($setting['setting_key'] === 'homepage_social_instagram') $church_instagram = $setting['setting_value'];
        if ($setting['setting_key'] === 'homepage_social_tiktok') $church_tiktok = $setting['setting_value'];
        if ($setting['setting_key'] === 'homepage_social_youtube') $church_youtube = $setting['setting_value'];
        if ($setting['setting_key'] === 'homepage_social_x') $church_x = $setting['setting_value'];
        if ($setting['setting_key'] === 'homepage_about_image') {
            if($setting['setting_value'] != null ){
            $church_about_img = BASE_URL . '/assets/images/' . $setting['setting_value'];
            } else{
                $church_about_img = BASE_URL . '/assets/images/church_sanctuary.jpg';
            }
        }
        if ($setting['setting_key'] === 'homepage_about_text') $church_about_text = $setting['setting_value'];
    }

    $events = $db->fetchAll("SELECT * FROM events WHERE status = 'scheduled' AND event_date >= CURDATE() ORDER BY event_date ");
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
                <img src="<?= $church_logo ?>" alt="Methodist Church Logo" class="me-2" style="height: 50px;">
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
                <img src="<?= $church_logo ?>" alt="<?= $church_name ?>  Logo" class="hero-logo mb-4">
                <h1 class="hero-title mb-3"><?php echo htmlspecialchars($church_name); ?></h1>
                <p class="hero-subtitle mb-4"><?php echo htmlspecialchars($church_motto); ?></p>
                <p class="hero-tagline mb-5"><?php echo htmlspecialchars($church_tagline); ?></p>
                <div class="hero-buttons">
                    <a href="#programs" class="btn btn-light btn-lg me-3 mb-2">
                        <i class="fas fa-arrow-down me-2"></i>Learn More
                    </a>
                    <a href="#" class="btn btn-warning btn-lg mb-2 disabled">
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
                        <img src="<?= $church_about_img ?>" alt="About Image" class="img-fluid rounded-3">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title mb-3">
                        <i class="fas fa-church me-2"></i>About Our Church
                    </h2>
                    <p><?= $church_about_text ?></p>
                    <!-- <p class="lead">The Methodist Church Ghana is a thriving community of believers dedicated to spreading God's word and serving others with compassion and integrity.</p>
                    <p>Our mission is to make disciples of Jesus Christ for the transformation of the world. We believe in:</p>
                    <ul class="about-list">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Living out our faith in daily action</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Serving the poor and marginalized</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Building strong community connections</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Growing spiritually through worship and study</li>
                    </ul> -->
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
                <?php if (!empty($homepagePrograms)): ?>
                    <?php foreach($homepagePrograms as $program): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="<?= htmlspecialchars($program['icon_class'] ?: 'fas-fa-calendar'); ?>"></i>
                        </div>
                        <h4><?= htmlspecialchars($program['title']); ?></h4>
                        <p class="text-muted mb-2"><?= htmlspecialchars($program['schedule_text']); ?></p>
                        <?php if (!empty($program['description'])): ?>
                    <p class="small">
                        <?= htmlspecialchars($program['description']); ?>
                    </p>
                <?php endif; ?>
                    </div>
                </div>
                <?php endforeach?>
                <?php else: ?>

    <div class="col-12">
        <div class="alert alert-light text-center py-5 border rounded">
            <i class="fas fa-info-circle fa-2x mb-3 text-muted"></i>
            <h5 class="mb-2">Programs Coming Soon</h5>
            <p class="text-muted mb-0">
                Our programs will be updated shortly. Please check back later.
            </p>
        </div>
    </div>

<?php endif; ?>

                <!-- <div class="col-md-6 col-lg-3">
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
                </div> -->
            </div>
        </div>
    </section>




    <!-- Organization Section -->
  <section id="ministries" class="ministries-section py-5">
    <div class="container-lg">
        <div class="text-center mb-5">
            <h2 class="section-title mb-3">
                <i class="fas fa-hands-helping me-2"></i>Our Organizations
            </h2>
            <p class="section-subtitle">Find your place to serve and grow</p>
        </div>
       <?php if (!empty($homepageMinistries)): ?>
            <div class="row g-4">
                <?php foreach ($homepageMinistries as $hm): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="ministry-card">
                            <div class="ministry-header">
                            <?php
                        $defaultImage = BASE_URL . '/assets/images/church_sanctuary.jpg';
                        $imageSrc = !empty($hm['image_path'])
                            ? BASE_URL . '/assets/images/' . htmlspecialchars($hm['image_path'])
                            : $defaultImage;
                        ?>
                            <img 
                                src="<?= $imageSrc; ?>"
                                class="img-fluid"
                                alt="<?= htmlspecialchars($hm['name']); ?>"
                                onerror="this.src='<?= $defaultImage; ?>';"
                            >
                                <div class="ministry-overlay">
                                    <i class="<?= htmlspecialchars($hm['icon_class']); ?>"></i>
                                </div>
                            </div>
                            <div class="ministry-body">
                                <h4><?= htmlspecialchars($hm['name']); ?></h4>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($hm['description']); ?></p>
                                <?php if (!empty($hm['link_url'])): ?>
                                    <a href="<?= htmlspecialchars($hm['link_url']); ?>" class="btn btn-sm btn-outline-primary">
                                        Learn More
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Fallback UI -->
            <div class="text-center py-5">
                <i class="fas fa-church fa-3x text-muted mb-3"></i>
                <h5 class="fw-semibold">No ministries are currently displayed</h5>
                <p class="text-muted mb-0">
                    Please check back later or contact the church office for more information.
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

    <!-- <section id="ministries" class="ministries-section py-5">
        <div class="container-lg">
            <div class="text-center mb-5">
                <h2 class="section-title mb-3">
                    <i class="fas fa-hands-helping me-2"></i>Our Organizations
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
    </section> -->

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
                <!-- <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-date">Dec 25</div>
                        <h5 class="news-title">Christmas Celebration Service</h5>
                        <p class="text-muted small mb-3">Join us for a special Christmas worship service celebrating the birth of Christ with music, prayers, and fellowship.</p>
                        <a href="#" class="btn btn-sm btn-primary">Read More</a>
                    </div>
                </div> -->
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="col-md-6 col-lg-4">
                        <div class="news-card">
                            <div class="news-date"><?php echo date('M d', strtotime($event['event_date'])); ?></div>
                            <h5 class="news-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars($event['description']); ?></p>
                            <button 
    class="btn btn-sm btn-primary view-event-btn"
    data-bs-toggle="modal"
    data-bs-target="#eventDetailsModal"
    data-title="<?= htmlspecialchars($event['title']) ?>"
    data-date="<?= date('F d, Y', strtotime($event['event_date'])) ?>"
    data-time="<?= date('g:i A', strtotime($event['event_date'])) ?>"
    data-location="<?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8') ?>"
    data-description="<?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?>"
>
    Read More
</button>

                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>No upcoming events at this time. Please check back soon!
                        </div>
                    </div>
                <?php endif; ?>
                <!-- <div class="col-md-6 col-lg-4">
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
            </div> -->
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
                        <a href="#" class="btn btn-light btn-lg me-3 mb-2 disabled" >
                            <i class="fas fa-user-plus me-2"></i>Register
                        </a>
                        <a href="<?php echo BASE_URL ?>/public/login.php" class="btn btn-outline-light btn-lg mb-2">
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
                            <img src="<?= $church_logo ?>" alt="Logo" style="height: 30px;" class="me-2">
                            <?php echo htmlspecialchars($church_name)?>
                        </h5>
                        <p class="text-muted small"><?php echo htmlspecialchars($church_motto)?> - <?php echo htmlspecialchars($church_tagline)?></p>
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
                            <li><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($church_address)?></li>
                            <li><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($church_phone)?></li>
                            <li><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($church_email)?></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="footer-section">
                        <h5 class="footer-title">Follow Us</h5>
                        <div class="social-links">
                            <a href="<?php echo htmlspecialchars($church_facebook) ?>" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="<?php echo htmlspecialchars($church_x) ?>" class="social-link"><i class="fab fa-x"></i></a> <!-- Twitter renamed to X (yet to find the correct icon) -->
                            <a href="<?php echo htmlspecialchars($church_instagram) ?>" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="<?php echo htmlspecialchars($church_youtube) ?>" class="social-link"><i class="fab fa-youtube"></i></a>
                            <a href="<?php echo htmlspecialchars($church_tiktok) ?>" class="social-link"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row">
                <div class="col-md-6">
                    <b><p class="text-muted small mb-0">&copy; 2025 The Methodist Church Ghana. All rights reserved.</p></b>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted small mb-0">
                        <a href="#" class="text-decoration-none">Privacy Policy</a> | 
                        <a href="#" class="text-decoration-none">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="text-muted mb-2">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span id="eventModalDate"></span>
                </p>

                <p class="text-muted mb-2">
    <i class="fas fa-clock me-2"></i>
    <span id="eventModalTime"></span>
</p>


                <p class="text-muted mb-2">
    <i class="fas fa-map-marker-alt me-2"></i>
    <span id="eventModalLocation"></span>
</p>

                <hr>
                <p id="eventModalDescription" class="text-dark"></p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('eventDetailsModal');

    if (!modal) return;

    modal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;

        document.getElementById('eventModalTitle').textContent =
            button.dataset.title;

        document.getElementById('eventModalDate').textContent =
            button.dataset.date;

        document.getElementById('eventModalTime').textContent =
    button.dataset.time || 'Time not specified';

        
         document.getElementById('eventModalLocation').textContent =
            button.dataset.location || 'Venue to be announced';

        document.getElementById('eventModalDescription').textContent =
            button.dataset.description;
    });
});

</script>
    <script src="<?php echo BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
