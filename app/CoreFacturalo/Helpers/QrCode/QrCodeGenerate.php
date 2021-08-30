<?php

namespace App\CoreFacturalo\Helpers\QrCode;

use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

class QrCodeGenerate
{
    public function displayPNGBase64($value, $w = 150, $level = 'L', $background = [255, 255, 255], $color = [0, 0, 0], $filename = null, $quality = 0)
    {
        $qrCode = new QrCode($value, $level);
        $output = new Output\Png();
        $base64 = base64_encode($output->output($qrCode, $w, $background, $color, $quality));
        return ($base64);
    }
}