<?php
// Koneksi ke MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kedai_kiosk";

try {
    // Buat koneksi PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set error mode ke exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi database berhasil.<br>";

    // Cek apakah kolom sudah ada
    $stmt = $conn->query("SHOW COLUMNS FROM categories LIKE 'has_variants'");
    $columnExists = ($stmt->rowCount() > 0);

    if ($columnExists) {
        echo "Kolom has_variants sudah ada di tabel categories.<br>";
    } else {
        // Tambahkan kolom
        $sql = "ALTER TABLE categories ADD COLUMN has_variants TINYINT(1) DEFAULT 0 AFTER is_active";
        $conn->exec($sql);
        echo "Kolom has_variants berhasil ditambahkan ke tabel categories.<br>";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Tutup koneksi
$conn = null;
echo "Proses selesai.";
