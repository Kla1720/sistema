<?php
namespace Modules\Account\Http\Controllers;

use Modules\Account\Exports\ReportAccountingConcarExport;
use Modules\Account\Exports\ReportAccountingFoxcontExport;
use Modules\Account\Exports\ReportAccountingContasisExport;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Document;
use App\Models\Tenant\Item;
use App\Models\Tenant\Configuration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Account\Models\AccountingPlan;
use Modules\Account\Models\CompanyAccount;
use Modules\Account\Http\Resources\AccountingPlanResource;
use Modules\Account\Http\Resources\AccountingPlanCollection;
use Modules\Account\Http\Requests\AccountingPlanRequest;
use App\Models\Tenant\Catalogs\CurrencyType;

class AccountingController extends Controller
{
    public function index()
    {
        return view('account::accounting.accounting_plan.index');
    }
    public function columns()
    {
        return [
            'cuenta' => 'Cuenta',
            'nombre' => 'Nombre',
        ];
    }
    public function record($id)
    {
        $record = new AccountingPlanResource(AccountingPlan::findOrFail($id));

        return $record;
    }
    public function records(Request $request)
    {
        $records = AccountingPlan::where($request->column, 'like', "%{$request->value}%")
            ->latest();

        return new AccountingPlanCollection($records->paginate(config('tenant.items_per_page')));

    }
    public function tables()
    {

        $currency_types = CurrencyType::whereActive()->get();

        return compact(  'currency_types');

    }
}
