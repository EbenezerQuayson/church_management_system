<?php
require_once __DIR__ . '/../../../config/config.php';

$memberMinistries = $member->getMemberMinistries($m['id']);

?>


<tr>
    <td class="col-essential">
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

    <td class="col-hide-mobile"><?= htmlspecialchars($m['gender'] ?? 'N/A') ?></td>
    <td class="col-hide-mobile"><?= htmlspecialchars($m['phone'] ?? 'N/A') ?></td>
    <td class="col-hide-mobile"><?= htmlspecialchars($m['email'] ?? 'N/A') ?></td>

    <td class="col-hide-mobile">
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

    <td class="col-essential text-end">
        <button class="btn btn-sm btn-outline-primary viewMemberBtn" data-member-id="<?= $m['id']; ?>"
                                   data-first-name="<?php echo htmlspecialchars($m['first_name']); ?>"
                                   data-last-name="<?php echo htmlspecialchars($m['last_name']); ?>"
                                   data-gender="<?php echo htmlspecialchars($m['gender']); ?>"
                                    data-phone="<?php echo htmlspecialchars($m['phone']); ?>"
                                    data-email="<?php echo htmlspecialchars($m['email']); ?>"
                                    data-address="<?php echo htmlspecialchars($m['address']); ?>"
                                    data-area="<?php echo htmlspecialchars($m['area']); ?>"
                                    data-ministry='<?= htmlspecialchars(json_encode($memberMinistries), ENT_QUOTES) ?>'
                                    data-date-of-birth="<?php echo htmlspecialchars($m['date_of_birth']); ?>"
                                    data-join-date="<?php echo htmlspecialchars($m['join_date']); ?>"
                                    data-city="<?php echo htmlspecialchars($m['city']); ?>"
                                    data-region="<?php echo htmlspecialchars($m['region']); ?>"
                                    data-landmark="<?php echo htmlspecialchars($m['landmark']); ?>"
                                    data-gps="<?php echo htmlspecialchars($m['gps']); ?>"
                                    data-member-img="<?= !empty($m['member_img']) ? BASE_URL . '/assets/uploads/members/' . htmlspecialchars($m['member_img']) : '' ?>"
                                    data-emergency-contact-name = "<?php echo htmlspecialchars($m['emergency_contact_name']); ?>"
                                    data-emergency-phone = "<?php echo htmlspecialchars($m['emergency_phone']); ?>"
                                    data-bs-target="#memberDetails"
                                    data-bs-toggle="modal">
                                    <i class="fas fa-eye"></i>
                                </button>
        <!-- <button class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#editMemberModal<?= $m['id']; ?>">
            <i class="fas fa-edit"></i>
        </button>

        <button class="btn btn-sm btn-outline-danger"
                onclick="confirmDelete(<?= $m['id']; ?>)">
            <i class="fas fa-trash"></i>
        </button> -->
    </td>
</tr>
