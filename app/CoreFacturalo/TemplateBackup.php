<?php

namespace App\CoreFacturalo;

class TemplateBackup
{
    public function pdf($template, $company, $document, $format_pdf)
    {
        if($template === 'credit' || $template === 'debit') {
            $template = 'note';
        }
        $template = 'pdf.'.$template.'_'.$format_pdf;

//        dd($template);
        return self::render($template, $company, $document);
    }

    public function xml($template, $company, $document)
    {
        return self::render('xml.'.$template, $company, $document);
    }

    private function render($view, $company, $document)
    {
        view()->addLocation(__DIR__.'/Templates');
        return view($view, compact('company', 'document'))->render();
    }

    public function pdfFooter()
    {
        view()->addLocation(__DIR__.'/Templates');
        return view('pdf.partials.footer')->render();
    }
}