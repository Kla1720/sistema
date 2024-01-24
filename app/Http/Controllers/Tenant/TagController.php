<?php
namespace App\Http\Controllers\Tenant;

use App\Imports\ItemsImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\Tenant\TagRequest;
use App\Http\Resources\Tenant\TagCollection;
use App\Http\Resources\Tenant\TagResource;
use Exception;
use Illuminate\Http\Request;
use App\Models\Tenant\ItemTag;
use App\Models\Tenant\Catalogs\Tag;
use Modules\Finance\Helpers\UploadFileHelper;


class TagController extends Controller
{
    public function index()
    {
        return view('tenant.tags.index');
    }


    public function columns()
    {
        return [
            'description' => 'Nombre'
            // 'description' => 'Descripción'
        ];
    }

    public function records(Request $request)
    {
        $records = Tag::where($request->column, 'like', "%{$request->value}%")->orderBy('description');

        return new TagCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function create()
    {
        return view('tenant.items.form');
    }


    public function record($id)
    {
        $record = new TagResource(Tag::findOrFail($id));

        return $record;
    }

    public function store(TagRequest $request) {
        $id = $request->input('id');
        $person = Tag::firstOrNew(['id' => $id]);
        $person->fill($request->all());

        $temp_path = $request->input('temp_path');
        if($temp_path) {

            $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'tags'.DIRECTORY_SEPARATOR;
            $file_name_old = $request->input('image');
            $file_name_old_array = explode('.', $file_name_old);
            $file_content = file_get_contents($temp_path);
            $datenow = date('YmdHis');
            $file_name = Str::slug($person->description).'-'.$datenow.'.'.$file_name_old_array[1];
            Storage::put($directory.$file_name, $file_content);
            $person->image = $file_name;

        }else if(!$request->input('image') && !$request->input('temp_path') && !$request->input('image_url')){
            $person->image = 'imagen-no-disponible.jpg';
        }

        $person->save();

        return [
            'success' => true,
            'message' => ($id)?'Tag editado con éxito':'Tag registrado con éxito',
            'id' => $person->id
        ];
    }

    public function destroy($id)
    {
        //return 'sd';
        $item = Tag::findOrFail($id);
       // $this->deleteRecordInitialKardex($item);
        $item->status = 0;
        $item->save();

       // $item->delete();

        return [
            'success' => true,
            'message' => 'Tag eliminado con éxito'
        ];
    }
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


}
