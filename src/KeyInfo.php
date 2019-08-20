<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming;


use Streaming\Process\Process;

class KeyInfo
{
    private $url;

    private $path;

    private $path_info;

    private $openssl;


    /**
     * GenerateKeyInfo constructor.
     * @param $url
     * @param $path
     * @param string $binary
     * @throws Exception\RuntimeException
     */
    public function __construct(string $url, string $path, $binary = "openssl")
    {
        $this->url = $url;
        $this->path = $path;
        $this->path_info = pathinfo($path);
        $this->openssl = new Process($binary);
    }

    /**
     * @throws Exception\Exception
     */
    public function generate(): string
    {
        $key_info[] = $this->url;
        $key_info[] = $this->generateRandomKey();
        $key_info[] = $this->generateIV();

        file_put_contents($path = FileManager::tmpFile(), implode(PHP_EOL, $key_info));

        return $path;
    }

    /**
     * @return string
     * @throws Exception\Exception
     */
    public function __toString(): string
    {
        return $this->generate();
    }

    /**
     * @return string
     * @throws Exception\Exception
     */
    private function generateRandomKey(): string
    {
        FileManager::makeDir($this->path_info["dirname"]);
        file_put_contents($this->path, $this->openssl->addCommand(['rand', '16'])->run());

        return $this->path;
    }

    /**
     * @return string
     * @throws Exception\RuntimeException
     */
    private function generateIV(): string
    {
        return $this->openssl->reset()->addCommand(['rand', '-hex', '16'])->run();
    }
}