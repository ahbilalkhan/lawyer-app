<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$page_title = 'View Appointment';
include __DIR__ . '/../Views/header.php';
require_once __DIR__ . '/../Models/db.php';

$appointmentId = intval($_GET['id'] ?? 0);
if (!$appointmentId) {
    echo '<div class="dashboard-container"><div class="dashboard-header"><h1>Invalid appointment</h1></div></div>';
    include __DIR__ . '/../Views/footer.php';
    exit;
}
$stmt = mysqli_prepare($conn, 'SELECT a.*, u1.full_name as customer_name, u1.email as customer_email, u1.phone as customer_phone, u1.id as customer_id, lp.id as lawyer_profile_id, u2.full_name as lawyer_name, u2.email as lawyer_email, u2.phone as lawyer_phone, u2.id as lawyer_user_id FROM appointments a JOIN users u1 ON a.customer_id = u1.id JOIN lawyer_profiles lp ON a.lawyer_id = lp.id JOIN users u2 ON lp.user_id = u2.id WHERE a.id = ?');
mysqli_stmt_bind_param($stmt, 'i', $appointmentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$appt = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$appt) {
    echo '<div class="dashboard-container"><div class="dashboard-header"><h1>Appointment not found</h1></div></div>';
    include __DIR__ . '/../Views/footer.php';
    exit;
}
$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'] ?? '';
if ($userId != $appt['customer_id'] && $userId != $appt['lawyer_user_id'] && $userType !== 'admin') {
    echo '<div class="dashboard-container"><div class="dashboard-header"><h1>Unauthorized</h1></div></div>';
    include __DIR__ . '/../Views/footer.php';
    exit;
}
?>
<?php
$showActions = false;
if (
    ($userType === 'lawyer' && ($appt['status'] === 'pending' || $appt['status'] === 'confirmed')) ||
    ($userType === 'customer' && ($appt['status'] === 'pending' || $appt['status'] === 'confirmed')) ||
    ($userType === 'customer' && $appt['status'] === 'completed')
) {
    $showActions = true;
}
?>
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fas fa-calendar"></i> Appointment Details</h1>
    </div>
    <div class="appointment-sections three-cols">
        <div class="section summary-section">
            <h2>Appointment Summary</h2>
            <table class="summary-table">
                <tr><th>ID</th><td>#<?php echo $appt['id']; ?></td></tr>
                <tr><th>Status</th><td>
<select class="status-select" data-appt-id="<?php echo $appt['id']; ?>" data-current-status="<?php echo $appt['status']; ?>">
    <option value="pending" <?php if ($appt['status']==='pending') echo 'selected'; ?>>Pending</option>
    <option value="confirmed" <?php if ($appt['status']==='confirmed') echo 'selected'; ?>>Confirmed</option>
    <option value="completed" <?php if ($appt['status']==='completed') echo 'selected'; ?>>Completed</option>
    <option value="cancelled" <?php if ($appt['status']==='cancelled') echo 'selected'; ?>>Cancelled</option>
</select>
</td></tr>
                <tr><th>Date</th><td><?php echo date('Y-m-d', strtotime($appt['appointment_date'])); ?></td></tr>
                <tr><th>Time</th><td><?php echo date('g:i A', strtotime($appt['appointment_time'])); ?></td></tr>
                <tr><th>Meeting Type</th><td><?php echo ucfirst($appt['meeting_type']); ?></td></tr>
                <?php if (isset($appt['consultation_fee'])): ?>
                <tr><th>Consultation Fee</th><td>$<?php echo number_format($appt['consultation_fee'], 2); ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
        <div class="section parties-section mid-section">
            <div class="party-box">
                <h3>Lawyer</h3>
                <div><strong>Name:</strong> <a href="profile.php?id=<?php echo $appt['lawyer_profile_id']; ?>" class="lawyer-link"><?php echo htmlspecialchars($appt['lawyer_name']); ?></a></div>
                <div><strong>Email:</strong> <?php echo htmlspecialchars($appt['lawyer_email']); ?></div>
                <div><strong>Phone:</strong> <?php echo htmlspecialchars($appt['lawyer_phone']); ?></div>
            </div>
            <div class="party-box">
                <h3>Customer</h3>
                <div><strong>Name:</strong> <?php echo htmlspecialchars($appt['customer_name']); ?></div>
                <div><strong>Email:</strong> <?php echo htmlspecialchars($appt['customer_email']); ?></div>
                <div><strong>Phone:</strong> <?php echo htmlspecialchars($appt['customer_phone']); ?></div>
            </div>
        </div>
        <?php if ($showActions): ?>
        <div class="section actions-section">
            <div class="actions-row actions-row-side">
                <?php if ($userType === 'lawyer' && $appt['status'] === 'pending'): ?>
                    <a href="#" class="btn btn-success btn-action" data-appt-id="<?php echo $appt['id']; ?>" data-action="accept">Accept</a>
                    <a href="#" class="btn btn-danger btn-action" data-appt-id="<?php echo $appt['id']; ?>" data-action="cancel">Cancel</a>
                <?php elseif ($userType === 'lawyer' && $appt['status'] === 'confirmed'): ?>
                    <a href="#" class="btn btn-primary btn-action" data-appt-id="<?php echo $appt['id']; ?>" data-action="complete">Mark as Completed</a>
                <?php elseif ($userType === 'customer' && ($appt['status'] === 'pending' || $appt['status'] === 'confirmed')): ?>
                    <a href="#" class="btn btn-danger btn-action" data-appt-id="<?php echo $appt['id']; ?>" data-action="cancel">Cancel</a>
                <?php endif; ?>
                <?php if ($userType === 'customer' && $appt['status'] === 'completed'): ?>
                    <a href="review.php?appointment_id=<?php echo $appt['id']; ?>" class="btn btn-primary">Review</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="section notes-section">
        <h3>Notes</h3>
        <div class="notes-box"><?php echo $appt['notes'] ? htmlspecialchars($appt['notes']) : '<span style="color:#aaa">No notes provided.</span>'; ?></div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-action').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const apptId = btn.dataset.apptId;
            const action = btn.dataset.action;
            if (!apptId || !action) return;
            let msg = '';
            if (action === 'accept') msg = 'Accept this appointment?';
            else if (action === 'cancel') msg = 'Cancel this appointment?';
            else if (action === 'complete') msg = 'Mark this appointment as completed?';
            else msg = 'Are you sure?';
            if (!confirm(msg)) return;
            fetch('api/book_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=${action}&appointment_id=${encodeURIComponent(apptId)}`
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'error');
                if (data.success) setTimeout(() => window.location.reload(), 800);
            })
            .catch(() => showToast('Request failed', 'error'));
        });
    });
    function updateStatusSelectStyle(sel) {
        sel.classList.remove('pending', 'confirmed', 'completed', 'cancelled');
        sel.classList.add(sel.value);
    }
    document.querySelectorAll('.status-select').forEach(function(sel) {
        updateStatusSelectStyle(sel);
        sel.addEventListener('change', function() {
            updateStatusSelectStyle(this);
            const apptId = this.dataset.apptId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;
            if (newStatus === currentStatus) return;
            this.disabled = true;
            fetch('api/book_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=set_status&appointment_id=${encodeURIComponent(apptId)}&new_status=${encodeURIComponent(newStatus)}`
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'error');
                if (data.success) setTimeout(() => window.location.reload(), 800);
                else this.disabled = false;
            })
            .catch(() => { showToast('Request failed', 'error'); this.disabled = false; });
        });
        sel.addEventListener('input', function() { updateStatusSelectStyle(this); });
    });
});
</script>
<style>
.appointment-sections.three-cols {
    display: flex;
    flex-wrap: wrap;
    gap: 2.5rem;
    margin-top: 2rem;
}
.summary-section {
    max-width: 320px;
    min-width: 220px;
}
.parties-section {
    min-width: 260px;
    max-width: 420px;
    flex: 2 1 340px;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}
.actions-section {
    min-width: 180px;
    max-width: 220px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: flex-start;
    gap: 1.2rem;
    background: #fff;
    box-shadow: 0 2px 12px rgba(44,62,80,0.07);
    border-radius: 12px;
    padding: 1.5rem 1.2rem 1.2rem 1.2rem;
    margin-bottom: 1.5rem;
}
.actions-row-side {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
    align-items: flex-end;
}
.section {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(44,62,80,0.07);
    padding: 1.5rem 2rem 1.2rem 2rem;
    margin-bottom: 1.5rem;
}
.notes-section {
    max-width: 100%;
    margin-top: 0;
}
.notes-box {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem 1.2rem;
    min-height: 60px;
    font-size: 1.04rem;
    color: #23272f;
    box-shadow: 0 1px 4px rgba(44,62,80,0.05);
}
.badge {
    display: inline-block;
    padding: 0.25em 0.7em;
    border-radius: 12px;
    font-size: 0.92em;
    font-weight: 600;
    color: #fff;
}
.badge-pending { background: #fbbf24; }
.badge-confirmed { background: #38bdf8; }
.badge-completed { background: #22c55e; }
.badge-cancelled { background: #ef4444; }
.lawyer-link {
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.18s;
}
.lawyer-link:hover {
    color: #38bdf8;
    text-decoration: underline;
}
.btn {
    font-size: 1rem;
    padding: 0.5rem 1.3rem;
    border-radius: 7px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    margin-right: 0.5rem;
    margin-bottom: 0.2rem;
    transition: background 0.18s, color 0.18s;
    box-shadow: 0 1px 4px rgba(44,62,80,0.07);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4em;
}
.btn-success { background: #22c55e; color: #fff; }
.btn-danger { background: #ef4444; color: #fff; }
.btn-primary { background: #2563eb; color: #fff; }
.btn:hover { opacity: 0.92; }
.btn-success:hover { background: #15803d; }
.btn-danger:hover { background: #b91c1c; }
.btn-primary:hover { background: #1d4ed8; }
.summary-table th, .summary-table td {
    padding: 0.7rem 1rem;
    border-bottom: 1px solid #e0e7ef;
    text-align: left;
    font-size: 1.04rem;
    vertical-align: middle;
    width: 1%;
    white-space: nowrap;
}
.summary-table th {
    background: #f4f7fb;
    color: #2563eb;
    font-weight: 600;
    width: 140px;
    min-width: 120px;
    text-align: left;
}
.summary-table td {
    width: auto;
    min-width: 120px;
    text-align: left;
}
.status-select {
    appearance: none;
    -webkit-appearance: none;
    border: none;
    outline: none;
    font-size: 0.92em;
    font-weight: 600;
    color: #fff;
    border-radius: 12px;
    padding: 0.25em 1.7em 0.25em 0.7em;
    background: #fbbf24;
    transition: background 0.18s, color 0.18s;
    min-width: 110px;
    box-shadow: 0 1px 4px rgba(44,62,80,0.07);
    cursor: pointer;
    margin: 0;
    display: inline-block;
    text-align: center;
}
.status-select.pending { background: #fbbf24; color: #fff; }
.status-select.confirmed { background: #38bdf8; color: #fff; }
.status-select.completed { background: #22c55e; color: #fff; }
.status-select.cancelled { background: #ef4444; color: #fff; }
.status-select:disabled { opacity: 0.7; cursor: not-allowed; }
.status-select option { color: #23272f; background: #fff; }
@media (max-width: 1200px) {
    .appointment-sections.three-cols { flex-direction: column; gap: 1.5rem; }
    .summary-section, .parties-section, .actions-section { max-width: 100%; }
    .actions-section { align-items: flex-start; }
    .actions-row-side { align-items: flex-start; }
}
@media (max-width: 700px) {
    .section { padding: 1rem 0.7rem; }
    .party-box, .notes-box { padding: 0.7rem 0.7rem; }
    .actions-row, .actions-row-side { gap: 0.5rem; }
}
</style> 