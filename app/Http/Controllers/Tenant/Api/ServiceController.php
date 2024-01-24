<?php

    namespace App\Http\Controllers\Tenant\Api;

    use App\CoreFacturalo\Helpers\Storage\StorageDocument;
    use App\CoreFacturalo\Services\Dni\Dni;
    use App\CoreFacturalo\Services\Extras\ExchangeRate;
    use App\CoreFacturalo\Services\IntegratedQuery\AuthApi;
    use App\CoreFacturalo\Services\IntegratedQuery\ValidateCpe;
    use App\CoreFacturalo\Services\Ruc\Sunat;
    use App\Http\Controllers\Controller;
    use App\Http\Requests\Tenant\ServiceRequest;
    use App\Models\Tenant\Catalogs\Department;
    use App\Models\Tenant\Catalogs\District;
    use App\Models\Tenant\Catalogs\Province;
    use App\Models\Tenant\Document;
    use Carbon\Carbon;
    use Exception;
    use Illuminate\Http\Request;
    use Modules\ApiPeruDev\Data\ServiceData;
    use Modules\Document\Helpers\ConsultCdr;


    class ServiceController extends Controller
    {


        public const ACCEPTED = '05';
        protected $wsClient;
        use StorageDocument;
        protected $document;
        protected $access_token;

        public function consultCdrStatus(ServiceRequest $request)
        {

            $document_type_id = $request->codigo_tipo_documento;
            $series = $request->serie_documento;
            $number = $request->numero_documento;

            $this->document = Document::where([['soap_type_id', '02'],
                ['document_type_id', $document_type_id],
                ['series', $series],
                ['number', $number]
            ])->first();

            // if(!$this->document)  throw new Exception("Documento no encontrado");
            if (!$this->document) return [
                'success' => false,
                'message' => "Documento no encontrado"
            ];

            return (new ConsultCdr())->search($this->document);

        }


        /**
         * @param int $number
         *
         * @return array
         * @deprecated usar modules/ApiPeruDev/Data/ServiceData.php
         */
        public function ruc($number)
        {
            $service = new Sunat();
            $res = $service->get($number);
            if ($res) {
                $province_id = Province::idByDescription($res->provincia);
                return [
                    'success' => true,
                    'data' => [
                        'name' => $res->razonSocial,
                        'trade_name' => $res->nombreComercial,
                        'address' => $res->direccion,
                        'phone' => implode(' / ', $res->telefonos),
                        'department' => ($res->departamento) ?: 'LIMA',
                        'department_id' => Department::idByDescription($res->departamento),
                        'province' => ($res->provincia) ?: 'LIMA',
                        'province_id' => $province_id,
                        'district' => ($res->distrito) ?: 'LIMA',
                        'district_id' => District::idByDescription($res->distrito, $province_id),
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $service->getError()
                ];
            }
        }


        /**
         * @param int $number
         *
         * @return array
         *
         * @deprecated usar modules/ApiPeruDev/Data/ServiceData.php
         */
        public function dni($number)
        {
            $res = Dni::search($number);

            return $res;
        }

        public function exchangeRateTest($date)
        {
            return (new ServiceData())->exchange($date);
//            $sale = 1;
//            $purchase = 1;
//            if ($date <= now()->format('Y-m-d')) {
//                /**
//                 * @var \App\Models\Tenant\ExchangeRate $ex_rate
//                 * @var \App\Models\Tenant\ExchangeRate $last_ex_rate
//                 */
//                $ex_rate = \App\Models\Tenant\ExchangeRate::where('date', $date)->first();
//                if ($ex_rate) {
//                    $sale = $ex_rate->sale;
//                    $purchase = $ex_rate->purchase;
//                } else {
//                    $exchange_rate = new ExchangeRate();
//                    $res = $exchange_rate->searchDate($date);
//                    if ($res) {
//                        $ex_rate = \App\Models\Tenant\ExchangeRate::create([
//                            'date' => $date,
//                            'date_original' => $res['date_data'],
//                            'purchase' => $res['data']['purchase'],
//                            'purchase_original' => $res['data']['purchase'],
//                            'sale' => $res['data']['sale'],
//                            'sale_original' => $res['data']['sale']
//                        ]);
//                        $sale = $ex_rate->sale;
//                        $purchase = $ex_rate->purchase;
//                    } else {
//                        $last_ex_rate = \App\Models\Tenant\ExchangeRate::orderBy('date', 'desc')->first();
//                        if ($last_ex_rate) {
//                            $sale = $last_ex_rate->sale;
//                            $purchase = $last_ex_rate->purchase;
//                        } else {
//                            $sale = 0;
//                            $purchase = 0;
//                        }
//                    }
//                }
//            }
//            return [
//                'date' => $date,
//                'sale' => $sale,
//                'purchase' => $purchase,
//            ];
        }

        public function documentStatus(Request $request)
        {
            if ($request->has('external_id') or $request->has('serie_number')) {
                $external_id = $request->input('external_id');
                $request_serie = $request->input('serie_number');
                $serie_number = explode('-', $request_serie);
                $serie = $serie_number[0];
                $number = $serie_number[1];

                if (!$external_id) {
                    $document = Document::where('number', $number)
                        ->where('series', $serie)
                        ->first();
                } else {
                    $document = Document::where('external_id', $external_id)
                        ->where('number', $number)
                        ->where('series', $serie)
                        ->first();
                }

                if (!$document) {
                    throw new Exception("El documento con código externo {$external_id} o numero {$request_serie}, no se encuentra registrado.");
                }
                return [
                    'success' => true,
                    'data' => [
                        'number' => $document->number_full,
                        'filename' => $document->filename,
                        'external_id' => $document->external_id,
                        'status_id' => $document->state_type_id,
                        'status' => $document->state_type->description,
                        'qr' => $document->qr,
                        'number_to_letter' => $document->number_to_letter,
                    ],
                    'links' => [
                        'xml' => $document->download_external_xml,
                        'pdf' => $document->download_external_pdf,
                        'cdr' => ($document->download_external_cdr) ? $document->download_external_cdr : '',
                    ],
                ];
            }
        }

        private function getToken()
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientesextranet/11d21fcf-2a30-4e98-bd5b-fb56f1e9096f/oauth2/token/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'grant_type=client_credentials&scope=https%3A%2F%2Fapi.sunat.gob.pe%2Fv1%2Fcontribuyente%2Fcontribuyentes&client_id=11d21fcf-2a30-4e98-bd5b-fb56f1e9096f&client_secret=OhQ25%2FGh55x8CFwsal1FAg%3D%3D',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    //'Cookie: TS019e7fc2=014dc399cbd5a552b1554969aef7c38dfbc4845c762c261502f754f0a263522b6e67201bea8e5a96586307dbe4057ab8b023e4bbca'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response);

            return $data->access_token;
        }

        public function validateCpeSunat(Request $request)
        {
//            $auth_api = (new AuthApi())->getToken();
//            if(!$auth_api['success']) return $auth_api;
            $this->access_token = $this->access_token = $this->getToken();

            $company_number = $request->numero_ruc_emisor;
            $document_type_id = $request->codigo_tipo_documento;
            $series = $request->serie_documento;
            $number = $request->numero_documento;
            $date_of_issue=$request->fecha_de_emision;
            $total = $request->total;

            //$validate_cpe = new ValidateCpe();

            $validate_cpe = new ValidateCpe(
                $this->access_token,
                $company_number,
                $document_type_id,
                $series,
                $number,
                $date_of_issue,
                $total
            );

            $response = $validate_cpe->search();

            if ($response['success']) {

                return [
                    'success' => true,
                    'data' => $response['data']
                ];

            } else {
                return [
                    'success' => false,
                    'data' => $response
                ];
            }

        }


    }
