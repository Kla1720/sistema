<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SearchRequest;
use App\Http\Resources\Tenant\SearchResource;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\Document;
use App\Models\Tenant\Person;
use Exception;

class SearchController extends Controller
{
    public function index()
    {
        return view('tenant.search.index');
    }

    public function tables()
    {
        $document_types = DocumentType::whereIn('id', ['01', '03', '07', '08','09','31'])->get();

        return compact('document_types');
    }

    public function store(SearchRequest $request)
    {
        $type_doc=$request->input('document_type_id');

        $customer = Person::where('number', $request->input('customer_number'))
            ->where('type', 'customers')
            ->first();

        if (!$customer) {
            throw new Exception('El número del cliente ingresado no se encontró en la base de datos.');
        }

        if($type_doc=='09'){// guia remitente
            $document = Dispatch::where('date_of_issue', $request->input('date_of_issue'))
                ->where('document_type_id', $request->input('document_type_id'))
                ->where('series', strtoupper($request->input('series')))
                ->where('number', (int) $request->input('number'))
                ->where('customer_id', $customer->id)
                ->first();
        }
        elseif($type_doc ==="31"){//guia transportista
            $document = Dispatch::where('date_of_issue', $request->input('date_of_issue'))
                ->where('document_type_id', $request->input('document_type_id'))
                ->where('series', strtoupper($request->input('series')))
                ->where('number', (int) $request->input('number'))
                ->where('sender_id', $customer->id)
                ->first();
        }else{
            $document = Document::where('date_of_issue', $request->input('date_of_issue'))
                ->where('document_type_id', $request->input('document_type_id'))
                ->where('series', strtoupper($request->input('series')))
                ->where('number', (int) $request->input('number'))
                ->where('total', $request->input('total'))
                ->where('customer_id', $customer->id)
                ->first();
        }

        if ($document) {
            return [
                'success' => true,
                'data' => new SearchResource($document)
            ];
        } else {
            return [
                'success' => false,
                'message' => 'El documento no fue encontrado.'
            ];
        }
    }
}
