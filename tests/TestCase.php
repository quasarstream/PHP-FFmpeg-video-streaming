<?php

namespace Tests\FFMpegStreaming;

use Streaming\FFMpeg;
use Streaming\FFMpegInstance;
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
