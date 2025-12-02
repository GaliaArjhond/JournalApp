<?php
// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "admin", "journalapp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form submitted
if (isset($_POST['register'])) {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $createdAt = date("Y-m-d H:i:s");

    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists.";
        } else {

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address, createdAt) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $full_name, $email, $hashedPassword, $phone, $address, $createdAt);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <main>
        <div class="register-container">
            <h1>Create Account</h1>
            <div class="welcome-container">
                <p class="welcome">Please enter your credentials to log in.</p>
            </div>


        </div>
    </main>
</body>

</html>