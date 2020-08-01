<?php


namespace Streaming\Filters;


use FFMpeg\Format\VideoInterface;
use Streaming\Utiles;

abstract class FormatFilter extends StreamFilter
{
    /**
     * @param VideoInterface $format
     * @return array
     */
    protected function getFormatOptions(VideoInterface $format): array
    {
        $basic = Utiles::arrayToFFmpegOpt([
            'c:v' => $format->getVideoCodec(),
            'c:a' => $format->getAudioCodec(),
        ]);

        $options = Utiles::arrayToFFmpegOpt(
            array_merge($format->getAdditionalParameters() ?? [])
        );

        return array_merge($basic, $options);
    }
}