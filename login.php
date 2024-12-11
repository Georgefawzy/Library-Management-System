<?php
session_start();
require_once "functions.php";  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users = readData("users.json");

    foreach ($users as $user) {
        if (
            $user['username'] === $_POST['username'] &&
            password_verify($_POST['password'], $user['password'])
        ) {
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        }
    }
    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Logo -->
            <div class="text-center mb-4">
                <h1>📚</h1>
                <h2>Welcome to the Library</h2>
            </div>

            <h2 class="text-center mb-4">Login</h2>

            <!-- SweetAlert2 Error Alert -->
            <?php if (isset($error)): ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: '<?= $error ?>'
                    });
                </script>
            <?php endif; ?>

            <div class="card p-4 shadow-sm">
                <form method="post" id="loginForm">
                    <div class="form-group mb-3">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                </form>

                <!-- Register Link -->
                <div class="text-center">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.all.min.js"></script>

<script>
// Client-side validation with SweetAlert
document.getElementById("loginForm").addEventListener("submit", function(event) {
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!username || !password) {
        event.preventDefault(); // Prevent form submission
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            text: 'Both fields are required!',
        });
    }
});
</script>

</body>
</html>
