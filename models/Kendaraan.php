<?php

/**
 * ============================================================
 *  Abstract Class : Kendaraan
 *  Proyek         : Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan
 *  Representasi   : Tabel induk `tb_kendaraan`
 *
 *  Pilar OOP yang diterapkan:
 *    ✔ Abstraction   — Kelas ini tidak dapat diinstansiasi langsung;
 *                      hanya menjadi "cetak biru" bagi subkelas konkret.
 *    ✔ Encapsulation — Semua atribut dideklarasikan protected sehingga
 *                      tidak dapat diakses langsung dari luar hierarki,
 *                      namun tetap bisa diwariskan ke subkelas.
 *    ✔ Abstract Method — Memaksa setiap subkelas untuk mengimplementasikan
 *                        hitungPajakTahunan() dan tampilkanSpesifikasi().
 * ============================================================
 */

abstract class Kendaraan
{
    // =========================================================
    // ATRIBUT (dipetakan 1:1 ke kolom tabel `tb_kendaraan`)
    // Access modifier: protected → dapat diwarisi, tidak bisa
    // diakses langsung dari luar kelas.
    // =========================================================

    /** @var int|null  Kolom: id_kendaraan (PK, AUTO_INCREMENT) */
    protected ?int $id_kendaraan;

    /** @var string    Kolom: brand — merek kendaraan (maks 50 karakter) */
    protected string $brand;

    /** @var string    Kolom: model — nama model (maks 50 karakter) */
    protected string $model;

    /** @var int       Kolom: tahun — tahun produksi */
    protected int $tahun;

    /** @var int       Kolom: stok — jumlah unit tersedia */
    protected int $stok;

    /**
     * @var float      Kolom: harga_dasar — DECIMAL(15,2)
     *                 Menggunakan float (PHP) untuk mewakili DECIMAL MySQL.
     */
    protected float $harga_dasar;

    /**
     * @var string     Kolom: transmisi — ENUM('Manual','AT','CVT')
     *                 Nilai valid divalidasi di dalam setter.
     */
    protected string $transmisi;

    /** @var int       Kolom: jumlah_kursi — kapasitas penumpang */
    protected int $jumlah_kursi;

    /** @var array<string> Nilai ENUM yang diizinkan untuk kolom transmisi */
    private const TRANSMISI_VALID = ['Manual', 'AT', 'CVT'];

    // =========================================================
    // CONSTRUCTOR
    // =========================================================

    /**
     * Konstruktor untuk menginisialisasi semua atribut kendaraan.
     * Dipanggil otomatis saat subkelas konkret membuat objek baru.
     *
     * @param int|null $id_kendaraan  ID unik kendaraan (null jika belum tersimpan di DB)
     * @param string   $brand         Merek kendaraan (cth: 'Toyota', 'Honda')
     * @param string   $model         Nama model (cth: 'Avanza Veloz')
     * @param int      $tahun         Tahun produksi (cth: 2023)
     * @param int      $stok          Jumlah stok unit
     * @param float    $harga_dasar   Harga dasar dalam rupiah
     * @param string   $transmisi     Jenis transmisi: 'Manual' | 'AT' | 'CVT'
     * @param int      $jumlah_kursi  Kapasitas kursi penumpang
     *
     * @throws InvalidArgumentException Jika nilai transmisi tidak valid.
     */
    public function __construct(
        ?int   $id_kendaraan,
        string $brand,
        string $model,
        int    $tahun,
        int    $stok,
        float  $harga_dasar,
        string $transmisi,
        int    $jumlah_kursi
    ) {
        $this->id_kendaraan = $id_kendaraan;
        $this->brand        = $brand;
        $this->model        = $model;
        $this->tahun        = $tahun;
        $this->stok         = $stok;
        $this->harga_dasar  = $harga_dasar;
        $this->setTransmisi($transmisi); // Lewat setter agar validasi ENUM berjalan
        $this->jumlah_kursi = $jumlah_kursi;
    }

    // =========================================================
    // ABSTRACT METHODS
    // Subkelas WAJIB mengimplementasikan kedua metode ini.
    // =========================================================

    /**
     * Menghitung pajak tahunan kendaraan.
     * Logika perhitungan berbeda tiap jenis kendaraan (mobil listrik,
     * konvensional, motor besar, dsb.), sehingga dideklarasikan abstract
     * agar setiap subkelas menentukan rumusnya sendiri.
     *
     * @return float Nilai pajak tahunan dalam rupiah.
     */
    abstract public function hitungPajakTahunan(): float;

    /**
     * Menampilkan spesifikasi lengkap kendaraan ke layar/output.
     * Setiap jenis kendaraan memiliki spesifikasi uniknya masing-masing
     * (cth: motor besar menampilkan tipe motor, mobil listrik menampilkan
     * kapasitas baterai), sehingga dideklarasikan abstract.
     *
     * @return void
     */
    abstract public function tampilkanSpesifikasi(): void;

    // =========================================================
    // GETTERS — method public untuk membaca nilai atribut protected
    // =========================================================

    /**
     * @return int|null ID kendaraan (null jika belum disimpan ke database)
     */
    public function getIdKendaraan(): ?int
    {
        return $this->id_kendaraan;
    }

    /**
     * @return string Merek kendaraan
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @return string Nama model kendaraan
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return int Tahun produksi
     */
    public function getTahun(): int
    {
        return $this->tahun;
    }

    /**
     * @return int Jumlah stok saat ini
     */
    public function getStok(): int
    {
        return $this->stok;
    }

    /**
     * @return float Harga dasar dalam rupiah
     */
    public function getHargaDasar(): float
    {
        return $this->harga_dasar;
    }

    /**
     * @return string Jenis transmisi ('Manual' | 'AT' | 'CVT')
     */
    public function getTransmisi(): string
    {
        return $this->transmisi;
    }

    /**
     * @return int Jumlah kursi penumpang
     */
    public function getJumlahKursi(): int
    {
        return $this->jumlah_kursi;
    }

    // =========================================================
    // SETTERS — method public untuk mengubah nilai atribut protected
    // =========================================================

    /**
     * Mengatur ID kendaraan (biasanya diisi setelah INSERT ke database).
     *
     * @param int $id_kendaraan
     * @return void
     */
    public function setIdKendaraan(int $id_kendaraan): void
    {
        $this->id_kendaraan = $id_kendaraan;
    }

    /**
     * Mengatur merek kendaraan.
     *
     * @param string $brand Merek kendaraan (tidak boleh kosong)
     * @return void
     * @throws InvalidArgumentException
     */
    public function setBrand(string $brand): void
    {
        $brand = trim($brand);
        if (empty($brand)) {
            throw new InvalidArgumentException("Brand kendaraan tidak boleh kosong.");
        }
        $this->brand = $brand;
    }

    /**
     * Mengatur nama model kendaraan.
     *
     * @param string $model Nama model (tidak boleh kosong)
     * @return void
     * @throws InvalidArgumentException
     */
    public function setModel(string $model): void
    {
        $model = trim($model);
        if (empty($model)) {
            throw new InvalidArgumentException("Model kendaraan tidak boleh kosong.");
        }
        $this->model = $model;
    }

    /**
     * Mengatur tahun produksi kendaraan.
     *
     * @param int $tahun Tahun (min: 1886 — tahun pertama mobil diproduksi)
     * @return void
     * @throws InvalidArgumentException
     */
    public function setTahun(int $tahun): void
    {
        $tahunSekarang = (int) date('Y');
        if ($tahun < 1886 || $tahun > $tahunSekarang + 1) {
            throw new InvalidArgumentException(
                "Tahun tidak valid. Harus antara 1886 hingga " . ($tahunSekarang + 1) . "."
            );
        }
        $this->tahun = $tahun;
    }

    /**
     * Mengatur jumlah stok kendaraan.
     *
     * @param int $stok Jumlah stok (tidak boleh negatif)
     * @return void
     * @throws InvalidArgumentException
     */
    public function setStok(int $stok): void
    {
        if ($stok < 0) {
            throw new InvalidArgumentException("Stok tidak boleh bernilai negatif.");
        }
        $this->stok = $stok;
    }

    /**
     * Mengatur harga dasar kendaraan.
     *
     * @param float $harga_dasar Harga dalam rupiah (harus lebih dari 0)
     * @return void
     * @throws InvalidArgumentException
     */
    public function setHargaDasar(float $harga_dasar): void
    {
        if ($harga_dasar <= 0) {
            throw new InvalidArgumentException("Harga dasar harus bernilai lebih dari 0.");
        }
        $this->harga_dasar = $harga_dasar;
    }

    /**
     * Mengatur jenis transmisi dengan validasi ENUM MySQL.
     * Nilai yang diterima hanya: 'Manual', 'AT', atau 'CVT'.
     *
     * @param string $transmisi
     * @return void
     * @throws InvalidArgumentException
     */
    public function setTransmisi(string $transmisi): void
    {
        if (!in_array($transmisi, self::TRANSMISI_VALID, true)) {
            $valid = implode(', ', self::TRANSMISI_VALID);
            throw new InvalidArgumentException(
                "Transmisi '$transmisi' tidak valid. Pilihan: $valid."
            );
        }
        $this->transmisi = $transmisi;
    }

    /**
     * Mengatur jumlah kursi kendaraan.
     *
     * @param int $jumlah_kursi Harus bernilai positif (minimal 1)
     * @return void
     * @throws InvalidArgumentException
     */
    public function setJumlahKursi(int $jumlah_kursi): void
    {
        if ($jumlah_kursi < 1) {
            throw new InvalidArgumentException("Jumlah kursi minimal adalah 1.");
        }
        $this->jumlah_kursi = $jumlah_kursi;
    }

    // =========================================================
    // HELPER METHOD — tersedia untuk semua subkelas
    // =========================================================

    /**
     * Memformat harga dasar ke format Rupiah yang mudah dibaca.
     * Method ini protected agar dapat digunakan oleh subkelas
     * saat mengimplementasikan tampilkanSpesifikasi().
     *
     * @param  float  $nominal Nilai dalam rupiah
     * @return string Contoh: "Rp 290.000.000,00"
     */
    protected function formatRupiah(float $nominal): string
    {
        return 'Rp ' . number_format($nominal, 2, ',', '.');
    }

    /**
     * Mengembalikan representasi singkat kendaraan (brand + model + tahun).
     * Berguna untuk logging, debugging, atau label tabel.
     *
     * @return string Contoh: "Toyota Avanza Veloz (2022)"
     */
    public function getNamaLengkap(): string
    {
        return "{$this->brand} {$this->model} ({$this->tahun})";
    }
}
