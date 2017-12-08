<?php

namespace AminYazdanpanah\HLSExporter;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

class HLSExporter
{
    protected $ffmpeg;

    protected $ffprobe;

    /**
     * HLSExporter constructor.
     * @param FFMpeg $ffmpeg
     * @param FFProbe $ffprobe
     */
    public function __construct(FFMpeg $ffmpeg, FFProbe $ffprobe)
    {
        $this->ffmpeg = $ffmpeg;
        $this->ffprobe = $ffprobe;
    }

    public static function open($path)
    {
        $ffmpeg = FFMpeg::create();
        $ffprobe = FFProbe::create();

        return (new self($ffmpeg,$ffprobe))->create($path);
    }

    private function create($path)
    {
         $ffmpeg = $this->ffmpeg->open($path);
         $ffprobe = $this->ffprobe->streams($path);

        return new HLSCreator($ffmpeg,$ffprobe);
    }


}