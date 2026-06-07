<?php

/**
 * ============================================================
 *  Kelas   : Database
 *  Proyek  : Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan
 *  Fungsi  : Mengelola koneksi tunggal ke MySQL menggunakan PDO
 *
 *  Konsep OOP yang diterapkan:
 *    - Encapsulation  : Properti koneksi dideklarasikan private/protected
 *    - Constructor    : Inisialisasi koneksi dilakukan otomatis saat objek dibuat
 *    - Singleton-lite : Hanya satu instance PDO yang dibuat per objek Database
 * ============================================================
 */

class Database
{
    // ---------------------------------------------------------
    // KONFIGURASI KONEKSI — sesuaikan jika diperlukan
    // ---------------------------------------------------------
    private string $host     = 'localhost';
    private string $dbName   = 'pbo_dbshowroom';
    private string $username = 'root';
    private string $password = '';
    private string $charset  = 'utf8mb4';

    /**
     * Menyimpan instance PDO yang aktif.
     * Dideklarasikan protected agar dapat diwarisi oleh subkelas
     * tanpa membuka akses ke luar (prinsip Encapsulation).
     *
     * @var PDO|null
     */
    protected ?PDO $connection = null;

    // ---------------------------------------------------------
    // CONSTRUCTOR — dijalankan otomatis saat: new Database()
    // ---------------------------------------------------------

    /**
     * Konstruktor otomatis yang langsung menginisialisasi koneksi PDO
     * ke database MySQL saat objek Database pertama kali dibuat.
     *
     * @throws PDOException Jika koneksi ke database gagal.
     */
    public function __construct()
    {
        $this->connect();
    }

    // ---------------------------------------------------------
    // PRIVATE METHOD — hanya digunakan secara internal
    // ---------------------------------------------------------

    /**
     * Membangun koneksi PDO ke MySQL.
     * Metode ini bersifat private sehingga tidak dapat dipanggil
     * dari luar kelas — hanya dipanggil oleh konstruktor.
     *
     * @return void
     * @throws PDOException
     */
    private function connect(): void
    {
        // Jika koneksi sudah ada, langsung keluar (hindari duplikasi)
        if ($this->connection !== null) {
            return;
        }

        // Data Source Name (DSN) untuk PDO MySQL
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";

        // Opsi tambahan untuk PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lempar exception saat error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Hasil query sebagai array asosiatif
            PDO::ATTR_EMULATE_PREPARES   => false,                    // Gunakan prepared statement asli
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}", // Pastikan encoding konsisten
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Tampilkan pesan yang ramah (tanpa membocorkan kredensial ke browser)
            throw new PDOException(
                "Koneksi ke database gagal: " . $e->getMessage(),
                (int) $e->getCode()
            );
        }
    }

    // ---------------------------------------------------------
    // PUBLIC METHOD — antarmuka yang tersedia untuk pengguna kelas
    // ---------------------------------------------------------

    /**
     * Mengembalikan objek PDO yang sudah aktif.
     * Ini adalah satu-satunya cara kode luar bisa mengakses koneksi
     * (getter eksplisit — prinsip Encapsulation).
     *
     * @return PDO Objek koneksi PDO yang siap digunakan.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Menutup koneksi PDO secara eksplisit dengan menghapus referensinya.
     * Berguna untuk membebaskan resource saat koneksi tidak lagi diperlukan.
     *
     * @return void
     */
    public function closeConnection(): void
    {
        $this->connection = null;
    }

    // ---------------------------------------------------------
    // DESTRUCTOR — dipanggil otomatis saat objek dihancurkan
    // ---------------------------------------------------------

    /**
     * Destruktor otomatis yang memastikan koneksi selalu ditutup
     * ketika objek Database tidak lagi digunakan atau keluar dari scope.
     */
    public function __destruct()
    {
        $this->closeConnection();
    }
}
