<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;

class ResourceController extends Controller
{
    /**
     * Menampilkan panduan pengguna dalam format PDF
     *
     * @param string $type Tipe panduan (admin atau kiosk)
     * @return \Illuminate\Http\Response
     */
    public function downloadGuide($type)
    {
        // Validasi tipe panduan
        if (!in_array($type, ['admin', 'kiosk'])) {
            abort(404);
        }

        // Judul panduan
        $title = $type === 'admin' ? 'Panduan Admin' : 'Panduan Kiosk';

        // Konten panduan berdasarkan tipe
        $content = $this->getGuideContent($type);

        // Buat PDF
        $pdf = PDF::loadView('admin.resources.guide_pdf', [
            'title' => $title,
            'content' => $content
        ]);

        // Atur ukuran kertas dan orientasi
        $pdf->setPaper('a4', 'portrait');

        // Unduh PDF
        return $pdf->download("panduan_{$type}_kedai_coffee_kiosk.pdf");
    }

    /**
     * Menampilkan halaman video tutorial
     *
     * @param string $type Tipe video tutorial (admin atau kiosk)
     * @return \Illuminate\Http\Response
     */
    public function showVideo($type)
    {
        // Validasi tipe video
        if (!in_array($type, ['admin', 'kiosk'])) {
            abort(404);
        }

        // Judul video
        $title = $type === 'admin' ? 'Video Tutorial Admin' : 'Video Tutorial Kiosk';

        // Konten video berdasarkan tipe
        $videoData = $this->getVideoData($type);

        return view('admin.resources.video', [
            'title' => $title,
            'videos' => $videoData
        ]);
    }

    /**
     * Menampilkan halaman FAQ
     *
     * @return \Illuminate\Http\Response
     */
    public function showFaq()
    {
        // Data FAQ
        $faqData = $this->getFaqData();

        return view('admin.resources.faq', [
            'faqs' => $faqData
        ]);
    }

    /**
     * Mendapatkan konten panduan berdasarkan tipe
     *
     * @param string $type Tipe panduan
     * @return array
     */
    private function getGuideContent($type)
    {
        if ($type === 'admin') {
            return [
                [
                    'title' => 'Pengenalan',
                    'content' => 'Selamat datang di Panduan Admin Kedai Coffee Kiosk. Panduan ini akan membantu Anda memahami cara menggunakan panel admin untuk mengelola sistem pemesanan mandiri kedai kopi Anda.'
                ],
                [
                    'title' => 'Dashboard',
                    'content' => 'Dashboard adalah halaman utama panel admin yang menampilkan ringkasan data penting seperti total pesanan, pendapatan, produk terlaris, dan aktivitas terbaru. Data diperbarui secara real-time setiap 30 detik.',
                    'subsections' => [
                        [
                            'title' => 'Statistik Utama',
                            'content' => 'Bagian atas dashboard menampilkan statistik utama: total pesanan, total produk, pendapatan, dan jumlah kategori. Statistik ini memberikan gambaran cepat tentang performa bisnis Anda.'
                        ],
                        [
                            'title' => 'Pesanan Terbaru',
                            'content' => 'Tabel pesanan terbaru menampilkan 5 pesanan terakhir yang masuk ke sistem. Anda dapat melihat detail pesanan dengan mengklik tombol "Lihat" di kolom aksi.'
                        ],
                        [
                            'title' => 'Produk Terlaris',
                            'content' => 'Widget produk terlaris menampilkan 5 produk dengan penjualan tertinggi bulan ini. Ini membantu Anda memahami preferensi pelanggan.'
                        ],
                        [
                            'title' => 'Aktivitas Terbaru',
                            'content' => 'Feed aktivitas menampilkan perubahan terbaru dalam sistem, seperti pesanan baru, produk yang ditambahkan, dan perubahan status pesanan.'
                        ],
                        [
                            'title' => 'Grafik Penjualan',
                            'content' => 'Grafik penjualan menampilkan data penjualan harian untuk bulan berjalan. Anda dapat beralih antara melihat jumlah pesanan atau pendapatan.'
                        ]
                    ]
                ],
                [
                    'title' => 'Mengelola Produk',
                    'content' => 'Menu Produk memungkinkan Anda menambah, mengedit, dan menghapus produk yang tersedia untuk pelanggan.',
                    'subsections' => [
                        [
                            'title' => 'Menambah Produk Baru',
                            'content' => 'Untuk menambah produk baru, klik tombol "Tambah Produk" di halaman daftar produk. Isi formulir dengan informasi produk seperti nama, harga, deskripsi, dan kategori. Anda juga dapat mengunggah gambar produk.'
                        ],
                        [
                            'title' => 'Mengedit Produk',
                            'content' => 'Untuk mengedit produk, klik tombol "Edit" di samping produk yang ingin diubah. Anda dapat memperbarui semua informasi produk termasuk gambar.'
                        ],
                        [
                            'title' => 'Menghapus Produk',
                            'content' => 'Untuk menghapus produk, klik tombol "Hapus" di samping produk yang ingin dihapus. Konfirmasi penghapusan saat diminta.'
                        ],
                        [
                            'title' => 'Mengelola Gambar Produk',
                            'content' => 'Anda dapat mengelola gambar produk dengan mengklik menu "Kelola Gambar" di halaman produk. Di sini Anda dapat mengunggah, mengganti, atau menghapus gambar produk.'
                        ]
                    ]
                ],
                [
                    'title' => 'Mengelola Kategori',
                    'content' => 'Menu Kategori memungkinkan Anda mengelompokkan produk ke dalam kategori untuk memudahkan navigasi pelanggan.',
                    'subsections' => [
                        [
                            'title' => 'Menambah Kategori Baru',
                            'content' => 'Untuk menambah kategori baru, klik tombol "Tambah Kategori" di halaman daftar kategori. Isi formulir dengan nama kategori dan deskripsi opsional.'
                        ],
                        [
                            'title' => 'Mengedit Kategori',
                            'content' => 'Untuk mengedit kategori, klik tombol "Edit" di samping kategori yang ingin diubah. Anda dapat memperbarui nama dan deskripsi kategori.'
                        ],
                        [
                            'title' => 'Menghapus Kategori',
                            'content' => 'Untuk menghapus kategori, klik tombol "Hapus" di samping kategori yang ingin dihapus. Perhatikan bahwa menghapus kategori tidak akan menghapus produk yang terkait, tetapi produk tersebut tidak akan memiliki kategori.'
                        ]
                    ]
                ],
                [
                    'title' => 'Mengelola Pesanan',
                    'content' => 'Menu Pesanan memungkinkan Anda melihat dan mengelola semua pesanan yang masuk ke sistem.',
                    'subsections' => [
                        [
                            'title' => 'Melihat Daftar Pesanan',
                            'content' => 'Halaman daftar pesanan menampilkan semua pesanan dengan informasi seperti nomor pesanan, nama pelanggan, total, status, dan tanggal. Anda dapat menggunakan fitur auto-refresh untuk melihat pesanan baru secara otomatis.'
                        ],
                        [
                            'title' => 'Melihat Detail Pesanan',
                            'content' => 'Untuk melihat detail pesanan, klik tombol "Lihat" di samping pesanan yang ingin dilihat. Halaman detail menampilkan informasi lengkap tentang pesanan, termasuk item yang dipesan, harga, dan informasi pelanggan.'
                        ],
                        [
                            'title' => 'Mengubah Status Pesanan',
                            'content' => 'Untuk mengubah status pesanan, klik tombol "Edit" di samping pesanan, lalu pilih status baru dari dropdown (Pending, Diproses, Selesai, atau Dibatalkan) dan simpan perubahan.'
                        ]
                    ]
                ],
                [
                    'title' => 'Pengaturan',
                    'content' => 'Menu Pengaturan memungkinkan Anda mengkonfigurasi berbagai aspek aplikasi Kedai Coffee Kiosk.',
                    'subsections' => [
                        [
                            'title' => 'Informasi Toko',
                            'content' => 'Di sini Anda dapat mengatur informasi dasar toko seperti nama, deskripsi, logo, dan informasi kontak.'
                        ],
                        [
                            'title' => 'Pengaturan Footer',
                            'content' => 'Pengaturan footer memungkinkan Anda mengkonfigurasi konten yang muncul di footer website, seperti teks tentang toko, jam buka, informasi kontak, dan tautan media sosial.'
                        ],
                        [
                            'title' => 'Pengaturan Pembayaran',
                            'content' => 'Di sini Anda dapat mengatur metode pembayaran yang tersedia untuk pelanggan, termasuk pembayaran tunai, QRIS, dan kartu debit/kredit. Anda juga dapat mengatur persentase pajak dan format mata uang.'
                        ],
                        [
                            'title' => 'Tentang Aplikasi',
                            'content' => 'Halaman ini menampilkan informasi tentang aplikasi Kedai Coffee Kiosk, termasuk versi, pengembang, dan hak cipta. Anda juga dapat mengakses panduan pengguna, video tutorial, dan FAQ dari halaman ini.'
                        ]
                    ]
                ]
            ];
        } else { // kiosk
            return [
                [
                    'title' => 'Pengenalan',
                    'content' => 'Selamat datang di Panduan Kiosk Kedai Coffee. Panduan ini akan membantu Anda memahami cara menggunakan sistem pemesanan mandiri untuk memesan makanan dan minuman di kedai kopi kami.'
                ],
                [
                    'title' => 'Memulai Pesanan',
                    'content' => 'Untuk memulai pesanan, sentuh layar kiosk dan pilih tipe pesanan Anda: Dine-in (makan di tempat) atau Take Away (bawa pulang).',
                    'subsections' => [
                        [
                            'title' => 'Dine-in',
                            'content' => 'Pilih opsi ini jika Anda ingin menikmati pesanan di kedai. Anda akan diminta untuk memasukkan nomor meja Anda (opsional).'
                        ],
                        [
                            'title' => 'Take Away',
                            'content' => 'Pilih opsi ini jika Anda ingin membawa pulang pesanan Anda.'
                        ]
                    ]
                ],
                [
                    'title' => 'Memilih Produk',
                    'content' => 'Setelah memilih tipe pesanan, Anda akan melihat daftar kategori produk di sebelah kiri layar dan daftar produk di sebelah kanan.',
                    'subsections' => [
                        [
                            'title' => 'Navigasi Kategori',
                            'content' => 'Sentuh kategori di sebelah kiri untuk melihat produk dalam kategori tersebut. Kategori yang dipilih akan disorot.'
                        ],
                        [
                            'title' => 'Melihat Detail Produk',
                            'content' => 'Sentuh gambar atau nama produk untuk melihat informasi lebih lanjut, termasuk deskripsi lengkap dan harga.'
                        ],
                        [
                            'title' => 'Menambahkan Produk ke Keranjang',
                            'content' => 'Untuk menambahkan produk ke keranjang, sentuh tombol "Tambah" di kartu produk. Anda dapat menambahkan beberapa produk yang sama dengan menyentuh tombol "+" di kartu produk.'
                        ]
                    ]
                ],
                [
                    'title' => 'Keranjang Belanja',
                    'content' => 'Keranjang belanja menampilkan semua item yang telah Anda pilih untuk dipesan.',
                    'subsections' => [
                        [
                            'title' => 'Melihat Keranjang',
                            'content' => 'Sentuh ikon keranjang di bagian atas layar untuk melihat item yang telah Anda tambahkan. Jumlah item dalam keranjang ditampilkan di samping ikon.'
                        ],
                        [
                            'title' => 'Mengubah Jumlah Item',
                            'content' => 'Di halaman keranjang, Anda dapat mengubah jumlah setiap item dengan menyentuh tombol "+" atau "-" di samping item.'
                        ],
                        [
                            'title' => 'Menghapus Item',
                            'content' => 'Untuk menghapus item dari keranjang, sentuh tombol "Hapus" di samping item tersebut.'
                        ],
                        [
                            'title' => 'Melanjutkan Belanja',
                            'content' => 'Jika Anda ingin menambahkan lebih banyak item, sentuh tombol "Lanjutkan Belanja" untuk kembali ke daftar produk.'
                        ],
                        [
                            'title' => 'Checkout',
                            'content' => 'Setelah Anda selesai memilih item, sentuh tombol "Checkout" untuk melanjutkan ke langkah berikutnya.'
                        ]
                    ]
                ],
                [
                    'title' => 'Checkout',
                    'content' => 'Halaman checkout menampilkan ringkasan pesanan Anda dan memungkinkan Anda menyelesaikan pemesanan.',
                    'subsections' => [
                        [
                            'title' => 'Memasukkan Nama',
                            'content' => 'Anda dapat memasukkan nama Anda (opsional) agar staf dapat memanggil Anda saat pesanan siap.'
                        ],
                        [
                            'title' => 'Memilih Metode Pembayaran',
                            'content' => 'Pilih metode pembayaran yang tersedia: Tunai, QRIS, atau Kartu Debit/Kredit.'
                        ],
                        [
                            'title' => 'Menyelesaikan Pesanan',
                            'content' => 'Setelah mengisi informasi yang diperlukan, sentuh tombol "Selesaikan Pesanan" untuk mengirimkan pesanan Anda ke dapur.'
                        ]
                    ]
                ],
                [
                    'title' => 'Konfirmasi Pesanan',
                    'content' => 'Setelah menyelesaikan pesanan, Anda akan melihat halaman konfirmasi dengan nomor pesanan dan instruksi pembayaran.',
                    'subsections' => [
                        [
                            'title' => 'Nomor Pesanan',
                            'content' => 'Nomor pesanan Anda akan ditampilkan dengan jelas. Perhatikan nomor ini karena akan digunakan untuk mengidentifikasi pesanan Anda saat siap.'
                        ],
                        [
                            'title' => 'Instruksi Pembayaran',
                            'content' => 'Tergantung pada metode pembayaran yang Anda pilih, Anda akan melihat instruksi yang sesuai. Untuk pembayaran tunai, bayar di kasir. Untuk QRIS, pindai kode QR yang ditampilkan. Untuk kartu debit/kredit, ikuti instruksi di mesin EDC.'
                        ],
                        [
                            'title' => 'Struk',
                            'content' => 'Struk akan dicetak secara otomatis. Ambil struk Anda sebagai bukti pembayaran dan untuk mengambil pesanan Anda.'
                        ],
                        [
                            'title' => 'Pesanan Baru',
                            'content' => 'Jika Anda ingin membuat pesanan baru, sentuh tombol "Pesanan Baru" untuk kembali ke layar awal.'
                        ]
                    ]
                ]
            ];
        }
    }

    /**
     * Mendapatkan data video berdasarkan tipe
     *
     * @param string $type Tipe video
     * @return array
     */
    private function getVideoData($type)
    {
        if ($type === 'admin') {
            return [
                [
                    'title' => 'Pengenalan Dashboard Admin',
                    'description' => 'Video ini menjelaskan cara menggunakan dashboard admin untuk melihat statistik dan aktivitas terbaru.',
                    'thumbnail' => 'resources/videos/admin_dashboard_thumb.jpg',
                    'video_url' => 'resources/videos/admin_dashboard.mp4',
                    'duration' => '3:45'
                ],
                [
                    'title' => 'Mengelola Produk dan Kategori',
                    'description' => 'Pelajari cara menambah, mengedit, dan menghapus produk serta kategori di panel admin.',
                    'thumbnail' => 'resources/videos/admin_products_thumb.jpg',
                    'video_url' => 'resources/videos/admin_products.mp4',
                    'duration' => '5:20'
                ],
                [
                    'title' => 'Mengelola Pesanan',
                    'description' => 'Video ini menunjukkan cara melihat dan memproses pesanan yang masuk melalui panel admin.',
                    'thumbnail' => 'resources/videos/admin_orders_thumb.jpg',
                    'video_url' => 'resources/videos/admin_orders.mp4',
                    'duration' => '4:10'
                ],
                [
                    'title' => 'Konfigurasi Pengaturan Toko',
                    'description' => 'Pelajari cara mengkonfigurasi informasi toko, footer, dan metode pembayaran.',
                    'thumbnail' => 'resources/videos/admin_settings_thumb.jpg',
                    'video_url' => 'resources/videos/admin_settings.mp4',
                    'duration' => '6:30'
                ]
            ];
        } else { // kiosk
            return [
                [
                    'title' => 'Cara Memesan di Kiosk',
                    'description' => 'Video panduan langkah demi langkah untuk memesan makanan dan minuman di kiosk.',
                    'thumbnail' => 'resources/videos/kiosk_ordering_thumb.jpg',
                    'video_url' => 'resources/videos/kiosk_ordering.mp4',
                    'duration' => '2:30'
                ],
                [
                    'title' => 'Menggunakan Keranjang Belanja',
                    'description' => 'Pelajari cara menambah, mengubah, dan menghapus item dari keranjang belanja Anda.',
                    'thumbnail' => 'resources/videos/kiosk_cart_thumb.jpg',
                    'video_url' => 'resources/videos/kiosk_cart.mp4',
                    'duration' => '1:45'
                ],
                [
                    'title' => 'Proses Checkout dan Pembayaran',
                    'description' => 'Video ini menjelaskan proses checkout dan berbagai metode pembayaran yang tersedia.',
                    'thumbnail' => 'resources/videos/kiosk_checkout_thumb.jpg',
                    'video_url' => 'resources/videos/kiosk_checkout.mp4',
                    'duration' => '3:15'
                ]
            ];
        }
    }

    /**
     * Mendapatkan data FAQ
     *
     * @return array
     */
    private function getFaqData()
    {
        return [
            [
                'category' => 'Umum',
                'questions' => [
                    [
                        'question' => 'Apa itu Kedai Coffee Kiosk?',
                        'answer' => 'Kedai Coffee Kiosk adalah sistem pemesanan mandiri untuk kedai kopi yang memungkinkan pelanggan memesan makanan dan minuman tanpa harus mengantri di kasir. Sistem ini terdiri dari antarmuka kiosk untuk pelanggan dan panel admin untuk pengelola kedai.'
                    ],
                    [
                        'question' => 'Bagaimana cara mengakses panel admin?',
                        'answer' => 'Panel admin dapat diakses melalui URL /admin. Anda perlu memiliki akun admin untuk masuk ke panel ini.'
                    ],
                    [
                        'question' => 'Apakah aplikasi ini mendukung beberapa bahasa?',
                        'answer' => 'Saat ini, aplikasi hanya tersedia dalam Bahasa Indonesia. Dukungan untuk bahasa lain akan ditambahkan di versi mendatang.'
                    ],
                    [
                        'question' => 'Bagaimana cara mendapatkan bantuan teknis?',
                        'answer' => 'Untuk bantuan teknis, Anda dapat menghubungi tim dukungan kami melalui email di alcateambot@gmail.com atau WhatsApp di +6281340078956.'
                    ]
                ]
            ],
            [
                'category' => 'Panel Admin',
                'questions' => [
                    [
                        'question' => 'Bagaimana cara menambahkan produk baru?',
                        'answer' => 'Untuk menambahkan produk baru, masuk ke panel admin, klik menu "Produk", lalu klik tombol "Tambah Produk". Isi formulir dengan informasi produk dan klik "Simpan".'
                    ],
                    [
                        'question' => 'Bagaimana cara mengubah status pesanan?',
                        'answer' => 'Untuk mengubah status pesanan, masuk ke panel admin, klik menu "Pesanan", temukan pesanan yang ingin diubah, klik tombol "Edit", pilih status baru dari dropdown, dan klik "Simpan".'
                    ],
                    [
                        'question' => 'Dapatkah saya mengubah logo toko?',
                        'answer' => 'Ya, Anda dapat mengubah logo toko melalui menu "Pengaturan" > "Informasi Toko". Anda dapat memilih dari ikon bawaan atau mengunggah logo kustom Anda sendiri.'
                    ],
                    [
                        'question' => 'Bagaimana cara mengatur metode pembayaran?',
                        'answer' => 'Untuk mengatur metode pembayaran, masuk ke panel admin, klik menu "Pengaturan" > "Pembayaran". Di sini Anda dapat mengaktifkan atau menonaktifkan metode pembayaran seperti tunai, QRIS, dan kartu debit/kredit.'
                    ],
                    [
                        'question' => 'Apakah saya bisa melihat laporan penjualan?',
                        'answer' => 'Ya, Anda dapat melihat ringkasan penjualan di dashboard admin. Untuk laporan lebih detail, lihat grafik penjualan bulanan dan statistik produk terlaris.'
                    ]
                ]
            ],
            [
                'category' => 'Kiosk',
                'questions' => [
                    [
                        'question' => 'Bagaimana cara memulai pesanan di kiosk?',
                        'answer' => 'Untuk memulai pesanan, sentuh layar kiosk dan pilih tipe pesanan Anda: Dine-in (makan di tempat) atau Take Away (bawa pulang).'
                    ],
                    [
                        'question' => 'Dapatkah saya mengubah pesanan setelah checkout?',
                        'answer' => 'Tidak, setelah Anda menyelesaikan checkout, pesanan langsung dikirim ke dapur. Jika Anda perlu mengubah pesanan, hubungi staf kedai.'
                    ],
                    [
                        'question' => 'Apakah saya harus memasukkan nama saat memesan?',
                        'answer' => 'Memasukkan nama bersifat opsional, tetapi sangat disarankan agar staf dapat memanggil Anda saat pesanan siap.'
                    ],
                    [
                        'question' => 'Metode pembayaran apa saja yang tersedia?',
                        'answer' => 'Metode pembayaran yang tersedia tergantung pada konfigurasi kedai. Umumnya, tersedia pembayaran tunai, QRIS, dan kartu debit/kredit.'
                    ],
                    [
                        'question' => 'Bagaimana saya tahu pesanan saya siap?',
                        'answer' => 'Setelah pesanan Anda siap, nomor pesanan Anda akan dipanggil atau ditampilkan di layar pengambilan pesanan. Pastikan Anda mengingat nomor pesanan Anda.'
                    ]
                ]
            ],
            [
                'category' => 'Teknis',
                'questions' => [
                    [
                        'question' => 'Apakah aplikasi ini berjalan secara online atau offline?',
                        'answer' => 'Aplikasi ini membutuhkan koneksi internet untuk berfungsi dengan baik, terutama untuk sinkronisasi data antara kiosk dan panel admin.'
                    ],
                    [
                        'question' => 'Perangkat apa yang didukung untuk kiosk?',
                        'answer' => 'Kiosk dapat dijalankan pada tablet atau layar sentuh dengan browser modern. Untuk pengalaman terbaik, gunakan perangkat dengan layar minimal 10 inci.'
                    ],
                    [
                        'question' => 'Apakah aplikasi ini terintegrasi dengan printer struk?',
                        'answer' => 'Ya, aplikasi ini dapat dikonfigurasi untuk terhubung dengan printer struk termal untuk mencetak bukti pesanan.'
                    ],
                    [
                        'question' => 'Bagaimana cara memperbarui aplikasi ke versi terbaru?',
                        'answer' => 'Untuk memperbarui aplikasi, hubungi tim dukungan kami. Mereka akan membantu Anda melakukan proses pembaruan dengan aman.'
                    ],
                    [
                        'question' => 'Apakah data pesanan disimpan secara lokal atau di cloud?',
                        'answer' => 'Data pesanan disimpan dalam database lokal di server Anda. Disarankan untuk melakukan backup database secara berkala.'
                    ]
                ]
            ]
        ];
    }
}
