<?php
// Members Page
$activePage='members';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Member.php';

requireLogin();

$member = new Member();
$members = $member->getAll();
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_member'])) {
    $data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'join_date' => $_POST['join_date'] ?? date('Y-m-d'),
        'address' => trim($_POST['address'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'state' => trim($_POST['state'] ?? ''),
        'zip_code' => trim($_POST['zip_code'] ?? ''),
    ];

    if (empty($data['first_name']) || empty($data['last_name'])) {
        $message = 'First and last names are required';
        $message_type = 'error';
    } else {
        if ($member->create($data)) {
            // $message = 'Member added successfully!';
            // $message_type = 'success';
            // $members = $member->getAll();
            header("Location: members.php?msg=added");
            exit();
        } else {
            // $message = 'Failed to add member';
            // $message_type = 'error';
            header("Location: members.php?msg=add_failed");
            exit();
        }
    }

    if($member->create($_POST)){
        // $message = "Member added successfully!";
        // $message_type = "success";
        header("Location: members.php?msg=added");
        exit();
    } else {
        // $message = "Failed to add member. Email may already exist.";
        // $message_type = "error";
        header("Location: members.php?msg=add_failed");
        exit();
    }
}

// Handle member update
if (isset($_POST['update_member'])) {
    $editId = (int)$_POST['edit_id'];
    $data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'join_date' => $_POST['join_date'] ?? date('Y-m-d'),
        'address' => trim($_POST['address'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'state' => trim($_POST['state'] ?? ''),
        'zip_code' => trim($_POST['zip_code'] ?? ''),
    ];
    if ($member->update($editId, $data)) {
        header("Location: members.php?msg=updated");
        exit();
        // $message = 'Member updated successfully!';
        // $message_type = 'success';
        // $members = $member->getAll();
    } else {
        header("Location: members.php?msg=update_failed");
        exit();
        // $message = 'Failed to update member';
        // $message_type = 'error';
    }
}

//Handle Delete Member
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Implement delete functionality in Member model
    if ($member->permanentDelete($id)) {
        header("Location: members.php?msg=deleted");
        exit();
        // $message = 'Member deleted successfully!';
        // $message_type = 'success';
    } else {
        header("Location: members.php?msg=delete_failed");
        exit();
        // $message = 'Failed to delete member';
        // $message_type = 'error';

    }
    // $members = $member->getAll();
}
//Logic to prevent resubmission after any refresh

if(isset($_GET['msg'])){
    switch($_GET['msg']){
        case 'added':
            $message = 'Member added successfully!';
            $message_type = 'success';
            break;
        case 'add_failed':
            $message = 'Failed to add member';
            $message_type = 'error';
            break;
        case 'updated':
            $message = 'Member updated successfully!';
            $message_type = 'success';
            break;
        case 'update_failed':
            $message = 'Failed to update member';
            $message_type = 'error';
            break;
        case 'deleted':
            $message = 'Member deleted successfully!';
            $message_type = 'success';
            break;
        case 'delete_failed':
            $message = 'Failed to delete member';
            $message_type = 'error';
            break;
        default:
            $message = '';
            $message_type = '';
            break;
    }
}
?>
<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Members</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="fas fa-user-plus"></i> Add Member
            </button>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Members Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: var(--primary-color); color: white;">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Join Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $m): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($m['first_name'] . ' ' . $m['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($m['email'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($m['phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($m['join_date'] ?? $m['created_at'])); ?></td>
                                    <td><span class="badge bg-success"><?php echo ucfirst($m['status']); ?></span></td>
                                    <td>
                                        <!-- <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editMemberModal<?php echo $m['id']; ?>"> -->
                                            <button class="btn btn-sm btn-outline-primary editBtn" data-bs-toggle="modal" data-bs-target="#editMemberModal<?php echo $m['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo $m['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal<?php echo $m['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">
                <input type="hidden" name="edit_id" value="<?php echo $m['id']; ?>">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name"
                                value="<?php echo $m['first_name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name"
                                value="<?php echo $m['last_name']; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?php echo $m['email']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone"
                                value="<?php echo $m['phone']; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth"
                                value="<?php echo $m['date_of_birth']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo $m['gender']=='Male'?'selected':''; ?>>Male</option>
                                <option value="Female" <?php echo $m['gender']=='Female'?'selected':''; ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="address"
                            value="<?php echo $m['address']; ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city"
                                value="<?php echo $m['city']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state"
                                value="<?php echo $m['state']; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Zip Code</label>
                        <input type="text" class="form-control" name="zip_code"
                            value="<?php echo $m['zip_code']; ?>">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="update_member">Save Changes</button>
                </div>
            </form>

        </div>
    </div>
</div>




                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" name="state">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="zip_code" class="form-label">Zip Code</label>
                        <input type="text" class="form-control" name="zip_code">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>





<?php include 'footer.php'; ?>
<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this member?')) {
        window.location.href = '?delete=' + id;
        console.log('Delete member:', id);
    }
}
</script>
