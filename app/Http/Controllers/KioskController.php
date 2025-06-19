<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KioskController extends Controller
{
    public function index(Request $request)
    {
        // Cek jika user belum memilih tipe pesanan dan langsung ke /kiosk
        if (!$request->session()->has('order_type')) {
            return redirect()->route('kiosk.order-type');
        }

        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_available', true)->with('category')->get();

        // Ambil pengaturan pembayaran dari database
        $paymentSettings = DB::table('site_settings')
            ->where('group', 'payment')
            ->pluck('value', 'key')
            ->toArray();

        return view('kiosk.index', compact('categories', 'products', 'paymentSettings'));
    }

    public function cart()
    {
        // Ambil pengaturan pembayaran dari database
        $paymentSettings = DB::table('site_settings')
            ->where('group', 'payment')
            ->pluck('value', 'key')
            ->toArray();

        return view('kiosk.cart', compact('paymentSettings'));
    }

    public function checkout(Request $request)
    {
        // Ambil pengaturan pembayaran dari database
        $paymentSettings = DB::table('site_settings')
            ->where('group', 'payment')
            ->pluck('value', 'key')
            ->toArray();

        return view('kiosk.checkout', compact('paymentSettings'));
    }

    public function processOrder(Request $request)
    {
        $request->validate([
            'cart_items' => 'required|json',
            'customer_name' => 'nullable|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris,card',
            'order_type' => 'required|in:dine-in,take-away',
            'notes' => 'nullable|string',
        ]);

        // Decode order items dari JSON
        $orderItems = json_decode($request->cart_items, true);

        if (empty($orderItems)) {
            return redirect()->route('kiosk.cart')->with('error', 'Keranjang Anda kosong.');
        }

        // Log cart_items untuk debug
        Log::info('Cart items received:', [
            'cart_items' => $request->cart_items
        ]);

        // Ambil pengaturan pembayaran dari database untuk validasi
        $paymentSettings = DB::table('site_settings')
            ->where('group', 'payment')
            ->pluck('value', 'key')
            ->toArray();

        // Validasi metode pembayaran aktif
        $paymentMethod = $request->payment_method;
        $paymentEnabled = false;

        if ($paymentMethod === 'cash' && isset($paymentSettings['payment_cash_enabled']) && $paymentSettings['payment_cash_enabled'] === '1') {
            $paymentEnabled = true;
        } else if ($paymentMethod === 'qris' && isset($paymentSettings['payment_qris_enabled']) && $paymentSettings['payment_qris_enabled'] === '1') {
            $paymentEnabled = true;
        } else if ($paymentMethod === 'card' && isset($paymentSettings['payment_debit_enabled']) && $paymentSettings['payment_debit_enabled'] === '1') {
            $paymentEnabled = true;
        }

        if (!$paymentEnabled) {
            return redirect()->route('kiosk.checkout')->with('error', 'Metode pembayaran yang dipilih tidak tersedia. Silakan pilih metode pembayaran lain.');
        }

        // Generate order number
        $orderNumber = 'ORD' . date('Ymd') . rand(100, 999);

        // Create order
        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $request->customer_name,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'order_type' => $request->order_type,
            'notes' => $request->notes,
        ]);

        // Simpan item pesanan
        foreach ($orderItems as $item) {
            // Debug informasi item
            Log::info('Processing order item:', [
                'item' => $item,
                'variantType' => isset($item['variantType']) ? $item['variantType'] : null
            ]);

            // Pastikan variant_type yang disimpan dalam format yang benar
            $variantType = null;
            if (isset($item['variantType']) && !empty($item['variantType'])) {
                // Normalisasi nilai variantType menjadi lowercase dan trim
                $value = strtolower(trim($item['variantType']));

                // Hanya terima nilai 'hot' atau 'ice'
                if ($value === 'hot' || $value === 'ice') {
                    $variantType = $value;
                    Log::info('Valid variant type found', ['variant' => $value]);
                } else {
                    // Log jika ada nilai lain
                    Log::warning('Invalid variant type', ['value' => $value]);
                }
            }

            // Buat order item
            try {
                $orderItem = [
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'notes' => $item['notes'] ?? null,
                    'variant_type' => $variantType, // Nilai sudah divalidasi
                ];

                // Log data sebelum menyimpan ke database
                Log::info('Saving order item with data:', $orderItem);

                OrderItem::create($orderItem);
            } catch (\Exception $e) {
                Log::error('Error saving order item: ' . $e->getMessage(), [
                    'item' => $item,
                    'variantType' => $variantType
                ]);
                // Lanjutkan ke item berikutnya
            }
        }

        // Simpan order number di session untuk halaman sukses
        session()->put('order_number', $orderNumber);

        // Tambahkan script untuk mengosongkan keranjang
        session()->put('clear_cart', true);

        return redirect()->route('kiosk.success');
    }

    public function success()
    {
        if (!session()->has('order_number')) {
            return redirect()->route('kiosk.index');
        }

        $orderNumber = session('order_number');
        $order = Order::where('order_number', $orderNumber)
            ->with('orderItems.product')
            ->first();

        if (!$order) {
            return redirect()->route('kiosk.index');
        }

        // Generate tracking URL dengan menggunakan host yang sama dengan permintaan saat ini
        $host = request()->getHost();
        $port = request()->getPort();
        $portSuffix = ($port && $port != 80 && $port != 443) ? ":{$port}" : "";
        $protocol = request()->isSecure() ? 'https' : 'http';
        $trackingUrl = "{$protocol}://{$host}{$portSuffix}/track/{$orderNumber}";

        // Log untuk debugging
        Log::info('Generating tracking URL for order', [
            'orderNumber' => $orderNumber,
            'trackingUrl' => $trackingUrl,
            'host' => $host,
            'port' => $port,
            'protocol' => $protocol
        ]);

        return view('kiosk.success', compact('order', 'trackingUrl'));
    }

    /**
     * Menampilkan halaman pemilihan tipe pesanan.
     *
     * @return \Illuminate\Http\Response
     */
    public function orderType()
    {
        return view('kiosk.order_type');
    }

    /**
     * Memproses pemilihan tipe pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processOrderType(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:dine-in,take-away',
        ]);

        session(['order_type' => $request->order_type]);

        return redirect()->route('kiosk.index');
    }
}
