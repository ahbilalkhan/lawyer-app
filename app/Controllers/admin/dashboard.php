<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$page_title = 'Admin Dashboard';
include __DIR__ . '/../../Views/header.php';
require_once __DIR__ . '/../../Models/db.php';

// Get counts for dashboard
$totalUsers = $totalLawyers = $totalAppointments = $totalReviews = 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalUsers = $row['cnt'];
}
$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE user_type = 'lawyer'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalLawyers = $row['cnt'];
}
$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM appointments");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalAppointments = $row['cnt'];
}
$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM reviews");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalReviews = $row['cnt'];
}
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fas fa-user-shield"></i> Admin Dashboard</h1>
        <p class="user-type-badge admin">Welcome, Admin!</p>
    </div>

    <div class="dashboard-grid">
        <!-- Summary Cards -->
        <div class="dashboard-card">
            <h3><i class="fas fa-users"></i> Users</h3>
            <div class="stat-number"><?php echo $totalUsers; ?></div>
            <div class="stat-label">Total Users</div>
            <a href="manage_users.php" class="btn btn-primary btn-full"><i class="fas fa-users-cog"></i> Manage Users</a>
        </div>
        <div class="dashboard-card">
            <h3><i class="fas fa-user-tie"></i> Lawyers</h3>
            <div class="stat-number"><?php echo $totalLawyers; ?></div>
            <div class="stat-label">Total Lawyers</div>
            <a href="manage_users.php?type=lawyer" class="btn btn-primary btn-full"><i class="fas fa-user-check"></i> Manage Lawyers</a>
        </div>
        <div class="dashboard-card">
            <h3><i class="fas fa-calendar-alt"></i> Appointments</h3>
            <div class="stat-number"><?php echo $totalAppointments; ?></div>
            <div class="stat-label">Total Appointments</div>
            <a href="manage_appointments.php" class="btn btn-primary btn-full"><i class="fas fa-calendar-alt"></i> Manage Appointments</a>
        </div>
        <div class="dashboard-card">
            <h3><i class="fas fa-star"></i> Reviews</h3>
            <div class="stat-number"><?php echo $totalReviews; ?></div>
            <div class="stat-label">Total Reviews</div>
            <a href="#" class="btn btn-primary btn-full"><i class="fas fa-star"></i> View Reviews</a>
        </div>
        <div class="dashboard-card">
            <h3><i class="fas fa-cogs"></i> Platform Settings</h3>
            <p>Configure platform-wide settings and preferences.</p>
            <a href="#" class="btn btn-secondary btn-full"><i class="fas fa-cogs"></i> Settings</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../Views/footer.php'; ?>

<style>
.user-type-badge.admin {
    background: #6366f1;
    color: #fff;
    font-size: 1rem;
    padding: 0.35rem 1.1rem;
    border-radius: 999px;
    display: inline-block;
    font-weight: 600;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(99,102,241,0.08);
}
.dashboard-header {
    margin-bottom: 2.5rem;
}
.dashboard-header h1 {
    color: #2563eb;
    font-size: 2.2rem;
    margin-bottom: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.7rem;
}
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 2rem;
}
.dashboard-card {
    background: white;
    padding: 2rem 1.5rem 1.5rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 6px 24px rgba(37,99,235,0.07);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-height: 220px;
}
.dashboard-card h3 {
    color: #2563eb;
    margin-bottom: 1.2rem;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.stat-number {
    font-size: 2.2rem;
    font-weight: bold;
    color: #38bdf8;
    margin-bottom: 0.3rem;
}
.stat-label {
    font-size: 1rem;
    color: #64748b;
    margin-bottom: 1.2rem;
}
.btn-full {
    width: 100%;
    margin-top: auto;
}
@media (max-width: 900px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style> 


