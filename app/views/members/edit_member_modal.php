<div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header" style="background-color: var(--primary-color); color: #fff;">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_member_id">

                <!-- BODY -->
                <div class="modal-body">

                    <!-- TABS NAV -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-personal">Personal Info</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-address">Address</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-church">Church Info</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-emergency">Emergency</button>
                        </li>
                    </ul>

                    <!-- TABS CONTENT -->
    <div class="tab-content px-3 pt-3" style="max-height: 60vh; overflow-y: auto;">

                        <!-- PERSONAL INFO -->
                        <div class="tab-pane fade show active" id="tab-personal">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>First Name *</label>
                                    <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Last Name *</label>
                                    <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" id="edit_email">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Phone</label>
                                    <input type="tel" class="form-control" name="phone" id="edit_phone">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Date of Birth</label>
                                    <input type="date" class="form-control" name="date_of_birth" id="edit_date_of_birth">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Gender</label>
                                    <select class="form-select" name="gender" id="edit_gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ADDRESS -->
                        <div class="tab-pane fade" id="tab-address">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="address" id="edit_address">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>City / Town</label>
                                    <input type="text" class="form-control" name="city" id="edit_city">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Region</label>
                                    <input type="text" class="form-control" name="region" id="edit_region">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Area</label>
                                    <input type="text" class="form-control" name="area" id="edit_area">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Landmark</label>
                                    <input type="text" class="form-control" name="landmark" id="edit_landmark">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>GhanaPost GPS</label>
                                    <input type="text" class="form-control" name="gps" id="edit_gps">
                                </div>
                            </div>
                        </div>
                        <!-- CHURCH INFO -->
                        <div class="tab-pane fade" id="tab-church">
                            <div class="mb-3">
                                <label>Ministries</label>
                            <select class="form-select" id="edit_ministries" name="ministries[]" multiple>
                                <?php foreach ($ministries as $ministry): ?>
                                    <option value="<?= htmlspecialchars($ministry['id']) ?>">
                                        <?= htmlspecialchars($ministry['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">
                                Hold Ctrl (Windows) or Cmd (Mac) to select multiple
                            </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Member Image</label>
                                <input type="file" name="member_img" class="form-control" id="edit_member_img">
                                <div id="currentMemberImg" class="mt-1"></div>
                            </div>
                        </div>
                        <!-- EMERGENCY -->
                        <div class="tab-pane fade" id="tab-emergency">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Emergency Contact Name</label>
                                    <input type="text" class="form-control" name="emergency_contact_name" id="edit_emergency_contact_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Emergency Phone</label>
                                    <input type="tel" class="form-control" name="emergency_phone" id="edit_emergency_phone">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer d-flex flex-wrap gap-2">
    <button type="button"
            class="btn btn-secondary flex-fill"
            data-bs-dismiss="modal">
        Cancel
    </button>

    <button type="submit"
            class="btn btn-primary flex-fill"
            name="update_member"
            >
        Save Changes
    </button>
</div>


            </form>
        </div>
    </div>
</div>
