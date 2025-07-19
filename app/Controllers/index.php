<?php
$page_title = "Find Legal Professionals";
include __DIR__ . '/../Views/header.php';
?>

<section class="hero hero-video" style="position: relative; min-height: 420px; display: flex; align-items: center; justify-content: center; overflow: hidden; width: 100vw; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; max-width: 100vw; border-radius: 0;">
    <video autoplay loop muted playsinline style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;">
        <source src="https://rrlawaz.com/wp-content/uploads/2025/05/rrlaw_adobestock_345225353-1080p.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="hero-content" style="position: relative; z-index: 2; width: 100%; text-align: center; color: #fff; padding: 4rem 1rem;">
        <h1 class="hero-headline--home" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1.2rem;">Find the Right Lawyer for Your Case</h1>
        <h4 class="hero-subheadline" style="font-size: 1.3rem; font-weight: 500; margin-bottom: 2.2rem;">Connect with qualified legal professionals in your area for all your legal needs</h4>
        <a href="lawyers.php" class="btn btn-primary" style="margin-top:2rem; font-size: 1.25rem; padding: 1.1rem 2.5rem; border-radius: 10px; box-shadow: 0 6px 24px rgba(37,99,235,0.18); font-weight: 700; letter-spacing: 0.5px;">Find Lawyers Now</a>
    </div>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(37,99,235,0.45) 0%, rgba(56,189,248,0.35) 100%); z-index: 1;"></div>
</section>
<section class="search-section">
    <h2>Search for Lawyers</h2>
    <form class="search-form" action="lawyers.php" method="GET">
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
    <div class="container">
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
    </div>
</section>

<section class="legal-services">
    <h2>Legal Services We Cover</h2>
    <div class="container">
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
/* Consistent search form styling with lawyers.php */
.search-form {
    display: flex;
    gap: 1.2rem;
    flex-wrap: wrap;
    align-items: flex-end;
}
.search-form .form-group {
    flex: 1;
    min-width: 220px;
    margin-bottom: 0;
}
.search-form .form-group:last-child {
    flex: 0 0 auto;
    min-width: 160px;
    margin-bottom: 0;
}
.search-form button.btn {
    width: 100%;
    padding: 0.9rem 1.2rem;
    font-size: 1rem;
    border-radius: 7px;
    margin-top: 0;
    box-shadow: 0 2px 8px rgba(56,189,248,0.08);
}
@media (max-width: 700px) {
    .search-form {
        flex-direction: column;
        align-items: stretch;
        gap: 0.7rem;
    }
    .search-form .form-group, .search-form button.btn {
        min-width: 0;
        width: 100%;
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

    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const params = new URLSearchParams();
            const location = document.getElementById('location').value.trim();
            const service = document.getElementById('service').value;
            const experience = document.getElementById('experience').value;
            if (location) params.append('location', location);
            if (service) params.append('service', service);
            if (experience) params.append('experience', experience);
            window.location.href = 'lawyers.php' + (params.toString() ? '?' + params.toString() : '');
        });
    }
});
</script>

<?php include __DIR__ . '/../Views/footer.php'; ?>
