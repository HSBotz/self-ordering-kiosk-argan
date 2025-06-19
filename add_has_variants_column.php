<?php

// Load environment variables dari .env
$env = file_get_contents(__DIR__ . '/.env');
$lines = explode("\n", $env);
$envVars = [];
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
        list($key, $value) = explode('=', $line, 2);
        $envVars[trim($key)] = trim($value);
    }
}

// Ambil informasi database dari .env
$host = $envVars['DB_HOST'] ?? 'localhost';
$username = $envVars['DB_USERNAME'] ?? 'root';
$password = $envVars['DB_PASSWORD'] ?? '';
$database = $envVars['DB_DATABASE'] ?? 'kedai_kiosk';

// Koneksi ke database
try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    // Set mode error ke exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi database berhasil.<br>";

    // Cek apakah kolom has_variants sudah ada
    $sql = "SHOW COLUMNS FROM categories LIKE 'has_variants'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "Kolom has_variants sudah ada di tabel categories.<br>";
    } else {
        // Tambahkan kolom has_variants
        $sql = "ALTER TABLE categories ADD COLUMN has_variants BOOLEAN DEFAULT FALSE AFTER is_active";
        $conn->exec($sql);
        echo "Kolom has_variants berhasil ditambahkan ke tabel categories.<br>";
    }

} catch(PDOException $e) {
    echo "Koneksi database gagal: " . $e->getMessage() . "<br>";
}

// Tutup koneksi
$conn = null;
echo "Proses selesai.";
