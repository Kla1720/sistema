<?php
namespace Modules\Account\Http\Controllers;

use Modules\Account\Exports\ReportAccountingConcarExport;
use Modules\Account\Exports\ReportAccountingFoxcontExport;
use Modules\Account\Exports\ReportAccountingContasisExport;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use App\Models\Tenant\Item;
use App\Models\Tenant\Note;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Configuration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Account\Models\CompanyAccount;
use DateTime;
use Illuminate\Support\Facades\DB;

class TributoController extends Controller
{
    const IGV = 0.18;


    public function index()
    {
        return view('account::account.tributo');
    }

    public function table(Request $request)
    {
        $year   = $request->input('year');

        //obtengo todos los meses de ese año
        //$meses = $this->getMonths($year);
        $meses= array('01','02','03','04','05','06','07','08','09','10','11','12');
        //return $meses;
        $table = collect([]);
        //itero por cada mes para ir sacando las sumatorias
        foreach($meses as $mes){

            $ventasNetas = Document::query()
            ->whereMonth('date_of_issue',$mes)
            ->whereYear('date_of_issue',$year)
            ->whereNotIn('state_type_id', ['09','11','13'])
            ->sum('total_taxed');

            $comprasNetas = Purchase::query()
            ->whereMonth('date_periodo',$mes)
            ->whereYear('date_periodo',$year)
            ->whereNotIn('state_type_id', ['09','11','13'])
            ->sum('total_taxed');

            $debitoFiscal = round($ventasNetas * self::IGV,2);
            $creditoFiscal = round($comprasNetas * self::IGV,2);

            $igvPorPagar = round($debitoFiscal - $creditoFiscal,2);

            $baseImponible = Document::query()
            ->whereMonth('date_of_issue',$mes)
            ->whereYear('date_of_issue',$year)
            ->whereNotIn('state_type_id', ['09','11','13'])
            ->sum('total_igv');

            $coeficiente = 0.015;

            $rentaPorPagar = round($baseImponible * $coeficiente,2);

            $table->push([
                'periodo' =>  $mes.'- '.$year,
                'ventas_netas' => $ventasNetas,
                'compras_netas' => $comprasNetas,
                'debito_fiscal' => $debitoFiscal,
                'credito_fiscal' => $creditoFiscal,
                'igv_por_pagar' => $igvPorPagar,
                'base_imponible' => $baseImponible,
                'coeficiente' => $coeficiente,
                'renta_por_pagar' => $rentaPorPagar
            ]);


        }

        return response()->json(['table' => $table],200);

    }

    private function getMonths($year){
        /* //lo uso porque da un error al momento de hacer un group by con varios campos
        DB::connection('tenant')->statement("SET sql_mode = '' ");
        //obtengo los meses y los orderno y agrupo por mes
        return Document::select(DB::raw('MONTH(date_of_issue) as month'),'date_of_issue')
        ->whereYear('date_of_issue',$year)
        ->groupBy('month')
        ->orderBy('month')
        ->get(); */

    }

    private function getDocuments($year)
    {

            return Document::query()
                ->whereYear('date_of_issue', $year)
                // ->whereIn('document_type_id', ['01', '03','07','08'])
                // ->whereIn('currency_type_id', ['PEN','USD'])
                ->get();


    }

}
