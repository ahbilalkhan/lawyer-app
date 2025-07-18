<!-- Image Slider -->

<?php
$page_title = "Find Legal Professionals";
include 'header.php';
?>

<section class="hero">
    <h1>Find the Right Lawyer for Your Case</h1>
    <p>Connect with qualified legal professionals in your area for all your legal needs</p>
    <a href="lawyers.php" class="btn btn-primary">Find Lawyers Now</a>
</section>
<div class="image-slider">
    <div class="slider-wrapper">
        <img src="img/1170654-supremecourtFBRSBRstory-1472235718.webp" class="slider-image active" alt="Supreme Court">
        <img src="img/female-lawyers-working-at-the-law-firms-judge-gavel-with-scales-of-justice-legal-law-lawyer.webp" class="slider-image" alt="Female Lawyers">
        <img src="img/stock-photo-lawyers-discussing-contract-in-office.webp" class="slider-image" alt="Lawyers Discussing Contract">
    </div>
    <button class="slider-arrow slider-arrow-left" aria-label="Previous">&#10094;</button>
    <button class="slider-arrow slider-arrow-right" aria-label="Next">&#10095;</button>
    <div class="slider-dots">
        <span class="slider-dot active"></span>
        <span class="slider-dot"></span>
        <span class="slider-dot"></span>
    </div>
</div>
<section class="search-section">
    <h2>Search for Lawyers</h2>
    <form id="searchForm" class="search-form">
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" placeholder="Enter city or state">
        </div>
        
        <div class="form-group">
            <label for="service">Legal Service</label>
            <select id="service" name="service">
                <option value="">All Services</option>
                <option value="criminal">Criminal Law</option>
                <option value="family">Family Law</option>
                <option value="divorce">Divorce</option>
                <option value="civil">Civil Law</option>
                <option value="corporate">Corporate Law</option>
                <option value="property">Property Law</option>
                <option value="immigration">Immigration</option>
                <option value="tax">Tax Law</option>
                <option value="labor">Labor Law</option>
                <option value="intellectual_property">Intellectual Property</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="experience">Experience</label>
            <select id="experience" name="experience">
                <option value="">Any Experience</option>
                <option value="1-3">1-3 years</option>
                <option value="4-7">4-7 years</option>
                <option value="8-15">8-15 years</option>
                <option value="15+">15+ years</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search Lawyers
            </button>
        </div>
    </form>
</section>

<section class="featured-lawyers">
    <h2>Featured Lawyers</h2>
    <div id="lawyersGrid" class="lawyers-grid">
        <!-- Lawyers will be loaded here via JavaScript -->
    </div>
</section>

<section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps">
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>1. Search</h3>
            <p>Search for lawyers by location, specialization, or specific legal services you need.</p>
        </div>
        
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3>2. Browse Profiles</h3>
            <p>View detailed profiles including experience, ratings, and client reviews.</p>
        </div>
        
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3>3. Book Appointment</h3>
            <p>Schedule a consultation at your convenience with your chosen lawyer.</p>
        </div>
        
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h3>4. Get Legal Help</h3>
            <p>Meet with your lawyer and get the legal assistance you need.</p>
        </div>
    </div>
</section>

<section class="legal-services">
    <h2>Legal Services We Cover</h2>
    <div class="services-grid">
        <div class="service-card">
            <i class="fas fa-gavel"></i>
            <h3>Criminal Law</h3>
            <p>Defense for criminal charges, DUI, drug offenses, and more.</p>
        </div>
        
        <div class="service-card">
            <i class="fas fa-heart"></i>
            <h3>Family Law</h3>
            <p>Divorce, child custody, adoption, and family legal matters.</p>
        </div>
        
        <div class="service-card">
            <i class="fas fa-building"></i>
            <h3>Corporate Law</h3>
            <p>Business formation, contracts, mergers, and corporate legal issues.</p>
        </div>
        
        <div class="service-card">
            <i class="fas fa-scales-balanced"></i>
            <h3>Civil Law</h3>
            <p>Personal injury, civil disputes, and litigation services.</p>
        </div>
        
        <div class="service-card">
            <i class="fas fa-home"></i>
            <h3>Property Law</h3>
            <p>Real estate transactions, property disputes, and land law.</p>
        </div>
        
        <div class="service-card">
            <i class="fas fa-passport"></i>
            <h3>Immigration</h3>
            <p>Visa applications, citizenship, and immigration legal services.</p>
        </div>
    </div>
</section>

<style>
/* Additional styles for index page */
.how-it-works {
    padding: 3rem 0;
    background: white;
    margin: 2rem 0;
    border-radius: 10px;
}

.steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.step {
    text-align: center;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.step-icon {
    font-size: 3rem;
    color: #3498db;
    margin-bottom: 1rem;
}

.step h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.legal-services {
    padding: 3rem 0;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.service-card {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-card i {
    font-size: 3rem;
    color: #3498db;
    margin-bottom: 1rem;
}

.service-card h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.featured-lawyers {
    margin: 3rem 0;
}

.featured-lawyers h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 2rem;
}
.image-slider {
    position: relative;
    width: 100%;
    margin: 2rem 0 2.5rem 0;
    overflow: hidden;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(37,99,235,0.10);
    background: #f4f7fb;
}
.slider-wrapper {
    position: relative;
    width: 100%;
    height: 340px;
}
.slider-image {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0;
    transition: opacity 0.7s cubic-bezier(0.4,0,0.2,1);
    z-index: 1;
    pointer-events: none;
}
.slider-image.active {
    opacity: 1;
    z-index: 2;
    pointer-events: auto;
}
.slider-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(44,62,80,0.7);
    color: #fff;
    border: none;
    font-size: 2rem;
    padding: 0.3rem 1rem;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
    transition: background 0.2s;
}
.slider-arrow-left {
    left: 18px;
}
.slider-arrow-right {
    right: 18px;
}
.slider-arrow:hover {
    background: #2563eb;
}
.slider-dots {
    position: absolute;
    bottom: 18px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.6rem;
    z-index: 11;
}
.slider-dot {
    width: 12px;
    height: 12px;
    background: #e0e7ef;
    border-radius: 50%;
    display: inline-block;
    cursor: pointer;
    transition: background 0.2s;
    border: 2px solid #fff;
}
.slider-dot.active {
    background: #2563eb;
}
@media (max-width: 900px) {
    .slider-wrapper {
        height: 220px;
    }
}
@media (max-width: 600px) {
    .image-slider {
        border-radius: 8px;
    }
    .slider-wrapper {
        height: 140px;
    }
}
.hero {
    background: none;
    color: #23272f;
    text-align: center;
    padding: 1.2rem 0 1.2rem 0;
    margin-bottom: 1.2rem;
}
.hero h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.hero p {
    font-size: 1.05rem;
    margin-bottom: 1.1rem;
    font-weight: 500;
}
.hero .btn {
    font-size: 1rem;
    padding: 0.38rem 1.1rem;
}
@media (max-width: 600px) {
    .hero {
        padding: 0.7rem 0 0.7rem 0;
    }
    .hero h1 {
        font-size: 1.2rem;
    }
    .hero p {
        font-size: 0.95rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.slider-image');
    const dots = document.querySelectorAll('.slider-dot');
    const leftArrow = document.querySelector('.slider-arrow-left');
    const rightArrow = document.querySelector('.slider-arrow-right');
    let current = 0;
    let interval;

    function showSlide(idx) {
        images.forEach((img, i) => {
            img.classList.toggle('active', i === idx);
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === idx);
        });
        current = idx;
    }

    function nextSlide() {
        showSlide((current + 1) % images.length);
    }
    function prevSlide() {
        showSlide((current - 1 + images.length) % images.length);
    }
    function startAutoSlide() {
        interval = setInterval(nextSlide, 4000);
    }
    function stopAutoSlide() {
        clearInterval(interval);
    }

    rightArrow.addEventListener('click', () => {
        nextSlide();
        stopAutoSlide();
        startAutoSlide();
    });
    leftArrow.addEventListener('click', () => {
        prevSlide();
        stopAutoSlide();
        startAutoSlide();
    });
    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            showSlide(i);
            stopAutoSlide();
            startAutoSlide();
        });
    });

    showSlide(0);
    startAutoSlide();
});
</script>

<?php include 'footer.php'; ?>
