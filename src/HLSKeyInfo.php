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


use FFMpeg\Driver\FFMpegDriver;
use Streaming\Exception\RuntimeException;

class HLSKeyInfo
{
    /** @var string */
    private $path;

    /** @var string */
    private $c_path;

    /** @var string */
    private $url;

    /** @var string */
    private $c_url;

    /** @var string */
    private $key_info_path;

    /** @var string */
    private $suffix = "";

    /** @var int */
    private $length = 16;

    /** @var array */
    private $segments = [];

    /**
     * HLSKeyInfo constructor.
     * @param string $path
     * @param string $url
     */
    public function __construct(string $path, string $url)
    {
        $this->path = $this->c_path = $path;
        $this->url = $this->c_url = $url;
        File::makeDir(dirname($path));
        $this->key_info_path = File::tmp();
    }

    /**
     * @param string $path
     * @param string $url
     * @return HLSKeyInfo
     */
    public static function create(string $path, string $url): HLSKeyInfo
    {
        return new static($path, $url);
    }

    /**
     * Generate a encryption key
     */
    public function createKey(): void
    {
        if (!extension_loaded('openssl')) {
            throw new RuntimeException('OpenSSL is not installed.');
        }

        File::put($this->path, openssl_random_pseudo_bytes($this->length));
    }

    /**
     * update or generate a key info file
     */
    public function createKeyInfo(): void
    {
        $content = implode(
            PHP_EOL,
            [
                $this->url,
                $this->path,
                bin2hex(openssl_random_pseudo_bytes($this->length))
            ]
        );
        File::put($this->key_info_path, $content);
    }

    /**
     * @param FFMpegDriver $driver
     * @param int $period
     * @param string $needle
     */
    public function rotateKey(FFMpegDriver $driver, int $period, string $needle): void
    {
        call_user_func_array([$driver->listen(new FFMpegListener), 'on'], ['listen', $this->call($needle, $period)]);
    }

    /**
     * @return void
     */
    public function generate(): void
    {
        $this->createKey();
        $this->createKeyInfo();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->generate();
        return $this->key_info_path;
    }

    /**
     * @param string $key_info_path
     * @return HLSKeyInfo
     */
    public function setKeyInfoPath(string $key_info_path): HLSKeyInfo
    {
        $this->key_info_path = $key_info_path;
        return $this;
    }

    /**
     * @param int $length
     * @return HLSKeyInfo
     */
    public function setLength(int $length): HLSKeyInfo
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @param string $suffix
     * @return HLSKeyInfo
     */
    public function setSuffix(string $suffix): HLSKeyInfo
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * update the suffix of paths
     */
    public function updateSuffix(): void
    {
        $suffix = uniqid("_") . $this->suffix;

        $this->path = $this->c_path . $suffix;
        $this->url = $this->c_url . $suffix;
    }

    /**
     * check if a new segment is created or not
     * @param string $needle
     * @param int $period
     * @return callable
     */
    private function call(string $needle, int $period): callable
    {
        return function ($line) use ($needle, $period) {
            if (false !== strpos($line, $needle) && !in_array($line, $this->segments)) {
                array_push($this->segments, $line);
                if (0 === count($this->segments) % $period) {
                    $this->updateSuffix();
                    $this->generate();
                }
            }
        };
    }

    /**
     * @return array
     */
    public function getSegments(): array
    {
        return $this->segments;
    }

    /**
     * @return string
     */
    public function getKeyInfoPath(): string
    {
        return $this->key_info_path;
    }
}
