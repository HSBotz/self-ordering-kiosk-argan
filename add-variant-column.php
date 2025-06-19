<?php
// Script sederhana untuk menambahkan kolom variant_type ke tabel order_items

// Konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kedai_kiosk";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat output sebagai plain text
header('Content-Type: text/plain');

echo "Mulai menambahkan kolom variant_type...\n";

// Cek apakah kolom sudah ada
$checkColumn = $conn->query("SHOW COLUMNS FROM order_items LIKE 'variant_type'");
if ($checkColumn->num_rows > 0) {
    echo "Kolom variant_type sudah ada di tabel order_items\n";
} else {
    // Tambahkan kolom jika belum ada
    $sql = "ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes";
    if ($conn->query($sql) === TRUE) {
        echo "Kolom variant_type berhasil ditambahkan ke tabel order_items\n";
    } else {
        echo "Error menambahkan kolom: " . $conn->error . "\n";
    }
}

// Contoh data dengan variant_type untuk pengujian
echo "\nMemperbarui beberapa data existing dengan variant_type untuk pengujian...\n";
$updateHot = "UPDATE order_items SET variant_type = 'hot' WHERE id % 2 = 0 AND variant_type IS NULL LIMIT 5";
if ($conn->query($updateHot) === TRUE) {
    echo "5 item dengan ID genap diupdate menjadi hot\n";
} else {
    echo "Error updating records: " . $conn->error . "\n";
}

$updateIce = "UPDATE order_items SET variant_type = 'ice' WHERE id % 2 = 1 AND variant_type IS NULL LIMIT 5";
if ($conn->query($updateIce) === TRUE) {
    echo "5 item dengan ID ganjil diupdate menjadi ice\n";
} else {
    echo "Error updating records: " . $conn->error . "\n";
}

echo "\nSelesai!\n";

// Tutup koneksi
$conn->close();
