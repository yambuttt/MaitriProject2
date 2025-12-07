<?php

namespace App\Mail;

use App\Models\MarketplaceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarketplaceOrderPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public MarketplaceOrder $order;

    /**
     * Create a new message instance.
     */
    public function __construct(MarketplaceOrder $order)
    {
        $this->order = $order->loadMissing(['product', 'variant']);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('[MaitriProject] Pesanan Marketplace '
                . $this->order->invoice_number
                . ' sudah diterima')
            ->view('emails.marketplace.order-paid');
    }
}
