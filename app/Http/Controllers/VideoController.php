<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoCollection;
use App\Models\Party;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    
    public function index()
    {
        return Video::orderBy('id', 'DESC')->get();
    }

    public function showVideo($id)
    {
        $video = Video::findOrFail($id);

        //TODO: check file path after uploading videos
        $file_path = $video->path;
        if (!Storage::exists($file_path)) {
            return response()->json(["message" => "Video Not Found"], Response::HTTP_NOT_FOUND);
        }

        $headers = [
            'Content-Type' => 'video/mp4', // Adjust the content type based on your video format
        ];

        return new StreamedResponse(function () use ($file_path) {
            $stream = Storage::readStream($file_path);
            fpassthru($stream);
            fclose($stream);
        }, Response::HTTP_OK, $headers);
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

        //TODO: check
        $image = $request->file('image')->store('images');

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            $fileSaved = $this->saveFile($save->getFile());

            Video::create([
                'title' => $save->getFile()->getClientOriginalName(),
                'path' => $fileSaved['path'] . $fileSaved['name'],
                'user_id' => $request->user()->id,
                'description' => 'Video description',
                'image' => $image,
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
        $finalPath = public_path($filePath);

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

    public function show_videos($id)
    {
        $user_id = 1;
        $video = Video::find($id);
        $party = Party::where('video_id', $id)->where('user_id', $user_id)->first();
        return [
            'video' => $video,
            'party' => $party
        ];
    }
}
