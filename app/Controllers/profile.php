<?php
$page_title = "Lawyer Profile";
include __DIR__ . '/../Views/header.php';
require_once __DIR__ . '/../Models/db.php';

$lawyerId = intval($_GET['id'] ?? 0);
if ($lawyerId === 0) {
    header('Location: lawyers.php');
    exit;
}

// Get lawyer details
$sql = "SELECT lp.*, u.full_name, u.email, u.phone, u.profile_image, GROUP_CONCAT(DISTINCT ls.service_type) as services FROM lawyer_profiles lp JOIN users u ON lp.user_id = u.id LEFT JOIN lawyer_services ls ON lp.id = ls.lawyer_id WHERE lp.id = ? AND lp.is_verified = 1 GROUP BY lp.id LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $lawyerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lawyer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$lawyer) {
    header('Location: lawyers.php');
    exit;
}

// Get lawyer's reviews
$reviewsSql = "SELECT r.*, u.full_name as customer_name FROM reviews r JOIN users u ON r.customer_id = u.id WHERE r.lawyer_id = ? ORDER BY r.created_at DESC LIMIT 10";
$reviewsStmt = mysqli_prepare($conn, $reviewsSql);
mysqli_stmt_bind_param($reviewsStmt, 'i', $lawyerId);
mysqli_stmt_execute($reviewsStmt);
$reviewsResult = mysqli_stmt_get_result($reviewsStmt);
$reviews = [];
while ($row = mysqli_fetch_assoc($reviewsResult)) {
    $reviews[] = $row;
}
mysqli_stmt_close($reviewsStmt);

// Get lawyer's time slots
$timeSlotsSql = "SELECT * FROM time_slots WHERE lawyer_id = ? AND is_available = 1 ORDER BY day_of_week";
$timeSlotsStmt = mysqli_prepare($conn, $timeSlotsSql);
mysqli_stmt_bind_param($timeSlotsStmt, 'i', $lawyerId);
mysqli_stmt_execute($timeSlotsStmt);
$timeSlotsResult = mysqli_stmt_get_result($timeSlotsStmt);
$timeSlots = [];
while ($row = mysqli_fetch_assoc($timeSlotsResult)) {
    $timeSlots[] = $row;
}
mysqli_stmt_close($timeSlotsStmt);
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php if (!empty($lawyer['profile_image'])): ?>
                <img src="<?php echo htmlspecialchars($lawyer['profile_image']); ?>" alt="Profile Image" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" />
            <?php else: ?>
                <?php echo strtoupper(substr($lawyer['full_name'], 0, 1)); ?>
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($lawyer['full_name']); ?></h1>
            <p class="specialization"><?php echo htmlspecialchars($lawyer['specialization']); ?></p>
            <div class="rating">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?php echo $i <= $lawyer['rating'] ? 'filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                </div>
                <span class="rating-text"><?php echo $lawyer['rating']; ?>/5.0 (<?php echo $lawyer['total_reviews']; ?> reviews)</span>
            </div>
            <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($lawyer['location']); ?></p>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-details">
            <div class="detail-section">
                <h3><i class="fas fa-info-circle"></i> About</h3>
                <p><?php echo htmlspecialchars($lawyer['bio']); ?></p>
            </div>

            <div class="detail-section">
                <h3><i class="fas fa-briefcase"></i> Professional Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Experience:</strong> <?php echo $lawyer['experience_years']; ?> years
                    </div>
                    <div class="info-item">
                        <strong>License Number:</strong> <?php echo htmlspecialchars($lawyer['license_number']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Education:</strong> <?php echo htmlspecialchars($lawyer['education']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Consultation Fee:</strong> $<?php echo number_format($lawyer['consultation_fee'], 2); ?>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h3><i class="fas fa-gavel"></i> Legal Services</h3>
                <div class="services-list">
                    <?php if ($lawyer['services']): ?>
                        <?php foreach (explode(',', $lawyer['services']) as $service): ?>
                            <span class="service-tag"><?php echo ucfirst(str_replace('_', ' ', trim($service))); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-section">
                <h3><i class="fas fa-building"></i> Office Information</h3>
                <div class="office-info">
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($lawyer['office_address']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($lawyer['phone']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($lawyer['email']); ?></p>
                </div>
            </div>

            <div class="detail-section">
                <h3><i class="fas fa-clock"></i> Available Hours</h3>
                <div class="time-slots">
                    <?php if (!empty($timeSlots)): ?>
                        <?php foreach ($timeSlots as $slot): ?>
                            <div class="time-slot">
                                <strong><?php echo ucfirst($slot['day_of_week']); ?>:</strong>
                                <?php echo date('g:i A', strtotime($slot['start_time'])); ?> - 
                                <?php echo date('g:i A', strtotime($slot['end_time'])); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No available time slots set.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="detail-section">
                <h3><i class="fas fa-star"></i> Client Reviews</h3>
                <?php if (!empty($reviews)): ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <div class="review-header">
                                    <h4><?php echo htmlspecialchars($review['customer_name']); ?></h4>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                                <small class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="booking-sidebar">
            <div class="booking-card">
                <h3><i class="fas fa-calendar-plus"></i> Book Appointment</h3>
                <div class="fee-display">
                    <span class="fee-amount">$<?php echo number_format($lawyer['consultation_fee'], 2); ?></span>
                    <span class="fee-label">Consultation Fee</span>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form id="bookingForm" method="POST" action="api/book_appointment.php">
                        <input type="hidden" name="lawyer_id" value="<?php echo $lawyerId; ?>">
                        
                        <div class="form-group">
                            <label for="appointment_date">Select Date</label>
                            <input type="date" id="appointment_date" name="appointment_date" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_time">Select Time</label>
                            <select id="appointment_time" name="appointment_time" required>
                                <option value="">Choose time...</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="meeting_type">Meeting Type</label>
                            <select id="meeting_type" name="meeting_type" required>
                                <option value="office">Office Visit</option>
                                <option value="online">Online Meeting</option>
                                <option value="phone">Phone Call</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Brief description of your legal matter..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">
                            <i class="fas fa-calendar-check"></i> Book Appointment
                        </button>
                    </form>
                <?php else: ?>
                    <div class="login-prompt">
                        <p>Please login to book an appointment</p>
                        <a href="login.php" class="btn btn-primary btn-full">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.profile-header {
    display: flex;
    align-items: center;
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3498db, #2980b9);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
    font-weight: bold;
    margin-right: 2rem;
}

.profile-info h1 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.specialization {
    color: #3498db;
    font-size: 1.2rem;
    font-weight: 500;
    margin-bottom: 1rem;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stars {
    display: flex;
    gap: 0.1rem;
}

.star {
    color: #ddd;
    font-size: 1.2rem;
}

.star.filled {
    color: #f39c12;
}

.rating-text {
    font-size: 0.9rem;
    color: #666;
}

.location {
    color: #666;
    font-size: 1rem;
}

.profile-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

.profile-details {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.detail-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.detail-section:last-child {
    border-bottom: none;
}

.detail-section h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.services-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.service-tag {
    background: #3498db;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.office-info p {
    margin-bottom: 0.5rem;
}

.time-slots {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.time-slot {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    text-align: center;
}

.reviews-list {
    max-height: 400px;
    overflow-y: auto;
}

.review-item {
    padding: 1rem;
    border: 1px solid #eee;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.review-header h4 {
    margin: 0;
    color: #2c3e50;
}

.review-date {
    color: #666;
    font-style: italic;
}

.booking-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.booking-card {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.booking-card h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.fee-display {
    text-align: center;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.fee-amount {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    color: #27ae60;
}

.fee-label {
    color: #666;
    font-size: 0.9rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
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
}

.login-prompt {
    text-align: center;
    padding: 2rem;
    color: #666;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-avatar {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .profile-content {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .time-slots {
        grid-template-columns: 1fr;
    }
    
    .booking-sidebar {
        position: static;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(bookingForm);
            fetch('api/book_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'error');
                if (data.success) setTimeout(() => window.location.reload(), 1200);
            })
            .catch(() => showToast('Booking failed. Please try again.', 'error'));
        });
    }
});
</script>

<?php include __DIR__ . '/../Views/footer.php'; ?>
