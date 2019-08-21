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

use Streaming\KeyInfo;

class KeyInfoTest extends TestCase
{
    public function testKeyInfoClass()
    {
        $this->assertInstanceOf(KeyInfo::class, $this->initKeyInfo());
    }

    public function testGenerate()
    {
        $this->assertFileExists($this->initKeyInfo()->generate());
    }

    private function initKeyInfo()
    {
        $url = "https://www.aminyazdanpanah.com/keys/test.key";
        $path = $this->srcDir . DIRECTORY_SEPARATOR . "test.key";

        return new KeyInfo($url, $path);
    }
}
