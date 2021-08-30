<?php

namespace App\Imports;

use App\Models\Tenant\Document;
use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DocumentsImportTwoFormat implements ToCollection
{
    use Importable;

    protected $data;

    public function collection(Collection $rows)
    {
            $total = count($rows);
            $registered = 0;
            unset($rows[0]);
            foreach ($rows as $row)
            {
                // dd($row);
                //serie-correlativo
                $nrodocumento = $row[1];
                $serienumero = explode('-', $nrodocumento);
                $serie = $serienumero[0];
                $number = $serienumero[1];
                $correlativo = (int)$number;

                //tipo de documento
                if($row[0] == '03'){
                    $document_type = '03';
                    $document_type_operation = '0101';
                } elseif ($row[0] == '01') {
                    $document_type = '01';
                    $document_type_operation = '0101';
                }else {
                    return 'el tipo de documento: '.$row[0].' no es válido para documentos electrónicos';
                }

                //fecha de documento
                $create_date = $row[2];

                // $create_date = Carbon::instance(Date::excelToDateTimeObject($row[2]));
                // $date_document = Carbon::parse($create_date)->format('Y-m-d');

                $date_create = Carbon::createFromFormat('d/m/Y', $create_date);
                $date_document = $date_create->format('Y-m-d');

                //moneda
                $currency = ($row[5] == 'S') ? 'PEN' : 'Registre con los administradores nueva moneda' ;

                //cliente
                $co_number = rtrim($row[3]);
                if ($co_number > 0) {
                    if (strlen($co_number) == 11) {
                        $client_document_type = '6';
                        $company_number = $co_number;
                    } elseif (strlen($co_number) == 8) {
                        $client_document_type = '1';
                        $company_number = $co_number;
                    }
                }
                else {
                    $client_document_type = '0';
                    $company_number = '00000000'; 
                }

                $company_name = $row[4];
                $company_address = $row[9];

                //totales
                $mtototal = $row[8];
                $mtoimpuesto = $row[7];
                $mtosubtotal = $row[6];

                //unidad de medida
                $cdunimed = $row[12];
                if (rtrim($cdunimed) == 'GLNS') {
                    $unit_type = 'GLL';
                } elseif (rtrim($cdunimed) == 'GLN'){
                    $unit_type = 'GLL';
                } elseif (rtrim($cdunimed) == 'LT'){
                    $unit_type = 'LTR';
                } else {
                    $unit_type = 'NIU';
                }

                //genero json y envio a api para no hacer insert 

                //valores
                $cantidad = $row[13];
                $precio_unitario = $row[14];
                $subtotal = $row[15];
                $total_impuesto = $row[16];
                
                $json = array(
                    "serie_documento" => $serie,
                    "numero_documento" => $correlativo,
                    "fecha_de_emision" => $date_document,
                    "hora_de_emision" => "11:00:00",
                    "codigo_tipo_operacion" => $document_type_operation,
                    "codigo_tipo_documento" => $document_type,
                    "codigo_tipo_moneda" => $currency,
                    "fecha_de_vencimiento" => $date_document,
                    "numero_orden_de_compra" => "-",
                    "totales" => [
                        "total_exportacion" => 0.00,
                        "total_operaciones_gravadas" => $mtosubtotal,
                        "total_operaciones_inafectas" => 0.00,
                        "total_operaciones_exoneradas" => 0.00,
                        "total_operaciones_gratuitas" => 0.00,
                        "total_igv" => $mtoimpuesto,
                        "total_impuestos" => $mtoimpuesto,
                        "total_valor" => $mtosubtotal,
                        "total_venta" => $mtototal
                    ],
                    "datos_del_emisor" => [
                        "codigo_del_domicilio_fiscal" => "0000"
                    ],
                    "datos_del_cliente_o_receptor" => [
                        "codigo_tipo_documento_identidad" => $client_document_type,
                        "numero_documento" => $company_number,
                        "apellidos_y_nombres_o_razon_social" => rtrim($company_name),
                        "codigo_pais" => "PE",
                        "ubigeo" => "010101",
                        "direccion" => rtrim($company_address),
                        "correo_electronico" => "",
                        "telefono" => ""
                    ],
                    "items" => [
                        [
                            "codigo_interno" => substr($row[11],0,10),
                            "descripcion" => rtrim($row[11]),
                            "codigo_producto_sunat" => "",
                            "unidad_de_medida" => $unit_type,
                            "cantidad" => $cantidad,
                            "valor_unitario" => $precio_unitario - ($total_impuesto / $cantidad),
                            "codigo_tipo_precio" => "01",
                            "precio_unitario" => $precio_unitario,
                            "codigo_tipo_afectacion_igv" => "10",
                            "total_base_igv" => $subtotal,
                            "porcentaje_igv" => "18",
                            "total_igv" => $total_impuesto,
                            "total_impuestos" => $total_impuesto,
                            "total_valor_item" => $subtotal,
                            "total_item" => $subtotal + $total_impuesto,
                            "datos_adicionales" => [
                                [
                                    "codigo" => "5010",
                                    "descripcion" => "Número de Placa",
                                    "valor" => $row[10] != null ? $row[10] : 0,
                                    "fecha_inicio" => "",
                                    "fecha_fin" => "",
                                    "duracion" => ""
                                ]
                            ]
                        ]
                    ]
                );

                if ($row[17] != null) {
                    $unitm = $row[18];
                    if (rtrim($unitm) == 'GLNS') {
                        $unit_m = 'GLL';
                    } elseif (rtrim($unitm) == 'GLN'){
                        $unit_m = 'GLL';
                    } elseif (rtrim($unitm) == 'LT'){
                        $unit_m = 'LTR';
                    } else {
                        $unit_m = $unitm;
                    }
                    //valores
                    $cantidad_s = $row[19];
                    $precio_unitario_s = $row[20];
                    $subtotal_s = $row[21];
                    $total_impuesto_s = $row[22];
                    $new_item = [
                            "codigo_interno" => substr($row[17],0,10),
                            "descripcion" => rtrim($row[17]),
                            "codigo_producto_sunat" => "",
                            "unidad_de_medida" => $unit_m,
                            "cantidad" => $cantidad_s,
                            "valor_unitario" => $precio_unitario - ($total_impuesto / $cantidad),
                            "codigo_tipo_precio" => "01",
                            "precio_unitario" => $precio_unitario_s,
                            "codigo_tipo_afectacion_igv" => "10",
                            "total_base_igv" => $subtotal_s,
                            "porcentaje_igv" => "18",
                            "total_igv" => $total_impuesto_s,
                            "total_impuestos" => $total_impuesto_s,
                            "total_valor_item" => $subtotal_s,
                            "total_item" => $subtotal_s + $total_impuesto_s
                        ];
                    array_push($json["items"], $new_item);
                }

                $url = url('/api/documents');
                $token = \Auth::user()->api_token;

                // dd($json);

                try {

                    $client = new \GuzzleHttp\Client();

                    $response = $client->post($url, [
                        'headers' => [
                            'Content-Type' => 'Application/json',
                            'Authorization' => 'Bearer '.$token
                        ],
                        'json' => $json
                    ]);
                } catch (Exception $e) {
                    dd($e);
                }

                // dd($response);

                $registered += 1;
            }
            $this->data = compact('total', 'registered');

    }

    public function getData()
    {
        return $this->data;
    }
}
