<?php
// ============================================================
//  Entry Point & View : index.php
//  Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan
//  Layout: Sidebar Admin Dashboard (SIAKAD-style)
// ============================================================
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/ManajemenShowroom.php';

// ── Inisialisasi data dari database ─────────────────────────
$error      = null;
$showroom   = null;
$ringkasan  = [];
$koleksi    = [];

try {
    $db       = new Database();
    $showroom = new ManajemenShowroom($db);
    $ringkasan = $showroom->getRingkasanFiskal();
    $koleksi   = $showroom->getKoleksiKendaraan();
} catch (PDOException $e) {
    $error = "Koneksi database gagal: " . $e->getMessage();
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}

// ── Page routing ─────────────────────────────────────────────
$page = $_GET['page'] ?? 'dashboard';
$validPages = ['dashboard', 'mobil_konvensional', 'mobil_hybrid', 'mobil_listrik', 'motor_besar'];
if (!in_array($page, $validPages)) {
    $page = 'dashboard';
}

// ── Helper formatting ────────────────────────────────────────
function rupiah(float $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// ── Konfigurasi badge warna per kategori ─────────────────────
$badgeClass = [
    'Mobil Konvensional' => 'badge-konvensional',
    'Mobil Hybrid'       => 'badge-hybrid',
    'Mobil Listrik'      => 'badge-listrik',
    'Motor Besar'        => 'badge-motor',
];
$categoryIcon = [
    'Mobil Konvensional' => '🚗',
    'Mobil Hybrid'       => '⚡🚗',
    'Mobil Listrik'      => '⚡',
    'Motor Besar'        => '🏍️',
];

// ── Page title mapping ───────────────────────────────────────
$pageTitles = [
    'dashboard'          => 'Dashboard',
    'mobil_konvensional' => 'Mobil Konvensional',
    'mobil_hybrid'       => 'Mobil Hybrid',
    'mobil_listrik'      => 'Mobil Listrik',
    'motor_besar'        => 'Motor Besar',
];
$pageTitle = $pageTitles[$page] ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — ShowroomPro Admin</title>
    <meta name="description" content="Sistem Manajemen Inventaris dan Fiskal Showroom Kendaraan berbasis PHP OOP dengan laporan pajak tahunan dinamis.">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ══════════════════════════════════════════════════════════
           BASE & VARIABLES
           ══════════════════════════════════════════════════════════ */
        :root {
            --sidebar-bg:       #1e293b;
            --sidebar-hover:    #334155;
            --sidebar-active:   #0ea5e9;
            --sidebar-active-bg: rgba(14,165,233,0.12);
            --sidebar-width:    270px;
            --sidebar-text:     #94a3b8;
            --sidebar-text-bright: #e2e8f0;
            --content-bg:       #f1f5f9;
            --card-bg:          #ffffff;
            --text-dark:        #212529;
            --text-secondary:   #64748b;
            --border-light:     #e2e8f0;
            --accent-blue:      #0ea5e9;
            --accent-green:     #22c55e;
            --accent-orange:    #f97316;
            --accent-purple:    #a855f7;
            --accent-pink:      #ec4899;
            --konvensional:     #f97316;
            --hybrid:           #eab308;
            --listrik:          #0ea5e9;
            --motor:            #a855f7;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--content-bg);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ══════════════════════════════════════════════════════════
           LAYOUT: Two-Column Flexbox
           ══════════════════════════════════════════════════════════ */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════════════════════════
           SIDEBAR
           ══════════════════════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--sidebar-hover) transparent;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--sidebar-hover); border-radius: 4px; }

        /* ── Sidebar Brand ── */
        .sidebar-brand {
            padding: 1.5rem 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .sidebar-brand:hover { text-decoration: none; }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--accent-blue), #6366f1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(14,165,233,0.3);
        }
        .brand-text {
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.03em;
        }
        .brand-text span {
            color: var(--accent-blue);
        }
        .brand-sub {
            font-size: 0.65rem;
            color: var(--sidebar-text);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 500;
        }

        /* ── Sidebar Nav ── */
        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
        }
        .sidebar-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--sidebar-text);
            padding: 0.75rem 1.5rem 0.5rem;
            opacity: 0.6;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li { position: relative; }

        .sidebar-menu a,
        .sidebar-menu .menu-toggle {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.7rem 1.5rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        .sidebar-menu a:hover,
        .sidebar-menu .menu-toggle:hover {
            color: var(--sidebar-text-bright);
            background: var(--sidebar-hover);
        }
        .sidebar-menu a.active {
            color: var(--accent-blue);
            background: var(--sidebar-active-bg);
            border-right: 3px solid var(--accent-blue);
            font-weight: 600;
        }
        .sidebar-menu a .menu-icon,
        .sidebar-menu .menu-toggle .menu-icon {
            width: 22px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .sidebar-menu a .menu-text,
        .sidebar-menu .menu-toggle .menu-text {
            flex: 1;
        }

        /* ── Submenu (Collapsible) ── */
        .menu-toggle .toggle-arrow {
            font-size: 0.7rem;
            transition: transform 0.3s ease;
            color: var(--sidebar-text);
        }
        .menu-toggle[aria-expanded="true"] .toggle-arrow {
            transform: rotate(90deg);
        }
        .menu-toggle[aria-expanded="true"] {
            color: var(--sidebar-text-bright);
            background: rgba(255,255,255,0.03);
        }
        .sidebar-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            background: rgba(0,0,0,0.12);
        }
        .sidebar-submenu a {
            padding: 0.55rem 1.5rem 0.55rem 3.3rem;
            font-size: 0.82rem;
            font-weight: 400;
            position: relative;
        }
        .sidebar-submenu a::before {
            content: '';
            position: absolute;
            left: 2.3rem;
            top: 50%;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--sidebar-text);
            transform: translateY(-50%);
            transition: background 0.2s;
        }
        .sidebar-submenu a.active::before,
        .sidebar-submenu a:hover::before {
            background: var(--accent-blue);
        }
        .sidebar-submenu a.active {
            border-right: 3px solid var(--accent-blue);
        }

        /* ── Sidebar Footer ── */
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            font-size: 0.72rem;
            color: var(--sidebar-text);
            opacity: 0.6;
        }
        .sidebar-footer i { margin-right: 0.3rem; }

        /* ══════════════════════════════════════════════════════════
           MAIN CONTENT AREA
           ══════════════════════════════════════════════════════════ */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        /* ── Top Header Bar ── */
        .top-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-light);
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .top-header .breadcrumb-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .top-header .breadcrumb-area h1 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }
        .top-header .breadcrumb-path {
            font-size: 0.78rem;
            color: var(--text-secondary);
        }
        .top-header .breadcrumb-path a {
            color: var(--accent-blue);
            text-decoration: none;
        }
        .top-header .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .top-header .header-badge {
            background: #f0f9ff;
            color: var(--accent-blue);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid #bae6fd;
        }
        .top-header .header-badge i { margin-right: 4px; }
        .top-header .header-time {
            font-size: 0.78rem;
            color: var(--text-secondary);
        }

        .btn-toggle-sidebar {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--text-dark);
            cursor: pointer;
            padding: 0.25rem;
            margin-right: 0.75rem;
        }

        /* ── Content Body ── */
        .content-body {
            flex: 1;
            padding: 1.75rem 2rem;
            min-width: 0;
        }

        /* ══════════════════════════════════════════════════════════
           DASHBOARD CARDS (Summary Statistics)
           ══════════════════════════════════════════════════════════ */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-light);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
        }
        .stat-card.blue::before    { background: linear-gradient(90deg, #0ea5e9, #6366f1); }
        .stat-card.orange::before  { background: linear-gradient(90deg, #f97316, #ef4444); }
        .stat-card.green::before   { background: linear-gradient(90deg, #22c55e, #14b8a6); }
        .stat-card.purple::before  { background: linear-gradient(90deg, #a855f7, #ec4899); }

        .stat-card .stat-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .stat-card.blue .stat-icon-wrap    { background: #f0f9ff; color: #0ea5e9; }
        .stat-card.orange .stat-icon-wrap  { background: #fff7ed; color: #f97316; }
        .stat-card.green .stat-icon-wrap   { background: #f0fdf4; color: #22c55e; }
        .stat-card.purple .stat-icon-wrap  { background: #faf5ff; color: #a855f7; }

        .stat-card .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-top: 0.25rem;
            letter-spacing: -0.02em;
        }
        .stat-card .stat-sub {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.3rem;
        }

        /* ══════════════════════════════════════════════════════════
           DASHBOARD GRID (Breakdown + Rumus)
           ══════════════════════════════════════════════════════════ */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .dash-panel {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-light);
            padding: 1.5rem;
        }
        .dash-panel .panel-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dash-panel .panel-title i {
            color: var(--accent-blue);
        }

        /* Breakdown Items */
        .breakdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }
        .breakdown-item:last-child { border-bottom: none; }
        .breakdown-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            margin-right: 0.65rem;
            flex-shrink: 0;
        }
        .dot-konvensional { background: var(--konvensional); }
        .dot-hybrid       { background: var(--hybrid); }
        .dot-listrik      { background: var(--listrik); }
        .dot-motor        { background: var(--motor); }

        .breakdown-unit {
            font-weight: 700;
            color: var(--text-dark);
        }
        .breakdown-pajak {
            font-size: 0.72rem;
            color: var(--text-secondary);
        }

        /* Rumus Cards */
        .rumus-card {
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 0.65rem;
            border: 1px solid;
        }
        .rumus-card:last-child { margin-bottom: 0; }
        .rumus-card .rumus-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.3rem;
        }
        .rumus-card code {
            font-size: 0.75rem;
            color: var(--text-dark);
            background: none;
        }
        .rumus-konvensional { background: #fff7ed; border-color: #fed7aa; }
        .rumus-konvensional .rumus-label { color: var(--konvensional); }
        .rumus-hybrid { background: #fefce8; border-color: #fde68a; }
        .rumus-hybrid .rumus-label { color: var(--hybrid); }
        .rumus-listrik { background: #f0f9ff; border-color: #bae6fd; }
        .rumus-listrik .rumus-label { color: var(--listrik); }
        .rumus-motor { background: #faf5ff; border-color: #e9d5ff; }
        .rumus-motor .rumus-label { color: var(--motor); }

        /* ══════════════════════════════════════════════════════════
           DATA TABLES (Content Area)
           ══════════════════════════════════════════════════════════ */
        .table-panel {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-light);
            overflow: hidden;
        }
        .table-panel .table-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .table-panel .table-header h2 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .table-panel .table-header .record-count {
            font-size: 0.75rem;
            color: var(--text-secondary);
            background: #f1f5f9;
            padding: 3px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .table { margin-bottom: 0; font-size: 0.85rem; width: 100%; }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table thead th {
            background: #f8fafc;
            color: var(--text-secondary);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 2px solid var(--border-light);
            border-top: none;
            padding: 0.85rem 1rem;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            color: var(--text-dark);
            border-color: #f1f5f9;
            background: #ffffff;
        }
        .table-sm thead th {
            padding: 0.5rem 0.75rem;
        }
        .table-sm tbody td {
            padding: 0.5rem 0.75rem;
        }
        .table tbody tr {
            transition: background 0.15s;
        }
        .table tbody tr:hover td {
            background: #f8fafc;
        }

        /* ── Badge Kategori ── */
        .badge-kategori {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .badge-konvensional {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }
        .badge-hybrid {
            background: #fefce8;
            color: #a16207;
            border: 1px solid #fde68a;
        }
        .badge-listrik {
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .badge-motor {
            background: #faf5ff;
            color: #7e22ce;
            border: 1px solid #e9d5ff;
        }

        /* ── Spesifikasi (Key-Value) ── */
        .spec-list { min-width: 200px; }
        .spec-row {
            font-size: 0.82rem;
            color: var(--text-dark);
            line-height: 1.7;
        }
        .spec-row strong {
            color: var(--text-dark);
            font-weight: 700;
            margin-right: 3px;
        }
        .spec-row span {
            color: #495057;
            font-weight: 400;
        }

        /* ── Harga & Pajak ── */
        .price-main-table {
            font-weight: 700;
            color: var(--text-dark) !important;
            white-space: nowrap;
            font-size: 0.88rem;
        }
        .pajak-val-table {
            font-weight: 700;
            color: #dc2626 !important;
            white-space: nowrap;
            font-size: 0.88rem;
        }

        /* ── Stok badge ── */
        .stok-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .stok-ok   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .stok-low  { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }

        /* ── Transmisi badge ── */
        .trans-badge {
            font-size: 0.78rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* ── No / ID col ── */
        .no-col {
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-weight: 500;
        }
        .id-chip {
            font-family: 'Courier New', monospace;
            font-size: 0.75rem;
            background: #f1f5f9;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 2px 8px;
            color: var(--text-secondary);
        }

        /* ── Table Footer ── */
        .table-footer {
            background: #f8fafc;
            border-top: 2px solid var(--border-light);
            padding: 1rem 1.5rem;
        }
        .table-footer .footer-info {
            font-size: 0.78rem;
            color: var(--text-secondary);
        }
        .table-footer .footer-info code {
            font-size: 0.75rem;
            color: var(--accent-blue);
            background: #f0f9ff;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .table-footer .footer-total {
            text-align: left;
        }
        @media (min-width: 768px) {
            .table-footer .footer-total {
                text-align: right;
            }
        }
        .table-footer .footer-total .label {
            font-size: 0.68rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        .table-footer .footer-total .value {
            font-weight: 800;
            color: #dc2626;
            font-size: 1rem;
        }

        /* ══════════════════════════════════════════════════════════
           CONTENT FOOTER
           ══════════════════════════════════════════════════════════ */
        .content-footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-light);
            padding: 1rem 2rem;
            font-size: 0.78rem;
            color: var(--text-secondary);
            text-align: center;
        }
        .content-footer span {
            color: var(--accent-blue);
            font-weight: 600;
        }

        /* ══════════════════════════════════════════════════════════
           ALERT / ERROR
           ══════════════════════════════════════════════════════════ */
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
        }
        .alert-error strong { color: #b91c1c; }

        /* ══════════════════════════════════════════════════════════
           ANIMATIONS
           ══════════════════════════════════════════════════════════ */
        html { scroll-behavior: smooth; }
        .fade-in {
            animation: fadeIn 0.45s ease-out both;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-delay { animation-delay: 0.1s; }
        .fade-in-delay-2 { animation-delay: 0.2s; }

        /* ══════════════════════════════════════════════════════════
           SIDEBAR OVERLAY (mobile)
           ══════════════════════════════════════════════════════════ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }
        .sidebar-overlay.active { display: block; }

        /* ══════════════════════════════════════════════════════════
           RESPONSIVE
           ══════════════════════════════════════════════════════════ */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .btn-toggle-sidebar {
                display: inline-block;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .content-body {
                padding: 1.25rem 1rem;
            }
            .top-header {
                padding: 0.75rem 1rem;
            }
        }
        @media (max-width: 575.98px) {
            .stat-cards {
                grid-template-columns: 1fr;
            }
            .top-header .header-badge,
            .top-header .header-time {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR OVERLAY (mobile) ═════════════════════════════ -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-wrapper">

    <!-- ══════════════════════════════════════════════════════
         SIDEBAR
         ══════════════════════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand -->
        <a class="sidebar-brand" href="index.php?page=dashboard">
            <div class="brand-logo">
                <i class="fa-solid fa-car-side"></i>
            </div>
            <div>
                <div class="brand-text">Showroom<span>Pro</span></div>
                <div class="brand-sub">Admin Panel</div>
            </div>
        </a>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="sidebar-label">Menu Utama</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>" id="nav-dashboard">
                        <span class="menu-icon"><i class="fa-solid fa-gauge-high"></i></span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-label" style="margin-top:0.5rem;">Manajemen</div>
            <ul class="sidebar-menu">
                <li>
                    <button class="menu-toggle <?= in_array($page, ['mobil_konvensional','mobil_hybrid','mobil_listrik','motor_besar']) ? '' : 'collapsed' ?>"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#menuKendaraan"
                            aria-expanded="<?= in_array($page, ['mobil_konvensional','mobil_hybrid','mobil_listrik','motor_besar']) ? 'true' : 'false' ?>">
                        <span class="menu-icon"><i class="fa-solid fa-car"></i></span>
                        <span class="menu-text">Kendaraan</span>
                        <i class="fa-solid fa-chevron-right toggle-arrow"></i>
                    </button>
                    <div class="collapse <?= in_array($page, ['mobil_konvensional','mobil_hybrid','mobil_listrik','motor_besar']) ? 'show' : '' ?>" id="menuKendaraan">
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="index.php?page=mobil_konvensional" class="<?= $page === 'mobil_konvensional' ? 'active' : '' ?>" id="nav-konvensional">
                                    <span class="menu-text">Mobil Konvensional</span>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=mobil_hybrid" class="<?= $page === 'mobil_hybrid' ? 'active' : '' ?>" id="nav-hybrid">
                                    <span class="menu-text">Mobil Hybrid</span>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=mobil_listrik" class="<?= $page === 'mobil_listrik' ? 'active' : '' ?>" id="nav-listrik">
                                    <span class="menu-text">Mobil Listrik</span>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?page=motor_besar" class="<?= $page === 'motor_besar' ? 'active' : '' ?>" id="nav-motor">
                                    <span class="menu-text">Motor Besar</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div><i class="fa-solid fa-database"></i> pbo_dbshowroom</div>
            <div style="margin-top:0.3rem;"><i class="fa-solid fa-code"></i> PHP OOP Project</div>
        </div>
    </aside>

    <!-- ══════════════════════════════════════════════════════
         MAIN CONTENT AREA
         ══════════════════════════════════════════════════════ -->
    <main class="main-content">

        <!-- ── Top Header Bar ── -->
        <header class="top-header">
            <div class="breadcrumb-area">
                <button class="btn-toggle-sidebar" id="btnToggleSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h1><?= htmlspecialchars($pageTitle) ?></h1>
                    <div class="breadcrumb-path">
                        <a href="index.php?page=dashboard">ShowroomPro</a>
                        <i class="fa-solid fa-chevron-right" style="font-size:0.6rem;margin:0 0.4rem;opacity:0.5"></i>
                        <?= htmlspecialchars($pageTitle) ?>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <span class="header-badge"><i class="fa-solid fa-database"></i> pbo_dbshowroom</span>
                <span class="header-badge d-none d-md-inline"><i class="fa-solid fa-code"></i> PHP OOP</span>
                <span class="header-time d-none d-sm-inline">
                    <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i') ?> WIB
                </span>
            </div>
        </header>

        <!-- ── Content Body ── -->
        <div class="content-body">

            <?php if ($error): ?>
            <!-- Error State -->
            <div class="alert-error fade-in">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <strong>Gagal memuat data.</strong> <?= htmlspecialchars($error) ?>
                <div class="mt-2" style="font-size:.8rem">Pastikan XAMPP/WAMP aktif dan database <code>pbo_dbshowroom</code> sudah diimport.</div>
            </div>

            <?php else: ?>

            <?php
            // ══════════════════════════════════════════════════════
            //  PAGE ROUTING via switch-case
            // ══════════════════════════════════════════════════════
            switch ($page):

                // ──────────────────────────────────────────────────
                //  DASHBOARD (Default)
                // ──────────────────────────────────────────────────
                case 'dashboard':
            ?>
            <!-- Summary Stat Cards -->
            <div class="stat-cards fade-in">
                <!-- Total Kendaraan -->
                <div class="stat-card blue">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-car-side"></i></div>
                    <div class="stat-label">Total Kendaraan</div>
                    <div class="stat-value"><?= $ringkasan['total_unit'] ?> <small style="font-size:.55em;color:var(--text-secondary)">unit</small></div>
                    <div class="stat-sub">Terdaftar di inventaris showroom</div>
                </div>

                <!-- Total Pajak -->
                <div class="stat-card orange">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-receipt"></i></div>
                    <div class="stat-label">Total Pajak Tahunan</div>
                    <div class="stat-value" style="font-size:1.15rem"><?= rupiah($ringkasan['total_pajak']) ?></div>
                    <div class="stat-sub">Kalkulasi Dynamic Binding</div>
                </div>

                <!-- Total Nilai Stok -->
                <div class="stat-card green">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div class="stat-label">Total Nilai Stok</div>
                    <div class="stat-value" style="font-size:1.1rem"><?= rupiah($ringkasan['total_nilai_stok']) ?></div>
                    <div class="stat-sub">Harga dasar × stok unit</div>
                </div>

                <!-- Kategori Count -->
                <div class="stat-card purple">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-layer-group"></i></div>
                    <div class="stat-label">Jenis Kategori</div>
                    <div class="stat-value"><?= count($ringkasan['per_kategori']) ?> <small style="font-size:.55em;color:var(--text-secondary)">tipe</small></div>
                    <div class="stat-sub">Mobil & Motor terdaftar</div>
                </div>
            </div>

            <!-- Dashboard Grid: Breakdown + Rumus -->
            <div class="dashboard-grid fade-in fade-in-delay">
                <!-- Distribusi per Kategori -->
                <div class="dash-panel">
                    <div class="panel-title">
                        <i class="fa-solid fa-chart-pie"></i> Distribusi per Kategori
                    </div>
                    <?php
                    $dotMap = [
                        'Mobil Konvensional' => 'dot-konvensional',
                        'Mobil Hybrid'       => 'dot-hybrid',
                        'Mobil Listrik'      => 'dot-listrik',
                        'Motor Besar'        => 'dot-motor',
                    ];
                    foreach ($ringkasan['per_kategori'] as $kat => $data): ?>
                    <div class="breakdown-item">
                        <div class="d-flex align-items-center">
                            <div class="breakdown-dot <?= $dotMap[$kat] ?? '' ?>"></div>
                            <span style="color:var(--text-dark);font-weight:500"><?= $categoryIcon[$kat] ?? '' ?> <?= $kat ?></span>
                        </div>
                        <div class="text-end">
                            <div class="breakdown-unit"><?= $data['unit'] ?> unit</div>
                            <div class="breakdown-pajak"><?= rupiah($data['pajak']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Rumus Pajak -->
                <div class="dash-panel">
                    <div class="panel-title">
                        <i class="fa-solid fa-calculator"></i> Rumus Pajak (Polymorphism)
                    </div>
                    <div class="rumus-card rumus-konvensional">
                        <div class="rumus-label">🚗 Mobil Konvensional</div>
                        <code>(2% × Harga) + (Mesin × 500)</code>
                    </div>
                    <div class="rumus-card rumus-hybrid">
                        <div class="rumus-label">⚡🚗 Mobil Hybrid</div>
                        <code>(1% × Harga) + (Mesin × 250)</code>
                    </div>
                    <div class="rumus-card rumus-listrik">
                        <div class="rumus-label">⚡ Mobil Listrik</div>
                        <code>0.5% × Harga Dasar</code>
                    </div>
                    <div class="rumus-card rumus-motor">
                        <div class="rumus-label">🏍️ Motor Besar</div>
                        <code>1.5% × Harga Dasar</code>
                    </div>
                </div>
            </div>

            <!-- Dashboard: All-Vehicle Table Preview -->
            <div class="fade-in fade-in-delay-2">
                <div class="table-panel">
                    <div class="table-header">
                        <h2><i class="fa-solid fa-table-list" style="color:var(--accent-blue)"></i> Ringkasan Seluruh Inventaris</h2>
                        <span class="record-count"><?= count($koleksi) ?> kendaraan</span>
                    </div>
                    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                        <table class="table table-sm table-borderless" id="tabel-dashboard">
                            <thead class="text-nowrap">
                                <tr>
                                    <th>No</th>
                                    <th>Brand & Model</th>
                                    <th>Tahun</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th class="text-end">Harga Dasar</th>
                                    <th class="text-end">Pajak Tahunan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($koleksi as $i => $kendaraan):
                                $kategori = $showroom->getJenisKategori($kendaraan);
                                $pajak    = $kendaraan->hitungPajakTahunan();
                                $stok     = $kendaraan->getStok();
                                $badge    = $badgeClass[$kategori] ?? 'badge-konvensional';
                            ?>
                                <tr>
                                    <td class="no-col"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-bold" style="color:#212529;font-size:.88rem"><?= htmlspecialchars($kendaraan->getBrand()) ?></div>
                                        <div style="color:#64748b;font-size:.8rem;margin-top:2px"><?= htmlspecialchars($kendaraan->getModel()) ?></div>
                                    </td>
                                    <td class="fw-semibold" style="color:#212529"><?= $kendaraan->getTahun() ?></td>
                                    <td><span class="badge-kategori <?= $badge ?>"><?= $categoryIcon[$kategori] ?? '' ?> <?= $kategori ?></span></td>
                                    <td><span class="stok-badge <?= $stok > 2 ? 'stok-ok' : 'stok-low' ?>"><?= $stok ?> unit</span></td>
                                    <td class="price-main-table text-end"><?= rupiah($kendaraan->getHargaDasar()) ?></td>
                                    <td class="pajak-val-table text-end"><?= rupiah($pajak) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="row w-100 align-items-center g-2">
                            <div class="col-md-7 footer-info">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Pajak dihitung via <strong>Dynamic Binding</strong> — <code>hitungPajakTahunan()</code> sesuai subkelas saat runtime
                            </div>
                            <div class="col-md-5 text-md-end footer-total">
                                <div class="label">Total Pajak/Tahun</div>
                                <div class="value"><?= rupiah($ringkasan['total_pajak']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                    break;

                // ──────────────────────────────────────────────────
                //  MOBIL KONVENSIONAL
                // ──────────────────────────────────────────────────
                case 'mobil_konvensional':
                    $filtered = $showroom->filterJenis(MobilKonvensional::class);
            ?>
            <div class="fade-in">
                <div class="table-panel">
                    <div class="table-header">
                        <h2><i class="fa-solid fa-car" style="color:var(--konvensional)"></i> Data Mobil Konvensional</h2>
                        <span class="record-count"><?= count($filtered) ?> unit</span>
                    </div>
                    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                        <table class="table table-sm table-borderless" id="tabel-konvensional">
                            <thead class="text-nowrap">
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Brand & Model</th>
                                    <th>Tahun</th>
                                    <th>Stok</th>
                                    <th>Transmisi</th>
                                    <th class="text-end">Harga Dasar</th>
                                    <th>Kapasitas Mesin</th>
                                    <th>Bahan Bakar</th>
                                    <th>Kap. Tangki</th>
                                    <th>Konsumsi BBM</th>
                                    <th class="text-end">Pajak Tahunan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; foreach ($filtered as $kendaraan):
                                $spesifikasi = $showroom->getSpesifikasiKhusus($kendaraan);
                                $pajak       = $kendaraan->hitungPajakTahunan();
                                $stok        = $kendaraan->getStok();
                            ?>
                                <tr>
                                    <td class="no-col"><?= $no++ ?></td>
                                    <td><span class="id-chip">#<?= $kendaraan->getIdKendaraan() ?></span></td>
                                    <td>
                                        <div class="fw-bold" style="color:#212529"><?= htmlspecialchars($kendaraan->getBrand()) ?></div>
                                        <div style="color:#64748b;font-size:.8rem"><?= htmlspecialchars($kendaraan->getModel()) ?></div>
                                    </td>
                                    <td class="fw-semibold" style="color:#212529"><?= $kendaraan->getTahun() ?></td>
                                    <td><span class="stok-badge <?= $stok > 2 ? 'stok-ok' : 'stok-low' ?>"><?= $stok ?> unit</span></td>
                                    <td><span class="trans-badge"><?= $kendaraan->getTransmisi() ?></span></td>
                                    <td class="price-main-table text-end"><?= rupiah($kendaraan->getHargaDasar()) ?></td>
                                    <td><strong><?= $spesifikasi['Kapasitas Mesin'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Bahan Bakar'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Kapasitas Tangki'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Konsumsi BBM'] ?? '-' ?></strong></td>
                                    <td class="pajak-val-table text-end"><?= rupiah($pajak) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="row w-100 align-items-center g-2">
                            <div class="col-md-7 footer-info">
                                <i class="fa-solid fa-calculator me-1"></i>
                                <strong>Rumus Pajak:</strong> <code>(2% × Harga) + (Mesin cc × 500)</code>
                            </div>
                            <div class="col-md-5 text-md-end footer-total">
                                <div class="label">Total Pajak Konvensional</div>
                                <div class="value"><?= rupiah(array_sum(array_map(fn($k) => $k->hitungPajakTahunan(), $filtered))) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                    break;

                // ──────────────────────────────────────────────────
                //  MOBIL HYBRID
                // ──────────────────────────────────────────────────
                case 'mobil_hybrid':
                    $filtered = $showroom->filterJenis(MobilHybrid::class);
            ?>
            <div class="fade-in">
                <div class="table-panel">
                    <div class="table-header">
                        <h2><i class="fa-solid fa-bolt-lightning" style="color:var(--hybrid)"></i> Data Mobil Hybrid</h2>
                        <span class="record-count"><?= count($filtered) ?> unit</span>
                    </div>
                    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                        <table class="table table-sm table-borderless" id="tabel-hybrid">
                            <thead class="text-nowrap">
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Brand & Model</th>
                                    <th>Tahun</th>
                                    <th>Stok</th>
                                    <th>Transmisi</th>
                                    <th class="text-end">Harga Dasar</th>
                                    <th>Baterai (kWh)</th>
                                    <th>Motor Listrik</th>
                                    <th>Tipe Hybrid</th>
                                    <th>Mode Berkendara</th>
                                    <th>Konsumsi BBM</th>
                                    <th class="text-end">Pajak Tahunan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; foreach ($filtered as $kendaraan):
                                $spesifikasi = $showroom->getSpesifikasiKhusus($kendaraan);
                                $pajak       = $kendaraan->hitungPajakTahunan();
                                $stok        = $kendaraan->getStok();
                            ?>
                                <tr>
                                    <td class="no-col"><?= $no++ ?></td>
                                    <td><span class="id-chip">#<?= $kendaraan->getIdKendaraan() ?></span></td>
                                    <td>
                                        <div class="fw-bold" style="color:#212529"><?= htmlspecialchars($kendaraan->getBrand()) ?></div>
                                        <div style="color:#64748b;font-size:.8rem"><?= htmlspecialchars($kendaraan->getModel()) ?></div>
                                    </td>
                                    <td class="fw-semibold" style="color:#212529"><?= $kendaraan->getTahun() ?></td>
                                    <td><span class="stok-badge <?= $stok > 2 ? 'stok-ok' : 'stok-low' ?>"><?= $stok ?> unit</span></td>
                                    <td><span class="trans-badge"><?= $kendaraan->getTransmisi() ?></span></td>
                                    <td class="price-main-table text-end"><?= rupiah($kendaraan->getHargaDasar()) ?></td>
                                    <td><strong><?= $spesifikasi['Kapasitas Baterai'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Daya Motor Listrik'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Tipe Hybrid'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Mode Berkendara'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Konsumsi BBM'] ?? '-' ?></strong></td>
                                    <td class="pajak-val-table text-end"><?= rupiah($pajak) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="row w-100 align-items-center g-2">
                            <div class="col-md-7 footer-info">
                                <i class="fa-solid fa-calculator me-1"></i>
                                <strong>Rumus Pajak:</strong> <code>(1% × Harga) + (Mesin cc × 250)</code>
                            </div>
                            <div class="col-md-5 text-md-end footer-total">
                                <div class="label">Total Pajak Hybrid</div>
                                <div class="value"><?= rupiah(array_sum(array_map(fn($k) => $k->hitungPajakTahunan(), $filtered))) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                    break;

                // ──────────────────────────────────────────────────
                //  MOBIL LISTRIK
                // ──────────────────────────────────────────────────
                case 'mobil_listrik':
                    $filtered = $showroom->filterJenis(MobilListrik::class);
            ?>
            <div class="fade-in">
                <div class="table-panel">
                    <div class="table-header">
                        <h2><i class="fa-solid fa-charging-station" style="color:var(--listrik)"></i> Data Mobil Listrik</h2>
                        <span class="record-count"><?= count($filtered) ?> unit</span>
                    </div>
                    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                        <table class="table table-sm table-borderless" id="tabel-listrik">
                            <thead class="text-nowrap">
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Brand & Model</th>
                                    <th>Tahun</th>
                                    <th>Stok</th>
                                    <th>Transmisi</th>
                                    <th class="text-end">Harga Dasar</th>
                                    <th>Kap. Baterai</th>
                                    <th>Jarak Tempuh</th>
                                    <th>Kec. Maksimum</th>
                                    <th>Waktu Pengisian</th>
                                    <th class="text-end">Pajak Tahunan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; foreach ($filtered as $kendaraan):
                                $spesifikasi = $showroom->getSpesifikasiKhusus($kendaraan);
                                $pajak       = $kendaraan->hitungPajakTahunan();
                                $stok        = $kendaraan->getStok();
                            ?>
                                <tr>
                                    <td class="no-col"><?= $no++ ?></td>
                                    <td><span class="id-chip">#<?= $kendaraan->getIdKendaraan() ?></span></td>
                                    <td>
                                        <div class="fw-bold" style="color:#212529"><?= htmlspecialchars($kendaraan->getBrand()) ?></div>
                                        <div style="color:#64748b;font-size:.8rem"><?= htmlspecialchars($kendaraan->getModel()) ?></div>
                                    </td>
                                    <td class="fw-semibold" style="color:#212529"><?= $kendaraan->getTahun() ?></td>
                                    <td><span class="stok-badge <?= $stok > 2 ? 'stok-ok' : 'stok-low' ?>"><?= $stok ?> unit</span></td>
                                    <td><span class="trans-badge"><?= $kendaraan->getTransmisi() ?></span></td>
                                    <td class="price-main-table text-end"><?= rupiah($kendaraan->getHargaDasar()) ?></td>
                                    <td><strong><?= $spesifikasi['Kapasitas Baterai'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Jarak Tempuh'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Kecepatan Maks.'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Waktu Pengisian'] ?? '-' ?></strong></td>
                                    <td class="pajak-val-table text-end"><?= rupiah($pajak) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="row w-100 align-items-center g-2">
                            <div class="col-md-7 footer-info">
                                <i class="fa-solid fa-calculator me-1"></i>
                                <strong>Rumus Pajak:</strong> <code>0.5% × Harga Dasar</code>
                            </div>
                            <div class="col-md-5 text-md-end footer-total">
                                <div class="label">Total Pajak Listrik</div>
                                <div class="value"><?= rupiah(array_sum(array_map(fn($k) => $k->hitungPajakTahunan(), $filtered))) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                    break;

                // ──────────────────────────────────────────────────
                //  MOTOR BESAR
                // ──────────────────────────────────────────────────
                case 'motor_besar':
                    $filtered = $showroom->filterJenis(MotorBesar::class);
            ?>
            <div class="fade-in">
                <div class="table-panel">
                    <div class="table-header">
                        <h2><i class="fa-solid fa-motorcycle" style="color:var(--motor)"></i> Data Motor Besar</h2>
                        <span class="record-count"><?= count($filtered) ?> unit</span>
                    </div>
                    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                        <table class="table table-sm table-borderless" id="tabel-motor">
                            <thead class="text-nowrap">
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Brand & Model</th>
                                    <th>Tahun</th>
                                    <th>Stok</th>
                                    <th>Transmisi</th>
                                    <th class="text-end">Harga Dasar</th>
                                    <th>Kapasitas Mesin</th>
                                    <th>Tipe Motor</th>
                                    <th>Kap. Tangki</th>
                                    <th class="text-end">Pajak Tahunan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; foreach ($filtered as $kendaraan):
                                $spesifikasi = $showroom->getSpesifikasiKhusus($kendaraan);
                                $pajak       = $kendaraan->hitungPajakTahunan();
                                $stok        = $kendaraan->getStok();
                            ?>
                                <tr>
                                    <td class="no-col"><?= $no++ ?></td>
                                    <td><span class="id-chip">#<?= $kendaraan->getIdKendaraan() ?></span></td>
                                    <td>
                                        <div class="fw-bold" style="color:#212529"><?= htmlspecialchars($kendaraan->getBrand()) ?></div>
                                        <div style="color:#64748b;font-size:.8rem"><?= htmlspecialchars($kendaraan->getModel()) ?></div>
                                    </td>
                                    <td class="fw-semibold" style="color:#212529"><?= $kendaraan->getTahun() ?></td>
                                    <td><span class="stok-badge <?= $stok > 2 ? 'stok-ok' : 'stok-low' ?>"><?= $stok ?> unit</span></td>
                                    <td><span class="trans-badge"><?= $kendaraan->getTransmisi() ?></span></td>
                                    <td class="price-main-table text-end"><?= rupiah($kendaraan->getHargaDasar()) ?></td>
                                    <td><strong><?= $spesifikasi['Kapasitas Mesin'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Tipe Motor'] ?? '-' ?></strong></td>
                                    <td><strong><?= $spesifikasi['Kapasitas Tangki'] ?? '-' ?></strong></td>
                                    <td class="pajak-val-table text-end"><?= rupiah($pajak) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="row w-100 align-items-center g-2">
                            <div class="col-md-7 footer-info">
                                <i class="fa-solid fa-calculator me-1"></i>
                                <strong>Rumus Pajak:</strong> <code>1.5% × Harga Dasar</code>
                            </div>
                            <div class="col-md-5 text-md-end footer-total">
                                <div class="label">Total Pajak Motor Besar</div>
                                <div class="value"><?= rupiah(array_sum(array_map(fn($k) => $k->hitungPajakTahunan(), $filtered))) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                    break;

                default:
                    // Redirect to dashboard
                    header('Location: index.php?page=dashboard');
                    exit;

            endswitch;
            ?>

            <?php endif; ?>

        </div><!-- /content-body -->

        <!-- ── Content Footer ── -->
        <footer class="content-footer">
            <span>Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan</span>
            &nbsp;·&nbsp; PHP OOP — Encapsulation · Inheritance · Polymorphism · Abstract Class
            &nbsp;·&nbsp; Database: <span>pbo_dbshowroom</span>
        </footer>

    </main>

</div><!-- /admin-wrapper -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Sidebar Toggle Script -->
<script>
    (function() {
        const sidebar  = document.getElementById('sidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        const btnToggle = document.getElementById('btnToggleSidebar');

        function openSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('active');
        }
        function closeSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('active');
        }

        if (btnToggle) {
            btnToggle.addEventListener('click', function() {
                sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
            });
        }
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
    })();
</script>

</body>
</html>
