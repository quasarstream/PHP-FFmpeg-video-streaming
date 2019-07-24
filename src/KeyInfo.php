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
     * @throws Exception\Exception
     */
    public function __construct($url, $path, $binary = "openssl")
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

        $key_info_path = $this->path_info["dirname"] . DIRECTORY_SEPARATOR . Helper::randomString() . ".keyinfo";

        file_put_contents($key_info_path, implode(PHP_EOL, $key_info));

        return $key_info_path;
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
        Helper::makeDir($this->path_info["dirname"]);
        file_put_contents($this->path, $this->openssl->addCommand(['rand', '16'])->run());

        return $this->path;
    }

    /**
     * @return string
     * @throws Exception\Exception
     */
    private function generateIV(): string
    {
        return $this->openssl->removeCommand('16')->addCommand(['-hex' ,'16'])->run();
    }
}