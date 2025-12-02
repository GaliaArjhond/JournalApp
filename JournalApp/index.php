<?php
session_start();
$conn = new mysqli("localhost", "root", "admin", "journalapp");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        header("Location: profile.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
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
            <form action="index.php" method="post">
                <div class="input-container">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required />
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="options-container">
                    <div class="remember-me">
                        <input type="checkbox" id="remember-me" name="remember-me" />
                        <label for="remember-me">Remember me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="forgot_pass.html">Forgot Password?</a>
                    </div>
                    <div class="register-text">
                        Don't have an account?<a href="includes/register.php">Register</a>
                    </div>

                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </main>
</body>

</html>