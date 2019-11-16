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
     * @param Representation $rep
     * @return $this
     * @deprecated Please use addRepresentations instead
     */
    public function addRepresentation(Representation $rep)
    {
        @trigger_error('addRepresentation method is deprecated and will be removed in a future release. Use addRepresentations instead.', E_USER_DEPRECATED);
        $this->checkFormat();
        $this->representations[] = $rep;

        return $this;
    }

    /**
     * @return $this
     */
    public function addRepresentations()
    {
        $this->checkFormat();
        $reps = func_get_args();

        foreach ($reps as $rep) {
            if (!$rep instanceof Representation) {
                throw new InvalidArgumentException('It must be instance of Representation object');
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
     * @param array $side_values
     * @param array|null $k_bitrate_values
     * @return $this
     */
    public function autoGenerateRepresentations(array $side_values = null, array $k_bitrate_values = null)
    {
        $this->checkFormat();
        $this->representations = (new AutoRepresentations($this->getMedia()->probe(), $side_values, $k_bitrate_values))->get();

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