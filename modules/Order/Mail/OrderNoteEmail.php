<?php

namespace Modules\Order\Mail;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderNoteEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $company;
    public $order_note;

    public function __construct($company, $order_note)
    {
        $this->company = $company;
        $this->order_note = $order_note;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->order_note->filename, 'order_note');
        return $this->subject('Envio de pedido')
                    ->from(config('mail.username'), 'Pedido')
                    ->view('order::order_notes.templates.email')
                    ->attachData($pdf, $this->order_note->filename.'.pdf');
    }
}