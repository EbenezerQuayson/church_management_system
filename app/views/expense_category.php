<?php
// Page active
$activePage = 'expense_category';

// Required files
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ExpenseCategory.php';

requireLogin();

$user_id = $_SESSION['user_id'];


$pdo = Database::getInstance()->getConnection();
$category = new ExpenseCategory($pdo);

// Fetch all
$categories = $category->getAll()->fetchAll(PDO::FETCH_ASSOC);

// Message system (same format as donations)
$message = '';
$message_type = '';

if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':
            $message = 'Category added successfully!';
            $message_type = 'success';
            break;
        case 'add_failed':
            $message = 'Failed to add category.';
            $message_type = 'error';
            break;
        case 'updated':
            $message = 'Category updated successfully!';
            $message_type = 'success';
            break;
        case 'update_failed':
            $message = 'Failed to update category.';
            $message_type = 'error';
            break;
        case 'deleted':
            $message = 'Category deleted successfully!';
            $message_type = 'success';
            break;
        case 'delete_failed':
            $message = 'Failed to delete category.';
            $message_type = 'error';
            break;
    }
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';
    
    /* CREATE */
    if ($action === 'create') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description'] ?? '');

        if ($category->create($name, $description)) {
            header("Location: expense_category.php?msg=added");
            exit();
        } else {
            header("Location: expense_category.php?msg=add_failed");
            exit();
        }
    }

    /* EDIT */
    if ($action === 'edit') {
        $id = (int) $_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description'] ?? '');

        if ($category->update($id, $name, $description)) {
            header("Location: expense_category.php?msg=updated");
            exit();
        } else {
            header("Location: expense_category.php?msg=update_failed");
            exit();
        }
    }

    /* DELETE */
    if ($action === 'delete') {
        $id = (int) $_POST['id'];

        if ($category->delete($id)) {
            header("Location: expense_category.php?msg=deleted");
            exit();
        } else {
            header("Location: expense_category.php?msg=delete_failed");
            exit();
        }
    }
}
?>


<?php include 'header.php'; ?>
<div class="main-content">
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid">

        <!-- Title + Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Expense Categories</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Category Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: var(--primary-color); color:white;">
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th style="width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat['name']) ?></td>
                                    <td><?= htmlspecialchars($cat['description']) ?></td>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-outline-primary editCategoryBtn"
                                            data-id="<?= $cat['id'] ?>"
                                            data-name="<?= htmlspecialchars($cat['name']) ?>"
                                            data-description="<?= htmlspecialchars($cat['description']) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button 
                                            class="btn btn-sm btn-outline-danger deleteCategoryBtn"
                                            data-id="<?= $cat['id'] ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCategoryModal"
                                        >
                                            <i class="fas fa-trash"></i>
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



<!-- Add Category -->
 <div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" value="create">
            
            <div class="modal-header" style="background:var(--primary-color); color:white;">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add Category</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input required type="text" name="name" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Save</button>
            </div>

        </form>
    </div>
</div>


<!-- Edit Category -->
 <div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-header" style="background:var(--primary-color); color:white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Category</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input required type="text" name="name" id="edit_name" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit_description" class="form-control"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Save Changes</button>
            </div>

        </form>
    </div>
</div>

<!-- Delete Category -->
 <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="delete_id">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Delete Category</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Are you sure you want to delete this category?</p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger">Delete</button>
            </div>

        </form>
    </div>
</div>


<script>
document.querySelectorAll('.editCategoryBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_name').value = btn.dataset.name;
        document.getElementById('edit_description').value = btn.dataset.description;
    });
});

document.querySelectorAll('.deleteCategoryBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('delete_id').value = btn.dataset.id;
    });
});
</script>

<?php include 'footer.php' ?>
