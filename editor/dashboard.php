<?php 
// 1. Setup Error Reporting
error_reporting(E_ALL); 
ini_set('display_errors', 1);

// 2. Load Header (Includes DB Connection)
include_once('../includes/header.php'); 
?>

<div class="main-card shadow-sm border-0 mb-5">
    <div class="card-header-dark p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-white">EDITOR DASHBOARD</h5>

        </div>
        <div class="text-end">
            <span class="badge bg-success rounded-pill px-3 shadow-sm">
                <i class="bi bi-broadcast me-1"></i> Live Feed: Active
            </span>
        </div>
    </div>

    <div class="p-4 p-md-5 bg-white rounded-bottom border">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 border-0 ps-3">CAMPAIGN DETAILS</th>
                        <th class="py-3 border-0">DURATION</th>
                        <th class="py-3 border-0">PLATFORMS</th>
                        <th class="py-3 border-0 text-center">STATUS</th>
                        <th class="py-3 border-0 text-end pe-3">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Fetch all schedules from newest to oldest
                    $sql = "SELECT * FROM schedules ORDER BY id DESC";
                    $result = mysqli_query($conn, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $s_id = (int)$row['id'];
                            
                            // --- BULLETPROOF STATUS LOGIC ---
                            // 1. Clean the data (Handle NULLs and extra spaces)
                            $dbStatus = isset($row['status']) ? trim($row['status']) : '';
                            
                            // 2. Set Default (Fallback)
                            $displayText = "Pending";
                            $bgClass = "bg-warning text-dark"; // Yellow

                            // 3. Precise Matching (Ignoring Case)
                            if (strcasecmp($dbStatus, 'In Progress') == 0) {
                                $displayText = "In Progress";
                                $bgClass = "bg-info text-white"; // Blue
                            } elseif (strcasecmp($dbStatus, 'Completed') == 0) {
                                $displayText = "Completed";
                                $bgClass = "bg-success text-white"; // Green
                            } elseif (strcasecmp($dbStatus, 'Pending') == 0) {
                                $displayText = "Pending";
                                $bgClass = "bg-warning text-dark"; // Yellow
                            } else {
                                // If the status is empty, blank, or NULL, we force it to show as Pending
                                $displayText = "Pending";
                                $bgClass = "bg-warning text-dark";
                            }

                            // --- FETCH PLATFORMS ---
                            $details_sql = "SELECT platform FROM schedule_details WHERE schedule_id = $s_id";
                            $details_res = mysqli_query($conn, $details_sql);
                            $plat_list = [];
                            while($d = mysqli_fetch_assoc($details_res)) { 
                                $plat_list[] = $d['platform']; 
                            }
                    ?>
                    <tr>
                        <td class="py-4 ps-3">
                            <div class="fw-bold text-dark h6 mb-1"><?php echo htmlspecialchars($row['title']); ?></div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                                <i class="bi bi-hash"></i> SH-0<?php echo $row['id']; ?>
                            </small>
                        </td>

                        <td>
                            <div class="small fw-bold text-secondary">
                                <?php echo $row['start_date']; ?> 
                                <span class="mx-1 text-muted opacity-50">→</span> 
                                <?php echo $row['end_date']; ?>
                            </div>
                        </td>

                        <td>
                            <?php if(!empty($plat_list)): ?>
                                <?php foreach($plat_list as $plt): ?>
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small fw-normal me-1" style="font-size: 10px;">
                                        <?php echo $plt; ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <small class="text-muted italic">No platforms</small>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <span class="badge <?php echo $bgClass; ?> px-3 py-2 shadow-sm fw-bold" style="min-width: 115px; font-size: 11px;">
                                <?php echo strtoupper($displayText); ?>
                            </span>
                        </td>

                        <td class="text-end pe-3">
                            <a href="view_task.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                Open Task <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        } // End While
                        } else {
                        // Empty State - Corrected quotes here
                        echo "<tr><td colspan='5' class='text-center py-5 text-muted small italic'>
                                <i class='bi bi-inbox d-block h1 opacity-25'></i>
                                No digital campaigns currently dispatched.
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once('../includes/footer.php'); ?>