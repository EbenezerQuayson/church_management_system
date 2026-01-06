<?php
$activePage = 'service';
$pageTitle = 'Service';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Notifications.php';
require_once __DIR__ . '/../models/Program.php';

requireLogin();
$role =$_SESSION['user_role'] ?? null;
$authorized = in_array($role, ['Admin', 'Leader']);




$notification = new Notification();
$programModel = new Program();
$db = Database::getInstance();

$user_id = $_SESSION['user_id'];

$admins = $db->fetchAll("
    SELECT u.id 
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    WHERE r.name = 'Admin'
");

$programs = $db->fetchAll("SELECT * FROM programs
ORDER BY display_order ASC, id ASC
");

$message = '';
$message_type = '';

// Session flash messages
if (isset($_SESSION['error'])) {
    $message = $_SESSION['error'];
    $message_type = 'error';
    unset($_SESSION['error']); // VERY IMPORTANT
}

if (isset($_SESSION['success'])) {
    $message = $_SESSION['success'];
    $message_type = 'success';
    unset($_SESSION['success']);
}


/* Flash messages */
if (isset($_GET['msg'])) {
    $map = [
        'added' => ['Program added successfully!', 'success'],
        'updated' => ['Program updated successfully!', 'success'],
        'deleted' => ['Program deleted successfully!', 'success'],
        'add_failed' => ['Failed to add program.', 'error'],
        'update_failed' => ['Failed to update program.', 'error'],
        'delete_failed' => ['Failed to delete program.', 'error'],
    ];

    if (isset($map[$_GET['msg']])) {
        [$message, $message_type] = $map[$_GET['msg']];
    }
}




/* POST actions */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    // To prevent Random POSTs, mistyped values and tampering

$allowedActions = ['add', 'edit', 'delete'];

if (!in_array($action, $allowedActions)) {
    $_SESSION['error'] = 'Invalid action';
    header('Location: service.php');
    exit;
}
    /* Delete */
    if ($action === 'delete') {
       $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        if ($role !== 'Admin') {
        $_SESSION['error'] = 'Only admins can delete programs';
        header('Location: service.php');
        exit;
    }

        if ($id && $programModel->delete($id)) {
            foreach ($admins as $admin) {
                $notification->create(
                    $admin['id'],
                    'Program Deleted',
                    'A program was deleted.',
                    'programs.php'
                );
            }
            header("Location: service.php?msg=deleted");
            exit;
        }
        header("Location: service.php?msg=delete_failed");
        exit;
    }

    /* Edit */
    if ($action === 'edit') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        if (!in_array($role, ['Admin', 'Leader'])) {
        $_SESSION['error'] = 'You are not allowed to edit programs';
        header('Location: service.php');
        exit;
    }


        $data = [
            'title'         => trim($_POST['title']),
            'icon_class'    => trim($_POST['icon_class']),
            'schedule_text' => trim($_POST['schedule_text']),
            'description'   => trim($_POST['description']),
           'is_active' => ($_POST['status'] === '1') ? 1 : 0,
            'display_order' => (int) $_POST['display_order']
        ];

        if ($id && $programModel->update($id, $data)) {
            foreach ($admins as $admin) {
                $notification->create($admin['id'], 'Program Updated', 'A program was updated.', 'service.php');
            }
            header("Location: service.php?msg=updated");
            exit;
        }
        header("Location: service.php?msg=update_failed");
        exit;
    }

    /* Add */
    if ($action === 'add') {
        if(!in_array($role, ['Admin', 'Leader'])){
            $_SESSION['error'] = 'You are not allowed to create programs';
            header('Location: service.php');
            exit;
        }
        $data = [
            'title'         => trim($_POST['title']),
            'icon_class'    => trim($_POST['icon_class']),
            'schedule_text' => trim($_POST['schedule_text']),
            'description'   => trim($_POST['description']),
           'is_active' => ($_POST['status'] === '1') ? 1 : 0,
            'display_order' => (int) $_POST['display_order']
        ];

        if ($programModel->create($data)) {
            foreach ($admins as $admin) {
                $notification->create($admin['id'], 'New Program Added', 'A new program was added.', 'service.php');
            }
            header("Location: service.php?msg=added");
            exit;
        }
        header("Location: service.php?msg=add_failed");
        exit;
    }
}

include 'header.php';
?>

<div class="main-content">
<?php include 'sidebar.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary-color);">
            <i class="fas fa-calendar-alt me-2"></i> Programs & Services
        </h2>
        <?php if($authorized): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProgramModal">
            <i class="fas fa-plus"></i> Add Program
        </button>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

   <?php if (!empty($programs)): ?>

    <div class="row g-4">
        <?php foreach ($programs as $p): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3 fs-2 text-primary">
                            <i class="<?php echo htmlspecialchars($p['icon_class']); ?>"></i>
                        </div>
                        <h5 class="fw-bold"><?php echo htmlspecialchars($p['title']); ?></h5>
                        <p class="text-muted small mb-2">
                            <?php echo htmlspecialchars($p['schedule_text']); ?>
                        </p>
                        <p class="small">
                            <?php echo htmlspecialchars($p['description']); ?>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">

                        <button class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editProgramModal<?php echo $p['id']; ?>">
                            <i class="fas fa-<?= $authorized ? 'edit' : 'eye'; ?> me-1"></i><?= $authorized ? 'Edit' : 'View' ?>
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
        <h5 class="fw-bold mb-2">No Programs Available</h5>
        <p class="text-muted mb-4">
            There are currently no active programs to display.
        </p>
        <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addProgramModal">
            <i class="fas fa-plus me-1"></i> Add First Program
        </button>
    </div>

<?php endif; ?>

</div>
</div>


<!-- Add Program Modal -->
<div class="modal fade" id="addProgramModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-1"></i> Add New Program
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">
                <div class="modal-body">

                    <input type="hidden" name="action" value="add">

                    <div class="mb-3">
                        <label class="form-label">Program Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>

                    <div class="mb-3">
                       <label class="form-label">Choose an Icon</label>
    <select id="iconSelect" class="form-select" name="icon_class">
        <option value="">-- Select an icon --</option>
        <option value="fas fa-church">Church <i class="fas fa-church"></i></option>
        <option value="fas fa-users">Users</option>
        <option value="fas fa-hand-holding-heart">Ministry</option>
        <option value="fas fa-praying-hands">Prayer</option>
        <option value="fas fa-book">Bible</option>
        <option value="fas fa-heart">Love</option>
        <option value="fas fa-praying-hands">Prayer</option>
        <option value="fas fa-bible">Bible</option>
        <option value="fas fa-hands-helping">Charity/Outreach</option>
        <option value="fas fa-users">Community</option>
        <option value="fas fa-music">Music / Choir</option>
        <option value="fas fa-graduation-cap">Teaching / Education</option>
        <option value="fas fa-heart">Youth / Care</option>
        <option value="fas fa-children">Children’s Ministry</option>
        <option value="fas fa-hand-holding-heart">Support / Counseling</option>
        <option value="fas fa-bullhorn">Announcements / Evangelism</option>
    </select>
    <div class="mt-2">
        <span>Preview: </span>
        <i id="iconPreview" class=""></i>
    </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Schedule Text *</label>
                        <input type="text"
                               class="form-control"
                               name="schedule_text"
                               placeholder="e.g. Sundays 8:00 AM & 10:00 AM"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control"
                                  name="description"
                                  rows="3"></textarea>
                    </div>
                    <?php if($role === 'Admin'): ?>
                     <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number"
                               class="form-control"
                               name="display_order"
                               value="0">
                    </div>
                    <?php endif; ?>
                    

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Program
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- Edit -->
<?php foreach ($programs as $p): ?>
<div class="modal fade" id="editProgramModal<?php echo $p['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title">
    <i class="fas fa-<?php echo $authorized ? 'edit' : 'eye'; ?> me-1"></i>
    <?php echo $authorized ? 'Edit Program' : 'View Program'; ?>
</h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">
                <div class="modal-body">

                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                    <?php if (!$authorized): ?>
    <input type="hidden" name="status" value="<?php echo $p['is_active']; ?>">
                    <?php endif; ?>


                    <div class="mb-3">
                        <label class="form-label">Program Title *</label>
                        <input type="text"
                               class="form-control"
                               name="title"
                               value="<?php echo htmlspecialchars($p['title']); ?>"
                               <?php echo !$authorized ? 'readonly' : ''; ?>
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Choose an Icon</label>
                        <select class="form-select iconSelectEdit"
                                name="icon_class"
                                data-preview="iconPreview<?php echo $p['id']; ?>"
                                <?php echo !$authorized ? 'disabled' : ''; ?>
                                >

                            <option value="">-- Select an icon --</option>

                            <?php
                            $icons = [
                                'fas fa-church' => 'Church',
                                'fas fa-users' => 'Community',
                                'fas fa-hand-holding-heart' => 'Support / Care',
                                'fas fa-praying-hands' => 'Prayer',
                                'fas fa-book' => 'Bible Study',
                                'fas fa-music' => 'Music / Choir',
                                'fas fa-graduation-cap' => 'Teaching',
                                'fas fa-children' => 'Children’s Ministry',
                                'fas fa-bullhorn' => 'Evangelism'
                            ];

                            foreach ($icons as $class => $label):
                            ?>
                                <option value="<?php echo $class; ?>"
                                    <?php echo ($p['icon_class'] === $class) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div class="mt-2">
                            <span>Preview: </span>
                            <i id="iconPreview<?php echo $p['id']; ?>"
                               class="<?php echo htmlspecialchars($p['icon_class']); ?>"></i>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Schedule Text *</label>
                        <input type="text"
                               class="form-control"
                               name="schedule_text"
                               value="<?php echo htmlspecialchars($p['schedule_text']); ?>"
                               <?php echo !$authorized ? 'readonly' : ''; ?>
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control"
                                  name="description"
                                  rows="3"
                                  <?php echo !$authorized ? 'readonly' : ''; ?>
                                  ><?php echo htmlspecialchars($p['description']); ?></textarea>
                    </div>
                    <?php if ($role === 'Admin'): ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number"
                               class="form-control"
                               name="display_order"
                               value="<?php echo (int) $p['display_order']; ?>"
                               >
                    </div>


                     <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" >
                            <option value="1" <?php echo $p['is_active'] === 1 ? 'selected' : ''; ?>>
                                Active
                            </option>
                            <option value="0" <?php echo $p['is_active'] === 0 ? 'selected' : ''; ?>>
                                Inactive
                            </option>
                        </select>
                    </div>
                    </div>
                    <?php endif; ?>
   



                </div>

                <div class="modal-footer d-flex justify-content-between">

    <!-- Delete button (no form here) -->
     <?php if($role === 'Admin'): ?>
    <button type="button"
            class="btn btn-danger"
            onclick="confirmDeleteProgram(<?php echo $p['id']; ?>)">
        <i class="fas fa-trash-alt me-1"></i> Delete
    </button>
    <?php endif; ?>

    <div>
        <button type="button"
                class="btn btn-outline-secondary me-2"
                data-bs-dismiss="modal">
            <?= $authorized ? 'Cancel' : 'Close' ?>
        </button>
        <?php if($authorized): ?>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Save Changes
        </button>
        <?php endif; ?>
    </div>

</div>


            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Form for deleting -->
<form id="deleteProgramForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteProgramId">
</form>

<script>
document.addEventListener('change', function (e) {

    if (e.target.matches('#iconSelect, .iconSelectEdit')) {

        const previewId = e.target.dataset.preview || 'iconPreview';
        const previewIcon = document.getElementById(previewId);

        if (previewIcon) {
            previewIcon.className = e.target.value;
        }
    }
});


function confirmDeleteProgram(id) {
    if (confirm('Are you sure you want to delete this program?')) {
        document.getElementById('deleteProgramId').value = id;
        document.getElementById('deleteProgramForm').submit();
    }
}



</script>


<?php include "footer.php"; ?>
