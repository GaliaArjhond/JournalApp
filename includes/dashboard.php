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

// Fetch user information
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

// Fetch journal entries for the logged-in user
$entries_query = "SELECT * FROM journal_entries WHERE user_id = '$user_id' ORDER BY createdAt DESC";
$entries_result = $conn->query($entries_query);
$entries = [];

if ($entries_result->num_rows > 0) {
    while ($row = $entries_result->fetch_assoc()) {
        $entries[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Journal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

    <div class="journal-container">
        <div class="journal-header">
            <h1>My Journal</h1>
            <p class="greeting">Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
        </div>

        <div class="entries">
            <?php if (count($entries) > 0): ?>
                <?php foreach ($entries as $entry): ?>
                    <div class="entry-card">
                        <div class="entry-header">
                            <h3><?php echo htmlspecialchars($entry['title']); ?></h3>
                            <span class="entry-date"><?php echo date('M d, Y', strtotime($entry['createdAt'])); ?></span>
                        </div>
                        <div class="entry-content">
                            <?php echo htmlspecialchars(substr($entry['content'], 0, 150)) . (strlen($entry['content']) > 150 ? '...' : ''); ?>
                        </div>
                        <div class="entry-actions">
                            <a href="editEntry.php?id=<?php echo $entry['id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="deleteEntry.php?id=<?php echo $entry['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-entries">
                    <i class="fas fa-book"></i>
                    <p>No journal entries yet. Create your first entry!</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="add-entry">
            <a href="newEntry.php" class="btn-add-entry">
                <i class="fas fa-plus"></i> New Entry
            </a>
        </div>

        <div class="bottom-nav">
            <a href="dashboard.php" class="nav-item active" title="Home">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="newEntry.php" class="nav-item" title="Add Entry">
                <i class="fas fa-plus-square"></i>
                <span>Add Entry</span>
            </a>
            <a href="profile.php" class="nav-item" title="Profile">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </div>
    </div>

</body>

</html>