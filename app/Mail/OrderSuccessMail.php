<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    /**
     * Terima object Order ketika mailable dibuat.
     */
    public function __construct(Order $order)
    {
        // Load relasi supaya bisa dipakai di view (product, variant)
        $this->order = $order->loadMissing(['product', 'variant']);
    }

    /**
     * Atur subject dan view email.
     */
    public function build()
    {
        return $this->subject('[MaitriProject] Transaksi ' . $this->order->code . ' berhasil')
            ->markdown('emails.orders.success');
    }
}
