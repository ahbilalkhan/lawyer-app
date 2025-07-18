<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'customer') {
    header('Location: login.php');
    exit;
}
$page_title = 'My Appointments';
include 'header.php';
require_once 'db.php';

$userId = $_SESSION['user_id'];
$sql = "SELECT a.*, lp.id as lawyer_profile_id, u.full_name as lawyer_name, lp.specialization, lp.location
        FROM appointments a
        JOIN lawyer_profiles lp ON a.lawyer_id = lp.id
        JOIN users u ON lp.user_id = u.id
        WHERE a.customer_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
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
                    <th>Lawyer</th>
                    <th>Specialization</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appt): ?>
                <tr>
                    <td><a href="profile.php?id=<?php echo $appt['lawyer_profile_id']; ?>" class="lawyer-link"><?php echo htmlspecialchars($appt['lawyer_name']); ?></a></td>
                    <td><?php echo htmlspecialchars($appt['specialization']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($appt['appointment_date'])); ?></td>
                    <td><?php echo date('g:i A', strtotime($appt['appointment_time'])); ?></td>
                    <td><?php echo htmlspecialchars($appt['location']); ?></td>
                    <td><span class="badge badge-<?php echo $appt['status']; ?>"><?php echo ucfirst($appt['status']); ?></span></td>
                    <td>
                        <a href="view_appointment.php?id=<?php echo $appt['id']; ?>" class="btn btn-xs btn-secondary">View</a>
                        <?php if ($appt['status'] === 'pending' || $appt['status'] === 'confirmed'): ?>
                            <a href="#" class="btn btn-xs btn-danger cancel-appt-btn" data-appt-id="<?php echo $appt['id']; ?>">Cancel</a>
                        <?php endif; ?>
                        <?php if ($appt['status'] === 'completed'): ?>
                            <a href="review.php?appointment_id=<?php echo $appt['id']; ?>" class="btn btn-xs btn-primary">Review</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                <tr><td colspan="7" style="text-align:center; color:#888;">No appointments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cancel-appt-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to cancel this appointment?')) return;
            fetch('api/book_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=cancel&appointment_id=${encodeURIComponent(btn.dataset.apptId)}`
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