<?php
$page_title = "Register";
include __DIR__ . '/../Views/header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<div class="form-container">
    <h2><i class="fas fa-user-plus"></i> Create Your Account</h2>
    
    <div class="user-type-selector">
        <label>
            <input type="radio" name="user_type" value="customer" checked>
            <span class="user-type-card">
                <i class="fas fa-user"></i>
                <h3>Customer</h3>
                <p>Looking for legal services</p>
            </span>
        </label>
        
        <label>
            <input type="radio" name="user_type" value="lawyer">
            <span class="user-type-card">
                <i class="fas fa-user-tie"></i>
                <h3>Lawyer</h3>
                <p>Providing legal services</p>
            </span>
        </label>
    </div>
    
    <form id="registerForm" method="POST">
        <input type="hidden" id="selected_user_type" name="user_type" value="customer">
        
        <!-- Common fields -->
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <!-- Lawyer-specific fields -->
        <div id="lawyer-fields" class="lawyer-fields" style="display: none;">
            <div class="form-group">
                <label for="specialization">Specialization</label>
                <select id="specialization" name="specialization">
                    <option value="">Select Specialization</option>
                    <option value="Criminal Law">Criminal Law</option>
                    <option value="Family Law">Family Law</option>
                    <option value="Corporate Law">Corporate Law</option>
                    <option value="Civil Law">Civil Law</option>
                    <option value="Property Law">Property Law</option>
                    <option value="Immigration Law">Immigration Law</option>
                    <option value="Tax Law">Tax Law</option>
                    <option value="Labor Law">Labor Law</option>
                    <option value="Intellectual Property">Intellectual Property</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="experience_years">Years of Experience</label>
                <input type="number" id="experience_years" name="experience_years" min="0" max="50">
            </div>
            
            <div class="form-group">
                <label for="license_number">License Number</label>
                <input type="text" id="license_number" name="license_number">
            </div>
            
            <div class="form-group">
                <label for="education">Education</label>
                <textarea id="education" name="education" rows="3" placeholder="e.g., Harvard Law School - JD"></textarea>
            </div>
            
            <div class="form-group">
                <label for="bio">Biography</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Tell us about your legal expertise and experience..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="consultation_fee">Consultation Fee ($)</label>
                <input type="number" id="consultation_fee" name="consultation_fee" step="0.01" min="0">
            </div>
            
            <div class="form-group">
                <label for="location">Practice Location</label>
                <input type="text" id="location" name="location" placeholder="e.g., New York, NY">
            </div>
            
            <div class="form-group">
                <label for="office_address">Office Address</label>
                <textarea id="office_address" name="office_address" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label for="services">Services Offered</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="services[]" value="criminal"> Criminal Law</label>
                    <label><input type="checkbox" name="services[]" value="family"> Family Law</label>
                    <label><input type="checkbox" name="services[]" value="divorce"> Divorce</label>
                    <label><input type="checkbox" name="services[]" value="civil"> Civil Law</label>
                    <label><input type="checkbox" name="services[]" value="corporate"> Corporate Law</label>
                    <label><input type="checkbox" name="services[]" value="property"> Property Law</label>
                    <label><input type="checkbox" name="services[]" value="immigration"> Immigration</label>
                    <label><input type="checkbox" name="services[]" value="tax"> Tax Law</label>
                    <label><input type="checkbox" name="services[]" value="labor"> Labor Law</label>
                    <label><input type="checkbox" name="services[]" value="intellectual_property"> Intellectual Property</label>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-user-plus"></i> Create Account
        </button>
    </form>
    
    <div class="form-footer">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<style>
.user-type-selector {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.user-type-selector label {
    flex: 1;
    cursor: pointer;
}

.user-type-selector input[type="radio"] {
    display: none;
}

.user-type-card {
    display: block;
    padding: 1.5rem;
    border: 2px solid #ddd;
    border-radius: 8px;
    text-align: center;
    transition: all 0.3s;
}

.user-type-card:hover {
    border-color: #3498db;
    background: #f8f9fa;
}

.user-type-card i {
    font-size: 2rem;
    color: #3498db;
    margin-bottom: 1rem;
}

.user-type-card h3 {
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.user-type-card p {
    color: #666;
    margin: 0;
}

.user-type-selector input[type="radio"]:checked + .user-type-card {
    border-color: #3498db;
    background: #e3f2fd;
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: normal;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
}

.lawyer-fields {
    border-top: 2px solid #eee;
    padding-top: 2rem;
    margin-top: 2rem;
}

.lawyer-fields .form-group {
    margin-bottom: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeRadios = document.querySelectorAll('input[name="user_type"]');
    const lawyerFields = document.getElementById('lawyer-fields');
    const selectedUserType = document.getElementById('selected_user_type');
    
    userTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            selectedUserType.value = this.value;
            
            if (this.value === 'lawyer') {
                lawyerFields.style.display = 'block';
                // Make lawyer fields required
                lawyerFields.querySelectorAll('input[required], select[required]').forEach(field => {
                    field.required = true;
                });
            } else {
                lawyerFields.style.display = 'none';
                // Make lawyer fields not required
                lawyerFields.querySelectorAll('input, select').forEach(field => {
                    field.required = false;
                });
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../Views/footer.php'; ?>
