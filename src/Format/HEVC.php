<?php
/**
 * Created by PhpStorm.
 * User: amin6
 * Date: 10/24/2018
 * Time: 2:10 AM
 */

namespace App\Library\Format;
use FFMpeg\Format\Video\DefaultVideo;

class HEVC extends DefaultVideo
{
    public function __construct($audioCodec = '', $videoCodec = '')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    public function supportBFrames()
    {
        return false;
    }

    public function getAvailableAudioCodecs()
    {
        return array('');
    }

    public function getAvailableVideoCodecs()
    {
        return array('');
    }
}