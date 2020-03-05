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


use Streaming\DASH;
use Streaming\Format\X264;
use Streaming\Representation;

class DASHFilter extends Filter
{
    /**
     * @param $media
     */
    public function setFilter($media): void
    {
        $this->filter = $this->DASHFilter($media);
    }

    /**
     * @param DASH $dash
     * @return array
     */
    private function DASHFilter(DASH $dash): array
    {
        $filter = $this->getBaseFilters($dash, count($dash->getRepresentations()));

        foreach ($dash->getRepresentations() as $key => $representation) {
            $filter[] = "-map";
            $filter[] = "0";
            $filter[] = "-b:v:" . $key;
            $filter[] = $representation->getKiloBitrate() . "k";
            $filter = array_merge($filter, $this->getAudioBitrate($representation, $key));

            if (null !== $representation->getResize()) {
                $filter[] = "-s:v:" . $key;
                $filter[] = $representation->getResize();
            }
        }

        if ($dash->getAdaption()) {
            $filter[] = "-adaptation_sets";
            $filter[] = $dash->getAdaption();
        }
        $filter = array_merge($filter, $dash->getAdditionalParams());
        $filter = array_merge($filter, ["-strict", $dash->getStrict()]);

        return $filter;
    }

    /**
     * @param $dash
     * @param $count
     * @return array
     */
    private function getBaseFilters(DASH $dash, int $count): array
    {
        $dirname = $dash->getPathInfo(PATHINFO_FILENAME);
        $filename = $dash->getPathInfo(PATHINFO_FILENAME);

        $filter = [
            "-bf", "1",
            "-keyint_min", "120",
            "-g", "120",
            "-sc_threshold", "0",
            "-b_strategy", "0",
            "-use_timeline", "1",
            "-use_template", "1",
            "-init_seg_name", ($filename . '_init_$RepresentationID$.$ext$'),
            "-media_seg_name", ($filename . '_chunk_$RepresentationID$_$Number%05d$.$ext$'),
            "-seg_duration", $dash->getSegDuration(),
            "-hls_playlist", (int)$dash->isGenerateHlsPlaylist(),
            "-f", "dash",
        ];

        if ($dash->getFormat() instanceof X264) {
            $filter[] = "-profile:v:0";
            $filter[] = "main";

            while ($count > 0) {
                $filter[] = "-profile:v:" . $count;
                $filter[] = "baseline";
                $count--;
            }
        }

        return $filter;
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