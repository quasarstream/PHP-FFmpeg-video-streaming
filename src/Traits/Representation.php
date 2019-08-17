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
use Streaming\MediaInfo\MediaInfo;
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
     * @return $this
     * @throws Exception
     */
    public function autoGenerateRepresentations(array $side_values = null)
    {
        if (!$this->format) {
            throw new Exception('Format has not been set');
        }

        $media_info = MediaInfo::initialize($this->media->getPath(), $this->mediaInfoBinary);

        $this->representations = (new AutoRepresentations($media_info, $side_values))
            ->get();

        return $this;
    }

}