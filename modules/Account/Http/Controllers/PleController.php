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

class PleController extends Controller
{
    public function index()
    {
        return view('account::account.ple');
    }

    public function download(Request $request)
    {
        $type    = $request->input('type');
        $month   = $request->input('month');
        $date_periodo= Carbon::parse($request->input('month'))->format('Y-m-d');
        $periodo = Carbon::parse($month.'-01')->format('Ym');
        $moneda  = '1';

        $d_start = Carbon::parse($month.'-01')->format('Y-m-d');
        $d_end = Carbon::parse($month.'-01')->endOfMonth()->format('Y-m-d');

        $records = $this->getDocuments($d_start, $d_end,$type,$date_periodo);
        $company = Company::active();

        switch ($type) {

            case '140100': // ventas

                $records = $this->getStructureVentas($records);

                $temp = tempnam(sys_get_temp_dir(), 'txt');
                $file = fopen($temp, 'w+');
                foreach ($records as $record)
                {
                    $line = implode('|', $record);
                    fwrite($file, $line."\r\n");
                }
                fclose($file);

                $lineas=($records) ? '1' : '0';
                $filename = "LE". $company->number.$periodo."00".$type."001". $lineas.$moneda."1";
                return response()->download($temp, $filename.'.txt');

            case '080100': //compras

                $records = $this->getStructureCompras($records);

                //dd($records);
                $temp = tempnam(sys_get_temp_dir(), 'txt');
                $file = fopen($temp, 'w+');
                foreach ($records as $record)
                {
                    $line = implode('|', $record);
                    fwrite($file, $line."\r\n");
                }
                fclose($file);

                $lineas=($records) ? '1' : '0';
                $filename = "LE". $company->number.$periodo."00".$type."001". $lineas.$moneda."1";
                return response()->download($temp, $filename.'.txt');

            case '080200': //compras

                $records = $this->getStructureComprasND($records);

                //dd($records);
                $temp = tempnam(sys_get_temp_dir(), 'txt');
                $file = fopen($temp, 'w+');
                foreach ($records as $record)
                {
                    $line = implode('|', $record);
                    fwrite($file, $line."\r\n");
                }
                fclose($file);

                $lineas=($records) ? '1' : '0';
                $filename = "LE". $company->number.$periodo."00".$type."001". $lineas.$moneda."1";
                return response()->download($temp, $filename.'.txt');

            case 'foxcont':

                $data = [
                    'records' => $this->getStructureFoxcont($records),
                ];

                return (new ReportAccountingFoxcontExport)
                    ->data($data)
                    ->download($filename.'.xlsx');
        }

    }

    private function getDocuments($d_start, $d_end,$type,$date_periodo)
    {
        if($type=='140100') // ventas
        {
            return Document::query()
                ->whereBetween('date_of_issue', [$d_start, $d_end])
                ->whereIn('document_type_id', ['01', '03','07','08'])
                ->whereIn('currency_type_id', ['PEN','USD'])
                ->orderBy('series')
                ->orderBy('number')
                ->get();

        }
        else if($type=='080100') // compras
        {
            $anio= Carbon::parse($date_periodo)->format('Y');
            $mes= Carbon::parse($date_periodo)->format('m');

            return Purchase::query()
                ->whereYear('date_periodo', $anio)
                ->whereMonth('date_periodo', $mes)
                ->whereIn('document_type_id', ['01', '03','07','08','04','02','14'])
                ->whereIn('currency_type_id', ['PEN','USD'])
                ->orderBy('series')
                ->orderBy('number')
                ->get();

        }
        else if($type=='080200') // compras ND
        {
            $anio= Carbon::parse($date_periodo)->format('Y');
            $mes= Carbon::parse($date_periodo)->format('m');

            return Purchase::query()
                ->whereYear('date_periodo', $anio)
                ->whereMonth('date_periodo', $mes)
                ->whereIn('document_type_id', ['00','91','97','98']) // para que me dé vacío
                ->whereIn('currency_type_id', ['PEN','USD'])
                ->orderBy('series')
                ->orderBy('number')
                ->get();

        }
    }

    private function getStructureCompras($documents)
    {

        $company_account = CompanyAccount::first();
        $rows = [];
        foreach ($documents as $index => $row)
        {
            $date_of_issue = Carbon::parse($row->date_of_issue);
            $date_doc= strtotime($row->date_of_issue->format('Y-m'));
            $date_periodo= strtotime($row->date_periodo->format('Y-m'));
            $currency_type_id = ($row->currency_type_id === 'PEN')?'S':'D';
            $detail = $row->supplier->name;
            $tip_doc =$row->document_type_id;

            $number_index = $date_of_issue->format('m').str_pad($index + 1, 4, "0", STR_PAD_LEFT);
            $regimen="";

            if($regimen=='RER'){
                $number_index="M-RER";
            }

            $fechaMod  = '';
            $seriesMod = '';
            $seriesMod = '';
            $numberMod = '';
            $tipoMod   = '';
            if(($row->document_type_id=='07' || $row->document_type_id=='08') && $row->note_purchase==true ){
                $seriesMod = $row->note_purchase->affected_purchase->series;
                $numberMod = $row->note_purchase->affected_purchase->number;
                $fechaMod= str_pad($row->note_purchase->affected_purchase->date_of_issue->format('d/m/Y'), 8,'00', STR_PAD_RIGHT);
                $tipoMod =  $row->note_purchase->affected_purchase->document_type_id;
            }

            $type_basimp= $row->type_basimp;
            $estado=$row->state_type_id;

            $estado_libro ="";
            // SIN RECHAZAR, SIN ANULAR RH, boleta -- NO DAN CREDITO FIZCAL
            if(($estado!='09' && $estado!='11') && $tip_doc  == '02' || $tip_doc == '03'){
                $estado_libro="0";
            }

            // SIN RECHAZAR, SIN ANULAR , DEL PERIODO, FACTURA, LIQ COMPRA, NC, ND, SERVICIOS PUBLICOS -- SI DAN CREDITO FIZCAL
            elseif(($estado!='09' && $estado!='11') && ($date_doc==$date_periodo) && $tip_doc == '01' || $tip_doc == '04' || $tip_doc == '07' || $tip_doc == '08' || $tip_doc == '14'){
                $estado_libro="1";
            }

            //sin rechazar sin anular, factura de fecha pasada, mas de 12 meses
            elseif(($estado!='09' && $estado!='11')  && ($date_doc<$date_periodo) ){

                $firstDate  = new DateTime($row->date_of_issue->format('Y-m'));
                $secondDate = new DateTime($row->date_periodo->format('Y-m'));
                $intvl = $firstDate->diff($secondDate);
                $anio=  $intvl->y;
                $meses=  $intvl->m;
                $tot_mes=($anio*12)+$meses;
                if($tot_mes <12){//sin rechazar sin anular, factura de fecha pasada, menos de 12 meses
                    $estado_libro="6";
                }
                else{
                    $estado_libro="7";  //sin rechazar sin anular, factura de fecha pasada, mas de 12 meses
                }
            }


            $tc    = $row->exchange_rate_sale;
            $total_taxed = $row->total_taxed;
            $total_igv =$row->total_igv;
            $total_free =$row->total_free;
            $total_exportation = $row->total_exportation;
            $total_exonerated  = $row->total_exonerated;
            $total_unaffected  = $row->total_unaffected;
            $total             = $row->total;

            if($row->currency_type_id == 'USD'){
                $total_taxed = $total_taxed*$tc;
                $total_igv   = $total_igv *$tc;
                $total_exportation =$total_exportation*$tc;
                $total_exonerated  = $total_exonerated *$tc;
                $total_unaffected  = $total_unaffected *$tc;
                $total       = $total *$tc;
            }

            $basimp1="";
            $basimp2="";
            $basimp3="";
            $igv1   ="";
            $igv2   ="";
            $igv3   ="";
            $total_no_grabado   = $total_exonerated+$total_unaffected;
            if($type_basimp=='01'){ //credito fizcal
                $basimp1 = $total_taxed;
                $igv1    = $total_igv;
            }
            elseif($type_basimp=='02') // prorrata
            {
                $basimp2 = $total_exportation+$total_free+$total_taxed;
                $igv2    = $total_igv;
            }
            elseif($type_basimp=='03') // costo/gasto
            {
                $basimp3 = $total_exportation+$total_free+$total_taxed;
                $igv3    = $total_igv;
            }

            $rows[] = [
                'col_1' => str_pad($row->date_periodo->format('Ym'), 8,'00', STR_PAD_RIGHT),
                'col_2' =>$number_index,
                'col_3' =>'M'.$number_index,
                'col_4' =>$row->date_of_issue->format('d/m/Y'),
                'col_5' =>'',
                'col_6' =>$row->document_type_id,
                'col_7' =>$row->series,
                'col_8' =>'',
                'col_9' =>$row->number,
                'col_10' =>'',
                'col_11'=>($estado =='11' ) ? '' : $row->supplier->identity_document_type_id,
                'col_12'=>($estado =='11' ) ? '' : $row->supplier->number,
                'col_13' =>($estado =='11' ) ? '' : $detail,
                'col_14' =>($estado =='11') ? ''  : round($basimp1,2),
                'col_15' =>($estado =='11') ? '' : round($igv1,2),
                'col_16' =>($estado =='11')  ? '' : round($basimp2,2), //BI 2 compra destinado a operación no grabada
                'col_17' =>($estado =='11') ? '' : round($igv2,2), //IGV 2
                'col_18' =>($estado =='11') ? '' : round($basimp3,2), //BI 3 compra destinado a exportación
                'col_19' =>($estado =='11') ? '' : round($igv3,2),//IGV 3
                'col_20' =>($estado =='11') ? '' : round($total_no_grabado,2),
                'col_21' =>($row->total_isc>0 && $estado !='11') ? $row->total_isc : '',
                'col_22' =>'0.00',//bolsas
                'col_23' =>'',
                'col_24' =>($estado =='11' ) ? '' : round($total,2),
                'col_25' =>($estado =='11' || $row->currency_type_id =='PEN') ? 'PEN' : $row->currency_type_id,
                'col_26' =>($tc <1 || $estado =='11' || $row->currency_type_id =='PEN') ? '1.000' : $tc,
                'col_27' =>$fechaMod,
                'col_28' =>$tipoMod,
                'col_29' =>$seriesMod,
                'col_30' =>'',
                'col_31' =>$numberMod,
                'col_32' =>'',
                'col_33' =>'',
                'col_34' =>'',
                'col_35' =>'',
                'col_36' =>'',
                'col_37' =>'',
                'col_38' =>'',
                'col_39' =>'',
                'col_40' =>'',
                'col_41' =>'',
                'col_42' =>$estado_libro,
                'col_43' =>''
            ];




        }
        return $rows;
    }
    private function getStructureComprasND($documents)
    {
        $rows = [];
        foreach ($documents as $index => $row)
        {
            $date_of_issue = Carbon::parse($row->date_of_issue);
            $detail = $row->supplier->name;

            $number_index = $date_of_issue->format('m').str_pad($index + 1, 4, "0", STR_PAD_LEFT);
            $regimen="";

            if($regimen=='RER'){
                $number_index="M-RER";
            }

            $estado_libro ="0";
            $tc    = $row->exchange_rate_sale;
            $total             = $row->total;

            if($row->currency_type_id == 'USD'){
                $total       = $total *$tc;
            }

            $rows[] = [
                'col_1' => str_pad($row->date_periodo->format('Ym'), 8,'00', STR_PAD_RIGHT),
                'col_2' =>$number_index,
                'col_3' =>'M'.$number_index,
                'col_4' =>$row->date_of_issue->format('d/m/Y'),
                'col_5' =>$row->document_type_id,
                'col_6' =>$row->series,
                'col_7' =>$row->number,
                'col_8' =>"",
                'col_9' =>"",
                'col_10' =>round($total,2),
                'col_11' =>"",
                'col_12' =>"",
                'col_13' =>"",
                'col_14' =>"",
                'col_15' =>"",
                'col_16' =>($row->currency_type_id =='PEN') ? 'PEN' : $row->currency_type_id,
                'col_17' =>($tc <1 || $row->currency_type_id =='PEN') ? '1.000' : $tc,
                'col_18' =>"PONER EL CODIGO DE PAIS DE TABLA 35, 4 DIGITOS",
                'col_19' =>$detail,
                'col_20' =>"",
                'col_21' =>$row->supplier->number,
                'col_22' =>"",
                'col_23' =>"",
                'col_24' =>"",
                'col_25' =>"",
                'col_26' =>"",
                'col_27' =>"",
                'col_28' =>"",
                'col_29' =>"",
                'col_30' =>"",
                'col_31' =>"CONVENIO DOBLE IMPOSICION, RELLENAR DE TABLA 25, 2 DIGITOS",
                'col_32' =>"",
                'col_33' =>"TIPO DE RENTA, RELLENAR DE TALBA 31, 2 DIGITOS",
                'col_34' =>"",
                'col_35' =>"",
                'col_36' =>$estado_libro,
                'col_37' =>"",
            ];
        }
        return $rows;
    }
    private function getStructureVentas($documents)
    {
        $company_account = CompanyAccount::first();
        $rows = [];
        foreach ($documents as $index => $row)
        {
            $date_of_issue = Carbon::parse($row->date_of_issue);
            $currency_type_id = ($row->currency_type_id === 'PEN')?'S':'D';
            $detail = $row->customer->name;

            $number_index = $date_of_issue->format('m').str_pad($index + 1, 4, "0", STR_PAD_LEFT);
            $regimen="";

            if($regimen=='RER'){
                $number_index="M-RER";
            }

            $estado=$row->state_type_id;
            $tc    = $row->exchange_rate_sale;
            $total_taxed = $row->total_taxed;
            $total_igv =$row->total_igv;
            $total_icbper=$row->total_plastic_bag_taxes;
            $total_exportation = $row->total_exportation;
            $total_exonerated  = $row->total_exonerated;
            $total_unaffected  = $row->total_unaffected;
            $total             = $row->total;

            if($row->currency_type_id == 'USD'){
                $total_taxed = $total_taxed*$tc;
                $total_igv   = $total_igv *$tc;
                $total_icbper= $total_icbper*$tc;
                $total_exportation =$total_exportation*$tc;
                $total_exonerated  = $total_exonerated *$tc;
                $total_unaffected  = $total_unaffected *$tc;
                $total             = $total *$tc;
            }

            /*================NOTAS DE CRÉDITO DEBITO===============*/
            $fechaMod  = '';
            $seriesMod = '';
            $numberMod = '';
            $tipoMod   = '';
            if($row->document_type_id=='07' || $row->document_type_id=='08'){
                $seriesMod = $row->note->affected_document->series;
                $numberMod = $row->note->affected_document->number;
                $fechaMod= str_pad($row->note->affected_document->date_of_issue->format('d/m/Y'), 8,'00', STR_PAD_RIGHT);
                $tipoMod =  $row->note->affected_document->document_type_id;
            }

            /*================CAMBIO SIGNOS==================*/
            if($row->document_type_id=='07'){ //nota de crédito
                $total_taxed       = $total_taxed       * -1;
                $total_igv         = $total_igv         * -1;
                $total_exportation = $total_exportation * -1;
                $total_exonerated  = $total_exonerated  * -1;
                $total_unaffected  = $total_unaffected  * -1;
                $total             = $total             * -1;
            }


            $rows[] = [
                'col_1' => str_pad($row->date_of_issue->format('Ym'), 8,'00', STR_PAD_RIGHT),
                'col_2' =>$number_index,
                'col_3' =>'M'.$number_index,
                'col_4' =>$row->date_of_issue->format('d/m/Y'),
                'col_5' =>'',
                'col_6' =>$row->document_type_id,
                'col_7' =>$row->series,
                'col_8' =>$row->number,
                'col_9' =>'',
                'col_10'=>($estado =='11' || $estado=='09') ? '' : $row->customer->identity_document_type_id,
                'col_11'=>($estado =='11' || $estado=='09') ? '' : $row->customer->number,
                'col_12'=>($estado =='11' || $estado=='09') ? '' : $detail,
                'col_13' =>($total_exportation>0 && $estado !='11') ? $total_exportation : '',
                'col_14' =>($estado =='11' || $estado=='09') ? '' : $total_taxed,
                'col_15' =>'',//para nota revisar
                'col_16' =>($estado =='11' || $estado=='09') ? '' : $total_igv,
                'col_17' =>'',//para nota revisar
                'col_18' =>($total_exonerated>0 && $estado !='11' || $estado!='09') ? $total_exonerated : '',
                'col_19' =>($total_unaffected>0 && $estado !='11' || $estado!='09') ? $total_unaffected : '',
                'col_20' =>($row->total_isc>0 && $estado !='11' || $estado!='09') ? $row->total_isc : '',
                'col_21' =>'',
                'col_22' =>'',
                'col_23' =>($total_icbper>0 && $estado !='11' || $estado!='09')? $total_icbper:'0.00',//bolsas
                'col_24' =>'',
                'col_25' =>($estado =='11' || $estado=='09' ) ? '' : $total,
                'col_26' =>($estado =='11' || $estado=='09' || $row->currency_type_id =='PEN') ? 'PEN' : $row->currency_type_id,
                'col_27' =>($tc <1 || $estado =='11' || $estado=='09' || $row->currency_type_id =='PEN') ? '1.000' : $tc,
                'col_28' =>$fechaMod,
                'col_29' =>$tipoMod,
                'col_30' =>$seriesMod,
                'col_31' =>$numberMod,
                'col_32' =>'',
                'col_33' =>'',
                'col_34' =>'',
                'col_35' =>($estado=='11' || $estado=='09') ? '2' : '1',
                'col_36' =>''
            ];



        }
        return $rows;
    }

}
