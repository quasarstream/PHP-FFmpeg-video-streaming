<?php

namespace Tests\FFMpegStreaming;

use AYazdanpanah\FFMpegStreaming\FFMpeg;
use AYazdanpanah\FFMpegStreaming\FFMpegInstance;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public $srcDir;

    public function setUp()
    {
        $this->srcDir = __DIR__ . '/files';
    }

    public function getFFMpeg(): FFMpegInstance
    {
        return FFMpeg::create();
    }

    public function getVideo()
    {
        $service = $this->getFFMpeg();
        return $service->open($this->srcDir .'/test.mp4');
    }

}
