<?php

/**
 * ============================================================
 *  Class   : ManajemenShowroom
 *  Proyek  : Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan
 *  Peran   : Controller terpusat (koordinator antara Database & Model)
 *
 *  Tanggung jawab utama:
 *    1. Menarik seluruh data dari 5 tabel melalui LEFT JOIN tunggal
 *    2. Mengidentifikasi jenis kendaraan dari kolom yang terisi
 *    3. Menginstansiasi objek subkelas yang tepat (Factory Pattern)
 *    4. Menyimpan semua objek dalam Polymorphic Collection (array of Kendaraan)
 *    5. Menyediakan method laporan yang memanfaatkan Dynamic Binding
 *
 *  Pilar OOP yang diterapkan:
 *    ✔ Polymorphism (Dynamic Binding) — cetakLaporanInventaris() memanggil
 *      tampilkanSpesifikasi() & hitungPajakTahunan() tanpa tahu tipe
 *      konkret objek saat compile-time; PHP memutuskannya saat runtime.
 *    ✔ Encapsulation — properti $db dan $koleksiKendaraan bersifat private
 *    ✔ Abstraction   — detail JOIN & instansiasi disembunyikan di private method
 * ============================================================
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/MobilKonvensional.php';
require_once __DIR__ . '/../models/MobilHybrid.php';
require_once __DIR__ . '/../models/MobilListrik.php';
require_once __DIR__ . '/../models/MotorBesar.php';

class ManajemenShowroom
{
    // =========================================================
    // PROPERTI PRIVATE (Encapsulation)
    // =========================================================

    /**
     * Instance kelas Database yang mengelola koneksi PDO.
     * @var Database
     */
    private Database $db;

    /**
     * Polymorphic Collection — array yang menampung objek-objek bertipe
     * Kendaraan (atau subkelas turunannya).
     * Karena semua elemen dijamin merupakan instance dari Kendaraan,
     * kita bisa memanggil method manapun yang didefinisikan di Kendaraan
     * tanpa casting, dan PHP akan menjalankan versi yang tepat (Dynamic Binding).
     *
     * @var Kendaraan[]
     */
    private array $koleksiKendaraan = [];

    // =========================================================
    // CONSTRUCTOR
    // =========================================================

    /**
     * Konstruktor ManajemenShowroom.
     * Menerima objek Database dari luar (Dependency Injection) agar
     * kelas ini tidak bergantung langsung pada implementasi koneksi —
     * lebih mudah diuji dan diganti.
     *
     * Kontruksi otomatis memuat seluruh data kendaraan dari database
     * ke dalam koleksi polimorfik saat objek pertama dibuat.
     *
     * @param Database $database Objek Database yang sudah terhubung ke MySQL
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->muatSemuaKendaraan(); // Isi koleksi otomatis saat objek dibuat
    }

    // =========================================================
    // PRIVATE METHOD — Detail implementasi tersembunyi dari luar
    // =========================================================

    /**
     * Menjalankan LEFT JOIN tunggal ke semua 5 tabel dan mengisi
     * $koleksiKendaraan dengan objek subkelas yang sesuai.
     *
     * Strategi JOIN:
     *   - tb_kendaraan sebagai tabel induk (kiri / driving table)
     *   - 4 tabel anak di-LEFT JOIN agar setiap baris tetap muncul
     *     meskipun hanya cocok di satu tabel anak
     *   - Jenis kendaraan dideteksi dari kolom mana yang NOT NULL
     *     (cth: jika mk.id_kendaraan IS NOT NULL → MobilKonvensional)
     *
     * @return void
     */
    private function muatSemuaKendaraan(): void
    {
        $pdo = $this->db->getConnection();

        // ---------------------------------------------------------
        // Query LEFT JOIN tunggal — menarik semua kolom dari 5 tabel
        // Prefix alias: k=kendaraan, mk=konvensional, mh=hybrid,
        //               ml=listrik, mb=motor_besar
        // ---------------------------------------------------------
        $sql = "
            SELECT
                -- ── Kolom induk (tb_kendaraan) ──────────────────────────
                k.id_kendaraan,
                k.brand,
                k.model,
                k.tahun,
                k.stok,
                k.harga_dasar,
                k.transmisi,
                k.jumlah_kursi,

                -- ── Penanda jenis (NULL = bukan tipe ini) ───────────────
                mk.id_kendaraan  AS mk_id,
                mh.id_kendaraan  AS mh_id,
                ml.id_kendaraan  AS ml_id,
                mb.id_kendaraan  AS mb_id,

                -- ── Kolom MobilKonvensional (tb_mobil_konvensional) ──────
                mk.kapasitas_mesin       AS mk_kapasitas_mesin,
                mk.jenis_bahan_bakar     AS mk_jenis_bahan_bakar,
                mk.kapasitas_tangki      AS mk_kapasitas_tangki,
                mk.konsumsi_bbm          AS mk_konsumsi_bbm,

                -- ── Kolom MobilHybrid (tb_mobil_hybrid) ──────────────────
                mh.kapasitas_mesin       AS mh_kapasitas_mesin,
                mh.jenis_bahan_bakar     AS mh_jenis_bahan_bakar,
                mh.kapasitas_tangki      AS mh_kapasitas_tangki,
                mh.kapasitas_baterai     AS mh_kapasitas_baterai,
                mh.daya_motor_listrik    AS mh_daya_motor_listrik,
                mh.tipe_hybrid           AS mh_tipe_hybrid,
                mh.mode_berkendara       AS mh_mode_berkendara,
                mh.konsumsi_bbm          AS mh_konsumsi_bbm,

                -- ── Kolom MobilListrik (tb_mobil_listrik) ─────────────────
                ml.kapasitas_baterai     AS ml_kapasitas_baterai,
                ml.daya_motor_listrik    AS ml_daya_motor_listrik,
                ml.waktu_pengisian       AS ml_waktu_pengisian,
                ml.jarak_tempuh          AS ml_jarak_tempuh,
                ml.kecepatan_maksimum    AS ml_kecepatan_maksimum,

                -- ── Kolom MotorBesar (tb_motor_besar) ────────────────────
                mb.jenis_bahan_bakar     AS mb_jenis_bahan_bakar,
                mb.kapasitas_mesin       AS mb_kapasitas_mesin,
                mb.kapasitas_tangki      AS mb_kapasitas_tangki,
                mb.konsumsi_bbm          AS mb_konsumsi_bbm,
                mb.tipe_motor            AS mb_tipe_motor

            FROM       tb_kendaraan           AS k
            LEFT JOIN  tb_mobil_konvensional  AS mk  ON k.id_kendaraan = mk.id_kendaraan
            LEFT JOIN  tb_mobil_hybrid        AS mh  ON k.id_kendaraan = mh.id_kendaraan
            LEFT JOIN  tb_mobil_listrik       AS ml  ON k.id_kendaraan = ml.id_kendaraan
            LEFT JOIN  tb_motor_besar         AS mb  ON k.id_kendaraan = mb.id_kendaraan
            ORDER BY   k.id_kendaraan ASC
        ";

        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            $objek = $this->instansiasiKendaraan($row);
            if ($objek !== null) {
                $this->koleksiKendaraan[] = $objek;
            }
        }
    }

    /**
     * Factory Method — menentukan dan menginstansiasi subkelas yang tepat
     * berdasarkan kolom penanda yang NOT NULL dari hasil JOIN.
     *
     * Urutan pengecekan:
     *   1. mk_id NOT NULL → MobilKonvensional
     *   2. mh_id NOT NULL → MobilHybrid
     *   3. ml_id NOT NULL → MobilListrik
     *   4. mb_id NOT NULL → MotorBesar
     *
     * @param  array       $row Satu baris hasil fetchAll() dari query JOIN
     * @return Kendaraan|null   Objek subkelas yang sesuai, atau null jika tidak dikenali
     */
    private function instansiasiKendaraan(array $row): ?Kendaraan
    {
        // Data umum — selalu ada (dari tb_kendaraan)
        $id          = (int)   $row['id_kendaraan'];
        $brand       =         $row['brand'];
        $model       =         $row['model'];
        $tahun       = (int)   $row['tahun'];
        $stok        = (int)   $row['stok'];
        $harga       = (float) $row['harga_dasar'];
        $transmisi   =         $row['transmisi'];
        $kursi       = (int)   $row['jumlah_kursi'];

        // ── Kasus 1: MobilKonvensional ────────────────────────────────
        if (!is_null($row['mk_id'])) {
            return new MobilKonvensional(
                $id, $brand, $model, $tahun, $stok, $harga, $transmisi, $kursi,
                (int)   $row['mk_kapasitas_mesin'],
                        $row['mk_jenis_bahan_bakar'],
                (float) $row['mk_kapasitas_tangki'],
                        $row['mk_konsumsi_bbm']
            );
        }

        // ── Kasus 2: MobilHybrid ──────────────────────────────────────
        if (!is_null($row['mh_id'])) {
            return new MobilHybrid(
                $id, $brand, $model, $tahun, $stok, $harga, $transmisi, $kursi,
                (int)   $row['mh_kapasitas_mesin'],
                        $row['mh_jenis_bahan_bakar'],
                (float) $row['mh_kapasitas_tangki'],
                (float) $row['mh_kapasitas_baterai'],
                (int)   $row['mh_daya_motor_listrik'],
                        $row['mh_tipe_hybrid'],
                        $row['mh_mode_berkendara'],
                        $row['mh_konsumsi_bbm']
            );
        }

        // ── Kasus 3: MobilListrik ─────────────────────────────────────
        if (!is_null($row['ml_id'])) {
            return new MobilListrik(
                $id, $brand, $model, $tahun, $stok, $harga, $transmisi, $kursi,
                (float) $row['ml_kapasitas_baterai'],
                (int)   $row['ml_daya_motor_listrik'],
                (int)   $row['ml_waktu_pengisian'],
                (int)   $row['ml_jarak_tempuh'],
                (int)   $row['ml_kecepatan_maksimum']
            );
        }

        // ── Kasus 4: MotorBesar ───────────────────────────────────────
        if (!is_null($row['mb_id'])) {
            return new MotorBesar(
                $id, $brand, $model, $tahun, $stok, $harga, $transmisi, $kursi,
                        $row['mb_jenis_bahan_bakar'],
                (int)   $row['mb_kapasitas_mesin'],
                (int)   $row['mb_kapasitas_tangki'],
                        $row['mb_konsumsi_bbm'],
                        $row['mb_tipe_motor']
            );
        }

        // Jika id_kendaraan tidak cocok dengan tabel manapun
        return null;
    }

    // =========================================================
    // PUBLIC METHODS — Antarmuka yang tersedia untuk pengguna kelas
    // =========================================================

    /**
     * Mencetak Laporan Inventaris lengkap semua kendaraan.
     *
     * Ini adalah demonstrasi Polymorphism + Dynamic Binding:
     * - Loop berjalan terhadap $koleksiKendaraan (array of Kendaraan)
     * - tampilkanSpesifikasi() dipanggil pada setiap elemen
     * - PHP runtime secara otomatis memilih implementasi yang benar:
     *     → MobilKonvensional::tampilkanSpesifikasi() untuk mobil konvensional
     *     → MobilHybrid::tampilkanSpesifikasi() untuk hybrid, dst.
     * - Perilaku yang sama juga terjadi pada hitungPajakTahunan()
     *
     * Ini disebut Dynamic Binding / Late Static Binding karena
     * keputusan method mana yang dipanggil terjadi SAAT RUNTIME,
     * bukan saat kode dikompilasi/di-parse.
     *
     * @return void
     */
    public function cetakLaporanInventaris(): void
    {
        $totalKendaraan   = count($this->koleksiKendaraan);
        $totalPajak       = 0.0;
        $totalNilaiStok   = 0.0;

        echo PHP_EOL;
        echo "══════════════════════════════════════════════════════" . PHP_EOL;
        echo "   LAPORAN INVENTARIS SHOWROOM KENDARAAN              " . PHP_EOL;
        echo "   Total Kendaraan Terdaftar : {$totalKendaraan} unit " . PHP_EOL;
        echo "══════════════════════════════════════════════════════" . PHP_EOL;

        // ── Dynamic Binding terjadi di sini ──────────────────────────
        // Tipe variabel $kendaraan adalah Kendaraan (abstract),
        // namun PHP memanggil implementasi subkelas yang sesungguhnya.
        foreach ($this->koleksiKendaraan as $index => $kendaraan) {
            $nomorUrut = $index + 1;
            echo PHP_EOL . "[ Kendaraan #{$nomorUrut} dari {$totalKendaraan} ]" . PHP_EOL;

            // Dynamic Binding → memanggil tampilkanSpesifikasi() versi subkelas yang tepat
            $kendaraan->tampilkanSpesifikasi();

            // Dynamic Binding → memanggil hitungPajakTahunan() versi subkelas yang tepat
            $pajakKendaraan = $kendaraan->hitungPajakTahunan();
            $totalPajak    += $pajakKendaraan;
            $totalNilaiStok += $kendaraan->getHargaDasar() * $kendaraan->getStok();
        }

        // ── Ringkasan Fiskal ──────────────────────────────────────────
        echo PHP_EOL;
        echo "╔══════════════════════════════════════════════════════╗" . PHP_EOL;
        echo "║              RINGKASAN FISKAL SHOWROOM               ║" . PHP_EOL;
        echo "╠══════════════════════════════════════════════════════╣" . PHP_EOL;
        echo "║ Total Kendaraan Terdaftar : " . str_pad($totalKendaraan . ' unit', 24) . " ║" . PHP_EOL;
        echo "║ Total Pajak Tahunan Gabungan: " . str_pad($this->formatRupiah($totalPajak), 22) . " ║" . PHP_EOL;
        echo "║ Total Nilai Stok Showroom   : " . str_pad($this->formatRupiah($totalNilaiStok), 22) . " ║" . PHP_EOL;
        echo "╚══════════════════════════════════════════════════════╝" . PHP_EOL;
    }

    /**
     * Mengembalikan seluruh Polymorphic Collection sebagai array.
     * Berguna jika kode lain ingin mengiterasi koleksi secara mandiri.
     *
     * @return Kendaraan[]
     */
    public function getKoleksiKendaraan(): array
    {
        return $this->koleksiKendaraan;
    }

    /**
     * Mencari dan mengembalikan objek Kendaraan berdasarkan ID.
     *
     * @param  int          $id ID kendaraan yang dicari
     * @return Kendaraan|null   Objek yang ditemukan, atau null jika tidak ada
     */
    public function cariKendaraanById(int $id): ?Kendaraan
    {
        foreach ($this->koleksiKendaraan as $kendaraan) {
            if ($kendaraan->getIdKendaraan() === $id) {
                return $kendaraan;
            }
        }
        return null;
    }

    /**
     * Mengembalikan hanya kendaraan dengan jenis tertentu.
     * Memanfaatkan instanceof untuk menyaring koleksi polimorfik.
     *
     * Contoh penggunaan:
     *   $semuaEV = $showroom->filterJenis(MobilListrik::class);
     *
     * @param  string      $namaKelas Nama kelas subkelas (cth: 'MobilListrik')
     * @return Kendaraan[] Array objek yang cocok
     */
    public function filterJenis(string $namaKelas): array
    {
        return array_filter(
            $this->koleksiKendaraan,
            fn(Kendaraan $k) => $k instanceof $namaKelas
        );
    }

    /**
     * Mencetak ringkasan satu kendaraan berdasarkan ID tanpa mencetak
     * seluruh laporan. Berguna untuk tampilan detail per item.
     *
     * @param  int  $id ID kendaraan yang ingin dicetak detailnya
     * @return void
     */
    public function cetakDetailKendaraan(int $id): void
    {
        $kendaraan = $this->cariKendaraanById($id);

        if ($kendaraan === null) {
            echo "❌ Kendaraan dengan ID {$id} tidak ditemukan." . PHP_EOL;
            return;
        }

        // Dynamic Binding — PHP memilih implementasi yang tepat saat runtime
        $kendaraan->tampilkanSpesifikasi();
    }

    // =========================================================
    // PRIVATE HELPER
    // =========================================================

    /**
     * Memformat angka ke format Rupiah.
     * Duplikat dari Kendaraan::formatRupiah() karena ManajemenShowroom
     * bukan subkelas Kendaraan, namun memerlukan format yang sama.
     *
     * @param  float  $nominal
     * @return string cth: "Rp 52.000.000,00"
     */
    private function formatRupiah(float $nominal): string
    {
        return 'Rp ' . number_format($nominal, 2, ',', '.');
    }

    // =========================================================
    // PUBLIC HELPER UNTUK VIEW / HTML INTERFACE
    // =========================================================

    /**
     * Mengembalikan label jenis kategori kendaraan sebagai string.
     * Memanfaatkan instanceof (Dynamic Binding) untuk mendeteksi tipe objek.
     *
     * @param  Kendaraan $kendaraan
     * @return string    cth: 'Mobil Konvensional', 'Mobil Hybrid', dll.
     */
    public function getJenisKategori(Kendaraan $kendaraan): string
    {
        if ($kendaraan instanceof MobilKonvensional) return 'Mobil Konvensional';
        if ($kendaraan instanceof MobilHybrid)       return 'Mobil Hybrid';
        if ($kendaraan instanceof MobilListrik)      return 'Mobil Listrik';
        if ($kendaraan instanceof MotorBesar)        return 'Motor Besar';
        return 'Tidak Diketahui';
    }

    /**
     * Mengembalikan spesifikasi unik kendaraan sebagai array asosiatif
     * yang siap ditampilkan di tabel HTML.
     *
     * Setiap subkelas mengembalikan key-value yang berbeda sesuai
     * atribut khasnya (Polymorphism pada level data).
     *
     * @param  Kendaraan  $kendaraan
     * @return array<string, string>  ['Label' => 'Nilai', ...]
     */
    public function getSpesifikasiKhusus(Kendaraan $kendaraan): array
    {
        if ($kendaraan instanceof MobilKonvensional) {
            return [
                'Kapasitas Mesin'  => $kendaraan->getKapasitasMesin() . ' cc',
                'Bahan Bakar'      => $kendaraan->getJenisBahanBakar(),
                'Kapasitas Tangki' => $kendaraan->getKapasitasTangki() . ' L',
                'Konsumsi BBM'     => $kendaraan->getKonsumsibbm(),
            ];
        }

        if ($kendaraan instanceof MobilHybrid) {
            return [
                'Kapasitas Mesin'   => $kendaraan->getKapasitasMesin() . ' cc',
                'Kapasitas Baterai' => $kendaraan->getKapasitasBaterai() . ' kWh',
                'Daya Motor Listrik'=> $kendaraan->getDayaMotorListrik() . ' HP',
                'Tipe Hybrid'       => $kendaraan->getTipeHybrid(),
                'Mode Berkendara'   => $kendaraan->getModeBerkendara(),
                'Konsumsi BBM'      => $kendaraan->getKonsumsibbm(),
            ];
        }

        if ($kendaraan instanceof MobilListrik) {
            return [
                'Kapasitas Baterai'  => $kendaraan->getKapasitasBaterai() . ' kWh',
                'Daya Motor'         => $kendaraan->getDayaMotorListrik() . ' HP',
                'Jarak Tempuh'       => $kendaraan->getJarakTempuh() . ' km',
                'Kecepatan Maks.'    => $kendaraan->getKecepatanMaksimum() . ' km/h',
                'Waktu Pengisian'    => $kendaraan->getWaktuPengisian() . ' menit',
            ];
        }

        if ($kendaraan instanceof MotorBesar) {
            return [
                'Tipe Motor'       => $kendaraan->getTipeMotor(),
                'Kapasitas Mesin'  => $kendaraan->getKapasitasMesin() . ' cc',
                'Bahan Bakar'      => $kendaraan->getJenisBahanBakar(),
                'Kapasitas Tangki' => $kendaraan->getKapasitasTangki() . ' L',
                'Konsumsi BBM'     => $kendaraan->getKonsumsibbm(),
            ];
        }

        return [];
    }

    /**
     * Menentukan status pajak kendaraan berdasarkan tahun.
     * Logika simulasi:
     * - Tahun < 2022: EXPIRED
     * - Tahun >= 2022: ACTIVE
     *
     * @param  Kendaraan $kendaraan
     * @return string
     */
    public function getStatusPajak(Kendaraan $kendaraan): string
    {
        return $kendaraan->getTahun() < 2022 ? 'EXPIRED' : 'ACTIVE';
    }

    /**
     * Mengembalikan data ringkasan fiskal showroom sebagai array.
     * Digunakan oleh view untuk menampilkan kartu ringkasan di bagian atas halaman.
     *
     * @return array{
     *   total_unit: int,
     *   total_pajak: float,
     *   total_nilai_stok: float,
     *   per_kategori: array<string, array{unit: int, pajak: float}>
     * }
     */
    public function getRingkasanFiskal(): array
    {
        $totalUnit       = 0;
        $totalPajak      = 0.0;
        $totalNilaiStok  = 0.0;
        $perKategori     = [];

        foreach ($this->koleksiKendaraan as $kendaraan) {
            $kategori        = $this->getJenisKategori($kendaraan);
            $pajak           = $kendaraan->hitungPajakTahunan(); // Dynamic Binding
            $nilaiStok       = $kendaraan->getHargaDasar() * $kendaraan->getStok();

            $totalUnit++;
            $totalPajak     += $pajak;
            $totalNilaiStok += $nilaiStok;

            if (!isset($perKategori[$kategori])) {
                $perKategori[$kategori] = ['unit' => 0, 'pajak' => 0.0];
            }
            $perKategori[$kategori]['unit']++;
            $perKategori[$kategori]['pajak'] += $pajak;
        }

        return [
            'total_unit'      => $totalUnit,
            'total_pajak'     => $totalPajak,
            'total_nilai_stok'=> $totalNilaiStok,
            'per_kategori'    => $perKategori,
        ];
    }
}
