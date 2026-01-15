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

// 2. Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $is_promoted = isset($_POST['is_promoted']) ? 1 : 0;

    $sql = "INSERT INTO products (name, price, stock, is_promoted) VALUES ('$name', $price, $stock, $is_promoted)";

    if ($conn->query($sql) === TRUE) {
        $message = "Product created successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Create Product</h2>

        <?php if (isset($message)): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium">Product Name</label>
                <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="price" class="block text-sm font-medium">Price (RM)</label>
                <input type="number" name="price" id="price" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium">Stock</label>
                <input type="number" name="stock" id="stock" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_promoted" id="is_promoted" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_promoted" class="ml-2 block text-sm text-gray-800">Promote this product</label>
            </div>

            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">Create Product</button>
            </div>
        </form>
    </div>
</body>
</html>