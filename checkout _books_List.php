<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'navbar.php';
require_once "Book.php";
require_once "functions.php";

$file = "data.json";

// Handle book return
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return']) && isset($_POST['book_id']) && isset($_POST['borrower'])) {
    $bookId = $_POST['book_id'];
    $borrower = $_POST['borrower'];

    try {
        if (Book::returnBook($file, $bookId, $borrower)) {
            $_SESSION['success'] = "Book returned successfully!";
        } else {
            $_SESSION['error'] = "Failed to return the book.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
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
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $_SESSION['success']; ?>'
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= $_SESSION['error']; ?>'
            });
        </script>
        <?php unset($_SESSION['error']); ?>
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
                foreach ($book['borrowedTo'] as $borrower):
            ?>
                <tr>
                    <td><?= htmlspecialchars($book['name']) ?></td>
                    <td><?= htmlspecialchars($borrower['name']) ?></td>
                    <td><?= htmlspecialchars($dueDate) ?></td>
                    <td><?= htmlspecialchars($borrower['numBooks']) ?></td>
                    <td>
                        <form method="post" onsubmit="return confirmReturn();">
                            <input type="hidden" name="return" value="1">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['id']) ?>">
                            <input type="hidden" name="borrower" value="<?= htmlspecialchars($borrower['name']) ?>">
                            <button type="submit" class="btn btn-primary">Return</button>
                        </form>
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
function confirmReturn() {
    return Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to return this book. Are you sure you want to proceed?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, return it!'
    }).then((result) => {
        return result.isConfirmed;
    });
}
</script>
</body>
</html>
