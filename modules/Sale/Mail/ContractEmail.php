<?php

namespace Modules\Sale\Mail;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $company;
    public $contract;

    public function __construct($company, $contract)
    {
        $this->company = $company;
        $this->contract = $contract;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->contract->filename, 'contract');
        return $this->subject('Envio de contrato')
                    ->from(config('mail.username'), 'Contrato')
                    ->view('sale::contracts.templates.email')
                    ->attachData($pdf, $this->contract->filename.'.pdf');
    }
}