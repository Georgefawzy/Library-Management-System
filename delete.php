<?php
require_once "functions.php";
$file = "data.json";

// Check if the file exists and is readable
if (!file_exists($file) || !is_readable($file)) {
    die("Error: Unable to read data file.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $books = readData($file);

    // Iterate through the books to find the book by id
    $bookFound = false;
    foreach ($books as $key => $book) {
        if ($book['id'] == $id) {
            unset($books[$key]);  // Remove the book from the array
            writeData($file, array_values($books));  // Re-index the array and write back to the file
            $bookFound = true;
            break;
        }
    }

    if (!$bookFound) {
        die("Error: Book not found.");
    }
}

// Redirect to the index page after deletion
header("Location: index.php");
exit();
?>
