<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "bookstoree"; // Updated database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for the edit form
$editMode = false;
$bookID = $bookName = $bookDescription = $quantityAvailable = $price = $author = $publishedYear = "";

// Handle POST requests for CRUD operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        // Add new book
        $name = htmlspecialchars($_POST['BookName']);
        $description = htmlspecialchars($_POST['BookDescription']);
        $quantity = (int)$_POST['QuantityAvailable'];
        $price = (float)$_POST['Price'];
        $author = htmlspecialchars($_POST['Author']);
        $publishedYear = (int)$_POST['PublishedYear'];

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO books (BookName, BookDescription, QuantityAvailable, Price, Author, PublishedYear) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidsi", $name, $description, $quantity, $price, $author, $publishedYear);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update'])) {
        // Update existing book
        $id = (int)$_POST['BookID'];
        $name = htmlspecialchars($_POST['BookName']);
        $description = htmlspecialchars($_POST['BookDescription']);
        $quantity = (int)$_POST['QuantityAvailable'];
        $price = (float)$_POST['Price'];
        $author = htmlspecialchars($_POST['Author']);
        $publishedYear = (int)$_POST['PublishedYear'];

        // Prepare and bind
        $stmt = $conn->prepare("UPDATE books SET BookName=?, BookDescription=?, QuantityAvailable=?, Price=?, Author=?, PublishedYear=? WHERE BookID=?");
        $stmt->bind_param("ssidsii", $name, $description, $quantity, $price, $author, $publishedYear, $id);
        $stmt->execute();
        $stmt->close();

        // Reset the form after updating
        $editMode = false;
    } elseif (isset($_POST['delete'])) {
        // Delete book
        $id = (int)$_POST['BookID'];
        $stmt = $conn->prepare("DELETE FROM books WHERE BookID=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        // Fetch the book details for editing
        $bookID = (int)$_POST['BookID'];
        $result = $conn->query("SELECT * FROM books WHERE BookID = $bookID");
        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();
            $bookName = $book['BookName'];
            $bookDescription = $book['BookDescription'];
            $quantityAvailable = $book['QuantityAvailable'];
            $price = $book['Price'];
            $author = $book['Author'];
            $publishedYear = $book['PublishedYear'];
            $editMode = true;
        }
    }
}

// Fetch all books
$result = $conn->query("SELECT * FROM books");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Books</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="container mt-5">
    <h1>Admin Portal - Manage Books</h1>
    
    <!-- Add/Edit Book Form -->
    <h3><?php echo $editMode ? "Edit Book" : "Add New Book"; ?></h3>
    <form method="POST" action="">
        <input type="hidden" name="BookID" value="<?php echo $bookID; ?>">
        <input type="text" name="BookName" placeholder="Book Name" value="<?php echo $bookName; ?>" required class="form-control mb-2">
        <textarea name="BookDescription" placeholder="Book Description" required class="form-control mb-2"><?php echo $bookDescription; ?></textarea>
        <input type="number" name="QuantityAvailable" placeholder="Quantity Available" value="<?php echo $quantityAvailable; ?>" required class="form-control mb-2">
        <input type="text" name="Price" placeholder="Price" value="<?php echo $price; ?>" required class="form-control mb-2">
        <input type="text" name="Author" placeholder="Author" value="<?php echo $author; ?>" required class="form-control mb-2">
        <input type="number" name="PublishedYear" placeholder="Published Year" value="<?php echo $publishedYear; ?>" required class="form-control mb-2">
        <button type="submit" name="<?php echo $editMode ? 'update' : 'add'; ?>" class="btn btn-add"><?php echo $editMode ? 'Update Book' : 'Add Book'; ?></button>
    </form>

    <!-- Book List -->
    <h3 class="mt-5">Book List</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Book ID</th>
                <th>Book Name</th>
                <th>Description</th>
                <th>Quantity Available</th>
                <th>Price</th>
                <th>Author</th>
                <th>Published Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['BookID']; ?></td>
                <td><?php echo $row['BookName']; ?></td>
                <td><?php echo $row['BookDescription']; ?></td>
                <td><?php echo $row['QuantityAvailable']; ?></td>
                <td><?php echo $row['Price']; ?></td>
                <td><?php echo $row['Author']; ?></td>
                <td><?php echo $row['PublishedYear']; ?></td>
                <td class="inline-buttons">
                    <!-- Update Button -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                        <button type="submit" name="edit" class="btn btn-update">Update</button>
                    </form>
                    
                    <!-- Delete Form -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                        <button type="submit" name="delete" class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
