<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ChangelogController extends Controller
{
    /**
     * Display a listing of changelog.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Changelog statis yang berisi daftar perubahan aplikasi
        $changelogs = [
            [
                'version' => '1.3.2',
                'release_date' => Carbon::now(),
                'type' => 'feature',
                'title' => 'Penambahan halaman changelog',
                'description' => 'Penambahan halaman changelog untuk dokumentasi perubahan dan pembaruan pada aplikasi.',
                'is_major' => false
            ],
            [
                'version' => '1.3.1',
                'release_date' => Carbon::now()->subDays(2),
                'type' => 'improvement',
                'title' => 'Optimalisasi tampilan checkout dan cart',
                'description' => 'Peningkatan tampilan halaman cart dan checkout dengan layout yang lebih kompak dan responsif.',
                'is_major' => false
            ],
            [
                'version' => '1.3.0',
                'release_date' => Carbon::now()->subDays(5),
                'type' => 'feature',
                'title' => 'Penambahan fitur bulk delete di halaman admin orders',
                'description' => 'Penambahan fitur untuk memilih dan menghapus beberapa pesanan sekaligus di halaman admin.',
                'is_major' => false
            ],
            [
                'version' => '1.2.1',
                'release_date' => Carbon::now()->subDays(15),
                'type' => 'improvement',
                'title' => 'Peningkatan tampilan dashboard admin',
                'description' => 'Penyempurnaan tampilan dashboard admin dengan widget statistik dan grafik untuk monitoring penjualan.',
                'is_major' => false
            ],
            [
                'version' => '1.2.0',
                'release_date' => Carbon::now()->subDays(20),
                'type' => 'feature',
                'title' => 'Integrasi pembayaran QRIS',
                'description' => 'Penambahan metode pembayaran QRIS untuk memudahkan transaksi tanpa uang tunai.',
                'is_major' => true
            ],
            [
                'version' => '1.1.1',
                'release_date' => Carbon::now()->subMonths(1)->subDays(10),
                'type' => 'bugfix',
                'title' => 'Perbaikan bug pada perhitungan total',
                'description' => 'Memperbaiki masalah perhitungan total harga pada keranjang belanja yang tidak akurat.',
                'is_major' => false
            ],
            [
                'version' => '1.1.0',
                'release_date' => Carbon::now()->subMonths(1)->subDays(15),
                'type' => 'feature',
                'title' => 'Penambahan fitur varian (panas/dingin)',
                'description' => 'Penambahan opsi untuk memilih minuman dalam versi panas atau dingin dengan harga yang berbeda.',
                'is_major' => false
            ],
            [
                'version' => '1.0.0',
                'release_date' => Carbon::now()->subMonths(2),
                'type' => 'feature',
                'title' => 'Peluncuran awal Kedai Coffee Kiosk',
                'description' => 'Versi pertama dari aplikasi Kedai Coffee Kiosk dengan fitur dasar pemesanan dan pengelolaan produk.',
                'is_major' => true
            ],
        ];

        // Kelompokkan changelog berdasarkan bulan
        $groupedChangelogs = collect($changelogs)->groupBy(function($item) {
            return Carbon::parse($item['release_date'])->format('Y-m');
        });

        return view('admin.changelog.index', compact('groupedChangelogs'));
    }
}
