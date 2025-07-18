<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'lawyer') {
    header('Location: login.php');
    exit;
}
$page_title = 'My Appointments';
include 'header.php';
require_once 'db.php';

$userId = $_SESSION['user_id'];
// Get lawyer profile id
$stmt = $pdo->prepare('SELECT id FROM lawyer_profiles WHERE user_id = ?');
$stmt->execute([$userId]);
$profile = $stmt->fetch();
if (!$profile) {
    echo '<div class="dashboard-container"><div class="dashboard-header"><h1>No profile found</h1></div></div>';
    include 'footer.php';
    exit;
}
$lawyerId = $profile['id'];
$sql = "SELECT a.*, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
        FROM appointments a
        JOIN users u ON a.customer_id = u.id
        WHERE a.lawyer_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$lawyerId]);
$appointments = $stmt->fetchAll();
?>
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fas fa-calendar"></i> My Appointments</h1>
    </div>
    <div class="appointments-table-wrapper">
        <table class="appointments-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appt): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appt['customer_name']); ?></td>
                    <td>
                        <div><?php echo htmlspecialchars($appt['customer_email']); ?></div>
                        <div><?php echo htmlspecialchars($appt['customer_phone']); ?></div>
                    </td>
                    <td><?php echo date('Y-m-d', strtotime($appt['appointment_date'])); ?></td>
                    <td><?php echo date('g:i A', strtotime($appt['appointment_time'])); ?></td>
                    <td><span class="badge badge-<?php echo $appt['status']; ?>"><?php echo ucfirst($appt['status']); ?></span></td>
                    <td>
                        <a href="view_appointment.php?id=<?php echo $appt['id']; ?>" class="btn btn-xs btn-secondary">View</a>
                        <?php if ($appt['status'] === 'pending'): ?>
                            <a href="#" class="btn btn-xs btn-success appt-action-btn" data-appt-id="<?php echo $appt['id']; ?>" data-action="accept">Accept</a>
                            <a href="#" class="btn btn-xs btn-danger appt-action-btn" data-appt-id="<?php echo $appt['id']; ?>" data-action="cancel">Cancel</a>
                        <?php else: ?>
                            <span style="color:#888;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                <tr><td colspan="6" style="text-align:center; color:#888;">No appointments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.appt-action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const apptId = btn.dataset.apptId;
            const action = btn.dataset.action;
            if (!apptId || !action) return;
            let msg = action === 'accept' ? 'Accept this appointment?' : 'Cancel this appointment?';
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
});
</script>
<?php include 'footer.php'; ?>
<style>
.appointments-table-wrapper {
    overflow-x: auto;
}
.appointments-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(44,62,80,0.07);
    font-size: 0.98rem;
    margin-bottom: 2rem;
}
.appointments-table th, .appointments-table td {
    padding: 0.7rem 1rem;
    border-bottom: 1px solid #f0f0f0;
    text-align: left;
}
.appointments-table th {
    background: #f4f7fb;
    color: #2563eb;
    font-weight: 600;
}
.appointments-table tr:last-child td {
    border-bottom: none;
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
.btn-xs {
    font-size: 0.88em;
    padding: 0.32em 0.85em;
    border-radius: 7px;
    margin-right: 0.18em;
    margin-bottom: 0.18em;
    display: inline-flex;
    align-items: center;
    gap: 0.3em;
    box-shadow: 0 1px 4px rgba(44,62,80,0.07);
    border: none;
    background: #f4f7fb;
    color: #2563eb;
    transition: background 0.18s, color 0.18s;
    text-decoration: none;
}
.btn-xs.btn-danger { background: #ef4444; color: #fff; }
.btn-xs.btn-success { background: #22c55e; color: #fff; }
.btn-xs.btn-secondary { background: #6366f1; color: #fff; }
.btn-xs.btn-primary { background: #2563eb; color: #fff; }
.btn-xs:hover { opacity: 0.92; background: #e0e7ef; color: #23272f; }
.btn-xs.btn-danger:hover { background: #b91c1c; color: #fff; }
.btn-xs.btn-success:hover { background: #15803d; color: #fff; }
.btn-xs.btn-secondary:hover { background: #4338ca; color: #fff; }
.btn-xs.btn-primary:hover { background: #1d4ed8; color: #fff; }
@media (max-width: 900px) {
    .appointments-table th, .appointments-table td { padding: 0.5rem 0.5rem; }
}
@media (max-width: 600px) {
    .dashboard-header h1 { font-size: 1.2rem; }
    .appointments-table { font-size: 0.93rem; }
    .appointments-table th, .appointments-table td { padding: 0.35rem 0.3rem; }
}
</style> 