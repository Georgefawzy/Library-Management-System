<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once "functions.php";
$file = "data.json";
$books = readData($file);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Library Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="checkout_books_List.php">Checkout Books List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_book.php">Add Book</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4 text-center">📚 Library Management System</h1>

    <!-- SweetAlert2 Success and Error Alerts -->
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
                title: 'Oops...',
                text: '<?= $_SESSION['error']; ?>'
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-secondary">Books Inventory</h4>
        <a href="add_book.php" class="btn btn-primary">➕ Add New Book</a>
    </div>

    <table class="table table-striped table-hover table-bordered mb-5">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Created At</th>
                <th>Created By</th>
                <th>Checkout Date</th>
                <th>Due Date</th>
                <th>Borrowed</th>
                <th>R.Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $index => $book): ?>
                    <?php
                        $borrowedCount = is_array($book['borrowedTo']) ? count($book['borrowedTo']) : 0;
                        $remainingStock = max(0, $book['quantity'] - $borrowedCount);
                        // Default values if no borrowings exist
                        $checkoutDate = $borrowedCount > 0 ? $book['borrowedTo'][0]['checkoutDate'] : 'N/A';
                        $dueDate = $borrowedCount > 0 ? $book['borrowedTo'][0]['returnDate'] : 'N/A';
                    ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= htmlspecialchars($book['name']) ?></td>
                        <td><?= htmlspecialchars($book['quantity']) ?></td>
                        <td><?= htmlspecialchars($book['createdAt']) ?></td>
                        <td><?= htmlspecialchars($book['createdBy']) ?></td>
                        <td><?= htmlspecialchars($checkoutDate) ?></td>
                        <td><?= htmlspecialchars($dueDate) ?></td>
                        <td><?= $borrowedCount ?></td>
                        <td><?= $remainingStock ?></td>
                        <td>
                            <a href="update.php?id=<?= $book['id'] ?>" class="btn btn-warning btn-sm">✏️ Update</a>
                            <a href="delete.php?id=<?= $book['id'] ?>" class="btn btn-danger btn-sm delete-btn" 
                               data-book-name="<?= $book['name'] ?>">🗑️ Delete</a>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#checkoutModal<?= $book['id'] ?>">
                                📚 Check Out
                            </button>
                        </td>
                    </tr>

                    <!-- Checkout Modal -->
                    <div class="modal fade" id="checkoutModal<?= $book['id'] ?>" tabindex="-1" aria-labelledby="checkoutLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="checkout.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Check Out Book: <?= htmlspecialchars($book['name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">

                                        <div class="mb-3">
                                            <label for="borrowed_to" class="form-label">Borrower's Name</label>
                                            <input type="text" name="borrowed_to" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="return_date" class="form-label">Return Date</label>
                                            <input type="date" name="return_date" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="num_books" class="form-label">Number of Books</label>
                                            <input type="number" name="num_books" class="form-control" min="1" max="<?= $remainingStock ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Check Out</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center text-muted">No books available.</td>
                </tr>
            <?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Add event listener to handle book deletion with confirmation
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const bookName = this.getAttribute('data-book-name');
            const href = this.getAttribute('href');

            Swal.fire({
                title: `Are you sure you want to delete ${bookName}?`,
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>

</body>
</html>
