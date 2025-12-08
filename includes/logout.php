<?php
session_start();

// Confirmation check
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Destroy the session
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// If no confirmation, show the confirmation page
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/logout.css">
</head>

<body>

    <div class="logout-container">
        <div class="logout-card">
            <div class="logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>

            <h1>Logout Confirmation</h1>
            <p class="logout-message">Are you sure you want to logout?</p>

            <div class="logout-actions">
                <a href="logout.php?confirm=yes" class="btn-logout">
                    <i class="fas fa-check"></i> Yes, Logout
                </a>
                <a href="dashboard.php" class="btn-cancel">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

</body>

</html>