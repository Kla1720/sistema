<?php 

namespace Modules\Transporte\Traits;

use Illuminate\Support\Collection;
use Modules\Transporte\Models\TransporteProgramacion;
use Modules\Transporte\Models\TransporteTerminales;

trait Rutas {

    private function getPositionInRoute(TransporteTerminales $terminal,Collection $rutas){
        $indexOrigen = $rutas->search(function($item) use ($terminal){
            return $item->id == $terminal->id;
        });

        return $indexOrigen;
    }
    private function getRutasMayores($position,Collection $rutas){
        $rutasSuperiores = $rutas->filter(function($item,$index) use ($position){
            return $index > $position;
        });
        return $rutasSuperiores;
    }

    private function getRutasMenores($position,Collection $rutas){
        $rutasMenores = $rutas->filter(function($item,$index) use ($position){
            return $index < $position;
        });
        return $rutasMenores;
    }

    private function getFilterExclude(TransporteTerminales $item, Collection $rutas){
        return $rutas->filter(function($row) use ($item){
            [$origen,$destino] = $row;
            return $origen != $item->id;
        });
    }

    private function combinaciones(Collection $collection1, Collection $collection2,Collection $result = null){

        $result = is_null($result) ?  new Collection() : $result;

        foreach($collection1 as $item1){
            foreach($collection2 as $item2){
                if($item1 != $item2){
                    $result->push([
                        $item1->id,
                        $item2->id,
                    ]);    
                }
                
            }
        }

        return $result;
    }

    private function getRoutesBeetween($origin,$destiny, Collection $rutas){
        return $rutas->filter(function($_,$index) use ($origin, $destiny){
            return $index > $origin && $index < $destiny;
        });
    }

    private function getProgramacionesMatch(TransporteProgramacion $programacion){
       
        $listProgramaciones = new Collection();

        $parent = $programacion->programacion;

        $origen = $programacion->origen;
        $destino = $programacion->destino;

        $listaRutas = $parent->rutas()->get();


        $listaRutas->prepend($parent->origen);
        $listaRutas->push($parent->destino);
        
        $positionOrigen = $this->getPositionInRoute($origen,$listaRutas);
        $positionDestino = $this->getPositionInRoute($destino,$listaRutas);

        $terminalesPosteriores = $this->getRutasMayores($positionDestino,$listaRutas);
        $terminalesAnteriores = $this->getRutasMenores($positionOrigen,$listaRutas);

        $terminalesAnterioresDestino = $this->getRutasMenores($positionDestino,$listaRutas);

        $rutasPosiblesAnteriores = $this->combinaciones($terminalesAnteriores,$terminalesPosteriores);
        $rutasPosiblesPosteriores = $this->combinaciones($terminalesAnterioresDestino, $terminalesPosteriores);
        

        $rutaEntre = $this->getRoutesBeetween($positionOrigen,$positionDestino, $listaRutas);

        $routes = $this->combinaciones($terminalesAnteriores,$rutaEntre);


        $rutasPosibles = $rutasPosiblesAnteriores
        ->merge($rutasPosiblesPosteriores)
        ->merge($routes)
        ->unique();

        $rutasPosiblesResultantes = $this->getFilterExclude($destino, $rutasPosibles);

        foreach($rutasPosiblesResultantes as $item){
            [$origen,$destino] = $item;

            $program = TransporteProgramacion::where('terminal_origen_id',$origen)
            ->where('terminal_destino_id',$destino)
            ->where('programacion_id',$parent->id)
            ->where('active',true)
            ->first();

            if($program) $listProgramaciones->push($program);

        }

        return $listProgramaciones;
    }


}