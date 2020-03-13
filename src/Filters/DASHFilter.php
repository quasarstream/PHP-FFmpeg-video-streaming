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
use Streaming\Format\X264;
use Streaming\Representation;

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

            if (null !== $representation->getResize()) {
                $this->filter[] = "-s:v:" . $key;
                $this->filter[] = $representation->getResize();
            }
        }

        if ($this->dash->getAdaption()) {
            $this->filter[] = "-adaptation_sets";
            $this->filter[] = $this->dash->getAdaption();
        }
        $this->filter = array_merge($this->filter, $this->dash->getAdditionalParams());
        $this->filter = array_merge($this->filter, ["-strict", $this->dash->getStrict()]);

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

        if ($this->dash->getFormat() instanceof X264) {
            $this->filter[] = "-profile:v:0";
            $this->filter[] = "main";

            $count = count($this->dash->getRepresentations());

            while ($count > 0) {
                $this->filter[] = "-profile:v:" . $count;
                $this->filter[] = "baseline";
                $count--;
            }
        }

        return $this->filter;
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