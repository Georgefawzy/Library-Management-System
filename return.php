<?php
session_start();
require_once "Book.php";

// Define the data file
$file = "data.json";

// Handle form submission for returning a book
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookId = $_POST['book_id'];
    $borrowedTo = $_POST['borrowed_to'];

    // Return the book
    if (Book::returnBook($file, $bookId, $borrowedTo)) {
        $_SESSION['success'] = "Book returned successfully!";
    } else {
        $_SESSION['error'] = "Failed to return the book. It may not have been borrowed by this person.";
    }

    header("Location: index.php");
    exit();
}
?>
