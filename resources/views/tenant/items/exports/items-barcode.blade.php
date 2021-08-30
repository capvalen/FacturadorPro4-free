<!DOCTYPE html>
<html lang="es">
    <head>
    </head>
    <body>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="table" width="100%">
                        @php
                            function withoutRounding($number, $total_decimals) {
                                $number = (string)$number;
                                if($number === '') {
                                    $number = '0';
                                }
                                if(strpos($number, '.') === false) {
                                    $number .= '.';
                                }
                                $number_arr = explode('.', $number);

                                $decimals = substr($number_arr[1], 0, $total_decimals);
                                if($decimals === false) {
                                    $decimals = '0';
                                }

                                $return = '';
                                if($total_decimals == 0) {
                                    $return = $number_arr[0];
                                } else {
                                    if(strlen($decimals) < $total_decimals) {
                                        $decimals = str_pad($decimals, $total_decimals, '0', STR_PAD_RIGHT);
                                    }
                                    $return = $number_arr[0] . '.' . $decimals;
                                }
                                return $return;
                            }
                        @endphp
                        @foreach($records as $key => $value)
                            @if($loop->iteration % 3 === 1)
                                <tr>
                            @endif
                            <td class="celda" width="33%" style="text-align: center; padding-top: 10px; padding-bottom: 10px; font-size: 9px; vertical-align: top;">
                                <p>{{withoutRounding($value->sale_unit_price, 2)}} {{$value->currency_type->symbol}}</p>
                                <p>
                                    @php
                                        if ($value->internal_id) {
                                            $colour = [0,0,0];
                                            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                                            echo '<img style="width:110px; max-height: 30px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value->internal_id, $generator::TYPE_CODE_128, 1, 60, $colour)) . '">';
                                        }
                                    @endphp
                                </p>
                                <p>{{$value->internal_id}}</p>
                            </td>
                            @if($loop->iteration % 3 === 0)
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        @else
            <div>
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
