<?php
$establishment = $document->establishment;
$customer = $document->customer;
//$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

$document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
// $document_type_driver = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->driver->identity_document_type_id);
$document_type_dispatcher = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->dispatcher->identity_document_type_id);

$allowed_items = 90;
$quantity_items = $document->items()->count();
$cycle_items = $allowed_items - ($quantity_items * 5);
$total_weight = 0;

$configuracion = \App\Models\Tenant\Configuration::get();

$color1= $configuracion[0]['color1'];
$color2= $configuracion[0]['color2'];
$formato=$configuracion[0]['formats'];
$fondo=$configuracion[0]['fondo'];



?>
<html>
<head>


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
                <tr>
                    <td><strong>Transportista:</strong> <span style="font-size: 10px"><?php echo e($document->dispatcher->name); ?></span></td>
                </tr>
                <tr>
                    <td><strong><?php echo e($document_type_dispatcher->description); ?>:</strong> <?php echo e($document->dispatcher->number); ?></td>
                </tr>
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
                <?php if($document->license_plate): ?>
                <tr>
                    <td><strong style="font-size: 9px">Placa del vehículo:</strong> <?php echo e($document->license_plate); ?></td>
                </tr>
                <?php endif; ?>
                <?php if($document->secondary_license_plates): ?>
                <?php if($document->secondary_license_plates->semitrailer): ?>
                <td>Placa semirremolque: <?php echo e($document->secondary_license_plates->semitrailer); ?></td>
                <?php endif; ?>
                <?php endif; ?>
                <?php if($document->driver->number): ?>
                <tr>
                    <td><strong>Dni del conductor:</strong>: <?php echo e($document->driver->number); ?></td>
                </tr>
                <?php endif; ?>
                <?php if($document->driver->license): ?>
                <tr>
                    <td><strong>N° Licencia:</strong> <?php echo e($document->driver->license); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </td>
    </tr>
</table>


<table class="full-width mt-0 mb-0" >
    <thead >
    <tr class="">
        <th class="text-center py-1 desc text-white"  width="3%" style="background: <?php echo e($color1); ?>">ITEM</th>
        <th class="text-center py-1 desc text-white"  width="10%" style="background: <?php echo e($color1); ?>">CÓDIGO</th>
        <th class="text-center py-1 desc text-white"  width="8%" style="background: <?php echo e($color1); ?>">CANTIDAD</th>
        <th class="text-center py-1 desc text-white"  width="8%" style="background: <?php echo e($color2); ?>">U.M.</th>
        <th class="text-center py-1 desc text-white"  width="44%" style="background: <?php echo e($color2); ?>">DESCRIPCIÓN</th>
        <th class="text-center py-1 desc text-white"   width="8%" style="background: <?php echo e($color2); ?>">PESO</th>
    </tr>
    </thead>
    <tbody class="">
    <?php $__currentLoopData = $document->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
    $total_weight_line = 0;
    ?>
    <tr>
        <td class="p-1 text-center align-top desc cell-solid-rl"><?php echo e($loop->iteration); ?></td>
        <td class="p-1 text-center align-top desc cell-solid-rl"><?php echo e($row->item->internal_id); ?></td>
        <td class="p-1 text-center align-top desc cell-solid-rl">
            <?php if(((int)$row->quantity != $row->quantity)): ?>
                    <?php echo e($row->quantity); ?>

                <?php else: ?>
                    <?php echo e(number_format($row->quantity, 0)); ?>

                <?php endif; ?>

        </td>
        <td class="p-1 text-center align-top desc cell-solid-rl"><?php echo e($row->item->unit_type_id); ?></td>
        <td class="p-1 text-left align-top desc text-upp cell-solid-rl">
            <?php if($row->name_product_pdf): ?>
                    <?php echo $row->name_product_pdf; ?>

                <?php else: ?>
                    <?php echo $row->item->description; ?>

                <?php endif; ?>

            <?php if(!empty($row->item->presentation)): ?> <?php echo $row->item->presentation->description; ?> <?php endif; ?>

            <?php if($row->attributes): ?>
            <?php $__currentLoopData = $row->attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <br/><span style="font-size: 9px"><?php echo $attr->description; ?> : <?php echo e($attr->value); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <?php if($row->discounts): ?>
            <?php $__currentLoopData = $row->discounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dtos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <br/><span style="font-size: 9px"><?php echo e($dtos->factor * 100); ?>% <?php echo e($dtos->description); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <?php if($row->relation_item->is_set == 1): ?>
            <br>
            <?php $itemSet = app('App\Services\ItemSetService'); ?>
            <?php $__currentLoopData = $itemSet->getItemsSet($row->item_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e($item); ?><br>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <?php if($document->has_prepayment): ?>
            <br>
            *** Pago Anticipado ***
            <?php endif; ?>
        </td>
        <td class="p-1 text-center align-top desc cell-solid-rl">
            <?php echo e($total_weight_line); ?>

        </td>
    </tr>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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

        <td width="25%" class="pl-3">
            <table class="full-width">
                <tr>
                    <td ><strong>PESO TOTAL:</strong> <?php echo e($document->total_weight); ?>  <?php echo e($document->unit_type_id); ?></td>
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
            <table class="full-width">
                <tr>
                    <td rowspan="2"><strong>Representación impresa de la Guía de Remisión</strong></td>
                </tr>
            </table>
        </td>

    </tr>
</table>
<?php if($document->data_affected_document): ?>
<?php
$document_data_affected_document = $document->data_affected_document;

$number = (property_exists($document_data_affected_document,'number'))?$document_data_affected_document->number:null;
$series = (property_exists($document_data_affected_document,'series'))?$document_data_affected_document->series:null;
$document_type_id = (property_exists($document_data_affected_document,'document_type_id'))?$document_data_affected_document->document_type_id:null;

?>
<?php if($number !== null && $series !== null && $document_type_id !== null): ?>

<?php
$documentType  = App\Models\Tenant\Catalogs\DocumentType::find($document_type_id);
$textDocumentType = $documentType->getDescription();
?>
<table class="full-width border-box">
    <tr>
        <td class="text-bold border-bottom font-bold"><?php echo e($textDocumentType); ?></td>
    </tr>
    <tr>
        <td><?php echo e($series); ?>-<?php echo e($number); ?></td>
    </tr>
</table>
<?php endif; ?>
<?php endif; ?>
<?php if($document->reference_order_form_id): ?>
<table class="full-width border-box">
    <?php if($document->order_form): ?>
    <tr>
        <td class="text-bold border-bottom font-bold">ORDEN DE PEDIDO</td>
    </tr>
    <tr>
        <td><?php echo e(($document->order_form) ? $document->order_form->number_full : ""); ?></td>
    </tr>
    <?php endif; ?>
</table>

<?php elseif($document->order_form_external): ?>
<table class="full-width border-box">
    <tr>
        <td class="text-bold border-bottom font-bold">ORDEN DE PEDIDO</td>
    </tr>
    <tr>
        <td><?php echo e($document->order_form_external); ?></td>
    </tr>
</table>

<?php endif; ?>


<?php if($document->reference_sale_note_id): ?>
<table class="full-width border-box">
    <?php if($document->sale_note): ?>
    <tr>
        <td class="text-bold border-bottom font-bold">NOTA DE VENTA</td>
    </tr>
    <tr>
        <td><?php echo e(($document->sale_note) ? $document->sale_note->number_full : ""); ?></td>
    </tr>
    <?php endif; ?>
</table>
<?php endif; ?>
</body>
</html>
