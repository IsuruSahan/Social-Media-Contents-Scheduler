<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 1);
include_once('../includes/header.php'); 

// --- LOGIC: SAVE THE SCHEDULE ---
if (isset($_POST['dispatch_schedule'])) {
    $title      = mysqli_real_escape_string($conn, $_POST['title']);
    $category   = mysqli_real_escape_string($conn, $_POST['category']);
    $date       = $_POST['schedule_date'];
    $mode       = $_POST['sync_mode']; // 'sync' or 'custom'
    $platforms  = isset($_POST['platforms']) ? $_POST['platforms'] : [];

    // 1. Insert into main 'schedules' table
    $sql_main = "INSERT INTO schedules (title, start_date, mode, status) VALUES ('$title', '$date', '$mode', 'Pending')";
    mysqli_query($conn, $sql_main);
    $schedule_id = mysqli_insert_id($conn);

    // 2. Handle Content & Ad Logic
    if ($mode == 'sync') {
        // SYNC MODE: One set of details for all platforms
        $format = $_POST['sync_format'];
        $inst   = mysqli_real_escape_string($conn, $_POST['sync_instructions']);
        
        foreach ($platforms as $p) {
            mysqli_query($conn, "INSERT INTO schedule_details (schedule_id, platform, format, instructions) 
                                 VALUES ($schedule_id, '$p', '$format', '$inst')");
        }

        // Handle File Uploads for Sync Group
        if (!empty($_FILES['sync_ads']['name'][0])) {
            foreach ($_FILES['sync_ads']['name'] as $key => $val) {
                $file_name = time() . "_" . $val;
                move_uploaded_file($_FILES['sync_ads']['tmp_name'][$key], "../uploads/" . $file_name);
                mysqli_query($conn, "INSERT INTO assets (schedule_id, platform, file_name) VALUES ($schedule_id, 'global', '$file_name')");
            }
        }
    } else {
        // CUSTOM MODE: Different details for each platform
        foreach ($platforms as $p) {
            $format = $_POST["format_$p"];
            $inst   = mysqli_real_escape_string($conn, $_POST["inst_$p"]);
            mysqli_query($conn, "INSERT INTO schedule_details (schedule_id, platform, format, instructions) 
                                 VALUES ($schedule_id, '$p', '$format', '$inst')");
            
            // Handle File Uploads per platform
            if (!empty($_FILES["ads_$p"]['name'][0])) {
                foreach ($_FILES["ads_$p"]['name'] as $key => $val) {
                    $file_name = time() . "_" . $p . "_" . $val;
                    move_uploaded_file($_FILES["ads_$p"]['tmp_name'][$key], "../uploads/" . $file_name);
                    mysqli_query($conn, "INSERT INTO assets (schedule_id, platform, file_name) VALUES ($schedule_id, '$p', '$file_name')");
                }
            }
        }
    }
    echo "<script>alert('Schedule Dispatched Successfully!'); window.location.href='create.php';</script>";
}
?>

<div class="main-card shadow-sm border-0">
    <div class="card-header-dark">
        <h5 class="mb-0 fw-bold">SWARNAVAHINI | CONTENT SCHEDULER</h5>
    </div>

    <form action="create.php" method="POST" enctype="multipart/form-data" class="p-4 p-md-5">
        
        <h6 class="section-title">1. Global Campaign Details</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <input type="text" name="title" class="form-control py-2" placeholder="Campaign Title (e.g. Teledrama Ep 45)" required>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select py-2">
                    <option value="Drama">Drama</option>
                    <option value="News">News</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="schedule_date" class="form-control py-2" required>
            </div>
        </div>

        <h6 class="section-title">2. Target Platforms & Logic</h6>
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="platforms[]" value="Facebook" id="pltFB">
                <label class="form-check-label fw-bold" for="pltFB">Facebook</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="platforms[]" value="TikTok" id="pltTT">
                <label class="form-check-label fw-bold" for="pltTT">TikTok</label>
            </div>
        </div>

        <div class="alert alert-info d-flex justify-content-between align-items-center py-2 rounded-3 border-0">
            <span class="fw-bold"><i class="bi bi-arrow-repeat"></i> Sync Mode Active</span>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="sync_mode" value="custom" id="modeToggle">
                <label class="form-check-label small" for="modeToggle">Switch to Custom Mode</label>
            </div>
        </div>

        <div id="sync-view">
            <h6 class="section-title">3. Synced Content (Main Version)</h6>
            <div class="content-card card-sync p-4 border">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Format</label>
                        <select name="sync_format" class="form-select">
                            <option>Reel (9:16)</option>
                            <option>Video (16:9)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload Ads Pool [+]</label>
                        <input type="file" name="sync_ads[]" class="form-control" multiple>
                    </div>
                    <div class="col-12">
                        <textarea name="sync_instructions" class="form-control" placeholder="Instructions for editors..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div id="custom-view" class="d-none">
            <h6 class="section-title">3. Custom Content per Platform</h6>
            <div class="content-card card-fb p-4 border mb-3">
                <p class="fw-bold text-primary mb-2">FACEBOOK VERSION</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <select name="format_Facebook" class="form-select"><option>Reel (9:16)</option></select>
                    </div>
                    <div class="col-md-6">
                        <input type="file" name="ads_Facebook[]" class="form-control" multiple>
                    </div>
                    <div class="col-12">
                        <textarea name="inst_Facebook" class="form-control" placeholder="FB Instructions..."></textarea>
                    </div>
                </div>
            </div>
            <div class="content-card card-tiktok p-4 border">
                <p class="fw-bold text-warning mb-2">TIKTOK VERSION</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <select name="format_TikTok" class="form-select"><option>TikTok Video</option></select>
                    </div>
                    <div class="col-md-6">
                        <input type="file" name="ads_TikTok[]" class="form-control" multiple>
                    </div>
                    <div class="col-12">
                        <textarea name="inst_TikTok" class="form-control" placeholder="TikTok Instructions..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" name="dispatch_schedule" class="btn btn-dispatch w-100 mt-4">DISPATCH SCHEDULE</button>
    </form>
</div>

<script>
document.getElementById('modeToggle').addEventListener('change', function() {
    const syncView = document.getElementById('sync-view');
    const customView = document.getElementById('custom-view');
    if(this.checked) {
        syncView.classList.add('d-none');
        customView.classList.remove('d-none');
    } else {
        syncView.classList.remove('d-none');
        customView.classList.add('d-none');
    }
});
</script>

<?php include_once('../includes/footer.php'); ?>