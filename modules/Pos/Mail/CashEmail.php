<?php

namespace Modules\Pos\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CashEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $pdf;

    public function __construct($company, $pdf)
    {
        $this->company = $company;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $pdf = $this->getStorage($this->document->filename, 'sale_note');

        return $this->subject('Envio de Reporte de caja')
                    ->from(config('mail.username'), 'Reporte de caja')
                    ->view('pos::cash.email')
                    ->attachData($this->pdf, 'reporte_caja'.'.pdf');
    }
}