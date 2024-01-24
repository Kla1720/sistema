@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $tittle = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $payments = $document->payments;
    $accounts = \App\Models\Tenant\BankAccount::all();

    $logo = "storage/uploads/logos/{$company->logo}";
    if($establishment->logo) {
        $logo = "{$establishment->logo}";
    }

    $encomienda = $document->encomienda;
    $pasaje = $document->pasaje;

@endphp
<html>
<head>
    {{--<title>{{ $tittle }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>

@if($company->logo)
    <div class="text-center company_logo_box pt-3">
        <img src="data:{{mime_content_type(public_path("{$logo}"))}};base64, {{base64_encode(file_get_contents(public_path("{$logo}")))}}" alt="{{$company->name}}" class="company_logo_ticket contain">
    </div>
{{--@else--}}
    {{--<div class="text-center company_logo_box pt-5">--}}
        {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo_ticket contain">--}}
    {{--</div>--}}
@endif
@if($document->state_type->id == '11')
    <div class="company_logo_box" style="position: absolute; text-align: center; top:500px">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")))}}" alt="anulado" class="" style="opacity: 0.6;">
    </div>
@endif
<table class="full-width">
    <tr>
        <td class="text-center"><h4><b>{{ $company->name }}</b></h4></td>
    </tr>
    <tr>
        <td class="text-center"><h5><b>{{ 'RUC '.$company->number }}</b></h5></td>
    </tr>
    <tr>
        <td class="text-center" style="text-transform: uppercase;">
            {{ ($establishment->address !== '-')? $establishment->address : '' }}
            {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
            {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
            {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
        </td>
    </tr>
    <tr>
        <td class="text-center">{{ ($establishment->email !== '-')? $establishment->email : '' }}</td>
    </tr>
    <tr>
        <td class="text-center pb-3">{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</td>
    </tr>
    <tr>
        <td class="text-center pt-3 border-top"><h4><b>NOTA DE VENTA</b></h4></td>
    </tr>
    <tr>
        <td class="text-center pb-3 border-bottom"><h3>{{ $tittle }}</h3></td>
    </tr>
</table>
<table class="full-width">
    <tr>
        <td width="" class="pt-3"><p class="desc"><b>F. Emisión:</b></p></td>
        <td width="" class="pt-3"><p class="desc">{{ $document->date_of_issue->format('Y-m-d') }}</p></td>
    </tr>

    @if ($document->due_date)
        <tr>
            <td width="" class="pt-3"><p class="desc">F. Vencimiento:</p></td>
            <td width="" class="pt-3"><p class="desc">{{ $document->getFormatDueDate() }}</p></td>
        </tr>
    @endif

    <tr>
        <td class="align-top"><p class="desc"><b>Cliente:</b></p></td>
        <td><p class="desc">{{ $customer->name }}</p></td>
    </tr>
    <tr>
        <td><p class="desc"><b>{{ $customer->identity_document_type->description }}:</b></p></td>
        <td><p class="desc">{{ $customer->number }}</p></td>
    </tr>
    @if ($customer->address !== '' && $customer->identity_document_type->description== 'RUC')
        <tr>
            <td class="align-top"><p class="desc"><b>Dirección:</b></p></td>
            <td>
                <p class="desc">
                    {{ strtoupper($customer->address) }}
                    {{ ($customer->district_id !== '-')? ', '.strtoupper($customer->district->description) : '' }}
                    {{ ($customer->province_id !== '-')? ', '.strtoupper($customer->province->description) : '' }}
                    {{ ($customer->department_id !== '-')? '- '.strtoupper($customer->department->description) : '' }}
                </p>
            </td>
        </tr>
    @endif
    @if ($document->plate_number !== null)
    <tr>
        <td class="align-top"><p class="desc">N° Placa:</p></td>
        <td><p class="desc">{{ $document->plate_number }}</p></td>
    </tr>
    @endif
    @if ($document->purchase_order)
        <tr>
            <td><p class="desc">Orden de Compra:</p></td>
            <td><p class="desc">{{ $document->purchase_order }}</p></td>
        </tr>
    @endif
    @if ($document->observation)
        <tr>
            <td><p class="desc">Observación:</p></td>
            <td><p class="desc">{{ $document->observation }}</p></td>
        </tr>
    @endif
    @if ($document->reference_data)
        <tr>
            <td class="align-top"><p class="desc">D. Referencia:</p></td>
            <td>
                <p class="desc">
                    {{ $document->reference_data }}
                </p>
            </td>
        </tr>
    @endif

</table>
@if(!is_null($encomienda))
    <table>
        <tr>
            <td class="desc"><b>Destinatario: </b></td>
            <td class="desc">{{ $encomienda->destinatario->name }}</td>
        </tr>
        @if ($encomienda->programacion)
            <tr>
                <td class="desc"><b>Origen: </b></td>
                <td class="desc">
                    {{ $encomienda->programacion->origen->nombre  }}
                </td>
            </tr>
            <tr style="margin-top: 20px">
                <td class="desc"><h3><b>Destino: </b></h3> </td>
                <td class="desc">
                    <h3>
                        <b>{{ $encomienda->programacion->destino->nombre }}</b>
                    </h3>
                </td>
            </tr>
            {{-- <tr>
                <td class="align-top desc"><b>Hora salida</b></td>
                <td class="text-left desc">{{ $encomienda->programacion->hora_salida }}</td>
            </tr>
            <tr>
                <td class="align-top desc"><b>Fecha Salida</b></td>
                <td class="text-left desc">{{ $encomienda->fecha_salida }}</td>
            </tr> --}}
        @else
            <tr>
                <td class="desc"><h4><b>Origen: </b></h4></td>
                <td class="desc">
                    <h4><b>{{ $encomienda->terminal->nombre  }}</b></h4>
                </td>
            </tr>
            <tr style="margin-top: 20px">
                <td class="desc"><h3><b>Destino: </b></h3> </td>
                <td class="desc">
                    <h3>
                        <b>{{ $encomienda->destino->nombre }}</b>
                    </h3>
                </td>
            </tr>
        @endif
    </table>
@endif

@if(!is_null($pasaje))
    <table>
{{--        @if ($pasaje->pasajero->name && $document->document_type->id=='01')--}}
{{--            <tr>--}}
{{--                <td class="align-top desc"><h5><b>Pasajero: </b></h5></td>--}}
{{--                <td class="text-left desc"><h4>{{ $pasaje->pasajero->name }}</h4></td>--}}
{{--            </tr>--}}
{{--        @endif--}}
        @if ($pasaje->programacion)
            <tr>
                <td class="desc" with="40"><h3 style="padding: 0px;"><b>Origen: </b></h3> </td>
                <td class="desc">
                    <h3><b>{{ $pasaje->programacion->origen->nombre  }}</b></h3>
                </td>
            </tr>
            <tr style="margin-top: 20px">
                <td class="desc"><h3><b>Destino: </b></h3> </td>
                <td class="desc">
                    <h3><b>{{ $pasaje->programacion->destino->nombre }}</b></h3>
                </td>
            </tr>
            <tr>
                <td class="align-top desc"><h5><b>Fecha viaje: </b></h5></td>
                <td class="text-left desc"><h4>{{ $pasaje->fecha_salida }} <strong>{{ $pasaje->programacion->hora_salida }}</strong> </h4></td>
            </tr>
        @else
            <tr>
                <td class="align-top desc"><h5><b>Fecha viaje: </b></h5></td>
                <td class="text-left desc"><h4>{{ $pasaje->fecha_salida }}</h4></td>
            </tr>
            <tr>
                <td class="desc"> <h5> <b>Hora viaje: </b> </h5> </td>
                <td class="desc"> <h4> <strong>{{ $pasaje->hora_salida }}</strong></h4></td>
            </tr>
        @endif
        <tr>
            <td class="desc">
                <h5><b>N°. Asiento: </b></h5>
            </td>
            <td class="desc">
                <h1><b>{{ $pasaje->numero_asiento }}</b></h1>
            </td>
        </tr>

    </table>
@endif
<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr>
        <th class="border-top-bottom desc-9 text-left">CANT.</th>
        <th class="border-top-bottom desc-9 text-left">UNIDAD</th>
        <th class="border-top-bottom desc-9 text-left">DESCRIPCIÓN</th>
        <th class="border-top-bottom desc-9 text-left">P.UNIT</th>
        <th class="border-top-bottom desc-9 text-left">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center desc-9 align-top">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center desc-9 align-top">{{ $row->item->unit_type_id }}</td>
            <td class="text-left desc-9 align-top">

                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif
                    @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/>{!! $attr->description !!} : {{ $attr->value }}
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><small>{{ $dtos->factor * 100 }}% {{$dtos->description }}</small>
                    @endforeach
                @endif
                @if($row->item->is_set == 1)

                 <br>
                 @inject('itemSet', 'App\Services\ItemSetService')
                 @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                     {{$item}}<br>
                 @endforeach
                @endif
            </td>
            <td class="text-right desc-9 align-top">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right desc-9 align-top">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="border-bottom"></td>
        </tr>
    @endforeach
        @if($document->total_exportation > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        {{-- @if($document->total_taxed > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif --}}
         @if($document->total_discount > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        {{--<tr>
            <td colspan="4" class="text-right font-bold desc">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc">{{ number_format($document->total_igv, 2) }}</td>
        </tr>--}}

        @if($document->total_charge > 0 && $document->charges)
            <tr>
                <td colspan="4" class="text-right font-bold desc">CARGOS ({{$document->getTotalFactor()}}%): {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_charge, 2) }}</td>
            </tr>
        @endif

        <tr>
            <td colspan="4" class="text-right font-bold desc">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc">{{ number_format($document->total, 2) }}</td>
        </tr>

        @php
            $change_payment = $document->getChangePayment();
        @endphp

        @if($change_payment < 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">VUELTO: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format(abs($change_payment),2, ".", "") }}</td>
            </tr>
        @endif

    </tbody>
</table>
<table class="full-width">
    <tr>

        @foreach(array_reverse((array) $document->legends) as $row)
            <tr>
                @if ($row->code == "1000")
                    <td class="desc pt-3" style="text-transform: uppercase;">Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></td>
                    @if (count((array) $document->legends)>1)
                    <tr><td class="desc pt-3"><span class="font-bold">Leyendas</span></td></tr>
                    @endif
                @else
                    <td class="desc pt-3">{{$row->code}}: {{ $row->value }}</td>
                @endif
            </tr>
        @endforeach
    </tr>

    <tr>
        <td class="desc">
            <br>
            @foreach($accounts as $account)
                <span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                <br>
                <span class="font-bold">N°:</span> {{$account->number}}
                @if($account->cci)
                - <span class="font-bold">CCI:</span> {{$account->cci}}
                @endif
                <br>
            @endforeach

        </td>
    </tr>

</table>

@if($document->payment_method_type_id && $payments->count() == 0)
<table class="full-width">
    <tr>
    <td class="desc pt-5">
        <strong>PAGO: </strong>{{ $document->payment_method_type->description }}
    </td>
</tr>
</table>
@endif

@if($payments->count())
<table class="full-width">
    <tr><td><strong>PAGOS:</strong> </td></tr>
    @php
        $payment = 0;
    @endphp
    @foreach($payments as $row)
        <tr><td>- {{ $row->date_of_payment->format('d/m/Y') }} - {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td></tr>
        @php
            $payment += (float) $row->payment;
        @endphp
    @endforeach
    <tr><td><strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}</td></tr>
</table>
@endif

<table class="full-width">
    @if (!is_null($pasaje))
        <tr>
            <td class="text-center desc">Condición</td>
        </tr>
        <tr>
            <td class="text-center desc pb-2">
               <h2><strong>{{ $pasaje->estado_pago_id == '1' ? 'PAGADO' : 'PAGO EN DESTINO' }}</strong></h2>
            </td>
        </tr>
    @endif
    @if (!is_null($encomienda))
        <tr>
            <td class="text-center desc">Condición</td>
        </tr>
        <tr>
            <td class="text-center desc">
                <h2><strong>{{ $encomienda->estado_pago_id == '1' ? 'PAGADO' : 'PAGO EN DESTINO' }}</strong></h2>
            </td>
        </tr>
    @endif
</table>

</body>
</html>
