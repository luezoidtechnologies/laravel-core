<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 1/28/18
 * Time: 10:45 AM
 */

namespace Luezoid\Laravelcore\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Image;
use Intervention\Image\ImageManager;
use Luezoid\Laravelcore\Constants\ErrorConstants;
use Luezoid\Laravelcore\Contracts\IFile;
use Luezoid\Laravelcore\Repositories\FileRepository;
use Ramsey\Uuid\Uuid;

class FileController extends ApiController
{

    protected $repository = FileRepository::class;
    private $fileService;

    public function __construct(IFile $fileService)
    {
        parent::__construct();
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $validation = "";

        if (in_array($request->get('type'), array_keys(config('file.types'))) && config('file.types')[$request->get('type')] && isset(config('file.types')[$request->get('type')]['validation'])) {
            $validation = config('file.types')[$request->get('type')]['validation'];
        }

        $validator = [
            'type' => ['required', Rule::in(array_keys(config('file.types')))]
        ];
        if ($validation) {
            $validator['file'] = $validation;
        }
        $data = $request->all();
        if (isset(config('file.types')[$request->get('type')]['valid_file_types']) && $request->file) {
            $validator['extension'] = ['required', Rule::in(config('file.types')[$request->get('type')]['valid_file_types'])];
            $data['extension'] = strtolower($request->file->getClientOriginalExtension());
        }


        $validator = Validator::make($data, $validator);


        if ($validator->fails()) {
            return $this->standardResponse(null, $validator->errors()->all(), 400);
        }

        $file = $request->file('file');
        $fileType = $request->get('fileType', 'normal');

        $type = $request->get('type');
        if ($fileType == "base64") {

            $imageManager = new ImageManager();
            $base64EncodedImageSource = $request->get('file');

            $imageObject = $imageManager->make($base64EncodedImageSource);

            if ($imageObject->mime == 'image/jpeg')
                $extension = '.jpg';
            elseif ($imageObject->mime == 'image/png')
                $extension = '.png';
            elseif ($imageObject->mime == 'image/gif')
                $extension = '.gif';
            else
                $extension = '';

            $fileName = Uuid::uuid4() . $extension;
            $tempFilePath = public_path(config('file.temp_path')) . '/' . $fileName;

            $imageObject->save($tempFilePath, 100);
            $file = new UploadedFile($tempFilePath, $fileName);
        } else {
            $fileName = $request->get('fileName', $file->getClientOriginalName());
//        TODO: file extension validation
            if (!$request->file('file')->isValid()) {
                Log::error(__('errors.errorInUploadingFile') . $request->file('file')->getError());
                return $this->standardResponse(null, "Invalid File", 400, ErrorConstants::TYPE_VALIDATION_ERROR);
            }
        }


        $output = $this->fileService->save($fileName, $file, $type, config('file.save_uuid_file_name', true));

        return $this->standardResponse($output, "File uploaded successfully");
    }
}