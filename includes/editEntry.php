<?php
session_start();

// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "admin", "journalapp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$entry_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate entry ID
if ($entry_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

// Fetch the entry
$stmt = $conn->prepare("SELECT * FROM journal_entries WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $entry_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$entry = $result->fetch_assoc();
$stmt->close();

// Check if entry exists
if (!$entry) {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
$update_message = '';
if (isset($_POST['update_entry'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Validation
    if (empty($title) || empty($content)) {
        $update_message = '<div class="error-message">Please fill all required fields.</div>';
    } else {
        // Update the entry
        $update = $conn->prepare("UPDATE journal_entries SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("ssii", $title, $content, $entry_id, $user_id);

        if ($update->execute()) {
            $update_message = '<div class="success-message">Entry updated successfully!</div>';
            // Refresh entry data
            $stmt = $conn->prepare("SELECT * FROM journal_entries WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $entry_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $entry = $result->fetch_assoc();
            $stmt->close();
        } else {
            $update_message = '<div class="error-message">Failed to update entry. Please try again.</div>';
        }
        $update->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Entry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/editEntry.css">
</head>

<body>

    <div class="edit-entry-container">
        <div class="entry-header">
            <div class="header-content">
                <a href="dashboard.php" class="back-btn" title="Back to Dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1>Edit Entry</h1>
            </div>
        </div>

        <?php echo $update_message; ?>

        <form action="" method="post" class="entry-form">
            <div class="form-group">
                <label for="title">Entry Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($entry['title']); ?>" placeholder="Entry title..." required>
            </div>

            <div class="form-group">
                <label for="content">Entry Content:</label>
                <textarea id="content" name="content" placeholder="Write your journal entry here..." required><?php echo htmlspecialchars($entry['content']); ?></textarea>
            </div>

            <div class="entry-meta">
                <p class="created-date">
                    <i class="fas fa-calendar"></i>
                    Created: <?php echo date('F d, Y - h:i A', strtotime($entry['createdAt'])); ?>
                </p>
            </div>

            <div class="form-actions">
                <button type="submit" name="update_entry" class="btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="dashboard.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

</body>

</html>