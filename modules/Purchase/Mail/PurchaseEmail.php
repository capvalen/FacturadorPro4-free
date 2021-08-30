<?php

namespace Modules\Purchase\Mail;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $company;
    public $purchase;

    public function __construct($company, $purchase)
    {
        $this->company = $company;
        $this->purchase = $purchase;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->purchase->filename, 'purchase');

        return $this->subject('Envio de Compra')
                    ->from(config('mail.username'), 'Compra')
                    ->view('purchase::email.purchase')
                    ->attachData($pdf, $this->purchase->filename.'.pdf');
    }
}