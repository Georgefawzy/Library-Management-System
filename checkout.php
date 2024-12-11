<?php
session_start();
require_once "Book.php";
require_once "functions.php";

$file = "data.json";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $bookId = filter_input(INPUT_POST, 'book_id', FILTER_SANITIZE_STRING);
    $borrowedTo = filter_input(INPUT_POST, 'borrowed_to', FILTER_SANITIZE_STRING);
    $returnDate = filter_input(INPUT_POST, 'return_date', FILTER_SANITIZE_STRING);
    $numBooks = filter_input(INPUT_POST, 'num_books', FILTER_VALIDATE_INT);

    // Validate the number of books
    if ($numBooks <= 0 || !$numBooks) {
        $_SESSION['error'] = "Please enter a valid number of books to borrow.";
        header("Location: index.php");
        exit();
    }

    // Validate required fields (borrowedTo, returnDate)
    if (empty($borrowedTo) || empty($returnDate)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: index.php");
        exit();
    }

    // Checkout the book(s)
    try {
        if (Book::checkout($file, $bookId, $borrowedTo, $returnDate, $numBooks)) {
            $_SESSION['success'] = "Book(s) checked out successfully!";
        } else {
            $_SESSION['error'] = "Failed to check out the book(s). It may be out of stock or not enough copies available.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }

    header("Location: index.php");
    exit();
}
?>
