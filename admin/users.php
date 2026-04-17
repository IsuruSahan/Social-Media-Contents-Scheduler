<?php 
include_once('../includes/header.php'); 

// --- LOGIC: CREATE USER ---
$message = "";
if (isset($_POST['create_user'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $team     = ($role == 'Scheduler' || $role == 'Admin') ? 'None' : $_POST['team'];

    // We use a try-catch block to prevent the "Fatal Error" crash
    try {
        $sql = "INSERT INTO users (full_name, email, phone, password, role, team) 
                VALUES ('$name', '$email', '$phone', '$password', '$role', '$team')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success border-0 shadow-sm'>Account created for $name</div>";
        }
    } catch (mysqli_sql_exception $e) {
        // Check if the error is specifically a "Duplicate Entry" (Error code 1062)
        if ($e->getCode() == 1062) {
            $message = "<div class='alert alert-warning border-0 shadow-sm'>
                            <strong>Registration Failed:</strong> The email <b>$email</b> is already registered. 
                            Please use a different email.
                        </div>";
        } else {
            // If it's some other error, show the general error
            $message = "<div class='alert alert-danger border-0 shadow-sm'>Something went wrong. Please try again.</div>";
        }
    }
}

// --- LOGIC: DELETE USER ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    echo "<script>window.location.href='users.php';</script>";
}
?>

<div class="main-card shadow-sm border-0">
    <div class="card-header-dark">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">ADMIN | STAFF MANAGEMENT</h5>
            <span class="badge bg-primary px-3">Super Admin</span>
        </div>
    </div>

    <div class="p-4 p-md-5">
        <?php echo $message; ?>

        <h6 class="section-title">1. Add New Staff Member</h6>
        <div class="p-4 bg-light rounded-4 border">
            <form action="users.php" method="POST">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Isuru Sahan" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="isuru@swarnavahini.lk" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="+94 7X XXX XXXX" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Login Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••••••" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assign Role</label>
                        <select name="role" id="roleSelect" class="form-select" onchange="checkRole()">
                            <option value="Editor">Editor</option>
                            <option value="Scheduler">Scheduler</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="teamSelection">
                        <label class="form-label">Assign Team (Editors Only)</label>
                        <select name="team" class="form-select">
                            <option value="News Team">News Team</option>
                            <option value="Content Team">Content Team</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" name="create_user" class="btn btn-primary px-5 py-2 fw-bold rounded-pill">Create Account</button>
                    </div>
                </div>
            </form>
        </div>

        <h6 class="section-title mt-5">2. Registered Staff Members</h6>
        <div class="table-responsive mt-3">
            <table class="table align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 border-0">NAME / CONTACT</th>
                        <th class="py-3 border-0">ROLE</th>
                        <th class="py-3 border-0">TEAM / DEPT</th>
                        <th class="py-3 border-0 text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                    while($row = mysqli_fetch_assoc($res)) {
                        $roleClass = ($row['role'] == 'Editor') ? 'badge-editor' : 'badge-scheduler';
                        $teamLabel = ($row['role'] == 'Scheduler' || $row['role'] == 'Admin') ? '<span class="text-muted opacity-50">—</span>' : $row['team'];
                    ?>
                    <tr>
                        <td class="py-3">
                            <div class="fw-bold mb-0"><?php echo $row['full_name']; ?></div>
                            <small class="text-muted"><?php echo $row['phone']; ?></small>
                        </td>
                        <td>
                            <span class="badge-role <?php echo $roleClass; ?>"><?php echo strtoupper($row['role']); ?></span>
                        </td>
                        <td><?php echo $teamLabel; ?></td>
                        <td class="text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-2">Edit</a>
                            <a href="users.php?delete=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                               onclick="return confirm('Delete this user account?')">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function checkRole() {
    const role = document.getElementById('roleSelect').value;
    const teamBox = document.getElementById('teamSelection');
    if(role === 'Scheduler' || role === 'Admin') {
        teamBox.style.opacity = "0.4";
        teamBox.style.pointerEvents = "none";
    } else {
        teamBox.style.opacity = "1";
        teamBox.style.pointerEvents = "auto";
    }
}
// Run on load
checkRole();
</script>

<?php include_once('../includes/footer.php'); ?>