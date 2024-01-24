<!DOCTYPE html>
<html lang="es">
<head>
    <style>

    </style>
</head>
<body>
    <div>
        <table style="width: 100%; border-collapse: collapse">
            <tr>
                <td align="center" colspan="2"><span style="font-size: 1.5em">COMANDA #</span> <b> <span style="font-size: 2em">{{$pedido->id}}</span></b></td>
            </tr>
            <tr>
                <td colspan="2"><b>MOZO:</b> {{$pedido->mozo->name}}</td>
            </tr>
            <tr>
                <td style="width:90px"><b>MESA:</b> <span style="font-size: 1.5em;font-weight: bold">{{$pedido->mesa->numero}}</span></td>
                <td>HORA: <b>{{$fecha_hora}}</b></td>
            </tr>
            <tr>
                <td style="border:thin solid grey;text-align: center"> CANT </td>
                <td style="border:thin solid grey;text-align: center">DESCRIPCIÓN</td>
            </tr>
            @foreach($detalles as $detalle)
                <tr>
                    <td style="border-bottom: 1px solid grey; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;text-align: center">{{$detalle->cantidad}}</td>
                    <td style="border-bottom: 1px solid grey;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-size: 0.9em">{{$detalle->descripcion}}</td>
                </tr>
            @endforeach

        </table>
    </div>
</body>
</html>
