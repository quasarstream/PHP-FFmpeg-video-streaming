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

use Streaming\FileManager;

class FileManagerTest extends TestCase
{
    public function testKeyInfoClass()
    {
        $this->assertInstanceOf(FileManager::class, $this->fileManager());
    }

    public function testDownloadFile()
    {
        $download_path = $this->srcDir . DIRECTORY_SEPARATOR . "downloaded_file.mp4";
        $this->fileManager()->downloadFile($download_path);

        $this->assertFileExists($download_path);
    }

    public function testMakeDir()
    {
        $path = $this->srcDir . DIRECTORY_SEPARATOR . "test_make_dir";
        FileManager::makeDir($path);

        $this->assertDirectoryExists($path);
    }

    public function testTmp()
    {
        $tmp_file = FileManager::tmpFile();
        $tmp_dir = FileManager::tmpDir();

        $this->assertIsString($tmp_file);
        $this->assertIsString($tmp_dir);
    }

    private function fileManager()
    {
        $url = 'https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/tests/files/test.mp4?raw=true';
        return new FileManager($url);
    }
}
