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

// If form submitted
if (isset($_POST['save_entry'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $createdAt = date("Y-m-d H:i:s");

    // Validation
    if (empty($title) || empty($content)) {
        $error = "Please fill all required fields.";
    } else {
        // Insert new journal entry
        $stmt = $conn->prepare("INSERT INTO journal_entries (user_id, title, content, createdAt) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $content, $createdAt);

        if ($stmt->execute()) {
            $success = "Entry created successfully!";
            // Redirect to dashboard after 2 seconds
            header("refresh:2;url=dashboard.php");
        } else {
            $error = "Failed to create entry. Please try again.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Entry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/newEntry.css">
</head>

<body>

    <div class="new-entry-container">
        <div class="entry-header">
            <div class="header-content">
                <a href="dashboard.php" class="back-btn" title="Back to Dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1>Create New Entry</h1>
            </div>
        </div>

        <?php
        if (!empty($error)) echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
        if (!empty($success)) echo '<div class="success-message">' . htmlspecialchars($success) . '</div>';
        ?>

        <form action="" method="post" class="entry-form">
            <div class="form-group">
                <label for="title">Entry Title:</label>
                <input type="text" id="title" name="title" placeholder="What's on your mind today?" required>
            </div>

            <div class="form-group">
                <label for="content">Entry Content:</label>
                <textarea id="content" name="content" placeholder="Write your journal entry here..." required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" name="save_entry" class="btn-save">
                    <i class="fas fa-save"></i> Save Entry
                </button>
                <a href="dashboard.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

</body>

</html>