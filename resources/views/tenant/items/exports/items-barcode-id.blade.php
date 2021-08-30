<!DOCTYPE html>
<html lang="es">
    <head>
    </head>
    <body>
        @if(!empty($record))
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
                        @for($i=0; $i < $stock; $i+=3)
                        <tr>
                            @for($j=0; $j < 3; $j++)
                            <td class="celda" width="33%" style="text-align: center; padding-top: 10px; padding-bottom: 10px; font-size: 9px; vertical-align: top;">
                                <p>{{withoutRounding($record->sale_unit_price, 2)}} {{$record->currency_type->symbol}}</p>
                                <p>
                                    @php
                                        $colour = [0,0,0];
                                        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                                        echo '<img style="width:110px; max-height: 40px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($record->barcode, $generator::TYPE_CODE_128, 1, 60, $colour)) . '">';
                                    @endphp
                                </p>
                                <p>{{$record->barcode}}</p>
                            </td>
                            @endfor
                        </tr>
                        @endfor
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
