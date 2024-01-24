<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenant\CardBrandRequest;
use Exception;
use Modules\Restaurante\Models\Pedido;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

class SalesController extends Controller
{

    public function comandaPdfPrint(Request $request)
    {
        $fecha_hora = date("H:i");
        $pedido_id= $request->id;
        $pedido = Pedido::with('pedido_detalle','mozo','mesa')
        ->where("id",$pedido_id)
            ->first();

        $detalles = $pedido->pedido_detalle;

        $ancho = 58;
        $alto = count($detalles) * 5 + 45;

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [
                $ancho,
                $alto
            ],
            'margin_top' => 2,
            'margin_right' => 2,
            'margin_bottom' => 2,
            'margin_left' => 2
        ]);
        $html = view('restaurante::comanda_pdf', compact('fecha_hora','pedido','detalles'))->render();


        $pdf->WriteHTML($html, HTMLParserMode::HTML_BODY);

        $pdf->output('pedido-'."$pedido_id".'.pdf', 'I');

    }
}
