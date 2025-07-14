// Main JavaScript for Lawyer App

// DOM Elements
const searchForm = document.getElementById('searchForm');
const lawyersGrid = document.getElementById('lawyersGrid');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    // Load lawyers on page load
    if (lawyersGrid) {
        loadLawyers();
    }
    
    // Setup event listeners
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Search form
    if (searchForm) {
        searchForm.addEventListener('submit', handleSearch);
    }
    
    // Login form
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Register form
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
    
    // Time slot selection
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('time-slot')) {
            selectTimeSlot(e.target);
        }
    });
}

// Load all lawyers
async function loadLawyers() {
    try {
        const response = await fetch('api/lawyers.php');
        const lawyers = await response.json();
        
        if (lawyers.success) {
            displayLawyers(lawyers.data);
        } else {
            showAlert('Error loading lawyers', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error loading lawyers', 'error');
    }
}

// Display lawyers in grid
function displayLawyers(lawyers) {
    if (!lawyersGrid) return;
    
    lawyersGrid.innerHTML = '';
    
    lawyers.forEach(lawyer => {
        const lawyerCard = createLawyerCard(lawyer);
        lawyersGrid.appendChild(lawyerCard);
    });
}

// Create lawyer card element
function createLawyerCard(lawyer) {
    const card = document.createElement('div');
    card.className = 'lawyer-card';
    
    const stars = generateStars(lawyer.rating);
    const services = lawyer.services ? lawyer.services.split(',') : [];
    
    card.innerHTML = `
        <div class="lawyer-avatar">
            ${lawyer.full_name.charAt(0).toUpperCase()}
        </div>
        <div class="lawyer-info">
            <h3>${lawyer.full_name}</h3>
            <div class="lawyer-specialization">${lawyer.specialization}</div>
            <div class="lawyer-location">📍 ${lawyer.location}</div>
            <div class="lawyer-rating">
                <span class="stars">${stars}</span>
                <span>(${lawyer.total_reviews} reviews)</span>
            </div>
            <div class="lawyer-services">
                ${services.map(service => `<span class="service-tag">${service.trim()}</span>`).join('')}
            </div>
            <div class="lawyer-fee">
                <strong>Consultation Fee: $${lawyer.consultation_fee}</strong>
            </div>
            <a href="profile.php?id=${lawyer.id}" class="btn btn-primary">View Profile</a>
        </div>
    `;
    
    return card;
}

// Generate star rating
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 !== 0;
    let stars = '';
    
    for (let i = 0; i < fullStars; i++) {
        stars += '★';
    }
    
    if (halfStar) {
        stars += '☆';
    }
    
    const remainingStars = 5 - Math.ceil(rating);
    for (let i = 0; i < remainingStars; i++) {
        stars += '☆';
    }
    
    return stars;
}

// Handle search form submission
async function handleSearch(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const searchParams = new URLSearchParams(formData);
    
    try {
        const response = await fetch(`api/search.php?${searchParams}`);
        const result = await response.json();
        
        if (result.success) {
            displayLawyers(result.data);
        } else {
            showAlert('No lawyers found matching your criteria', 'info');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error searching lawyers', 'error');
    }
}

// Handle login form submission
async function handleLogin(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('api/login.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Login successful!', 'success');
            setTimeout(() => {
                window.location.href = result.redirect || 'index.php';
            }, 1500);
        } else {
            showAlert(result.message || 'Login failed', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Login error', 'error');
    }
}

// Handle register form submission
async function handleRegister(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Basic validation
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    
    if (password !== confirmPassword) {
        showAlert('Passwords do not match', 'error');
        return;
    }
    
    try {
        const response = await fetch('api/register.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Registration successful!', 'success');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1500);
        } else {
            showAlert(result.message || 'Registration failed', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Registration error', 'error');
    }
}

// Select time slot
function selectTimeSlot(slotElement) {
    // Remove previous selections
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    // Add selection to clicked slot
    slotElement.classList.add('selected');
    
    // Store selected time
    const selectedTime = slotElement.dataset.time;
    const hiddenInput = document.getElementById('selectedTime');
    if (hiddenInput) {
        hiddenInput.value = selectedTime;
    }
}

// Book appointment
async function bookAppointment(lawyerId) {
    const appointmentDate = document.getElementById('appointmentDate').value;
    const selectedTime = document.getElementById('selectedTime').value;
    const meetingType = document.getElementById('meetingType').value;
    const notes = document.getElementById('notes').value;
    
    if (!appointmentDate || !selectedTime) {
        showAlert('Please select date and time', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('lawyer_id', lawyerId);
    formData.append('appointment_date', appointmentDate);
    formData.append('appointment_time', selectedTime);
    formData.append('meeting_type', meetingType);
    formData.append('notes', notes);
    
    try {
        const response = await fetch('api/book_appointment.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Appointment booked successfully!', 'success');
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1500);
        } else {
            showAlert(result.message || 'Booking failed', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Booking error', 'error');
    }
}

// Show alert message
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    // Insert at top of main content
    const mainContent = document.querySelector('.main-content') || document.body;
    mainContent.insertBefore(alert, mainContent.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Format time for display
function formatTime(timeString) {
    const time = new Date(`2000-01-01T${timeString}`);
    return time.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Check if user is logged in
function isLoggedIn() {
    return sessionStorage.getItem('user_id') !== null;
}

// Logout user
function logout() {
    sessionStorage.clear();
    window.location.href = 'login.php';
}

// Initialize user session
function initializeSession() {
    const userId = sessionStorage.getItem('user_id');
    const userType = sessionStorage.getItem('user_type');
    
    if (userId && userType) {
        updateNavigation(userType);
    }
}

// Update navigation based on user type
function updateNavigation(userType) {
    const authButtons = document.querySelector('.auth-buttons');
    if (authButtons) {
        authButtons.innerHTML = `
            <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
            <button onclick="logout()" class="btn btn-primary">Logout</button>
        `;
    }
}
