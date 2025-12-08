<?php
session_start();
$conn = new mysqli("localhost", "root", "admin", "journalapp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $error = "Email and password are required!";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Compare the entered password with the stored hash
            if (md5($password) === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: includes/dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "Invalid email or password!";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="assets/css/login.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log in</title>
</head>

<body>
    <main>
        <div class="login-container">
            <h1>Journal</h1>
            <div class="welcome-container">
                <h2>Welcome Back!</h2>
                <p class="welcome">Please enter your credentials to log in.</p>
            </div>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="index.php" method="post">
                <div class="input-container">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required />
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="options-container">
                    <div class="remember-me">
                        <input type="checkbox" id="remember-me" name="remember-me" />
                        <label for="remember-me">Remember me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="includes/forgotPass.php">Forgot Password?</a>
                    </div>
                    <div class="register-text">
                        Don't have an account? <a href="includes/register.php">Register</a>
                    </div>

                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </main>
</body>

</html>