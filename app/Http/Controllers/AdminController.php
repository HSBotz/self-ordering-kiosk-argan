<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin
     */
    public function dashboard()
    {
        // Mengambil total orders
        $totalOrders = Order::count();

        // Mengambil total products
        $totalProducts = Product::count();

        // Mengambil total categories
        $totalCategories = Category::count();

        // Mengambil total pendapatan
        $totalRevenue = Order::where('status', '!=', 'cancelled')
                            ->sum('total_amount');

        // Mengambil pesanan terbaru
        $recentOrders = Order::latest()
                            ->take(5)
                            ->get();

        // Mengambil produk terlaris
        $popularProducts = Product::withCount(['orderItems as sold' => function($query) {
                                $query->whereHas('order', function($q) {
                                    $q->where('status', '!=', 'cancelled');
                                });
                            }])
                            ->orderBy('sold', 'desc')
                            ->take(5)
                            ->get();

        // Mengambil aktivitas terbaru dari database
        $activities = Activity::latest()->take(5)->get();

        // Jika tidak ada aktivitas, buat beberapa aktivitas dummy untuk ditampilkan
        if ($activities->isEmpty()) {
            // Buat beberapa aktivitas dummy
            $this->createSampleActivities();
            $activities = Activity::latest()->take(5)->get();
        }

        // Mengambil data penjualan bulanan untuk chart
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $daysInMonth = Carbon::now()->daysInMonth;

        // Data penjualan harian dalam bulan ini
        $dailySales = $this->getDailySalesData($currentMonth, $currentYear, $daysInMonth);

        // Data pendapatan harian dalam bulan ini
        $dailyRevenue = $this->getDailyRevenueData($currentMonth, $currentYear, $daysInMonth);

        // Produk terlaris bulan ini
        $topProductsThisMonth = $this->getTopProductsThisMonth($currentMonth, $currentYear);

        // Statistik penjualan bulanan (6 bulan terakhir)
        $monthlySalesStats = $this->getMonthlySalesStats();

        // Nama bulan saat ini untuk judul
        $currentMonthName = Carbon::now()->translatedFormat('F Y');

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalCategories',
            'totalRevenue',
            'recentOrders',
            'popularProducts',
            'activities',
            'dailySales',
            'dailyRevenue',
            'topProductsThisMonth',
            'monthlySalesStats',
            'currentMonthName',
            'daysInMonth'
        ));
    }

    /**
     * Membuat aktivitas sampel untuk ditampilkan
     */
    private function createSampleActivities()
    {
        // Cek apakah ada pesanan
        $latestOrder = Order::latest()->first();
        if ($latestOrder) {
            Activity::log('order', 'Pesanan baru #' . $latestOrder->order_number . ' telah dibuat', $latestOrder);
        }

        // Cek apakah ada produk
        $latestProduct = Product::latest()->first();
        if ($latestProduct) {
            Activity::log('product', 'Produk ' . $latestProduct->name . ' ditambahkan', $latestProduct);
        }

        // Cek apakah ada kategori
        $latestCategory = Category::latest()->first();
        if ($latestCategory) {
            Activity::log('category', 'Kategori ' . $latestCategory->name . ' diperbarui', $latestCategory);
        }

        // Tambahkan aktivitas umum
        Activity::log('system', 'Sistem berhasil diperbarui');
    }

    /**
     * Mendapatkan data penjualan harian dalam bulan ini
     */
    private function getDailySalesData($month, $year, $daysInMonth)
    {
        // Inisialisasi array untuk data penjualan harian
        $dailySales = array_fill(1, $daysInMonth, 0);

        // Query untuk mendapatkan jumlah pesanan per hari dalam bulan ini
        $salesData = Order::selectRaw('DAY(created_at) as day, COUNT(*) as count')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', '!=', 'cancelled')
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get();

        // Isi array dengan data dari database
        foreach ($salesData as $sale) {
            $dailySales[$sale->day] = $sale->count;
        }

        return $dailySales;
    }

    /**
     * Mendapatkan data pendapatan harian dalam bulan ini
     */
    private function getDailyRevenueData($month, $year, $daysInMonth)
    {
        // Inisialisasi array untuk data pendapatan harian
        $dailyRevenue = array_fill(1, $daysInMonth, 0);

        // Query untuk mendapatkan total pendapatan per hari dalam bulan ini
        $revenueData = Order::selectRaw('DAY(created_at) as day, SUM(total_amount) as total')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', '!=', 'cancelled')
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get();

        // Isi array dengan data dari database
        foreach ($revenueData as $revenue) {
            $dailyRevenue[$revenue->day] = $revenue->total;
        }

        return $dailyRevenue;
    }

    /**
     * Mendapatkan produk terlaris bulan ini
     */
    private function getTopProductsThisMonth($month, $year)
    {
        return DB::table('products')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->whereMonth('orders.created_at', $month)
            ->whereYear('orders.created_at', $year)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    /**
     * Mendapatkan statistik penjualan bulanan (6 bulan terakhir)
     */
    private function getMonthlySalesStats()
    {
        $stats = [];
        $now = Carbon::now();

        // Ambil data 6 bulan terakhir
        for ($i = 0; $i < 6; $i++) {
            $month = $now->copy()->subMonths($i);
            $monthName = $month->translatedFormat('M Y');
            $monthNumber = $month->month;
            $year = $month->year;

            // Jumlah pesanan
            $orderCount = Order::whereMonth('created_at', $monthNumber)
                ->whereYear('created_at', $year)
                ->where('status', '!=', 'cancelled')
                ->count();

            // Total pendapatan
            $revenue = Order::whereMonth('created_at', $monthNumber)
                ->whereYear('created_at', $year)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');

            $stats[] = [
                'month' => $monthName,
                'order_count' => $orderCount,
                'revenue' => $revenue
            ];
        }

        // Balik urutan array agar bulan terlama ada di awal
        return array_reverse($stats);
    }

    /**
     * API untuk mendapatkan data dashboard secara realtime
     */
    public function getDashboardData()
    {
        // Mengambil total orders
        $totalOrders = Order::count();

        // Mengambil total pendapatan
        $totalRevenue = Order::where('status', '!=', 'cancelled')
                            ->sum('total_amount');

        // Mengambil pesanan terbaru
        $recentOrders = Order::with('orderItems.product')
                            ->latest()
                            ->take(5)
                            ->get();

        // Mengambil aktivitas terbaru
        $activities = Activity::latest()->take(5)->get();

        // Data penjualan bulan ini
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $daysInMonth = Carbon::now()->daysInMonth;
        $dailySales = $this->getDailySalesData($currentMonth, $currentYear, $daysInMonth);
        $dailyRevenue = $this->getDailyRevenueData($currentMonth, $currentYear, $daysInMonth);

        return response()->json([
            'success' => true,
            'data' => [
                'totalOrders' => $totalOrders,
                'totalRevenue' => $totalRevenue,
                'recentOrders' => $recentOrders,
                'activities' => $activities,
                'dailySales' => array_values($dailySales),
                'dailyRevenue' => array_values($dailyRevenue),
            ]
        ]);
    }

    /**
     * API untuk mendapatkan data aktivitas terbaru secara realtime
     */
    public function getActivitiesData()
    {
        // Mengambil aktivitas terbaru
        $activities = Activity::latest()->take(10)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'activities' => $activities
            ]
        ]);
    }
}
