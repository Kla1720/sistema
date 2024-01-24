@php
    $path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $configuracion = \App\Models\Tenant\Configuration::all();
     foreach($configuracion as $config){
        $legend_footer= $config['legend_footer'];
     }
@endphp
<head>
    <link href="{{ $path_style }}" rel="stylesheet" />
</head>
<body>
<table class="full-width">
    <tr>
        <td class="text-center desc font-bold">Para consultar el comprobante ingresar a {!! url('/buscar') !!}</td>
    </tr>
    @if ($legend_footer)
        <tr>
            <td class="text-center desc pt-2"><b>BIENES TRANSFERIDOS Y/O SERVICIOS PRESTADOS EN LA AMAZONIA PARA SER CONSUMIDOS EN LA MISMA</b></td>
        </tr>
    @endif
</table>
</body>
