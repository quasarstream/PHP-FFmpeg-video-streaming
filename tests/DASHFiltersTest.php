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

use Streaming\DASH;
use Streaming\Filters\DASHFilter;
use Streaming\Filters\StreamFilter;

class DASHFiltersTest extends TestCase
{
    public function testFilterClass()
    {
        $this->assertInstanceOf(StreamFilter::class, $this->getFilter());
    }

    public function testGetApply()
    {
        $apply = $this->getFilter()->apply();

        $this->assertIsArray($apply);

        $this->assertEquals(
            [
                "-c:v", "libx265", "-c:a", "aac", "-keyint_min", "25", "-g", "250", "-sc_threshold", "40",
                "-use_timeline", "1", "-use_template", "1", "-init_seg_name", "test_init_\$RepresentationID$.\$ext$",
                "-media_seg_name", "test_chunk_\$RepresentationID\$_\$Number%05d$.\$ext$", "-seg_duration", "10",
                "-hls_playlist", "0", "-f", "dash", "-adaptation_sets", "id=0,streams=v id=1,streams=a", "-map", "0",
                "-s:v:0", "256x144", "-b:v:0", "103k", "-map", "0", "-s:v:1", "426x240", "-b:v:1", "138k", "-map", "0",
                "-s:v:2", "640x360", "-b:v:2", "207k", "-strict", "-2"
            ],
            $apply);
    }

    private function getFilter()
    {
        return new DASHFilter($this->getDASH());
    }

    private function getDASH()
    {
        $hls = new DASH($this->getVideo());

        return $hls->HEVC()
            ->autoGenerateRepresentations()
            ->setAdaption('id=0,streams=v id=1,streams=a');
    }
}