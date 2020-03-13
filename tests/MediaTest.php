<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\FFMpegStreaming;

use Streaming\DASH;
use Streaming\HLS;
use Streaming\Media;
use FFMpeg\FFProbe\DataMapping\Stream;

class MediaTest extends TestCase
{
    public function testMediaClass()
    {
        $media = $this->getVideo();
        $this->assertInstanceOf(Media::class, $media);
    }

    public function testDASH()
    {
        $this->assertInstanceOf(DASH::class, $this->getDASH());
    }

    public function testHLS()
    {
        $this->assertInstanceOf(HLS::class, $this->getHLS());
    }

    public function testGetPath()
    {
        $media = $this->getVideo();
        $get_path_info = pathinfo($media->getPathfile());

        $this->assertIsArray($get_path_info);
        $this->assertArrayHasKey('dirname', $get_path_info);
        $this->assertArrayHasKey('filename', $get_path_info);
    }

    private function getDASH()
    {
        $media = $this->getVideo();
        return $media->DASH();
    }

    private function getHLS()
    {
        $media = $this->getVideo();
        return $media->HLS();
    }
}