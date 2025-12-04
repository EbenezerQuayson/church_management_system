<?php
// Attendance Page
$activePage = 'attendance';

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Member.php';

requireLogin();

$attendance = new Attendance();
$member_model = new Member();
$members = $member_model->getAll();
$attendance_date = $_GET['date'] ?? date('Y-m-d');
$today_attendance = $attendance->getByDate($attendance_date);
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'] ?? null;
    $status = $_POST['status'] ?? 'present';
    
    if ($member_id) {
        $data = [
            'member_id' => $member_id,
            'attendance_date' => $attendance_date,
            'status' => $status,
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        if ($attendance->recordAttendance($data)) {
            $message = 'Attendance recorded successfully!';
            $message_type = 'success';
            $today_attendance = $attendance->getByDate($attendance_date);
        } else {
            $message = 'Failed to record attendance';
            $message_type = 'error';
        }
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

        <!-- Quick Add Attendance -->
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

        <!-- Attendance List -->
        <div class="card">
            <div class="card-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="mb-0">Attendance for <?php echo date('F d, Y', strtotime($attendance_date)); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: var(--primary-color); color: white;">
                            <tr>
                                <th>Member Name</th>
                                <th>Status</th>
                                <th>Time Recorded</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($today_attendance)): ?>
                                <?php foreach ($today_attendance as $a): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name']); ?></td>
                                        <td>
                                            <?php 
                                            $status_color = 'secondary';
                                            if ($a['status'] === 'present') $status_color = 'success';
                                            elseif ($a['status'] === 'absent') $status_color = 'danger';
                                            elseif ($a['status'] === 'late') $status_color = 'warning';
                                            ?>
                                            <span class="badge bg-<?php echo $status_color; ?>"><?php echo ucfirst($a['status']); ?></span>
                                        </td>
                                        <td><?php echo date('g:i A', strtotime($a['created_at'])); ?></td>
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

<?php include 'footer.php'; ?>
<script>
document.getElementById('attendanceDate')?.addEventListener('change', function() {
    window.location.href = '?date=' + this.value;
});
</script>
