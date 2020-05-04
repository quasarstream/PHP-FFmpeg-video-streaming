<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming\Filters;


use Streaming\StreamInterface;
use Streaming\Representation;
use Streaming\Utiles;

class DASHFilter extends FormatFilter
{
    /** @var \Streaming\DASH */
    private $dash;

    /**
     * @param Representation $rep
     * @param int $key
     * @return array
     */
    private function getAudioBitrate(Representation $rep, int $key): array
    {
        return $rep->getAudioKiloBitrate() ? ["-b:a:" . $key, $rep->getAudioKiloBitrate() . "k"] : [];
    }

    /**
     * @return array
     */
    private function streams(): array
    {
        $streams = [];
        foreach ($this->dash->getRepresentations() as $key => $rep) {
            $streams = array_merge(
                $streams,
                Utiles::arrayToFFmpegOpt([
                    'map'       => 0,
                    "s:v:$key"  => $rep->size2string(),
                    "b:v:$key"  => $rep->getKiloBitrate() . "k"
                ]),
                $this->getAudioBitrate($rep, $key)
            );
        }

        return $streams;
    }

    /**
     * @return array
     */
    private function getAdaptions(): array
    {
        return $this->dash->getAdaption() ? ['-adaptation_sets', $this->dash->getAdaption()] : [];
    }

    /**
     * @return array
     */
    private function init(): array
    {
        $name = $this->dash->pathInfo(PATHINFO_FILENAME);

        $init = [
            "use_timeline"      => 1,
            "use_template"      => 1,
            "init_seg_name"     => $name . '_init_$RepresentationID$.$ext$',
            "media_seg_name"    => $name . '_chunk_$RepresentationID$_$Number%05d$.$ext$',
            "seg_duration"      => $this->dash->getSegDuration(),
            "hls_playlist"      => (int)$this->dash->isGenerateHlsPlaylist(),
            "f"                 => "dash",
        ];

        return array_merge(
            Utiles::arrayToFFmpegOpt($init),
            $this->getAdaptions(),
            Utiles::arrayToFFmpegOpt($this->dash->getAdditionalParams())
        );
    }

    /**
     * @return array
     */
    private function getArgs(): array
    {
        return array_merge(
            $this->init(),
            $this->streams(),
            ['-strict', $this->dash->getStrict()]
        );
    }

    /**
     * @param StreamInterface $dash
     */
    public function streamFilter(StreamInterface $dash): void
    {
        $this->dash = $dash;

        $this->filter = array_merge(
            $this->getFormatOptions($dash->getFormat()),
            $this->getArgs()
        );
    }
}