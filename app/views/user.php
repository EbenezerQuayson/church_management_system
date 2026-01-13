<?php
$activePage = 'user';
$pageTitle = 'User Management';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/User.php';
requireLogin(); // Ensure only logged-in users can access

$role = $_SESSION['user_role'] ?? 'User';
$user_id = $_SESSION['user_id'];

$db = Database::getInstance()->getConnection();
$userModel = new User();

$message = '';
$message_type = '';

// Handle success messages (from redirects)
if (isset($_GET['msg'])) {
    switch($_GET['msg']){
        case 'added': $message = 'User added successfully!'; $message_type='success'; break;
        case 'updated': $message = 'User updated successfully!'; $message_type='success'; break;
        case 'deleted': $message = 'User deleted successfully!'; $message_type='success'; break;
        default: $message=''; $message_type=''; break;
    }
}

// =======================
// HANDLE FORM ACTIONS
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Admin') {

    // ADD USER (kept for completeness)
    if ($_POST['action'] === 'add') {
        $userModel->register($_POST);
        header("Location: user.php?msg=added");
        exit;
    }

    // EDIT USER
    if ($_POST['action'] === 'edit') {

    // Ensure required fields exist
    if (!isset($_POST['user_id'], $_POST['role_id'], $_POST['status'])) {
        header("Location: user.php?msg=error");
        exit;
    }

    // Cast values to prevent tampering
    $userId  = (int) $_POST['user_id'];
    $roleId  = (int) $_POST['role_id'];
    $status  = (int) $_POST['status'];

    // Optional: whitelist valid roles
    $allowedRoles = [1, 3, 6];
    if (!in_array($roleId, $allowedRoles, true)) {
        header("Location: user.php?msg=error");
        exit;
    }

    $userModel->update($userId, [
        'role_id' => $roleId,
        'status'  => $status
    ]);

    header("Location: user.php?msg=updated");
    exit;
}


    // DELETE USER (soft delete)
    if ($_POST['action'] === 'delete') {
        $userModel->deactivate($_POST['user_id']);
        header("Location: user.php?msg=deleted");
        exit;
    }
}

$users = $userModel->getAllUsers(); // Fetch all users



include "header.php";
include "sidebar.php";
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>User Management</h3>
            <?php if($role === 'Admin'): ?>
             <button type="button" class="btn btn-primary" onclick="window.open('../../public/register.php', '_blank', 'noopener, noreferrer')">
    <i class="fas fa-user-plus"></i> Add User
</button>

            <?php endif; ?>
        </div>

        <!-- Alert messages -->
        <?php if($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search -->
        <div class="mb-3">
            <input type="text" id="userSearch" class="form-control" placeholder="Search users by name or email...">
        </div>

        <!-- Users Table -->
        <div class="card shadow-sm">
            <div class="card-body table-responsive table-mobile-friendly">
                <table class="table table-hover" id="usersTable">
                    <thead class="table">
                        <tr>
                            <th class="col-essential">Name</th>
                            <th class="col-hide-mobile">Email</th>
                            <th class="col-essential">Role</th>
                            <th class="col-hide-mobile">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $u): ?>
                                <tr>
                                    <td class="col-essential"><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
                                    <td class="col-hide-mobile"><?= htmlspecialchars($u['email']) ?></td>
                                    <td class="col-essential"><?= htmlspecialchars($u['role_name']) ?></td>
                                    <td class="col-hide-mobile"><?= $u['status'] ? 'Active' : 'Inactive' ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary viewUserBtn"
                                                data-id="<?= $u['id'] ?>"
                                                data-first="<?= htmlspecialchars($u['first_name']) ?>"
                                                data-last="<?= htmlspecialchars($u['last_name']) ?>"
                                                data-email="<?= htmlspecialchars($u['email']) ?>"
                                                data-role="<?= htmlspecialchars($u['role_name']) ?>"
                                                data-status="<?= $u['status'] ? 'Active' : 'Inactive' ?>"
                                                data-bs-toggle="modal" data-bs-target="#viewUserModal">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <?php if($role === 'Admin'): ?>
                                            <button class="btn btn-sm btn-outline-success editUserBtn"
                                                    data-id="<?= $u['id'] ?>"
                                                    data-first="<?= htmlspecialchars($u['first_name']) ?>"
                                                    data-last="<?= htmlspecialchars($u['last_name']) ?>"
                                                    data-email="<?= htmlspecialchars($u['email']) ?>"
                                                    data-role="<?= htmlspecialchars($u['role_id']) ?>"
                                                    data-status="<?= $u['status'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger deleteUserBtn"
                                                    data-id="<?= $u['id'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No users found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<?php if($role==='Admin'): ?>
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" value="add">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role_id" class="form-select" required>
                            <option value="3">Leader</option>
                            <option value="6">Treasurer</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Add User</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="viewName"></span></p>
                <p><strong>Email:</strong> <span id="viewEmail"></span></p>
                <p><strong>Role:</strong> <span id="viewRole"></span></p>
                <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<?php if($role==='Admin'): ?>
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="user_id" id="editUserId">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" name="first_name" id="editFirstName" class="form-control" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" name="last_name" id="editLastName" class="form-control" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role_id" id="editRole" class="form-select" required>
                            <option value="1">Admin</option>
                            <option value="3">Leader</option>
                            <option value="6">Treasurer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="editStatus" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" type="submit">Update User</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="user_id" id="deleteUserId">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="submit">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include "footer.php"; ?>

<script>
const usersTable = document.getElementById('usersTable');
const searchInput = document.getElementById('userSearch');

// View User
document.querySelectorAll('.viewUserBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('viewName').innerText = this.dataset.first + ' ' + this.dataset.last;
        document.getElementById('viewEmail').innerText = this.dataset.email;
        document.getElementById('viewRole').innerText = this.dataset.role;
        document.getElementById('viewStatus').innerText = this.dataset.status;
    });
});

// Edit User
document.querySelectorAll('.editUserBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('editUserId').value = this.dataset.id;
        document.getElementById('editFirstName').value = this.dataset.first;
        document.getElementById('editLastName').value = this.dataset.last;
        document.getElementById('editEmail').value = this.dataset.email;
        document.getElementById('editRole').value = this.dataset.role;
        document.getElementById('editStatus').value = this.dataset.status;
    });
});

// Delete User
document.querySelectorAll('.deleteUserBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('deleteUserId').value = this.dataset.id;
    });
});

// Search (basic)
searchInput.addEventListener('keyup', function() {
    const query = this.value.toLowerCase();
    [...usersTable.tBodies[0].rows].forEach(row => {
        row.style.display = row.cells[0].innerText.toLowerCase().includes(query) || 
                            row.cells[1].innerText.toLowerCase().includes(query) ? '' : 'none';
    });
});
</script>
