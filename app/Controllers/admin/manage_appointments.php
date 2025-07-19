<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$page_title = 'Manage Appointments';
include __DIR__ . '/../../Views/header.php';
require_once __DIR__ . '/../../Models/db.php';

// Handle filters
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$date = $_GET['date'] ?? '';
$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = '(c.full_name LIKE ? OR l.full_name LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}
if ($status !== '' && in_array($status, ['pending','confirmed','completed','cancelled'])) {
    $where[] = 'a.status = ?';
    $params[] = $status;
    $types .= 's';
}
if ($date !== '') {
    $where[] = 'a.appointment_date = ?';
    $params[] = $date;
    $types .= 's';
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT a.*, 
               c.full_name as customer_name, c.email as customer_email, c.phone as customer_phone, 
               lp.id as lawyer_profile_id, l.full_name as lawyer_name, lp.specialization, lp.location
        FROM appointments a
        JOIN users c ON a.customer_id = c.id
        JOIN lawyer_profiles lp ON a.lawyer_id = lp.id
        JOIN users l ON lp.user_id = l.id
        $whereSql
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$appointments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}
mysqli_stmt_close($stmt);

// Fetch all customers
$customers = [];
$res = mysqli_query($conn, "SELECT id, full_name FROM users WHERE user_type = 'customer' AND is_active = 1 ORDER BY full_name");
while ($row = mysqli_fetch_assoc($res)) { $customers[] = $row; }
// Fetch all lawyers
$lawyers = [];
$res = mysqli_query($conn, "SELECT lp.id, u.full_name FROM lawyer_profiles lp JOIN users u ON lp.user_id = u.id WHERE u.is_active = 1 ORDER BY u.full_name");
while ($row = mysqli_fetch_assoc($res)) { $lawyers[] = $row; }

// Handle create appointment
$create_success = false;
$create_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_create_appointment'])) {
    $customer_id = intval($_POST['customer_id'] ?? 0);
    $lawyer_id = intval($_POST['lawyer_id'] ?? 0);
    $date = $_POST['appointment_date'] ?? '';
    $time = $_POST['appointment_time'] ?? '';
    $meeting_type = $_POST['meeting_type'] ?? 'office';
    $notes = trim($_POST['notes'] ?? '');
    if ($customer_id && $lawyer_id && $date && $time) {
        $sql = "INSERT INTO appointments (customer_id, lawyer_id, appointment_date, appointment_time, meeting_type, notes, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'iissss', $customer_id, $lawyer_id, $date, $time, $meeting_type, $notes);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: manage_appointments.php?created=1');
            exit;
        } else {
            $create_error = 'Failed to create appointment.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $create_error = 'All fields except notes are required.';
    }
}
?>
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fas fa-calendar"></i> Manage Appointments</h1>
    </div>
    <?php if (isset($_GET['created']) && $_GET['created'] == 1): ?>
        <div class="alert alert-success">Appointment created successfully!</div>
    <?php elseif ($create_error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($create_error); ?></div>
    <?php endif; ?>
    <div id="createApptModal" class="modal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.18);">
        <div class="modal-content" style="background:#fff;max-width:420px;margin:5% auto;padding:2rem 2rem 1.5rem 2rem;border-radius:10px;position:relative;">
            <span onclick="document.getElementById('createApptModal').style.display='none'" style="position:absolute;top:1rem;right:1.2rem;font-size:1.5rem;cursor:pointer;">&times;</span>
            <h2 style="margin-bottom:1.2rem;color:#2563eb;"><i class="fas fa-calendar-plus"></i> Create Appointment</h2>
            <form method="post">
                <input type="hidden" name="admin_create_appointment" value="1">
                <div class="form-group">
                    <label>Customer</label>
                    <select name="customer_id" required>
                        <option value="">Select customer</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lawyer</label>
                    <select name="lawyer_id" required>
                        <option value="">Select lawyer</option>
                        <?php foreach ($lawyers as $l): ?>
                            <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="appointment_date" required>
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="appointment_time" required>
                </div>
                <div class="form-group">
                    <label>Meeting Type</label>
                    <select name="meeting_type" required>
                        <option value="office">Office</option>
                        <option value="online">Online</option>
                        <option value="phone">Phone</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Create Appointment</button>
            </form>
        </div>
    </div>
    <form method="get" class="filter-form" style="margin-bottom:2rem; display:flex; flex-wrap:wrap; gap:1.2rem; align-items:end;">
        <div>
            <label for="search">Search (Customer/Lawyer):</label><br>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name..." style="min-width:180px;">
        </div>
        <div>
            <label for="status">Status:</label><br>
            <select id="status" name="status">
                <option value="">All</option>
                <option value="pending" <?php if($status==='pending') echo 'selected'; ?>>Pending</option>
                <option value="confirmed" <?php if($status==='confirmed') echo 'selected'; ?>>Confirmed</option>
                <option value="completed" <?php if($status==='completed') echo 'selected'; ?>>Completed</option>
                <option value="cancelled" <?php if($status==='cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>
        <div>
            <label for="date">Date:</label><br>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="manage_appointments.php" class="btn btn-secondary" style="margin-left:0.5em;">Reset</a>
        </div>
    </form>
    <div class="appointments-table-wrapper">
        <table class="appointments-table">
            <thead>
                <tr>
                    <th>Customer</th>
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
                    <td><?php echo htmlspecialchars($appt['customer_name']); ?></td>
                    <td><a href="../profile.php?id=<?php echo $appt['lawyer_profile_id']; ?>" class="lawyer-link"><?php echo htmlspecialchars($appt['lawyer_name']); ?></a></td>
                    <td><?php echo htmlspecialchars($appt['specialization']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($appt['appointment_date'])); ?></td>
                    <td><?php echo date('g:i A', strtotime($appt['appointment_time'])); ?></td>
                    <td><?php echo htmlspecialchars($appt['location']); ?></td>
                    <td><span class="badge badge-<?php echo $appt['status']; ?>"><?php echo ucfirst($appt['status']); ?></span></td>
                    <td>
                        <a href="../view_appointment.php?id=<?php echo $appt['id']; ?>" class="btn btn-xs btn-secondary">View</a>
                        <?php if ($appt['status'] !== 'cancelled'): ?>
                            <a href="#" class="btn btn-xs btn-danger admin-cancel-appt-btn" data-appt-id="<?php echo $appt['id']; ?>">Cancel</a>
                        <?php endif; ?>
                        <select class="admin-status-select" data-appt-id="<?php echo $appt['id']; ?>">
                            <option value="">Change Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($appointments)): ?>
                <tr><td colspan="8" style="text-align:center; color:#888;">No appointments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.admin-cancel-appt-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to cancel this appointment?')) return;
            fetch('../api/book_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=cancel&appointment_id=${encodeURIComponent(btn.dataset.apptId)}&admin=1`
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) setTimeout(() => window.location.reload(), 800);
            })
            .catch(() => alert('Request failed'));
        });
    });
    document.querySelectorAll('.admin-status-select').forEach(sel => {
        sel.addEventListener('change', function() {
            const newStatus = sel.value;
            const apptId = sel.dataset.apptId;
            if (!newStatus) return;
            if (!confirm('Change status to ' + newStatus + '?')) return;
            fetch('../api/book_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=set_status&appointment_id=${encodeURIComponent(apptId)}&new_status=${encodeURIComponent(newStatus)}&admin=1`
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) setTimeout(() => window.location.reload(), 800);
            })
            .catch(() => alert('Request failed'));
        });
    });
});
</script>
<?php include __DIR__ . '/../../Views/footer.php'; ?>
<style>
.appointments-table-wrapper { overflow-x: auto; }
.appointments-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; box-shadow: 0 2px 12px rgba(44,62,80,0.07); font-size: 0.98rem; margin-bottom: 2rem; }
.appointments-table th, .appointments-table td { padding: 0.7rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left; }
.appointments-table th { background: #f4f7fb; color: #2563eb; font-weight: 600; }
.appointments-table tr:last-child td { border-bottom: none; }
.lawyer-link { color: #2563eb; font-weight: 600; text-decoration: none; transition: color 0.18s; }
.lawyer-link:hover { color: #38bdf8; text-decoration: underline; }
.badge { display: inline-block; padding: 0.25em 0.7em; border-radius: 12px; font-size: 0.92em; font-weight: 600; color: #fff; }
.badge-pending { background: #fbbf24; }
.badge-confirmed { background: #38bdf8; }
.badge-completed { background: #22c55e; }
.badge-cancelled { background: #ef4444; }
.btn-xs { font-size: 0.88em; padding: 0.32em 0.85em; border-radius: 7px; margin-right: 0.18em; margin-bottom: 0.18em; display: inline-flex; align-items: center; gap: 0.3em; box-shadow: 0 1px 4px rgba(44,62,80,0.07); border: none; background: #f4f7fb; color: #2563eb; transition: background 0.18s, color 0.18s; text-decoration: none; }
.btn-xs.btn-danger { background: #ef4444; color: #fff; }
.btn-xs.btn-success { background: #22c55e; color: #fff; }
.btn-xs.btn-secondary { background: #6366f1; color: #fff; }
.btn-xs.btn-primary { background: #2563eb; color: #fff; }
.btn-xs:hover { opacity: 0.92; background: #e0e7ef; color: #23272f; }
.btn-xs.btn-danger:hover { background: #b91c1c; color: #fff; }
.btn-xs.btn-success:hover { background: #15803d; color: #fff; }
.btn-xs.btn-secondary:hover { background: #4338ca; color: #fff; }
.btn-xs.btn-primary:hover { background: #1d4ed8; color: #fff; }
.filter-form label { font-weight: 600; color: #2563eb; font-size: 0.97em; }
.filter-form input, .filter-form select { padding: 0.5em 0.7em; border-radius: 7px; border: 1.5px solid #e0e7ef; margin-top: 0.2em; }
.filter-form button, .filter-form a.btn { margin-top: 1.2em; }
@media (max-width: 900px) { .appointments-table th, .appointments-table td { padding: 0.5rem 0.5rem; } }
@media (max-width: 600px) { .dashboard-header h1 { font-size: 1.2rem; } .appointments-table { font-size: 0.93rem; } .appointments-table th, .appointments-table td { padding: 0.35rem 0.3rem; } }
</style> 