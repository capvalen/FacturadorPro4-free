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

class DocumentsImport implements ToCollection
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
                $nrodocumento = $row[3];
                $serienumero = explode('-', $nrodocumento);
                $serie = $serienumero[0];
                $number = $serienumero[1];
                $correlativo = (int)$number;

                if($row[2] == '03'){
                    $document_type = '03';
                    $document_type_operation = '0101';
                } elseif($row[2] == '0001'){
                    $document_type = '01';
                    $document_type_operation = '0101';  
                } elseif ($row[2] == '01') {
                    $document_type = '01';
                    $document_type_operation = '0101';
                }else {
                    return 'la serie: '.$serie.' no es valida para documentos electrónicos';
                }

                // dd("row2:".$row[2]."document_type:".$document_type);

                $create_date = Carbon::instance(Date::excelToDateTimeObject($row[5]));
                $date_create = Carbon::parse($create_date)->format('Y-m-d');

                $currency = ($row[11] == 'S') ? 'PEN' : 'Registre nueva moneda' ;

                //cliente
                $co_number = rtrim($row[9]);
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

                if ($row[10] == null) {
                    if ($client_document_type == '0') {
                        $company_name = 'SIN NOMBRE';
                    }
                } else {
                    $company_name = $row[10];
                }
                $company_address = $row[20];

                //totales
                $mtototal = $row[15];
                $mtoimpuesto = $row[13];
                $mtosubtotal = $row[12];

                //unidad de medida
                $cdunimed = $row[23];
                if (rtrim($cdunimed) == 'GLNS') {
                    $unit_type = 'GLL';
                } elseif (rtrim($cdunimed) == 'GLN'){
                    $unit_type = 'GLL';
                } elseif (rtrim($cdunimed) == 'LT'){
                    $unit_type = 'LTR';
                } else {
                    $unit_type = 'NIU';
                }
                

                $total_primer_producto = $row[26] + $row[27];

                //genero json y envio a api para no hacer insert 
                
                $json = array(
                    "serie_documento" => $serie,
                    "numero_documento" => $correlativo,
                    "fecha_de_emision" => $date_create,
                    "hora_de_emision" => "11:00:00",
                    "codigo_tipo_operacion" => $document_type_operation,
                    "codigo_tipo_documento" => $document_type,
                    "codigo_tipo_moneda" => $currency,
                    "fecha_de_vencimiento" => $date_create,
                    "numero_orden_de_compra" => "-",
                    "totales" => [
                        "total_exportacion" => 0.00,
                        "total_operaciones_gravadas" => $mtosubtotal,
                        "total_operaciones_inafectas" => 0.00,
                        "total_operaciones_exoneradas" => 0.00,
                        "total_operaciones_gratuitas" => 0.00,
                        "total_igv" => $mtoimpuesto,
                        "total_impuestos" => $mtoimpuesto,
                        "total_valor" => $mtototal,
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
                            "codigo_interno" => substr($row[22],0,10),
                            "descripcion" => rtrim($row[22]),
                            "codigo_producto_sunat" => "",
                            "unidad_de_medida" => $unit_type,
                            "cantidad" => $row[24],
                            "valor_unitario" => $row[25],
                            "codigo_tipo_precio" => "01",
                            "precio_unitario" => $row[25],
                            "codigo_tipo_afectacion_igv" => "10",
                            "total_base_igv" => $row[26] != null ? $row[26] : $mtosubtotal,
                            "porcentaje_igv" => "18",
                            "total_igv" => $row[27] != null ? $row[27] : $mtoimpuesto,
                            "total_impuestos" => $row[27] != null ? $row[27] : $mtoimpuesto,
                            "total_valor_item" => $row[26] != null ? $row[26] : $mtosubtotal,
                            "total_item" => $total_primer_producto > 0 ? $total_primer_producto : $mtototal,
                            "datos_adicionales" => [
                                [
                                    "codigo" => "5010",
                                    "descripcion" => "Número de Placa",
                                    "valor" => $row[21] != null ? $row[21] : 0,
                                    "fecha_inicio" => "",
                                    "fecha_fin" => "",
                                    "duracion" => ""
                                ]
                            ]
                        ]
                    ]
                );

                if ($row[28] != null) {
                    $new_item = [
                            "codigo_interno" => substr($row[28],0,10),
                            "descripcion" => rtrim($row[28]),
                            "codigo_producto_sunat" => "",
                            "unidad_de_medida" => $unit_type,
                            "cantidad" => $row[29],
                            "valor_unitario" => $row[30],
                            "codigo_tipo_precio" => "01",
                            "precio_unitario" => $row[30],
                            "codigo_tipo_afectacion_igv" => "10",
                            "total_base_igv" => $row[31],
                            "porcentaje_igv" => "18",
                            "total_igv" => $row[32],
                            "total_impuestos" => $row[32],
                            "total_valor_item" => $row[31],
                            "total_item" => $row[31] + $row[32]
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

                $registered += 1;
            }
            $this->data = compact('total', 'registered');

    }

    public function getData()
    {
        return $this->data;
    }
}
