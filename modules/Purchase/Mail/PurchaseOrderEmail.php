<?php

namespace Modules\Purchase\Mail;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseOrderEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $purchase_order;

    public function __construct($purchase_order)
    {
        $this->purchase_order = $purchase_order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->purchase_order->filename, 'purchase_order');
        return $this->subject('Envio de Orden de compra')
                    ->from(config('mail.username'), 'Orden de compra')
                    ->view('purchase::email.purchase_order')
                    ->attachData($pdf, $this->purchase_order->filename.'.pdf');
    }
}
