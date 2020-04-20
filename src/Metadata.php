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


use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\FFProbe\DataMapping\Stream as VideoStream;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use Streaming\Exception\InvalidArgumentException;

class Metadata
{
    /** @var Stream */
    private $stream;

    /** @var \FFMpeg\FFProbe\DataMapping\Format */
    private $format;

    /** @var \FFMpeg\FFProbe\DataMapping\StreamCollection */
    private $video_streams;

    /**
     * Metadata constructor.
     * @param Stream $stream
     */
    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
        $this->format = $stream->getMedia()->getFormat();
        $this->video_streams = $stream->getMedia()->getStreams();
    }

    /**
     * @return \FFMpeg\FFProbe\DataMapping\Format
     */
    public function getFormat(): Format
    {
        return $this->format;
    }

    /**
     * @return \FFMpeg\FFProbe\DataMapping\StreamCollection
     */
    public function getVideoStreams(): StreamCollection
    {
        return $this->video_streams;
    }

    /**
     * @param VideoStream $stream
     * @return array
     */
    private function streamToArray(VideoStream $stream): array
    {
        return $stream->all();
    }

    /**
     * @return mixed
     */
    private function getVideoMetadata(): array
    {
        return [
            'format' => $this->getFormat()->all(),
            'streams' => array_map([$this, 'streamToArray'], $this->getVideoStreams()->all())
        ];
    }

    /**
     * @param Representation $rep
     * @return array
     */
    private function repToArray(Representation $rep): array
    {
        return [
            "dimension" => strtoupper($rep->size2string()),
            "video_kilo_bitrate" => $rep->getKiloBitrate(),
            "audio_kilo_bitrate" => $rep->getAudioKiloBitrate() ?? "Not specified"
        ];
    }

    /**
     * @return array
     */
    private function getResolutions(): array
    {
        if (!method_exists($this->stream, 'getRepresentations')) {
            return [];
        }

        return array_map([$this, 'repToArray'], $this->stream->getRepresentations()->all());
    }


    /**
     * @return array
     */
    public function getStreamsMetadata(): array
    {
        $dirname = $this->stream->pathInfo(PATHINFO_DIRNAME);
        $basename = $this->stream->pathInfo(PATHINFO_BASENAME);
        $filename = $dirname . DIRECTORY_SEPARATOR . $basename;

        $technique = explode("\\", get_class($this->stream));
        $format = explode("\\", get_class($this->stream->getFormat()));

        $metadata = [
            "filename" => $filename,
            "size_of_stream_dir" => File::directorySize($dirname),
            "created_at" => file_exists($filename) ? date("Y-m-d H:i:s", filemtime($filename)) : 'The file has been deleted',
            "resolutions" => $this->getResolutions(),
            "format" => end($format),
            "streaming_technique" => end($technique)
        ];

        if ($this->stream instanceof DASH) {
            $metadata = array_merge($metadata, ["seg_duration" => $this->stream->getSegDuration()]);
        } elseif ($this->stream instanceof HLS) {
            $metadata = array_merge(
                $metadata,
                [
                    "hls_time" => (int)$this->stream->getHlsTime(),
                    "hls_cache" => (bool)$this->stream->isHlsAllowCache(),
                    "encrypted_hls" => (bool)$this->stream->getHlsKeyInfoFile(),
                    "ts_sub_directory" => $this->stream->getSegSubDirectory(),
                    "base_url" => $this->stream->getHlsBaseUrl()
                ]
            );
        }

        return $metadata;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return [
            "video" => $this->getVideoMetadata(),
            "stream" => $this->getStreamsMetadata()
        ];
    }

    /**
     * @param null $opts
     * @return string
     */
    public function getJson($opts = null): string
    {
        return json_encode($this->get(), $opts ?? JSON_PRETTY_PRINT);
    }

    /**
     * @param string $filename
     * @param int $opts
     * @return string
     */
    public function saveAsJson(string $filename = null, int $opts = null): string
    {
        if (is_null($filename)) {
            if ($this->stream->isTmpDir()) {
                throw new InvalidArgumentException("It is a temp directory! It is not possible to save it");
            }

            $name = uniqid(($this->stream->pathInfo(PATHINFO_FILENAME) ?? "meta") . "-") . ".json";
            $filename = $this->stream->pathInfo(PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $name;
        }
        File::put($filename, $this->getJson($opts));

        return $filename;
    }

    /**
     * @param string|null $save_to
     * @param int|null $opts
     * @return array
     */
    public function export(string $save_to = null, int $opts = null): array
    {
        return array_merge($this->get(), ['filename' => $this->saveAsJson($save_to, $opts)]);
    }
}