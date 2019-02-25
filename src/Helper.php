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

namespace AYazdanpanah\FFMpegStreaming;

class Helper
{
    /**
     * round a number to nearest even number
     *
     * @param float $number
     * @return int
     */
    public static function roundToEven(float $number): int
    {
        return (($number = intval($number)) % 2 == 0) ? $number : $number + 1;
    }


    /**
     * @param $dirname
     */
    public static function makeDir($dirname): void
    {
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }
    }
}