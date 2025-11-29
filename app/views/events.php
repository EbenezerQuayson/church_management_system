<?php
// Events Page

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Event.php';

requireLogin();

$event = new Event();
$events = $event->getAll();
$message = '';
$message_type = '';


//Handle edit event
if(isset($_POST['edit_event'])){
    $eventId = (int)$_POST['event_id'];
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'event_date' => $_POST['event_date'] ?? '',
        'location' => trim($_POST['location'] ?? ''),
        'capacity' => $_POST['capacity'] ?? '',
        'organizer_id' => $_SESSION['user_id'],
        'status' => $_POST['status'] ?? 'scheduled',
    ];

    if (empty($data['title']) || empty($data['event_date'])) {
        $message = 'Title and date are required';
        $message_type = 'error';
    } else {
        if ($event->update($eventId, $data)) {
            $message = 'Event updated successfully!';
            $message_type = 'success';
            $events = $event->getAll();
        } else {
            $message = 'Failed to update event';
            $message_type = 'error';
        }
    }
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_event'])) {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'event_date' => $_POST['event_date'] ?? '',
        'location' => trim($_POST['location'] ?? ''),
        'capacity' => $_POST['capacity'] ?? '',
        'organizer_id' => $_SESSION['user_id'],
        'status' => $_POST['status'] ?? 'scheduled',
    ];

    if (empty($data['title']) || empty($data['event_date'])) {
        $message = 'Title and date are required';
        $message_type = 'error';
    } else {
        if ($event->create($data)) {
            $message = 'Event created successfully!';
            $message_type = 'success';
            $events = $event->getAll();
        } else {
            $message = 'Failed to create event';
            $message_type = 'error';
        }
    }
}
//soft delete event
if(isset($_GET['delete'])){
    $eventId = $_GET['delete'];
    if($event->hardDelete($eventId)){
        // $message = 'Event deleted successfully!';
        // $message_type = 'success';
        header("Location: events.php?msg=deleted");
        exit();
    } else {
        // $message = 'Failed to delete event';
        // $message_type = 'error';
        header("Location: events.php?msg=delete_failed");
        exit();
    }
    // $events = $event->getAll();
}

if(isset($_GET['msg'])){
    if($_GET['msg'] == 'deleted'){
        $message = 'Event deleted successfully!';
        $message_type = 'success';
    } elseif($_GET['msg'] == 'delete_failed'){
        $message = 'Failed to delete event';
        $message_type = 'error';
    }
}




?>
<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Events</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class="fas fa-calendar-plus"></i> Create Event
            </button>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Events Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: var(--primary-color); color: white;">
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $e): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($e['title']); ?></td>
                                    <td><?php echo date('M d, Y - g:i A', strtotime($e['event_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($e['location'] ?? 'TBD'); ?></td>
                                    <td><?php echo $e['capacity'] ?? '-'; ?></td>
                                    <td><span class="badge bg-info"><?php echo ucfirst($e['status']); ?></span></td>
                                    <td>
                                        <button 
    class="btn btn-sm btn-outline-primary editBtn"
    data-id="<?php echo $e['id']; ?>"
    data-title="<?php echo htmlspecialchars($e['title']); ?>"
    data-description="<?php echo htmlspecialchars($e['description']); ?>"
    data-date="<?php echo $e['event_date']; ?>"
    data-location="<?php echo htmlspecialchars($e['location']); ?>"
    data-capacity="<?php echo $e['capacity']; ?>"
    data-status="<?php echo $e['status']; ?>"
>
    <i class="fas fa-edit"></i>
</button>
 <button class="btn btn-sm btn-outline-danger" onclick=confirmDelete(<?php echo $e['id']; ?>)><i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="fas fa-calendar-plus"></i> Create New Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title *</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" id="description"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_date" class="form-label">Date & Time *</label>
                            <input type="datetime-local" class="form-control" name="event_date" id="event_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" name="location">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="scheduled">Scheduled</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Event</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color:white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">
                <input type="hidden" name="event_id" id="edit_event_id">
                <input type="hidden" name="edit_event" value="1">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Event Title *</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date & Time *</label>
                            <input type="datetime-local" class="form-control" id="edit_event_date" name="event_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" id="edit_location" name="location">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" id="edit_capacity" name="capacity">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="scheduled">Scheduled</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

            </form>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>
<script>
    function confirmDelete(eventId) {
        if (confirm('Are you sure you want to delete this event?')) {
            window.location.href = '?delete=' + eventId;
            console.log("Deleting event with ID: " + eventId);
        }
    }
</script>
