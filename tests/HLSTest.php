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

use Streaming\Format\StreamFormat;
use Streaming\HLS;
use Streaming\Stream;
use Streaming\Representation;

class HLSTest extends TestCase
{
    public function testHLSClass()
    {
        $this->assertInstanceOf(Stream::class, $this->getHLS());
    }

    public function testFormat()
    {
        $hls = $this->getHLS();
        $hls->X264();

        $this->assertInstanceOf(StreamFormat::class, $hls->getFormat());
    }

    public function testAutoRepresentations()
    {
        $hls = $this->getHLS();
        $hls->X264()
            ->autoGenerateRepresentations();

        $representations = $hls->getRepresentations()->all();

        $this->assertIsArray($representations);
        $this->assertInstanceOf(Representation::class, current($representations));

        $this->assertEquals('256x144', $representations[0]->size2string());
        $this->assertEquals('426x240', $representations[1]->size2string());
        $this->assertEquals('640x360', $representations[2]->size2string());

        $this->assertEquals(103, $representations[0]->getKiloBitrate());
        $this->assertEquals(138, $representations[1]->getKiloBitrate());
        $this->assertEquals(207, $representations[2]->getKiloBitrate());
    }

    public function testSetHlsTime()
    {
        $hls = $this->getHLS();
        $hls->setHlsTime(10);

        $this->assertEquals(10, $hls->getHlsTime());
    }

    public function testSetHlsAllowCache()
    {
        $hls = $this->getHLS();
        $hls->setHlsAllowCache(false);

        $this->assertFalse($hls->isHlsAllowCache());
    }

    public function testSave()
    {
        $rep_1 = (new Representation())->setKiloBitrate(200)->setResize(640, 360);
        $rep_2 = (new Representation())->setKiloBitrate(100)->setResize(480, 270);

        $hls = $this->getHLS();
        $hls->X264()
            ->addRepresentations([$rep_1, $rep_2])
            ->save($this->srcDir . '/hls/test.m3u8');

        $this->assertInstanceOf(Representation::class, $rep_1);
        $this->assertEquals($rep_1->getKiloBitrate(), 200);
        $this->assertEquals($rep_2->size2string(), "480x270");
        $this->assertFileExists($this->srcDir . '/hls/test.m3u8');
    }

    public function testEncryptedHLS()
    {
        $this->creatKeyInfoFile();

        $hls = $this->getHLS();
        $hls->X264()
            ->setHlsKeyInfoFile($this->srcDir . '/enc.keyinfo')
            ->autoGenerateRepresentations()
            ->save($this->srcDir . '/enc_hls/test.m3u8');


        $this->assertFileExists($this->srcDir . '/enc_hls/test.m3u8');
    }

    public function testRandomEncryptedHLS()
    {
        $url = "https://www.aminyazdanpanah.com/keys/test.key";
        $path = $this->srcDir . DIRECTORY_SEPARATOR . "test2.key";

        $hls = $this->getHLS();
        $hls->encryption($path, $url)
            ->X264()
            ->autoGenerateRepresentations()
            ->save($this->srcDir . '/enc_random_hls/test.m3u8');

        $this->assertFileExists($this->srcDir . '/enc_random_hls/test.m3u8');
    }

    private function getHLS()
    {
        return new HLS($this->getVideo());
    }

    private function creatKeyInfoFile()
    {
        $contents[] = 'http://www.aminyazdanpanah.com/key/enc.key';
        $contents[] = $this->srcDir . '/enc.key';
        $contents[] = '17e15e333e4347e31017c5415adde26f';

        file_put_contents($this->srcDir . '/enc.keyinfo', implode(PHP_EOL, $contents));
    }
}
