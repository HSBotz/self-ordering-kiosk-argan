<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Activity;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        Activity::log(
            'order',
            'Pesanan baru #' . $order->order_number . ' telah dibuat',
            $order
        );
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        // Jika status berubah, catat aktivitas
        if ($order->isDirty('status')) {
            $statusText = '';
            switch ($order->status) {
                case 'pending':
                    $statusText = 'menunggu';
                    break;
                case 'processing':
                    $statusText = 'diproses';
                    break;
                case 'completed':
                    $statusText = 'selesai';
                    break;
                case 'cancelled':
                    $statusText = 'dibatalkan';
                    break;
                default:
                    $statusText = $order->status;
            }

            Activity::log(
                'order',
                'Pesanan #' . $order->order_number . ' status diubah menjadi ' . $statusText,
                $order
            );
        }
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        Activity::log(
            'order',
            'Pesanan #' . $order->order_number . ' telah dihapus',
            $order
        );
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        Activity::log(
            'order',
            'Pesanan #' . $order->order_number . ' telah dipulihkan',
            $order
        );
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        Activity::log(
            'order',
            'Pesanan #' . $order->order_number . ' telah dihapus permanen',
            $order
        );
    }
}
