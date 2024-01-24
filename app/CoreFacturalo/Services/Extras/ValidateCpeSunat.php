<?php

namespace App\CoreFacturalo\Services\Extras;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use \DateTime;

class ValidateCpeSunat
{
    const URL_CONSULT = 'https://api.sunat.gob.pe/v1/contribuyente/contribuyentes/20601411076/validarcomprobante';
    const URL_TOKEN = 'https://api-seguridad.sunat.gob.pe/v1/clientesextranet/11d21fcf-2a30-4e98-bd5b-fb56f1e9096f/oauth2/token/';

    protected $client;

    protected $document_state = [
        '0' => '-1', //'NO EXISTE' custom code
        '1' => '05', //'ACEPTADO'
        '2' => '11', //'ANULADO'
    ];

    protected $document_estado = [
        '-' => '-',
        '0' => 'NO EXISTE',
        '1' => 'ACEPTADO',
        '2' => 'ANULADO',
        '3' => 'AUTORIZADO',
        '4' => 'NO AUTORIZADO'
    ];

    protected $company_state = [
        '-' => '-',
        '00' => 'ACTIVO',
        '01' => 'BAJA PROVISIONAL',
        '02' => 'BAJA PROV. POR OFICIO',
        '03' => 'SUSPENSION TEMPORAL',
        '10' => 'BAJA DEFINITIVA',
        '11' => 'BAJA DE OFICIO',
        '12' => 'BAJA MULT.INSCR. Y OTROS ',
        '20' => 'NUM. INTERNO IDENTIF.',
        '21' => 'OTROS OBLIGADOS',
        '22' => 'INHABILITADO-VENT.UNICA',
        '30' => 'ANULACION - ERROR SUNAT   '
    ];

    protected $company_condition = [
        '-' => '-',
        '00' => 'HABIDO',
        '01' => 'NO HALLADO SE MUDO DE DOMICILIO',
        '02' => 'NO HALLADO FALLECIO',
        '03' => 'NO HALLADO NO EXISTE DOMICILIO',
        '04' => 'NO HALLADO CERRADO',
        '05' => 'NO HALLADO NRO.PUERTA NO EXISTE',
        '06' => 'NO HALLADO DESTINATARIO DESCONOCIDO',
        '07' => 'NO HALLADO RECHAZADO',
        '08' => 'NO HALLADO OTROS MOTIVOS',
        '09' => 'PENDIENTE',
        '10' => 'NO APLICABLE',
        '11' => 'POR VERIFICAR',
        '12' => 'NO HABIDO',
        '20' => 'NO HALLADO',
        '21' => 'NO EXISTE LA DIRECCION DECLARADA',
        '22' => 'DOMICILIO CERRADO',
        '23' => 'NEGATIVA RECEPCION X PERSONA CAPAZ',
        '24' => 'AUSENCIA DE PERSONA CAPAZ',
        '25' => 'NO APLICABLE X TRAMITE DE REVERSION',
        '40' => 'DEVUELTO'
    ];

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::URL_CONSULT,
            'defaults' => [
                'exceptions' => false,
                'allow_redirects' => false
            ]
        ]);
        $this->client = new Client(['cookies' => true]);
    }

    public function search($company_number, $document_type_id, $series, $number, $date_of_issue, $total)
    {
        $token ="";
        if(Session::has('token_time') && Session::has('token_key')){
            $tiempoOld = Session::get('token_time');
            $tiempo = date("H:i:s");

            $fechaUno=new DateTime($tiempoOld);
            $fechaDos=new DateTime($tiempo);

            $dateInterval = $fechaUno->diff($fechaDos);
            $min=$dateInterval->format('%i');

            if($min<30) {
                $token = Session::get('token_key');
            }
            else{
                $token = trim($this->getToken());
            }
        }

        else{
            $token = trim($this->getToken());
        }

        try {

            $response = $this->client->request('POST', self::URL_CONSULT, [
                'http_errors' => true,
                'headers' => [
                    'Accept' =>'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization'=> 'Bearer '. $token
                ],
                'json' =>[
                    "numRuc" => $company_number,
                    "codComp" => $document_type_id,
                    "numeroSerie" => $series,
                    "numero" => $number,
                    "fechaEmision" => $date_of_issue,
                    "monto" =>$total
                ]
            ]);

            if($response->getStatusCode() == 200) {
                $text =  $response->getBody()->getContents();
                $datos = json_decode($text,true);
                return [
                    'success' => true,
                    'response' => "Ok ".$response->getBody()->getContents(),
                    'data' => [
                        'comprobante_estado_codigo' => $datos['data']['estadoCp'],
                        'comprobante_estado_descripcion' => $this->document_state[$datos['data']['estadoCp']],
                        'state_type_id'=> $datos['data']['estadoCp'],
                        'message'=> $this->document_state[$datos['data']['estadoCp']],
                        // 'empresa_estado_codigo' => $response->data->estadoRuc,
                        // 'empresa_estado_description' => $this->company_state[$response->data->estadoRuc],
                        // 'empresa_condicion_codigo' => $response->data->condDomiRuc,
                        // 'empresa_condicion_descripcion' => $this->company_condition[$response->data->condDomiRuc],
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Error ".$response->getBody()->getContents()
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
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

        $tiempo = date("H:i:s");

        Session::put('token_key', $data->access_token);
        Session::put('token_time', $tiempo);

        return $data->access_token;
    }

    public function validateAndChangeDocuments($month, $year)
    {
        $company = Company::first();
        for ($i = $month; $i <= $month; $i++) {
            $date = Carbon::createFromDate($year, $i, 1);
            $date_from = $date->format('Y-m-d');
            $date_to = $date->endOfMonth()->format('Y-m-d');
            $documents = Document::where('state_type_id', '01')
                ->where('soap_type_id', '02')
                ->where('document_type_id', '03')
                ->where('series', 'B146')
                ->whereBetween('date_of_issue', [$date_from, $date_to])
                ->orderBy('number')
                ->get();
            Log::info('-------------------------------------------------');
            Log::info('Periodo: ' . $date_from . ' al ' . $date_to);
            Log::info('Documentos:' . count($documents));
            foreach ($documents as $document) {
                reValidate:
                sleep(2);
                $response = $this->search($company->number,
                    $document->document_type_id, $document->series, $document->number,
                    $document->date_of_issue->format('Y-m-d'), $document->total);
                if ($response['success']) {
                    Log::info($document->series . '-' . $document->number . '|' . 'Mensaje: ' . $response['data']['comprobante_estado_descripcion']);
                    if ($response['data']['comprobante_estado_codigo'] === '1') {
                        $document->update([
                            'state_type_id' => '05'
                        ]);
                    }
                } else {
                    //Log::info($document->series.'-'.$document->number.'|'.'Mensaje: '.$response['message']);
                    goto reValidate;
                }
            }
            Log::info('-------------------------------------------------');
        }
    }

}
