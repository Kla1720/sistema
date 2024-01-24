@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    // $document_type_driver = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->driver->identity_document_type_id);


    $allowed_items = 60;
    $quantity_items = $document->items()->count();
    $cycle_items = $allowed_items - ($quantity_items * 5);
    $total_weight = 0;

    $configuracion = \App\Models\Tenant\Configuration::get();

    $color1= $configuracion[0]['color1'];
    $color2= $configuracion[0]['color2'];
    $formato=$configuracion[0]['formats'];
    $fondo=$configuracion[0]['fondo'];
@endphp
<html>
<head>
</head>
<body>
<table class="full-width">
    <tr>
        @if($company->logo)
            <td width="10%">
                <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{ $company->name }}" class="company_logo" style="max-width: 300px">
            </td>
        @else
            <td width="10%">
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            </td>
        @endif
        <td width="50%" class="pl-3">
            <div class="text-left">
                <h3 class="">{{ $company->name }}</h3>
                <h4>{{ 'RUC '.$company->number }}</h4>
                <h5 style="text-transform: uppercase;">
                    {{ ($establishment->address !== '-')? $establishment->address : '' }}
                    {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                    {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                    {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                </h5>
                <h5>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h5>
                <h5>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h5>
                @if($company->number_mtc)
                    <h5><b>N° AUTORIZACIÓN MTC:</b> {{ $company->number_mtc}}</h5>
                @endif

            </div>
        </td>
        <td width="40%" class="py-2 px-2 text-center" style="border: 1px solid <?php echo e($color1); ?>">
            <h3 class="font-bold"><?php echo e('R.U.C. '.$company->number); ?></h3>
            <h4 class="text-center text-white font-bold" style="background: <?php echo e($color1); ?>">GUÍA DE REMISIÓN ELECTRÓNICA TRANSPORTISTA</h4>
            <h3 class="text-center font-bold">{{ $document_number }}</h3>
        </td>
    </tr>
</table>

<div class="mt-2" style="border: 1px solid black; border-radius: 10px">
    <table class="full-width mt-10 mb-10">
        <tbody >
        <tr>
            <td class="pl-3 text-center" width="35%"><strong>Fecha Emisión:</strong> {{ $document->date_of_issue->format('Y-m-d') }}</td>
            <td class="pl-3 text-center" width="35%"><strong>Fecha Inicio de Traslado:</strong> {{ $document->date_of_shipping->format('Y-m-d') }}</td>
            <td class="pl-3 text-center"width="35%"><strong >Peso Bruto Total({{ $document->unit_type_id }})</strong> : {{ $document->total_weight }}
        </tr>
        </tbody>
    </table>
</div>

<table class="mt-2 full-width mt-10 mb-10">
    <tbody>
    <tr>
        <td class="pl-3" width="48%" style="border: 1px solid black">
            <table>
                <tr>
                    <td class="full-width">
                        <strong style="font-size: 11px">PUNTO DE PARTIDA</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ $document->sender_address_data['location_id'] }} - {{ $document->sender_address_data['address'] }}
                    </td>
                </tr>
            </table>

        </td>
        <td width="4%">

        </td>
        <td class="pl-3" width="48%" style="border: 1px solid black">
            <table>
                <tr>
                    <td class="full-width">
                        <strong style="font-size: 11px">PUNTO DE LLEGADA</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{ $document->receiver_address_data['location_id'] }} - {{ $document->receiver_address_data['address'] }}
                    </td>
                </tr>
            </table>

        </td>
    </tr>
    </tbody>
</table>

<table class="mt-2 full-width mt-10 mb-10">
    <tbody>
    <tr>
        <td class="pl-3" width="48%" style="border: 1px solid black">
            <table>
                <tr>
                    <td class="full-width">
                        <strong style="font-size: 11px">REMITENTE</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-size: 10px">{{ $document->sender_data['name'] }}- {{ $document->sender_data['identity_document_type_description'] }} {{ $document->sender_data['number'] }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>RUC:</strong> {{ $document->sender_data['number'] }}

                    </td>
                </tr>
            </table>

        </td>
        <td width="4%">

        </td>
        <td class="pl-3" width="48%" style="border: 1px solid black">
            <table>
                <tr>
                    <td class="full-width">
                        <strong style="font-size: 11px">DESTINATARIO</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-size: 10px">{{ $document->receiver_data['name'] }}- {{ $document->receiver_data['identity_document_type_description'] }} {{ $document->receiver_data['number'] }}</p>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>RUC:</strong> {{ $document->receiver_data['number'] }}

                    </td>
                </tr>
            </table>

        </td>
    </tr>
    </tbody>
</table>

<div class="mt-2" style="border: 1px solid black; border-radius: 10px">
    <table class="full-width mt-10 mb-10">
        <thead>
        <tr>
            <th class="border-bottom text-left" colspan="2">DATOS DE LOS VEHÍCULOS</th>
        </tr>
        </thead>
        <tbody>
        @if($document->transport_data)
            <tr>
                <td>
                    <b>PRINCIPAL:</b>
                    <p><b>N° de placa:</b> {{ $document->transport_data['plate_number'] }} <br></p>
                    <p><b>N° de TUCE o Cert. de Hab. Vehicular:</b> {{ $document->transport_data['hab_veh'] }} <br></p>

                </td>
                @if($document->transport2_data)
                    <td>
                        <b>SECUNDARIO:</b>
                        <p><b>N° de placa aqui:</b> {{ $document->transport2_data['plate_number'] }} <br></p>
                        <p><b>N° de TUCE o Cert. de Hab. Vehicular:</b> {{ $document->transport2_data['hab_veh'] }} <br></p>
                    </td>
                @endif
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="mt-2" style="border: 1px solid black; border-radius: 10px">
    <table class="full-width mt-10 mb-10">
        <thead>
        <tr>
            <th class="border-bottom text-left" colspan="2">DATOS DE LOS CONDUCTORES</th>
        </tr>
        </thead>
        <tbody>

        @if($document->driver)
            <tr>
                <td>
                    <b>PRINCIPAL:</b>
                    <p><b>Conductor:</b> {{ $document->driver->number }} <br></p>
                    <p><b>Licencia del conductor:</b> {{ $document->driver->license }} <br></p>

                </td>
                @if($document->driver2_data)
                    <td>
                        <b>SECUNDARIO:</b>
                        <p><b>Conductor:</b> {{ $document->driver2_data['number'] }} <br></p>
                        <p><b>Licencia del conductor:</b> {{ $document->driver2_data['license'] }} <br></p>
                    </td>
                @endif
            </tr>
        @endif
        </tbody>
    </table>
</div>
<table class="full-width border-box mt-10 mb-10">
    <thead class="">
    <tr>
        <th class="border-top-bottom py-1 text-center text-white" style="background: <?php echo e($color1); ?>">Item</th>
        <th class="border-top-bottom py-1 text-center text-white" style="background: <?php echo e($color1); ?>">Código</th>
        <th class="border-top-bottom py-1 text-left text-white" style="background: <?php echo e($color1); ?>">Descripción</th>
        <th class="border-top-bottom py-1 text-left text-white" style="background: <?php echo e($color2); ?>">Modelo</th>
        <th class="border-top-bottom py-1 text-center text-white" style="background: <?php echo e($color2); ?>">Unidad</th>
        <th class="border-top-bottom py-1 text-right text-white"style="background: <?php echo e($color2); ?>" >Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center cell-solid-rl" style="font-size:0.8em">{{ $loop->iteration }}</td>
            <td class="text-center cell-solid-rl" style="font-size:0.8em">{{ $row->item->internal_id }}</td>
            <td class="text-left cell-solid-rl" style="font-size:0.8em">
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
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
                @if($row->relation_item->is_set == 1)
                    <br>
                    @inject('itemSet', 'App\Services\ItemSetService')
                    @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                        {{$item}}<br>
                    @endforeach
                @endif

                @if($document->has_prepayment)
                    <br>
                    *** Pago Anticipado ***
                @endif
            </td>
            <td class="text-left cell-solid-rl" style="font-size:0.8em">{{ $row->item->model ?? '' }}</td>
            <td class="text-center cell-solid-rl" style="font-size:0.8em">{{ $row->item->unit_type_id }}</td>
            <td class="text-right cell-solid-rl" style="font-size:0.8em">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
        </tr>
    @endforeach
    <?php for($i = 0; $i < $cycle_items; $i++): ?>
    <tr>
        <td class="p-1 text-center align-top desc cell-solid-rl"></td>
        <td class="p-1 text-center align-top desc cell-solid-rl"></td>
        <td class="p-1 text-right align-top desc cell-solid-rl"></td>
        <td class="p-1 text-right align-top desc cell-solid-rl"></td>
        <td class="p-1 text-right align-top desc cell-solid-rl"></td>
        <td class="p-1 text-right align-top desc cell-solid-rl"></td>
    </tr>
    <?php endfor; ?>
    <tr>
        <td class="cell-solid-offtop"></td>
        <td class="cell-solid-offtop"></td>
        <td class="cell-solid-offtop"></td>
        <td class="cell-solid-offtop"></td>
        <td class="cell-solid-offtop"></td>
        <td class="cell-solid-offtop"></td>
    </tr>
    </tbody>
</table>
@if($document->observations)
    <table class="full-width border-box mt-10 mb-10">
        <tr>
            <td class="text-bold border-bottom font-bold">OBSERVACIONES</td>
        </tr>
        <tr>
            <td>{{ $document->observations }}</td>
        </tr>
    </table>
@endif
@if($document->qr)
    <table class="full-width">
        <tr>
            <td class="text-left">
                <img src="data:image/png;base64, {{ $document->qr }}" style="margin-right: -10px;"/>
            </td>
        </tr>
    </table>
@endif
</body>
</html>
