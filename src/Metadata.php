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


use Streaming\Exception\InvalidArgumentException;

class Metadata
{
    /**
     * @var Export
     */
    private $export;

    /**
     * Metadata constructor.
     * @param Export $export
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * @return mixed
     */
    private function getVideoMetadata(): array
    {
        $probe = $this->export->getMedia()->probe();
        $streams = $probe['streams']->all();
        $format = $probe['format']->all();

        foreach ($streams as $key => $stream) {
            $streams[$key] = $stream->all();
        }

        return [
            'format' => $format,
            'streams' => $streams
        ];
    }

    /**
     * @return mixed
     */
    private function getStreamsMetadata(): array
    {
        $stream_path = $this->export->getPathInfo();
        $filename = $stream_path["dirname"] . DIRECTORY_SEPARATOR . $stream_path["basename"];
        $export_class = explode("\\", get_class($this->export));
        $format_class = explode("\\", get_class($this->export->getFormat()));

        $metadata = [
            "filename" => $filename,
            "size_of_stream_dir" => File::directorySize($stream_path["dirname"]),
            "created_at" => file_exists($filename) ? date("Y-m-d H:i:s", filemtime($filename)) : 'The file has been deleted',
            "resolutions" => $this->getResolutions(),
            "format" => end($format_class),
            "streaming_technique" => end($export_class)
        ];

        if ($this->export instanceof DASH) {
            $metadata = array_merge($metadata, ["seg_duration" => $this->export->getSegDuration()]);
        } elseif ($this->export instanceof HLS) {
            $metadata = array_merge(
                $metadata,
                [
                    "hls_time" => $this->export->getHlsTime(),
                    "hls_cache" => (bool)$this->export->isHlsAllowCache(),
                    "encrypted_hls" => (bool)$this->export->getHlsKeyInfoFile(),
                    "ts_sub_directory" => $this->export->getTsSubDirectory(),
                    "base_url" => $this->export->getHlsBaseUrl()
                ]
            );
        }

        return $metadata;
    }

    /**
     * @return array
     */
    private function getResolutions(): array
    {
        $resolutions = [];
        if (!method_exists($this->export, 'getRepresentations')) {
            return $resolutions;
        }

        foreach ($this->export->getRepresentations() as $representation) {
            $resolutions[] = [
                "dimension" => strtoupper($representation->getResize()),
                "video_kilo_bitrate" => $representation->getKiloBitrate(),
                "audio_kilo_bitrate" => $representation->getAudioKiloBitrate() ?? "Not specified"
            ];
        }

        return $resolutions;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return [
            "video" => $this->getVideoMetadata(),
            "streams" => $this->getStreamsMetadata()
        ];
    }

    /**
     * @param string $filename
     * @param int $opts
     * @return string
     */
    public function saveAsJson(string $filename = null, int $opts = null): string
    {
        if (is_null($filename)) {
            if ($this->export->isTmpDir()) {
                throw new InvalidArgumentException("It is a temp directory! It is not possible to save it");
            }

            $name = uniqid(($this->export->getPathInfo()["filename"] ?? "meta") . "-") . ".json";
            $filename = $this->export->getPathInfo()["dirname"] . DIRECTORY_SEPARATOR . $name;
        }

        file_put_contents(
            $filename,
            json_encode($this->getMetadata(), $opts ?? JSON_PRETTY_PRINT)
        );

        return $filename;
    }

    /**
     * @param string|null $save_to
     * @param int|null $opts
     * @return array
     */
    public function export(string $save_to = null, int $opts = null): array
    {
        return array_merge($this->getMetadata(), ['filename' => $this->saveAsJson($save_to, $opts)]);
    }
}