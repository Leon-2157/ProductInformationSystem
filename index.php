<?php

declare(strict_types=1);

$errorMessage = null;
$products = [];
$ringkasan = [
    'total_produk'       => 0,
    'total_unit_stok'    => 0,
    'jumlah_stok_kritis' => 0,
    'total_nilai_aset'   => 0.0
];

try {
    // 1. Impor dependensi Data Layer dan Processing Layer
    require_once __DIR__ . '/products.php';
    require_once __DIR__ . '/functions.php';

    // 2. Validasi ketersediaan variabel data dari Data Layer
    if (!isset($products) || !is_array($products)) {
        throw new RuntimeException("Data Layer tidak menyediakan koleksi produk yang valid.");
    }

    // 3. Kalkulasi metrik agregat menggunakan Processing Layer
    $ringkasan = hitung_ringkasan_inventaris($products);

} catch (Throwable $e) {
    // Graceful error handling
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Product Information System - Sistem manajemen data informasi produk dan monitoring valuasi aset stok gudang.">
    <title>Product Information System - Warehouse Commodity Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Header Aplikasi -->
        <header class="app-header" role="banner">
            <div class="header-top">
                <div class="brand-wrapper">
                    <div class="brand-icon" aria-hidden="true">
                        <!-- Box Warehouse SVG Icon -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m7.5 4.27 9 5.15"></path>
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                            <path d="m3.3 7 8.7 5 8.7-5"></path>
                            <path d="M12 22V12"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="brand-title">Product Information System</h1>
                        <span class="header-meta">
                            <!-- Shield Check SVG Icon -->
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                            Sistem pengelolaan data komoditas produk gudang
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Penanganan Error Jika Terjadi Kendala -->
        <?php if ($errorMessage !== null): ?>
            <div class="alert alert-danger" role="alert">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    <strong>Terjadi Kesalahan Pemrosesan Data:</strong>
                    <p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Kartu Metrik KPI Inventaris -->
        <section class="stats-grid" aria-label="Ringkasan Statistik Gudang">
            <!-- Kartu 1: Total Valuasi Aset -->
            <article class="stat-card">
                <div class="stat-icon icon-asset" aria-hidden="true">
                    <!-- Banknote SVG Icon -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="12" x="2" y="6" rx="2"></rect>
                        <circle cx="12" cy="12" r="2"></circle>
                        <path d="M6 12h.01M18 12h.01"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Valuasi Aset</span>
                    <span class="stat-value"><?= format_rupiah($ringkasan['total_nilai_aset']) ?></span>
                </div>
            </article>

            <!-- Kartu 2: Jumlah Komoditas -->
            <article class="stat-card">
                <div class="stat-icon icon-items" aria-hidden="true">
                    <!-- Layers SVG Icon -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"></path>
                        <path d="m22 12.5-8.58 3.91a2 2 0 0 1-1.66 0L2 12.5"></path>
                        <path d="m22 17.5-8.58 3.91a2 2 0 0 1-1.66 0L2 17.5"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Jenis Komoditas</span>
                    <span class="stat-value"><?= number_format($ringkasan['total_produk'], 0, ',', '.') ?> Produk</span>
                </div>
            </article>

            <!-- Kartu 3: Total Unit Fisik -->
            <article class="stat-card">
                <div class="stat-icon icon-units" aria-hidden="true">
                    <!-- Package Check SVG Icon -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m16 16 2 2 4-4"></path>
                        <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"></path>
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path d="M3.29 7 12 12l8.71-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Unit Fisik</span>
                    <span class="stat-value"><?= number_format($ringkasan['total_unit_stok'], 0, ',', '.') ?> Unit</span>
                </div>
            </article>

            <!-- Kartu 4: Stok Kritis Alert -->
            <article class="stat-card stat-card-critical">
                <div class="stat-icon icon-critical" aria-hidden="true">
                    <!-- Alert Triangle SVG Icon -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Stok Kritis (&lt; 3)</span>
                    <span class="stat-value"><?= $ringkasan['jumlah_stok_kritis'] ?> Komoditas</span>
                </div>
            </article>
        </section>

        <!-- Tabel Data Produk -->
        <main class="content-card">
            <div class="card-header">
                <div class="card-title-group">
                    <h2 class="card-title">Daftar Komoditas &amp; Valuasi Stok</h2>
                </div>
                <div class="legend-group" aria-label="Keterangan status">
                    <div class="legend-item">
                        <span class="legend-color-box legend-safe" aria-hidden="true"></span>
                        <span>Stok Aman (&ge; 3)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color-box legend-critical" aria-hidden="true"></span>
                        <span>Stok Kritis (&lt; 3)</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="product-table" id="product-table" aria-label="Tabel Data Produk">
                    <thead>
                        <tr>
                            <th scope="col" class="cell-id text-center">ID</th>
                            <th scope="col">Nama Komoditas</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-right">Harga Satuan</th>
                            <th scope="col" class="text-center">Stok</th>
                            <th scope="col" class="text-right">Subtotal Nilai</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col">Spesifikasi / Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 32px; color: var(--color-text-muted);">
                                    Tidak ada data produk yang tersedia saat ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <?php
                                    $stokKritis = is_stok_kritis((int) $product['stok']);
                                    $subtotalNilai = hitung_subtotal_produk($product);
                                    $rowClass = $stokKritis ? 'row-critical' : '';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td class="cell-id text-center"><?= (int) $product['id'] ?></td>
                                    <td class="cell-name"><?= htmlspecialchars((string) $product['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <span class="category-badge">
                                            <?= htmlspecialchars((string) $product['kategori'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="text-right cell-numeric">
                                        <?= format_rupiah($product['harga']) ?>
                                    </td>
                                    <td class="text-center cell-stock <?= $stokKritis ? 'text-danger' : '' ?>">
                                        <?= (int) $product['stok'] ?>
                                    </td>
                                    <td class="text-right cell-numeric" style="font-weight: 600;">
                                        <?= format_rupiah($subtotalNilai) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($stokKritis): ?>
                                            <span class="status-badge status-critical" title="Stok kritis membutuhkan pengadaan ulang">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                </svg>
                                                Kritis (&lt; 3)
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-safe" title="Kuantitas stok memenuhi ambang batas aman">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Aman
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cell-desc"><?= htmlspecialchars((string) $product['deskripsi'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right">Total Akumulasi Valuasi Aset Gudang:</td>
                            <td class="text-center cell-stock"><?= number_format($ringkasan['total_unit_stok'], 0, ',', '.') ?></td>
                            <td class="text-right cell-numeric" style="color: var(--color-accent-dark); font-size: 1.0625rem;">
                                <?= format_rupiah($ringkasan['total_nilai_aset']) ?>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </main>

        <!-- Footer Aplikasi -->
        <footer class="app-footer" role="contentinfo">
            <p>Muhammad Taufiq | Mini Project 1</p>
        </footer>
    </div>
</body>
</html>
