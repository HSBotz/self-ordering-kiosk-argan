<?php

// Set header content type to text
header('Content-Type: text/plain');

// Autoload composer
require __DIR__ . '/vendor/autoload.php';

// Load .env file
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Connect to the database
$host = env('DB_HOST', 'localhost');
$port = env('DB_PORT', '3306');
$database = env('DB_DATABASE', 'forge');
$username = env('DB_USERNAME', 'forge');
$password = env('DB_PASSWORD', '');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully\n";
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if variant_type column exists in order_items table
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'variant_type'");
    $columnExists = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$columnExists) {
        echo "variant_type column does not exist in order_items table. Adding it now...\n";

        // Add variant_type column
        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
        echo "variant_type column added successfully\n";
    } else {
        echo "variant_type column already exists\n";
    }
} catch (PDOException $e) {
    echo "Error checking/adding column: " . $e->getMessage() . "\n";
}

// Get a sample of order items
try {
    $stmt = $pdo->query("SELECT * FROM order_items LIMIT 10");
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nSample of order items:\n";
    echo "----------------------\n";
    foreach ($orderItems as $item) {
        echo "ID: {$item['id']}, Order ID: {$item['order_id']}, Product ID: {$item['product_id']}, " .
             "Quantity: {$item['quantity']}, Price: {$item['price']}, " .
             "Variant Type: " . ($item['variant_type'] ?? 'NULL') . "\n";
    }
} catch (PDOException $e) {
    echo "Error fetching order items: " . $e->getMessage() . "\n";
}

// Helper function to get environment variables
function env($key, $default = null) {
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    return $value;
}

echo "\nScript completed\n";
