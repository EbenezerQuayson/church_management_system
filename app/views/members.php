<?php
// Members Page
$activePage='members';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Ministry.php';
require_once __DIR__ . '/../../vendor/autoload.php';
$db = Database::getInstance()->getConnection();
$ministryModel = new Ministry();
$ministries = $ministryModel->getAllActive();
$member = new Member();



requireLogin();

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_members'])) {

    if (!empty($_FILES['import_file']['name'])) {

        $fileTmpPath = $_FILES['import_file']['tmp_name'];
        $fileName = $_FILES['import_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            header("Location: members.php?msg=import_failed_type");
            exit();
        }

        try {
            $spreadsheet = IOFactory::load($fileTmpPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip the header row
            array_shift($rows);

            $importedCount = 0;

            foreach ($rows as $row) {
                $data = [
                    'first_name' => trim($row[1] ?? ''),
                    'last_name'  => trim($row[2] ?? ''),
                    'email'      => trim($row[3] ?? ''),
                    'phone'      => trim($row[4] ?? ''),
                    'gender'     => ucfirst(trim($row[5] ?? '')),
                    'date_of_birth' => $row[6] ?? '',
                    'join_date'  => $row[7] ?? date('Y-m-d'),
                    'address'    => $row[10] ?? '',
                    'city'       => $row[11] ?? '',
                    'region'     => $row[9] ?? '',
                    'area'       => $row[12] ?? '',
                    'emergency_contact_name' => $row[13] ?? '',
                    'emergency_phone'        => $row[14] ?? '',
                    'member_img' => null
                ];

                if (!empty($data['first_name']) && !empty($data['last_name'])) {
                    $existingMemberId = $member->exists($data['first_name'], $data['last_name'], $data['email'], $data['phone']);

 if (!$existingMemberId) {
    $newMemberId = $member->create($data);
    if ($newMemberId) $importedCount++;
} else {
    $newMemberId = $existingMemberId;
}

// Handle Ministries for both new and existing members
$ministries = explode(',', $row[8] ?? '');
foreach ($ministries as $ministryName) {
    $ministryName = trim($ministryName);
    if (!$ministryName) continue;

    $ministryId = $ministryModel->getIdByName($ministryName);
    if (!$ministryId) $ministryId = $ministryModel->create($ministryName);

    $checkLink = $db->prepare("SELECT 1 FROM ministry_members WHERE member_id = :member_id AND ministry_id = :ministry_id");
    $checkLink->execute([
        ':member_id' => $newMemberId,
        ':ministry_id' => $ministryId
    ]);

    if (!$checkLink->fetch()) {
        $stmt = $db->prepare("INSERT INTO ministry_members (member_id, ministry_id, role, joined_date, created_at)
                              VALUES (:member_id, :ministry_id, 'Member', :joined_date, NOW())");
        $stmt->execute([
            ':member_id' => $newMemberId,
            ':ministry_id' => $ministryId,
            ':joined_date' => $data['join_date'] ?? date('Y-m-d')
        ]);
    }
}

                } else {
                    // Log or handle invalid row (missing required fields)  
                    error_log("Skipping row due to missing required fields: " . implode(',', $row));
            }
            }

            if ($importedCount > 0) {
                header("Location: members.php?msg=imported&count=$importedCount");
            } else {
                header("Location: members.php?msg=import_failed");
            }

        } catch (Exception $e) {
            header("Location: members.php?msg=import_failed");
        }

    } else {
        header("Location: members.php?msg=import_failed_no_file");
    }

    exit();
}






if (isset($_GET['view']) && in_array($_GET['view'], ['flat', 'grouped'])) {
    $_SESSION['members_view'] = $_GET['view'];
}

$viewMode = $_SESSION['members_view'] ?? 'flat';


$search = trim($_GET['search'] ?? '');
if($search !== ''){
   $members = $member->search($search);
} else {
    $members = $member->getAll();
}
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_member'])) {

    // 🔹 Ministries come separately (array)
    $ministries = $_POST['ministries'] ?? [];

    $data = [
        // Identity fields
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name'  => trim($_POST['last_name'] ?? ''),
        'email'      => trim($_POST['email'] ?? ''),
        'phone'      => trim($_POST['phone'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?? '',
        'gender'     => $_POST['gender'] ?? '',

        // Church related fields 
        'join_date'  => $_POST['join_date'] ?? date('Y-m-d'),

        // Address (Ghana)
        'address'  => trim($_POST['address'] ?? ''),
        'city'     => trim($_POST['city'] ?? ''),
        'region'   => trim($_POST['region'] ?? ''),
        'area'     => trim($_POST['area'] ?? ''),
        'landmark' => trim($_POST['landmark'] ?? ''),
        'gps'      => trim($_POST['gps'] ?? ''),

        // Emergency
        'emergency_contact_name' => trim($_POST['emergency_contact_name'] ?? ''),
        'emergency_phone'        => trim($_POST['emergency_phone'] ?? ''),

        // Image placeholder
        'member_img' => null,
    ];

    // Basic Validation
    if (empty($data['first_name']) || empty($data['last_name'])) {
        header("Location: members.php?msg=add_failed");
        exit();
    }


    // Handle image upload
    if (!empty($_FILES['member_img']['name'])) {
        $uploadDir = __DIR__ . '/../../assets/uploads/members/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($_FILES['member_img']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('member_', true) . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['member_img']['tmp_name'], $targetPath)) {
            $data['member_img'] = $filename;
        }
    }

   $newMemberId = $member->create($data);

if ($newMemberId) {
    // Get selected ministries
    $ministryIds = $_POST['ministries'] ?? [];

    // If none selected, assign default ministry (e.g., id = 1)
    if (empty($ministryIds)) {
        $ministryIds = [1]; // Replace 1 with your default ministry ID
    }

    $insertSql = "INSERT INTO ministry_members (member_id, ministry_id, role, joined_date, created_at)
                  VALUES (:member_id, :ministry_id, :role, :joined_date, NOW())";
    $stmt = $db->prepare($insertSql);

    foreach ($ministryIds as $ministryId) {
        $stmt->execute([
            ':member_id' => $newMemberId,
            ':ministry_id' => $ministryId,
            ':role' => 'Member',
            ':joined_date' => $data['join_date'] ?? date('Y-m-d')
        ]);
    }

    header("Location: members.php?msg=added");
} else {
    header("Location: members.php?msg=add_failed");
}
exit();

}

if (isset($_POST['update_member'])) {
    $editId = (int)$_POST['edit_id'];

    $data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name'  => trim($_POST['last_name'] ?? ''),
        'email'      => trim($_POST['email'] ?? ''),
        'phone'      => trim($_POST['phone'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?? '',
        'gender'     => $_POST['gender'] ?? '',
        'join_date'  => $_POST['join_date'] ?? date('Y-m-d'),
        'address'    => trim($_POST['address'] ?? ''),
        'city'       => trim($_POST['city'] ?? ''),
        'region'     => trim($_POST['region'] ?? ''),
        'area'       => trim($_POST['area'] ?? ''),
        'landmark'   => trim($_POST['landmark'] ?? ''),
        'gps'        => trim($_POST['gps'] ?? ''),
        'emergency_contact_name' => trim($_POST['emergency_contact_name'] ?? ''),
        'emergency_phone'        => trim($_POST['emergency_phone'] ?? '')
    ];

    // Handle image upload if provided
    if (!empty($_FILES['member_img']['name'])) {
        $uploadDir = __DIR__ . '/../../assets/uploads/members/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $ext = pathinfo($_FILES['member_img']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('member_', true) . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['member_img']['tmp_name'], $targetPath)) {
            $data['member_img'] = $filename;
        }
    }

    if ($member->update($editId, $data)) {

        // Update ministries
        $selectedMinistries = $_POST['ministries'] ?? [];
        $member->updateMinistries($editId, $selectedMinistries);

        header("Location: members.php?msg=updated");
        exit();
    } else {
        header("Location: members.php?msg=update_failed");
        exit();
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
        case 'exported':
            $message = 'Members exported successfully.';
            $message_type = 'success';
            break;

        case 'export_failed':
            $message = 'Failed to export members.';
            $message_type = 'error';
            break;
        case 'imported':
            $count = $_GET['count'] ?? 0;
            $message = "Successfully imported $count members!";
            $message_type = 'success';
            break;
        case 'import_failed':
            $message = 'Failed to import members. Please check the file format and data.';
            $message_type = 'error';
            break;
        case 'import_failed_type':
            $message = 'Invalid file type. Only XLSX, XLS, and CSV are allowed.';
            $message_type = 'error';
            break;
        case 'import_failed_no_file':
            $message = 'No file selected for import.';
            $message_type = 'error';
            break;


        default:
            $message = '';
            $message_type = '';
            break;
    }
}

   $uploadDir = __DIR__ . '/../../assets/uploads/members/'; // For server check

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
        <br>

        <!-- Search Input -->
         <form method="GET" class="mb-3">
            <div class="input-group">
          <span class="input-group-text">
            <i class="bi bi-search"></i>
          </span>
          <input
          type="text"
          name="search"
          id="memberSearch"
          class="form-control"
          placeholder="Search members by name, phone, or email..."
          autocomplete = "off"
          >
          <!-- <button class="btn btn-outline-primary" type="submit" >Search</button> -->
            </div>
         </form>
<br>
        <!-- Members Table -->
<div class="row mb-4 g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body ">
                        <h5 class="card-title mb-3 ">Quick Actions</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <!-- <a href="members.php?view=flat" class="btn btn-outline-primary <?= $viewMode === 'flat' ? 'active' : '' ?>">
                                <i class="bi bi-layout-text-sidebar-reverse"></i> Flat View
                            </a>
                            <a href="members.php?view=grouped" class="btn btn-outline-primary <?= $viewMode === 'grouped' ? 'active' : '' ?>">
                                <i class="bi bi-diagram-3"></i> Grouped by Ministry
                            </a> -->
                            <a href="<?= BASE_URL ?>/app/views/members/export_members.php" class="btn btn-success" onclick="return confirm('Export members to Excel?');">
                             <i class="bi bi-file-earmark-excel"></i> Export Members </a>

                             <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importMembersModal">
    <i class="bi bi-upload"></i> Import Members
</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: var(--primary-color); color: white;">
                            <tr>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Ministry</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <?php
                                $groupedMembers = [];

                                if ($viewMode === 'grouped') {
                                    foreach ($members as $m) {
                                        $ministries = $member->getMemberMinistries($m['id']);

                                        if (empty($ministries)) {
                                            $groupedMembers['No Ministry'][] = $m;
                                        } else {
                                            foreach ($ministries as $ministryName) {
                                                $groupedMembers[$ministryName][] = $m;
                                            }
                                        }
                                    }
                                }
                                ?>

   <tbody id="membersTable">
<?php if ($viewMode === 'flat'): ?>

    <?php if (!empty($members)): ?>
        <?php foreach ($members as $m): ?>
            <?php include 'members/member_row.php'; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                No members found
            </td>
        </tr>
    <?php endif; ?>

<?php endif; ?>

<?php if ($viewMode === 'grouped'): ?>

<?php foreach ($groupedMembers as $ministryName => $group): ?>
    <?php $collapseId = 'ministry_' . md5($ministryName); ?>

    <!-- Ministry Header -->
    <tr class="table-light">
        <td colspan="6">
            <button class="btn btn-sm btn-link text-decoration-none fw-bold"
                    data-bs-toggle="collapse"
                    data-bs-target="#<?= $collapseId ?>">
                <i class="fas fa-chevron-down me-2"></i>
                <?= htmlspecialchars($ministryName) ?>
                <span class="badge bg-secondary ms-2"><?= count($group) ?></span>
            </button>
        </td>
    </tr>
</tbody>
    <!-- Members -->
    <tr class="collapse show" id="<?= $collapseId ?>">
        <td colspan="6" class="p-0">
            <table class="table mb-0">
                <tbody>
                <?php foreach ($group as $m): ?>
                    <?php include 'members/member_row.php'; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </td>
    </tr>

<?php endforeach; ?>

<?php endif; ?>
</tbody>










<?php 
$includedMembers = [];
foreach ($members as $m): 
    if (!in_array($m['id'], $includedMembers)) {
        include 'members/edit_member_modal.php';
        $includedMembers[] = $m['id'];
    }
endforeach; 
?>


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
            <form method="POST" enctype="multipart/form-data">
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
                            </select>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ministry" class="form-label">Ministry</label>
                        <select class="form-select" name="ministries[]" multiple>
    <?php foreach ($ministries as $ministry): ?>
        <option value="<?= htmlspecialchars($ministry['id']) ?>">
            <?= htmlspecialchars($ministry['name']) ?>
        </option>
    <?php endforeach; ?>
</select>
<small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple ministries</small>

                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address">
                    </div>
               </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City/Town</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="region" class="form-label">Region</label>
                            <input type="text" class="form-control" name="region">
                        </div>
                    </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="area" class="form-label">Area</label>
                        <input type="text" class="form-control" name="area">
                    </div>
                     <div class="col-md-6 mb-3">
                        <label for="landmark" class="form-label">Landmark</label>
                        <input type="text" class="form-control" name="landmark">
                    </div>
               </div>
               <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gps" class="form-label">GhanaPost GPS (optional)</label>
                        <input type="text" class="form-control" name="gps">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="member_img" class="form-label">Member Image</label>
                        <input type="file" class="form-control" name="member_img" id="member_img" accept="image/png,image/jpeg,image/jpg,image/gif">
                            <small class="text-muted">Optional. Accepted formats: PNG, JPEG, JPG, GIF (Max 5MB)</small>

                    </div>

               </div>
               
                <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                            <input type="text" class="form-control" name="emergency_contact_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="emergency_phone" class="form-label">Emergency Phone</label>
                            <input type="tel" class="form-control" name="emergency_phone">
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
             </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Members Modal -->
<div class="modal fade" id="importMembersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="bi bi-upload"></i> Import Members</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Upload Excel/CSV File</label>
                        <input type="file" class="form-control" name="import_file" id="import_file" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Accepted formats: XLSX, XLS, CSV</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="import_members" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>





<?php include 'footer.php'; ?>
<script>
const searchInput = document.getElementById('memberSearch');
const tableBody = document.getElementById('membersTable');

searchInput.addEventListener('keyup', function () {
    const query = this.value.trim();

    fetch(`../ajax/search_members.php?q=${encodeURIComponent(query)}`)
        .then(res => res.text())
        .then(html => {
            tableBody.innerHTML = html;
        })
        .catch(err => {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Error loading results
                    </td>
                </tr>`;
        });
});


let timer;
searchInput.addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
        const query = this.value.trim();

        fetch(`../ajax/search_members.php?q=${encodeURIComponent(query)}`)
            .then(res => res.text())
            .then(html => tableBody.innerHTML = html)
            .catch(() => {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger">
                            Error loading results
                        </td>
                    </tr>`;
            });
    }, 300);
});



function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this member?')) {
        window.location.href = '?delete=' + id;
        console.log('Delete member:', id);
    }
}
</script>
