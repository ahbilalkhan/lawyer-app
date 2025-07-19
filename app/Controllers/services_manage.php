<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'lawyer') {
    header('Location: login.php');
    exit;
}
$page_title = 'Manage Services & Availability';
include __DIR__ . '/../Views/header.php';
require_once __DIR__ . '/../Models/db.php';

$userId = $_SESSION['user_id'];
// Get lawyer profile id
$stmt = mysqli_prepare($conn, 'SELECT id FROM lawyer_profiles WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$profile = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$profile) {
    echo '<div class="dashboard-container"><div class="dashboard-header"><h1>No profile found</h1></div></div>';
    include __DIR__ . '/../Views/footer.php';
    exit;
}
$lawyerId = $profile['id'];

// Handle form submissions
$serviceMsg = $slotMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Service
    if (isset($_POST['add_service'])) {
        $service_type = $_POST['service_type'] ?? '';
        $description = $_POST['description'] ?? '';
        if ($service_type) {
            $stmt = mysqli_prepare($conn, 'INSERT INTO lawyer_services (lawyer_id, service_type, description) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'iss', $lawyerId, $service_type, $description);
            if (mysqli_stmt_execute($stmt)) {
                $serviceMsg = 'Service added!';
            } else {
                $serviceMsg = 'Error adding service.';
            }
            mysqli_stmt_close($stmt);
        }
    }
    // Remove Service
    if (isset($_POST['remove_service'])) {
        $service_id = intval($_POST['service_id'] ?? 0);
        if ($service_id) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM lawyer_services WHERE id = ? AND lawyer_id = ?');
            mysqli_stmt_bind_param($stmt, 'ii', $service_id, $lawyerId);
            if (mysqli_stmt_execute($stmt)) {
                $serviceMsg = 'Service removed!';
            } else {
                $serviceMsg = 'Error removing service.';
            }
            mysqli_stmt_close($stmt);
        }
    }
    // Add Time Slot
    if (isset($_POST['add_slot'])) {
        $day = $_POST['day_of_week'] ?? '';
        $start = $_POST['start_time'] ?? '';
        $end = $_POST['end_time'] ?? '';
        if ($day && $start && $end) {
            $stmt = mysqli_prepare($conn, 'INSERT INTO time_slots (lawyer_id, day_of_week, start_time, end_time, is_available) VALUES (?, ?, ?, ?, 1)');
            mysqli_stmt_bind_param($stmt, 'isss', $lawyerId, $day, $start, $end);
            if (mysqli_stmt_execute($stmt)) {
                $slotMsg = 'Time slot added!';
            } else {
                $slotMsg = 'Error adding time slot.';
            }
            mysqli_stmt_close($stmt);
        }
    }
    // Remove Time Slot
    if (isset($_POST['remove_slot'])) {
        $slot_id = intval($_POST['slot_id'] ?? 0);
        if ($slot_id) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM time_slots WHERE id = ? AND lawyer_id = ?');
            mysqli_stmt_bind_param($stmt, 'ii', $slot_id, $lawyerId);
            if (mysqli_stmt_execute($stmt)) {
                $slotMsg = 'Time slot removed!';
            } else {
                $slotMsg = 'Error removing time slot.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
// Fetch current services
$services = [];
$res = mysqli_query($conn, "SELECT * FROM lawyer_services WHERE lawyer_id = $lawyerId");
while ($row = mysqli_fetch_assoc($res)) {
    $services[] = $row;
}
// Fetch current time slots
$slots = [];
$res = mysqli_query($conn, "SELECT * FROM time_slots WHERE lawyer_id = $lawyerId ORDER BY FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), start_time");
while ($row = mysqli_fetch_assoc($res)) {
    $slots[] = $row;
}
$service_types = [
    'criminal' => 'Criminal Law',
    'divorce' => 'Divorce',
    'civil' => 'Civil Law',
    'corporate' => 'Corporate Law',
    'family' => 'Family Law',
    'property' => 'Property Law',
    'immigration' => 'Immigration Law',
    'tax' => 'Tax Law',
    'labor' => 'Labor Law',
    'intellectual_property' => 'Intellectual Property',
];
$days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
?>
<div class="dashboard-container services-manage-page">
    <div class="dashboard-header">
        <h1><i class="fas fa-briefcase"></i> Manage Services & Availability</h1>
        <p class="subtitle">Add, edit, or remove your legal services and set your available appointment slots.</p>
    </div>
    <div class="dashboard-flex">
        <div class="dashboard-card card-services">
            <h3><i class="fas fa-gavel"></i> Legal Services</h3>
            <?php if ($serviceMsg): ?><div class="alert alert-success"> <?php echo htmlspecialchars($serviceMsg); ?> </div><?php endif; ?>
            <form method="POST" class="inline-form form-services">
                <select name="service_type" required>
                    <option value="">Select Service</option>
                    <?php foreach ($service_types as $key => $label): ?>
                        <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="description" placeholder="Description (optional)">
                <button type="submit" name="add_service" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
            </form>
            <ul class="service-list">
                <?php foreach ($services as $srv): ?>
                    <?php $desc = trim($srv['description'] ?? ''); ?>
                    <li class="service-pill">
                        <span class="service-tag" <?php if ($desc): ?>data-tooltip="<?php echo htmlspecialchars($desc); ?>"<?php endif; ?>>
                            <i class="fas fa-tag"></i> <?php echo $service_types[$srv['service_type']] ?? ucfirst($srv['service_type']); ?>
                            <?php if ($desc): ?>
                                <span class="service-desc">- <?php echo htmlspecialchars($desc); ?></span>
                            <?php endif; ?>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="service_id" value="<?php echo $srv['id']; ?>">
                                <button type="submit" name="remove_service" class="btn btn-xs btn-danger" title="Remove"><i class="fas fa-times"></i></button>
                            </form>
                        </span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($services)): ?><li class="empty">No services added yet.</li><?php endif; ?>
            </ul>
        </div>
        <div class="dashboard-card card-slots">
            <h3><i class="fas fa-clock"></i> Availability (Time Slots)</h3>
            <?php if ($slotMsg): ?><div class="alert alert-success"> <?php echo htmlspecialchars($slotMsg); ?> </div><?php endif; ?>
            <form method="POST" class="inline-form form-slots">
                <select name="day_of_week" required>
                    <option value="">Day</option>
                    <?php foreach ($days as $d): ?>
                        <option value="<?php echo $d; ?>"><?php echo ucfirst($d); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="time" name="start_time" required>
                <input type="time" name="end_time" required>
                <button type="submit" name="add_slot" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
            </form>
            <ul class="slot-list">
                <?php foreach ($slots as $slot): ?>
                    <li>
                        <span class="slot-day"><i class="fas fa-calendar-day"></i> <?php echo ucfirst($slot['day_of_week']); ?></span>
                        <span class="slot-time"><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($slot['start_time'])); ?> - <?php echo date('g:i A', strtotime($slot['end_time'])); ?></span>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="slot_id" value="<?php echo $slot['id']; ?>">
                            <button type="submit" name="remove_slot" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($slots)): ?><li class="empty">No time slots set yet.</li><?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<style>
.services-manage-page .dashboard-header {
    background: linear-gradient(90deg, #2563eb 0%, #38bdf8 100%);
    color: white;
    border-radius: 12px;
    margin-bottom: 2.2rem;
    padding: 2rem 2.5rem 1.5rem 2.5rem;
    box-shadow: 0 4px 18px rgba(56,189,248,0.10);
}
.services-manage-page .dashboard-header h1 {
    font-size: 2.1rem;
    margin-bottom: 0.5rem;
}
.services-manage-page .dashboard-header .subtitle {
    font-size: 1.1rem;
    color: #e0e7ef;
    margin-top: 0.2rem;
}
.dashboard-flex {
    display: flex;
    gap: 2.5rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}
.dashboard-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(37,99,235,0.07);
    padding: 2rem 2rem 1.5rem 2rem;
    min-width: 340px;
    flex: 1 1 350px;
    margin-bottom: 2rem;
    border: 1.5px solid #e0e7ef;
}
.card-services { max-width: 500px; }
.card-slots { max-width: 500px; }
.dashboard-card h3 {
    color: #2563eb;
    margin-bottom: 1.2rem;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 0.6em;
}
.inline-form {
    display: flex;
    gap: 0.7em;
    margin-bottom: 1.2em;
    flex-wrap: wrap;
}
.inline-form select, .inline-form input {
    padding: 0.7em;
    border-radius: 7px;
    border: 2px solid #e0e7ef;
    font-size: 1rem;
    background: #f8fafc;
    transition: border 0.2s;
}
.inline-form select:focus, .inline-form input:focus {
    border-color: #38bdf8;
    background: #fff;
}
.btn-xs {
    font-size: 0.93em;
    padding: 0.32em 0.85em;
    border-radius: 7px;
    margin-left: 0.3em;
    background: #ef4444;
    color: #fff;
    border: none;
    transition: background 0.18s;
}
.btn-xs.btn-danger:hover { background: #b91c1c; color: #fff; }
.btn-primary i, .btn-danger i { margin-right: 0.2em; }
.service-list {
    list-style: none;
    padding: 0;
    margin-top: 0.5em;
    display: flex;
    flex-wrap: wrap;
    gap: 0.7em;
}
.service-list .service-pill {
    padding: 0;
    margin: 0;
    background: none;
    border: none;
    box-shadow: none;
    display: inline-block;
}
.service-list .service-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5em;
    background: #f3f6fa;
    color: #2563eb;
    border: 1.5px solid #dbeafe;
    border-radius: 999px;
    padding: 0.45em 0.95em 0.45em 0.95em;
    font-size: 0.98rem;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(56,189,248,0.07);
    transition: background 0.18s, border 0.18s, color 0.18s;
    position: relative;
    max-width: 100%;
}
.service-list .service-tag:hover {
    background: #e0e7ef;
    border-color: #38bdf8;
    color: #174ea6;
}
.service-list .service-desc {
    color: #64748b;
    font-size: 0.97rem;
    margin-left: 0.4em;
    font-weight: 400;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
}
.service-list .service-tag form {
    margin: 0 0 0 0.5em;
    display: flex;
    align-items: center;
}
.service-list .service-tag .btn-xs {
    background: #e0e7ef;
    color: #ef4444;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    margin-left: 0.1em;
    transition: background 0.18s, color 0.18s;
    padding: 0;
    box-shadow: none;
}
.service-list .service-tag .btn-xs:hover {
    background: #ef4444;
    color: #fff;
}
.slot-list {
    list-style: none;
    padding: 0;
    margin-top: 0.5em;
}
.slot-list li {
    margin-bottom: 0.7em;
    background: #f8fafc;
    padding: 0.9em 1.2em;
    border-radius: 9px;
    display: flex;
    align-items: center;
    gap: 1.2em;
    justify-content: space-between;
    box-shadow: 0 1px 4px rgba(44,62,80,0.04);
    border: 1px solid #e0e7ef;
    transition: box-shadow 0.18s, border 0.18s;
}
.slot-list li:hover {
    box-shadow: 0 4px 12px rgba(56,189,248,0.10);
    border-color: #38bdf8;
}
.slot-list .slot-day {
    color: #2563eb;
    font-weight: 600;
    font-size: 1.01rem;
    margin-right: 0.7em;
    display: inline-flex;
    align-items: center;
    gap: 0.4em;
}
.slot-list .slot-time {
    color: #38bdf8;
    font-size: 0.98rem;
    margin-right: 0.7em;
    display: inline-flex;
    align-items: center;
    gap: 0.4em;
}
.service-list .empty, .slot-list .empty {
    color: #aaa;
    font-style: italic;
    background: none;
    box-shadow: none;
    border: none;
    padding-left: 0;
}
.alert {
    background: #d1fae5;
    color: #065f46;
    padding: 0.7em 1em;
    border-radius: 7px;
    margin-bottom: 1em;
    font-weight: 500;
    border: 1.5px solid #38bdf8;
    box-shadow: 0 1px 4px rgba(56,189,248,0.08);
}
.alert-success { background: #d1fae5; color: #065f46; border-color: #22c55e; }
.service-tag[data-tooltip] {
    position: relative;
    cursor: pointer;
}
.service-tag[data-tooltip]:hover::after,
.service-tag[data-tooltip]:focus::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 50%;
    bottom: 120%;
    transform: translateX(-50%);
    background: #23272f;
    color: #fff;
    padding: 0.6em 1em;
    border-radius: 8px;
    white-space: pre-line;
    font-size: 0.97rem;
    box-shadow: 0 4px 18px rgba(44,62,80,0.13);
    z-index: 10;
    min-width: 180px;
    max-width: 320px;
    opacity: 1;
    pointer-events: none;
    transition: opacity 0.18s;
}
.service-tag[data-tooltip]:hover::before,
.service-tag[data-tooltip]:focus::before {
    content: '';
    position: absolute;
    left: 50%;
    bottom: 110%;
    transform: translateX(-50%);
    border: 8px solid transparent;
    border-top: 10px solid #23272f;
    z-index: 11;
}
@media (max-width: 900px) {
    .dashboard-flex { flex-direction: column; gap: 1.5rem; }
    .dashboard-card { min-width: unset; }
}
@media (max-width: 600px) {
    .service-list {
        gap: 0.4em;
    }
    .service-list .service-tag {
        font-size: 0.93rem;
        padding: 0.38em 0.7em 0.38em 0.7em;
        max-width: 98vw;
    }
    .service-list .service-desc {
        max-width: 90px;
    }
    .service-tag[data-tooltip]:hover::after,
    .service-tag[data-tooltip]:focus::after {
        font-size: 0.93rem;
        min-width: 120px;
        max-width: 90vw;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // For mobile: show tooltip on tap
  document.querySelectorAll('.service-tag[data-tooltip]').forEach(function(tag) {
    tag.addEventListener('touchstart', function(e) {
      this.classList.add('show-tooltip');
    });
    tag.addEventListener('touchend', function(e) {
      setTimeout(() => this.classList.remove('show-tooltip'), 2000);
    });
  });
});
</script>
<?php include __DIR__ . '/../Views/footer.php'; ?> 