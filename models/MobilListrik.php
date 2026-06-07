<?php

/**
 * ============================================================
 *  Class   : MobilListrik
 *  Extends : Kendaraan (Abstract Class)
 *  Proyek  : Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan
 *  Tabel   : tb_mobil_listrik (JOIN tb_kendaraan)
 *
 *  Pilar OOP yang diterapkan:
 *    ✔ Inheritance   — Mewarisi semua atribut & method dari Kendaraan
 *    ✔ Polymorphism  — Override hitungPajakTahunan() & tampilkanSpesifikasi()
 *    ✔ Encapsulation — Atribut unik dideklarasikan protected
 * ============================================================
 */

require_once __DIR__ . '/Kendaraan.php';

class MobilListrik extends Kendaraan
{
    // =========================================================
    // ATRIBUT UNIK (dipetakan ke kolom tb_mobil_listrik)
    // =========================================================

    /** @var float Kolom: kapasitas_baterai — DECIMAL(5,2), dalam kWh */
    protected float $kapasitas_baterai;

    /** @var int   Kolom: daya_motor_listrik — dalam HP */
    protected int $daya_motor_listrik;

    /** @var int   Kolom: waktu_pengisian — dalam menit (fast charging) */
    protected int $waktu_pengisian;

    /** @var int   Kolom: jarak_tempuh — jangkauan per full charge, dalam km */
    protected int $jarak_tempuh;

    /** @var int   Kolom: kecepatan_maksimum — top speed, dalam km/h */
    protected int $kecepatan_maksimum;

    // =========================================================
    // CONSTRUCTOR
    // =========================================================

    /**
     * Konstruktor MobilListrik.
     * Memanggil parent::__construct() untuk data umum kendaraan,
     * lalu menginisialisasi seluruh atribut spesifik kendaraan listrik.
     *
     * @param int|null $id_kendaraan
     * @param string   $brand
     * @param string   $model
     * @param int      $tahun
     * @param int      $stok
     * @param float    $harga_dasar
     * @param string   $transmisi           'AT' (EV umumnya single-speed)
     * @param int      $jumlah_kursi
     * @param float    $kapasitas_baterai   Kapasitas baterai (kWh)
     * @param int      $daya_motor_listrik  Daya motor (HP)
     * @param int      $waktu_pengisian     Waktu pengisian cepat (menit)
     * @param int      $jarak_tempuh        Jarak tempuh per charge penuh (km)
     * @param int      $kecepatan_maksimum  Kecepatan puncak (km/h)
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
        float  $kapasitas_baterai,
        int    $daya_motor_listrik,
        int    $waktu_pengisian,
        int    $jarak_tempuh,
        int    $kecepatan_maksimum
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

        // Inisialisasi atribut spesifik EV
        $this->kapasitas_baterai  = $kapasitas_baterai;
        $this->daya_motor_listrik = $daya_motor_listrik;
        $this->waktu_pengisian    = $waktu_pengisian;
        $this->jarak_tempuh       = $jarak_tempuh;
        $this->kecepatan_maksimum = $kecepatan_maksimum;
    }

    // =========================================================
    // ABSTRACT METHOD IMPLEMENTATION (Polymorphism - Overriding)
    // =========================================================

    /**
     * Menghitung pajak tahunan mobil listrik.
     *
     * Rumus: 0.5% × harga_dasar
     *
     * Tarif paling rendah sebagai bentuk dukungan pemerintah
     * terhadap adopsi kendaraan zero-emission.
     *
     * Contoh (Hyundai Ioniq 5, Rp 780.000.000):
     *   = 0.005 × 780.000.000
     *   = Rp 3.900.000
     *
     * @return float Pajak tahunan dalam rupiah
     */
    public function hitungPajakTahunan(): float
    {
        return 0.005 * $this->harga_dasar;
    }

    /**
     * Menampilkan seluruh spesifikasi mobil listrik ke output.
     *
     * @return void
     */
    public function tampilkanSpesifikasi(): void
    {
        $pajak = $this->hitungPajakTahunan();

        echo "╔══════════════════════════════════════════════════╗" . PHP_EOL;
        echo "║            SPESIFIKASI MOBIL LISTRIK             ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ ID Kendaraan      : " . str_pad($this->id_kendaraan ?? '-', 28) . " ║" . PHP_EOL;
        echo "║ Brand             : " . str_pad($this->brand, 28) . " ║" . PHP_EOL;
        echo "║ Model             : " . str_pad($this->model, 28) . " ║" . PHP_EOL;
        echo "║ Tahun             : " . str_pad($this->tahun, 28) . " ║" . PHP_EOL;
        echo "║ Transmisi         : " . str_pad($this->transmisi, 28) . " ║" . PHP_EOL;
        echo "║ Jumlah Kursi      : " . str_pad($this->jumlah_kursi . ' kursi', 28) . " ║" . PHP_EOL;
        echo "║ Stok              : " . str_pad($this->stok . ' unit', 28) . " ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Kapasitas Baterai : " . str_pad($this->kapasitas_baterai . ' kWh', 28) . " ║" . PHP_EOL;
        echo "║ Daya Motor Lstrik : " . str_pad($this->daya_motor_listrik . ' HP', 28) . " ║" . PHP_EOL;
        echo "║ Waktu Pengisian   : " . str_pad($this->waktu_pengisian . ' menit', 28) . " ║" . PHP_EOL;
        echo "║ Jarak Tempuh      : " . str_pad($this->jarak_tempuh . ' km / charge', 28) . " ║" . PHP_EOL;
        echo "║ Kecepatan Maks.   : " . str_pad($this->kecepatan_maksimum . ' km/h', 28) . " ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Harga Dasar       : " . str_pad($this->formatRupiah($this->harga_dasar), 28) . " ║" . PHP_EOL;
        echo "║ Pajak Tahunan     : " . str_pad($this->formatRupiah($pajak), 28) . " ║" . PHP_EOL;
        echo "╚══════════════════════════════════════════════════╝" . PHP_EOL;
    }

    // =========================================================
    // GETTERS & SETTERS ATRIBUT UNIK
    // =========================================================

    public function getKapasitasBaterai(): float   { return $this->kapasitas_baterai; }
    public function getDayaMotorListrik(): int      { return $this->daya_motor_listrik; }
    public function getWaktuPengisian(): int        { return $this->waktu_pengisian; }
    public function getJarakTempuh(): int           { return $this->jarak_tempuh; }
    public function getKecepatanMaksimum(): int     { return $this->kecepatan_maksimum; }

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

    public function setWaktuPengisian(int $menit): void
    {
        if ($menit <= 0) throw new InvalidArgumentException("Waktu pengisian harus lebih dari 0 menit.");
        $this->waktu_pengisian = $menit;
    }

    public function setJarakTempuh(int $km): void
    {
        if ($km <= 0) throw new InvalidArgumentException("Jarak tempuh harus lebih dari 0 km.");
        $this->jarak_tempuh = $km;
    }

    public function setKecepatanMaksimum(int $kmh): void
    {
        if ($kmh <= 0) throw new InvalidArgumentException("Kecepatan maksimum harus lebih dari 0 km/h.");
        $this->kecepatan_maksimum = $kmh;
    }
}
