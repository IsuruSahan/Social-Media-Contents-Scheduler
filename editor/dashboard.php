<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 1);

include_once('../includes/header.php'); 
?>

<div class="main-card shadow-sm border-0">
    <div class="card-header-dark p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">EDITOR DASHBOARD</h5>
            <small class="text-secondary">Assigned Digital Campaigns</small>
        </div>
        <div class="text-end">
            <span class="badge bg-success rounded-pill px-3">Live Feed: Active</span>
        </div>
    </div>

    <div class="p-4 p-md-5 bg-white rounded-bottom border">
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 border-0">CAMPAIGN DETAILS</th>
                        <th class="py-3 border-0">DURATION</th>
                        <th class="py-3 border-0">PLATFORMS</th>
                        <th class="py-3 border-0">STATUS</th>
                        <th class="py-3 border-0 text-end">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Fetch all schedules from newest to oldest
                    $sql = "SELECT * FROM schedules ORDER BY id DESC";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $s_id = $row['id'];
                            
                            // Determine status color
                            $statusColor = ($row['status'] == 'Pending') ? 'warning' : 'success';
                            
                            // Get the platforms for this schedule to show icons/labels
                            $details_sql = "SELECT platform FROM schedule_details WHERE schedule_id = $s_id";
                            $details_res = mysqli_query($conn, $details_sql);
                            $plat_list = [];
                            while($d = mysqli_fetch_assoc($details_res)) { $plat_list[] = $d['platform']; }
                    ?>
                    <tr>
                        <td class="py-4">
                            <div class="fw-bold text-dark h6 mb-0"><?php echo $row['title']; ?></div>
                            <small class="text-muted text-uppercase" style="font-size: 10px;">ID: #SH-0<?php echo $row['id']; ?></small>
                        </td>
                        <td>
                            <div class="small fw-bold text-secondary">
                                <i class="bi bi-calendar-range"></i> <?php echo $row['start_date']; ?> 
                                <span class="mx-1 text-muted">→</span> 
                                <?php echo $row['end_date']; ?>
                            </div>
                        </td>
                        <td>
                            <?php foreach($plat_list as $plt): ?>
                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small fw-normal me-1">
                                    <?php echo $plt; ?>
                                </span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?> border border-<?php echo $statusColor; ?> px-3 py-2">
                                ● <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="view_task.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                Open Task
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-5 text-muted small italic'>No schedules dispatched yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>