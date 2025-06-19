<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('category')->get();
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:204800',
            'image_cropped' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'is_available' => $request->has('is_available'),
        ];

        // Handle image upload
        if ($request->filled('image_cropped')) {
            // Handle cropped image from base64
            $imageData = $request->input('image_cropped');

            // Get content after the comma
            $imageData = substr($imageData, strpos($imageData, ',') + 1);

            // Decode base64 data
            $decodedImage = base64_decode($imageData);

            // Generate a unique filename
            $filename = 'products/' . Str::uuid() . '.jpg';

            // Store the file
            Storage::disk('public')->put($filename, $decodedImage);

            $data['image'] = $filename;
        } elseif ($request->hasFile('image')) {
            // Handle regular file upload if no cropped image
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('admin.products.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:204800',
            'image_cropped' => 'nullable|string',
            'delete_image' => 'nullable|boolean',
        ]);

        $product = Product::findOrFail($id);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'is_available' => $request->has('is_available'),
        ];

        // Handle image upload, cropping, or deletion
        if ($request->filled('image_cropped')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Handle cropped image from base64
            $imageData = $request->input('image_cropped');

            // Get content after the comma
            $imageData = substr($imageData, strpos($imageData, ',') + 1);

            // Decode base64 data
            $decodedImage = base64_decode($imageData);

            // Generate a unique filename
            $filename = 'products/' . Str::uuid() . '.jpg';

            // Store the file
            Storage::disk('public')->put($filename, $decodedImage);

            $data['image'] = $filename;
        } elseif ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        } elseif ($request->has('delete_image') && $request->delete_image) {
            // Delete image if requested
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete product image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
