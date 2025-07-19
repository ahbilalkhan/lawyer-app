<?php
$page_title = "Contact Us";
include __DIR__ . '/../Views/header.php';
?>
<div class="contact-container">
    <div class="contact-header">
        <h1><i class="fas fa-envelope"></i> Contact Us</h1>
        <p>Get in touch with our team for any questions or support needs</p>
    </div>

    <div class="contact-content">
        <div class="contact-info-section">
            <h2>Get in Touch</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Address</h3>
                        <p>123 Legal Street<br>Law City, LC 12345<br>United States</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Phone</h3>
                        <p>+1 (555) 123-4567<br>Available 24/7</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Email</h3>
                        <p>info@lawyerconnect.com<br>support@lawyerconnect.com</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Business Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form-section">
            <h2>Send us a Message</h2>
            <form id="contactForm" class="contact-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a topic...</option>
                            <option value="general">General Inquiry</option>
                            <option value="support">Technical Support</option>
                            <option value="lawyer">Lawyer Registration</option>
                            <option value="billing">Billing Question</option>
                            <option value="feedback">Feedback</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required placeholder="Please describe your inquiry in detail..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>

    <div class="contact-additional">
        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3>How do I find a lawyer?</h3>
                <p>Use our search feature to find lawyers by location, specialization, and experience level.</p>
            </div>
            <div class="faq-item">
                <h3>How do I book an appointment?</h3>
                <p>Once you find a lawyer, visit their profile page and use the booking form to schedule a consultation.</p>
            </div>
            <div class="faq-item">
                <h3>Are all lawyers verified?</h3>
                <p>Yes, all lawyers on our platform are verified and licensed professionals.</p>
            </div>
            <div class="faq-item">
                <h3>How can I become a lawyer on this platform?</h3>
                <p>Click "Register" and select "Lawyer" to create your professional profile.</p>
            </div>
        </div>
    </div>
</div>

<style>
.contact-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.contact-header {
    text-align: center;
    margin-bottom: 3rem;
}

.contact-header h1 {
    color: #2c3e50;
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.contact-header p {
    color: #666;
    font-size: 1.1rem;
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.contact-info-section,
.contact-form-section {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.contact-info-section h2,
.contact-form-section h2 {
    color: #2c3e50;
    margin-bottom: 2rem;
    font-size: 1.8rem;
}

.contact-info {
    display: grid;
    gap: 2rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.contact-icon {
    width: 60px;
    height: 60px;
    background: #3498db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.contact-details h3 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.contact-details p {
    color: #666;
    line-height: 1.6;
}

.contact-form {
    display: grid;
    gap: 1rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
}

.btn-full {
    width: 100%;
    padding: 1rem;
    font-size: 1.1rem;
    margin-top: 1rem;
}

.contact-additional {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.faq-section h2 {
    color: #2c3e50;
    margin-bottom: 2rem;
    text-align: center;
}

.faq-item {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.faq-item h3 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.faq-item p {
    color: #666;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .contact-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .contact-header h1 {
        font-size: 2rem;
    }
}
</style>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Here you would typically send the form data to a server
    // For now, we'll just show a success message
    showAlert('Thank you for your message! We will get back to you soon.', 'success');
    
    // Reset the form
    this.reset();
});
</script>

<?php include __DIR__ . '/../Views/footer.php'; ?>
