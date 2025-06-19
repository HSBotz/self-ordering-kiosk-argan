<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TrackApiController extends Controller
{
    /**
     * Mendapatkan status pesanan untuk pelacakan AJAX.
     *
     * @param  string  $orderNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderStatus($orderNumber)
    {
        try {
            // Log permintaan
            Log::info('API Track Order accessed', [
                'orderNumber' => $orderNumber,
                'requestUrl' => request()->fullUrl(),
                'method' => request()->method(),
                'timestamp' => now()->format('Y-m-d H:i:s.u')
            ]);

            // Reset cache query untuk memastikan data terbaru
            DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");

            // Cari pesanan berdasarkan nomor pesanan dengan no cache
            $order = Order::where('order_number', $orderNumber)
                ->with('orderItems.product')
                ->useWritePdo()  // Menggunakan write connection untuk memastikan data terbaru
                ->first();

            // Jika pesanan tidak ditemukan
            if (!$order) {
                Log::warning('API Track: Order not found', ['orderNumber' => $orderNumber]);
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan',
                    'timestamp' => now()->timestamp
                ], 404);
            }

            // Refresh model untuk memastikan data terbaru
            $order->refresh();

            // Debug log status pesanan
            Log::info('API Track: Order status fetched', [
                'orderNumber' => $orderNumber,
                'currentStatus' => $order->status,
                'lastUpdated' => $order->updated_at->format('Y-m-d H:i:s')
            ]);

            // Format data pesanan untuk respons
            $formattedOrder = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'customer_name' => $order->customer_name,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'order_type' => $order->order_type,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'updated_at' => $order->updated_at->format('d/m/Y H:i:s')
            ];

            // Return respons JSON dengan timestamp untuk mencegah cache
            return response()->json([
                'success' => true,
                'order' => $formattedOrder,
                'server_time' => now()->format('Y-m-d H:i:s'),
                'timestamp' => now()->timestamp
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');
        } catch (\Exception $e) {
            // Log error
            Log::error('API Track: Error getting order status', [
                'orderNumber' => $orderNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return respons error
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa status pesanan',
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp
            ], 500)->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        }
    }
}
