<?php
$page_title = "Home";
include __DIR__ . '/../Views/header.php';
?>

<main>
    <!-- Hero Section with Stretched Video Background -->
    <section class="hero hero-video" style="position: relative; min-height: 420px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
        <video autoplay loop muted playsinline style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;">
            <source src="https://rrlawaz.com/wp-content/uploads/2025/05/rrlaw_adobestock_345225353-1080p.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-content" style="position: relative; z-index: 2; width: 100%; text-align: center; color: #fff; padding: 4rem 1rem;">
            <h1 class="hero-headline--home" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1.2rem;">Safety. Clarity. Hope.</h1>
            <h4 class="hero-subheadline" style="font-size: 1.3rem; font-weight: 500; margin-bottom: 2.2rem;">Call us today for a free case evaluation and speak with an intake specialist immediately!</h4>
            <a href="#contact" class="btn btn-primary" style="margin-top:2rem;">Click Here for a Case Evaluation or to Schedule a Call</a>
        </div>
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(37,99,235,0.45) 0%, rgba(56,189,248,0.35) 100%); z-index: 1;"></div>
    </section>

    <!-- About Us Section -->
    <section class="about-section" style="background-color: #f2f2f2; padding: 3rem 0;">
        <div class="container">
            <h3 style="text-align:center; color:#2563eb;">About Us</h3>
            <h2 style="text-align:center; color:#2c3e50;">Winning Defense Team</h2>
            <p style="max-width:700px; margin:1.5rem auto; text-align:center;">Being charged with a crime is one of the most difficult situations you or your loved ones will ever face. We are here to help.<br>Our team of experienced criminal defense attorneys will provide you with aggressive representation in DUI, felony, and misdemeanor cases. We bring a fresh and tenacious approach to criminal defense to ensure your rights are protected and to reach the best possible outcome in your case while providing you with safety, clarity, and hope in your life.</p>
            <div style="text-align:center;">
                <a href="#contact" class="btn btn-secondary">Click Here for a Case Evaluation or to Schedule a Call</a>
            </div>
        </div>
    </section>

    <!-- Reviews/Stats Section -->
    <section class="stats-row" style="background-color: #141a3b; color: #fff; padding: 3rem 0;">
        <div class="container">
            <h2 style="text-align:center; color:#fff;">Strength in Numbers</h2>
            <p style="text-align:center; color:#fff;">Every year, we help thousands of people charged with crimes navigate the criminal justice system and resolve their criminal cases.</p>
            <div class="services-grid" style="margin-top:2.5rem;">
                <div class="service-card" style="background:#23272f; color:#fff;">
                    <span style="font-size:2.2rem; font-weight:700;">150+</span>
                    <div>Courts Throughout Arizona</div>
                </div>
                <div class="service-card" style="background:#23272f; color:#fff;">
                    <span style="font-size:2.2rem; font-weight:700;">5160</span>
                    <div>Cases Handled</div>
                </div>
                <div class="service-card" style="background:#23272f; color:#fff;">
                    <span style="font-size:2.2rem; font-weight:700;">35</span>
                    <div>Years of Combined Experience</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Practice Areas Section -->
    <section class="legal-services">
        <h2>Practice Areas</h2>
        <div class="services-grid">
            <div class="service-card">
                <i class="fas fa-car"></i>
                <h3>DUI</h3>
                <p>Overcome your DUI with a team that knows the science.</p>
                <a href="#contact" class="btn btn-secondary" style="margin-top:1rem;">Learn More</a>
            </div>
            <div class="service-card">
                <i class="fas fa-gavel"></i>
                <h3>Felony</h3>
                <p>Defeat your felony charges. Preserve your record and protect your rights.</p>
                <a href="#contact" class="btn btn-secondary" style="margin-top:1rem;">Learn More</a>
            </div>
            <div class="service-card">
                <i class="fas fa-balance-scale"></i>
                <h3>Misdemeanor</h3>
                <p>A misdemeanor charge can be scary, but not with us in your corner!</p>
                <a href="#contact" class="btn btn-secondary" style="margin-top:1rem;">Learn More</a>
            </div>
            <div class="service-card">
                <i class="fas fa-traffic-light"></i>
                <h3>Criminal Traffic</h3>
                <p>Conquer your traffic ticket. You are not a criminal for a traffic infraction!</p>
                <a href="#contact" class="btn btn-secondary" style="margin-top:1rem;">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section" style="background-color: #f2f2f2; padding: 3rem 0;">
        <div class="container">
            <h2 style="text-align:center; color:#2563eb;">Free Case Evaluation</h2>
            <p style="text-align:center; max-width:600px; margin:0 auto 2rem auto;">Call us today to schedule a free case evaluation at <strong><a href="tel:6024973088">(602) 497-3088</a></strong> or email us at <strong><a href="mailto:info@rrlawaz.com">info@rrlawaz.com</a></strong>.</p>
            <form class="form-container" style="max-width:500px; margin:0 auto;">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Submit</button>
            </form>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../Views/footer.php'; ?> 