<?php
$page_title = "Dashboard";
include 'header.php';
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];
$fullName = $_SESSION['full_name'];

// Get user-specific data
try {
    if ($userType === 'lawyer') {
        // Get lawyer profile
        $profileSql = "SELECT lp.*, u.email, u.phone, u.address 
                      FROM lawyer_profiles lp 
                      JOIN users u ON lp.user_id = u.id 
                      WHERE lp.user_id = ?";
        $profileStmt = $pdo->prepare($profileSql);
        $profileStmt->execute([$userId]);
        $profile = $profileStmt->fetch();
        
        // Get lawyer's appointments
        $appointmentSql = "SELECT a.*, u.full_name as customer_name, u.phone as customer_phone 
                          FROM appointments a 
                          JOIN users u ON a.customer_id = u.id 
                          WHERE a.lawyer_id = ? 
                          ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                          LIMIT 10";
        $appointmentStmt = $pdo->prepare($appointmentSql);
        $appointmentStmt->execute([$profile['id']]);
        $appointments = $appointmentStmt->fetchAll();
        
        // Get lawyer's services
        $servicesSql = "SELECT service_type FROM lawyer_services WHERE lawyer_id = ?";
        $servicesStmt = $pdo->prepare($servicesSql);
        $servicesStmt->execute([$profile['id']]);
        $services = $servicesStmt->fetchAll();
        
        // Get reviews
        $reviewsSql = "SELECT r.*, u.full_name as customer_name 
                      FROM reviews r 
                      JOIN users u ON r.customer_id = u.id 
                      WHERE r.lawyer_id = ? 
                      ORDER BY r.created_at DESC 
                      LIMIT 5";
        $reviewsStmt = $pdo->prepare($reviewsSql);
        $reviewsStmt->execute([$profile['id']]);
        $reviews = $reviewsStmt->fetchAll();
        
    } else {
        // Get customer's appointments
        $appointmentSql = "SELECT a.*, lp.id as lawyer_profile_id, u.full_name as lawyer_name, 
                          lp.specialization, lp.consultation_fee, lp.location 
                          FROM appointments a 
                          JOIN lawyer_profiles lp ON a.lawyer_id = lp.id 
                          JOIN users u ON lp.user_id = u.id 
                          WHERE a.customer_id = ? 
                          ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                          LIMIT 10";
        $appointmentStmt = $pdo->prepare($appointmentSql);
        $appointmentStmt->execute([$userId]);
        $appointments = $appointmentStmt->fetchAll();
        
        // Get customer's reviews
        $reviewsSql = "SELECT r.*, u.full_name as lawyer_name 
                      FROM reviews r 
                      JOIN lawyer_profiles lp ON r.lawyer_id = lp.id 
                      JOIN users u ON lp.user_id = u.id 
                      WHERE r.customer_id = ? 
                      ORDER BY r.created_at DESC 
                      LIMIT 5";
        $reviewsStmt = $pdo->prepare($reviewsSql);
        $reviewsStmt->execute([$userId]);
        $reviews = $reviewsStmt->fetchAll();
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($fullName); ?>!</h1>
        <p class="user-type-badge <?php echo $userType; ?>">
            <i class="fas fa-<?php echo $userType === 'lawyer' ? 'user-tie' : 'user'; ?>"></i>
            <?php echo ucfirst($userType); ?>
        </p>
    </div>

    <?php if ($userType === 'lawyer'): ?>
        <!-- Lawyer Dashboard -->
        <div class="dashboard-grid">
            <!-- Profile Status -->
            <div class="dashboard-card">
                <h3><i class="fas fa-user-check"></i> Profile Status</h3>
                <div class="status-info">
                    <div class="status-item">
                        <span class="status-label">Verification:</span>
                        <span class="status-value <?php echo $profile['is_verified'] ? 'verified' : 'pending'; ?>">
                            <?php echo $profile['is_verified'] ? 'Verified' : 'Pending'; ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Availability:</span>
                        <span class="status-value <?php echo $profile['availability_status']; ?>">
                            <?php echo ucfirst($profile['availability_status']); ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Rating:</span>
                        <span class="status-value">
                            <?php echo $profile['rating']; ?>/5.0 (<?php echo $profile['total_reviews']; ?> reviews)
                        </span>
                    </div>
                </div>
                <a href="profile-edit.php" class="btn btn-primary">Edit Profile</a>
            </div>

            <!-- Quick Stats -->
            <div class="dashboard-card">
                <h3><i class="fas fa-chart-bar"></i> Quick Stats</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($appointments); ?></span>
                        <span class="stat-label">Total Appointments</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($reviews); ?></span>
                        <span class="stat-label">Reviews</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($services); ?></span>
                        <span class="stat-label">Services</span>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <div class="dashboard-card">
                <h3><i class="fas fa-briefcase"></i> Your Services</h3>
                <div class="services-list">
                    <?php foreach ($services as $service): ?>
                        <span class="service-tag"><?php echo ucfirst(str_replace('_', ' ', $service['service_type'])); ?></span>
                    <?php endforeach; ?>
                </div>
                <a href="services-manage.php" class="btn btn-secondary">Manage Services</a>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="dashboard-card">
            <h3><i class="fas fa-calendar-alt"></i> Recent Appointments</h3>
            <?php if (!empty($appointments)): ?>
                <div class="appointments-list">
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="appointment-item">
                            <div class="appointment-info">
                                <h4><?php echo htmlspecialchars($appointment['customer_name']); ?></h4>
                                <p><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></p>
                                <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['customer_phone']); ?></p>
                            </div>
                            <div class="appointment-status">
                                <span class="status-badge <?php echo $appointment['status']; ?>">
                                    <?php echo ucfirst($appointment['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No appointments yet.</p>
            <?php endif; ?>
            <a href="appointments.php" class="btn btn-primary">View All Appointments</a>
        </div>

        <!-- Recent Reviews -->
        <div class="dashboard-card">
            <h3><i class="fas fa-star"></i> Recent Reviews</h3>
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
                            <small><?php echo date('M j, Y', strtotime($review['created_at'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No reviews yet.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Customer Dashboard -->
        <div class="dashboard-grid">
            <!-- Quick Actions -->
            <div class="dashboard-card">
                <h3><i class="fas fa-search"></i> Quick Actions</h3>
                <div class="quick-actions">
                    <a href="lawyers.php" class="btn btn-primary">
                        <i class="fas fa-search"></i> Find Lawyers
                    </a>
                    <a href="appointments.php" class="btn btn-secondary">
                        <i class="fas fa-calendar"></i> My Appointments
                    </a>
                    <a href="profile-edit.php" class="btn btn-secondary">
                        <i class="fas fa-user-edit"></i> Edit Profile
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="dashboard-card">
                <h3><i class="fas fa-chart-line"></i> Your Activity</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($appointments); ?></span>
                        <span class="stat-label">Total Appointments</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($reviews); ?></span>
                        <span class="stat-label">Reviews Given</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="dashboard-card">
            <h3><i class="fas fa-calendar-alt"></i> Recent Appointments</h3>
            <?php if (!empty($appointments)): ?>
                <div class="appointments-list">
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="appointment-item">
                            <div class="appointment-info">
                                <h4><?php echo htmlspecialchars($appointment['lawyer_name']); ?></h4>
                                <p><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($appointment['specialization']); ?></p>
                                <p><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></p>
                                <p><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($appointment['location']); ?></p>
                            </div>
                            <div class="appointment-actions">
                                <span class="status-badge <?php echo $appointment['status']; ?>">
                                    <?php echo ucfirst($appointment['status']); ?>
                                </span>
                                <?php if ($appointment['status'] === 'completed'): ?>
                                    <a href="review.php?appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-small">
                                        <i class="fas fa-star"></i> Review
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No appointments yet. <a href="lawyers.php">Find a lawyer</a> to book your first appointment.</p>
            <?php endif; ?>
            <a href="appointments.php" class="btn btn-primary">View All Appointments</a>
        </div>

        <!-- Recent Reviews -->
        <div class="dashboard-card">
            <h3><i class="fas fa-star"></i> Your Reviews</h3>
            <?php if (!empty($reviews)): ?>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <h4><?php echo htmlspecialchars($review['lawyer_name']); ?></h4>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <small><?php echo date('M j, Y', strtotime($review['created_at'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No reviews yet.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 0;
}

.dashboard-header {
    text-align: center;
    margin-bottom: 3rem;
}

.dashboard-header h1 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.user-type-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 500;
    color: white;
}

.user-type-badge.lawyer {
    background: #3498db;
}

.user-type-badge.customer {
    background: #27ae60;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.dashboard-card {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.dashboard-card h3 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-info {
    margin-bottom: 1.5rem;
}

.status-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.status-label {
    font-weight: 500;
}

.status-value {
    font-weight: bold;
}

.status-value.verified {
    color: #27ae60;
}

.status-value.pending {
    color: #f39c12;
}

.status-value.available {
    color: #27ae60;
}

.status-value.busy {
    color: #e74c3c;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    color: #3498db;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.services-list {
    margin-bottom: 1.5rem;
}

.service-tag {
    display: inline-block;
    background: #ecf0f1;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}

.quick-actions {
    display: grid;
    gap: 1rem;
}

.appointments-list {
    margin-bottom: 1.5rem;
}

.appointment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border: 1px solid #eee;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.appointment-info h4 {
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.appointment-info p {
    margin-bottom: 0.25rem;
    color: #666;
    font-size: 0.9rem;
}

.appointment-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.status-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge.confirmed {
    background: #d4edda;
    color: #155724;
}

.status-badge.completed {
    background: #cce5ff;
    color: #004085;
}

.status-badge.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.btn-small {
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
}

.reviews-list {
    margin-bottom: 1.5rem;
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

.rating {
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

.no-data {
    text-align: center;
    color: #666;
    font-style: italic;
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .appointment-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .appointment-actions {
        align-items: flex-start;
        margin-top: 1rem;
    }
}
</style>

<?php include 'footer.php'; ?>
