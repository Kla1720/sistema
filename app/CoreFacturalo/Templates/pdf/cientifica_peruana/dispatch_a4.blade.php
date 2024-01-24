@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    // $document_type_driver = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->driver->identity_document_type_id);


    $allowed_items = 80;
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
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
<table class="full-width">
    <tr>
        <?php if($company->logo): ?>
        <td width="20%">
            <img src="data:<?php echo e(mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))); ?>;base64, <?php echo e(base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))); ?>" alt="<?php echo e($company->name); ?>" alt="<?php echo e($company->name); ?>"  class="company_logo" style="max-width: 300px">
        </td>
        <?php else: ?>
        <td width="20%">

        </td>
        <?php endif; ?>
        <td width="45%" class="pl-3">
            <div class="text-left">
                <p style="font-size: medium"><b><?php echo e($company->name); ?></b></p>
                <h5><b>RUC: </b><?php echo e($company->number); ?></h5>
                <p style="text-transform: uppercase;font-size: 11px">
                    <?php echo e(($establishment->address !== '-')? $establishment->address : ''); ?>

                    <?php echo e(($establishment->district_id !== '-')? ', '.$establishment->district->description : ''); ?>

                    <?php echo e(($establishment->province_id !== '-')? ', '.$establishment->province->description : ''); ?>

                    <?php echo e(($establishment->department_id !== '-')? '- '.$establishment->department->description : ''); ?>

                </p>

                <?php if($establishment->email!== '-'): ?>
                <p><b>Correo: </b> <?php echo e($establishment->email); ?></p>
                <?php endif; ?>
                <?php if($establishment->telephone!== '-'): ?>
                <p><b>Teléfono: </b> <?php echo e($establishment->telephone); ?></p>
                <?php endif; ?>
            </div>
        </td>
        <td width="35%" class="py-2 px-2 text-center" style="border: 1px solid <?php echo e($color1); ?>">
            <h3 class="font-bold"><?php echo e('R.U.C. '.$company->number); ?></h3>
            <h5 class="text-center text-white font-bold" style="background: <?php echo e($color1); ?>">GUIA DE REMISION ELECTRÓNICA REMITENTE</h5>
            <h3 class="text-center font-bold"><?php echo e($document_number); ?></h3>
        </td>
    </tr>
</table>
<div class="mt-2" style="border: 1px solid black; border-radius: 10px">
    <table class="full-width mt-10 mb-10">
        <tbody >
        <tr>
            <td class="pl-3 text-left" width="35%"><strong>Fecha Emisión:</strong> <?php echo e($document->date_of_issue->format('d/m/Y')); ?></td>
            <td class="pl-3 text-center" width="35%"><strong>Fecha de Traslado:</strong> <?php echo e($document->date_of_shipping->format('d/m/Y')); ?></td>
            <td class="pl-3 text-right"width="35%"><strong>Número de factura:</strong>
            <?php if($document->reference_document): ?><?php echo e($document->reference_document->number_full); ?>

            <?php else: ?>
            <td class="pl-3"></td>
            <?php endif; ?>
            </td>
            <td>
            <?php if($document->reference_document): ?>
            <?php if($document->reference_document->purchase_order): ?>
            <td class="pl-3"><strong>O. COMPRA:</strong> <?php echo e($document->reference_document->purchase_order); ?></td>
            <?php else: ?>
            <td class="pl-3"></td>
            <?php endif; ?>
            <?php else: ?>
            <td class="pl-3"></td>
            <?php endif; ?>
            </td>
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
                        <strong style="font-size: 11px">DIRECCIÓN DE PARTIDA</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo e($document->origin->address); ?> - <?php echo e($document->origin->location_id); ?>

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
                        <strong style="font-size: 11px">DIRECCIÓN DE LLEGADA</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-size: 10px"><?php echo e($document->delivery->address); ?> - <?php echo e($document->delivery->location_id); ?></p>
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
                        <p style="font-size: 10px"><?php echo e($company->name); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>RUC:</strong> <?php echo e($company->number); ?>

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
                        <p style="font-size: 10px"><?php echo e($customer->name); ?></p>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>RUC:</strong> <?php echo e($customer->number); ?>

                    </td>
                </tr>
            </table>

        </td>
    </tr>
    </tbody>
</table>
<div class="mt-2" style="border: 1px solid black; border-radius: 10px">
    <table class="full-width mt-10 mb-10">
        <tbody>
        <tr>
            <td class="pl-3"><strong>Motivo Traslado:</strong> <?php echo e($document->transfer_reason_type->description); ?></td>
            <td class="pr-3 text-right"><strong>Modalidad de Transporte:</strong> <?php echo e($document->transport_mode_type->description); ?></td>
        </tr>

        </tbody>
    </table>
</div>
<table class="full-width mt-10 mb-10">
    <tr>
        <td width="50%" class="border-box pl-3" style="border: 1px solid black">
            <table class="full-width">
                <tr>
                    <td colspan="2"><strong style="font-size: 11px">EMPRESA DE TRANSPORTE</strong></td>
                </tr>
                @if($document->transport_mode_type_id === '01')
                    @php
                        $document_type_dispatcher = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->dispatcher->identity_document_type_id);
                    @endphp
                    <tr>
                        <td><strong>Transportista:</strong> <span style="font-size: 10px"><?php echo e($document->dispatcher->name); ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>{{$document_type_dispatcher->description}}:</strong> {{$document->dispatcher->number}}</td>
                    </tr>
                @endif
                <tr>
                    <td></td>

                </tr>
            </table>
        </td>
        <td width="3%"></td>
        <td width="45%" class="pl-3" style="border: 1px solid black; border-radius: 10px">
            <table class="full-width" >
                <tr>
                    <td colspan="2"><strong style="font-size: 11px">UNIDAD DE TRANSPORTE - CONDUCTOR</strong></td>
                </tr>
                @if($document->transport_mode_type_id === '02')
                    <tr>
                        @if($document->license_plate)
                            <td>Número de placa del vehículo: {{ $document->license_plate }}</td>
                        @endif
                        @if($document->driver->number)
                            <td>Conductor: {{ $document->driver->number }}</td>
                        @endif
                    </tr>
                    <tr>
                        @if($document->secondary_license_plates)
                            @if($document->secondary_license_plates->semitrailer)
                                <td>Número de placa semirremolque: {{ $document->secondary_license_plates->semitrailer }}</td>
                            @endif
                        @endif
                        @if($document->driver->license)
                            <td>Licencia del conductor: {{ $document->driver->license }}</td>
                        @endif
                    </tr>
                @endif
            </table>
        </td>
    </tr>
</table>


<table class="full-width mt-0 mb-0">
    <thead >
    <tr class="">
        <th class="text-center py-1 desc text-white"  width="3%" style="background: <?php echo e($color1); ?>">ITEM</th>
        <th class="text-center py-1 desc text-white"  width="10%" style="background: <?php echo e($color1); ?>">CÓDIGO</th>
        <th class="text-center py-1 desc text-white"  width="4%" style="background: <?php echo e($color1); ?>">CANT.</th>
        <th class="text-center py-1 desc text-white"  width="2%" style="background: <?php echo e($color2); ?>">U.M.</th>
        <th class="text-center py-1 desc text-white"  width="46%" style="background: <?php echo e($color2); ?>">DESCRIPCIÓN</th>
        <th class="text-center py-1 desc text-white"   width="8%" style="background: <?php echo e($color2); ?>">PESO</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center cell-solid-rl" style="font-size:0.8em">{{ $loop->iteration }}</td>
            <td class="text-center cell-solid-rl" style="font-size:0.8em">{{ $row->item->internal_id }}</td>
            <td class="text-center cell-solid-rl" style="font-size:0.8em">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center cell-solid-rl" style="font-size:0.8em">{{ $row->item->unit_type_id }}</td>
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


<table class="full-widthmt-10 mb-10">
    <tr>
        <td width="75%">
            <table class="full-width">
                <tr>
                    <?php
                    $total_packages = $document->items()->sum('quantity');
                    ?>
                    <td ><strong>TOTAL NÚMERO DE BULTOS:</strong>
                        <?php if(((int)$total_packages != $total_packages)): ?>
                            <?php echo e($total_packages); ?>

                        <?php else: ?>
                            <?php echo e(number_format($total_packages, 0)); ?>

                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </td>

        <td width="25%" class="pl-3 text-right">
            <table class="full-width">
                <tr>
                    <td><strong>PESO TOTAL:</strong> <?php echo e($document->total_weight); ?>  <?php echo e($document->unit_type_id); ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>



<table class="full-width border-box mt-10 mb-10">
    <tr>
        <td width="50%" class="border-box pl-3">
            <table class="full-width">
                <tr>
                    <td colspan="2"><strong>OBSERVACIONES:</strong></td>
                </tr>
                <tr>
                    <td><?php echo e($document->observations); ?></td>
                </tr>
            </table>
        </td>
        <td width="3%"></td>

        <td width="47%" class="">
            @if($document->qr)
                <table class="full-width">
                    <tr>
                        <td class="text-left">
                            <img src="data:image/png;base64, {{ $document->qr }}" style="margin-right: -10px;"/>
                        </td>
                    </tr>
                </table>
            @endif
        </td>

    </tr>
</table>
@if ($document->data_affected_document)
    @php
        $document_data_affected_document = $document->data_affected_document;

    $number = (property_exists($document_data_affected_document,'number'))?$document_data_affected_document->number:null;
    $series = (property_exists($document_data_affected_document,'series'))?$document_data_affected_document->series:null;
    $document_type_id = (property_exists($document_data_affected_document,'document_type_id'))?$document_data_affected_document->document_type_id:null;

    @endphp
    @if($number !== null && $series !== null && $document_type_id !== null)

        @php
            $documentType  = App\Models\Tenant\Catalogs\DocumentType::find($document_type_id);
            $textDocumentType = $documentType->getDescription();
        @endphp
        <table class="full-width border-box">
            <tr>
                <td class="text-bold border-bottom font-bold">{{$textDocumentType}}</td>
            </tr>
            <tr>
                <td>{{$series }}-{{$number}}</td>
            </tr>
        </table>
    @endif
@endif
@if ($document->reference_order_form_id)
    <table class="full-width border-box">
        @if($document->order_form)
            <tr>
                <td class="text-bold border-bottom font-bold">ORDEN DE PEDIDO</td>
            </tr>
            <tr>
                <td>{{ ($document->order_form) ? $document->order_form->number_full : "" }}</td>
            </tr>
        @endif
    </table>

@elseif ($document->order_form_external)
    <table class="full-width border-box">
        <tr>
            <td class="text-bold border-bottom font-bold">ORDEN DE PEDIDO</td>
        </tr>
        <tr>
            <td>{{ $document->order_form_external }}</td>
        </tr>
    </table>

@endif


@if ($document->reference_sale_note_id)
    <table class="full-width border-box">
        @if($document->sale_note)
            <tr>
                <td class="text-bold border-bottom font-bold">NOTA DE VENTA</td>
            </tr>
            <tr>
                <td>{{ ($document->sale_note) ? $document->sale_note->number_full : "" }}</td>
            </tr>
        @endif
    </table>
@endif
@if ($document->terms_condition)
    <br>
    <table class="full-width">
        <tr>
            <td>
                <h6 style="font-size: 12px; font-weight: bold;">Términos y condiciones del servicio</h6>
                {!! $document->terms_condition !!}
            </td>
        </tr>
    </table>
@endif

</body>
</html>
