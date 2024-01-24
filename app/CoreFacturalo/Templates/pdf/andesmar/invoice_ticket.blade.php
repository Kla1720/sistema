@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $accounts = \App\Models\Tenant\BankAccount::where('show_in_documents', true)->get();
    $document_base = ($document->note) ? $document->note : null;
    $payments = $document->payments;

    $configuracion = \App\Models\Tenant\Configuration::all();

     foreach($configuracion as $config){
        $legend_footer= $config['legend_footer'];
     }

    if($document_base) {
        $affected_document_number = ($document_base->affected_document) ? $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT) : $document_base->data_affected_document->series.'-'.str_pad($document_base->data_affected_document->number, 8, '0', STR_PAD_LEFT);

    } else {
        $affected_document_number = null;
    }
    $document->load('reference_guides');

    $total_payment = $document->payments->sum('payment');
    $balance = ($document->total - $total_payment) - $document->payments->sum('change');


    $logo = "storage/uploads/logos/{$company->logo}";
    if($establishment->logo) {
        $logo = "{$establishment->logo}";
    }

    $encomienda = $document->encomienda;
    $pasaje = $document->pasaje;

@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body class="ticket">

@if($company->logo)
    <div class="text-center company_logo_box">
        <img src="data:{{mime_content_type(public_path("{$logo}"))}};base64, {{base64_encode(file_get_contents(public_path("{$logo}")))}}" alt="{{$company->name}}" class="company_logo_ticket contain" style="width: 90%">
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
        <td class="text-center"><h5><b>{{ $company->name }}</b></h5></td>
    </tr>
    {{--<tr>
        <td class="text-center"><h5>{{ $company->trade_name }}</h5></td>
    </tr>--}}
    <tr>
        <td class="text-center"><h4>RUC: {{ $company->number }}</h4></td>
    </tr>
    <tr>
        <td class="text-center" style="text-transform: uppercase;">
            <p style="font-size: 10px">
                {{ ($establishment->address !== '-')? $establishment->address : '' }}
                {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
            </p>
        </td>
    </tr>

    @isset($establishment->trade_address)
        <tr>
            <td class="text-center ">
                <b>D. Comercial: </b>{{  ($establishment->trade_address !== '-')? ' '.$establishment->trade_address : ''  }}</td>
        </tr>
    @endisset
    <tr>
        <td class="text-center ">
            <p style="font-size: 10px">
                <b>Tel.: </b>{{ ($establishment->telephone !== '-')? ' '.$establishment->telephone : '' }}
                <b>Email: </b>{{ ($establishment->email !== '-')? ' '.$establishment->email : '' }}
            </p>
        </td>
    </tr>
    @isset($establishment->web_address)
        <tr>
            <td class="text-center"><b>Web: </b>{{ ($establishment->web_address !== '-')? $establishment->web_address : '' }}</td>
        </tr>
    @endisset

    @isset($establishment->aditional_information)
        <tr>
            <td class="text-center pb-3"><p style="font-size: 10px">{{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }}</p></td>
        </tr>
    @endisset

    <tr>
        <td class="text-center pt-3 border-top"><h4>{{ $document->document_type->description }}</h4></td>
    </tr>
    <tr>
        <td class="text-center pb-3 border-bottom"><b style="font-size: 20px">{{ $document_number }}</b></td>
    </tr>
</table>
<table class="full-width">
    <tr>
        <td><p class="desc"><b>F. Emisión:</b> </p>{{ $document->date_of_issue->format('Y-m-d') }}</td>
        <td class="text-center"><p class="desc"><b>H. Emisión:</b> </p>{{ $document->time_of_issue }}</td>
        <td class="text-right"><p class="desc"><b>F. Vencimiento:</b> </p>{{ $invoice->date_of_due->format('Y-m-d') }}</td>
    </tr>


    <tr>
        <td><p class="desc"><b>Cliente:</b></p></td>
        <td colspan="2">{{ $customer->name }}</td>
    </tr>
    <tr>
        <td><p class="desc"><b>{{ $customer->identity_document_type->description }}:</b></p></td>
        <td colspan="2">{{ $customer->number }}</td>
    </tr>
    @if ($customer->address !== '')
        <tr>
            <td class="align-top"><p class="desc"><b>Dirección:</b></p></td>
            <td colspan="2">
                <p class="desc">
                    {{ $customer->address }}
                    {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                    {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                    {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
                </p>
            </td>
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

    @if ($document->detraction)
    {{--<strong>Operación sujeta a detracción</strong>--}}
        <tr>
            <td  class="align-top"><p class="desc"><b>N. Cta Detracciones:</b></p></td>
            <td><p class="desc">{{ $document->detraction->bank_account}}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc"><b>B/S Sujeto a detracción:</b></p></td>
            @inject('detractionType', 'App\Services\DetractionTypeService')
            <td><p class="desc">{{$document->detraction->detraction_type_id}} - {{ $detractionType->getDetractionTypeDescription($document->detraction->detraction_type_id ) }}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc"><b>Método de pago:</b></p></td>
            <td><p class="desc">{{ $detractionType->getPaymentMethodTypeDescription($document->detraction->payment_method_id ) }}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc"><b>Porcentaje detracción:</b></p></td>
            <td><p class="desc">{{ $document->detraction->percentage}}%</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc"><b>Monto detracción:</b></p></td>
            <td><p class="desc">S/ {{ $document->detraction->amount}}</p></td>
        </tr>
        @if($document->detraction->pay_constancy)
        <tr>
            <td  class="align-top"><p class="desc"><b>Constancia de pago:</b></p></td>
            <td><p class="desc">{{ $document->detraction->pay_constancy}}</p></td>
        </tr>
        @endif


        @if($invoice->operation_type_id == '1004')
        <tr class="mt-2">
            <td colspan="2"></td>
        </tr>
        <tr class="mt-2">
            <td colspan="2">DETALLE - SERVICIOS DE TRANSPORTE DE CARGA</td>
        </tr>
        <tr>
            <td class="align-top"><p class="desc">Ubigeo origen:</p></td>
            <td><p class="desc">{{ $document->detraction->origin_location_id[2] }}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc">Dirección origen:</td>
            <td><p class="desc">{{ $document->detraction->origin_address }}</td>
        </tr>
        <tr>
            <td class="align-top"><p class="desc">Ubigeo destino:</p></td>
            <td><p class="desc">{{ $document->detraction->delivery_location_id[2] }}</p></td>
        </tr>
        <tr>

            <td  class="align-top"><p class="desc">Dirección destino:</p></td>
            <td><p class="desc">{{ $document->detraction->delivery_address }}</p></td>
        </tr>
        <tr>
            <td class="align-top"><p class="desc">Valor referencial servicio de transporte:</p></td>
            <td><p class="desc">{{ $document->detraction->reference_value_service }}</p></td>
        </tr>
        <tr>

            <td  class="align-top"><p class="desc">Valor referencia carga efectiva:</p></td>
            <td><p class="desc">{{ $document->detraction->reference_value_effective_load }}</p></td>
        </tr>
        <tr>
            <td class="align-top"><p class="desc">Valor referencial carga útil:</p></td>
            <td><p class="desc">{{ $document->detraction->reference_value_payload }}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc">Detalle del viaje:</p></td>
            <td><p class="desc">{{ $document->detraction->trip_detail }}</p></td>
        </tr>
        @endif

    @endif


    @if ($document->retention)
        <br>
        <tr>
            <td colspan="2">
                <p class="desc"><strong>Información de la retención</strong></p>
            </td>
        </tr>
        <tr>
            <td><p class="desc">Base imponible: </p></td>
            <td><p class="desc">{{ $document->currency_type->symbol}} {{ $document->retention->base }} </p></td>
        </tr>
        <tr>
            <td><p class="desc">Porcentaje:</p></td>
            <td><p class="desc">{{ $document->retention->percentage * 100 }}%</p></td>
        </tr>
        <tr>
            <td><p class="desc">Monto:</p></td>
            <td><p class="desc">{{ $document->currency_type->symbol}} {{ $document->retention->amount }}</p></td>
        </tr>
    @endif


    @if ($document->prepayments)
        @foreach($document->prepayments as $p)
        <tr>
            <td><p class="desc"><b>Anticipo :</b></p></td>
            <td><p class="desc">{{$p->number}}</p></td>
        </tr>
        @endforeach
    @endif
    @if ($document->purchase_order)
        <tr>
            <td><p class="desc"><b>Orden de Compra:</b></p></td>
            <td><p class="desc">{{ $document->purchase_order }}</p></td>
        </tr>
    @endif
    @if ($document->quotation_id)
        <tr>
            <td><p class="desc"><b>Cotización:</b></p></td>
            <td><p class="desc">{{ $document->quotation->identifier }}</p></td>
        </tr>
    @endif
    @isset($document->quotation->delivery_date)
        <tr>
            <td><p class="desc">F. Entrega</p></td>
            <td><p class="desc">{{ $document->date_of_issue->addDays($document->quotation->delivery_date)->format('d-m-Y') }}</p></td>
        </tr>
    @endisset
    @isset($document->quotation->sale_opportunity)
        <tr>
            <td><p class="desc"><b>O. Venta</b></p></td>
            <td><p class="desc">{{ $document->quotation->sale_opportunity->number_full}}</p></td>
        </tr>
    @endisset
</table>

@if ($document->guides)
{{--<strong>Guías:</strong>--}}
<table>
    @foreach($document->guides as $guide)
        <tr>
            @if(isset($guide->document_type_description))
                <td class="desc">{{ $guide->document_type_description }}</td>
            @else
                <td class="desc">{{ $guide->document_type_id }}</td>
            @endif
            <td class="desc">:</td>
            <td class="desc">{{ $guide->number }}</td>
        </tr>
    @endforeach
</table>
@endif




@if(!is_null($encomienda))
<table>
    <tr>
        <td class="desc"><b>Destinatario: </b></td>
        <td class="desc"><h4><b>{{ ($encomienda->destinatario) ? $encomienda->destinatario->name: $encomienda->destinatario_nombre }}</b></h4></td>
    </tr>
    <tr>
        <td class="desc"><b>DNI: </b></td>
        <td class="desc">{{ ($encomienda->destinatario) ? $encomienda->destinatario->number: "" }}</td>
    </tr>
    @if ($encomienda->viaje)
        <tr>
            <td class="desc"><b>Origen: </b></td>
            <td class="desc">
                <h2><b>{{ $encomienda->viaje->origen->nombre  }}</b></h2>
            </td>
        </tr>
        <tr style="margin-top: 20px">
            <td class="desc"><h2><b>Destino: </b></h2> </td>
            <td class="desc">
                <h2><b>{{ $encomienda->viaje->destino->nombre }}</b></h2>
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
            <td class="desc"><h2><b>Origen: </b></h2></td>
            <td class="desc text-center">
                <h2><b>{{ $encomienda->terminal->nombre  }}</b></h2>
            </td>
        </tr>
        <tr>
            <td class="desc"><h2><b>Destino: </b></h2> </td>
            <td class="desc text-center">
                <h2><b>{{ $encomienda->destino->nombre }}</b></h2>
            </td>
        </tr>
    @endif
</table>
@endif

@if(!is_null($pasaje))
<table>
    @if ($pasaje->pasajero->name && $document->document_type->id=='01')
    <tr>
        <td class="align-top desc"><h5><b>Pasajero: </b></h5></td>
        <td class="text-left desc"><h4>{{ $pasaje->pasajero->name }}</h4></td>
    </tr>
    @endif
    @if ($pasaje->viaje)
        <tr>
        <td class="desc" with="40"><h3 style="padding: 0px;"><b>Origen: </b></h3> </td>
            <td class="desc">
            <h3><b>{{ $pasaje->viaje->origen->nombre  }}</b></h3>
            </td>
        </tr>
        <tr style="margin-top: 20px">
            <td class="desc"><h3><b>Destino: </b></h3> </td>
            <td class="desc">
                <h3><b>{{ $pasaje->viaje->destino->nombre }}</b></h3>
            </td>
        </tr>
        <tr>
            <td class="align-top desc"><h5><b>Fecha viaje: </b></h5></td>
            <td class="text-left desc"><h4>{{ $pasaje->fecha_salida }}</h4></td>
        </tr>
        <tr>
            <td class="desc"> <h5> <b>Hora viaje: </b> </h5> </td>
            <td class="desc"> <h4> <strong>{{ $pasaje->viaje->hora_salida }}</strong></h4></td>
        </tr>
    @else
        <tr>
            <td class="desc" with="40"><h3 style="padding: 0px;"><b>Origen: </b></h3> </td>
            <td class="desc">
                <h3><b>{{ $pasaje->origen->nombre  }}</b></h3>
            </td>
        </tr>
        <tr style="margin-top: 20px">
            <td class="desc"><h3><b>Destino: </b></h3> </td>
            <td class="desc">
                <h3><b>{{ $pasaje->destino->nombre }}</b></h3>
            </td>
        </tr>
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
            <h4 style="font-size: 30pt;"><b>{{ $pasaje->numero_asiento }}</b></h4>
        </td>
    </tr>

</table>
@endif


@if (count($document->reference_guides) > 0)
<br/>
<strong>Guias de remisión</strong>
<table>
    @foreach($document->reference_guides as $guide)
        <tr>
            <td>{{ $guide->series }}</td>
            <td>-</td>
            <td>{{ $guide->number }}</td>
        </tr>
    @endforeach
</table>
@endif

@if(!is_null($document_base))
<table>
    <tr>
        <td class="desc">Documento Afectado:</td>
        <td class="desc">{{ $affected_document_number }}</td>
    </tr>
    <tr>
        <td class="desc">Tipo de nota:</td>
        <td class="desc">{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>
    </tr>
    <tr>
        <td class="align-top desc">Descripción:</td>
        <td class="text-left desc">{{ $document_base->note_description }}</td>
    </tr>
</table>
@endif

<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr>
        <th class="border-top-bottom desc-9 text-left">CANT.</th>
        <th class="border-top-bottom desc-9 text-left">UNIDAD</th>
        <th class="border-top-bottom desc-9 text-left">DESCRIPCIÓN</th>
        <th class="border-top-bottom desc-9 text-right">P.UNIT</th>
        <th class="border-top-bottom desc-9 text-right">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center desc-9 align-top font-bold">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center desc-9 align-top">{{ $row->item->unit_type_id }}</td>
            <td class="text-left desc-9 align-top font-bold">
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif

                @if($row->total_isc > 0)
                    <br/>ISC : {{ $row->total_isc }} ({{ $row->percentage_isc }}%)
                @endif

                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif

                @if($row->total_plastic_bag_taxes > 0)
                    <br/>ICBPER : {{ $row->total_plastic_bag_taxes }}
                @endif

                @foreach($row->additional_information as $information)
                    @if ($information)
                        <br/>{{ $information }}
                    @endif
                @endforeach

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

                @if($row->charges)
                    @foreach($row->charges as $charge)
                        <br/><small>{{ $document->currency_type->symbol}} {{ $charge->amount}} ({{ $charge->factor * 100 }}%) {{$charge->description }}</small>
                    @endforeach
                @endif

                @if($row->item->is_set == 1)

                 <br>
                 @inject('itemSet', 'App\Services\ItemSetService')
                 @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                     {{$item}}<br>
                 @endforeach
                 {{-- {{join( "-", $itemSet->getItemsSet($row->item_id) )}} --}}
                @endif

                @if($document->has_prepayment)
                    <br>
                    *** Pago Anticipado ***
                @endif
            </td>
            <td class="text-right desc-9 align-top">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right desc-9 align-top font-bold">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="border-bottom"></td>
        </tr>
    @endforeach

    @if ($document->prepayments)
        @foreach($document->prepayments as $p)
        <tr>
            <td class="text-center desc-9 align-top">
                1
            </td>
            <td class="text-center desc-9 align-top">NIU</td>
            <td class="text-left desc-9 align-top">
                ANTICIPO: {{($p->document_type_id == '02')? 'FACTURA':'BOLETA'}} NRO. {{$p->number}}
            </td>
            <td class="text-right  desc-9 align-top">-{{ number_format($p->total, 2) }}</td>
            <td class="text-right  desc-9 align-top">-{{ number_format($p->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="border-bottom"></td>
        </tr>
        @endforeach
    @endif

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

        @if ($document->document_type_id === '07')
            @if($document->total_taxed >= 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
            @endif
        @elseif($document->total_taxed > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif

        @if($document->total_plastic_bag_taxes > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">ICBPER: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="4" class="text-right font-bold desc">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc">{{ number_format($document->total_igv, 2) }}</td>
        </tr>

        @if($document->total_isc > 0)
        <tr>
            <td colspan="4" class="text-right font-bold desc">ISC: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc">{{ number_format($document->total_isc, 2) }}</td>
        </tr>
        @endif

        @if($document->total_discount > 0 && $document->subtotal > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">SUBTOTAL: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->subtotal, 2) }}</td>
            </tr>
        @endif

        @if($document->total_discount > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">DESCUENTO TOTAL: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif

        @if($document->total_charge > 0)
            @if($document->charges)
                @php
                    $total_factor = 0;
                    foreach($document->charges as $charge) {
                        $total_factor = ($total_factor + $charge->factor) * 100;
                    }
                @endphp
                <tr>
                    <td colspan="4" class="text-right font-bold desc">CARGOS ({{$total_factor}}%): {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold desc">{{ number_format($document->total_charge, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" class="text-right font-bold desc">CARGOS: {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold desc">{{ number_format($document->total_charge, 2) }}</td>
                </tr>
            @endif
        @endif

        <tr>
            <td colspan="4" class="text-right font-bold desc">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc">{{ number_format($document->total, 2) }}</td>
        </tr>

        @if(($document->retention || $document->detraction) && $document->total_pending_payment > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">M. PENDIENTE: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_pending_payment, 2) }}</td>
            </tr>
        @endif

        @if($balance < 0)
           <tr>
               <td colspan="4" class="text-right font-bold desc">VUELTO: {{ $document->currency_type->symbol }}</td>
               <td class="text-right font-bold desc">{{ number_format(abs($balance),2, ".", "") }}</td>
           </tr>
        @endif
    </tbody>
</table>
<table class="full-width">
    <tr>

        @foreach(array_reverse((array) $document->legends) as $row)
            <tr>
                @if ($row->code == "1000")
                    <td class="desc pt-1"><p>Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p></td>
                    @if (count((array) $document->legends)>1)
                    <tr><td class="desc pt-2"><span class="font-bold">Leyendas</span></td></tr>
                    @endif
                @else
{{--                    <td class="desc pt-3">{{$row->code}}: {{ $row->value }}</td>--}}
                @endif
            </tr>
        @endforeach
    </tr>


    @if (!is_null($encomienda))
        <tr>
            <td class="text-center desc"><h2>Condición</h2></td>
        </tr>
        <tr>
            <td class="text-center desc">
                <b style="font-size: 40px">{{ $encomienda->estado_pago_id == '1' ? 'PAGADO' : 'PAGO EN DESTINO' }}</b>
            </td>
        </tr>
    @endif


    @if ($document->detraction)
        <tr>
            <td class="desc pt-2 font-bold">
                Operación sujeta al Sistema de Pago de Obligaciones Tributarias
            </td>
        </tr>
    @endif

            <tr>
                <td class="desc">
                    @foreach($document->additional_information as $information)
                        @if ($information)
                            @if ($loop->first)
                                <strong>Información adicional</strong>
                            @endif
                            <p class="desc">@if(\App\CoreFacturalo\Helpers\Template\TemplateHelper::canShowNewLineOnObservation())
                                    {!! \App\CoreFacturalo\Helpers\Template\TemplateHelper::SetHtmlTag($information) !!}
                                @else
                                    {{$information}}
                                @endif</p>
                        @endif
                    @endforeach
                        <br>
                        @if(in_array($document->document_type->id,['01','03']))
                            @foreach($accounts as $account)
                                <p class="desc">
                                    <small>
                                        <span class="font-bold desc">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                                        <span class="font-bold desc">N°:</span> {{$account->number}}
                                        @if($account->cci)
                                            <span class="font-bold desc">CCI:</span> {{$account->cci}}
                                        @endif
                                    </small>
                                </p>
                            @endforeach
                        @endif
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ccc;padding: 5px">
                    <table class="full-width">
                        <tr>
                            <td style="width: 75%">
                                @php
                                    if($document->payment_condition_id === '01') {
                                        //$paymentCondition = \App\Models\Tenant\PaymentMethodType::where('id', '10')->first();
                                        $paymentCondition = "CONTADO";
                                    }
                                    else if($document->payment_condition_id === '02') {
                                        $paymentCondition = "CRÉDITO";
                                    }
                                    else if($document->payment_condition_id === '03') {
                                        $paymentCondition = "CRÉDITO CON CUOTAS";
                                    }
                                @endphp

                                <p class="font-bold" style="font-size: small">CONDICIÓN DE PAGO: </p>
                                <p>{{ $paymentCondition}}</p>

                                @if($document->payment_method_type_id)
                                    <p class="desc pt-5">
                                        <b>MÉTODO DE PAGO: </b>{{ $document->payment_method_type->description }}
                                    </p>
                                @endif

                                @if ($document->payment_condition_id === '01')

                                    @if($payments->count())

                                            <p class="desc pt-2">
                                                <strong>PAGOS:</strong>
                                            </p>

                                        @foreach($payments as $row)

                                                <p>&#8226; {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</p>

                                        @endforeach
                                    @endif
                                @else
                                    @foreach($document->fee as $key => $quote)

                                    <p class="desc">&#8226; {{ (empty($quote->getStringPaymentMethodType()) ? 'Cuota #'.( $key + 1) : $quote->getStringPaymentMethodType()) }} / Fecha: {{ $quote->date->format('d-m-Y') }} / Monto: {{ $quote->currency_type->symbol }}{{ $quote->amount }}</p>

                                    @endforeach
                                @endif

                                <strong>Vendedor:</strong>

                                    @if ($document->seller)
                                        <p class="desc">{{ $document->seller->name }}</p>
                                    @else
                                        <p class="desc">{{ $document->user->name }}</p>
                                    @endif

                            </td>

                            <td class="text-center pt-0" style="width: 35%">
                                <img width="100" class="qr_code" src="data:image/png;base64, {{ $document->qr }}" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center desc"><p>Código Hash: {{ $document->hash }}</p></td>
            </tr>

    @if ($document->terms_condition)
        <tr>
            <td class="desc">
                <br>
                <h6 style="font-size: 10px; font-weight: bold;">Términos y condiciones del servicio</h6>
                {!! $document->terms_condition !!}
            </td>
        </tr>
    @endif
    <tr>
        <td class="text-center desc">
            <p>Representación impresa del Comprobante de Pago Electrónico.</p>
        </td>
    </tr>

    <tr>
        <td class="text-center desc pt-2"> <p> Para consultar el comprobante ingresar a {!! url('/buscar') !!}</p></td>
    </tr>
    @if ($legend_footer)
        <tr>
            <td class="text-center desc pt-2"><p><b>BIENES TRANSFERIDOS Y/O SERVICIOS PRESTADOS EN LA AMAZONIA PARA SER CONSUMIDOS EN LA MISMA</b></p></td>
        </tr>
    @endif
</table>
@if(!is_null($pasaje))
    <pagebreak/>

    <table class="full-width">
        <tr>
            <td class="text-center pt-4" colspan="2"><h5><b>Control REF: {{$document_number}}</b></h5></td>
        </tr>
        <tr><td colspan="2"><b>PASAJERO:</b></td></tr>
        <tr><td colspan="2">SR(A): {{$pasaje->pasajero->number}} - {{$pasaje->pasajero->name}}</td></tr>

        <tr><td colspan="2"><b>AGENCIA DE EMBARQUE:</b></td></tr>
        <tr><td colspan="2">{{ ($establishment->address !== '-')? $establishment->address : '' }}</td></tr>

        @if ($pasaje->viaje)
            <tr><td width="50%"><b>ORIGEN:</b> {{ $pasaje->viaje->origen->nombre  }}</td> <td width="50%"><b>FECHA:</b> {{ $pasaje->fecha_salida }}</td></tr>
            <tr><td><b>DESTINO:</b> {{ $pasaje->viaje->destino->nombre  }}</td> <td><b>HORA VIAJE:</b> {{$pasaje->viaje->hora_salida}}</td></tr>
        @endif
        <tr><td class="pt-3"><b>ASIENTO: {{ $pasaje->numero_asiento }}</b></td> <td class="pt-3"><b>PRECIO: {{ number_format($document->total, 2) }}</b></td></tr>

        <tr><td class="pt-3" colspan="2">Fecha - Hora impresión: {{ $document->date_of_issue->format('Y-m-d') }} - {{ $document->time_of_issue }} </td></tr>
        <tr>
            <td colspan="2">USUARIO:
                @if ($document->seller)
                    {{ $document->seller->name }}
                @else
                    {{ $document->user->name }}
                @endif
           </td>
        </tr>

        <tr>
            <td class="text-center pt-3" colspan="2"><b>************************************</b></td>
        </tr>
        <tr>
            <td class="text-center" colspan="2">Este documento es con fines de control interno de la empresa y no tiene validez tributario.</td>
        </tr>
        <tr>
            <td class="text-center" colspan="2"><b>************************************</b></td>
        </tr>
    </table>
@endif
</body>
</html>
