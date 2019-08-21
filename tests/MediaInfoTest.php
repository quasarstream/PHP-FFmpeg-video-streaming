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

use Streaming\MediaInfo\Streams\Stream;
use Streaming\MediaInfo\Streams\StreamCollection;

class MediaInfoTest extends TestCase
{
    public function testKeyInfoClass()
    {
        $this->assertInstanceOf(StreamCollection::class, $this->mediaInfo());
    }

    public function testGetGeneral()
    {
        $general = $this->mediaInfo()->general();
        $this->assertInstanceOf(Stream::class, $general);
        $general = $general->all();
        $this->assertArrayHasKey('@type', $general);
        $this->assertEquals($general['@type'], 'General');
    }

    public function testGetFirstVideo()
    {
        $video = $this->mediaInfo()->videos()->first();
        $this->assertInstanceOf(Stream::class, $video);
        $video = $video->all();
        $this->assertArrayHasKey('@type', $video);
        $this->assertEquals($video['@type'], 'Video');
    }

    public function testGetFirstAudio()
    {
        $audio = $this->mediaInfo()->audios()->first();
        $this->assertInstanceOf(Stream::class, $audio);
        $audio = $audio->all();
        $this->assertArrayHasKey('@type', $audio);
        $this->assertEquals($audio['@type'], 'Audio');
    }

    private function mediaInfo()
    {
        return $this->getVideo()->mediaInfo();
    }
}
