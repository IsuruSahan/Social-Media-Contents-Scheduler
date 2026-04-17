<?php include_once(__DIR__ . '/../config/db.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swarnavahini | Digital Content Scheduler</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark card-header-dark py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>">SWARNAVAHINI</a>
        <div class="navbar-nav ms-auto">
            <span class="nav-link text-secondary">Logged in as: Admin</span>
        </div>
    </div>
</nav>

<div class="container mt-4">