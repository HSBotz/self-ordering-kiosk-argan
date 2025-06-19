<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class TrackController extends Controller
{
    /**
     * Menampilkan halaman pelacakan pesanan.
     *
     * @param  string  $orderNumber
     * @return \Illuminate\Http\Response
     */
    public function trackOrder($orderNumber)
    {
        // Logging untuk debug
        Log::info('Tracking order accessed', [
            'orderNumber' => $orderNumber,
            'requestUrl' => request()->fullUrl(),
            'method' => request()->method()
        ]);

        try {
            $order = Order::where('order_number', $orderNumber)
                ->with('orderItems.product')
                ->first();

            if (!$order) {
                Log::warning('Order not found when tracking', ['orderNumber' => $orderNumber]);
                return redirect()->route('kiosk.index')
                    ->with('error', 'Pesanan tidak ditemukan.');
            }

            // Ambil pengaturan toko
            $storeSettings = DB::table('site_settings')
                ->pluck('value', 'key')
                ->toArray();

            // Logging untuk debug
            Log::info('Order found, rendering track_order view', [
                'orderNumber' => $orderNumber,
                'orderStatus' => $order->status,
                'orderItems' => $order->orderItems->count()
            ]);

            // Cek apakah view track_order.blade.php ada
            if (!View::exists('kiosk.track_order')) {
                Log::error('View kiosk.track_order tidak ditemukan');
                return response()->view('errors.custom', [
                    'title' => 'Error View',
                    'message' => 'View untuk pelacakan pesanan tidak ditemukan.',
                    'storeSettings' => $storeSettings
                ], 500);
            }

            // Tampilkan view dengan data order
            return view('kiosk.track_order', compact('order', 'storeSettings'));
        } catch (\Exception $e) {
            Log::error('Error in trackOrder method', [
                'orderNumber' => $orderNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Ambil pengaturan toko jika memungkinkan
            try {
                $storeSettings = DB::table('site_settings')
                    ->pluck('value', 'key')
                    ->toArray();
            } catch (\Exception $dbEx) {
                $storeSettings = [];
            }

            return response()->view('errors.custom', [
                'title' => 'Error Sistem',
                'message' => 'Terjadi kesalahan saat melacak pesanan: ' . $e->getMessage(),
                'storeSettings' => $storeSettings
            ], 500);
        }
    }

    /**
     * Generate QR Code untuk pelacakan pesanan.
     *
     * @param  string  $orderNumber
     * @return \Illuminate\Http\Response
     */
    public function generateTrackingQrCode($orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::warning('Order not found when generating QR code', ['orderNumber' => $orderNumber]);
                abort(404);
            }

            // Gunakan URL lengkap dan absolut untuk QR Code
            $trackingUrl = url("/track/{$orderNumber}");

            // Logging untuk debug
            Log::info('Generating QR code', [
                'orderNumber' => $orderNumber,
                'trackingUrl' => $trackingUrl
            ]);

            // Gunakan library JavaScript untuk QR code, bukan library PHP
            return response("QR Code for {$trackingUrl}")
                ->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            Log::error('Error in generateTrackingQrCode method', [
                'orderNumber' => $orderNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Error generating QR code: ' . $e->getMessage(), 500);
        }
    }
}
