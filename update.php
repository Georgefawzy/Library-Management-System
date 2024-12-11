<?php
session_start();
include 'navbar.php';
require_once "functions.php";
$file = "data.json";

// Check if the user is logged in before allowing access to the update page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$books = readData($file);
$id = $_GET['id'];
$book = null;

foreach ($books as $key => $b) {
    if ($b['id'] == $id) {
        $book = &$books[$key];
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form inputs
    if (!empty($_POST['name']) && !empty($_POST['quantity']) && is_numeric($_POST['quantity']) && intval($_POST['quantity']) > 0) {
        // Update the book details
        $book['name'] = $_POST['name'];
        $book['quantity'] = intval($_POST['quantity']);
        $book['updatedBy'] = $_SESSION['username']; // Store who updated the book
        $book['updatedAt'] = date("Y-m-d H:i:s");   // Store when it was updated

        // Use the writeData function to update the file
        writeData($file, $books);  // Corrected here

        // Set session success message
        $_SESSION['success'] = 'Book updated successfully!';
        header("Location: index.php");
        exit();
    } else {
        // Set session error message
        $_SESSION['error'] = 'Please provide valid book name and quantity!';
    }
}

if (!$book) {
    // If the book is not found, redirect to the index page
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Update Book</h2>

    <!-- Display Success or Error Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>Book Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($book['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($book['quantity']) ?>" min="1" required>
        </div>
        <button type="submit" class="btn btn-warning">Update Book</button>
    </form>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
