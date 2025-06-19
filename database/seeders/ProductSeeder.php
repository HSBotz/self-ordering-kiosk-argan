<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan direktori products ada di storage
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }

        // Contoh produk kopi panas (kategori_id: 1)
        $this->createProduct(
            'Kopi Hitam',
            'Kopi hitam original dengan biji kopi pilihan',
            15000,
            1,
            'products/kopi-hitam.jpg',
            $this->generateDummyImage('kopi-hitam.jpg', 'coffee')
        );

        $this->createProduct(
            'Cappuccino',
            'Espresso dengan steamed milk dan foam susu yang lembut',
            25000,
            1,
            'products/cappuccino.jpg',
            $this->generateDummyImage('cappuccino.jpg', 'cappuccino')
        );

        // Contoh produk kopi dingin (kategori_id: 2)
        $this->createProduct(
            'Es Kopi Susu',
            'Es kopi dengan campuran susu segar dan gula aren',
            20000,
            2,
            'products/es-kopi-susu.jpg',
            $this->generateDummyImage('es-kopi-susu.jpg', 'iced coffee')
        );

        $this->createProduct(
            'Cold Brew',
            'Kopi yang diseduh dengan air dingin selama 12 jam',
            22000,
            2,
            'products/cold-brew.jpg',
            $this->generateDummyImage('cold-brew.jpg', 'cold brew')
        );

        // Contoh produk non-kopi (kategori_id: 3)
        $this->createProduct(
            'Teh Hijau',
            'Teh hijau premium dengan aroma yang menyegarkan',
            18000,
            3,
            'products/teh-hijau.jpg',
            $this->generateDummyImage('teh-hijau.jpg', 'green tea')
        );

        // Contoh produk makanan (kategori_id: 4)
        $this->createProduct(
            'Croissant',
            'Croissant butter yang renyah di luar dan lembut di dalam',
            15000,
            4,
            'products/croissant.jpg',
            $this->generateDummyImage('croissant.jpg', 'croissant')
        );
    }

    /**
     * Membuat produk baru
     */
    private function createProduct($name, $description, $price, $categoryId, $imagePath, $imageContent = null)
    {
        // Cek apakah produk sudah ada
        $existingProduct = Product::where('name', $name)->first();

        if (!$existingProduct) {
            // Simpan gambar jika ada
            if ($imageContent) {
                Storage::disk('public')->put($imagePath, $imageContent);
            }

            // Buat produk baru
            Product::create([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $categoryId,
                'image' => $imagePath,
                'is_available' => true,
            ]);
        }
    }

    /**
     * Generate dummy image dari Unsplash
     */
    private function generateDummyImage($filename, $keyword)
    {
        try {
            // Gunakan Unsplash untuk mendapatkan gambar dummy
            $imageUrl = "https://source.unsplash.com/480x360/?" . urlencode($keyword);
            return file_get_contents($imageUrl);
        } catch (\Exception $e) {
            // Jika gagal, return null
            return null;
        }
    }
}
