<?php
// review.php - Customer submits review for a completed appointment
session_start();
require_once __DIR__ . '/../Models/db.php';
include __DIR__ . '/../Views/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['user_id'];
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;
$success = false;
$error = '';

// Fetch appointment and lawyer info
if ($appointment_id > 0) {
    $sql = "SELECT a.*, lp.id AS lawyer_profile_id, u.full_name AS lawyer_name FROM appointments a JOIN lawyer_profiles lp ON a.lawyer_id = lp.id JOIN users u ON lp.user_id = u.id WHERE a.id = ? AND a.customer_id = ? AND a.status = 'completed'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $appointment_id, $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $appointment = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if (!$appointment) {
        $error = 'Invalid or unauthorized appointment.';
    } else {
        // Check if already reviewed
        $sql = "SELECT id FROM reviews WHERE appointment_id = ? AND customer_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $appointment_id, $customer_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'You have already reviewed this appointment.';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $error = 'No appointment specified.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $review_text = trim($_POST['review_text'] ?? '');
    if ($rating < 1 || $rating > 5) {
        $error = 'Please provide a valid rating.';
    } elseif (empty($review_text)) {
        $error = 'Please enter your review.';
    } else {
        $lawyer_id = $appointment['lawyer_profile_id'];
        $sql = "INSERT INTO reviews (customer_id, lawyer_id, appointment_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'iiiis', $customer_id, $lawyer_id, $appointment_id, $rating, $review_text);
        if (mysqli_stmt_execute($stmt)) {
            $success = true;
        } else {
            $error = 'Failed to submit review. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<div class="form-container">
    <h2><i class="fas fa-star"></i> Review Appointment</h2>
    <?php if ($success): ?>
        <div class="alert alert-success">Review submitted successfully! <a href="dashboard.php">Return to Dashboard</a></div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (!empty($appointment)): ?>
        <form method="POST">
            <div class="form-group">
                <label>Lawyer</label>
                <input type="text" value="<?php echo htmlspecialchars($appointment['lawyer_name']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="text" value="<?php echo htmlspecialchars($appointment['appointment_date']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Rating</label>
                <select name="rating" required>
                    <option value="">Select rating</option>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="review_text">Review</label>
                <textarea name="review_text" id="review_text" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../Views/footer.php'; ?> 