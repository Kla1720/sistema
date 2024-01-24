<?php

namespace App\Http\Controllers\Tenant\Api;

use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Tenant\EmailController;
use App\Http\Requests\Tenant\DispatchRequest;
use App\Models\Tenant\Catalogs\TransferReasonType;
use App\Models\Tenant\Catalogs\TransportModeType;
use App\Models\Tenant\Catalogs\UnitType;
use Exception;
use Carbon\Carbon;
use App\Models\Tenant\Item;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use App\Models\Tenant\Person;
use App\Models\Tenant\Series;
use App\Models\Tenant\Company;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Document;
use App\Mail\Tenant\DocumentEmail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Tenant\PersonRequest;
use Modules\ApiPeruDev\Http\Controllers\ServiceDispatchController;
use Modules\Dispatch\Http\Controllers\DispatcherController;
use Modules\Dispatch\Http\Requests\DispatcherRequest;
use Modules\Dispatch\Models\Dispatcher;
use Modules\Dispatch\Models\Driver;
use Modules\Dispatch\Models\Transport;
use Modules\Item\Http\Requests\ItemRequest;
use Modules\Dashboard\Helpers\DashboardData;
use Modules\Finance\Helpers\UploadFileHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Item\Http\Requests\ItemUpdateRequest;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Warehouse;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Finance\Traits\FinanceTrait;
use Modules\MobileApp\Models\AppConfiguration;
use Modules\Item\Models\{
    Category
};
use App\Http\Controllers\Tenant\ItemController as ItemWebController;
use Modules\Dispatch\Http\Requests\DriverRequest;
use Modules\Dispatch\Http\Controllers\DriverController;
use Modules\Dispatch\Http\Controllers\TransportController;
use Modules\Dispatch\Http\Requests\TransportRequest;
use Modules\Transporte\Models\TransporteChofer;
use Modules\Transporte\Models\TransporteVehiculo;


class MobileGuiaFacilController extends Controller
{
    use  FinanceTrait;

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'data_user'=>[],
                'message' => 'No Autorizado'
            ];
        }

        $company = Company::active();

        if($company->logo){
            $logo = file_get_contents(public_path("storage/uploads/logos/".$company->logo.""));
            $logo_base64 = base64_encode($logo);
        }else{
            $logo=file_get_contents(public_path("/logo/700x300.jpg"));
            $logo_base64 = base64_encode($logo);
        }

        $user = $request->user();
        return [
            'success' => true,
            'data_user'=>[
                'user_id'=> $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'seriedefault' => $user->series_id,
                'token' => $user->api_token,
                'company_ruc' => $company->number,
                'company_razon_social'=> $company->name,
                'company_trade_name'=> $company->trade_name,
                'establishment_mail'=> auth()->user()->establishment->email,
                'establishment_address' => auth()->user()->establishment->address. ', '. auth()->user()->establishment->department->description.' - '.auth()->user()->establishment->province->description.' - '.auth()->user()->establishment->district->description,
                'establishment_phone' => auth()->user()->establishment->telephone,
                'establishment_establishment_id'=>auth()->user()->establishment->id,
                'establishment_country_id'=>auth()->user()->establishment->country_id,
                'establishment_code'=>auth()->user()->establishment->code,
                'establishment_ubigeo'=>auth()->user()->establishment->district_id,
                'soap_type_id'=> $company->soap_type_id,
                'logo_base64'=>$logo_base64,
            ],
            'message' => 'Autorizado'
        ];

    }


    /**
     *
     * Obtener configuracion para app
     *
     * @return array
     */
    public function getAppConfiguration()
    {
        return optional(AppConfiguration::first())->getRowResource();
    }

    public function customers()
    {
        $customers = Person::whereType('customers')->orderBy('name')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'identity_document_type_code' => $row->identity_document_type->code,
                'address' => $row->address,
                'telephone' => $row->telephone,
                'country_id' => $row->country_id,
                'district_id' => $row->district_id,
                'email' => $row->email,
                'selected' => false
            ];
        });

        return [
            'success' => true,
            'data' => array('customers' => $customers)
        ];

    }

    public function tables()
    {
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        /*$customers = Person::whereType('customers')->orderBy('name')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'identity_document_type_code' => $row->identity_document_type->code
            ];
        });*/
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

        $items = Item::with(['brand', 'category'])
                    ->whereWarehouse()
                    ->whereHasInternalId()
                    // ->whereNotIsSet()
                    ->whereIsActive()
                    ->orderBy('description')
                    ->take(20)
                    ->get()
                    ->transform(function($row) use($warehouse){
                        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                        return [
                            'id' => $row->id,
                            'item_id' => $row->id,
                            'name' => $row->name,
                            'full_description' => $full_description,
                            'description' => $row->description,
                            'currency_type_id' => $row->currency_type_id,
                            'internal_id' => $row->internal_id,
                            'item_code' => $row->item_code,
                            'currency_type_symbol' => $row->currency_type->symbol,
                            'sale_unit_price' => $row->generalApplyNumberFormat($row->sale_unit_price),
                            // 'sale_unit_price' => number_format($row->sale_unit_price, 2),
                            'price' => $row->sale_unit_price,
                            'purchase_unit_price' => $row->purchase_unit_price,
                            'unit_type_id' => $row->unit_type_id,
                            'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                            'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                            'calculate_quantity' => (bool) $row->calculate_quantity,
                            'has_igv' => (bool) $row->has_igv,
                            'is_set' => (bool) $row->is_set,
                            'aux_quantity' => 1,
                            'brand' => $row->brand->name,
                            'category' => $row->brand->name,
                            'stock' => $row->getWarehouseCurrentStock($warehouse),
                            // 'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                            'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                        ];
                    });


        return [
            'success' => true,
            'data' => [
                'items' => $items,
                'affectation_types' => $affectation_igv_types,
                'categories' => Category::filterForTables()->get()
            ]
        ];

    }


    public function getSeries()
    {

        return Series::where('establishment_id', auth()->user()->establishment_id)
                    ->whereIn('document_type_id', ['01', '03'])
                    ->get()
                    ->transform(function($row) {
                        return $row->getApiRowResource();
                    });

    }

    public function getPaymentmethod(){

        $payment_method_type = PaymentMethodType::all();
        $payment_destinations = $this->getPaymentDestinations();
        return compact( 'payment_method_type','payment_destinations');
    }


    public function document_email(Request $request)
    {
        $company = Company::active();
        $document = Document::find($request->id);
        $customer_email = $request->email;

        $email = $customer_email;
        $mailable =new DocumentEmail($company, $document);
        $id =  $request->id;
        $sendIt = EmailController::SendMail($email, $mailable, $id, 1);
        /*
        Configuration::setConfigSmtpMail();
        $array_email = explode(',', $customer_email);
        if (count($array_email) > 1) {
            foreach ($array_email as $email_to) {
                $email_to = trim($email_to);
                if(!empty($email_to)) {
                    Mail::to($email_to)->send(new DocumentEmail($company, $document));
                }
            }
        } else {
            Mail::to($customer_email)->send(new DocumentEmail($company, $document));
        }
        */

        return [
            'success' => true,
            'message'=> 'Email enviado correctamente.'
        ];
    }


    public function item(ItemRequest $request)
    {

        $row = DB::connection('tenant')->transaction(function () use ($request) {

            $row = new Item();
            $row->item_type_id = '01';
            $row->amount_plastic_bag_taxes = Configuration::firstOrFail()->amount_plastic_bag_taxes;
            $row->fill($request->all());
            $temp_path = $request->input('temp_path');

            if($temp_path) {

                UploadFileHelper::checkIfValidFile($request->input('image'), $temp_path, true);

                $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR;

                $file_name_old = $request->input('image');
                $file_name_old_array = explode('.', $file_name_old);
                $file_content = file_get_contents($temp_path);
                $datenow = date('YmdHis');
                $file_name = Str::slug($row->description).'-'.$datenow.'.'.$file_name_old_array[1];
                Storage::put($directory.$file_name, $file_content);
                $row->image = $file_name;

                //--- IMAGE SIZE MEDIUM
                $image = \Image::make($temp_path);
                $file_name = Str::slug($row->description).'-'.$datenow.'_medium'.'.'.$file_name_old_array[1];
                $image->resize(512, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                Storage::put($directory.$file_name,  (string) $image->encode('jpg', 30));
                $row->image_medium = $file_name;

                //--- IMAGE SIZE SMALL
                $image = \Image::make($temp_path);
                $file_name = Str::slug($row->description).'-'.$datenow.'_small'.'.'.$file_name_old_array[1];
                $image->resize(256, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                Storage::put($directory.$file_name,  (string) $image->encode('jpg', 20));
                $row->image_small = $file_name;



            }else if(!$request->input('image') && !$request->input('temp_path') && !$request->input('image_url')){
                $row->image = 'imagen-no-disponible.jpg';
            }

            $row->save();

            (new ItemWebController)->generateInternalId($row);

            return $row;

        });

        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

        return [
            'success' => true,
            'msg' => 'Producto registrado con éxito',
            'data' => (object)[
                'id' => $row->id,
                'item_id' => $row->id,
                'name' => $row->name,
                'full_description' => $full_description,
                'description' => $row->description,
                'currency_type_id' => $row->currency_type_id,
                'internal_id' => $row->internal_id,
                'item_code' => $row->item_code,
                'barcode' => $row->barcode,
                'currency_type_symbol' => $row->currency_type->symbol,
                'sale_unit_price' => number_format( $row->sale_unit_price, 2),
                'purchase_unit_price' => $row->purchase_unit_price,
                'unit_type_id' => $row->unit_type_id,
                'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                'calculate_quantity' => (bool) $row->calculate_quantity,
                'has_igv' => (bool) $row->has_igv,
                'is_set' => (bool) $row->is_set,
                'aux_quantity' => 1,
            ],
        ];

    }

    public function person(PersonRequest $request)
    {
        $row = new Person();
        if ($request->department_id === '-') {
            $request->merge([
                'department_id' => null,
                'province_id'   => null,
                'district_id'   => null
            ]);
        }
        $row->fill($request->all());
        $row->save();

        return [
            'success' => true,
            'msg' => ($request->type == 'customers') ? 'Cliente registrado con éxito' : 'Proveedor registrado con éxito',
            'data' => (object)[
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'identity_document_type_code' => $row->identity_document_type->code,
                'address' => $row->address,
                'email' => $row->email,
                'telephone' => $row->telephone,
                'country_id' => $row->country_id,
                'district_id' => $row->district_id,
                'selected' => false
            ]
        ];
    }

    public function searchItems(Request $request)
    {
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();
        $search_by_barcode = $request->has('search_by_barcode') && (bool) $request->search_by_barcode;
        $category_id = $request->category_id ?? null;
        $limit = $request->limit ?? null;

        $item_query = Item::query();

        if($search_by_barcode)
        {
            $item_query->where('barcode', $request->input)->limit(1);
        }
        else
        {
            $item_query->where('description', 'like', "%{$request->input}%")->orWhere('internal_id', 'like', "%{$request->input}%");

            if($limit) $item_query->limit($limit);
        }

        $items = $item_query->whereHasInternalId()
                    ->whereWarehouse()
                    // ->whereNotIsSet()
                    ->filterByCategory($category_id)
                    ->whereIsActive()
                    ->orderBy('description')
                    ->get()
                    ->transform(function($row) use($warehouse){

                        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                        return [
                            'id' => $row->id,
                            'item_id' => $row->id,
                            'name' => $row->name,
                            'full_description' => $full_description,
                            'description' => $row->description,
                            'currency_type_id' => $row->currency_type_id,
                            'internal_id' => $row->internal_id,
                            'item_code' => $row->item_code ?? '',
                            'currency_type_symbol' => $row->currency_type->symbol,
                            'sale_unit_price' => $row->generalApplyNumberFormat($row->sale_unit_price),
                            // 'sale_unit_price' => number_format( $row->sale_unit_price, 2),
                            'purchase_unit_price' => $row->purchase_unit_price,
                            'unit_type_id' => $row->unit_type_id,
                            'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                            'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                            'calculate_quantity' => (bool) $row->calculate_quantity,
                            'has_igv' => (bool) $row->has_igv,
                            'is_set' => (bool) $row->is_set,
                            'aux_quantity' => 1,
                            'barcode' => $row->barcode ?? '',
                            'brand_id' => $row->brand_id,
                            'brand' => optional($row->brand)->name,
                            'category_id' => $row->category_id,
                            'category' => optional($row->category)->name,
                            'stock' => $row->getWarehouseCurrentStock($warehouse),
                            // 'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                            'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                            'warehouses' => collect($row->warehouses)->transform(function($row) {
                                return [
                                    'warehouse_description' => $row->warehouse->description,
                                    'stock' => $row->stock,
                                    'warehouse_id' => $row->warehouse_id,
                                ];
                            }),
                            'item_unit_types' => $row->item_unit_types->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->description,
                                    'unit_type_id' => $row->unit_type_id,
                                    'quantity_unit' => $row->quantity_unit,
                                    'price1' => $row->price1,
                                    'price2' => $row->price2,
                                    'price3' => $row->price3,
                                    'price_default' => $row->price_default,
                                ];
                            }),
                            'has_isc' => (bool)$row->has_isc,
                            'system_isc_type_id' => $row->system_isc_type_id,
                            'percentage_isc' => $row->percentage_isc,
                        ];
                    });

        return [
            'success' => true,
            'data' => array('items' => $items)
        ];
    }

    public function searchCustomers(Request $request)
    {

        $identity_document_type_id = $this->getIdentityDocumentTypeId($request->document_type_id);

        $customers = Person::where('name', 'like', "%{$request->input}%" )
                            ->orWhere('number','like', "%{$request->input}%")
                            ->whereType('customers')
                            ->whereIn('identity_document_type_id', $identity_document_type_id)
                            ->orderBy('id')
                            ->get()
                            ->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->number.' - '.$row->name,
                                    'name' => $row->name,
                                    'number' => $row->number,
                                    'identity_document_type_id' => $row->identity_document_type_id,
                                    'identity_document_type_code' => $row->identity_document_type->code,
                                    'address' => $row->address,
                                    'telephone' => $row->telephone,
                                    'email' => $row->email,
                                    'country_id' => $row->country_id,
                                    'district_id' => $row->district_id,
                                    'selected' => false
                                ];
                            });

        return [
            'success' => true,
            'data' => array('customers' => $customers)
        ];
    }


    public function getIdentityDocumentTypeId($document_type_id){

        return ($document_type_id == '01') ? [6] : [1,4,6,7,0];

    }

    public function report()
    {
        $request = [
            'customer_id' => null,
            'date_end' => date('Y-m-d'),
            'date_start' => date('Y-m-d'),
            'enabled_expense' => null,
            'enabled_move_item' => false,
            'enabled_transaction_customer' => false,
            'establishment_id' => 1,
            'item_id' => null,
            'month_end' => date('Y-m'),
            'month_start' => date('Y-m'),
            'period' => 'month',
        ];

        return [
            'data' => (new DashboardData())->data_mobile($request)
        ];
    }

    public function updateItem(ItemUpdateRequest $request, $itemId)
    {
        $row = Item::findOrFail($itemId);

        $row->fill($request->only('internal_id', 'barcode', 'model', 'has_igv', 'description', 'sale_unit_price', 'stock_min', 'item_code'));
        $row->save();

        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

        return [
            'success' => true,
            'msg' => 'Producto editado con éxito',
            'data' => (object)[
                'id' => $row->id,
                'item_id' => $row->id,
                'name' => $row->name,
                'full_description' => $full_description,
                'description' => $row->description,
                'currency_type_id' => $row->currency_type_id,
                'internal_id' => $row->internal_id,
                'item_code' => $row->item_code,
                'currency_type_symbol' => $row->currency_type->symbol,
                'sale_unit_price' => number_format( $row->sale_unit_price, 2),
                'purchase_unit_price' => $row->purchase_unit_price,
                'unit_type_id' => $row->unit_type_id,
                'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                'calculate_quantity' => (bool) $row->calculate_quantity,
                'has_igv' => (bool) $row->has_igv,
                'is_set' => (bool) $row->is_set,
                'aux_quantity' => 1,
            ],
        ];
    }

    //subir imagen app
    public function upload(Request $request)
    {

        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file', 'jpg,jpeg,png,gif,svg');

        if(!$validate_upload['success']){
            return $validate_upload;
        }

        if ($request->hasFile('file')) {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type'),
            ];

            return $this->upload_image($new_request);
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }


    function upload_image($request)
    {
        $file = $request['file'];
        $type = $request['type'];

        $temp = tempnam(sys_get_temp_dir(), $type);
        file_put_contents($temp, file_get_contents($file));

        $mime = mime_content_type($temp);
        $data = file_get_contents($temp);

        return [
            'success' => true,
            'data' => [
                'filename' => $file->getClientOriginalName(),
                'temp_path' => $temp,
                'temp_image' => 'data:' . $mime . ';base64,' . base64_encode($data)
            ]
        ];
    }

    public function suppliers()
    {
        $suppliers = Person::whereType('suppliers')->orderBy('name')->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'address' => $row->address,
                'email' => $row->email,
                'selected' => false
            ];
        });

        return [
            'success' => true,
            'customers' => $suppliers
        ];
    }


    //===========================modificaciones============================================

    //================CONDUCTORES
    public function conductores(){
        $conductores = (new DriverController())->getOptions();
        return [
            'success' => true,
            'data_conductor' =>$conductores
        ];
    }

    public function guardarConductor(DriverRequest $request){
        $row = (new DriverController())->store($request);
        return $row;
    }

    public function eliminarConductor(Request $request){
        $row = (new DriverController())->destroy($request->id);
        return $row;
    }

    //==================VEHICULOS
    public function vehiculos(){
        $vehiculos = (new TransportController())->getOptions();
        return [
            'success' => true,
            'data_vehiculo' =>$vehiculos
        ];
    }

    public function guardarVehiculo(TransportRequest $request){
        $row = (new TransportController())->store($request);
        return $row;
    }

    public function eliminarVehiculo(Request $request){
        $row = (new TransportController())->destroy($request->id);
        return $row;
    }

    //==================TRANSPORTISTAS
    public function transportistas(){
        $transportistas = (new DispatcherController())->getOptions();
        return [
            'success' => true,
            'data_transportista' =>$transportistas
        ];
    }

    public function guardarTransportista(DispatcherRequest $request){
        $row = (new DispatcherController())->store($request);
        return $row;
    }

    public function eliminarTransportista(Request $request){
        $row = (new DispatcherController())->destroy($request->id);
        return $row;
    }

    //==================GUIA DE REMISIÓN REMITENTE

    public function getClienteByNumber(Request $request){
        $num_doc = $request->num_doc;
        $cliente = Person::where("number",$num_doc)->whereType('customers')->first();

        if($cliente){
            return [
                "success" =>true,
                "id" =>$cliente->id,
                "codigoTipoDocumentoIdentidad" =>$cliente->identity_document_type_id,
                "numeroDocumento" =>$cliente->number,
                "apellidosYNombresORazonSocial" =>$cliente->name,
                "nombreComercial" =>$cliente->trade_name,
                "codigoPais" =>$cliente->country_id,
                "ubigeo" =>$cliente->district_id,
                "direccion" =>$cliente->address,
                "correoElectronico" =>$cliente->email,
                "telefono" =>$cliente->telephone,
            ];
        }else{
            return [
                "success" =>false,
                "apellidosYNombresORazonSocial"=>"Sin resultados"
            ];
        }
    }
    public function storeGuiaRemitente(Request $request)
    {
        $company = Company::query()
            ->select('soap_type_id')
            ->first();
        $configuration = Configuration::first();
        $res = [];
        if ($request->series[0] == 'T') {
            /** @var Facturalo $fact */
            $fact = DB::connection('tenant')->transaction(function () use ($request, $configuration) {
                $facturalo = new Facturalo();
                $facturalo->save($request->all());
                $document = $facturalo->getDocument();
                $data = (new ServiceDispatchController())->getData($document->id);
                $facturalo->setXmlUnsigned((new ServiceDispatchController())->createXmlUnsigned($data));
                $facturalo->signXmlUnsigned();
//                $facturalo->createXmlUnsigned();
//                $facturalo->signXmlUnsigned();
                $facturalo->createPdf();
//                if($configuration->isAutoSendDispatchsToSunat()) {
//                     $facturalo->senderXmlSignedBill();
//                }
                return $facturalo;
            });

            $document = $fact->getDocument();
//            if ($company->soap_type_id === '02') {
//                $res = ((new ServiceDispatchController())->send($document->external_id));
//            }
            // $response = $fact->getResponse();
        } else {
            /** @var Facturalo $fact */
            $fact = DB::connection('tenant')->transaction(function () use ($request) {
                $facturalo = new Facturalo();
                $facturalo->save($request->all());
                $facturalo->createPdf();

                return $facturalo;
            });

            $document = $fact->getDocument();
            // $response = $fact->getResponse();
        }

        if (!empty($document->reference_document_id) && $configuration->getUpdateDocumentOnDispaches()) {
            $reference = Document::find($document->reference_document_id);
            if (!empty($reference)) {
                $reference->updatePdfs();
            }
        }

        $message = "Se creo la guía de remisión {$document->series}-{$document->number}";

        return [
            'success' => true,
            'message' => $message,
            'data' => [
                'id' => $document->id,
                'send_sunat' => $configuration->auto_send_dispatchs_to_sunat
            ],
        ];
    }
    public function getAllViewDispatchdata(){
        $series = Series::where('establishment_id', auth()->user()->establishment_id)
                ->whereIn('document_type_id', ['09'])
                ->get()
                ->transform(function($row) {
                    return [
                        "id" =>$row->id,
                        "number"=>$row->number,
                    ];
                });
        $modo_traslado = TransportModeType::get()
                        ->transform(function($row) {
                            return [
                                "id" =>$row->id,
                                "description"=>$row->description,
                            ];
                        });

        $motivo_traslado = TransferReasonType::get()
                        ->transform(function($row) {
                            return [
                                "id" =>$row->id,
                                "description"=>$row->description,
                            ];
                        });
        $unidad_medida = UnitType::whereIn('id', ['KGM', 'TNE'])->get()
            ->transform(function($row) {
                return [
                    "id" =>$row->id,
                    "description"=>$row->description,
                ];
            });

        $transportista = Dispatcher::where('is_active', 1)->get()
            ->transform(function($row) {
                return [
                    "id" =>$row->id,
                    "identity_document_type_id"=>$row->identity_document_type_id,
                    "number"=>$row->number,
                    "name"=>$row->name,
                    "number_mtc"=>$row->number_mtc,
                    "is_default"=>$row->is_default
                ];
            });
        $conductor = Driver::where('is_active', 1)->get()
            ->transform(function($row) {
                return [
                    "id" =>$row->id,
                    "identity_document_type_id"=>$row->identity_document_type_id,
                    "number"=>$row->number,
                    "name"=>$row->name,
                    "is_default"=>$row->is_default
                ];
            });
        $vehiculo = Transport::where('is_active', 1)->get()
            ->transform(function($row) {
                return [
                    "id" =>$row->id,
                    "model"=>$row->model,
                    "brand"=>$row->brand,
                    "plate_number"=>$row->plate_number,
                    "is_default"=>$row->is_default
                ];
            });

        $producto = Item::where('active', 1)->whereNotNull("internal_id")->get()
        ->transform(function($row) {
                return [
                    "id"              => $row->id,
                    "description"     => $row->description,
                    "codigo_interno"  => $row->internal_id,
                    "cantidad"        =>0,
                    "item_id"         => $row->id,
                    "unit_type_id "   => $row->unit_type_id,
                    "isSelected"      =>false
                ];
            });

        return [
            'data' => [
                'success'           => true,
                'series'            =>$series,
                'modo_traslado'     => $modo_traslado,
                'motivo_traslado'   => $motivo_traslado,
                'unidad_medida'     => $unidad_medida,
                'transportista'     => $transportista,
                'conductor'         => $conductor,
                'vehiculo'          => $vehiculo,
                'producto'          => $producto
            ]
        ];
    }
    //==================GUIA DE REMISIÓN TRANSPORTISTA
    public function getSeriesDispatchCarrier()
    {

        return Series::where('establishment_id', auth()->user()->establishment_id)
            ->whereIn('document_type_id', ['31'])
            ->get()
            ->transform(function($row) {
                return $row->getApiRowResource();
            });

    }

}

