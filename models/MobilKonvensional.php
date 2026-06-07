<?php

/**
 * ============================================================
 *  Class   : MobilKonvensional
 *  Extends : Kendaraan (Abstract Class)
 *  Proyek  : Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan
 *  Tabel   : tb_mobil_konvensional (JOIN tb_kendaraan)
 *
 *  Pilar OOP yang diterapkan:
 *    ✔ Inheritance   — Mewarisi semua atribut & method dari Kendaraan
 *    ✔ Polymorphism  — Override hitungPajakTahunan() & tampilkanSpesifikasi()
 *    ✔ Encapsulation — Atribut unik dideklarasikan protected
 * ============================================================
 */

require_once __DIR__ . '/Kendaraan.php';

class MobilKonvensional extends Kendaraan
{
    // =========================================================
    // ATRIBUT UNIK (dipetakan ke kolom tb_mobil_konvensional)
    // =========================================================

    /** @var int    Kolom: kapasitas_mesin — dalam satuan cc (cubic centimeter) */
    protected int $kapasitas_mesin;

    /** @var string Kolom: jenis_bahan_bakar — cth: 'Bensin', 'Diesel' */
    protected string $jenis_bahan_bakar;

    /** @var float  Kolom: kapasitas_tangki — DECIMAL(5,2), dalam liter */
    protected float $kapasitas_tangki;

    /** @var string Kolom: konsumsi_bbm — cth: '1:15 km/l' */
    protected string $konsumsi_bbm;

    // =========================================================
    // CONSTRUCTOR
    // =========================================================

    /**
     * Konstruktor MobilKonvensional.
     * Memanggil parent::__construct() untuk mengisi data umum kendaraan,
     * kemudian menginisialisasi atribut spesifik mobil konvensional.
     *
     * @param int|null $id_kendaraan
     * @param string   $brand
     * @param string   $model
     * @param int      $tahun
     * @param int      $stok
     * @param float    $harga_dasar
     * @param string   $transmisi        'Manual' | 'AT' | 'CVT'
     * @param int      $jumlah_kursi
     * @param int      $kapasitas_mesin  Kapasitas mesin dalam cc
     * @param string   $jenis_bahan_bakar Jenis bahan bakar (cth: 'Bensin')
     * @param float    $kapasitas_tangki  Volume tangki dalam liter
     * @param string   $konsumsi_bbm     Konsumsi BBM (cth: '1:15 km/l')
     */
    public function __construct(
        ?int   $id_kendaraan,
        string $brand,
        string $model,
        int    $tahun,
        int    $stok,
        float  $harga_dasar,
        string $transmisi,
        int    $jumlah_kursi,
        int    $kapasitas_mesin,
        string $jenis_bahan_bakar,
        float  $kapasitas_tangki,
        string $konsumsi_bbm
    ) {
        // Inisialisasi data umum dari kelas induk
        parent::__construct(
            $id_kendaraan,
            $brand,
            $model,
            $tahun,
            $stok,
            $harga_dasar,
            $transmisi,
            $jumlah_kursi
        );

        // Inisialisasi atribut spesifik
        $this->kapasitas_mesin   = $kapasitas_mesin;
        $this->jenis_bahan_bakar = $jenis_bahan_bakar;
        $this->kapasitas_tangki  = $kapasitas_tangki;
        $this->konsumsi_bbm      = $konsumsi_bbm;
    }

    // =========================================================
    // ABSTRACT METHOD IMPLEMENTATION (Polymorphism - Overriding)
    // =========================================================

    /**
     * Menghitung pajak tahunan mobil konvensional.
     *
     * Rumus: (2% × harga_dasar) + (kapasitas_mesin × 500)
     *
     * Contoh (Toyota Avanza 1500cc, Rp 290.000.000):
     *   = (0.02 × 290.000.000) + (1500 × 500)
     *   = 5.800.000 + 750.000
     *   = Rp 6.550.000
     *
     * @return float Pajak tahunan dalam rupiah
     */
    public function hitungPajakTahunan(): float
    {
        return (0.02 * $this->harga_dasar) + ($this->kapasitas_mesin * 500);
    }

    /**
     * Menampilkan seluruh spesifikasi mobil konvensional ke output.
     * Menggabungkan data umum (dari kelas induk) dengan data spesifik.
     *
     * @return void
     */
    public function tampilkanSpesifikasi(): void
    {
        $pajak = $this->hitungPajakTahunan();

        echo "╔══════════════════════════════════════════════════╗" . PHP_EOL;
        echo "║         SPESIFIKASI MOBIL KONVENSIONAL           ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ ID Kendaraan    : " . str_pad($this->id_kendaraan ?? '-', 30) . " ║" . PHP_EOL;
        echo "║ Brand           : " . str_pad($this->brand, 30) . " ║" . PHP_EOL;
        echo "║ Model           : " . str_pad($this->model, 30) . " ║" . PHP_EOL;
        echo "║ Tahun           : " . str_pad($this->tahun, 30) . " ║" . PHP_EOL;
        echo "║ Transmisi       : " . str_pad($this->transmisi, 30) . " ║" . PHP_EOL;
        echo "║ Jumlah Kursi    : " . str_pad($this->jumlah_kursi . ' kursi', 30) . " ║" . PHP_EOL;
        echo "║ Stok            : " . str_pad($this->stok . ' unit', 30) . " ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Kapasitas Mesin : " . str_pad($this->kapasitas_mesin . ' cc', 30) . " ║" . PHP_EOL;
        echo "║ Bahan Bakar     : " . str_pad($this->jenis_bahan_bakar, 30) . " ║" . PHP_EOL;
        echo "║ Kapasitas Tangki: " . str_pad($this->kapasitas_tangki . ' liter', 30) . " ║" . PHP_EOL;
        echo "║ Konsumsi BBM    : " . str_pad($this->konsumsi_bbm, 30) . " ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Harga Dasar     : " . str_pad($this->formatRupiah($this->harga_dasar), 30) . " ║" . PHP_EOL;
        echo "║ Pajak Tahunan   : " . str_pad($this->formatRupiah($pajak), 30) . " ║" . PHP_EOL;
        echo "╚══════════════════════════════════════════════════╝" . PHP_EOL;
    }

    // =========================================================
    // GETTERS & SETTERS ATRIBUT UNIK
    // =========================================================

    public function getKapasitasMesin(): int    { return $this->kapasitas_mesin; }
    public function getJenisBahanBakar(): string { return $this->jenis_bahan_bakar; }
    public function getKapasitasTangki(): float  { return $this->kapasitas_tangki; }
    public function getKonsumsibbm(): string     { return $this->konsumsi_bbm; }

    public function setKapasitasMesin(int $cc): void
    {
        if ($cc <= 0) throw new InvalidArgumentException("Kapasitas mesin harus lebih dari 0 cc.");
        $this->kapasitas_mesin = $cc;
    }

    public function setJenisBahanBakar(string $bbm): void
    {
        $this->jenis_bahan_bakar = trim($bbm);
    }

    public function setKapasitasTangki(float $liter): void
    {
        if ($liter <= 0) throw new InvalidArgumentException("Kapasitas tangki harus lebih dari 0 liter.");
        $this->kapasitas_tangki = $liter;
    }

    public function setKonsumsibbm(string $konsumsi): void
    {
        $this->konsumsi_bbm = trim($konsumsi);
    }
}
