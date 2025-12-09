<?php
$activePage = 'service';
$pageTitle = 'Service';




include "header.php";
include "sidebar.php";
?>

<div class="main-content">
    
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-color);">Services</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="fas fa-plus"></i> Add Service
            </button>
        </div>
       <div class="row g-4">
             <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                            <div class="card-icon outline-primary">
                            <i class="fas fa-music"></i>
                        </div>
                        <h5 class="card-title" style="color: var(--primary-color);">Sunday Worship
                        </h5>
                        <p class="card-text text-muted mb-2">8:00 AM & 10:00 AM</p>
                        <p class="small">Experience uplifting worship and inspiring messages in a welcoming atmosphere.</p>
                    </div>
                     <div class="card-footer bg-light">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#">
                                <i class="fas fa-info-circle"></i> Details
                            </button>
                        </div>
                </div>
             </div>

                <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                            <div class="card-icon outline-primary">
                            <i class="fas fa-book"></i>
                        </div>
                        <h5 class="card-title" style="color: var(--primary-color);">Bible Study
                        </h5>
                        <p class="card-text text-muted mb-2">Wednesday 7:00 PM </p>
                        <p class="small">Deepen your understanding of Scripture in a supportive community.</p>
                     <div class="card-footer bg-light">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#">
                                <i class="fas fa-info-circle"></i> Details
                            </button>
                        </div>
                </div>
                </div>
                </div>

                
                    <!-- <div class="card-icon">
                            <i class="fas fa-music"></i>
                        </div> -->
                <!-- <div class="col-md-6 col-lg-3">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4>Bible Study</h4>
                        <p class="text-muted mb-2">Wednesday 7:00 PM</p>
                        <p class="small">Deepen your understanding of Scripture in a supportive community.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-children"></i>
                        </div>
                        <h4>Youth Ministry</h4>
                        <p class="text-muted mb-2">Saturdays 5:00 PM</p>
                        <p class="small">Fun, faith-building activities for young people ages 13-25.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-praying-hands"></i>
                        </div>
                        <h4>Prayer Meeting</h4>
                        <p class="text-muted mb-2">Thursday 6:00 PM</p>
                        <p class="small">Come together to intercede for our church and community.</p>
                    </div> -->
               
            </div>
        <!-- <div class="row mb-4">
            <div class="col-12">
                <h3>Service</h3>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body">
                <p>Service management content here.</p>
            </div>
        </div> -->
    </div>
</div>

<?php include "footer.php"; ?>
