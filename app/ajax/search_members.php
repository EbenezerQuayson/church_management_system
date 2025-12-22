<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Member.php';

$member = new Member();
$search = trim($_GET['q'] ?? '');

if ($search === '') {
    $members = $member->getAll();
} else {
    $members = $member->search($search);
}

if (empty($members)) {
    echo '<tr>
            <td colspan="6" class="text-center text-muted py-4">
                Member not found
            </td>
          </tr>';
    exit;
}

foreach ($members as $m): ?>
<tr>
    <td>
        <div class="d-flex align-items-center">
            <div class="avatar bg-primary text-white rounded-circle me-3"
                 style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                <?= strtoupper(substr($m['first_name'], 0, 1)); ?>
            </div>
            <div>
                <strong><?= htmlspecialchars($m['first_name'].' '.$m['last_name']); ?></strong><br>
                <small class="text-muted">
                    Joined: <?= date('M d, Y', strtotime($m['join_date'] ?? $m['created_at'])); ?>
                </small>
            </div>
        </div>
    </td>
    <td><?= htmlspecialchars($m['gender'] ?? 'N/A'); ?></td>
    <td><?= htmlspecialchars($m['phone'] ?? 'N/A'); ?></td>
    <td><?= htmlspecialchars($m['email'] ?? 'N/A'); ?></td>
    <td><span class="badge bg-info"><?= htmlspecialchars($m['ministry'] ?? 'N/A'); ?></span></td>
    <td>
        <button class="btn btn-sm btn-outline-primary">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
<?php endforeach; ?>
