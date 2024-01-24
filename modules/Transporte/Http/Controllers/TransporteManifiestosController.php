<?php

namespace Modules\Transporte\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Series;
use Carbon\Carbon;
use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use DateTime;
use Exception;
use Illuminate\Mail\Transport\Transport;
use Modules\Transporte\Http\Requests\ManifiestoFormRequest;
use Modules\Transporte\Http\Requests\ProgramacionesDisponiblesRequest;
use Modules\Transporte\Models\TransporteChofer;
use Modules\Transporte\Models\TransporteEncomienda;
use Modules\Transporte\Models\TransporteManifiesto;
use Modules\Transporte\Models\TransportePasaje;
use Modules\Transporte\Models\TransporteProgramacion;
use Modules\Transporte\Traits\PasajerosRuta;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

class TransporteManifiestosController extends Controller
{
    //
    use PasajerosRuta;

    protected $company;
    protected $configuration;

    public function __construct()
    {
        $this->configuration = Configuration::first();
        $this->company = Company::active();
    }

    public function index(){
        $establishment =  Establishment::where('id', auth()->user()->establishment_id)->first();
        $series = Series::where('establishment_id', $establishment->id)
                  ->whereIn('document_type_id', ['33', '100'])->get();
        $choferes = TransporteChofer::all();

        return view('transporte::manifiestos.index',compact('series','choferes'));
    }


    public function store(ManifiestoFormRequest $request){
        $company = Company::active();
        $soap_type_id = $company->soap_type_id;

        try {

            $programacion = TransporteProgramacion::findOrFail($request->programacion_id);

            $numero = TransporteManifiesto::where('tipo', $request->tipo)
                ->where('serie', $request->serie)
                ->max('numero')+1;

            $manifiesto = TransporteManifiesto::create([
                'soap_type_id'=>$soap_type_id,
                'serie' => $request->serie,
                'tipo' => $request->tipo,
                'numero' => $numero,
                'chofer_id' => $request->chofer_id,
                'copiloto_id' => $request->copiloto_id,
                'observaciones' => $request->observaciones,
                'programacion_id' => $programacion->id,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'user_id'=> auth()->user()->id
            ]);

            $manifiesto->load([
                'chofer',
                'copiloto',
                'serie'
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Se ha generado el manifiesto correctamente',
                'manifiesto' => $manifiesto,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Lo sentimos, ocurrio un error al generar el manifiesto',
                'error' => $th->getMessage()
            ],500);
        }
    }

    public function imprimirManifiesto(Request $request,TransporteManifiesto $manifiesto){


        if($manifiesto->tipo == 1){ //encomiendas
            $this->documentEncomiendas($request,$manifiesto);

        }else if($manifiesto->tipo == 2){ //pasajes
           $this->documentPasajeros($request,$manifiesto);
        }



    }

    public function listaPasajeros(Request $request,TransporteManifiesto $manifiesto){
        try{
            $programacion = $manifiesto->programacion;

            [$pasajes, $pasajesEnTerminal, $pasajesRecogidosRuta] = $this->getPasajeros($programacion, $manifiesto->fecha);

            return response()->json([
                'pasajes' => $pasajes

            ],200);
        }catch(Exception $e){

            return response()->json([
                'message' => 'Lo sentimos ocurrio un error'
            ],500);

        }
    }

    private function documentPasajeros(Request $request,TransporteManifiesto $manifiesto){

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'margin_top' => 0,
            'margin_right' => 2,
            'margin_bottom' => 0,
            'margin_left' => 12
        ]);

        $company = $this->company;
        $establishment = $manifiesto->user->establishment;
        $programacion = $manifiesto->programacion;
        $vehiculo = $programacion->vehiculo;

        [$pasajes, $pasajesEnTerminal, $pasajesRecogidosRuta] = $this->getPasajeros($programacion, $manifiesto->fecha);
        $leftText = '<div style="position: absolute;rotate: 90; text-align: center;font-size: 1.1em;color:#5d5a5a"><b>NÚMERO DE AUTORIZACIÓN DE IMPRESIÓN: ' .$company->num_aut_manifiesto_pasajero.'</b></div>';


        $content = view('transporte::manifiestos.manifiesto_pasajes.body',compact(
            'programacion',
            'manifiesto',
            'pasajes',
            'vehiculo',
            'pasajesEnTerminal',
            'pasajesRecogidosRuta',
            'company',
            'establishment'
        ));
        $pdf->WriteFixedPosHTML($leftText, 5, 100, 200, 90, 'auto');
        $pdf->WriteHTML($content);

        $name = 'manifiesto_pasajeros_'.(new DateTime())->getTimestamp().'.pdf';

        $pdf->Output($name,'I');

    }

    private function documentEncomiendas(Request $request,TransporteManifiesto $manifiesto){
        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 1,
            'margin_right' => 2,
            'margin_bottom' => 0,
            'margin_left' => 2
        ]);
        $company = $this->company;
        $establishment_id = auth()->user()->establishment_id;
        $establishment = Establishment::where('id',$establishment_id)->first();

        $programacion = $manifiesto->programacion;
        $vehiculo = $programacion->vehiculo;

        $encomiendas = TransporteEncomienda::with([
            'destinatario',
            'document'
        ])
        ->where('programacion_id',$programacion->id)
        ->whereDate('fecha_salida',$manifiesto->fecha)
        ->get();

        $user = $request->user();

        $content = view('transporte::manifiestos.manifiesto_encomiendas.body',compact(
            'programacion',
            'manifiesto',
            'encomiendas',
            'vehiculo',
            'company',
            'establishment'
        ));

        $pdf->WriteHTML($content);

        $name = 'manifiesto_encomiendas_'.(new DateTime())->getTimestamp().'.pdf';

        $pdf->Output($name,'I');

    }


    public function indexManifiestoEncomiendas(Request $request,$manifiesto){

        $manifiesto = TransporteManifiesto::with([
            'programacion.destino',
            'programacion.origen',
            'chofer',
            'copiloto'
        ])->find($manifiesto);

        if( is_null($manifiesto)) abort(404);

        return view('transporte::manifiestos.asignacion',compact('manifiesto'));

    }


    public function getEncomiendas(Request $request){
        try{

            $programacion = $request->input('programacion');
            $fecha = $request->input('fecha');
            $cliente = $request->input('cliente');

            $encomiendas_documents = TransporteEncomienda::with('document:id,total,series,number','document.items','remitente:id,name','programacion')
            ->where('programacion_id',$programacion)
            ->where('fecha_salida',$fecha)
            ->get();

            $encomiendas_notes = TransporteEncomienda::with('saleNote:id,total,series,number','saleNote.items','remitente:id,name','programacion')
                ->where('programacion_id',$programacion)
                ->where('fecha_salida',$fecha)
                ->get();

            if(!empty($cliente)){
                $encomiendas_documents->whereHas('remitente',function($remitente) use ($cliente){
                    $remitente->where('nombre','like',"%{$cliente}%");
                });
                $encomiendas_notes->whereHas('remitente',function($remitente) use ($cliente){
                    $remitente->where('nombre','like',"%{$cliente}%");
                });

            }

            return response()->json([
                'documents' => $encomiendas_documents,
                'sale_notes' => $encomiendas_notes,

            ],200);

        }catch(Exception $e){

            return response()->json([
                'message' => 'Lo sentimos ocurrio un error'
            ],500);

        }
    }

    public function getEncomiendasSinAsignar(Request $request){
        try{

            $fecha_inicio = $request->input('fecha_inicio');
            $fecha_final = $request->input('fecha_final');

            $cliente = $request->input('cliente');

            $encomiendas_document = TransporteEncomienda::with('document:id,total,series,number','document.items','remitente:id,name')
            ->whereNull('programacion_id')
            ->whereNotNull('document_id')
            ->whereBetween('fecha_salida',[$fecha_inicio,$fecha_final])->get();

            $encomiendas_notes = TransporteEncomienda::with('saleNote:id,total,series,number','saleNote.items','remitente:id,name')
                ->whereNull('programacion_id')
                ->whereNotNull('note_id')
                ->whereBetween('fecha_salida',[$fecha_inicio,$fecha_final])->get();

            return response()->json([
                    'documents' => $encomiendas_document,
                    'sale_notes' => $encomiendas_notes,

            ],200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Lo sentimos ocurrio un error'. $e->getMessage()
            ],500);

        }
    }

    public function asignarEncomienda(Request $request){
        try{

            $programacion =  $request->input('programacion');
            $manifiesto = TransporteManifiesto::find($request->input('manifiesto'));

            TransporteEncomienda::where('id',$request->input('encomienda'))->update([
                'programacion_id' => $programacion,
                'fecha_salida' => $manifiesto->fecha
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Asignado'
            ],200);

        }catch(Exception $e){

            return response()->json([
                'success' => false,
                'message' => 'Lo sentimos ocurrio un error'
            ],500);

        }
    }

    public function desasignarEncomienda(Request $request){
        try{

            TransporteEncomienda::where('id',$request->input('encomienda'))->update([
                'programacion_id' => null,
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Desasignado'
            ],200);

        }catch(Exception $e){

            return response()->json([
                'success' => false,
                'message' => 'Lo sentimos ocurrio un error'
            ],500);

        }
    }

    public function getManifiestos(Request $request){
        try{

            extract($request->only(['tipo']));

            $manifiestos = TransporteManifiesto::with([
                'chofer',
                'copiloto',
                'programacion' => function($programacion){
                    $programacion->with('destino','origen','vehiculo');
                }
            ])
            ->where('tipo',$tipo)
            ->orderBy('id','DESC')->get();

            return response()->json($manifiestos,200);

        }catch(Exception $e){

            return response()->json([
                'message' => 'Lo sentimos ha ocurrido un error'
            ],500);

        }

    }


    public function update(ManifiestoFormRequest $request,TransporteManifiesto $manifiesto){
        try {

            $programacion = TransporteProgramacion::findOrFail($request->programacion_id);

            $manifiesto->update([
                'serie' => $request->serie,
                'tipo' => $request->tipo,
                'chofer_id' => $request->chofer_id,
                'copiloto_id' => $request->copiloto_id,
                'observaciones' => $request->observaciones,
                'programacion_id' => $programacion->id,
                'fecha' => $request->fecha,
                'hora' => $request->hora
            ]);

            $manifiesto->chofer;
            $manifiesto->copiloto;
            $manifiesto->serie;


            return response()->json([
                'success' => true,
                'message' => 'Se ha actualizado la información correctamente',
                'manifiesto' => $manifiesto,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Lo sentimos, ocurrio un error al actualizar la información',
                'error' => $th->getMessage()
            ],500);
        }
    }

    public function getProgramaciones(ProgramacionesDisponiblesRequest $request){

        $programaciones = TransporteProgramacion::with('vehiculo','origen','destino')
            ->where('terminal_origen_id',$request->origen_id)
            ->where('terminal_destino_id',$request->destino_id)
            ->WhereEqualsOrBiggerDate($request->fecha_salida)
            ->where('active',1);

        return response()->json([
            'programaciones' => $programaciones->get()
        ]);
    }

    public  function verificarManifiestos(Request  $request){

        if(isset($request->programacion['id'])){

            $manifiesto= TransporteManifiesto::where('programacion_id',$request->programacion['id'])
                ->where('fecha',$request->fecha_salida)
                ->where('hora',$request->programacion['hora_salida'])
                ->first();

            if($manifiesto){
                return response()->json([
                    'success' => true,
                    'message' => 'Existe',
                    'manifiesto' => $manifiesto->id,
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'No existe',
                    'manifiesto' => null,
                ]);
            }
        } else{
            return response()->json([
                'success' => false,
                'message' => 'No existe',
                'manifiesto' => null,
            ]);
        }
    }

    public function confirmarAsistencia(Request $request){
        try{


            $transporte_pasaje = TransportePasaje::findOrFail($request->id);
            $transporte_pasaje->asistencia = $request->estado;
            $transporte_pasaje->update();


            return response()->json([
                'success' => true,
                'message' => 'Se actualizó el estado de asistencia',
            ],200);

        }catch(Exception $e){

            return response()->json([
                'message' => 'Lo sentimos ha ocurrido un error'
            ],500);

        }
    }



}
