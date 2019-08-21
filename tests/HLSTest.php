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

use Streaming\HLS;
use Streaming\Export;
use Streaming\Format\Video;
use Streaming\Representation;
use ReflectionClass;

class HLSTest extends TestCase
{
    public function testHLSClass()
    {
        $this->assertInstanceOf(Export::class, $this->getHLS());
    }

    public function testFilter()
    {
        $this->assertNotNull($this->getHLSMethod('setFilter'));
    }

    public function testFormat()
    {
        $hls = $this->getHLS();
        $hls->X264();

        $this->assertInstanceOf(Video::class, $hls->getFormat());
    }

    public function testAutoRepresentations()
    {
        $hls = $this->getHLS();
        $hls->X264()
            ->autoGenerateRepresentations();

        $representations = $hls->getRepresentations();

        $this->assertIsArray($representations);
        $this->assertInstanceOf(Representation::class, current($representations));

        $this->assertEquals('256x144', $representations[0]->getResize());
        $this->assertEquals('426x240', $representations[1]->getResize());
        $this->assertEquals('640x360', $representations[2]->getResize());

        $this->assertEquals(129, $representations[0]->getKiloBitrate());
        $this->assertEquals(159, $representations[1]->getKiloBitrate());
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
            ->addRepresentation($rep_1)
            ->addRepresentation($rep_2)
            ->save($this->srcDir . '/hls/test.m3u8');

        $get_path_info = $hls->getPathInfo();

        $this->assertInstanceOf(Representation::class, $rep_1);
        $this->assertEquals($rep_1->getKiloBitrate(), 200);
        $this->assertEquals($rep_2->getResize(), "480x270");
        $this->assertFileExists($this->srcDir . '/hls/test.m3u8');
        $this->assertIsArray($get_path_info);
        $this->assertArrayHasKey('dirname', $get_path_info);
        $this->assertArrayHasKey('filename', $get_path_info);
    }

    public function testEncryptedHLS()
    {
        $this->creatKeyInfoFile();

        $hls = $this->getHLS();
        $hls->X264()
            ->setHlsKeyInfoFile($this->srcDir . '/enc.keyinfo')
            ->autoGenerateRepresentations()
            ->save($this->srcDir . '/enc_hls/test.m3u8');

        $get_path_info = $hls->getPathInfo();

        $this->assertFileExists($this->srcDir . '/enc_hls/test.m3u8');
        $this->assertIsArray($get_path_info);
        $this->assertArrayHasKey('dirname', $get_path_info);
        $this->assertArrayHasKey('filename', $get_path_info);
    }

    public function testRandomEncryptedHLS()
    {
        $url = "https://www.aminyazdanpanah.com/keys/test.key";
        $path = $this->srcDir . DIRECTORY_SEPARATOR . "test2.key";

        $hls = $this->getHLS();
        $export_obj = $hls->generateRandomKeyInfo($url, $path)
            ->X264()
            ->autoGenerateRepresentations()
            ->save($this->srcDir . '/enc_random__hls/test.m3u8', false);

        $get_path_info = $hls->getPathInfo();

        $this->assertInstanceOf(Export::class, $export_obj);
        $this->assertFileExists($this->srcDir . '/enc_random__hls/test.m3u8');
        $this->assertIsArray($get_path_info);
        $this->assertArrayHasKey('dirname', $get_path_info);
        $this->assertArrayHasKey('filename', $get_path_info);
    }

    private function getHLS()
    {
        return new HLS($this->getVideo());
    }

    private function getHLSMethod($name)
    {
        try {
            $class = new ReflectionClass(HLS::class);
            $method = $class->getMethod($name);
            $method->setAccessible(true);
            return $method;
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    private function creatKeyInfoFile()
    {
        $contents[] = 'http://www.aminyazdanpanah.com/key/enc.key';
        $contents[] = $this->srcDir . '/enc.key';
        $contents[] = '17e15e333e4347e31017c5415adde26f';

        file_put_contents($this->srcDir . '/enc.keyinfo', implode(PHP_EOL, $contents));
    }
}
