<?php


namespace Miladimos\FileManager\Http\Controllers;


use Illuminate\Http\Request;
use Miladimos\FileManager\Services\UploadService;

class UploadController extends Controller
{
    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function uploadFile(Request $request)
    {
        $file = $request->file('file');
        return $this->uploadService->uploadFile($file, 1);
    }

    public function uploadFiles(Request $request)
    {
        //
    }
}
