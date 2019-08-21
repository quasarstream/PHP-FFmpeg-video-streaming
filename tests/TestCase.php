<?php

namespace Tests\FFMpegStreaming;

use Streaming\FFMpeg;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public $srcDir;

    public function setUp(): void
    {
        $this->srcDir = __DIR__ . '/files';
    }

    public function getFFMpeg(): FFMpeg
    {
        return FFMpeg::create();
    }

    public function getVideo()
    {
        $service = $this->getFFMpeg();
        return $service->open($this->srcDir . '/test.mp4');
    }
}
