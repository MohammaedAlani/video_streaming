<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function upload(Request $request) {
        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            $fileSaved = $this->saveFile($save->getFile());

            Video::create([
                'title' => $save->getFile()->getClientOriginalName(),
                'path' => $fileSaved['path'] . $fileSaved['name'],
                'user_id' => $request->user()->id,
                'description' => 'Video description',
            ]);

            return response()->json($fileSaved);
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    protected function saveFile(UploadedFile $file)
    {
        $fileName = $this->createFilename($file);
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());
        // Group files by the date (week
        $dateFolder = date("Y-m-W");

        // Build the file path
        $filePath = "upload/{$mime}/{$dateFolder}/";
        $finalPath = storage_path("app/".$filePath);

        // move the file name
        $file->move($finalPath, $fileName);

        return [
            'path' => $filePath,
            'name' => $fileName,
            'mime_type' => $mime
        ];
    }

    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }
}
