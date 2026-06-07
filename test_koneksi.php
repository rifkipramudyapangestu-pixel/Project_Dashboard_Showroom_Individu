<?php

/**
 * Contoh penggunaan kelas Database
 * File ini hanya untuk demonstrasi / pengujian koneksi.
 * Hapus atau jangan di-upload ke production.
 */

// Muat kelas Database
require_once __DIR__ . '/config/Database.php';

try {
    // 1. Buat objek Database — konstruktor langsung membuka koneksi PDO
    $db = new Database();

    // 2. Ambil objek PDO melalui getter publik
    $pdo = $db->getConnection();

    echo "✅ Koneksi ke 'pbo_dbshowroom' berhasil!" . PHP_EOL;

    // 3. Contoh query sederhana (cek versi MySQL)
    $stmt = $pdo->query("SELECT VERSION() AS versi");
    $row  = $stmt->fetch();
    echo "   MySQL versi: " . $row['versi'] . PHP_EOL;

    // 4. Koneksi ditutup otomatis oleh __destruct() saat $db keluar dari scope
    //    atau bisa dipanggil manual:
    // $db->closeConnection();

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}
