<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Streaming\Traits;

use Streaming\AutoRepresentations;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Representation;

trait Representations
{
    /** @var array */
    protected $representations = [];

    /**
     * @return $this
     */
    public function addRepresentations()
    {
        $this->checkFormat();
        $reps = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

        foreach ($reps as $rep) {
            if (!$rep instanceof Representation) {
                throw new InvalidArgumentException('Representations must be instance of Representation object');
            }
        }

        $this->representations = $reps;
        return $this;
    }

    /**
     * @return array
     */
    public function getRepresentations(): array
    {
        return $this->representations;
    }

    /**
     * @param array|null $sides
     * @param array|null $k_bitrate
     * @return $this
     */
    public function autoGenerateRepresentations(array $sides = null, array $k_bitrate = null)
    {
        $this->checkFormat();
        $this->addRepresentations((new AutoRepresentations($this->getMedia(), $sides, $k_bitrate))->get());

        return $this;
    }

    /**
     * check whether format is set or nor
     */
    private function checkFormat()
    {
        if (!$this->format) {
            throw new InvalidArgumentException('First you must set the format of the video');
        }
    }
}