<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming;


use Alchemy\BinaryDriver\Listeners\ListenerInterface;
use Evenement\EventEmitter;

class FFMpegListener extends EventEmitter implements ListenerInterface
{
    /** @var string */
    private $event;

    /**
     * FFMpegListener constructor.
     * @param string $event
     */
    public function __construct($event = 'listen')
    {
        $this->event = $event;
    }

    /**
     * Handle the output of a ProcessRunner
     *
     * @param string $type The data type, one of Process::ERR, Process::OUT constants
     * @param string $data The output
     */
    public function handle($type, $data)
    {
        foreach (explode(PHP_EOL, $data) as $line) {
            $this->emit($this->event, [$line]);
        }
    }

    /**
     * An array of events that should be forwarded to BinaryInterface
     *
     * @return array
     */
    public function forwardedEvents()
    {
        return [$this->event];
    }
}