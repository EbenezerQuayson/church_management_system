<?php
// Ministries Page

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();

$db = Database::getInstance();
$ministries = $db->fetchAll("SELECT * FROM ministries WHERE status = 'active' ORDER BY name");
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'leader_id' => $_POST['leader_id'] ?? null,
        'leader_email' => trim($_POST['leader_email'] ?? ''),
        'meeting_day' => $_POST['meeting_day'] ?? '',
        'meeting_time' => $_POST['meeting_time'] ?? '',
        'location' => trim($_POST['location'] ?? ''),
        'status' => $_POST['status'] ?? 'active',
    ];

    if (empty($data['name'])) {
        $message = 'Ministry name is required';
        $message_type = 'error';
    } else {
        $sql = "INSERT INTO ministries (name, description, leader_id, leader_email, meeting_day, meeting_time, location, status)
                VALUES (:name, :description, :leader_id, :leader_email, :meeting_day, :meeting_time, :location, :status)";
        
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':leader_id', $data['leader_id']);
        $stmt->bindParam(':leader_email', $data['leader_email']);
        $stmt->bindParam(':meeting_day', $data['meeting_day']);
        $stmt->bindParam(':meeting_time', $data['meeting_time']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':status', $data['status']);

        if ($stmt->execute()) {
            $message = 'Ministry created successfully!';
            $message_type = 'success';
            $ministries = $db->fetchAll("SELECT * FROM ministries WHERE status = 'active' ORDER BY name");
        } else {
            $message = 'Failed to create ministry';
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
            <h2 class="fw-bold" style="color: var(--primary-color);">Ministries</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMinistryModal">
                <i class="fas fa-handshake"></i> Add Ministry
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
                        <label for="name" class="form-label">Ministry Name *</label>
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
                    <button type="submit" class="btn btn-primary">Create Ministry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
