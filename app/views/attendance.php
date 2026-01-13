<?php
// Attendance Page
$activePage = 'attendance';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Member.php';

requireLogin();
$role =$_SESSION['user_role'] ?? null;
$authorized = in_array($role, ['Admin', 'Leader']);

$user_id = $_SESSION['user_id'];

$attendance = new Attendance();
$member_model = new Member();
$members = $member_model->getAll();
$attendance_date = $_GET['date'] ?? date('Y-m-d');
$today_attendance = $attendance->getByDate($attendance_date);
//Message Handling
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

if(isset($_GET['success'])) {
    $message = 'Operation completed successfully!';
    $message_type = 'success';
} elseif (isset($_GET['error'])) {
    $message = 'An error occurred. Please try again.';
    $message_type = 'error';
} elseif(isset($_GET['updated'])) {
    $message = 'Attendance updated successfully!';
    $message_type = 'success';
} elseif(isset($_GET['deleted'])) {
    $message = 'Attendance deleted successfully';
    $message_type = 'success';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //DELETE ATTENDANCE
    if (isset($_POST['delete_attendance']) && !empty($_POST['attendance_id'])) {

        $attendance_id = (int) $_POST['attendance_id'];
        if ($role !== 'Admin') {
        $_SESSION['error'] = 'Only admins can delete attendance';
        header('Location: attendance.php');
        exit;
    }

        if ($attendance->deleteAttendance($attendance_id)) {
            header('Location: attendance.php?date=' . $attendance_date . '&deleted=1');
            exit();
        } else {
            header('Location: attendance.php?date=' . $attendance_date . '&error=1');
            exit();
        }
    }

    // UPDATE ATTENDANCE
    if (isset($_POST['update_attendance']) && !empty($_POST['attendance_id'])) {

        $attendance_id = (int) $_POST['attendance_id'];
        $status = $_POST['status'] ?? 'present';
        $notes  = trim($_POST['notes'] ?? '');
        if (!$authorized) {
        $_SESSION['error'] = 'Youare not allowed to edit attendance';
        header('Location: attendance.php');
        exit;
    }

        if ($attendance->updateAttendance($attendance_id, $status, $notes, $attendance_date)) {
            header('Location: attendance.php?date=' . $attendance_date . '&updated=1');
            exit();
        } else {
            header('Location: attendance.php?date=' . $attendance_date . '&error=1');
            exit();
        }
    }

    //CREATE NEW ATTENDANCE
    if (!empty($_POST['member_id'])) {

        $data = [
            'member_id'       => $_POST['member_id'],
            'attendance_date' => $attendance_date,
            'status'          => $_POST['status'] ?? 'present',
            'notes'           => trim($_POST['notes'] ?? ''),
        ];
        if (!$authorized) {
        $_SESSION['error'] = 'You are not allowed to mark attendance';
        header('Location: attendance.php');
        exit;
    }

        if ($attendance->recordAttendance($data)) {
            header('Location: attendance.php?date=' . $attendance_date . '&success=1');
            exit();
        } else {
            header('Location: attendance.php?date=' . $attendance_date . '&error=1');
            exit();
        }

        $today_attendance = $attendance->getByDate($attendance_date);
    }
}

?>
<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Attendance</h2>
            <div class="d-flex gap-2">
                <input type="date" id="attendanceDate" class="form-control" value="<?php echo $attendance_date; ?>">
            </div>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Attendance Summary -->
         <?php if($authorized): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card stat-card-green">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="stat-value">
                            <?php 
                            $present = count(array_filter($today_attendance, function($a) { return $a['status'] === 'present'; }));
                            echo $present;
                            ?>
                        </p>
                        <p class="stat-label">Present</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card stat-card-orange">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <p class="stat-value">
                            <?php 
                            $absent = count(array_filter($today_attendance, function($a) { return $a['status'] === 'absent'; }));
                            echo $absent;
                            ?>
                        </p>
                        <p class="stat-label">Absent</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Add Attendance -->
         <?php if($authorized): ?>
        <div class="card mb-4">
            <div class="card-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="mb-0">Quick Mark Attendance</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="member_id" class="form-label">Select Member</label>
                            <select class="form-select" name="member_id" required>
                                <option value="">-- Choose Member --</option>
                                <?php foreach ($members as $m): ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['first_name'] . ' ' . $m['last_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                                <option value="excused">Excused</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check"></i> Mark Attendance
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Attendance List -->
        <div class="card">
            <div class="card-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="mb-0">Attendance for <?php echo date('F d, Y', strtotime($attendance_date)); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-mobile-friendly">
                        <thead style="background-color: var(--primary-color); color: white;">
                            <tr>
                                <th class="col-essential">Member Name</th>
                                <th class="col-essential">Status</th>
                                <th class="col-hide-mobile">Time Recorded</th>
                                <?php if($authorized): ?>
                                <th class="col-essential text-end">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($today_attendance)): ?>
                                <?php foreach ($today_attendance as $a): ?>
                                    <tr>
                                        <td class="col-essential"><?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name']); ?></td>
                                        <td class="col-essential">
                                            <?php 
                                            $status_color = 'secondary';
                                            if ($a['status'] === 'present') $status_color = 'success';
                                            elseif ($a['status'] === 'absent') $status_color = 'danger';
                                            elseif ($a['status'] === 'late') $status_color = 'warning';
                                            ?>
                                            <span class="badge bg-<?php echo $status_color; ?>"><?php echo ucfirst($a['status']); ?></span>
                                        </td>
                                        <td class="col-hide-mobile"><?php echo date('g:i A', strtotime($a['created_at'])); ?></td>
                                            <?php if($authorized): ?>
                                        <td class="col-essential text-end">
                                            <button 
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAttendanceModal"
                                            data-id="<?php echo $a['id']; ?>"
                                            data-status="<?php echo $a['status']; ?>"
                                            data-name="<?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name'], ENT_QUOTES); ?>"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if($role === 'Admin'):?>
                                            <button name = "delete_attendance"
                                                    class="btn btn-sm btn-outline-danger openDeleteAttendance"
                                                    data-id="<?php echo $a['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name'], ENT_QUOTES); ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteAttendanceModal"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>

                                        </td>
                                            <?php endif; ?>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No attendance recorded for this date
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit modal -->
 <div class="modal fade" id="editAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="attendance_id" id="edit_attendance_id">
                
                <div class="mb-3">
                     <label class="form-label">Member</label>
                     <input type="text" class="form-control" id="edit_member_name" readonly>
                </div>


                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="edit_status" class="form-select">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="excused">Excused</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="update_attendance" class="btn btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Delte Modal -->
 <div class="modal fade" id="deleteAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-trash"></i> Delete Attendance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="attendance_id" id="delete_attendance_id">

                <p class="mb-0">
                    Are you sure you want to delete attendance for
                    <strong id="delete_member_name"></strong>?
                </p>
                <small class="text-muted">This action cannot be undone.</small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" name="delete_attendance" class="btn btn-danger">
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>



<?php include 'footer.php'; ?>
<script>
document.getElementById('attendanceDate')?.addEventListener('change', function() {
    window.location.href = '?date=' + this.value;
});

const editModal = document.getElementById('editAttendanceModal');

editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    document.getElementById('edit_attendance_id').value =
        button.getAttribute('data-id');

    document.getElementById('edit_status').value =
        button.getAttribute('data-status');

    document.getElementById('edit_member_name').value =
        button.getAttribute('data-name');
});

const deleteModal = document.getElementById('deleteAttendanceModal');

deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    document.getElementById('delete_attendance_id').value =
        button.getAttribute('data-id');

    document.getElementById('delete_member_name').textContent =
        button.getAttribute('data-name');
});



</script>
