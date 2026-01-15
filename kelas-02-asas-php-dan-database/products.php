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

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id > 0) {
        $delete_sql = "DELETE FROM products WHERE id = $delete_id";
        if ($conn->query($delete_sql) === TRUE) {
            $message = "Product deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting product: " . $conn->error;
            $message_type = "error";
        }
    }
}

// Handle edit action
if (isset($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $is_promoted = isset($_POST['is_promoted']) ? 1 : 0;

    $edit_sql = "UPDATE products SET name = '$name', price = $price, stock = $stock, is_promoted = $is_promoted WHERE id = $edit_id";
    if ($conn->query($edit_sql) === TRUE) {
        $message = "Product updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating product: " . $conn->error;
        $message_type = "error";
    }
}
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
        <div class="mb-4 flex space-x-4">
            <a href="index.php" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700">Home</a>
            <a href="setup.php" class="px-4 py-2 bg-yellow-600 text-white rounded-md shadow hover:bg-yellow-700">Setup</a>
        </div>
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
        <?php if (isset($message)): ?>
        <div class="mb-4 p-4 bg-<?php echo $message_type === 'success' ? 'green' : 'red'; ?>-100 text-<?php echo $message_type === 'success' ? 'green' : 'red'; ?>-800 rounded-md">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
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
                        <button onclick="openModal(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', <?php echo $row['price']; ?>, <?php echo $row['stock']; ?>, <?php echo $row['is_promoted'] ? 'true' : 'false'; ?>)" class="px-2 py-1 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700">Edit</button>
                        <a href="?delete_id=<?php echo $row['id']; ?>" class="px-2 py-1 bg-red-600 text-white rounded-md shadow hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
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

    <!-- Modal for Edit Form -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Edit Product</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="mb-4">
                    <label for="edit_name" class="block text-sm font-medium">Product Name</label>
                    <input type="text" name="name" id="edit_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="edit_price" class="block text-sm font-medium">Price (RM)</label>
                    <input type="number" name="price" id="edit_price" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="edit_stock" class="block text-sm font-medium">Stock</label>
                    <input type="number" name="stock" id="edit_stock" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="is_promoted" id="edit_is_promoted" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="edit_is_promoted" class="ml-2 block text-sm text-gray-800">Promote this product</label>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-600 text-white rounded-md shadow hover:bg-gray-700 mr-2">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openModal(id, name, price, stock, isPromoted) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_stock').value = stock;
        document.getElementById('edit_is_promoted').checked = isPromoted;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
    </script>
</body>
</html>
<?php
$conn->close();
?>
