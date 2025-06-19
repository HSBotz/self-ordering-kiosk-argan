<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Menampilkan halaman upload gambar produk
     */
    public function index()
    {
        $products = Product::all();
        return view('admin.products.images', compact('products'));
    }

    /**
     * Menyimpan gambar produk
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:204800',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Hapus gambar lama jika ada
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Upload gambar baru
        $imagePath = $request->file('image')->store('products', 'public');
        $product->update(['image' => $imagePath]);

        return redirect()->back()->with('success', 'Gambar produk berhasil diupload.');
    }

    /**
     * Menghapus gambar produk
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
            $product->update(['image' => null]);
        }

        return redirect()->back()->with('success', 'Gambar produk berhasil dihapus.');
    }
}
