<?php
// Ministries Page
$activePage = 'organizations';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Notifications.php';
require_once __DIR__ . '/../models/Ministry.php';

$notification = new Notification();
$ministry = new Ministry();

requireLogin();

$user_id = $_SESSION['user_id'];


$db = Database::getInstance();
$admins = $db->fetchAll("SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Admin'");

$ministries = $db->fetchAll("SELECT * FROM ministries WHERE status = 'active' ORDER BY name");
$message = '';
$message_type = '';

//Logic to prevent resubmission after any refresh
if(isset($_GET['msg'])){
    switch($_GET['msg']){
        case 'added':
            $message='Organization Added Successfully!';
            $message_type='success';
            break;
        case 'add_failed':
            $message = 'Failed to create Organization';
            $message_type='error';
            break;
        case 'updated':
            $message = 'Organization Updated Successfully!';
            $message_type='success';
            break;
        case 'update_failed':
            $message = 'Failed to update Organization';
            $message_type='error';
            break;
        case 'delete':
            $message = 'Organization Deleted Successfully!';
            $message_type='success';
            break;
        case 'delete_failed':
            $message = 'Failed to delte Organization';
            $message_type='error';
            break;
        default:
            $message = '';
            $message_type = '';
            break;

    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // deleting ministry
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;

        if ($id) {
            $stmt = $db->getConnection()->prepare("DELETE FROM ministries WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                foreach($admins as $admin){
                $notification->create(
                    $admin['id'],
                    'Organization Deleted',
                    'An organization was deleted.',
                    'ministries.php'
                );
                }
                header("Location: ministries.php?msg=deleted");
                exit();
            } else {
                header("Location: ministries.php?msg=delete_failed");
                exit();
            }
        }
    }

    // editing ministry..
    else if ($action === 'edit') {
        $id = $_POST['id'] ?? null;

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'leader_email' => trim($_POST['leader_email'] ?? ''),
            'meeting_day' => $_POST['meeting_day'] ?? '',
            'meeting_time' => $_POST['meeting_time'] ?? '',
            'location' => trim($_POST['location'] ?? ''),
            'status' => $_POST['status'] ?? 'active'
        ];

        if ($id && $ministry->update($id, $data)) {
            foreach ($admins as $admin) {
                $notification->create($admin['id'], 'Organization Updated', 'An organization was updated.', 'ministries.php');
            }
            header("Location: ministries.php?msg=updated");
            exit();
        } else {
            header("Location: ministries.php?msg=update_failed");
            exit();
        }
    }


    // Adding new ministry....
    else if ($action === 'add') {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'leader_email' => trim($_POST['leader_email'] ?? ''),
            'meeting_day' => $_POST['meeting_day'] ?? '',
            'meeting_time' => $_POST['meeting_time'] ?? '',
            'location' => trim($_POST['location'] ?? ''),
            'status' => $_POST['status'] ?? 'active'
        ];

        if (empty($data['name'])) {
            $message = 'Ministry name is required';
            $message_type = 'error';
        } else {
            $newId = $ministry->createFull($data);

            if ($newId) {
                foreach ($admins as $admin) {
                    $notification->create($admin['id'], 'New Organization Added', 'A new organization was added.', 'ministries.php');
                }
                header("Location: ministries.php?msg=added");
                exit();
            } else {
                header("Location: ministries.php?msg=add_failed");
                exit();
            }
        }
    }
    // refresh ministries list after any change
    $ministries = $db->fetchAll("SELECT * FROM ministries WHERE status = 'active' ORDER BY name");
}

?>
<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Organizations</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMinistryModal">
                <i class="fas fa-handshake"></i> Add Organization
            </button>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Ministries Grid -->
     <?php if(!empty($ministries)): ?>    
        <div class="row g-4">
            <?php foreach ($ministries as $m): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--primary-color);">
                                <i class="fas fa-handshake"></i> <?php echo htmlspecialchars($m['name']); ?>
                            </h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($m['description']); ?></p>
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> <?php echo ucfirst($m['meeting_day']) . ' at ' . date('g:i A', strtotime($m['meeting_time'])); ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($m['location']); ?>
                                </small>
                            </div>
                            <?php if ($m['leader_email']): ?>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> Leader: <?php echo htmlspecialchars($m['leader_email']); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-light">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ministryDetailsModal<?php echo $m['id']; ?>">
                                <i class="fas fa-info-circle"></i> Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
               <!-- Fallback UI -->
    <div class="text-center py-5">
        <div class="mb-3 fs-1 text-muted">
            <i class="fas fa-calendar-times"></i>
        </div>
        <h5 class="fw-bold mb-2">No Organisations Available</h5>
        <p class="text-muted mb-4">
            There are currently no active organisations to display.
        </p>
        <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addMinistryModal">
            <i class="fas fa-plus me-1"></i> Add First Organisation
        </button>
    </div>

<?php endif; ?>
    </div>
</div>

<!-- Add Ministry Modal -->
<div class="modal fade" id="addMinistryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="fas fa-handshake"></i> Add New Ministry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="hidden" name="action" value="add">
                        <label for="name" class="form-label">Organization Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="meeting_day" class="form-label">Meeting Day</label>
                            <select class="form-select" name="meeting_day">
                                <option value="">Select Day</option>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="meeting_time" class="form-label">Meeting Time</label>
                            <input type="time" class="form-control" name="meeting_time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" name="location">
                    </div>
                    <div class="mb-3">
                        <label for="leader_email" class="form-label">Leader Email</label>
                        <input type="email" class="form-control" name="leader_email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Organization</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ministry Details  -->
<?php foreach ($ministries as $m): ?>
<div class="modal fade" id="ministryDetailsModal<?php echo $m['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-handshake"></i> 
                    <?php echo htmlspecialchars($m['name']); ?> — Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
    <div class="mb-4">
        <h6 class="fw-bold text-uppercase small text-secondary mb-2">
            <i class="fas fa-info-circle me-1"></i> Overview
        </h6>
        <div class="p-3 rounded" style="background: #f8f9fa;">
            <p class="mb-0 text-muted">
                <?php echo nl2br(htmlspecialchars($m['description'] ?: 'No description provided.')); ?>
            </p>
        </div>
    </div>
    <div class="mb-4">
        <h6 class="fw-bold text-uppercase small text-secondary mb-2">
            <i class="fas fa-calendar-alt me-1"></i> Meeting Information
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block mb-1">Meeting Day</small>
                    <span class="fw-semibold">
                        <?php echo ucfirst($m['meeting_day']) ?: 'Not set'; ?>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block mb-1">Meeting Time</small>
                    <span class="fw-semibold">
                        <?php echo $m['meeting_time'] ? date('g:i A', strtotime($m['meeting_time'])) : 'Not set'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-4">
        <h6 class="fw-bold text-uppercase small text-secondary mb-2">
            <i class="fas fa-map-marked-alt me-1"></i> Location
        </h6>
        <div class="border rounded p-3">
            <span class="fw-semibold text-muted">
                <?php echo htmlspecialchars($m['location'] ?: 'Not set'); ?>
            </span>
        </div>
    </div>
    <div class="mb-4">
        <h6 class="fw-bold text-uppercase small text-secondary mb-2">
            <i class="fas fa-user-tie me-1"></i> Leadership
        </h6>
        <div class="border rounded p-3">
            <small class="text-muted d-block mb-1">Leader Email</small>
            <span class="fw-semibold">
                <?php echo htmlspecialchars($m['leader_email'] ?: 'Not provided'); ?>
            </span>
        </div>
    </div>
    <div>
        <h6 class="fw-bold text-uppercase small text-secondary mb-2">
            <i class="fas fa-check-circle me-1"></i> Status
        </h6>
        <div class="border rounded p-3">
            <span class="badge bg-<?php echo $m['status'] === 'active' ? 'success' : 'secondary'; ?> px-3 py-2">
                <?php echo ucfirst($m['status']); ?>
            </span>
        </div>
    </div>

</div>
         <div class="modal-footer d-flex justify-content-between">
    <!--Delete -->
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this ministry?');">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
    <button type="submit" class="btn btn-danger">
        <i class="fas fa-trash-alt me-1"></i> Delete
    </button>
</form>

    <!--Close + Edit -->
    <div>
        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">
            Close
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editMinistryModal<?php echo $m['id']; ?>">
            <i class="fas fa-edit me-1"></i> Edit
        </button>
    </div>

</div>
        </div>
    </div>
</div>

<!-- Edit Ministry Modal -->
<div class="modal fade" id="editMinistryModal<?php echo $m['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST"  class="modal-content">
            <input type="hidden" name="action" value="edit">
<input type="hidden" name="id" value="<?php echo $m['id']; ?>">

            
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Organization — <?php echo htmlspecialchars($m['name']); ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                
                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Organization Name *</label>
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($m['name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($m['description']); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Meeting Day</label>
                        <select class="form-select" name="meeting_day">
                            <option value="">Select Day</option>
                            <?php 
                                $days = ["monday","tuesday","wednesday","thursday","friday","saturday","sunday"];
                                foreach($days as $day):
                            ?>
                                <option value="<?php echo $day; ?>" <?php echo ($m['meeting_day'] == $day) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($day); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Meeting Time</label>
                        <input type="time" class="form-control" name="meeting_time" value="<?php echo $m['meeting_time']; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($m['location']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Leader Email</label>
                    <input type="email" class="form-control" name="leader_email" value="<?php echo htmlspecialchars($m['leader_email']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="active" <?php echo ($m['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($m['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>

        </form>
    </div>
</div>

<?php endforeach; ?>

<?php include 'footer.php'; ?>
