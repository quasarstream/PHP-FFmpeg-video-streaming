<?php

namespace App\Dash;

use AYazdanpanah\FFMpegStreaming\Exception\Exception;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFProbe\DataMapping\Stream;

class AutoRepresentations
{
    private $stream;

    /**
     * AutoRepresentations constructor.
     * @param Stream $stream
     */
    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return Dimension
     */
    private function getDimensions(): Dimension
    {
        return $this->stream->getDimensions();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getKiloBitRate(): int
    {
        if (!$this->stream->has('bit_rate')) {
            throw new Exception("Invalid stream");
        }
        return (int)$this->stream->get('bit_rate') / 1000;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function get(): array
    {
        $dimension = $this->getDimensions();

        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $param = [
            'aspect_ratio' => $width / $height,
            'bit_rate' => $this->getKiloBitRate(),
        ];

        if ($height > 1050) {
            $representations = [
                $this->addRepresentation($param, 4, 240),
                $this->addRepresentation($param, 2, 480),
                $this->addRepresentation($param, 4 / 3, 720),
                $this->addRepresentation($param, 1, $height)
            ];
        } elseif ($height > 700) {
            $representations = [
                $this->addRepresentation($param, 4, 240),
                $this->addRepresentation($param, 2, 360),
                $this->addRepresentation($param, 4 / 3, 480),
                $this->addRepresentation($param, 1, $height)
            ];
        } elseif ($height > 450) {
            $representations = [
                $this->addRepresentation($param, 4, 144),
                $this->addRepresentation($param, 2, 240),
                $this->addRepresentation($param, 4 / 3, 360),
                $this->addRepresentation($param, 1, $height)
            ];
        } elseif ($height > 330) {
            $representations = [
                $this->addRepresentation($param, 3, 144),
                $this->addRepresentation($param, 3 / 2, 240),
                $this->addRepresentation($param, 1, $height),
                null
            ];
        } elseif ($height > 210) {
            $representations = [
                $this->addRepresentation($param, 2, 144),
                $this->addRepresentation($param, 1, $height),
            ];
        } else {
            $representations = [
                $this->addRepresentation($param, 1, $height),
            ];
        }

        return $representations;
    }

    /**
     * @param $param
     * @param $divide
     * @param $height
     * @return Representation
     * @internal param $aspect_ratio
     * @throws Exception
     */
    private function addRepresentation($param, $divide, $height): Representation
    {
        $width = intval($height * $param['aspect_ratio']);

        if ($width % 2 == 1) $width++;

        return (new Representation())
            ->setKiloBitrate(intval($param['bit_rate'] / $divide))
            ->setResize($width, $height);
    }
}