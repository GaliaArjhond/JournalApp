<?php
// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "admin", "journalapp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form submitted
if (isset($_POST['register'])) {
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Journal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="journal-container">
        <div class="journal-header">My Journal</div>

        <div class="entries">
        </div>

        <div class="add-entry">
            <button type="button">+</button>
        </div>

        <div class="bottom-nav">
            <div class="active"><i class="fas fa-home"></i>Home</div>
            <div><i class="fas fa-plus-square"></i>Add Entry</div>
            <div><i class="fas fa-user"></i>Profile</div>
        </div>
    </div>

</body>

</html>