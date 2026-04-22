<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 1);

include_once('../includes/header.php'); 

$status_msg = "";

// --- LOGIC: DISPATCH SCHEDULE ---
if (isset($_POST['dispatch_schedule'])) {
    $title      = mysqli_real_escape_string($conn, $_POST['campaign_title']);
    $duration   = mysqli_real_escape_string($conn, $_POST['schedule_duration']);
    $mode       = isset($_POST['custom_mode']) ? 'custom' : 'sync';
    $platforms  = $_POST['platforms'] ?? [];

    // Date Splitting
    $date_parts = explode(" to ", $duration);
    $start_date = $date_parts[0];
    $end_date   = (isset($date_parts[1])) ? $date_parts[1] : $date_parts[0];

    $sql_main = "INSERT INTO schedules (title, start_date, end_date, mode, status) 
                 VALUES ('$title', '$start_date', '$end_date', '$mode', 'Pending')";
    
    if (mysqli_query($conn, $sql_main)) {
        $schedule_id = mysqli_insert_id($conn);

        if ($mode == 'sync') {
            $format     = $_POST['sync_format'];
            $qty        = $_POST['sync_qty'];
            $inst       = mysqli_real_escape_string($conn, $_POST['sync_instructions']);
            $places     = isset($_POST['sync_placements']) ? implode(", ", $_POST['sync_placements']) : "";

            foreach ($platforms as $p) {
                mysqli_query($conn, "INSERT INTO schedule_details (schedule_id, platform, format, ad_quantity, placements, instructions) 
                                     VALUES ($schedule_id, '$p', '$format', '$qty', '$places', '$inst')");
            }
            
            if (!empty($_FILES['sync_ads']['name'][0])) {
                foreach ($_FILES['sync_ads']['name'] as $k => $v) {
                    $fn = time() . "_sync_" . basename($v);
                    move_uploaded_file($_FILES['sync_ads']['tmp_name'][$k], "../uploads/" . $fn);
                    mysqli_query($conn, "INSERT INTO assets (schedule_id, platform, file_name) VALUES ($schedule_id, 'global', '$fn')");
                }
            }
        } else {
            // CUSTOM MODE
            foreach ($platforms as $p) {
                $p_key    = str_replace(' ', '_', $p); 
                $format   = $_POST["format_$p_key"] ?? 'Reels (9:16)';
                $qty      = $_POST["qty_$p_key"] ?? 1;
                $inst     = mysqli_real_escape_string($conn, $_POST["inst_$p_key"] ?? '');
                $places   = isset($_POST["placements_$p_key"]) ? implode(", ", $_POST["placements_$p_key"]) : "";
                
                mysqli_query($conn, "INSERT INTO schedule_details (schedule_id, platform, format, ad_quantity, placements, instructions) 
                                     VALUES ($schedule_id, '$p', '$format', '$qty', '$places', '$inst')");

                if (!empty($_FILES["ads_$p_key"]['name'][0])) {
                    foreach ($_FILES["ads_$p_key"]['name'] as $k => $v) {
                        $fn = time() . "_".$p_key."_" . basename($v);
                        move_uploaded_file($_FILES["ads_$p_key"]['tmp_name'][$k], "../uploads/" . $fn);
                        mysqli_query($conn, "INSERT INTO assets (schedule_id, platform, file_name) VALUES ($schedule_id, '$p', '$fn')");
                    }
                }
            }
        }
        $status_msg = "<div class='alert alert-success border-0 shadow-sm'>🚀 Schedule Dispatched Successfully!</div>";
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="main-card shadow-sm border-0 mb-5">
    <div class="card-header-dark p-4">
        <h5 class="mb-0 fw-bold">CAMPAIGN SCHEDULER</h5>
    </div>

    <form action="create.php" method="POST" enctype="multipart/form-data" class="p-4 p-md-5 bg-white rounded-bottom border border-top-0">
        <?php echo $status_msg; ?>

        <h6 class="section-title text-uppercase small fw-bold text-muted mb-4">1. Global Campaign Details</h6>
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Campaign Name</label>
                <input type="text" name="campaign_title" class="form-control" placeholder="Enter title..." required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Schedule Duration</label>
                <input type="text" name="schedule_duration" id="schedule_duration" class="form-control" placeholder="Select dates..." required>
            </div>
        </div>

        <h6 class="section-title text-uppercase small fw-bold text-muted mb-4">2. Target Platforms & Mode</h6>
        <div class="mb-4">
            <div class="d-flex gap-2 mb-3">
                <?php $plats = ['Facebook', 'TikTok', 'Youtube', 'Instagram']; 
                foreach($plats as $pt): ?>
                    <input type="checkbox" class="btn-check platform-check" name="platforms[]" value="<?php echo $pt; ?>" id="btn-<?php echo $pt; ?>">
                    <label class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-bold" for="btn-<?php echo $pt; ?>">✓ <?php echo $pt; ?></label>
                <?php endforeach; ?>
            </div>
            
            <div class="form-check form-switch p-3 bg-light rounded-3 border d-inline-block w-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" name="custom_mode" id="modeSwitch">
                <label class="form-check-label fw-bold" for="modeSwitch">Custom Mode (Individual Settings)</label>
            </div>
        </div>

        <div id="sync-container">
            <div class="card mb-4 border-0 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4">Content Settings (Sync)</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Format</label>
                            <select name="sync_format" class="form-select"><option>Reels (9:16)</option><option>Standard (16:9)</option></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ad quantity</label>
                            <input type="number" name="sync_qty" class="form-control" value="1">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label small fw-bold d-block mb-2">Ad Placements</label>
                            <div class="btn-group w-100 shadow-sm rounded-3 overflow-hidden" role="group">
                                <input type="checkbox" class="btn-check" name="sync_placements[]" value="Pre-roll" id="sync-pre" checked>
                                <label class="btn btn-outline-secondary border py-2 small fw-bold" for="sync-pre">Pre-roll</label>

                                <input type="checkbox" class="btn-check" name="sync_placements[]" value="Mid-roll" id="sync-mid">
                                <label class="btn btn-outline-secondary border py-2 small fw-bold" for="sync-mid">Mid-roll</label>

                                <input type="checkbox" class="btn-check" name="sync_placements[]" value="End-roll" id="sync-end">
                                <label class="btn btn-outline-secondary border py-2 small fw-bold" for="sync-end">End-roll</label>
                            </div>
                        </div>

                        <div class="col-12"><label class="form-label small fw-bold">Ads Pool [+]</label><input type="file" name="sync_ads[]" class="form-control" multiple></div>
                        <div class="col-12"><textarea name="sync_instructions" class="form-control" placeholder="Instructions..."></textarea></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="custom-container" class="d-none">
            <div id="custom-boxes"></div>
        </div>

        <button type="submit" name="dispatch_schedule" class="btn btn-primary w-100 py-3 mt-4 rounded-3 shadow fw-bold h5">
            DISPATCH SCHEDULE
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#schedule_duration", { mode: "range", dateFormat: "Y-m-d", minDate: "today" });

const modeSwitch = document.getElementById('modeSwitch');
const platformChecks = document.querySelectorAll('.platform-check');
const customBoxes = document.getElementById('custom-boxes');

function updateCustomUI() {
    customBoxes.innerHTML = '';
    platformChecks.forEach(check => {
        if(check.checked) {
            const p = check.value;
            const pKey = p.replace(' ', '_');
            customBoxes.innerHTML += `
                <div class="card mb-3 border-0 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-primary">${p} Specific</h6>
                        <div class="row g-2">
                            <div class="col-md-6"><label class="small fw-bold">Format</label>
                                <select name="format_${pKey}" class="form-select form-select-sm"><option>Reels (9:16)</option><option>Standard (16:9)</option></select>
                            </div>
                            <div class="col-md-6"><label class="small fw-bold">Qty</label>
                                <input type="number" name="qty_${pKey}" class="form-control form-select-sm" value="1">
                            </div>
                            <div class="col-12">
                                <div class="btn-group w-100 shadow-sm" role="group">
                                    <input type="checkbox" class="btn-check" name="placements_${pKey}[]" value="Pre-roll" id="pre_${pKey}" checked>
                                    <label class="btn btn-outline-secondary border py-1 small fw-bold" for="pre_${pKey}">Pre</label>
                                    <input type="checkbox" class="btn-check" name="placements_${pKey}[]" value="Mid-roll" id="mid_${pKey}">
                                    <label class="btn btn-outline-secondary border py-1 small fw-bold" for="mid_${pKey}">Mid</label>
                                    <input type="checkbox" class="btn-check" name="placements_${pKey}[]" value="End-roll" id="end_${pKey}">
                                    <label class="btn btn-outline-secondary border py-1 small fw-bold" for="end_${pKey}">End</label>
                                </div>
                            </div>
                            <div class="col-12"><input type="file" name="ads_${pKey}[]" class="form-control form-select-sm" multiple></div>
                            <div class="col-12"><textarea name="inst_${pKey}" class="form-control form-select-sm" placeholder="Instructions for ${p}"></textarea></div>
                        </div>
                    </div>
                </div>`;
        }
    });
}

modeSwitch.addEventListener('change', function() {
    document.getElementById('sync-container').classList.toggle('d-none', this.checked);
    document.getElementById('custom-container').classList.toggle('d-none', !this.checked);
    if(this.checked) updateCustomUI();
});

platformChecks.forEach(check => check.addEventListener('change', () => { if(modeSwitch.checked) updateCustomUI(); }));
</script>

<?php include_once('../includes/footer.php'); ?>