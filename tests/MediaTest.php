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
use AYazdanpanah\FFMpegStreaming\HLS;
use AYazdanpanah\FFMpegStreaming\Media;
use FFMpeg\FFProbe\DataMapping\Stream;

class MediaTest extends TestCase
{
    public function testMediaClass()
    {
        $media = $this->getVideo();
        $this->assertInstanceOf(Media::class, $media);
    }

    public function testDASH()
    {
        $this->assertInstanceOf(DASH::class, $this->getDASH());
    }

    public function testHLS()
    {
        $this->assertInstanceOf(HLS::class, $this->getHLS());
    }

    public function testGetFirstStream()
    {
        $media = $this->getVideo();
        $get_first_stream = $media->getFirstStream();

        $this->assertInstanceOf(Stream::class, $get_first_stream);
    }

    public function testGetPathInfo()
    {
        $media = $this->getVideo();
        $get_path_info = $media->getPathInfo();

        $this->assertIsArray($get_path_info);
        $this->assertArrayHasKey('dirname',$get_path_info);
        $this->assertArrayHasKey('filename',$get_path_info);
    }

    private function getDASH()
    {
        $media = $this->getVideo();
        return $media->DASH();
    }

    private function getHLS()
    {
        $media = $this->getVideo();
        return $media->HLS();
    }
}