<?php
namespace Modules\Transporte\Traits;
use Carbon\Carbon;
use Modules\Transporte\Models\TransportePasaje;
use Modules\Transporte\Models\TransporteProgramacion;
use Modules\Transporte\Models\TransporteViajes;
use Modules\Transporte\Traits\Rutas;


trait PasajerosRuta{

    use Rutas;


    public function getPasajeros(TransporteProgramacion $programacion, $fechaSalida, $onlyPassage = false){
        $pasajes = collect([]);
        $programacion->load(['programacion','origen','destino']); //terminal
        $programacionPadre = $programacion->programacion;

        $rutas = $programacionPadre->rutas()->get();

        $recogidosEnRuta = 0;
        $pasajesEnTerminal = 0;

        $date = new Carbon(sprintf('%s %s', $fechaSalida,$programacion->hora_salida));

        $rutas->prepend($programacionPadre->origen);
        $rutas->push($programacionPadre->destino);

        $indexOrigen = $this->getPositionInRoute($programacion->origen,$rutas);
        $indexDestino = $this->getPositionInRoute($programacion->destino, $rutas);

        $mayores = $this->getRutasMayores($indexOrigen,$rutas)->pluck('id');
        $menores = $this->getRutasMenores($indexOrigen,$rutas)->pluck('id');

        $intermedios = $this->getRoutesBeetween($indexOrigen,$indexDestino,$rutas)->pluck('id');

        $listMenores = TransporteProgramacion::with('origen','destino')
        ->whereIn('terminal_origen_id',$menores)
        ->whereIn('terminal_destino_id',$mayores)
        ->where('programacion_id',$programacionPadre->id)
        ->get(); // obtengo todas las programaciones que vienen hacia mi


        $list = TransporteProgramacion::with('origen','destino')
        ->where('terminal_origen_id',$programacion->terminal_origen_id)
        ->where('programacion_id',$programacionPadre->id)
        ->get(); // obtengo todas las programaciones que vienen hacia mi


        $listIntermedios = TransporteProgramacion::with('origen','destino')
        ->whereIn('terminal_origen_id',$intermedios)
        ->where('programacion_id',$programacionPadre->id)
        ->get(); // obtengo los intemedios entre el punto de origen y destino si es que existen



        foreach($listMenores as $menor){
            $timeClone = clone $date;

            $tiempoEstimado = TransporteProgramacion::where('terminal_origen_id',$menor->terminal_origen_id)
            ->where('terminal_destino_id',$programacion->terminal_origen_id)
            ->where('programacion_id',$programacionPadre->id)
            ->first();


            $timeClone = $timeClone->subMinutes($tiempoEstimado->tiempo_estimado);

            $travels = TransporteViajes::where('terminal_origen_id',$menor->terminal_origen_id)
            ->where('terminal_destino_id',$menor->terminal_destino_id)
            ->whereDate('fecha_salida',$timeClone->format('Y-m-d'))
            ->whereTime('hora_salida' , $timeClone->format('H:i:s'))
            ->where('programacion_id',$programacionPadre->id)
            ->get();


            $searchPasajes = TransportePasaje::with( 'origen', 'destino', 'pasajero','document:id,document_type_id,series,number')
            ->whereIn('viaje_id',$travels->pluck('id'))
            ->where('estado_asiento_id','!=',4) //diferente de cancelado
            ->get();

            // $recogidosEnRuta += count($searchPasajes);

            $pasajes = [...$pasajes, ...$searchPasajes];

        }

        foreach($listIntermedios as $intermedio){
            $timeClone = clone $date;

            $tiempoEstimado = TransporteProgramacion::where('terminal_origen_id',$programacion->terminal_origen_id)
            ->where('terminal_destino_id',$intermedio->terminal_origen_id)
            ->where('programacion_id',$programacionPadre->id)
            ->first();

            if(is_null($tiempoEstimado)) continue;

            // $minutos = $this->hoursToMinutes();

            $timeClone = $timeClone->addMinutes($tiempoEstimado->tiempo_estimado);

            $travels = TransporteViajes::where('terminal_origen_id',$intermedio->terminal_origen_id)
            ->where('terminal_destino_id',$intermedio->terminal_destino_id)
            ->whereDate('fecha_salida',$timeClone->format('Y-m-d'))
            ->whereTime('hora_salida', $timeClone->format('H:i:s'))
            ->where('programacion_id',$programacionPadre->id)
            ->get();


            $searchPasajes = TransportePasaje::with('origen', 'destino', 'pasajero','document:id,document_type_id,series,number')
            ->whereIn('viaje_id',$travels->pluck('id'))
            ->where('estado_asiento_id','!=',4) //diferente de cancelado
            ->get();

            $recogidosEnRuta += count($searchPasajes);

            $pasajes = [...$pasajes, ...$searchPasajes];



        }

        foreach($list as $item){
            $travels = TransporteViajes::where('terminal_origen_id',$item->terminal_origen_id)
            ->where('terminal_destino_id',$item->terminal_destino_id)
            ->whereDate('fecha_salida', $fechaSalida)
            ->whereTime('hora_salida', $programacion->hora_salida)
            ->where('programacion_id',$programacionPadre->id)
            ->get();


            $searchPasajes = TransportePasaje::with('origen', 'destino', 'pasajero','document:id,document_type_id,series,number')
            ->whereIn('viaje_id',$travels->pluck('id'))
            ->where('estado_asiento_id','!=',4) //diferente de cancelado
            ->get();

            $pasajesEnTerminal += count($searchPasajes);

            $pasajes = [...$pasajes, ...$searchPasajes];



        }

        if($onlyPassage) return $pasajes;


        return [
            $pasajes,
            $pasajesEnTerminal,
            $recogidosEnRuta
        ];
    }



}
