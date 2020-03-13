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


use Streaming\Exception\RuntimeException;

class HLSPlaylist
{
    /** @var HLS */
    private $hls;

    /**
     * HLSPlaylist constructor.
     * @param HLS $hls
     */
    public function __construct(HLS $hls)
    {
        $this->hls = $hls;
    }

    /**
     * @param Representation $rep
     * @return string
     */
    private function segmentPath(Representation $rep): string
    {
        return $this->hls->pathInfo(PATHINFO_FILENAME) . "_" . $rep->getHeight() . "p.m3u8";
    }

    /**
     * @param Representation $rep
     * @return string
     */
    private function streamInfo(Representation $rep): string
    {
        $ext_stream = '#EXT-X-STREAM-INF:';
        $params = [
            "BANDWIDTH=" . $rep->getKiloBitrate() * 1024,
            "RESOLUTION=" . $rep->getResize(),
            "NAME=\"" . $rep->getHeight() . "\""
        ];

        return $ext_stream . implode(",", array_merge($params, $rep->getHlsStreamInfo()));
    }

    /**
     * @return string
     */
    private function getVersion(): string
    {
        $version = $this->hls->getHlsSegmentType() === "fmp4" ? 7 : 3;
        return "#EXT-X-VERSION:" . $version;
    }

    /**
     * @param array $description
     * @return string
     */
    private function contents(array $description): string
    {
        $content = array_merge(["#EXTM3U", $this->getVersion()], $description);

        foreach ($this->hls->getRepresentations() as $rep) {
            array_push($content, $this->streamInfo($rep), $this->segmentPath($rep));
        }

        return implode(PHP_EOL, $content);
    }

    /**
     * @param string $filename
     * @param array $description
     */
    public function save(string $filename, array $description): void
    {
        if (false === @file_put_contents($filename, $this->contents($description))) {
            throw new RuntimeException("Unable to save the master playlist file");
        }
    }
}