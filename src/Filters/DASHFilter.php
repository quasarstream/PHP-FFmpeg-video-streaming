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

class DASHFilter extends StreamFilter
{
    /** @var \Streaming\DASH */
    private $dash;

    /**
     * @param StreamInterface $stream
     */
    public function streamFilter(StreamInterface $stream): void
    {
        $this->dash = $stream;
        $this->set();
    }

    /**
     * @return array
     * @TODO: optimize this function
     */
    private function set()
    {
        $this->filter = $this->getBaseFilters();

        foreach ($this->dash->getRepresentations() as $key => $representation) {
            $this->filter[] = "-map";
            $this->filter[] = "0";
            $this->filter[] = "-b:v:" . $key;
            $this->filter[] = $representation->getKiloBitrate() . "k";
            $this->filter = array_merge($this->filter, $this->getAudioBitrate($representation, $key));

            if (null !== $representation->size2string()) {
                $this->filter[] = "-s:v:" . $key;
                $this->filter[] = $representation->size2string();
            }
        }
        $this->filter = array_merge($this->filter, $this->getFormats());

        if ($this->dash->getAdaption()) {
            $this->filter[] = "-adaptation_sets";
            $this->filter[] = $this->dash->getAdaption();
        }
        $this->filter = array_merge(
            $this->filter,
            Utiles::arrayToFFmpegOpt($this->dash->getAdditionalParams()),
            ["-strict", $this->dash->getStrict()]
        );

        return $this->filter;
    }

    /**
     * @return array
     */
    private function getBaseFilters(): array
    {
        $filename = $this->dash->pathInfo(PATHINFO_FILENAME);

        $this->filter = [
            "-bf", "1",
            "-keyint_min", "120",
            "-g", "120",
            "-sc_threshold", "0",
            "-b_strategy", "0",
            "-use_timeline", "1",
            "-use_template", "1",
            "-init_seg_name", ($filename . '_init_$RepresentationID$.$ext$'),
            "-media_seg_name", ($filename . '_chunk_$RepresentationID$_$Number%05d$.$ext$'),
            "-seg_duration", $this->dash->getSegDuration(),
            "-hls_playlist", (int)$this->dash->isGenerateHlsPlaylist(),
            "-f", "dash",
        ];

        return $this->filter;
    }

    /**
     * @return array
     */
    private function getFormats(): array
    {
        $format = ['-c:v', $this->dash->getFormat()->getVideoCodec()];
        $audio_format = $this->dash->getFormat()->getAudioCodec();

        return $audio_format ? array_merge($format, ['-c:a', $audio_format]) : $format;
    }


    /**
     * @param Representation $rep
     * @param int $key
     * @return array
     */
    private function getAudioBitrate(Representation $rep, int $key): array
    {
        return $rep->getAudioKiloBitrate() ? ["-b:a:" . $key, $rep->getAudioKiloBitrate() . "k"] : [];
    }
}