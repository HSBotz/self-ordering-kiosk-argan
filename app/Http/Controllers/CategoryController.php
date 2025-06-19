<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Constructor untuk memeriksa dan menambahkan kolom icon dan has_variants jika belum ada
     */
    public function __construct()
    {
        // Periksa apakah kolom icon sudah ada di tabel categories
        if (!Schema::hasColumn('categories', 'icon')) {
            // Tambahkan kolom icon
            Schema::table('categories', function ($table) {
                $table->string('icon')->nullable()->after('image');
            });
        }

        // Periksa apakah kolom has_variants sudah ada di tabel categories
        if (!Schema::hasColumn('categories', 'has_variants')) {
            // Tambahkan kolom has_variants
            Schema::table('categories', function ($table) {
                $table->boolean('has_variants')->default(false)->after('is_active');
            });
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:204800',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'has_variants' => 'nullable|boolean',
            'media_type' => 'required|in:icon,image',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'has_variants' => $request->has('has_variants') ? true : false,
        ];

        // Berdasarkan tipe media yang dipilih
        if ($request->media_type === 'icon') {
            $data['icon'] = $request->icon;
            // Hapus gambar jika ada (misalnya jika beralih dari gambar ke icon)
            $data['image'] = null;
        } else {
            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('categories', 'public');
                $data['image'] = $path;
            }
            // Hapus icon jika ada (misalnya jika beralih dari icon ke gambar)
            $data['icon'] = null;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('admin.categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:204800',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'has_variants' => 'nullable|boolean',
            'remove_image' => 'nullable|boolean',
        ]);

        $category = Category::findOrFail($id);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'has_variants' => $request->has('has_variants') ? true : false,
        ];

        // Periksa apakah kita perlu menghapus gambar saat ini
        if ($request->has('remove_image') && $request->remove_image) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = null;
        }

        // Periksa apakah tab yang aktif adalah icon atau image
        if ($request->has('media_type')) {
            if ($request->media_type === 'icon' && $request->has('icon')) {
                $data['icon'] = $request->icon;
                // Jika menggunakan icon, hapus gambar
                if ($category->image && !$request->hasFile('image')) {
                    Storage::disk('public')->delete($category->image);
                    $data['image'] = null;
                }
            } elseif ($request->media_type === 'image') {
                // Hapus icon jika ada
                $data['icon'] = null;
            }
        } else {
            // Tanpa media_type, periksa yang disubmit
            if ($request->has('icon')) {
                $data['icon'] = $request->icon;
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
            // Jika upload gambar baru, hapus icon
            $data['icon'] = null;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
