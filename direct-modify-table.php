<?php
// Baca file .env untuk mendapatkan informasi database
$envFile = __DIR__ . '/.env';
$dbname = 'laravel'; // Default

if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    $lines = explode("\n", $env);

    foreach ($lines as $line) {
        if (strpos($line, 'DB_DATABASE=') === 0) {
            $dbname = trim(substr($line, 12));
            break;
        }
    }
}

// Koneksi ke MySQL
$servername = "localhost";
$username = "root";
$password = "";

echo "Mencoba menggunakan database: $dbname<br>";

// Mencoba membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "Koneksi ke database berhasil.<br>";

// Memeriksa apakah kolom sudah ada
$checkColumn = $conn->query("SHOW COLUMNS FROM categories LIKE 'has_variants'");
if ($checkColumn->num_rows > 0) {
    echo "Kolom has_variants sudah ada di tabel categories.<br>";
} else {
    // Menjalankan ALTER TABLE
    $sql = "ALTER TABLE categories ADD COLUMN has_variants TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active";

    if ($conn->query($sql) === TRUE) {
        echo "Kolom has_variants berhasil ditambahkan ke tabel categories.<br>";
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}

$conn->close();
echo "Proses selesai.";
?>
