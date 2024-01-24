<?php

namespace Modules\Transporte\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Restaurant\Models\RestaurantConfiguration;
use App\Models\Tenant\User;
use App\Models\Tenant\Company;
use Modules\Transporte\Models\TransporteConfiguration;


class TransporteConfigurationController extends Controller
{
    /**
     * muestra vista para utilizar en mozo
     */
    public function configuration()
    {
        return view('transporte::configuration.index');
    }

    /**
     * obtiene configuración para utilizar en mozo
     */
    public function record()
    {
        $configurations = TransporteConfiguration::first();
        return [
            'success' => true,
            'data' => $configurations->getCollectionData(),
        ];
    }

    /**
     * guarda cada nueva configuración
     */
    public function setConfiguration(Request $request)
    {
        $configuration = TransporteConfiguration::first();
        $configuration->fill($request->all());
        $configuration->save();

        return [
            'success' => true,
            'configuration' => $configuration->getCollectionData(),
            'message' => 'Configuración actualizada',
        ];
    }
}
