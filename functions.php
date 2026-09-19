<?php

declare(strict_types=1);

// Stok kritis < 3
const AMBANG_BATAS_STOK_KRITIS = 3;

/**
 * Validasi integritas struktur dan tipe data entitas produk
 *
 * @param array
 * @throws InvalidArgumentException
 * @return void
 */
function validasi_produk(array $product): void
{
    $fieldWajib = ['id', 'nama', 'kategori', 'harga', 'stok', 'deskripsi'];

    foreach ($fieldWajib as $field) {
        if (!array_key_exists($field, $product)) {
            throw new InvalidArgumentException("Integritas data gagal: Field '{$field}' tidak ditemukan pada produk.");
        }
    }

    if (!is_int($product['id']) || $product['id'] <= 0) {
        throw new InvalidArgumentException("Validasi gagal: 'id' produk harus berupa integer positif.");
    }

    if (!is_string($product['nama']) || trim($product['nama']) === '') {
        throw new InvalidArgumentException("Validasi gagal: 'nama' produk harus berupa teks non-kosong.");
    }

    if (!is_numeric($product['harga']) || $product['harga'] < 0) {
        throw new InvalidArgumentException("Validasi gagal: 'harga' produk harus bernilai numerik >= 0.");
    }

    if (!is_int($product['stok']) || $product['stok'] < 0) {
        throw new InvalidArgumentException("Validasi gagal: 'stok' produk harus bernilai integer >= 0.");
    }
}

/**
 * Menghitung subtotal nilai inventaris untuk satu item produk
 *
 * @param array
 * @return float
 */
function hitung_subtotal_produk(array $product): float
{
    validasi_produk($product);
    return (float) ($product['harga'] * $product['stok']);
}

/**
 * Menghitung akumulasi total nilai valuasi aset seluruh produk
 *
 * @param array
 * @return float
 */
function hitung_total_nilai_stok(array $products): float
{
    if (empty($products)) {
        return 0.0;
    }

    $totalValuasi = 0.0;

    foreach ($products as $index => $product) {
        if (!is_array($product)) {
            throw new InvalidArgumentException("Elemen produk pada indeks [{$index}] bukan array yang valid.");
        }
        $totalValuasi += hitung_subtotal_produk($product);
    }

    return $totalValuasi;
}

/**
 * Alias fungsi camelCase untuk menjaga kompatibilitas dengan dokumen spesifikasi arsitektur
 *
 * @param array
 * @return float
 */
function hitungTotalNilaiStok(array $products): float
{
    return hitung_total_nilai_stok($products);
}

/**
 * Evaluasi stok kritis (< 3)
 *
 * @param int
 * @return bool
 */
function is_stok_kritis(int $stok): bool
{
    return $stok < AMBANG_BATAS_STOK_KRITIS;
}

/**
 * Format nilai angka Rupiah
 *
 * @param float|int
 * @return string
 */
function format_rupiah(float|int $angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

/**
 * Kalkulasi ringkasan metrik statistik inventaris gudang
 *
 * @param array
 * @return array
 */
function hitung_ringkasan_inventaris(array $products): array
{
    $ringkasan = [
        'total_produk'       => count($products),
        'total_unit_stok'    => 0,
        'jumlah_stok_kritis' => 0,
        'total_nilai_aset'   => 0.0
    ];

    foreach ($products as $product) {
        validasi_produk($product);

        $ringkasan['total_unit_stok'] += $product['stok'];
        $ringkasan['total_nilai_aset'] += hitung_subtotal_produk($product);

        if (is_stok_kritis($product['stok'])) {
            $ringkasan['jumlah_stok_kritis']++;
        }
    }

    return $ringkasan;
}
