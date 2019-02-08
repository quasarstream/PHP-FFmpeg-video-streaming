<?php

namespace App\Dash;

use AYazdanpanah\FFMpegStreaming\Exception\Exception;

class Representation
{
    private $kiloBitrate = 1000;
    private $resize = [];

    /**
     * @return mixed
     */
    public function getResize()
    {
        if (!isset($this->resize['width']) || !isset($this->resize['height'])) {
            return null;
        }

        return $this->resize['width'] . "x" . $this->resize['height'];
    }

    /**
     * @param $width
     * @param $height
     * @return Representation
     * @throws Exception
     */
    public function setResize(int $width, int $height): Representation
    {
        if ($width < 1 || $height < 1) {
            throw new Exception('Wrong resize value');
        }

        $this->resize['width'] = $width;
        $this->resize['height'] = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getKiloBitrate()
    {
        return $this->kiloBitrate;
    }

    /**
     * Sets the kiloBitrate value.
     *
     * @param  integer $kiloBitrate
     * @return Representation
     * @throws Exception
     */
    public function setKiloBitrate($kiloBitrate)
    {
        if ($kiloBitrate < 1) {
            throw new Exception('Wrong kilo bit rate value');
        }

        $this->kiloBitrate = (int)$kiloBitrate;

        return $this;
    }
}