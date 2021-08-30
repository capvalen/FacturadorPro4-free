<?php

namespace Modules\Sale\Mail;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaleOpportunityEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $company;
    public $sale_opportunity;

    public function __construct($company, $sale_opportunity)
    {
        $this->company = $company;
        $this->sale_opportunity = $sale_opportunity;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->sale_opportunity->filename, 'sale_opportunity');
        return $this->subject('Envio de oportudinad de venta')
                    ->from(config('mail.username'), 'O. Venta')
                    ->view('sale::sale_opportunities.templates.email')
                    ->attachData($pdf, $this->sale_opportunity->filename.'.pdf');
    }
}