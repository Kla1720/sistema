<style>

    .bordered {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .bordered td{
        border: 1px solid black;
    }

    .table-border{
        border: 1px solid black;
        border-collapse: collapse;
    }

    .aqpfact-color:{
        background-color: #003c71 !important;
    }

</style>

<table style="width: 100%;font-size:11px">
    <tr>
      
        <td style="width: 100%">
            <table class="bordered" style="width: 100%">
                <tr>
                    <td style="width: 100%;text-align:center">
                        {{-- <h1>RUC: {{ $company->number }}</h1> --}}
                    </td>
                </tr>
                <tr style="background-color: {{ $sucursal->color }};">
                    <td style="width: 100%;text-align:center;color:white">
                        <h2>REPORTE DE VENTAS POR OFICINA</h2><br>
                        Sucursal: <strong>{{ $sucursal->nombre }} </strong> <br>
                        Fecha: <strong>{{ $fecha }}</strong>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;text-align:center">
                        {{-- <h4>{{ $manifiesto->serie }} - {{ $manifiesto->numero }} </h4> --}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="width: 33%;text-align: right;font-size: 20px;">
            Ingresos totales: <strong style="background-color: yellow">${{ number_format($total,2,'.','') }} </strong> <br>
        </td>
    </tr>
</table>

@foreach ($vendedores as $vendedor)

    @if ($vendedor->total_vendido > 0)
        <table style="width: 100%">

            <tbody>
                <tr>
                    <td>Nombre del vendedor: <strong> {{ $vendedor->name }} </strong> </td>
                    <td>Total vendido: <strong>{{ number_format($vendedor->total_vendido,2,'.','') }}</strong> </td>
                </tr>
                <tr >
                    <td colspan="2">
                        <table class="bordered" style="width: 100%;font-size:9px;margin-top:20px;text-align:center">
                            <thead>
                                <tr>
                                    <td>Nombre</td>
                                    <td>Origen</td>
                                    <td>Destino</td>
                                    <td>Precio</td>        
                                </tr>
                                            
                            </thead>
                            <tbody>
                                @foreach ($vendedor->pasajes_vendidos as $pasaje)
                                    <tr>
                                        <td>{{ $pasaje->pasajero->name }}</td>
                                        <td>{{ $pasaje->origen->nombre }}</td>
                                        <td>{{ $pasaje->destino->nombre }}</td>
                                        <td style="background-color: yellow"> <strong>{{ number_format($pasaje->precio,2,'.','') }}</strong></td> 
                                    </tr>
                                @endforeach
            
                            </tbody>
                            
                        
            
                        </table>
                    </td>
                    
                </tr>

            </tbody>
        

        </table>
    @endif
    
@endforeach












