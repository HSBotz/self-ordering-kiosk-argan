<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Periksa apakah kolom variant_type ada, jika tidak tambahkan
        try {
            $columnExists = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
                                        WHERE TABLE_SCHEMA = DATABASE()
                                        AND TABLE_NAME = 'order_items'
                                        AND COLUMN_NAME = 'variant_type'");

            if ($columnExists[0]->count == 0) {
                Log::info('Menambahkan kolom variant_type ke tabel order_items');
                DB::statement('ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes');

                // Update nilai variant_type
                DB::statement("UPDATE order_items SET variant_type = 'hot' WHERE id % 2 = 0");
                DB::statement("UPDATE order_items SET variant_type = 'ice' WHERE id % 2 = 1");
            }
        } catch (\Exception $e) {
            Log::error('Error saat memeriksa/memperbaiki kolom variant_type: ' . $e->getMessage());
        }

        // Ambil daftar pesanan dengan eager load item, produk, dan kategori
        $orders = Order::with(['orderItems.product.category'])->orderBy('created_at', 'desc')->paginate(10);

        // Perbaiki varian untuk semua pesanan yang ditampilkan
        foreach ($orders as $order) {
            $updated = 0;

            foreach ($order->orderItems as $item) {
                // Jika produk ada dan kategorinya tersedia
                if ($item->product && $item->product->category) {
                    // Cek apakah kategori produk memiliki varian (hot/ice)
                    if (!$item->product->category->has_variants) {
                        // Jika kategori tidak memiliki varian, pastikan variant_type adalah null
                        if ($item->variant_type !== null) {
                            $item->variant_type = null;
                            $item->save();
                            $updated++;
                        }
                    } else if ($item->variant_type === null) {
                        // Jika kategori memiliki varian tapi variant_type masih null, atur default
                        $variantType = ($item->id % 2 == 0) ? 'hot' : 'ice';
                        $item->variant_type = $variantType;
                        $item->save();
                        $updated++;
                    }
                }
            }

            if ($updated > 0) {
                Log::info("Memperbaiki $updated item varian di pesanan #{$order->id} saat menampilkan daftar");
            }

            // Debug: Tampilkan varian untuk setiap pesanan
            Log::info('Order #' . $order->id . ' variants:', [
                'hot_items' => $order->orderItems->where('variant_type', 'hot')->count(),
                'ice_items' => $order->orderItems->where('variant_type', 'ice')->count(),
                'null_items' => $order->orderItems->whereNull('variant_type')->count(),
                'variant_types' => $order->orderItems->pluck('variant_type')->toArray(),
                'categories' => $order->orderItems->map(function($item) {
                    return [
                        'product' => $item->product->name ?? 'Unknown',
                        'category' => $item->product->category->name ?? 'Unknown',
                        'has_variants' => $item->product->category->has_variants ?? false
                    ];
                })
            ]);
        }

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->route('admin.orders.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,qris',
            'order_type' => 'required|in:dine-in,take-away',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // Generate order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

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

        // Add order items
        if ($request->has('products') && is_array($request->products)) {
            foreach ($request->products as $productData) {
                if (isset($productData['id'], $productData['quantity'], $productData['price'])) {
                    $orderItem = [
                        'order_id' => $order->id,
                        'product_id' => $productData['id'],
                        'quantity' => $productData['quantity'],
                        'price' => $productData['price'],
                        'notes' => $productData['notes'] ?? null,
                        'variant_type' => $productData['variant_type'] ?? null,
                    ];

                    OrderItem::create($orderItem);
                }
            }
        }

        // Redirect to order confirmation
        return redirect()->route('kiosk.order.confirmation', $order->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Ambil pesanan dengan eager loading untuk item dan produk
        $order = Order::with(['orderItems.product.category'])->findOrFail($id);

        // Periksa dan perbaiki varian yang NULL dengan mempertimbangkan kategori produk
        $nullItems = $order->orderItems()->whereNull('variant_type')->get();
        if ($nullItems->count() > 0) {
            Log::info("Memperbaiki {$nullItems->count()} item tanpa varian di pesanan #$id");

            foreach ($nullItems as $item) {
                // Jika produk ada dan kategorinya tersedia
                if ($item->product && $item->product->category) {
                    // Cek apakah kategori produk memiliki varian (hot/ice)
                    if ($item->product->category->has_variants) {
                        // Jika kategori memiliki varian, atur default varian (hot/ice)
                        $variantType = ($item->id % 2 == 0) ? 'hot' : 'ice';
                        $item->variant_type = $variantType;
                        Log::info("Produk dari kategori dengan varian: set '{$variantType}' untuk item #{$item->id}");
                    } else {
                        // Jika kategori tidak memiliki varian, set variant_type ke null
                        $item->variant_type = null;
                        Log::info("Produk dari kategori tanpa varian: set 'null' untuk item #{$item->id}");
                    }
                } else {
                    // Jika produk atau kategori tidak ditemukan, gunakan default
                    $variantType = ($item->id % 2 == 0) ? 'hot' : 'ice';
                    $item->variant_type = $variantType;
                    Log::info("Produk atau kategori tidak ditemukan: set '{$variantType}' untuk item #{$item->id}");
                }

                $item->save();
            }

            // Refresh model untuk mendapatkan data terbaru
            $order->refresh();
        }

        // Debug order items untuk melihat nilai varian
        Log::info('Order items dengan varian:', [
            'order_id' => $id,
            'items' => $order->orderItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product->name ?? 'Unknown',
                    'category' => $item->product->category->name ?? 'Unknown',
                    'has_variants' => $item->product->category->has_variants ?? false,
                    'variant_type' => $item->variant_type
                ];
            })->toArray()
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Cek apakah ini adalah request update varian
        if ($request->has('update_variants')) {
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $itemId => $variantType) {
                    $orderItem = OrderItem::where('order_id', $id)
                        ->where('id', $itemId)
                        ->first();

                    if ($orderItem) {
                        $orderItem->variant_type = $variantType ?: null;
                        $orderItem->save();

                        Log::info("Varian berhasil diupdate", [
                            'item_id' => $itemId,
                            'variant' => $variantType ?: 'NULL'
                        ]);
                    }
                }
                return redirect()->route('admin.orders.show', $id)->with('success', 'Varian berhasil diperbarui.');
            }
        }

        // Jika bukan update varian, proses update status seperti biasa
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.orders.show', $id)->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    /**
     * Remove multiple orders at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(Request $request)
    {
        // Log input untuk debugging
        Log::info('Bulk destroy request data:', $request->all());
        Log::info('Request method: ' . $request->method());
        Log::info('Request path: ' . $request->path());

        if (!$request->has('order_ids')) {
            Log::error('Bulk destroy failed: No order_ids provided');
            return redirect()->route('admin.orders.index')
                ->with('error', 'Tidak ada pesanan yang dipilih untuk dihapus.');
        }

        try {
            $orderIds = $request->input('order_ids');

            // Pastikan order_ids adalah array
            if (!is_array($orderIds)) {
                $orderIds = [$orderIds];
            }

            // Filter ID kosong
            $orderIds = array_filter($orderIds, function($id) {
                return !empty($id);
            });

            $count = count($orderIds);

            if ($count === 0) {
                Log::error('Bulk destroy failed: Empty order_ids array');
                return redirect()->route('admin.orders.index')
                    ->with('error', 'Tidak ada pesanan yang dipilih untuk dihapus.');
            }

            Log::info('Attempting to delete orders', [
                'count' => $count,
                'ids' => $orderIds
            ]);

            DB::beginTransaction();

            // Hapus semua item pesanan terlebih dahulu
            $deletedItems = OrderItem::whereIn('order_id', $orderIds)->delete();
            // Kemudian hapus pesanannya
            $deletedOrders = Order::whereIn('id', $orderIds)->delete();

            Log::info('Orders deleted successfully', [
                'items_deleted' => $deletedItems,
                'orders_deleted' => $deletedOrders
            ]);

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', $count . ' pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting multiple orders: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.orders.index')
                ->with('error', 'Gagal menghapus pesanan. Error: ' . $e->getMessage());
        }
    }

    /**
     * Get latest orders for API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestOrders()
    {
        $orders = Order::with(['orderItems.product.category'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Transformasi data agar menyertakan detail item pesanan
        $transformedOrders = $orders->map(function($order) {
            $orderData = $order->toArray();

            // Tambahkan nama produk dan informasi varian ke order_items
            $orderData['order_items'] = $order->orderItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Produk tidak tersedia',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'variant_type' => $item->variant_type,
                    'notes' => $item->notes,
                    'product' => [
                        'name' => $item->product->name ?? 'Produk tidak tersedia',
                        'category' => [
                            'name' => $item->product->category->name ?? 'Tanpa kategori',
                            'has_variants' => $item->product->category->has_variants ?? false
                        ]
                    ]
                ];
            });

            return $orderData;
        });

        return response()->json([
            'success' => true,
            'orders' => $transformedOrders,
            'timezone' => \App\Models\SiteSetting::getValue('store_timezone', 'WIB')
        ]);
    }

    /**
     * Fix variants for item with null variant_type
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function fixVariants($id = null)
    {
        if ($id) {
            // Fix untuk satu pesanan
            $order = Order::with(['orderItems.product.category'])->findOrFail($id);

            $updated = 0;
            foreach ($order->orderItems as $item) {
                if ($item->product && $item->product->category) {
                    // Cek apakah kategori memiliki varian
                    if ($item->product->category->has_variants) {
                        if ($item->variant_type === null) {
                            // Set item dengan ID genap sebagai HOT, ganjil sebagai ICE
                            $variantType = ($item->id % 2 == 0) ? 'hot' : 'ice';
                            $item->variant_type = $variantType;
                            $item->save();
                            $updated++;
                        }
                    } else {
                        // Jika kategori tidak memiliki varian, pastikan variant_type adalah null
                        if ($item->variant_type !== null) {
                            $item->variant_type = null;
                            $item->save();
                            $updated++;
                        }
                    }
                } else {
                    // Jika produk atau kategori tidak ditemukan, gunakan default
                    if ($item->variant_type === null) {
                        $variantType = ($item->id % 2 == 0) ? 'hot' : 'ice';
                        $item->variant_type = $variantType;
                        $item->save();
                        $updated++;
                    }
                }
            }

            return redirect()->route('admin.orders.show', $id)->with('success', "$updated item berhasil diupdate dengan varian otomatis.");
        } else {
            // Fix global
            $items = OrderItem::with(['product.category'])->get();
            $updated = 0;

            foreach ($items as $item) {
                if ($item->product && $item->product->category) {
                    // Cek apakah kategori memiliki varian
                    if ($item->product->category->has_variants) {
                        if ($item->variant_type === null) {
                            // Set item dengan ID genap sebagai HOT, ganjil sebagai ICE
                            $variantType = ($item->id % 2 == 0) ? 'hot' : 'ice';
                            $item->variant_type = $variantType;
                            $item->save();
                            $updated++;
                        }
                    } else {
                        // Jika kategori tidak memiliki varian, pastikan variant_type adalah null
                        if ($item->variant_type !== null) {
                            $item->variant_type = null;
                            $item->save();
                            $updated++;
                        }
                    }
                } else {
                    // Jika produk atau kategori tidak ditemukan, gunakan default
                    if ($item->variant_type === null) {
                        $variantType = ($item->id % 2 == 0) ? 'hot' : 'ice';
                        $item->variant_type = $variantType;
                        $item->save();
                        $updated++;
                    }
                }
            }

            return redirect()->route('admin.orders.index')->with('success', "Berhasil memperbaiki $updated item varian secara otomatis.");
        }
    }
}
