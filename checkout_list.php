<?php
session_start();
include 'navbar.php';
require_once "Book.php";
require_once "functions.php";

$file = "data.json";

// Handle book return
if (isset($_GET['return']) && isset($_GET['book_id']) && isset($_GET['borrower'])) {
    $bookId = $_GET['book_id'];
    $borrower = $_GET['borrower'];

    if (Book::returnBook($file, $bookId, $borrower)) {
        $_SESSION['success'] = "Book returned successfully!";
    } else {
        $_SESSION['error'] = "Failed to return the book.";
    }
    header("Location: checkout_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Books List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Checkout Books List</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"> <?= $_SESSION['success']; unset($_SESSION['success']); ?> </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"> <?= $_SESSION['error']; unset($_SESSION['error']); ?> </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Book Name</th>
                <th>Borrowed To</th>
                <th>Return Date</th>
                <th>No. of Books</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $books = Book::getCheckedOutBooks($file);
            foreach ($books as $book):
                // Create an empty array to store the count of books borrowed by each borrower
                $borrowerCounts = [];
                $borrowerReturnDates = [];

                // Loop through each borrower in the borrowedTo array
                foreach ($book['borrowedTo'] as $borrower) {
                    if (isset($borrower['name'])) {
                        // Count how many times each borrower appears
                        if (!isset($borrowerCounts[$borrower['name']])) {
                            $borrowerCounts[$borrower['name']] = 0;
                        }
                        $borrowerCounts[$borrower['name']]++;

                        // Store the return date of each borrower
                        $borrowerReturnDates[$borrower['name']] = $borrower['returnDate'] ?? 'N/A';
                    }
                }

                // Display each borrower and the count of their borrowed books
                foreach ($borrowerCounts as $borrowerName => $count):
            ?>
                <tr>
                    <td><?= htmlspecialchars($book['name']) ?></td>
                    <td><?= htmlspecialchars($borrowerName) ?></td>
                    <td><?= htmlspecialchars($borrowerReturnDates[$borrowerName]) ?></td>
                    <td><?= htmlspecialchars($count) ?></td>
                    <td>
                        <a href="javascript:void(0);" class="btn btn-primary return-book" 
                           data-book-id="<?= urlencode($book['id']) ?>" 
                           data-borrower="<?= urlencode($borrowerName) ?>">Return</a>
                    </td>
                </tr>
            <?php endforeach; endforeach; ?>
        </tbody>
    </table>
</div>
<!-- Footer -->
<footer class="footer bg-body-secondary text-dark py-3">
    <div class="container text-center">
        <p>&copy; 2024 Library Management System. All rights reserved.</p>
        <p>Developed by <a href="#" target="_blank" class="text-dark">George Fawzy Nada</a></p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Confirm before returning a book
    document.querySelectorAll('.return-book').forEach(button => {
        button.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            const borrower = this.getAttribute('data-borrower');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to return the book borrowed by ${borrower}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, return it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `checkout_list.php?return=1&book_id=${bookId}&borrower=${borrower}`;
                }
            });
        });
    });
</script>

</body>
</html>
