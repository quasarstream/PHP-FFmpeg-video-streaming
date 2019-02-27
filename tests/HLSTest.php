<?php

/**
 * Copyright 2019 Amin Yazdanpanah<http://www.aminyazdanpanah.com>.
 *
 * Licensed under the MIT License;
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://opensource.org/licenses/MIT
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Tests\FFMpegStreaming;

use AYazdanpanah\FFMpegStreaming\HLS;
use AYazdanpanah\FFMpegStreaming\Export;
use AYazdanpanah\FFMpegStreaming\Format\Video;
use AYazdanpanah\FFMpegStreaming\Representation;
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

        $this->assertEquals(237, $representations[0]->getKiloBitrate());
        $this->assertEquals(292, $representations[1]->getKiloBitrate());
        $this->assertEquals(380, $representations[2]->getKiloBitrate());
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
        $hls = $this->getHLS();
        $hls->X264()
            ->autoGenerateRepresentations()
            ->save($this->srcDir . '/hls/test.m3u8');

        $get_path_info = $hls->getPathInfo();

        $this->assertFileExists($this->srcDir . '/hls/test.m3u8');
        $this->assertIsArray($get_path_info);
        $this->assertArrayHasKey('dirname', $get_path_info);
        $this->assertArrayHasKey('filename', $get_path_info);

        sleep(1);
        $this->deleteDirectory($this->srcDir . '/hls');
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

        sleep(1);
        $this->deleteDirectory($this->srcDir . '/enc_hls');
        @unlink($this->srcDir . '/enc.keyinfo');
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

    public function deleteDirectory($dir)
    {
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

    private function creatKeyInfoFile()
    {
        $contents[] = 'http://www.aminyazdanpanah.com/key/enc.key';
        $contents[] = $this->srcDir . '/enc.key';
        $contents[] = '17e15e333e4347e31017c5415adde26f';

        file_put_contents($this->srcDir . '/enc.keyinfo', implode(PHP_EOL, $contents));
    }
}
