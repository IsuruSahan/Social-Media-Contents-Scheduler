<?php 
// Robust pathing: find config/db.php regardless of which subfolder calls this header
include_once(dirname(__DIR__) . '/config/db.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swarnavahini | Digital Scheduler</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .navbar { background-color: #1E293B !important; }
        .navbar-brand { font-weight: 800; letter-spacing: 1px; }
        .nav-link { font-weight: 500; font-size: 0.9rem; }
        /* Style for the badges used in users.php */
        .badge-role { padding: 5px 12px; border-radius: 50px; font-size: 11px; font-weight: 700; }
        .badge-editor { background-color: #F3E8FF; color: #6B21A8; }
        .badge-scheduler { background-color: #DBEAFE; color: #1E40AF; }
        .card-header-dark { background-color: #1E293B; color: white; border-radius: 12px 12px 0 0; }
        .section-title { color: #64748B; font-size: 13px; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand text-white" href="<?php echo BASE_URL; ?>">SWARNAVAHINI</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>admin/users.php">Staff Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>scheduler/create.php">New Dispatch</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo BASE_URL; ?>editor/dashboard.php">Editor Tasks</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">