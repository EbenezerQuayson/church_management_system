<?php
$currentMinistries = $member->getMinistries($m['id']); // for multi-select
?>
<div class="modal fade" id="editMemberModal<?= $m['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?= $m['id']; ?>">
                <div class="modal-body">
                    <!-- Identity Fields -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>First Name *</label>
                            <input type="text" class="form-control" name="first_name" 
                                   value="<?= htmlspecialchars($m['first_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Last Name *</label>
                            <input type="text" class="form-control" name="last_name" 
                                   value="<?= htmlspecialchars($m['last_name']); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?= htmlspecialchars($m['email']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?= htmlspecialchars($m['phone']); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" 
                                   value="<?= htmlspecialchars($m['date_of_birth']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Gender</label>
                            <select class="form-select" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?= ($m['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($m['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ministries -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Ministry</label>
                            <select class="form-select" name="ministries[]" multiple>
                                <?php foreach ($ministries as $min): ?>
                                    <option value="<?= $min['id']; ?>"
                                        <?= in_array($min['id'], $currentMinistries) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($min['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</small>
                        </div>

                        <!-- Address fields -->
                        <div class="col-md-6 mb-3">
                            <label>Address</label>
                            <input type="text" class="form-control" name="address" 
                                   value="<?= htmlspecialchars($m['address']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>City/Town</label>
                            <input type="text" class="form-control" name="city" 
                                   value="<?= htmlspecialchars($m['city']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Region</label>
                            <input type="text" class="form-control" name="region" 
                                   value="<?= htmlspecialchars($m['region']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Area</label>
                            <input type="text" class="form-control" name="area" 
                                   value="<?= htmlspecialchars($m['area']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Landmark</label>
                            <input type="text" class="form-control" name="landmark" 
                                   value="<?= htmlspecialchars($m['landmark']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>GhanaPost GPS</label>
                            <input type="text" class="form-control" name="gps" 
                                   value="<?= htmlspecialchars($m['gps']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Member Image</label>
                            <input type="file" class="form-control" name="member_img" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Emergency Contact Name</label>
                            <input type="text" class="form-control" name="emergency_contact_name" 
                                   value="<?= htmlspecialchars($m['emergency_contact_name']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Emergency Phone</label>
                            <input type="tel" class="form-control" name="emergency_phone" 
                                   value="<?= htmlspecialchars($m['emergency_phone']); ?>">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_member" class="btn btn-primary">Update Member</button>
                </div>
            </form>
        </div>
    </div>
</div>