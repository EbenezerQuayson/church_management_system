<?php
require_once __DIR__ . '/../../../config/config.php';

?>


<tr>
    <td>
        <div class="d-flex align-items-center">
            <?php 
$imgPath = $uploadDir . ($m['member_img'] ?? '');
$imgUrl  = BASE_URL . '/assets/uploads/members/' . ($m['member_img'] ?? '');

            ?>
            <div class="avatar rounded-circle me-3 d-flex align-items-center justify-content-center"
                 style="
                    width:40px; 
                    height:40px; 
                    background-color: var(--primary-color); 
                    color:white; 
                    overflow:hidden; 
                    flex-shrink:0; 
                    border: 2px solid #fff;
                    font-weight: bold;
                    font-size: 16px;
                 ">
                <?php if (!empty($m['member_img']) && file_exists($imgPath)): ?>
                    <img src="<?= htmlspecialchars($imgUrl) ?>" 
                         alt="Member Image" 
                         style="width:100%; height:100%; object-fit:cover; display:block;">
                <?php else: ?>
                    <?= strtoupper(substr($m['first_name'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div class="ms-2">
                <strong><?= htmlspecialchars($m['first_name'].' '.$m['last_name']) ?></strong><br>
                <small class="text-muted">
                    Joined: <?= date('M d, Y', strtotime($m['join_date'] ?? $m['created_at'])) ?>
                </small>
            </div>
        </div>
    </td>

    <td><?= htmlspecialchars($m['gender'] ?? 'N/A') ?></td>
    <td><?= htmlspecialchars($m['phone'] ?? 'N/A') ?></td>
    <td><?= htmlspecialchars($m['email'] ?? 'N/A') ?></td>

    <td>
        <?php
            $memberMinistries = $member->getMemberMinistries($m['id']);
            if (!empty($memberMinistries)) {
                foreach ($memberMinistries as $ministryName) {
                    echo '<span class="badge bg-info me-1">' . htmlspecialchars($ministryName) . '</span>';
                }
            } else {
                echo '<span class="badge bg-info">N/A</span>';
            }
        ?>
    </td>

    <td>
        <button class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#editMemberModal<?= $m['id']; ?>">
            <i class="fas fa-edit"></i>
        </button>

        <button class="btn btn-sm btn-outline-danger"
                onclick="confirmDelete(<?= $m['id']; ?>)">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
