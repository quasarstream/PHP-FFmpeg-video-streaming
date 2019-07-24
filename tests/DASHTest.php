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
use Streaming\Export;
use Streaming\Format\Video;
use Streaming\Representation;
use ReflectionClass;

class DASHTest extends TestCase
{
    public function testDASHClass()
    {
        $this->assertInstanceOf(Export::class, $this->getDASH());
    }

    public function testFilter()
    {
        $this->assertNotNull($this->getDASHMethod('setFilter'));
    }

    public function testFormat()
    {
        $dash = $this->getDASH();
        $dash->HEVC();

        $this->assertInstanceOf(Video::class, $dash->getFormat());
    }

    public function testAutoRepresentations()
    {
        $dash = $this->getDASH();
        $dash->HEVC()
            ->autoGenerateRepresentations();
        $representations = $dash->getRepresentations();

        $this->assertIsArray($representations);
        $this->assertInstanceOf(Representation::class, current($representations));

        $this->assertEquals('256x144', $representations[0]->getResize());
        $this->assertEquals('426x240', $representations[1]->getResize());
        $this->assertEquals('640x360', $representations[2]->getResize());

        $this->assertEquals(237, $representations[0]->getKiloBitrate());
        $this->assertEquals(292, $representations[1]->getKiloBitrate());
        $this->assertEquals(380, $representations[2]->getKiloBitrate());

    }

    public function testSet()
    {
        $dash = $this->getDASH();
        $dash->setAdaption('test-adaption');

        $this->assertEquals('test-adaption', $dash->getAdaption());
    }

    public function testSave()
    {
        $dash = $this->getDASH();
        $dash->HEVC()
            ->autoGenerateRepresentations()
            ->save($this->srcDir . '/dash/test.mpd');

        $get_path_info = $dash->getPathInfo();

        $this->assertFileExists($this->srcDir . '/dash/test.mpd');
        $this->assertIsArray($get_path_info);
        $this->assertArrayHasKey('dirname',$get_path_info);
        $this->assertArrayHasKey('filename',$get_path_info);

        sleep(1);
        $this->deleteDirectory($this->srcDir . '/dash');
    }

    private function getDASH()
    {
        return new DASH($this->getVideo());
    }

    private function getDASHMethod($name)
    {
        try {
            $class = new ReflectionClass(DASH::class);
            $method = $class->getMethod($name);
            $method->setAccessible(true);
            return $method;
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return @unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return @rmdir($dir);
    }

}
