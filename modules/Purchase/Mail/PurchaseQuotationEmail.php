<?php

namespace Modules\Purchase\Mail; 

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseQuotationEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $company;
    public $purchase_quotation;

    public function __construct($company, $purchase_quotation)
    {
        $this->company = $company;
        $this->purchase_quotation = $purchase_quotation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->purchase_quotation->filename, 'purchase_quotation');
        return $this->subject('Envio de Cotización de compra')
                    ->from(config('mail.username'), 'Cotización de compra')
                    ->view('purchase::email.purchase_quotation')
                    ->attachData($pdf, $this->purchase_quotation->filename.'.pdf');
    }
}