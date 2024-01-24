<?php
namespace Modules\Account\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tenant\Item;
use Illuminate\Http\Request;
use App\Models\Tenant\Document;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Modules\Account\Exports\ReportAccountingAbContExport;
use Modules\Account\Models\CompanyAccount;
use Modules\Account\Exports\ReportAccountingAdsoftExport;
use Modules\Account\Exports\ReportAccountingConcarExport;
use Modules\Account\Exports\ReportAccountingFoxcontExport;
use Modules\Account\Exports\ReportAccountingContasisExport;
use Modules\Account\Exports\ReportAccountingSumeriusExport;
use Modules\Account\Exports\ReportAccountingSiscontExport;

class AccountController extends Controller
{
    public function index()
    {
        return view('account::accounting.index');
    }

    public function download(Request $request)
    {
        $type = $request->input('type');
        $month = $request->input('month');

        $d_start = Carbon::parse($month.'-01')->format('Y-m-d');
        $d_end = Carbon::parse($month.'-01')->endOfMonth()->format('Y-m-d');

        $records = $this->getDocuments($d_start, $d_end);
        $filename = 'Reporte_'.ucfirst($type).'_Ventas_'.date('YmdHis');

        switch ($type) {
            case 'concar':
                $data = [
                    'records' => $this->getStructureConcar($this->getAllDocuments($d_start, $d_end)),
                ];

                $report = (new ReportAccountingConcarExport)
                            ->data($data)
                            ->download($filename.'.xlsx');

                return $report;

            case 'siscont':

                $data = [
                    'records' => $this->getStructureSiscont($this->getAllDocuments($d_start, $d_end)),
                ];

                $report = (new ReportAccountingSiscontExport)
                    ->data($data)
                    ->download($filename.'.xlsx');

                return $report;

            case 'foxcont':

                $data = [
                    'records' => $this->getStructureFoxcont($records),
                ];

                return (new ReportAccountingFoxcontExport)
                    ->data($data)
                    ->download($filename.'.xlsx');

            case 'contasis':

                $data = [
                    'records' => $this->getStructureContasis($records),
                ];

                return (new ReportAccountingContasisExport)
                    ->data($data)
                    ->download($filename.'.xlsx');

            case 'adsoft':

                $data = [
                    'records' => $this->getStructureAdsoft($records),
                ];

                return (new ReportAccountingAdsoftExport)
                    ->data($data)
                    ->download($filename.'.xlsx');
            case 'sumerius':

                $data = [
                    'records' => $this->getStructureSumerius($records),
                ];

                return (new ReportAccountingSumeriusExport)
                    ->data($data)
                    ->download($filename.'.xlsx');

            case 'abcont':

                $data = [
                    'records' => $this->getStructureAbcont($records),
                ];

                return (new ReportAccountingAbContExport)
                    ->data($data)
                    ->download($filename.'.csv');
        }
    }

    private function getStructureSumerius($documents)
    {
        return $documents->transform(function($row) {
            return [
                'col_A' => number_format($row->id, 2, ".", ""),
                'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                'date_of_due' => $row->invoice->date_of_due->format('d/m/Y'),
                'document_type_id' => $row->document_type_id,
		        'state_type_id' => $row->state_type_id,
                'series' => $row->series,
                'number' => str_pad($row->number, 7, '0', STR_PAD_LEFT),
                'col_G' => '',
                'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                'customer_number' => $row->customer->number,
                'customer_name' => $row->customer->name,
                'total_isc' => number_format($row->total_isc, 2, ".", ""),
                'total_exportation' => number_format($row->total_exportation, 2, ".", ""),
                'total_unaffected' => number_format($row->total_unaffected, 2, ".", ""),
                'total_taxed' => number_format($row->total_taxed, 2, ".", ""),
                'total_igv' => number_format($row->total_igv, 2, ".", ""),
                'total_plastic_bag_taxes' => number_format($row->total_plastic_bag_taxes, 2, ".", ""),
                'total' => number_format($row->total, 2, ".", ""),
                'total_exonerated' => number_format($row->total_exonerated, 2, ".", ""),
                'total_retention' => number_format(0, 2, ".", ""),
                'col_S' => '',
                'col_T' => '',
                'col_U' => '',
                'col_V' => '70121',
                'col_W' => '',
                'col_X' => '',
                'col_Y' => '401112',
                'col_Z' => '1212',
                'col_AA' => 'VENTA NACIONAL',
            ];
        });
    }

    private function getStructureAdsoft($documents)
    {
        $rows = [];
        foreach ($documents as $row)
        {
            $document = [
                'serie' => $row->series,
                'numero' => $row->number,
                'fecfac' => Carbon::parse($row->date_of_issue)->format('d/m/Y'),
                'fecven' => Carbon::parse($row->invoice->date_of_due)->format('d/m/Y'),
                'nro_ruc' => $row->customer->identity_document_type_id === '6' ? $row->customer->number : '',
                'nombre' => $row->customer->name,
                'tipdoc' => $row->document_type_id,
                'tipmon' => strtoupper($row->currency_type->description),
                'detrac' => '',
                'isc' => $row->state_type_id == '11' ? 0 : number_format($row->total_isc, 2, '.', ''),
                'icbper' => '',
                'imp_ina' => 0,
                'imp_exp' => '',
                'recargo' => '',
                'st' => $row->state_type_id === '11' ? 'A' : '',
                'ser_dqm' => '',
                'nro_dqm' => '',
                'fec_dqm' => '',
                'tip_dqm' => '',
                'serie_fin' => '',
                'numero_fin' => '',
                'nro_dni' => $row->customer->identity_document_type_id === '1' ? $row->customer->number : '',
                'pasaporte' => '',
                'cta_vta' => '',
                'tip_cam' => '',
            ];
			if ($row->state_type_id === '11') {
                $document['imp_exo'] = 0;
                $document['imp_vta'] = 0;
                $document['imp_tot'] = 0;
                $document['imp_igv'] = 0;
            } else {
                if ($row->total_exonerated == 0) {
                    $document['imp_exo'] = 0;
                    $document['imp_vta'] = number_format($row->total_value, 2, '.', '');
                    $document['imp_tot'] = number_format($row->total, 2, '.', '');
                    $document['imp_igv'] = number_format($row->total_igv, 2, '.', '');
                } else {
                    $document['imp_exo'] = number_format($row->total_exonerated, 2, '.', '');
                    $document['imp_vta'] = number_format($row->total_exonerated, 2, '.', '');
                    $document['imp_tot'] = number_format($row->total_exonerated, 2, '.', '');
                    $document['imp_igv'] = 0;
                }
            }
            array_push($rows, $document);
        }
        return $rows;
    }

    private function getStructureAbcont($documents)
    {
        $rows = [];
        foreach ($documents as $row)
        {
            $glosa='';
            if($row->document_type_id=='03' || $row->document_type_id=='01'){
                $glosa="VENTA";
            }
            elseif($row->document_type_id=='07'){
                $glosa="DSCTO";
            }
            elseif($row->document_type_id=='08'){
                $glosa="GASTOS";
            }
            if($row->total_exonerated>0){
                $glosa="EXONERADA";
            }
            elseif($row->state_type_id == '11'){
                $glosa="ANULADA";
            }

            $document = [
                'tipdoc' => $row->document_type_id,
                'serie' => $row->series,
                'numero' => $row->number,
                'fecfac' => Carbon::parse($row->date_of_issue)->format('d/m/Y'),
                'nro_ruc' => $row->state_type_id == '11' ? 1 : $row->customer->number,
                'nombre' => $row->customer->name,
                'tipmon' => strtoupper($row->currency_type->description),
                'detrac' => '',
                'isc' => $row->state_type_id == '11' ? 0 : number_format($row->total_isc, 2, '.', ''),
                'icbper' => '',
                'imp_ina' => 0,
                'imp_exp' => '',
                'recargo' => '',
                'st' => $row->state_type_id === '11' ? 'A' : '',
                'ser_dqm' => '',
                'nro_dqm' => '',
                'fec_dqm' => '',
                'tip_dqm' => '',
                'serie_fin' => '',
                'numero_fin' => '',
                'nro_dni' => $row->customer->identity_document_type_id === '1' ? $row->customer->number : '',
                'pasaporte' => '',
                'cta_vta' => '',
                't_base' => ($row->total_igv >1) ? 'A' : "X",
                'tip_cam' => ($row->exchange_rate_sale >1) ?$row->exchange_rate_sale : "1.000",
                'glosa'=>$glosa
            ];

            if ($row->state_type_id === '11') {
                $document['imp_exo'] = 0;
                $document['imp_vta'] = 0;
                $document['imp_tot'] = 0;
                $document['imp_igv'] = 0;
            } else {
                if ($row->total_exonerated == 0) {
                    $document['imp_exo'] = 0;
                    $document['imp_vta'] = number_format($row->total_value, 2, '.', '');
                    $document['imp_tot'] = number_format($row->total, 2, '.', '');
                    $document['imp_igv'] = number_format($row->total_igv, 2, '.', '');
                } else {
                    $document['imp_exo'] = number_format($row->total_unaffected +$row->total_exonerated, 2, '.', '');
                    $document['imp_vta'] = number_format($row->total_taxed, 2, '.', '');
                    $document['imp_tot'] = number_format($row->total, 2, '.', '');
                    $document['imp_igv'] =  number_format($row->total_igv, 2, '.', '');
                }
            }

            $reference_document_type_id = '';
            $reference_series='';
            $reference_number='';
            $reference_date_of_issue = '';

            if(in_array($row->document_type_id, ['07', '08']))
            {
                $reference_document_type_id = $this->getShortDocumentType($row->note->affected_document->document_type_id);
                $reference_series = $row->note->affected_document->series;
                $reference_number = $row->note->affected_document->number;
                $reference_date_of_issue = $row->note->affected_document->date_of_issue->format('d/m/Y');
            }
            $document['tref'] =$reference_document_type_id;
            $document['sref'] =$reference_series;
            $document['nref'] =$reference_number;
            $document['fref'] =$reference_date_of_issue;

            array_push($rows, $document);
        }
        return $rows;
    }

    private function getDocuments($d_start, $d_end)
    {
        return Document::query()
                                ->whereBetween('date_of_issue', [$d_start, $d_end])
                                ->whereIn('document_type_id', ['01', '03','07','08'])
                                ->whereIn('currency_type_id', ['PEN','USD'])
                                ->orderBy('series')
                                ->orderBy('number')
                                ->get();

    }

    private function getAllDocuments($d_start, $d_end)
    {
        return Document::query()
                                ->whereBetween('date_of_issue', [$d_start, $d_end])
                                ->whereIn('currency_type_id', ['PEN','USD'])
                                ->orderBy('series','DESC')
                                ->orderBy('number')
                                ->get();

    }

    private function getStructureFoxcont($documents)
    {

        return $documents->transform(function($row) {
            return [
                'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                'date_of_due' => $row->invoice->date_of_due->format('d/m/Y'),
                'document_type_id' => $row->document_type_id,
                'state_type_id' => $row->state_type_id,
                'series' => $row->series,
                'number' => str_pad($row->number, 7, '0', STR_PAD_LEFT),
                'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                'customer_number' => $row->customer->number,
                'customer_name' => $row->customer->name,
                'total_isc' => number_format($row->total_isc, 2, ".", ""),
                'total_exportation' => number_format($row->total_exportation, 2, ".", ""),
                'total_unaffected' => number_format($row->total_unaffected, 2, ".", ""),
                'total_taxed' => number_format($row->total_taxed, 2, ".", ""),
                'total_igv' => number_format($row->total_igv, 2, ".", ""),
                'total_plastic_bag_taxes' => number_format($row->total_plastic_bag_taxes, 2, ".", ""),
                'total_exonerated' => number_format($row->total_exonerated, 2, ".", ""),
                'total_retention' => number_format(0, 2, ".", ""),
                'total' => number_format($row->total, 2, ".", ""),
            ];
        });

    }

    private function getShortDocumentType($document_type_id)
    {

        $document_type = "";

        switch ($document_type_id) {
            case '01':
                $document_type = 'FT';
                break;
            case '03':
                $document_type = 'BV';
                break;
            case '07':
                $document_type = 'NA';
                break;
            case '08':
                $document_type = 'ND';
                break;
        }

        return $document_type;

    }


    private function getStructureConcar($documents)
    {
        $company_account = CompanyAccount::first();
        $rows = [];
        foreach ($documents as $index => $row)
        {
            $date_of_issue = Carbon::parse($row->date_of_issue);
            $currency_type_id = ($row->currency_type_id === 'PEN')?'MN':'US';
            $document_type_id = $this->getShortDocumentType($row->document_type_id);
            $detail = $row->customer->name.', '.$document_type_id.' '.$row->number_full;
            $number_index = $date_of_issue->format('m').str_pad($index + 1, 4, "0", STR_PAD_LEFT);

            $main_gloss = 'VENTAS DEL DIA '.$date_of_issue->format('dmY');
            $date_of_due = ($row->invoice) ? $row->invoice->date_of_due->format('d/m/Y') : '';

            $reference_document_type_id = '';
            $reference_number_full = '';
            $reference_date_of_issue = '';
            $reference_total_value = '';
            $reference_total_igv = '';

            if(in_array($row->document_type_id, ['07', '08']))
            {
                $reference_document_type_id = $this->getShortDocumentType($row->note->affected_document->document_type_id);
                $reference_number_full = $row->note->affected_document->number_full;
                $reference_date_of_issue = $row->note->affected_document->date_of_issue->format('d/m/Y');
                $reference_total_value = $row->note->affected_document->total_value;
                $reference_total_igv = $row->note->affected_document->total_igv;

            }

            foreach ($row->items as $item) {

                if(in_array($row->document_type_id, ['07']))
                {

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '121201',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->total_pen : $company_account->total_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'H',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total / $row->exchange_rate_sale, 2, ".", "") : $item->total),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total : number_format($item->total * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        // 'col_W' => $detail,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '401111',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->igv_pen : $company_account->igv_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'D',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total_igv,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total_igv / $row->exchange_rate_sale, 2, ".", "") : $item->total_igv),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total_igv : number_format($item->total_igv * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '704101',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->subtotal_pen : $company_account->subtotal_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'D',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total_value,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total_value / $row->exchange_rate_sale, 2, ".", "") : $item->total_value),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total_value : number_format($item->total_value * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                }
                else
                {

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '121201',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->total_pen : $company_account->total_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'D',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total / $row->exchange_rate_sale, 2, ".", "") : $item->total),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total : number_format($item->total * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        // 'col_W' => $detail,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '401111',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->igv_pen : $company_account->igv_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'H',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total_igv,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total_igv / $row->exchange_rate_sale, 2, ".", "") : $item->total_igv),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total_igv : number_format($item->total_igv * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '704101',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->subtotal_pen : $company_account->subtotal_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'H',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total_value,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total_value / $row->exchange_rate_sale, 2, ".", "") : $item->total_value),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total_value : number_format($item->total_value * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];
                }

            }

        }
        return $rows;
    }

    private function getStructureSiscont($documents)
    {
        return $documents->transform(function($row) {
            $company_account = CompanyAccount::first();
            return [
                'col_A' => number_format($row->id, 2, ".", ""),
                'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                'date_of_due' => ($row->invoice) ? $row->invoice->date_of_due->format('d/m/Y') : '',
                'document_type_id' => $row->document_type_id,
                'state_type_id' => $row->state_type_id,
                'series' => $row->series,
                'number' => str_pad($row->number, 7, '0', STR_PAD_LEFT),
                'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                'customer_number' => $row->customer->number,
                'customer_name' => $row->customer->name,
                'currency' => ($row->currency_type_id === 'PEN')?'S':'D',
                'total_isc' => ($row->total_isc>0) ? number_format($row->total_isc, 2, ".", "") : "",
                'total_exportation' =>($row->total_exportation>0) ? number_format($row->total_exportation, 2, ".", ""): "",
                'total_unaffected' =>($row->total_unaffected>0) ? number_format($row->total_unaffected, 2, ".", ""): "",
                'total_taxed' => ($row->total_taxed>0) ?number_format($row->total_taxed, 2, ".", ""): "",
                'total_igv' => ($row->total_igv>0) ?number_format($row->total_igv, 2, ".", ""): "",
                'total_plastic_bag_taxes' => ($row->total_plastic_bag_taxes>0) ?number_format($row->total_plastic_bag_taxes, 2, ".", ""): "",
                'total' => number_format($row->total, 2, ".", ""),
                'total_exonerated' => ($row->total_exonerated>0) ?number_format($row->total_exonerated, 2, ".", ""): "",
                'total_retention' => number_format(0, 2, ".", ""),
                'cta_ingreso' => ($row->currency_type_id === 'PEN') ? $company_account->subtotal_pen : $company_account->subtotal_usd,
                'cta_igv' => ($row->currency_type_id === 'PEN') ? $company_account->igv_pen : $company_account->ig_usd,
                'cta_x_cobrar' => ($row->currency_type_id === 'PEN') ? $company_account->total_pen : $company_account->total_pen,
                'exchange_rate_sale' => ($row->exchange_rate_sale >1) ?$row->exchange_rate_sale : "1.00"
            ];
        });

    }

    private function getStructureContasis($documents)
    {

        return $documents->transform(function($row) {
            $company_account = CompanyAccount::first();
            $document_base = ($row->note) ? $row->note : null;
            $payment_condition = '';
            $payment_method = '';

            if($row->payments->count() > 0){
                if($row->payments[0]->payment_method_type_id == '01') {
                    $payment_condition = 'CON';
                    $payment_method = '008';
                }elseif($row->payments[0]->payment_method_type_id == '09'){
                    $payment_condition = 'CRE';
                    $payment_method = '005';
                }
            }else{
                $payment_condition = '';
                $payment_method = '';
            }
            return [
                'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                'date_of_due' => $row->invoice->date_of_due->format('d/m/Y'),
                'document_type_id' => $row->document_type_id,
                'state_type_id' => $row->state_type_id,
                'series' => '00'.$row->series,
                'number' => str_pad($row->number, 13, '0', STR_PAD_LEFT),
                'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                'customer_number' => $row->customer->number,
                'customer_name' => $row->customer->name,

                'total_exportation' => number_format($row->total_exportation, 2, ".", ""),
                'total_taxed' => number_format($row->total_taxed, 2, ".", ""),
                'total_exonerated' => number_format($row->total_exonerated, 2, ".", ""),
                'total_unaffected' => number_format($row->total_unaffected, 2, ".", ""),
                'total_isc' => number_format($row->total_isc, 2, ".", ""),
                'total_igv' => number_format($row->total_igv, 2, ".", ""),
                'total_other_taxes' => number_format($row->total_total_other_taxes, 2, ".", ""),
                'total' => number_format($row->total, 2, ".", ""),
                'exchange_rate_sale' => number_format($row->exchange_rate_sale, 2, ".", ""),
                'db_date_issue' => ($document_base) ? $document_base->affected_document->date_of_issue->format('d/m/Y') : '',
                'db_document_type_id' => ($document_base) ? $document_base->affected_document->document_type_id : '',
                'db_series' => ($document_base) ? $document_base->affected_document->series : '',
                'db_number' => ($document_base) ? str_pad($document_base->affected_document->number, 13, '0', STR_PAD_LEFT) : '',
                'currency' => ($row->currency_type_id === 'PEN')?'S':'D',
                'amount_usd' => null,
                'date_of_due' => $row->invoice->date_of_due->format('d/m/Y'),
                'payment_condition' => $payment_condition,
                'account_taxed' => ($row->currency_type_id === 'PEN') ? $company_account->subtotal_pen : $company_account->subtotal_usd,
                'account_total' => ($row->currency_type_id === 'PEN') ? $company_account->total_pen : $company_account->total_usd,
                'aditional_information' => $row->aditional_information,
                'payment_method' => $payment_method,
            ];
        });
    }
}
