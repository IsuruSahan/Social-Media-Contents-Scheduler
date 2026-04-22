<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 1);
include_once('../includes/header.php'); 

$id = (int)$_GET['id'];

// --- LOGIC: ACKNOWLEDGE SCHEDULE ---
if(isset($_POST['acknowledge'])) {
    mysqli_query($conn, "UPDATE schedules SET status = 'In Progress' WHERE id = $id");
    echo "<script>window.location.href='view_task.php?id=$id&ack=1';</script>";
    exit;
}

// Fetch Schedule
$res = mysqli_query($conn, "SELECT * FROM schedules WHERE id = $id");
$task = mysqli_fetch_assoc($res);
if(!$task) die("Schedule not found.");
?>

<style>
    :root {
        --sw-green: #10b981;
        --sw-orange: #f39c12;
        --sw-dark: #1e293b;
    }
    .detail-header { background: var(--sw-dark); color: white; border-radius: 12px 12px 0 0; }
    .section-num { font-weight: 800; color: #64748b; margin-right: 5px; }
    .platform-card { border-radius: 15px; overflow: hidden; border: 2px solid transparent; margin-bottom: 30px; }
    .card-fb-ig { border-color: var(--sw-green); }
    .card-tiktok { border-color: var(--sw-orange); }
    .platform-header-fb { background: var(--sw-green); color: white; padding: 10px 20px; font-weight: 700; }
    .platform-header-tt { background: var(--sw-orange); color: white; padding: 10px 20px; font-weight: 700; }
    
    /* Dynamic Badge Colors */
    .badge-pre { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .badge-mid { background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; }
    .badge-end { background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; }
    
    .placement-badge { border-radius: 50px; padding: 5px 15px; font-size: 11px; font-weight: 700; margin-right: 8px; text-transform: uppercase; }
    .asset-row { background: #f8fafc; border-radius: 8px; padding: 12px 20px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; }
    .btn-ack { background-color: var(--sw-green); color: white; font-weight: 800; padding: 15px; border-radius: 10px; border: none; width: 100%; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
    .btn-ack:hover { background-color: #059669; transform: translateY(-2px); }
    .feedback-input { border-radius: 10px 0 0 10px; border: 1px solid #e2e8f0; padding: 12px; }
    .btn-feedback { background: var(--sw-orange); color: white; border-radius: 0 10px 10px 0; border: none; padding: 0 25px; font-weight: 700; }
</style>

<div class="container py-4" style="max-width: 900px;">

    <div class="detail-header p-4 d-flex justify-content-between align-items-center shadow-sm">
        <h4 class="mb-0 fw-bold">SCHEDULE DETAILS: #<?php echo $task['id']; ?></h4>
        <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold" style="font-size: 10px;">
            ● <?php echo strtoupper($task['status']); ?>
        </span>
    </div>

    <div class="bg-white p-4 p-md-5 border border-top-0 rounded-bottom shadow-sm mb-5">
        
        <div class="mb-5">
            <h6 class="text-muted fw-bold mb-3 small text-uppercase"><span class="section-num">1.</span> CAMPAIGN INFO</h6>
            <h3 class="fw-bold mb-1 text-dark"><?php echo $task['title']; ?></h3>
            <p class="text-muted small fw-bold">Duration: <?php echo $task['start_date']; ?> <span class="mx-2">→</span> <?php echo $task['end_date']; ?></p>
        </div>

        <?php 
        $details_res = mysqli_query($conn, "SELECT * FROM schedule_details WHERE schedule_id = $id");
        $count = 2; 
        while($det = mysqli_fetch_assoc($details_res)):
            $pName = $det['platform'];
            $isTiktok = (strpos(strtolower($pName), 'tiktok') !== false);
            $cardClass = $isTiktok ? 'card-tiktok' : 'card-fb-ig';
            $headerClass = $isTiktok ? 'platform-header-tt' : 'platform-header-fb';
            
            // Convert placement string to array
            $placements = !empty($det['placements']) ? explode(', ', $det['placements']) : [];
        ?>
        
        <div class="platform-card <?php echo $cardClass; ?> mb-5 shadow-sm">
            <div class="<?php echo $headerClass; ?> text-uppercase small" style="letter-spacing: 1px; font-size: 11px;">
                <?php echo ($isTiktok) ? 'TikTok Specific Details' : 'Social Media Details ('.$pName.')'; ?>
            </div>
            <div class="p-4 border-start border-end border-bottom rounded-bottom">
                <h6 class="text-muted fw-bold mb-4 small text-uppercase"><span class="section-num"><?php echo $count++; ?>.</span> <?php echo strtoupper($pName); ?></h6>
                
                <div class="row mb-4">
                    <div class="col-md-4 border-end">
                        <small class="text-muted d-block small fw-bold mb-1">FORMAT</small>
                        <span class="fw-bold text-dark"><?php echo $det['format']; ?></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block small fw-bold mb-1">AD QUANTITY</small>
                        <span class="fw-bold text-dark"><?php echo $det['ad_quantity']; ?> Ads Required</span>
                    </div>
                </div>

                <div class="mb-4">
                    <small class="text-muted fw-bold d-block mb-3 small text-uppercase">Required Ad Placements:</small>
                    <div class="d-flex flex-wrap gap-1">
                        <?php if(!empty($placements)): ?>
                            <?php foreach($placements as $place): 
                                $bClass = 'badge-end'; // Default
                                if($place == 'Pre-roll') $bClass = 'badge-pre';
                                if($place == 'Mid-roll') $bClass = 'badge-mid';
                            ?>
                                <span class="placement-badge <?php echo $bClass; ?>">✓ <?php echo $place; ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted small italic">No placements specified.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <small class="text-muted fw-bold d-block mb-3 small text-uppercase">Ad Files to use for these slots:</small>
                    <?php 
                    // Fetch files specific to this platform OR global sync files
                    $assets_res = mysqli_query($conn, "SELECT * FROM assets WHERE schedule_id = $id AND (platform = '$pName' OR platform = 'global')");
                    
                    if(mysqli_num_rows($assets_res) > 0){
                        while($asset = mysqli_fetch_assoc($assets_res)){
                            echo '<div class="asset-row">
                                    <span class="small fw-bold text-dark"><i class="bi bi-file-earmark-play-fill me-2 text-primary"></i> '.$asset['file_name'].'</span>
                                    <a href="../uploads/'.$asset['file_name'].'" download class="text-primary fw-bold small text-decoration-none border-bottom border-primary border-2">DOWNLOAD</a>
                                  </div>';
                        }
                    } else {
                        echo "<div class='p-3 bg-light rounded text-center text-muted small italic border'>No files uploaded for this section.</div>";
                    }
                    ?>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted fw-bold d-block mb-2 text-uppercase" style="font-size: 10px;">Editor Instructions:</small>
                    <p class="text-dark small mb-0"><?php echo !empty($det['instructions']) ? nl2br($det['instructions']) : "No special instructions provided."; ?></p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>

        <div class="mt-5 pt-4">
            <?php if($task['status'] == 'Pending'): ?>
                <form action="" method="POST">
                    <button type="submit" name="acknowledge" class="btn-ack shadow-lg">
                        I Acknowledge the shedule
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-info border-0 text-center fw-bold rounded-3">
                    🚀 This task is currently: <?php echo strtoupper($task['status']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-5 pt-3 border-top">
            <p class="text-muted small fw-bold mb-2 text-uppercase" style="font-size: 10px;">Problem with Schedule? Send feedback:</p>
            <form action="" method="POST" class="d-flex">
                <input type="text" name="feedback" class="form-control feedback-input shadow-none" placeholder="Wrong files? Unclear duration? Enter your issue here...">
                <button type="submit" class="btn-feedback shadow">SEND</button>
            </form>
        </div>

    </div>
</div>

<?php include_once('../includes/footer.php'); ?>