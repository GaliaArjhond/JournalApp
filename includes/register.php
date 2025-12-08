<?php
// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "admin", "journalapp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form submitted
if (isset($_POST['register'])) {

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $createdAt = date("Y-m-d H:i:s");

    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
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

            // Hash password with MD5 to match login system
            $hashedPassword = md5($password);

            // Insert user with correct column name: full_name
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address, createdAt) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $full_name, $email, $hashedPassword, $phone, $address, $createdAt);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='../index.php'>login</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>

<body>
    <main>
        <div class="register-container">
            <h1>Journal</h1>
            <div class="welcome-container">
                <h2>Create Account</h2>
                <p class="welcome">Join us and start journaling today!</p>
            </div>

            <?php
            if (!empty($error)) echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
            if (!empty($success)) echo '<div class="success-message">' . $success . '</div>';
            ?>

            <form action="" method="post">
                <div class="input-container">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="input-container">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="input-container">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="input-container">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address">
                </div>

                <div class="input-container">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <div class="password-requirements">
                        <strong>Password requirements:</strong>
                        <ul>
                            <li>Minimum 6 characters</li>
                            <li>Use a mix of letters and numbers</li>
                        </ul>
                    </div>
                </div>

                <div class="input-container">
                    <label for="confirm">Confirm Password:</label>
                    <input type="password" id="confirm" name="confirm" required>
                </div>

                <div class="options-container">
                    <div class="terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the terms and conditions</label>
                    </div>
                </div>

                <button type="submit" name="register">Create Account</button>

                <div class="login-text">
                    Already have an account? <a href="../index.php">Login here</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>