<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Activity;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        Activity::log(
            'category',
            'Kategori baru "' . $category->name . '" telah ditambahkan',
            $category
        );
    }

    /**
     * Handle the Category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        // Jika nama berubah
        if ($category->isDirty('name')) {
            $oldName = $category->getOriginal('name');
            Activity::log(
                'category',
                'Kategori "' . $oldName . '" diubah namanya menjadi "' . $category->name . '"',
                $category
            );
        } else {
            Activity::log(
                'category',
                'Kategori "' . $category->name . '" telah diperbarui',
                $category
            );
        }
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        Activity::log(
            'category',
            'Kategori "' . $category->name . '" telah dihapus',
            $category
        );
    }

    /**
     * Handle the Category "restored" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        Activity::log(
            'category',
            'Kategori "' . $category->name . '" telah dipulihkan',
            $category
        );
    }

    /**
     * Handle the Category "force deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        Activity::log(
            'category',
            'Kategori "' . $category->name . '" telah dihapus permanen',
            $category
        );
    }
}
