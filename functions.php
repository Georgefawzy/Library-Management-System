<?php
// Read data from the JSON file
function readData($file) {
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?? [];
    }
    return [];
}

// Function to write data to the JSON file
function writeData($file, $data) {
    // Write data to the JSON file with pretty print format
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Function to check out a book
function checkoutBook($file, $bookId, $borrowedTo, $returnDate, $borrowCount) {
    $books = readData($file);

    // Loop through books to find the book by its ID
    foreach ($books as &$book) {
        if ($book['id'] === $bookId) {
            // Check if there are enough copies available to borrow
            if (($book['quantity'] - $book['borrowed']) >= $borrowCount) {

                // Check if the borrower already has the book checked out
                foreach ($book['borrowedTo'] as $borrower) {
                    if ($borrower['name'] === $borrowedTo) {
                        // Borrower already has the book, return false
                        return "You already have this book checked out."; // Message for user
                    }
                }

                // Set the checkout date and due date for the borrowed books
                $checkoutDate = date("Y-m-d H:i:s");

                // Add borrowed books for the borrower
                for ($i = 0; $i < $borrowCount; $i++) {
                    $book['borrowedTo'][] = [
                        'name' => $borrowedTo,
                        'checkoutDate' => $checkoutDate,
                        'dueDate' => $returnDate
                    ];
                }

                // Update the borrowed count
                $book['borrowed'] += $borrowCount;

                // Save the updated data back to the file
                if (writeData($file, $books)) {
                    return true; // Successfully borrowed the book(s)
                } else {
                    return "Failed to save the updated data."; // Error message on failure
                }
            } else {
                return "Not enough copies available to borrow."; // Not enough copies available
            }
        }
    }

    return "Book not found."; // If the book ID was not found
}

// Function to handle book return
function returnBook($file, $bookId, $borrowedTo) {
    $books = readData($file);

    // Loop through books to find the book by its ID
    foreach ($books as &$book) {
        if ($book['id'] === $bookId) {

            // Loop through borrowed entries to find the borrower
            foreach ($book['borrowedTo'] as $key => $borrower) {
                if ($borrower['name'] === $borrowedTo) {
                    // Remove the borrowed entry for the user
                    unset($book['borrowedTo'][$key]);
                    $book['borrowed']--; // Decrease the borrowed count

                    // Reindex the array to prevent gaps
                    $book['borrowedTo'] = array_values($book['borrowedTo']);

                    // If there are no more borrowers, clear the checkout and due dates
                    if (empty($book['borrowedTo'])) {
                        unset($book['checkoutDate']);
                        unset($book['dueDate']);
                    }

                    // Save the updated data back to the file
                    if (writeData($file, $books)) {
                        return true; // Successfully returned the book
                    } else {
                        return "Failed to save the updated data."; // Error message on failure
                    }
                }
            }
            return "Borrower not found."; // If the borrower wasn't found in the borrowed list
        }
    }

    return "Book not found."; // If the book ID was not found
}
?>
