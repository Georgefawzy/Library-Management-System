<?php
session_start();
include 'navbar.php';
require_once "functions.php";
require_once "Book.php";
$file = "data.json";

// Check if the user is logged in before allowing access to the add book page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form inputs
    if (!empty($_POST['name']) && !empty($_POST['quantity']) && is_numeric($_POST['quantity']) && intval($_POST['quantity']) > 0) {
        $bookName = htmlspecialchars($_POST['name']);
        $quantity = intval($_POST['quantity']);

        // Ensure that book name is not too short (min 3 characters for example)
        if (strlen($bookName) < 3) {
            $_SESSION['error'] = 'Book name must be at least 3 characters long!';
        } else {
            $books = readData($file);

            // Create a new book instance
            $newBook = new Book(
                uniqid(),
                $bookName,
                $quantity,
                $_SESSION['username']
            );

            // Add the new book to the books array
            $books[] = (array)$newBook;

            // Write the updated data to the JSON file
            writeData($file, $books);

            // Set session success message
            $_SESSION['success'] = 'Book added successfully!';
            header("Location: index.php");
            exit();
        }
    } else {
        // Set session error message
        $_SESSION['error'] = 'Please provide valid book name and quantity!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add New Book</h2>

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
            <label for="name">Book Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter the book's name" required>
        </div>
        <div class="mb-3">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" placeholder="Enter the quantity" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Book</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
