<?php
// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "admin", "journalapp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$step = 1; // Step 1: Enter email, Step 2: Reset password

// Step 1: User enters email
if (isset($_POST['find_account'])) {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $message = '<div class="error-message">Please enter your email address.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="error-message">Invalid email format.</div>';
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $step = 2;
            // Store email in session for next step
            session_start();
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_user_id'] = $user['id'];
        } else {
            $message = '<div class="error-message">No account found with this email address.</div>';
        }
        $stmt->close();
    }
}

// Step 2: User resets password
if (isset($_POST['reset_password'])) {
    session_start();

    if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_user_id'])) {
        header("Location: forgotPass.php");
        exit();
    }

    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_id = $_SESSION['reset_user_id'];

    if (empty($new_password) || empty($confirm_password)) {
        $message = '<div class="error-message">Please fill all fields.</div>';
        $step = 2;
    } elseif (strlen($new_password) < 6) {
        $message = '<div class="error-message">Password must be at least 6 characters long.</div>';
        $step = 2;
    } elseif ($new_password !== $confirm_password) {
        $message = '<div class="error-message">Passwords do not match.</div>';
        $step = 2;
    } else {
        // Hash password and update
        $hashedPassword = md5($new_password);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashedPassword, $user_id);

        if ($update->execute()) {
            $message = '<div class="success-message">Password reset successfully! <a href="../index.php">Click here to login</a></div>';
            // Clear session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_user_id']);
            $step = 0; // Hide form
        } else {
            $message = '<div class="error-message">Failed to reset password. Please try again.</div>';
            $step = 2;
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/forgotPass.css">
</head>

<body>

    <main>
        <div class="forgot-container">
            <h1>Journal</h1>
            <div class="welcome-container">
                <h2>Reset Password</h2>
                <p class="welcome">Don't worry, we'll help you recover your account</p>
            </div>

            <?php echo $message; ?>

            <!-- Step 1: Enter Email -->
            <?php if ($step === 1): ?>
                <form action="" method="post">
                    <div class="input-container">
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <button type="submit" name="find_account">Find Account</button>

                    <div class="back-to-login">
                        <a href="../index.php">Back to Login</a>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Step 2: Reset Password -->
            <?php if ($step === 2): ?>
                <form action="" method="post">
                    <p class="step-info">
                        <i class="fas fa-check-circle"></i>
                        Account found! Now set your new password.
                    </p>

                    <div class="input-container">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                        <small>Minimum 6 characters</small>
                    </div>

                    <div class="input-container">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                    </div>

                    <button type="submit" name="reset_password">Reset Password</button>

                    <div class="back-to-login">
                        <a href="../index.php">Back to Login</a>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Success State -->
            <?php if ($step === 0 && strpos($message, 'success') !== false): ?>
                <div class="success-container">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="success-text">Your password has been reset successfully!</p>
                    <a href="../index.php" class="btn-login">Login to Your Account</a>
                </div>
            <?php endif; ?>

        </div>
    </main>

</body>

</html>