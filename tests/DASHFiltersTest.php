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
use Streaming\Filters\Filter;

class DASHFiltersTest extends TestCase
{
    public function testFilterClass()
    {
        $this->assertInstanceOf(Filter::class, $this->getFilter());
    }

    public function testGetApply()
    {
        $apply = $this->getFilter()->apply();

        $this->assertIsArray($apply);

        $this->assertEquals(
            [
                "-bf", "1", "-keyint_min", "120", "-g", "120"
                , "-sc_threshold", "0", "-b_strategy", "0", "-strict", "-2", "-use_timeline"
                , "1", "-use_template", "1", "-f", "dash", "-map", "0", "-b:v:0"
                , "237k", "-s:v:0", "256x144", "-map", "0", "-b:v:1", "292k"
                , "-s:v:1", "426x240", "-map", "0", "-b:v:2", "380k", "-s:v:2", "640x360"
                , "-adaptation_sets", "id=0,streams=v id=1,streams=a"
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