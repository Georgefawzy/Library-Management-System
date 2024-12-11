<?php
require_once "User.php"; // Include the User class
require_once "functions.php"; // Assuming this file contains the necessary data read/write functions
$file = "users.json";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users = readData($file); // Function to read existing users
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Check if username already exists
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            $error = "Username already exists!";
            break;
        }
    }

    // Check if email already exists
    if (!isset($error) && User::isEmailRegistered($email, $users)) {
        $error = "Email is already registered!";
    }

    // Register user if no error
    if (!isset($error)) {
        $newUser = new User($username, $password, $email);
        $users[] = (array)$newUser; // Convert object to array to store in the users list
        writeData($file, $users); // Function to save the updated users list

        // Show success message and redirect to login page
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful',
                    text: 'You can now log in.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'login.php';
                });
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Register</h2>
    
    <?php if (isset($error)): ?>
        <!-- Show error with SweetAlert if any validation fails -->
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?= $error ?>',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
    
    <form method="post">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Register</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.all.min.js"></script>
</body>
</html>
