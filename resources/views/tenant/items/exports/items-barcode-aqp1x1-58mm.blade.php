<!DOCTYPE html>
<html lang="es">
    <head>
        <style>

        </style>
    </head>
    <body>
        @if(!empty($record))
            <div class="" >
                <div class=" " >
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
                    
                        <tr>
                          
                                @if($format == 1)
                                    <td class="celda" width="100%" style="text-align: center; padding: 0px; vertical-align: top; width: 100%;">
                                        <tr>
                                            <td style="text-align: center;font-size: 10px">
                                                <p style="width:150px">{{ \Illuminate\Support\Str::limit($record->description,45)}}</p>
                                            </td>
                
                                        </tr>
                 
                                        <tr>
                                            <td style="text-align: center;font-size:14px">
                                                <p>
                                                    @php
                                                    $codigo = ($record->barcode) ? $record->barcode : $record->internal_id;

                                                        $colour = [0,0,0];
                                                        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                                                        echo '<img style="width:180px; max-height: 40px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($codigo, $generator::TYPE_CODE_128, 2, 80, $colour)) . '">';
                                                    @endphp
                                                </p>
                                                <p>{{$codigo}}</p>
                                            </td>
                                        </tr>
                                    </td>
                                @endif
                    
                        </tr>
                        
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
