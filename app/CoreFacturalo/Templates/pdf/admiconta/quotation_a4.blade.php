@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $accounts = \App\Models\Tenant\BankAccount::all();
    $configuracion = \App\Models\Tenant\Configuration::get();

    $color1= $configuracion[0]['color1'];
    $color2= $configuracion[0]['color2'];
    $formato=$configuracion[0]['formats'];
    $fondo=$configuracion[0]['fondo'];

    $tittle = $document->prefix.'-'.str_pad($document->id, 8, '0', STR_PAD_LEFT);

    $logo = "storage/uploads/logos/{$company->logo}";
    if($establishment->logo) {
        $logo = "{$establishment->logo}";
    }
    $config = \App\Models\Tenant\Configuration::first();
    $miimage = public_path("storage/uploads/fondos/{$fondo}");

@endphp
<html>
<head>
    {{--<title>{{ $tittle }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
<div class="" style="position: absolute; text-align: center; z-index: 0; top: -5px; left: -10px;">
   @if($fondo!='')
    <img src="data:{{mime_content_type($miimage)}};base64, {{base64_encode(file_get_contents($miimage))}}" alt="fondo" class="">
    @endif
</div>
<div style="position: absolute; left: 157px;top:7px;width: 79%;text-align:right;">COTIZACIÓN: {{ $tittle }}</div>
<div style="z-index: 1; position: absolute; left: 157px;padding-left:3px;padding-right: 4px;margin-top: -30px">
<table class="full-width">
    <tr>
        <td width="70%">
            <div class="text-center">
                <h4 class=""><b>{{ $company->name }}</b></h4>
                <h5>{{ 'RUC '.$company->number }}</h5>
                <h6 style="text-transform: uppercase;">
                    {{ ($establishment->address !== '-')? $establishment->address : '' }}
                    {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                    {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                    {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                </h6>

                @isset($establishment->trade_address)
                    <h6>{{ ($establishment->trade_address !== '-')? 'D. Comercial: '.$establishment->trade_address : '' }}</h6>
                @endisset
                <h6>{{ ($establishment->telephone !== '-')? 'Central telefónica: '.$establishment->telephone : '' }}</h6>

                <h6>{{ ($establishment->email !== '-')? 'Email: '.$establishment->email : '' }}</h6>

                @isset($establishment->web_address)
                    <h6>{{ ($establishment->web_address !== '-')? 'Web: '.$establishment->web_address : '' }}</h6>
                @endisset

                @isset($establishment->aditional_information)
                    <h6>{{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }}</h6>
                @endisset
            </div>
        </td>
        @if($company->logo)
            <td width="30%" style="text-align:center;">
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("{$logo}"))}};base64, {{base64_encode(file_get_contents(public_path("{$logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 250px;">
                </div>
            </td>
        @else
            <td width="30%">
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            </td>
        @endif
    </tr>
</table>
<table class="full-width mt-4">
    <tr>
        <td width="15%"><b>Cliente:</b></td>
        <td width="45%">{{ $customer->name }}</td>
        <td width="25%"><b>Fecha de emisión:</b></td>
        <td width="15%">{{ $document->date_of_issue->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <td><b>{{ $customer->identity_document_type->description }}:</b></td>
        <td>{{ $customer->number }}</td>
        @if($document->date_of_due)
            <td width="25%"><b>Tiempo de Validez:</b></td>
            <td width="15%">{{ $document->date_of_due }}</td>
        @endif
    </tr>
    @if ($customer->address !== '')
        <tr>
            <td class="align-top"><b>Dirección:</b></td>
            <td colspan="">
                {{ $customer->address }}
                {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
            </td>
            @if($document->delivery_date)
                <td width="25%"><b>Tiempo de Entrega:</b></td>
                <td width="15%">{{ $document->delivery_date }}</td>
            @endif
        </tr>
    @endif
    @if ($document->payment_method_type)
        <tr>
            <td class="align-top"><b>T. Pago:</b></td>
            <td colspan="">
                {{ $document->payment_method_type->description }}
            </td>
            @if($document->sale_opportunity)
                <td width="25%"><b>O. Venta:</b></td>
                <td width="15%">{{ $document->sale_opportunity->number_full }}</td>
            @endif
        </tr>
    @endif
    @if ($document->account_number)
        <tr>
            <td class="align-top"><b>N° Cuenta:</b></td>
            <td colspan="3">
                {{ $document->account_number }}
            </td>
        </tr>
    @endif
    @if ($document->shipping_address)
        <tr>
            <td class="align-top"><b>Dir. Envío:</b></td>
            <td colspan="3">
                {{ $document->shipping_address }}
            </td>
        </tr>
    @endif
    @if ($customer->telephone)
        <tr>
            <td class="align-top"><b>Teléfono:</b></td>
            <td colspan="3">
                {{ $customer->telephone }}
            </td>
        </tr>
    @endif
    <tr>
        <td class="align-top"><b>Vendedor:</b></td>
        <td colspan="3">
            @if ($document->seller->name)
                {{ $document->seller->name }}
            @else
                {{ $document->user->name }}
            @endif
        </td>
    </tr>
    @if ($document->contact)
    <tr>
        <td class="align-top">Contacto:</td>
        <td colspan="3">
            {{ $document->contact }}
        </td>
    </tr>
    @endif
    @if ($document->phone)
    <tr>
        <td class="align-top">Telf. Contacto:</td>
        <td colspan="3">
            {{ $document->phone }}
        </td>
    </tr>
    @endif
</table>

<table class="full-width mt-3">
    @if ($document->description)
        <tr>
            <td width="15%" class="align-top"><b>Observación:</b> </td>
            <td width="85%">{{ $document->description }}</td>
        </tr>
    @endif
</table>

@if ($document->guides)
    <br/>
    {{--<strong>Guías:</strong>--}}
    <table>
        @foreach($document->guides as $guide)
            <tr>
                @if(isset($guide->document_type_description))
                    <td><b>{{ $guide->document_type_description }}</b></td>
                @else
                    <td><b>{{ $guide->document_type_id }}</b></td>
                @endif
                <td>:</td>
                <td>{{ $guide->number }}</td>
            </tr>
        @endforeach
    </table>
@endif

<table class="full-width mt-1 mb-1">
    <thead class="encabezado">
    <tr class="bg-grey- fondo-ficho">
        <th class="text-center text-white py-2" width="8%" style="background: {{$color1}}">CANT.</th>
        <th class="text-center text-white py-2" width="10%" style="background: {{$color1}}">UNIDAD</th>
        <th class="text-left text-white py-2" style="background: {{$color1}}">DESCRIPCIÓN</th>
        <th class="text-left text-white py-2" width="8%" style="background: {{$color1}}">MARCA</th>
        <th class="text-left text-white py-2" width="9%" style="background: {{$color2}}">MODELO</th>
        <th class="text-right text-white py-2" width="12%" style="background: {{$color2}}">P.UNIT</th>
        <th class="text-right text-white py-2" width="8%" style="background: {{$color2}}">DTO.</th>
        <th class="text-right text-white py-2" width="10%" style="background: {{$color2}}">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        @php
        $brand = (!empty($row->item) &&
                 !empty($row->item->brand) &&
                 !empty($row->item->brand->name)
                 ) ?
            $row->item->brand->name :
                '';
        @endphp
        <tr>
            <td class="text-center align-top">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            @inject('unitType', 'App\Services\UnitTypeService')
            <td class="text-center align-top borde-gris">{{ $unitType->getDescription($row->item->unit_type_id ) }}</td>
            <td class="text-left">
                @if($row->item->name_product_pdf ?? false)
                    {!!$row->item->name_product_pdf ?? ''!!}
                @else
                    {!!$row->item->description!!}
                @endif
                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                @endif

                  @if($row->item !== null && property_exists($row->item,'extra_attr_value') && $row->item->extra_attr_value != '')
                    <br/><span style="font-size: 9px">{{$row->item->extra_attr_name}}: {{ $row->item->extra_attr_value }}</span>
                @endif

                @if($row->item->is_set == 1)
                 <br>
                    @inject('itemSet', 'App\Services\ItemSetService')
                    @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                        {{$item}}<br>
                    @endforeach
                @endif

            </td>
            <td class="text-left">{{ $brand }}</td>
            <td class="text-left">{{ $row->item->model ?? '' }}</td>
            <td class="text-right align-top">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right align-top">
                @if($row->discounts)
                    @php
                        $total_discount_line = 0;
                        foreach ($row->discounts as $disto) {
                            $total_discount_line = $total_discount_line + $disto->amount;
                        }
                    @endphp
                    {{ number_format($total_discount_line, 2) }}
                @else
                0
                @endif
            </td>
            <td class="text-right align-top">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="8" class="border-bottom"></td>
        </tr>
    @endforeach
        @if($document->total_exportation > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif
       @if($document->total_discount > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="7" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr>
        <tr>
            <td colspan="7" class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
</table>
<table class="full-width">
    <tr>
        <td width="65%" style="text-align: top; vertical-align: top;">
            <br>
            @foreach($accounts as $account)
                <p>
                <span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                <span class="font-bold">N°:</span> {{$account->number}}
                @if($account->cci)
                - <span class="font-bold">CCI:</span> {{$account->cci}}
                @endif
                </p>
            @endforeach
        </td>
    </tr>
    <tr>
        {{-- <td width="65%">
            @foreach($document->legends as $row)
                <p>Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
            @endforeach
            <br/>
            <strong>Información adicional</strong>
            @foreach($document->additional_information as $information)
                <p>{{ $information }}</p>
            @endforeach
        </td> --}}
    </tr>
</table>
<br>
<table class="full-width">
<tr>
    <td>
    <strong>PAGOS:</strong> </td></tr>
        @php
            $payment = 0;
        @endphp
        @foreach($document->payments as $row)
            <tr><td>- {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment }}</td></tr>
            @php
                $payment += (float) $row->payment;
            @endphp
        @endforeach
        <tr><td><strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}</td>
    </tr>

</table>
</div>
</body>
</html>
