<?php

/**
 * Copyright 2019 Amin Yazdanpanah<http://www.aminyazdanpanah.com>.
 *
 * Licensed under the MIT License;
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://opensource.org/licenses/MIT
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Tests\FFMpegStreaming;

use AYazdanpanah\FFMpegStreaming\DASH;
use AYazdanpanah\FFMpegStreaming\Filters\DASHFilter;
use AYazdanpanah\FFMpegStreaming\Filters\Filter;

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
                , "-sc_threshold", "0", "-b_strategy", "0", "-use_timeline"
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