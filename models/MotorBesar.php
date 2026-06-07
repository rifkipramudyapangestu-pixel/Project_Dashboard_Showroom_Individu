<?php

/**
 * ============================================================
 *  Class   : MotorBesar
 *  Extends : Kendaraan (Abstract Class)
 *  Proyek  : Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan
 *  Tabel   : tb_motor_besar (JOIN tb_kendaraan)
 *
 *  Pilar OOP yang diterapkan:
 *    ✔ Inheritance   — Mewarisi semua atribut & method dari Kendaraan
 *    ✔ Polymorphism  — Override hitungPajakTahunan() & tampilkanSpesifikasi()
 *    ✔ Encapsulation — Atribut unik dideklarasikan protected
 * ============================================================
 */

require_once __DIR__ . '/Kendaraan.php';

class MotorBesar extends Kendaraan
{
    // =========================================================
    // ATRIBUT UNIK (dipetakan ke kolom tb_motor_besar)
    // =========================================================

    /** @var string Kolom: jenis_bahan_bakar — cth: 'Bensin' */
    protected string $jenis_bahan_bakar;

    /** @var int    Kolom: kapasitas_mesin — dalam cc */
    protected int $kapasitas_mesin;

    /** @var int    Kolom: kapasitas_tangki — dalam liter (INT di SQL) */
    protected int $kapasitas_tangki;

    /** @var string Kolom: konsumsi_bbm — cth: '1:12 km/l' */
    protected string $konsumsi_bbm;

    /** @var string Kolom: tipe_motor — cth: 'Super Sport', 'Adventure / Touring' */
    protected string $tipe_motor;

    // =========================================================
    // CONSTRUCTOR
    // =========================================================

    /**
     * Konstruktor MotorBesar.
     * Memanggil parent::__construct() untuk data umum kendaraan,
     * lalu menginisialisasi seluruh atribut spesifik motor besar.
     *
     * @param int|null $id_kendaraan
     * @param string   $brand
     * @param string   $model
     * @param int      $tahun
     * @param int      $stok
     * @param float    $harga_dasar
     * @param string   $transmisi          'Manual' (motor besar umumnya manual)
     * @param int      $jumlah_kursi       1 atau 2 (tandem)
     * @param string   $jenis_bahan_bakar  Jenis bahan bakar (cth: 'Bensin')
     * @param int      $kapasitas_mesin    Kapasitas mesin (cc)
     * @param int      $kapasitas_tangki   Volume tangki (liter)
     * @param string   $konsumsi_bbm       Konsumsi BBM (cth: '1:12 km/l')
     * @param string   $tipe_motor         Tipe motor (cth: 'Super Sport')
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
        string $jenis_bahan_bakar,
        int    $kapasitas_mesin,
        int    $kapasitas_tangki,
        string $konsumsi_bbm,
        string $tipe_motor
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

        // Inisialisasi atribut spesifik motor besar
        $this->jenis_bahan_bakar = $jenis_bahan_bakar;
        $this->kapasitas_mesin   = $kapasitas_mesin;
        $this->kapasitas_tangki  = $kapasitas_tangki;
        $this->konsumsi_bbm      = $konsumsi_bbm;
        $this->tipe_motor        = $tipe_motor;
    }

    // =========================================================
    // ABSTRACT METHOD IMPLEMENTATION (Polymorphism - Overriding)
    // =========================================================

    /**
     * Menghitung pajak tahunan motor besar.
     *
     * Rumus: 1.5% × harga_dasar
     *
     * Motor besar dikenakan tarif lebih tinggi dari mobil listrik
     * namun lebih rendah dari mobil konvensional.
     *
     * Contoh (Honda CBR1000RR-R, Rp 1.050.000.000):
     *   = 0.015 × 1.050.000.000
     *   = Rp 15.750.000
     *
     * @return float Pajak tahunan dalam rupiah
     */
    public function hitungPajakTahunan(): float
    {
        return 0.015 * $this->harga_dasar;
    }

    /**
     * Menampilkan seluruh spesifikasi motor besar ke output.
     *
     * @return void
     */
    public function tampilkanSpesifikasi(): void
    {
        $pajak = $this->hitungPajakTahunan();

        echo "╔══════════════════════════════════════════════════╗" . PHP_EOL;
        echo "║             SPESIFIKASI MOTOR BESAR              ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ ID Kendaraan     : " . str_pad($this->id_kendaraan ?? '-', 29) . " ║" . PHP_EOL;
        echo "║ Brand            : " . str_pad($this->brand, 29) . " ║" . PHP_EOL;
        echo "║ Model            : " . str_pad($this->model, 29) . " ║" . PHP_EOL;
        echo "║ Tahun            : " . str_pad($this->tahun, 29) . " ║" . PHP_EOL;
        echo "║ Transmisi        : " . str_pad($this->transmisi, 29) . " ║" . PHP_EOL;
        echo "║ Kapasitas Kursi  : " . str_pad($this->jumlah_kursi . ' kursi', 29) . " ║" . PHP_EOL;
        echo "║ Stok             : " . str_pad($this->stok . ' unit', 29) . " ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Tipe Motor       : " . str_pad($this->tipe_motor, 29) . " ║" . PHP_EOL;
        echo "║ Kapasitas Mesin  : " . str_pad($this->kapasitas_mesin . ' cc', 29) . " ║" . PHP_EOL;
        echo "║ Bahan Bakar      : " . str_pad($this->jenis_bahan_bakar, 29) . " ║" . PHP_EOL;
        echo "║ Kapasitas Tangki : " . str_pad($this->kapasitas_tangki . ' liter', 29) . " ║" . PHP_EOL;
        echo "║ Konsumsi BBM     : " . str_pad($this->konsumsi_bbm, 29) . " ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Harga Dasar      : " . str_pad($this->formatRupiah($this->harga_dasar), 29) . " ║" . PHP_EOL;
        echo "║ Pajak Tahunan    : " . str_pad($this->formatRupiah($pajak), 29) . " ║" . PHP_EOL;
        echo "╚══════════════════════════════════════════════════╝" . PHP_EOL;
    }

    // =========================================================
    // GETTERS & SETTERS ATRIBUT UNIK
    // =========================================================

    public function getJenisBahanBakar(): string  { return $this->jenis_bahan_bakar; }
    public function getKapasitasMesin(): int       { return $this->kapasitas_mesin; }
    public function getKapasitasTangki(): int      { return $this->kapasitas_tangki; }
    public function getKonsumsibbm(): string       { return $this->konsumsi_bbm; }
    public function getTipeMotor(): string         { return $this->tipe_motor; }

    public function setJenisBahanBakar(string $bbm): void  { $this->jenis_bahan_bakar = trim($bbm); }
    public function setKonsumsibbm(string $konsumsi): void { $this->konsumsi_bbm = trim($konsumsi); }
    public function setTipeMotor(string $tipe): void       { $this->tipe_motor = trim($tipe); }

    public function setKapasitasMesin(int $cc): void
    {
        if ($cc <= 0) throw new InvalidArgumentException("Kapasitas mesin harus lebih dari 0 cc.");
        $this->kapasitas_mesin = $cc;
    }

    public function setKapasitasTangki(int $liter): void
    {
        if ($liter <= 0) throw new InvalidArgumentException("Kapasitas tangki harus lebih dari 0 liter.");
        $this->kapasitas_tangki = $liter;
    }
}
