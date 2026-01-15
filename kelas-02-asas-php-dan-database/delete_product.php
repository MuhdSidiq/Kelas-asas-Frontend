<?php

// 1. Connect to MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kelas_02";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->select_db($dbname);

// 2. Handle product deletion
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $message = "Product deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
} else {
    $message = "Invalid product ID.";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Delete Product</h2>

        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
            <?php echo $message; ?>
        </div>

        <a href="products.php" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">Back to Product List</a>
    </div>
</body>
</html>