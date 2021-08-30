<?php

namespace App\Mail\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;

class DocumentEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $company;
    public $document;

    public function __construct($company, $document)
    {
        $this->company = $company;
        $this->document = $document;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->document->filename, 'pdf');
        $xml = $this->getStorage($this->document->filename, 'signed');

        $image_detraction = ($this->document->detraction) ? (($this->document->detraction->image_pay_constancy) ? storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'image_detractions'.DIRECTORY_SEPARATOR.$this->document->detraction->image_pay_constancy):false):false;
        
        $email = $this->subject('Envio de Comprobante de Pago Electrónico')
                    ->from(config('mail.username'), 'Comprobante electrónico')
                    ->view('tenant.templates.email.document')
                    ->attachData($pdf, $this->document->filename.'.pdf')
                    ->attachData($xml, $this->document->filename.'.xml');

                    
        if($image_detraction){
            return $email->attachData(File::get($image_detraction), $this->document->detraction->image_pay_constancy);
        }

        return $email;
    }
}