<?php
namespace App\Http\Controllers\Tenant\Api;

use App\Models\Tenant\User;
use App\Models\Tenant\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenant\CashRequest;
use App\Http\Resources\Tenant\CashCollection;
use App\Http\Resources\Tenant\CashResource;
use App\Models\Tenant\Cash;
use App\Models\Tenant\CashDocument;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\PaymentMethodType;
use Modules\Pos\Models\CashTransaction;
use Modules\Finance\Traits\FinanceTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\SaleNoteItem;
use App\Exports\CashProductExport;
use App\Models\Tenant\Document;
use App\Models\Tenant\SaleNote;


class CashController extends Controller
{

    use FinanceTrait;

    /**
     * web service para recibo de documentos junto con caja
     * apertura y cierre de caja
     * creacion de relaciones de documentos con caja (notas de venta y facturas/boletas)
     *
     * @param $request {}
     * example:
     * beginningBalance: number
     * dateOpening: "Y-m-d"
     * timeOpening: "H:m:s"
     * internalsId: [{external_id: String, type: String NOTA|BOLETA|'' }]
     */
    public function storeRestaurant(Request $request) {

        $cash = new Cash();
        $cash->user_id = auth()->user()->id;
        $cash->date_opening = $request->dateOpening;
        $cash->time_opening = $request->timeOpening;
        $cash->date_closed = date('Y-m-d');
        $cash->time_closed = date('H:i:s');
        $cash->beginning_balance = (float)$request->beginningBalance;
        $cash->final_balance = 0;
        $cash->income = 0;
        $cash->state = 0;
        $cash->reference_number = $request->referenceNumber;
        $cash->apply_restaurant = 1;

        $cash->save();

        $total_documents = 0;

        // se recorren todos los externals id de la caja anteriormente creada
        // para registrar la relación con ella y acumular el monto total
        foreach ($request->internalsId as $row) {
            if($row['type'] == 'NOTA'){
                $sale_note = SaleNote::where('external_id', $row['external_id'])->first();
                $total_documents += (float)$sale_note->total;

                CashDocument::create([
                    'cash_id' => $cash->id,
                    'sale_note_id' => $sale_note->id,
                ]);
            } else {
                $document = Document::where('external_id', $row['external_id'])->first();
                $total_documents += (float)$document->total;

                CashDocument::create([
                    'cash_id' => $cash->id,
                    'document_id' => $document->id,
                ]);
            }
        }

        // se toman los montos anteriores para cerrar la caja
        $cash->income = $total_documents;
        $cash->final_balance = $cash->beginning_balance + $cash->income;
        $cash->save();





        return [
            'success' => true,
            'message' => 'Caja creada con éxito'
        ];
    }

    public function index()
    {
        return view('tenant.cash.index');
    }

    public function columns()
    {
        return [
            'income' => 'Ingresos',
            // 'expense' => 'Egresos',
        ];
    }

    public function records(Request $request)
    {
        $records = Cash::where($request->column, 'like', "%{$request->value}%")
            ->whereTypeUser()
            ->orderBy('date_opening', 'DESC');


        return new CashCollection($records->paginate(config('tenant.items_per_page')));
    }
    public function recordsMovil(Request $request)
    {
        $records = Cash::orderBy('date_opening', 'DESC');


        return new CashCollection($records->paginate(config('tenant.items_per_page')));
    }
    public function recordMovil(Request $request)
    {
        $cash =  Cash::where([['user_id',auth('api')->user()->id],['state',true]])->first();

        $totales= $this->getIncomeTotal($cash);

        return [
            'id' => $cash->id,
            'user_id' => $cash->user_id,
            'user' => $cash->user->name,
            'date_opening' => $cash->date_opening,
            'time_opening' => $cash->time_opening,
            'opening' => "{$cash->date_opening} {$cash->time_opening}",
            'date_closed' => $cash->date_closed,
            'time_closed' => $cash->time_closed,
            'closed' => "{$cash->date_closed} {$cash->time_closed}",
            'beginning_balance' => $cash->beginning_balance,
            'final_balance' => $cash->final_balance,
            'income' => $cash->income,
            'expense' => $cash->expense,
            'filename' => $cash->filename,
            'state' => (bool) $cash->state,
            'state_description' => ($cash->state) ? 'Aperturada':'Cerrada',
            'reference_number' => $cash->reference_number,
            'total_ingresos'=>$totales['total_ingresos'],
            'total_egresos'=>$totales['total_egresos'],
            'saldo_final'=>$totales['saldo_final'],

        ];
    }

    public function create()
    {
        return view('tenant.items.form');
    }

    public function tables()
    {
        $user = auth('api')->user();
        $type = $user->type;
        $users = array();

        switch($type)
        {
            case 'admin':
                $users = User::where('type', 'seller')->get();
                $users->push($user);
                break;
            case 'seller':
                $users = User::where('id', $user->id)->get();
                break;
        }

        return compact('users', 'user');
    }

    public function opening_cash()
    {

        $cash = Cash::where([['user_id', auth('api')->user()->id],['state', true]])->first();

        return compact('cash');
    }

    public function opening_cash_check($user_id)
    {
        $cash = Cash::where([['user_id', $user_id],['state', true]])->first();
        return compact('cash');
    }


    public function record($id)
    {
        $record = new CashResource(Cash::findOrFail($id));

        return $record;
    }

    public function store(CashRequest $request) {

        $id = $request->input('id');

        DB::connection('tenant')->transaction(function () use ($id, $request) {

            $cash = Cash::firstOrNew(['id' => $id]);
            $cash->fill($request->all());

            if(!$id){
                $cash->date_opening = date('Y-m-d');
                $cash->time_opening = date('H:i:s');
            }

            $cash->save();

            $this->createCashTransaction($cash, $request);

        });


        return [
            'success' => true,
            'message' => ($id)?'Caja actualizada con éxito':'Caja aperturada con éxito'
        ];

    }


    public function createCashTransaction($cash, $request){

        $this->destroyCashTransaction($cash);

        $data = [
            'date' => date('Y-m-d'),
            'description' => 'Saldo inicial',
            'payment_method_type_id' => '01',
            'payment' => $request->beginning_balance,
            'payment_destination_id' => 'cash',
            'user_id' => auth('api')->user()->id,
        ];

        $cash_transaction = $cash->cash_transaction()->create($data);

        $this->createGlobalPaymentTransaction($cash_transaction, $data);

    }


    public function close($id) {

        $cash = Cash::findOrFail($id);


        $cash->date_closed = date('Y-m-d');
        $cash->time_closed = date('H:i:s');

        $final_balance = 0;
        $income = 0;

        foreach ($cash->cash_documents as $cash_document) {


            if($cash_document->sale_note){

                if(in_array($cash_document->sale_note->state_type_id, ['01','03','05','07','13'])){
                    $final_balance += ($cash_document->sale_note->currency_type_id == 'PEN') ? $cash_document->sale_note->total : ($cash_document->sale_note->total * $cash_document->sale_note->exchange_rate_sale);
                }

                // $final_balance += $cash_document->sale_note->total;

            }
            else if($cash_document->document){

                if(in_array($cash_document->document->state_type_id, ['01','03','05','07','13'])){
                    $final_balance += ($cash_document->document->currency_type_id == 'PEN') ? $cash_document->document->total : ($cash_document->document->total * $cash_document->document->exchange_rate_sale);
                }

                // $final_balance += $cash_document->document->total;

            }
            else if($cash_document->expense_payment){

                if($cash_document->expense_payment->expense->state_type_id == '05'){
                    $final_balance -= ($cash_document->expense_payment->expense->currency_type_id == 'PEN') ? $cash_document->expense_payment->payment:($cash_document->expense_payment->payment  * $cash_document->expense_payment->expense->exchange_rate_sale);
                }

                // $final_balance -= $cash_document->expense_payment->payment;

            }

            // else if($cash_document->purchase){
            //     $final_balance -= $cash_document->purchase->total;
            // }
            // else if($cash_document->expense){
            //     $final_balance -= $cash_document->expense->total;
            // }

        }

        $cash->final_balance = round($final_balance + $cash->beginning_balance, 2);
        $cash->income = round($final_balance, 2);
        $cash->state = false;
        $cash->save();

        return [
            'success' => true,
            'message' => 'Caja cerrada con éxito',
        ];

    }


    public function cash_document(Request $request) {

        $cash = Cash::where([['user_id',auth()->user()->id],['state',true]])->first();
        $cash->cash_documents()->create($request->all());

        return [
            'success' => true,
            'message' => 'Venta con éxito',
        ];
    }


    public function destroy($id)
    {

        $data = DB::connection('tenant')->transaction(function () use ($id) {

            $cash = Cash::findOrFail($id);

            if($cash->global_destination()->where('payment_type', '!=', CashTransaction::class)->count() > 0){
                return [
                    'success' => false,
                    'message' => 'No puede eliminar la caja, tiene transacciones relacionadas'
                ];
            }

            $this->destroyCashTransaction($cash);
            $cash->delete();

            return [
                'success' => true,
                'message' => 'Caja eliminada con éxito'
            ];

        });

        return $data;

    }


    public function destroyCashTransaction($cash){

        $ini_cash_transaction = $cash->cash_transaction;

        if($ini_cash_transaction){
            CashTransaction::find($ini_cash_transaction->id)->delete();
        }

    }


    public function movimientos($id)
    {

        $cash = Cash::findOrFail($id);

        $company = Company::first();

        $methods_payment = collect(PaymentMethodType::all())->transform(function ($row) {
            return (object)[
                'id' => $row->id,
                'name' => $row->description,
                'sum' => 0
            ];
        });

        set_time_limit(0);

        $cash_documents = $cash->cash_documents;

        $all_documents = [];
        foreach ($cash_documents as $key => $value) {
            if ($value->sale_note) {
                $all_documents[] = $value;
            } else if ($value->document) {
                $all_documents[] = $value;
            } else if ($value->expense_payment) {
                if ($value->expense_payment->expense->state_type_id == '05') {
                    $all_documents[] = $value;
                }
            } else if ($value->income_payment) {
                if ($value->income_payment->income->state_type_id == '05') {
                    $all_documents[] = $value;
                }
            }
        }

        foreach ($all_documents as $key => $value){

            $type_transaction = null;
            $document_type_description = null;
            $number = null;
            $date_of_issue = null;
            $customer_name = null;
            $customer_number = null;
            $currency_type_id = null;
            $total = null;

            if ($value->sale_note) {

                $type_transaction = 'Venta';
                $document_type_description = 'NOTA DE VENTA';
                $number = $value->sale_note->number_full;
                $date_of_issue = $value->sale_note->date_of_issue->format('Y-m-d');
                $customer_name = $value->sale_note->customer->name;
                $customer_number = $value->sale_note->customer->number;

                $total = $value->sale_note->total;

                if (!in_array($value->sale_note->state_type_id, ['01', '03', '05', '07', '13'])) {
                    $total = 0;
                }

                $currency_type_id = $value->sale_note->currency_type_id;

            } else if ($value->document) {

                $type_transaction = 'Venta';
                $document_type_description = $value->document->document_type->description;
                $number = $value->document->number_full;
                $date_of_issue = $value->document->date_of_issue->format('Y-m-d');
                $customer_name = $value->document->customer->name;
                $customer_number = $value->document->customer->number;
                $total = $value->document->total;

                if (!in_array($value->document->state_type_id, ['01', '03', '05', '07', '13'])) {
                    $total = 0;
                }

                $currency_type_id = $value->document->currency_type_id;

            } else if ($value->expense_payment) {

                $type_transaction = 'Gasto';
                $document_type_description = $value->expense_payment->expense->expense_type->description;
                $number = $value->expense_payment->expense->number;
                $date_of_issue = $value->expense_payment->expense->date_of_issue->format('Y-m-d');
                $customer_name = $value->expense_payment->expense->supplier->name;
                $customer_number = $value->expense_payment->expense->supplier->number;
                $total = -$value->expense_payment->payment;
                $currency_type_id = $value->expense_payment->expense->currency_type_id;

            } else if ($value->income_payment) {
                $type_transaction = 'Ingreso';
                $document_type_description = $value->income_payment->income->income_type->description;
                $number = $value->income_payment->income->number;
                $date_of_issue = $value->income_payment->income->date_of_issue->format('Y-m-d');
                $customer_name = $value->income_payment->income->customer;
                $customer_number = 'otros';
                $total = $value->income_payment->payment;
                $currency_type_id = $value->income_payment->income->currency_type_id;

            }

            $documents[] = array(
                'id' =>$key,
                'type_transaction' => $type_transaction,
                'document_type_description' => $document_type_description,
                'number' => $number,
                'date_of_issue' => $date_of_issue,
                'customer_name' => $customer_name,
                'customer_number' => $customer_number,
                'currency_type_id' => $currency_type_id,
                'total' => number_format($total, 2, ".", ""),
            );
        }

        return $documents;
    }

    public function report_general()
    {
        $cashes = Cash::select('id')->whereDate('date_opening', date('Y-m-d'))->pluck('id');
        $cash_documents =  CashDocument::whereIn('cash_id', $cashes)->get();

        $company = Company::first();
        set_time_limit(0);

        $pdf = PDF::loadView('tenant.cash.report_general_pdf', compact("cash_documents", "company"));
        $filename = "Reporte_POS";
        return $pdf->download($filename.'.pdf');

    }

    public function report_products($id)
    {
        $cash = Cash::findOrFail($id);
        $company = Company::first();
        $cash_documents =  CashDocument::select('document_id')->where('cash_id', $cash->id)->get();
        $cash_documents2 =  CashDocument::select('sale_note_id')->where('cash_id', $cash->id)->get();

        $source = DocumentItem::with('document')->whereIn('document_id', $cash_documents)->get();
        $source2 = SaleNoteItem::with('sale_note')->whereIn('sale_note_id', $cash_documents2)->get();

        $documents = collect($source)->transform(function($row){
            return [
                'id' => $row->id,
                'number_full' =>$row->document->number_full,
                'description' => $row->item->description,
                'quantity' => $row->quantity,
            ];
        });
        $sale_notes = collect($source2)->transform(function($row){
            return [
                'id' => $row->id,
                'number_full' => $row->sale_note->series . '-' .str_pad($row->sale_note->number,8,'0',STR_PAD_LEFT),
                'description' => $row->item->description,
                'quantity' => $row->quantity,
            ];
        });


        $pdf = PDF::loadView('tenant.cash.report_product_pdf', compact("cash", "company", "documents","sale_notes"));

        $filename = "Reporte_POS_PRODUCTOS - {$cash->user->name} - {$cash->date_opening} {$cash->time_opening}";

        return $pdf->stream($filename.'.pdf');

    }



    public function report_products_excel($id)
    {

        $data = $this->getDataReport($id);
        $filename = "Reporte_POS_PRODUCTOS - {$data['cash']->user->name} - {$data['cash']->date_opening} {$data['cash']->time_opening}";

        return (new CashProductExport)
            ->documents($data['documents'])
            ->company($data['company'])
            ->cash($data['cash'])
            ->download($filename.'.xlsx');

    }


    public function getDataReport($id){

        $cash = Cash::findOrFail($id);
        $company = Company::first();
        $cash_documents =  CashDocument::select('document_id')->where('cash_id', $cash->id)->get();

        $source = DocumentItem::with('document')->whereIn('document_id', $cash_documents)->get();

        $documents = collect($source)->transform(function($row){
            return [
                'id' => $row->id,
                'number_full' => $row->document->number_full,
                'description' => $row->item->description,
                'quantity' => $row->quantity,
            ];
        });

        $documents = $documents->merge($this->getSaleNotesReportProducts($cash));

        return compact("cash", "company", "documents");

    }


    public function getSaleNotesReportProducts($cash){

        $cd_sale_notes =  CashDocument::select('sale_note_id')->where('cash_id', $cash->id)->get();

        $sale_note_items = SaleNoteItem::with('sale_note')->whereIn('sale_note_id', $cd_sale_notes)->get();

        return collect($sale_note_items)->transform(function($row){
            return [
                'id' => $row->id,
                'number_full' => $row->sale_note->number_full,
                'description' => $row->item->description,
                'quantity' => $row->quantity,
            ];
        });

    }
    public function getIncomeTotal($cash){

        $methods_payment = collect(PaymentMethodType::all())->transform(function($row){
            return (object)[
                'id' => $row->id,
                'name' => $row->description,
                'sum' => 0
            ];
        });

        $final_balance = 0;
        $cash_income = 0;
        $cash_egress = 0;
        $cash_final_balance = 0;


        $cash_documents = $cash->cash_documents;

        foreach ($cash_documents as $cash_document) {

            //$final_balance += ($cash_document->document) ? $cash_document->document->total : $cash_document->sale_note->total;

            if($cash_document->sale_note){

                if($cash_document->sale_note->currency_type_id == 'PEN'){

                    if(in_array($cash_document->sale_note->state_type_id, ['01','03','05','07','13'])){

                        $cash_income += $cash_document->sale_note->total;
                        $final_balance += $cash_document->sale_note->total;

                    }


                }else{

                    if(in_array($cash_document->sale_note->state_type_id, ['01','03','05','07','13'])){

                        $cash_income += $cash_document->sale_note->total * $cash_document->sale_note->exchange_rate_sale;
                        $final_balance += $cash_document->sale_note->total * $cash_document->sale_note->exchange_rate_sale;

                    }

                }

                if(in_array($cash_document->sale_note->state_type_id, ['01','03','05','07','13'])){

                    if( count($cash_document->sale_note->payments) > 0)
                    {
                        $pays = $cash_document->sale_note->payments;

                        foreach ($methods_payment as $record) {

                            $record->sum = ($record->sum + $pays->where('payment_method_type_id', $record->id)->sum('payment') );
                        }

                    }
                }


            }
            else if($cash_document->document){

                if($cash_document->document->currency_type_id == 'PEN'){

                    if(in_array($cash_document->document->state_type_id, ['01','03','05','07','13'])){

                        $cash_income += $cash_document->document->total;
                        $final_balance += $cash_document->document->total;

                    }

                }else{

                    if(in_array($cash_document->document->state_type_id, ['01','03','05','07','13'])){

                        $cash_income += $cash_document->document->total * $cash_document->document->exchange_rate_sale;
                        $final_balance += $cash_document->document->total * $cash_document->document->exchange_rate_sale;

                    }

                }

                if(in_array($cash_document->document->state_type_id, ['01','03','05','07','13'])){

                    if( count($cash_document->document->payments) > 0)
                    {
                        $pays = $cash_document->document->payments;

                        foreach ($methods_payment as $record) {
                            // dd($pays, $record);

                            $record->sum = ($record->sum + $pays->where('payment_method_type_id', $record->id)->whereIn('document.state_type_id', ['01','03','05','07','13'])->sum('payment'));

                        }

                    }
                }




            }
            else if($cash_document->expense_payment){

                if($cash_document->expense_payment->expense->state_type_id == '05'){

                    if($cash_document->expense_payment->expense->currency_type_id == 'PEN'){

                        $cash_egress += $cash_document->expense_payment->payment;
                        $final_balance -= $cash_document->expense_payment->payment;

                    }else{

                        $cash_egress += $cash_document->expense_payment->payment  * $cash_document->expense_payment->expense->exchange_rate_sale;
                        $final_balance -= $cash_document->expense_payment->payment  * $cash_document->expense_payment->expense->exchange_rate_sale;
                    }

                }
            }
            else if($cash_document->income_payment){

                //dd($cash_document->income_payment);
                if($cash_document->income_payment->income->state_type_id == '05'){

                    if($cash_document->income_payment->income->currency_type_id == 'PEN'){

                        $cash_income += $cash_document->income_payment->payment;
                        $final_balance += $cash_document->income_payment->payment;

                    }else{

                        $cash_income += $cash_document->income_payment->payment  * $cash_document->income_payment->income->exchange_rate_sale;
                        $final_balance += $cash_document->income_payment->payment  * $cash_document->income_payment->income->exchange_rate_sale;
                    }

                }
            }

        }

        $cash_final_balance = $final_balance + $cash->beginning_balance;

        return [
            'total_ingresos'=>$cash_income,
            'total_egresos'=>$cash_egress,
            'saldo_final'=>$cash_final_balance
        ];
    }

}
