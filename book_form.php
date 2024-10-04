<?php
// Include database connection
include 'db_connect.php';

// Define variables and initialize with empty values
$title = $author = $price = $quantity = $genre_id = "";
$titleErr = $authorErr = $priceErr = $quantityErr = $genreErr = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty($_POST["title"])) {
        $titleErr = "Title is required";
    } else {
        $title = test_input($_POST["title"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $title)) {
            $titleErr = "Only letters and white space allowed";
        }
    }

    // Validate author
    if (empty($_POST["author"])) {
        $authorErr = "Author is required";
    } else {
        $author = test_input($_POST["author"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $author)) {
            $authorErr = "Only letters and white space allowed";
        }
    }

    // Validate price
    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } else {
        $price = test_input($_POST["price"]);
        if (!is_numeric($price) || $price <= 0) {
            $priceErr = "Enter a valid positive number for the price";
        }
    }

    // Validate quantity
    if (empty($_POST["quantity"])) {
        $quantityErr = "Quantity is required";
    } else {
        $quantity = test_input($_POST["quantity"]);
        if (!is_numeric($quantity) || $quantity <= 0) {
            $quantityErr = "Enter a valid positive number for the quantity";
        }
    }

    // Validate genre
    if (empty($_POST["genre_id"])) {
        $genreErr = "Genre is required";
    } else {
        $genre_id = test_input($_POST["genre_id"]);
        if (!is_numeric($genre_id)) {
            $genreErr = "Invalid genre selection";
        }
    }

    // If there are no validation errors, insert the data into the database
    if (empty($titleErr) && empty($authorErr) && empty($priceErr) && empty($quantityErr) && empty($genreErr)) {
        $stmt = $conn->prepare("INSERT INTO books (title, author, price, quantity, genre_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdii", $title, $author, $price, $quantity, $genre_id);

        if ($stmt->execute()) {
            echo "New book added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Function to sanitize input data
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore - Add New Book</title>
    <style>
        .error {color: #FF0000;}
    </style>
</head>
<body>
    <h2>Add New Book</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Title: <input type="text" name="title" value="<?php echo $title;?>">
        <span class="error">* <?php echo $titleErr;?></span>
        <br><br>
        Author: <input type="text" name="author" value="<?php echo $author;?>">
        <span class="error">* <?php echo $authorErr;?></span>
        <br><br>
        Price: <input type="text" name="price" value="<?php echo $price;?>">
        <span class="error">* <?php echo $priceErr;?></span>
        <br><br>
        Quantity: <input type="text" name="quantity" value="<?php echo $quantity;?>">
        <span class="error">* <?php echo $quantityErr;?></span>
        <br><br>
        Genre:
        <select name="genre_id">
            <option value="">Select Genre</option>
            <?php
            // Fetch genres from the database
            include 'db_connect.php';
            $result = $conn->query("SELECT genre_id, genre_name FROM genres");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['genre_id'] . "'>" . $row['genre_name'] . "</option>";
            }
            ?>
        </select>
        <span class="error">* <?php echo $genreErr;?></span>
        <br><br>
        <input type="submit" name="submit" value="Submit">
    </form>
</body>
</html>
