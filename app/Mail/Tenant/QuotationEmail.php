<?php

namespace App\Mail\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class QuotationEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $company;
    public $quotation;

    public function __construct($company, $quotation)
    {
        $this->company = $company;
        $this->quotation = $quotation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->quotation->filename, 'quotation');
        return $this->subject('Envio de Cotización')
                    ->from(config('mail.username'), 'Cotización')
                    ->view('tenant.templates.email.quotation')
                    ->attachData($pdf, $this->quotation->filename.'.pdf');
    }
}