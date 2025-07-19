<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$page_title = 'Manage Users';
include __DIR__ . '/../../Views/header.php';
require_once __DIR__ . '/../../Models/db.php';

// Handle search and filter
$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$where = [];
$params = [];
$paramTypes = '';
if ($search) {
    $where[] = "(username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $paramTypes .= 'sss';
}
if ($type) {
    $where[] = "user_type = ?";
    $params[] = $type;
    $paramTypes .= 's';
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT id, username, full_name, email, user_type, is_active, created_at FROM users $whereSql ORDER BY created_at DESC";
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_stmt_close($stmt);
} else {
    $result = mysqli_query($conn, $sql);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fas fa-users-cog"></i> Manage Users</h1>
    </div>
    <form class="user-filter-form" method="get">
        <input type="text" name="search" placeholder="Search by name, username, or email" value="<?php echo htmlspecialchars($search); ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="customer" <?php if ($type==='customer') echo 'selected'; ?>>Customer</option>
            <option value="lawyer" <?php if ($type==='lawyer') echo 'selected'; ?>>Lawyer</option>
            <option value="admin" <?php if ($type==='admin') echo 'selected'; ?>>Admin</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
    </form>
    <div class="user-table-wrapper">
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Status</th>
                    <th class="created-col">Created</th>
                    <th class="actions-col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="badge badge-<?php echo $user['user_type']; ?>"><?php echo ucfirst($user['user_type']); ?></span></td>
                    <td>
                        <?php if ($user['is_active']): ?>
                            <span class="badge badge-active">Active</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="created-col"><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                    <td class="actions-col">
                        <a href="#" class="btn btn-xs btn-secondary edit-user-btn" data-user-id="<?php echo $user['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                        <?php if ($user['is_active']): ?>
                            <a href="#" class="btn btn-xs btn-danger user-action-btn" data-user-id="<?php echo $user['id']; ?>" data-action="deactivate"><i class="fas fa-user-slash"></i> Deactivate</a>
                        <?php else: ?>
                            <a href="#" class="btn btn-xs btn-success user-action-btn" data-user-id="<?php echo $user['id']; ?>" data-action="activate"><i class="fas fa-user-check"></i> Activate</a>
                        <?php endif; ?>
                        <a href="#" class="btn btn-xs btn-danger user-action-btn" data-user-id="<?php echo $user['id']; ?>" data-action="delete"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                <tr><td colspan="8" style="text-align:center; color:#888;">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close-modal" id="closeEditModal">&times;</span>
    <h2>Edit User</h2>
    <form id="editUserForm">
      <input type="hidden" name="user_id" id="editUserId">
      <div class="form-group">
        <label for="editFullName">Full Name</label>
        <input type="text" name="full_name" id="editFullName" required>
      </div>
      <div class="form-group">
        <label for="editEmail">Email</label>
        <input type="email" name="email" id="editEmail" required>
      </div>
      <div class="form-group">
        <label for="editUserType">User Type</label>
        <select name="user_type" id="editUserType" required>
          <option value="customer">Customer</option>
          <option value="lawyer">Lawyer</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div class="form-group">
        <label for="editIsActive">Status</label>
        <select name="is_active" id="editIsActive">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close-modal" id="closeConfirmModal">&times;</span>
    <h2 id="confirmModalTitle">Confirm Action</h2>
    <p id="confirmModalMsg">Are you sure?</p>
    <div style="margin-top:1.5rem; display:flex; gap:1rem; justify-content:flex-end;">
      <button id="confirmModalCancel" class="btn btn-secondary">Cancel</button>
      <button id="confirmModalOk" class="btn btn-danger">Confirm</button>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../Views/footer.php'; ?>

<style>
.user-filter-form {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    align-items: center;
}
.user-filter-form input[type="text"] {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: 1.5px solid #e0e7ef;
    font-size: 1rem;
    width: 260px;
}
.user-filter-form select {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: 1.5px solid #e0e7ef;
    font-size: 1rem;
}
.user-table-wrapper {
    overflow-x: auto;
}
.user-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(44,62,80,0.07);
    font-size: 0.98rem;
}
.user-table th, .user-table td {
    padding: 0.7rem 1rem;
    border-bottom: 1px solid #f0f0f0;
    text-align: left;
}
.user-table th {
    background: #f4f7fb;
    color: #2563eb;
    font-weight: 600;
}
.user-table tr:last-child td {
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
.badge-customer { background: #38bdf8; }
.badge-lawyer { background: #6366f1; }
.badge-admin { background: #f59e42; }
.badge-active { background: #22c55e; }
.badge-inactive { background: #ef4444; }
.user-table th.created-col, .user-table td.created-col {
    width: 110px;
    min-width: 90px;
    text-align: center;
}
.user-table th.actions-col, .user-table td.actions-col {
    width: 180px;
    min-width: 120px;
    text-align: center;
}
.user-table td.actions-col {
    white-space: nowrap;
}
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
}
.btn-xs.btn-danger {
    background: #ef4444;
    color: #fff;
}
.btn-xs.btn-success {
    background: #22c55e;
    color: #fff;
}
.btn-xs.btn-secondary {
    background: #6366f1;
    color: #fff;
}
.btn-xs:hover {
    opacity: 0.92;
    background: #e0e7ef;
    color: #23272f;
}
.btn-xs.btn-danger:hover {
    background: #b91c1c;
    color: #fff;
}
.btn-xs.btn-success:hover {
    background: #15803d;
    color: #fff;
}
.btn-xs.btn-secondary:hover {
    background: #4338ca;
    color: #fff;
}
.modal {
  position: fixed;
  z-index: 10000;
  left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(44,62,80,0.18);
  display: flex; align-items: center; justify-content: center;
}
.modal-content {
  background: #fff;
  padding: 2rem 2.2rem 1.5rem 2.2rem;
  border-radius: 12px;
  min-width: 320px;
  max-width: 95vw;
  box-shadow: 0 8px 32px rgba(44,62,80,0.18);
  position: relative;
}
.close-modal {
  position: absolute;
  right: 1.2rem;
  top: 1.2rem;
  font-size: 1.7rem;
  color: #888;
  cursor: pointer;
}
#editUserForm .form-group {
  margin-bottom: 1.1rem;
}
#editUserForm label {
  display: block;
  margin-bottom: 0.3rem;
  font-weight: 500;
}
#editUserForm input, #editUserForm select {
  width: 100%;
  padding: 0.6rem;
  border-radius: 6px;
  border: 1.5px solid #e0e7ef;
  font-size: 1rem;
}
.toast-container {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}
.toast {
    min-width: 220px;
    max-width: 350px;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    color: #fff;
    font-weight: 500;
    font-size: 1rem;
    box-shadow: 0 2px 12px rgba(44,62,80,0.13);
    opacity: 0;
    transform: translateY(-20px);
    animation: toast-in 0.4s forwards, toast-out 0.4s 2.6s forwards;
    pointer-events: auto;
    display: flex;
    align-items: center;
    gap: 0.7rem;
}
.toast-success {
    background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
}
.toast-error {
    background: linear-gradient(90deg, #ef4444 0%, #b91c1c 100%);
}
.toast-info {
    background: linear-gradient(90deg, #2563eb 0%, #38bdf8 100%);
}
@keyframes toast-in {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes toast-out {
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
}
</style> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only handle activate/deactivate/delete in the table event handler
    // Edit User Modal logic
    const modal = document.getElementById('editUserModal');
    const closeModal = document.getElementById('closeEditModal');
    const editForm = document.getElementById('editUserForm');
    let editingRow = null;
    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            editingRow = btn.closest('tr');
            document.getElementById('editUserId').value = editingRow.querySelector('td:nth-child(1)').textContent.trim();
            document.getElementById('editFullName').value = editingRow.querySelector('td:nth-child(3)').textContent.trim();
            document.getElementById('editEmail').value = editingRow.querySelector('td:nth-child(4)').textContent.trim();
            const userType = editingRow.querySelector('td:nth-child(5) .badge').textContent.trim().toLowerCase();
            document.getElementById('editUserType').value = userType;
            const isActive = editingRow.querySelector('td:nth-child(6) .badge').classList.contains('badge-active') ? '1' : '0';
            document.getElementById('editIsActive').value = isActive;
            modal.style.display = 'flex';
        });
    });
    closeModal.onclick = () => { modal.style.display = 'none'; };
    window.onclick = function(event) { if (event.target === modal) modal.style.display = 'none'; };
    editForm.onsubmit = function(e) {
        e.preventDefault();
        // Validate all fields before sending
        const userId = editForm.user_id.value.trim();
        const fullName = editForm.full_name.value.trim();
        const email = editForm.email.value.trim();
        const userType = editForm.user_type.value.trim();
        const isActive = editForm.is_active.value;
        if (!userId || !fullName || !email || !userType) {
            showToast('All fields are required.', 'error');
            return;
        }
        const formData = new URLSearchParams();
        formData.append('action', 'edit');
        formData.append('user_id', userId);
        formData.append('full_name', fullName);
        formData.append('email', email);
        formData.append('user_type', userType);
        formData.append('is_active', isActive);
        fetch('user_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showToast(data.message || 'Failed to update user', 'error');
                console.error('User update error:', data);
                return;
            }
            showToast(data.message, 'success');
            if (editingRow) {
                editingRow.querySelector('td:nth-child(3)').textContent = fullName;
                editingRow.querySelector('td:nth-child(4)').textContent = email;
                editingRow.querySelector('td:nth-child(5) .badge').textContent = userType.charAt(0).toUpperCase() + userType.slice(1);
                editingRow.querySelector('td:nth-child(5) .badge').className = 'badge badge-' + userType;
                editingRow.querySelector('td:nth-child(6) .badge').textContent = isActive === '1' ? 'Active' : 'Inactive';
                editingRow.querySelector('td:nth-child(6) .badge').className = 'badge ' + (isActive === '1' ? 'badge-active' : 'badge-inactive');
                modal.style.display = 'none';
            }
        })
        .catch(err => {
            showToast('Failed to update user', 'error');
            console.error('AJAX error:', err);
        });
    };
    // Confirmation Modal logic
    const confirmModal = document.getElementById('confirmModal');
    const closeConfirmModal = document.getElementById('closeConfirmModal');
    const confirmModalCancel = document.getElementById('confirmModalCancel');
    const confirmModalOk = document.getElementById('confirmModalOk');
    const confirmModalTitle = document.getElementById('confirmModalTitle');
    const confirmModalMsg = document.getElementById('confirmModalMsg');
    let confirmAction = null;
    let confirmBtn = null;
    let confirmUserId = null;
    let confirmRow = null;
    function showConfirmModal(action, btn, userId, row) {
        confirmAction = action;
        confirmBtn = btn;
        confirmUserId = userId;
        confirmRow = row;
        if (action === 'delete') {
            confirmModalTitle.textContent = 'Delete User';
            confirmModalMsg.textContent = 'Are you sure you want to delete this user? This cannot be undone.';
            confirmModalOk.className = 'btn btn-danger';
        } else if (action === 'deactivate') {
            confirmModalTitle.textContent = 'Deactivate User';
            confirmModalMsg.textContent = 'Are you sure you want to deactivate this user?';
            confirmModalOk.className = 'btn btn-secondary';
        } else if (action === 'activate') {
            confirmModalTitle.textContent = 'Activate User';
            confirmModalMsg.textContent = 'Are you sure you want to activate this user?';
            confirmModalOk.className = 'btn btn-success';
        }
        confirmModal.style.display = 'flex';
    }
    closeConfirmModal.onclick = () => { confirmModal.style.display = 'none'; };
    confirmModalCancel.onclick = () => { confirmModal.style.display = 'none'; };
    window.addEventListener('click', function(event) {
        if (event.target === confirmModal) confirmModal.style.display = 'none';
    });
    confirmModalOk.onclick = function() {
        if (!confirmAction || !confirmUserId) return;
        fetch('user_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${encodeURIComponent(confirmUserId)}&action=${encodeURIComponent(confirmAction)}`
        })
        .then(res => res.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                if (confirmAction === 'delete' && confirmRow) {
                    confirmRow.remove();
                } else {
                    setTimeout(() => window.location.reload(), 800);
                }
            }
        })
        .catch(() => showToast('Request failed', 'error'));
        confirmModal.style.display = 'none';
    };
    // Update table event handler for deactivate/delete
    document.querySelectorAll('.user-table').forEach(function(table) {
        table.addEventListener('click', function(e) {
            const btn = e.target.closest('.user-action-btn');
            if (!btn) return;
            const userId = btn.dataset.userId;
            const action = btn.dataset.action;
            if (!userId || !action) return;
            if (action === 'deactivate' || action === 'delete' || action === 'activate') {
                showConfirmModal(action, btn, userId, btn.closest('tr'));
                return;
            }
        });
    });
});
</script> 