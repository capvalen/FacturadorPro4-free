<?php

namespace App\CoreFacturalo\Helpers\Storage;

use Illuminate\Support\Facades\Storage;

trait StorageDocument
{
    protected $_folder;
    protected $_filename;

    public function uploadStorage($filename, $file_content, $file_type, $root = null)
    {
        $this->setData($filename, $file_type, $root);
        Storage::disk('tenant')->put($this->_folder.DIRECTORY_SEPARATOR.$this->_filename, $file_content);
    }

    public function downloadStorage($filename, $file_type, $root = null)
    {
        $this->setData($filename, $file_type, $root);
        return Storage::disk('tenant')->download($this->_folder.DIRECTORY_SEPARATOR.$this->_filename);
    }

    public function getStorage($filename, $file_type, $root = null)
    {
        $this->setData($filename, $file_type, $root);
        return Storage::disk('tenant')->get($this->_folder.DIRECTORY_SEPARATOR.$this->_filename);
    }

    private function setData($filename, $file_type, $root)
    {
        $extension = 'xml';
        switch ($file_type) {
            case 'unsigned':
                break;
            case 'signed':
                break;
            case 'pdf':
                $extension = 'pdf';
                break;
            case 'quotation':
                $extension = 'pdf';
                break;
            case 'sale_note':
                $extension = 'pdf';
                break;
            case 'cdr':
                $filename = 'R-'.$filename;
                $extension = 'zip';
                break;
            case 'purchase_quotation':
                $extension = 'pdf';
                break;
            case 'purchase_order_attached':
                $extension = '';
                break;
            case 'purchase_order':
                $extension = 'pdf';
                break;
            case 'order_note':
                $extension = 'pdf';
                break;
            case 'sale_opportunity':
                $extension = 'pdf';
                break;
            case 'contract':
                $extension = 'pdf';
                break;
            case 'order_form':
                $extension = 'pdf';
                break;
            case 'purchase':
                $extension = 'pdf';
            case 'devolution':
                $extension = 'pdf';
                break;
        }
        $this->_filename = $filename.'.'.$extension;
        $this->_folder = ($root)?$root.DIRECTORY_SEPARATOR.$file_type:$file_type;
    }
}