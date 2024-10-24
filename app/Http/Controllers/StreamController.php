<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Raju\Streamer\Helpers\VideoStream;

class StreamController extends Controller
{
    public function stream($moviename, $filename)
    {

        $videosDir = config('larastreamer.basepath')."/$moviename";

        if (file_exists($filePath = $videosDir."/".$filename)) {
            $stream = new VideoStream($filePath);
            return response()->stream(function() use ($stream) {
                $stream->start();
            });
        }
        return response("File doesn't exists", 404);
    }

    public function streamer()
    {
        return 'Hello from Streamer Package!';
    }
}
