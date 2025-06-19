<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Activity;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        Activity::log(
            'product',
            'Produk baru "' . $product->name . '" telah ditambahkan',
            $product
        );
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        // Jika nama berubah
        if ($product->isDirty('name')) {
            $oldName = $product->getOriginal('name');
            Activity::log(
                'product',
                'Produk "' . $oldName . '" diubah namanya menjadi "' . $product->name . '"',
                $product
            );
        }
        // Jika harga berubah
        elseif ($product->isDirty('price')) {
            $oldPrice = $product->getOriginal('price');
            Activity::log(
                'product',
                'Harga produk "' . $product->name . '" diubah dari Rp ' .
                number_format($oldPrice, 0, ',', '.') . ' menjadi Rp ' .
                number_format($product->price, 0, ',', '.'),
                $product
            );
        }
        // Perubahan lainnya
        else {
            Activity::log(
                'product',
                'Produk "' . $product->name . '" telah diperbarui',
                $product
            );
        }
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        Activity::log(
            'product',
            'Produk "' . $product->name . '" telah dihapus',
            $product
        );
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        Activity::log(
            'product',
            'Produk "' . $product->name . '" telah dipulihkan',
            $product
        );
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        Activity::log(
            'product',
            'Produk "' . $product->name . '" telah dihapus permanen',
            $product
        );
    }
}
