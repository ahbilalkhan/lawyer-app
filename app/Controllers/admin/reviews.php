<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$page_title = 'All Reviews';
include __DIR__ . '/../../Views/header.php';
require_once __DIR__ . '/../../Models/db.php';

// Filters
$customer = trim($_GET['customer'] ?? '');
$lawyer = trim($_GET['lawyer'] ?? '');
$rating = trim($_GET['rating'] ?? '');
$date_from = trim($_GET['date_from'] ?? '');
$date_to = trim($_GET['date_to'] ?? '');

$where = [];
$params = [];
$types = '';
if ($customer !== '') {
    $where[] = 'c.full_name LIKE ?';
    $params[] = "%$customer%";
    $types .= 's';
}
if ($lawyer !== '') {
    $where[] = 'l.full_name LIKE ?';
    $params[] = "%$lawyer%";
    $types .= 's';
}
if ($rating !== '' && is_numeric($rating)) {
    $where[] = 'r.rating = ?';
    $params[] = intval($rating);
    $types .= 'i';
}
if ($date_from !== '') {
    $where[] = 'DATE(r.created_at) >= ?';
    $params[] = $date_from;
    $types .= 's';
}
if ($date_to !== '') {
    $where[] = 'DATE(r.created_at) <= ?';
    $params[] = $date_to;
    $types .= 's';
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Pagination setup
$perPage = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total count with filters
$countSql = "SELECT COUNT(*) as cnt FROM reviews r JOIN users c ON r.customer_id = c.id JOIN lawyer_profiles lp ON r.lawyer_id = lp.id JOIN users l ON lp.user_id = l.id LEFT JOIN appointments a ON r.appointment_id = a.id $whereSql";
$countStmt = mysqli_prepare($conn, $countSql);
if ($types) mysqli_stmt_bind_param($countStmt, $types, ...$params);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalRow = mysqli_fetch_assoc($countResult);
$totalReviews = $totalRow['cnt'];
$totalPages = ceil($totalReviews / $perPage);
mysqli_stmt_close($countStmt);

// Fetch reviews with filters
$sql = "SELECT r.*, c.full_name AS customer_name, l.full_name AS lawyer_name, a.appointment_date FROM reviews r JOIN users c ON r.customer_id = c.id JOIN lawyer_profiles lp ON r.lawyer_id = lp.id JOIN users l ON lp.user_id = l.id LEFT JOIN appointments a ON r.appointment_id = a.id $whereSql ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
$allParams = $params;
$allTypes = $types . 'ii';
$allParams[] = $perPage;
$allParams[] = $offset;
mysqli_stmt_bind_param($stmt, $allTypes, ...$allParams);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<div class="dashboard-container">
    <h1><i class="fas fa-star"></i> All Reviews</h1>
    <form method="get" class="filter-form" style="margin-bottom:2rem;display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end;">
        <div>
            <label>Customer</label><br>
            <input type="text" name="customer" value="<?php echo htmlspecialchars($customer); ?>" placeholder="Customer name">
        </div>
        <div>
            <label>Lawyer</label><br>
            <input type="text" name="lawyer" value="<?php echo htmlspecialchars($lawyer); ?>" placeholder="Lawyer name">
        </div>
        <div>
            <label>Rating</label><br>
            <select name="rating">
                <option value="">All</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php if ($rating == $i) echo 'selected'; ?>><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div>
            <label>Date From</label><br>
            <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
        </div>
        <div>
            <label>Date To</label><br>
            <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="reviews.php" class="btn btn-secondary" style="margin-left:0.5rem;">Reset</a>
        </div>
    </form>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Lawyer</th>
                <th>Appointment Date</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Date</th>
                <th>Appointment</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['lawyer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo str_repeat('★', $row['rating']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['review_text'])); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                    <td>
                        <?php if ($row['appointment_id']): ?>
                            <a href="../view_appointment.php?id=<?php echo $row['appointment_id']; ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page-1])); ?>">&laquo; Prev</a>
        <?php endif; ?>
        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page+1])); ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>
<style>
.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 2rem;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(56,189,248,0.07);
}
.admin-table th, .admin-table td {
    padding: 0.8rem 1rem;
    border-bottom: 1px solid #eee;
    text-align: left;
    font-size: 0.97rem;
}
.admin-table th {
    background: #f8fafc;
    color: #2563eb;
    font-weight: 600;
}
.admin-table tr:last-child td {
    border-bottom: none;
}
.filter-form label {
    font-weight: 600;
    color: #2563eb;
    font-size: 0.97rem;
}
.filter-form input, .filter-form select {
    padding: 0.4rem 0.7rem;
    border-radius: 6px;
    border: 1.5px solid #e0e7ef;
    font-size: 0.97rem;
    margin-top: 0.2rem;
}
.filter-form .btn {
    margin-top: 0.2rem;
}
.pagination {
    margin: 1.5rem 0 0 0;
    text-align: center;
}
.pagination a {
    display: inline-block;
    margin: 0 0.5rem;
    padding: 0.4rem 1rem;
    background: #2563eb;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
}
.pagination span {
    margin: 0 0.7rem;
    color: #2563eb;
    font-weight: 600;
}
</style>
<?php include __DIR__ . '/../../Views/footer.php'; ?> 