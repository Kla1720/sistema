<?php

namespace Modules\Transporte\Http\Controllers;

use App\Models\Tenant\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Transporte\Models\TransportePasaje;
use Modules\Transporte\Models\TransporteProgramacion;
use Modules\Transporte\Models\TransporteTerminales;
use Mpdf\Mpdf;
use App\Models\Tenant\Company;
use DateTime;
use Modules\Transporte\Models\TransporteVehiculo;
use Modules\Transporte\Models\TransporteViajes;

class TransporteReportes extends Controller
{
    //
    protected $company;

    public function __construct()
    {
        $this->company = Company::active();
    }

    public function index(){
        $oficinas = TransporteTerminales::all();

        return view('transporte::reportes.index',compact('oficinas'));
    }

    public function getPreviewReporteVentarPorDia(Request $request){

        extract($request->only(['oficina','fecha','page','limit']));

        $vendedores = User::select('id','name')
        ->whereHas('transporte_user_terminal',function($query) use ($oficina){
            $query->where('terminal_id',$oficina);
        })
        ->take($limit)->skip($limit * ($page - 1) );

        $total = $vendedores->count();
        $vendedores = $vendedores->get();

        foreach($vendedores as $vendedor){

            $totalVendido = TransportePasaje::where('user_id',$vendedor->id)
            ->where('estado_asiento_id',2) //solo asientos vendidos
            ->whereDate('created_at',$fecha)
            ->sum('precio');

            $pasajes = TransportePasaje::with('origen','destino')
            ->where('user_id',$vendedor->id)
            ->where('estado_asiento_id',2) //solo asientos vendidos
            ->whereDate('created_at',$fecha)
            ->get();

            $vendedor->setAttribute('pasajes_vendidos',$pasajes);
            $vendedor->setAttribute('total_vendido',$totalVendido);

        }


        return response()->json([
            'total' => $total,
            'data' => $vendedores
        ]);
    }


    private function generarReportePorVentas(array $data){

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'margin_top' => 2,
            'margin_right' => 2,
            'margin_bottom' => 0,
            'margin_left' => 2
        ]);


        $content = view('transporte::reportes.templates.reporte_diario_venta.index',$data);
        $pdf->SetHTMLFooter('<div style="text-align: center; font-size: 7pt">Numéro de autorización SUNAT: '.$this->company->num_aut_manifiesto_pasajero.'</div>'
            ,0);
        $pdf->WriteHTML($content);

        $name = 'reporte_por_dia_ventas'.(new DateTime())->getTimestamp().'.pdf';

        $pdf->Output($name,'I');



    }

    public function reporteDiarioPorVentas(Request $request){

        extract($request->only(['oficina','fecha','page','limit']));

        $sucursal = TransporteTerminales::find($oficina);

        $vendedores = User::whereHas('transporte_user_terminal',function($query) use ($oficina){
            $query->where('terminal_id',$oficina);
        })

        ->get();

        $total = 0;

        foreach($vendedores as $vendedor){

            $totalVendido = TransportePasaje::where('user_id',$vendedor->id)
            ->where('estado_asiento_id',2) //solo asientos vendidos
            ->whereDate('created_at',$fecha)
            ->sum('precio');

            $pasajes = TransportePasaje::with('origen','destino')
            ->where('user_id',$vendedor->id)
            ->where('estado_asiento_id',2) //solo asientos vendidos
            ->whereDate('created_at',$fecha)
            ->get();

            $vendedor->setAttribute('pasajes_vendidos',$pasajes);
            $vendedor->setAttribute('total_vendido',$totalVendido);

            $total += $totalVendido;
        }

        $this->generarReportePorVentas([
            'sucursal' => $sucursal,
            'fecha' => $fecha,
            'vendedores' => $vendedores,
            'total' => $total
        ]);

    }


    //REPORTE DE AVANCE DE VENTAS

    private function getReporteAvanceVentas(array $data){

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'margin_top' => 2,
            'margin_right' => 2,
            'margin_bottom' => 0,
            'margin_left' => 2
        ]);


        $content = view('transporte::reportes.templates.reporte_avance_porcentaje.index',$data);
        $pdf->SetHTMLFooter('<div style="text-align: center; font-size: 7pt">Numéro de autorización SUNAT: '.$this->company->num_aut_manifiesto_pasajero.'</div>'
            ,0);
        $pdf->WriteHTML($content);

        $name = 'reporte_por_dia_ventas'.(new DateTime())->getTimestamp().'.pdf';

        $pdf->Output($name,'I');

    }


    public function getPreviewReportePorcentajeProgramaciones(Request $request){

        extract($request->only(['fecha','page','limit']));

//        $transportes = TransporteVehiculo::with('programaciones')
//        ->whereHas('programaciones',function($builder){
//            $builder->where('active',true);
//        });

        $transportes = TransporteProgramacion::with('vehiculo.seats','origen','destino')
            ->where('active',true)
            ->where('hidden',0)
            ->take($limit)->skip($limit * ($page - 1) );


        $total = $transportes->count();
        $transportes = $transportes->get();

        foreach($transportes as $transporte){

            $totalAsientos = $transporte->vehiculo->asientos;

            $viajes = TransporteViajes::where('programacion_id',$transporte->programacion_id)
            ->get()
            ->pluck('id');
            
            // $idsProgramaciones = $transporte->programaciones->map(function($item){
            //     return $item->id;
            // });

            $query = TransportePasaje::with('origen','destino')
            ->whereIn('viaje_id',$viajes)
            ->where('estado_asiento_id',2) //solo asientos vendidos
            ->whereDate('fecha_salida',$fecha);

            $copyQuery = $query;

            $efectivo = $copyQuery->sum('precio');

            $asientosOcupados = $copyQuery->count();

            $porcentaje = ((int)($asientosOcupados * 100 / ($totalAsientos >0 ? $totalAsientos :1 )));

            $transporte->setAttribute('asientos_ocupados',$asientosOcupados);
            $transporte->setAttribute('asientos_disponibles', $totalAsientos - $asientosOcupados);
            $transporte->setAttribute('total_vendido',number_format($efectivo,2,'.',''));
            $transporte->setAttribute('porcentaje',$porcentaje);
            $transporte->setAttribute('ocupados',$copyQuery->get());

        }

        return response()->json([
            'total' => $total,
            'data' => $transportes
        ]);
    }

    public function getReportePorcentajeProgramaciones(Request $request){

        extract($request->only(['fecha','page','limit']));

        $transportes = TransporteProgramacion::with('vehiculo.seats','origen','destino')
        ->where('active',true)
        ->where('hidden',0);


        $transportes = $transportes->get();

        $total = 0;

        foreach($transportes as $transporte){

            $totalAsientos = $transporte->vehiculo->asientos;
            $viajes = TransporteViajes::where('programacion_id',$transporte->programacion_id)
            ->get()
            ->pluck('id');

            $query = TransportePasaje::whereIn('viaje_id',$viajes)
            ->where('estado_asiento_id',2) //solo asientos vendidos
            ->whereDate('fecha_salida',$fecha);

            $copyQuery = $query;

            $efectivo = $copyQuery->sum('precio');

            $asientosOcupados = $copyQuery->count();

            $porcentaje = ((int)($asientosOcupados * 100 / ($totalAsientos >0 ? $totalAsientos :1 )));

            $transporte->setAttribute('asientos_ocupados',$asientosOcupados);
            $transporte->setAttribute('asientos_disponibles', $totalAsientos - $asientosOcupados);
            $transporte->setAttribute('total_vendido',number_format($efectivo,2,'.',''));
            $transporte->setAttribute('porcentaje',$porcentaje);

            $total += $efectivo;
        }

        $this->getReporteAvanceVentas([
            'transportes' => $transportes,
            'fecha' => $fecha,
            'total' => $total
        ]);
    }

     //REPORTE DE VENTAS POR BUSES

     private function getReporteVentasBuses(array $data){

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'margin_top' => 2,
            'margin_right' => 2,
            'margin_bottom' => 0,
            'margin_left' => 2
        ]);


        $content = view('transporte::reportes.templates.reporte_ventas_buses.index',$data);
        $pdf->SetHTMLFooter('<div style="text-align: center; font-size: 7pt">Numéro de autorización SUNAT: '.$this->company->num_aut_manifiesto_pasajero.'</div>'
            ,0);
        $pdf->WriteHTML($content);

        $name = 'reporte_por_dia_ventas'.(new DateTime())->getTimestamp().'.pdf';

        $pdf->Output($name,'I');

    }


    public function getPreviewReporteVentaBuses(Request $request){

        extract($request->only(['fecha','page','limit']));

        abort(404);

        $transportes = TransporteVehiculo::with('programaciones')
        ->whereHas('programaciones',function($builder){
            $builder->where('active',true);
        })
        ->take($limit)->skip($limit * ($page - 1) );


        $total = $transportes->count();
        $transportes = $transportes->get();

        foreach($transportes as $transporte){

            $idsProgramaciones = $transporte->programaciones->map(function($item){
                return $item->id;
            });

            $efectivo = TransportePasaje::whereIn('programacion_id',$idsProgramaciones)
            ->where('estado_asiento_id',2) //solo asientos vendidos
            ->whereDate('created_at',$fecha)
            ->sum('precio');

            $transporte->setAttribute('total_vendido',number_format($efectivo,2,'.',''));

        }

        return response()->json([
            'total' => $total,
            'data' => $transportes
        ]);
    }

    public function getReporteVentaBuses(Request $request){

        extract($request->only(['fecha','page','limit']));

        $transportes = TransporteVehiculo::with('programaciones')
        ->whereHas('programaciones',function($builder){
            $builder->where('active',true);
        });


        $transportes = $transportes->get();

        $total = 0;

        foreach($transportes as $transporte){

            $idsProgramaciones = $transporte->programaciones->map(function($item){
                return $item->id;
            });

            $efectivo = TransportePasaje::whereIn('programacion_id',$idsProgramaciones)
            ->where('estado_asiento_id',2) //solo asientos vendidos
            ->whereDate('created_at',$fecha)
            ->sum('precio');

            $transporte->setAttribute('total_vendido',number_format($efectivo,2,'.',''));

            $total += $efectivo;
        }

        $this->getReporteVentasBuses([
            'transportes' => $transportes,
            'fecha' => $fecha,
            'total' => $total
        ]);
    }

}
