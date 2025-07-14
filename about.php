<?php
$page_title = "About Us";
include 'header.php';
?>

<div class="about-container">
    <div class="hero-section">
        <h1><i class="fas fa-info-circle"></i> About LawyerConnect</h1>
        <p class="hero-subtitle">Connecting clients with qualified legal professionals for all their legal needs</p>
    </div>

    <div class="about-content">
        <div class="about-section">
            <h2>Our Mission</h2>
            <p>At LawyerConnect, we bridge the gap between clients seeking legal assistance and experienced attorneys ready to help. Our platform is designed to make finding the right lawyer simple, transparent, and efficient.</p>
        </div>

        <div class="about-section">
            <h2>What We Do</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-search"></i>
                    <h3>Easy Search</h3>
                    <p>Search for lawyers by location, specialization, and experience level to find the perfect match for your legal needs.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-user-check"></i>
                    <h3>Verified Professionals</h3>
                    <p>All lawyers on our platform are verified and licensed professionals with proven track records.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Simple Booking</h3>
                    <p>Book appointments directly through our platform with instant confirmation and reminders.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-star"></i>
                    <h3>Reviews & Ratings</h3>
                    <p>Read genuine reviews from previous clients to make informed decisions about your legal representation.</p>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2>Why Choose LawyerConnect?</h2>
            <div class="benefits-list">
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4>Extensive Network</h4>
                        <p>Access to a wide network of qualified lawyers across various legal specializations and locations.</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4>Transparent Pricing</h4>
                        <p>Clear consultation fees and pricing information upfront with no hidden costs.</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4>Secure Platform</h4>
                        <p>Your personal information and communications are protected with industry-standard security measures.</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4>24/7 Support</h4>
                        <p>Our support team is available around the clock to assist with any questions or concerns.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2>Legal Services We Cover</h2>
            <div class="services-grid">
                <div class="service-item">
                    <i class="fas fa-gavel"></i>
                    <span>Criminal Law</span>
                </div>
                <div class="service-item">
                    <i class="fas fa-heart"></i>
                    <span>Family Law</span>
                </div>
                <div class="service-item">
                    <i class="fas fa-building"></i>
                    <span>Corporate Law</span>
                </div>
                <div class="service-item">
                    <i class="fas fa-scales-balanced"></i>
                    <span>Civil Law</span>
                </div>
                <div class="service-item">
                    <i class="fas fa-home"></i>
                    <span>Real Estate</span>
                </div>
                <div class="service-item">
                    <i class="fas fa-passport"></i>
                    <span>Immigration</span>
                </div>
                <div class="service-item">
                    <i class="fas fa-calculator"></i>
                    <span>Tax Law</span>
                </div>
                <div class="service-item">
                    <i class="fas fa-users"></i>
                    <span>Employment Law</span>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2>Our Team</h2>
            <p>LawyerConnect was founded by a team of legal professionals and technology experts who understand the challenges of finding quality legal representation. We are committed to making legal services more accessible and transparent for everyone.</p>
        </div>

        <div class="about-section">
            <h2>Get Started Today</h2>
            <p>Whether you're facing a legal challenge or need professional advice, LawyerConnect is here to help you find the right attorney for your needs.</p>
            <div class="cta-buttons">
                <a href="lawyers.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Find a Lawyer
                </a>
                <a href="register.php" class="btn btn-secondary">
                    <i class="fas fa-user-plus"></i> Join as a Lawyer
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.about-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.hero-section {
    text-align: center;
    margin-bottom: 4rem;
    padding: 3rem 0;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border-radius: 15px;
}

.hero-section h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.hero-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
}

.about-content {
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    padding: 3rem;
}

.about-section {
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.about-section:last-child {
    border-bottom: none;
}

.about-section h2 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-size: 2rem;
}

.about-section p {
    line-height: 1.6;
    color: #666;
    margin-bottom: 1rem;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.feature-card {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 10px;
    transition: transform 0.3s;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-card i {
    font-size: 3rem;
    color: #3498db;
    margin-bottom: 1rem;
}

.feature-card h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.benefits-list {
    margin-top: 2rem;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.benefit-item i {
    color: #27ae60;
    font-size: 1.5rem;
    margin-right: 1rem;
    margin-top: 0.25rem;
}

.benefit-item h4 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.benefit-item p {
    margin: 0;
    color: #666;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.service-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background 0.3s;
}

.service-item:hover {
    background: #e3f2fd;
}

.service-item i {
    color: #3498db;
    font-size: 1.5rem;
}

.service-item span {
    font-weight: 500;
    color: #2c3e50;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

.cta-buttons .btn {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<?php include 'footer.php'; ?>
