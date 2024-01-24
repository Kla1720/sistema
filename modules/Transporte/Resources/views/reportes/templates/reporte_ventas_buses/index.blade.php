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
                <tr style="background-color: #0088cc;">
                    <td style="width: 100%;text-align:center;color:white">
                        <h2>REPORTE DE VENTAS POR BUSES</h2><br>
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
        <td colspan="2" style="width: 33%;text-align: right;font-size: 20px; ">
            Ingresos totales: <strong style="background-color: yellow" >${{ number_format($total,2,'.','') }} </strong> <br>
        </td>
    </tr>
</table>


<table class="bordered" style="width: 100%;font-size:9px;margin-top:20px;text-align:center" >

    <thead>
        <tr>
            <td>Placa</td>
            <td>Nombre</td>
            <td>Efectivo</td>
        </tr>
    </thead>
    <tbody>

        @foreach ($transportes as $transporte)
            <tr >
                <td>{{ $transporte->placa }}</td>
                <td>{{ $transporte->nombre }}</td>
                <td style="background-color: yellow"><strong>${{ $transporte->total_vendido }}</strong></td>
            </tr>
        @endforeach

        

    </tbody>


</table>












