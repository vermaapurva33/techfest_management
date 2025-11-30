<?php 
require 'includes/db.php';

// HANDLE REGISTRATION
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    // Check for duplicate email
    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if($check->get_result()->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $pass, $role, $phone);
        if ($stmt->execute()) {
            echo "<script>alert('Registration Successful! Please Login.');</script>";
        }
    }
}

// HANDLE LOGIN
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            header("Location: dashboard_" . $row['role'] . ".php");
            exit();
        } else {
            echo "<script>alert('Invalid Password');</script>";
        }
    } else {
        echo "<script>alert('User not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - TechFest</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="justify-content: center; align-items: center;">
    
    <div class="card" style="width: 400px; padding: 30px;">
        <h2 style="text-align: center; color: var(--primary); margin-bottom: 20px;">TechFest Portal</h2>

        <form method="POST" id="loginForm">
            <h3 style="margin-bottom: 15px;">Login</h3>
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            <input type="password" name="pass" class="form-control" placeholder="Password" required>
            <button type="submit" name="login" class="btn" style="width: 100%;">Login</button>
            <p style="margin-top: 15px; text-align: center; font-size: 0.9rem;">
                New User? <a href="#" onclick="toggleAuth()" style="color: var(--primary);">Register Here</a>
            </p>
        </form>

        <form method="POST" id="regForm" style="display: none;">
            <h3 style="margin-bottom: 15px;">Register</h3>
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
            <input type="password" name="pass" class="form-control" placeholder="Password" required>
            <select name="role" class="form-control">
                <option value="participant">Participant</option>
                <option value="organizer">Organizer</option>
                <option value="judge">Judge</option>
            </select>
            <button type="submit" name="register" class="btn" style="width: 100%;">Sign Up</button>
            <p style="margin-top: 15px; text-align: center; font-size: 0.9rem;">
                Already have an account? <a href="#" onclick="toggleAuth()" style="color: var(--primary);">Login Here</a>
            </p>
        </form>
    </div>

    <script>
        function toggleAuth() {
            var login = document.getElementById('loginForm');
            var reg = document.getElementById('regForm');
            if (login.style.display === 'none') {
                login.style.display = 'block';
                reg.style.display = 'none';
            } else {
                login.style.display = 'none';
                reg.style.display = 'block';
            }
        }
    </script>
</body>
</html>