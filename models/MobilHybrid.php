<?php

/**
 * ============================================================
 *  Class   : MobilHybrid
 *  Extends : Kendaraan (Abstract Class)
 *  Proyek  : Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan
 *  Tabel   : tb_mobil_hybrid (JOIN tb_kendaraan)
 *
 *  Pilar OOP yang diterapkan:
 *    ✔ Inheritance   — Mewarisi semua atribut & method dari Kendaraan
 *    ✔ Polymorphism  — Override hitungPajakTahunan() & tampilkanSpesifikasi()
 *    ✔ Encapsulation — Atribut unik dideklarasikan protected
 * ============================================================
 */

require_once __DIR__ . '/Kendaraan.php';

class MobilHybrid extends Kendaraan
{
    // =========================================================
    // ATRIBUT UNIK (dipetakan ke kolom tb_mobil_hybrid)
    // =========================================================

    /** @var int    Kolom: kapasitas_mesin — dalam cc */
    protected int $kapasitas_mesin;

    /** @var string Kolom: jenis_bahan_bakar — cth: 'Bensin' */
    protected string $jenis_bahan_bakar;

    /** @var float  Kolom: kapasitas_tangki — DECIMAL(5,2), liter */
    protected float $kapasitas_tangki;

    /** @var float  Kolom: kapasitas_baterai — DECIMAL(5,2), kWh */
    protected float $kapasitas_baterai;

    /** @var int    Kolom: daya_motor_listrik — dalam HP */
    protected int $daya_motor_listrik;

    /** @var string Kolom: tipe_hybrid — cth: 'HEV (Full Hybrid)', 'Series Hybrid' */
    protected string $tipe_hybrid;

    /** @var string Kolom: mode_berkendara — cth: 'EV, Eco, Normal, Power' */
    protected string $mode_berkendara;

    /** @var string Kolom: konsumsi_bbm — cth: '1:21 km/l' */
    protected string $konsumsi_bbm;

    // =========================================================
    // CONSTRUCTOR
    // =========================================================

    /**
     * Konstruktor MobilHybrid.
     * Memanggil parent::__construct() untuk data umum kendaraan,
     * lalu menginisialisasi seluruh atribut spesifik hybrid.
     *
     * @param int|null $id_kendaraan
     * @param string   $brand
     * @param string   $model
     * @param int      $tahun
     * @param int      $stok
     * @param float    $harga_dasar
     * @param string   $transmisi          'Manual' | 'AT' | 'CVT'
     * @param int      $jumlah_kursi
     * @param int      $kapasitas_mesin    Kapasitas mesin combustion (cc)
     * @param string   $jenis_bahan_bakar  Jenis BBM (cth: 'Bensin')
     * @param float    $kapasitas_tangki   Volume tangki BBM (liter)
     * @param float    $kapasitas_baterai  Kapasitas baterai (kWh)
     * @param int      $daya_motor_listrik Daya motor listrik (HP)
     * @param string   $tipe_hybrid        Tipe sistem hybrid
     * @param string   $mode_berkendara    Mode berkendara yang tersedia
     * @param string   $konsumsi_bbm       Efisiensi BBM (cth: '1:21 km/l')
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
        float  $kapasitas_baterai,
        int    $daya_motor_listrik,
        string $tipe_hybrid,
        string $mode_berkendara,
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

        // Inisialisasi atribut spesifik hybrid
        $this->kapasitas_mesin   = $kapasitas_mesin;
        $this->jenis_bahan_bakar = $jenis_bahan_bakar;
        $this->kapasitas_tangki  = $kapasitas_tangki;
        $this->kapasitas_baterai  = $kapasitas_baterai;
        $this->daya_motor_listrik = $daya_motor_listrik;
        $this->tipe_hybrid        = $tipe_hybrid;
        $this->mode_berkendara    = $mode_berkendara;
        $this->konsumsi_bbm       = $konsumsi_bbm;
    }

    // =========================================================
    // ABSTRACT METHOD IMPLEMENTATION (Polymorphism - Overriding)
    // =========================================================

    /**
     * Menghitung pajak tahunan mobil hybrid.
     *
     * Rumus: (1% × harga_dasar) + (kapasitas_mesin × 250)
     *
     * Tarif pajak hybrid lebih rendah dari konvensional sebagai insentif
     * kendaraan ramah lingkungan.
     *
     * Contoh (Toyota Innova Zenix 2000cc, Rp 540.000.000):
     *   = (0.01 × 540.000.000) + (2000 × 250)
     *   = 5.400.000 + 500.000
     *   = Rp 5.900.000
     *
     * @return float Pajak tahunan dalam rupiah
     */
    public function hitungPajakTahunan(): float
    {
        return (0.01 * $this->harga_dasar) + ($this->kapasitas_mesin * 250);
    }

    /**
     * Menampilkan seluruh spesifikasi mobil hybrid ke output.
     *
     * @return void
     */
    public function tampilkanSpesifikasi(): void
    {
        $pajak = $this->hitungPajakTahunan();

        echo "╔══════════════════════════════════════════════════╗" . PHP_EOL;
        echo "║            SPESIFIKASI MOBIL HYBRID              ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ ID Kendaraan     : " . str_pad($this->id_kendaraan ?? '-', 29) . " ║" . PHP_EOL;
        echo "║ Brand            : " . str_pad($this->brand, 29) . " ║" . PHP_EOL;
        echo "║ Model            : " . str_pad($this->model, 29) . " ║" . PHP_EOL;
        echo "║ Tahun            : " . str_pad($this->tahun, 29) . " ║" . PHP_EOL;
        echo "║ Transmisi        : " . str_pad($this->transmisi, 29) . " ║" . PHP_EOL;
        echo "║ Jumlah Kursi     : " . str_pad($this->jumlah_kursi . ' kursi', 29) . " ║" . PHP_EOL;
        echo "║ Stok             : " . str_pad($this->stok . ' unit', 29) . " ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Kapasitas Mesin  : " . str_pad($this->kapasitas_mesin . ' cc', 29) . " ║" . PHP_EOL;
        echo "║ Bahan Bakar      : " . str_pad($this->jenis_bahan_bakar, 29) . " ║" . PHP_EOL;
        echo "║ Kapasitas Tangki : " . str_pad($this->kapasitas_tangki . ' liter', 29) . " ║" . PHP_EOL;
        echo "║ Kapasitas Baterai: " . str_pad($this->kapasitas_baterai . ' kWh', 29) . " ║" . PHP_EOL;
        echo "║ Daya Motor Lstrik: " . str_pad($this->daya_motor_listrik . ' HP', 29) . " ║" . PHP_EOL;
        echo "║ Tipe Hybrid      : " . str_pad($this->tipe_hybrid, 29) . " ║" . PHP_EOL;
        echo "║ Mode Berkendara  : " . str_pad($this->mode_berkendara, 29) . " ║" . PHP_EOL;
        echo "║ Konsumsi BBM     : " . str_pad($this->konsumsi_bbm, 29) . " ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Harga Dasar      : " . str_pad($this->formatRupiah($this->harga_dasar), 29) . " ║" . PHP_EOL;
        echo "║ Pajak Tahunan    : " . str_pad($this->formatRupiah($pajak), 29) . " ║" . PHP_EOL;
        echo "╚══════════════════════════════════════════════════╝" . PHP_EOL;
    }

    // =========================================================
    // GETTERS & SETTERS ATRIBUT UNIK
    // =========================================================

    public function getKapasitasMesin(): int      { return $this->kapasitas_mesin; }
    public function getJenisBahanBakar(): string   { return $this->jenis_bahan_bakar; }
    public function getKapasitasTangki(): float    { return $this->kapasitas_tangki; }
    public function getKapasitasBaterai(): float   { return $this->kapasitas_baterai; }
    public function getDayaMotorListrik(): int     { return $this->daya_motor_listrik; }
    public function getTipeHybrid(): string        { return $this->tipe_hybrid; }
    public function getModeBerkendara(): string    { return $this->mode_berkendara; }
    public function getKonsumsibbm(): string       { return $this->konsumsi_bbm; }

    public function setKapasitasMesin(int $cc): void
    {
        if ($cc <= 0) throw new InvalidArgumentException("Kapasitas mesin harus lebih dari 0 cc.");
        $this->kapasitas_mesin = $cc;
    }

    public function setJenisBahanBakar(string $bbm): void   { $this->jenis_bahan_bakar  = trim($bbm); }
    public function setTipeHybrid(string $tipe): void        { $this->tipe_hybrid         = trim($tipe); }
    public function setModeBerkendara(string $mode): void    { $this->mode_berkendara     = trim($mode); }
    public function setKonsumsibbm(string $konsumsi): void   { $this->konsumsi_bbm        = trim($konsumsi); }

    public function setKapasitasTangki(float $liter): void
    {
        if ($liter <= 0) throw new InvalidArgumentException("Kapasitas tangki harus lebih dari 0 liter.");
        $this->kapasitas_tangki = $liter;
    }

    public function setKapasitasBaterai(float $kwh): void
    {
        if ($kwh <= 0) throw new InvalidArgumentException("Kapasitas baterai harus lebih dari 0 kWh.");
        $this->kapasitas_baterai = $kwh;
    }

    public function setDayaMotorListrik(int $hp): void
    {
        if ($hp <= 0) throw new InvalidArgumentException("Daya motor listrik harus lebih dari 0 HP.");
        $this->daya_motor_listrik = $hp;
    }
}
