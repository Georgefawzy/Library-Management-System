<?php

class BookException extends Exception {}

class Book {
    public $id;
    public $name;
    public $quantity;
    public $borrowed = 0;
    public $borrowedTo = [];
    public $createdBy;
    public $createdAt;
    public $updatedBy;
    public $updatedAt;

    // Constructor includes createdBy, createdAt, and updatedAt
    public function __construct($id, $name, $quantity, $createdBy) {
        $this->id = $id;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->createdBy = $createdBy;
        $this->createdAt = date("Y-m-d H:i:s");  // Set createdAt timestamp
        $this->updatedAt = $this->createdAt;     // Set updatedAt timestamp to createdAt initially
    }

    // Checkout method to borrow books
    public static function checkout($file, $bookId, $borrowedTo, $returnDate, $numBooks) {
        $books = self::readData($file);

        foreach ($books as &$book) {
            if ((string)$book['id'] === (string)$bookId) {
                // Check if there are enough copies available
                $availableBooks = $book['quantity'] - $book['borrowed'];
                if ($availableBooks < $numBooks) {
                    throw new BookException("Not enough copies available. Only {$availableBooks} available.");
                }
                
                // Check if the borrower has already borrowed this book
                foreach ($book['borrowedTo'] as $borrower) {
                    if ($borrower['name'] === $borrowedTo) {
                        throw new BookException("Borrower already has this book.");
                    }
                }

                // Proceed to check out the books
                $checkoutDate = date("Y-m-d H:i:s");
                for ($i = 0; $i < $numBooks; $i++) {
                    $book['borrowedTo'][] = [
                        'name' => $borrowedTo,
                        'checkoutDate' => $checkoutDate,
                        'returnDate' => $returnDate
                    ];
                }

                $book['borrowed'] += $numBooks;  // Update the borrowed count
                $book['updatedAt'] = date("Y-m-d H:i:s");  // Update the last updated time

                // Write data back to the file
                if (!self::writeData($file, $books)) {
                    throw new BookException("Failed to write data to file.");
                }

                return true;
            }
        }

        throw new BookException("Book not found.");
    }

    // Return method to return borrowed books
    public static function returnBook($file, $bookId, $borrowerName) {
        $books = self::readData($file);

        foreach ($books as &$book) {
            if ((string)$book['id'] === (string)$bookId) {
                foreach ($book['borrowedTo'] as $index => $borrower) {
                    if ($borrower['name'] === $borrowerName) {
                        // Update the borrowed count and remove the borrower
                        $book['borrowed'] -= $borrower['numBooks'];
                        unset($book['borrowedTo'][$index]);
                        $book['borrowedTo'] = array_values($book['borrowedTo']);  // Reindex the array

                        // If no one else has borrowed the book, clear checkout/return dates
                        if (empty($book['borrowedTo'])) {
                            unset($book['returnDate']);
                            unset($book['checkoutDate']);
                            unset($book['dueDate']);
                        }

                        $book['updatedAt'] = date("Y-m-d H:i:s");  // Update the last updated time

                        // Write data back to the file
                        if (!self::writeData($file, $books)) {
                            throw new BookException("Failed to write data to file.");
                        }

                        return true;
                    }
                }

                throw new BookException("Borrower not found.");
            }
        }

        throw new BookException("Book not found.");
    }

    // Utility method to read data from a file
    private static function readData($file) {
        if (!file_exists($file)) {
            throw new BookException("Data file not found.");
        }
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if ($data === null) {
            throw new BookException("Failed to decode data from file.");
        }
        return $data;
    }

    // Utility method to write data to a file
    private static function writeData($file, $data) {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        if (file_put_contents($file, $json) === false) {
            throw new BookException("Failed to save data to file.");
        }
        return true;
    }

    // Retrieve all checked-out books
    public static function getCheckedOutBooks($file) {
        $books = self::readData($file);
        return array_filter($books, fn($book) => !empty($book['borrowedTo']));
    }

    // Add a new book (for adding books via form)
    public static function addBook($file, $name, $quantity, $createdBy) {
        $books = self::readData($file);

        // Check if the book already exists
        foreach ($books as $book) {
            if (strtolower($book['name']) === strtolower($name)) {
                throw new BookException("Book with this name already exists.");
            }
        }

        // Create a new book
        $newBook = new Book(uniqid(), $name, $quantity, $createdBy);

        // Add the new book to the books array
        $books[] = (array)$newBook;

        // Save the updated data to the file
        return self::writeData($file, $books);
    }
}

?>
