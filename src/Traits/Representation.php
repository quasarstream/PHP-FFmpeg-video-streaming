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
use Streaming\Exception\Exception;
use Streaming\Representation as Rep;

trait Representation
{
    /** @var array */
    protected $representations = [];

    /**
     * @param Rep $rep
     * @return $this
     * @throws Exception
     */
    public function addRepresentation(Rep $rep)
    {
        if (!$this->format) {
            throw new Exception('Format has not been set');
        }

        $this->representations[] = $rep;
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
     * @throws Exception
     */
    public function autoGenerateRepresentations(array $side_values = null, array $k_bitrate_values = null)
    {
        if (!$this->format) {
            throw new Exception('Format has not been set');
        }

        $this->representations = (new AutoRepresentations($this->getMedia()->probe(), $side_values, $k_bitrate_values))
            ->get();

        return $this;
    }
}