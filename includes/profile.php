<?php
session_start();

$conn = new mysqli("localhost", "root", "admin", "journalapp");

// Handle DB connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Use prepared statements to avoid SQL injection
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

// Handle profile update
$update_message = '';
if (isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validation
    if (empty($full_name) || empty($email)) {
        $update_message = '<div class="error-message">Full name and email are required.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_message = '<div class="error-message">Invalid email format.</div>';
    } else {
        // Check if email is unique (excluding current user)
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $update_message = '<div class="error-message">Email already in use.</div>';
        } else {
            // Update user profile
            $update = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $update->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);

            if ($update->execute()) {
                $update_message = '<div class="success-message">Profile updated successfully!</div>';
                // Refresh user data
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();
            } else {
                $update_message = '<div class="error-message">Failed to update profile. Please try again.</div>';
            }
        }
        $check->close();
    }
}

// Get user's entry count
$count_query = "SELECT COUNT(*) as total FROM journal_entries WHERE user_id = ?";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_data = $count_result->fetch_assoc();
$entry_count = $count_data['total'];
$count_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>

<body>

    <div class="profile-container">
        <div class="profile-header">
            <a href="dashboard.php" class="back-btn" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>My Profile</h1>
            <div class="header-spacer"></div>
        </div>

        <?php echo $update_message; ?>

        <div class="profile-content">
            <!-- Profile Info Card -->
            <div class="profile-card info-card">
                <div class="card-header">
                    <h2><i class="fas fa-user-circle"></i> Profile Information</h2>
                </div>
                <form action="" method="post" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name:</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn-save">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Statistics Card -->
            <div class="profile-card stats-card">
                <div class="card-header">
                    <h2><i class="fas fa-chart-bar"></i> Statistics</h2>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-info">
                            <p class="stat-label">Total Entries</p>
                            <p class="stat-value"><?php echo $entry_count; ?></p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-info">
                            <p class="stat-label">Member Since</p>
                            <p class="stat-value"><?php echo date('M Y', strtotime($user['createdAt'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Card -->
            <div class="profile-card account-card">
                <div class="card-header">
                    <h2><i class="fas fa-lock"></i> Account</h2>
                </div>
                <div class="account-actions">
                    <p class="account-text">Manage your account settings and security</p>
                    <div class="button-group">
                        <a href="logout.php" class="btn-logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom-nav">
            <a href="dashboard.php" class="nav-item" title="Home">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="newEntry.php" class="nav-item" title="Add Entry">
                <i class="fas fa-plus-square"></i>
                <span>Add Entry</span>
            </a>
            <a href="profile.php" class="nav-item active" title="Profile">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </div>
    </div>

</body>

</html>