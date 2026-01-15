<?php

// 1. Connect to MySQL

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kelas_02";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);
$conn->select_db($dbname);

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    is_promoted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Seed data if table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $query = "INSERT INTO products (name, price, stock, is_promoted) VALUES ";
    $products = [];
    for ($i = 1; $i <= 50; $i++) {
        $name = "Product $i";
        $price = rand(10, 100) / 10; // Random price between 1.0 and 10.0
        $stock = rand(1, 100); // Random stock between 1 and 100
        $is_promoted = rand(0, 1) ? 'TRUE' : 'FALSE'; // Random boolean
        $products[] = "('$name', $price, $stock, $is_promoted)";
    }
    $query .= implode(",", $products);
    $conn->query($query);
}

// 2. Fetch and display data with filters
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

switch ($filter) {
    case 'price':
        $sql = "SELECT * FROM products ORDER BY price $order LIMIT $limit OFFSET $offset";
        break;
    case 'promotion':
        $sql = "SELECT * FROM products ORDER BY is_promoted $order LIMIT $limit OFFSET $offset";
        break;
    case 'stock':
        $sql = "SELECT * FROM products ORDER BY stock $order LIMIT $limit OFFSET $offset";
        break;
    default:
        $sql = "SELECT * FROM products LIMIT $limit OFFSET $offset";
        break;
}

$result = $conn->query($sql);

// Get total number of products for pagination
$totalResult = $conn->query("SELECT COUNT(*) as total FROM products");
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Product List</h2>
        <form method="GET" class="mb-4 flex items-center space-x-4">
            <div>
                <label for="filter" class="block text-sm font-medium">Sort by:</label>
                <select name="filter" id="filter" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">None</option>
                    <option value="price">Price</option>
                    <option value="promotion">Promotion</option>
                    <option value="stock">Stock</option>
                </select>
            </div>
            <div>
                <label for="order" class="block text-sm font-medium">Order:</label>
                <select name="order" id="order" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="ASC">Ascending</option>
                    <option value="DESC">Descending</option>
                </select>
            </div>
            <div>
                <button type="submit" class="mt-6 px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">Apply</button>
            </div>
        </form>
        <div class="mb-4">
            <a href="create_product.php" class="px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700">Add Product</a>
        </div>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (RM)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promotion</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                foreach ($rows as $row): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $row['id']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $row['name']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo number_format($row['price'], 2); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $row['stock']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $row['is_promoted'] ? 'text-green-600' : 'text-gray-900'; ?>">
                        <?php echo $row['is_promoted'] ? 'Yes' : 'No'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="px-2 py-1 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700">Edit</a>
                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="px-2 py-1 bg-red-600 text-white rounded-md shadow hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-4 flex justify-center space-x-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>&order=<?php echo $order; ?>" class="px-3 py-1 border border-gray-300 rounded-md shadow-sm hover:bg-gray-200 <?php echo $i == $page ? 'bg-indigo-600 text-white' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
