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


class StreamingAnalytics
{
    /**
     * @var Export
     */
    private $export;

    /**
     * StreamingAnalytics constructor.
     * @param Export $export
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * @return mixed
     */
    public function analyse()
    {
        $metadata["original"] = $this->getOriginalMetadata();
        $metadata["streams"] = $this->getStreamsMetadata();
        $metadata["general"] = $this->getGeneralMetadata();

        $filename = $this->export->getPathInfo()["dirname"] . DIRECTORY_SEPARATOR . "analyse.json";
        file_put_contents($filename, json_encode($metadata));

        return $metadata;
    }

    /**
     * @return mixed
     */
    private function getOriginalMetadata()
    {

        $streams = $this->export->getMedia()->mediaInfo()->all();

        foreach ($streams as $key => $stream) {
            $streams[$key] = $stream->all();
        }

        return $streams;
    }

    /**
     * @return mixed
     */
    private function getStreamsMetadata()
    {
        $metadata["qualities"] = $this->getQualities();

        $format_class = explode("\\", get_class($this->export->getFormat()));
        $metadata["format"] = end($format_class);

        $export_class = explode("\\", get_class($this->export));
        $metadata["streaming_technique"] = end($export_class);

        if ($this->export instanceof DASH) {
            $metadata["dash_adaption"] = $this->export->getAdaption();
        } elseif ($this->export instanceof HLS) {
            $metadata["hls_time"] = $this->export->getHlsTime();
            $metadata["hls_cache"] = $this->export->isHlsAllowCache();
            $metadata["encrypted_hls"] = (bool)$this->export->getHlsKeyInfoFile();
        }

        return $metadata;
    }

    private function getQualities()
    {
        $qualities = [];
        foreach ($this->export->getRepresentations() as $key => $representation) {
            if ($representation instanceof Representation) {
                $qualities[$key]["dimensions"] = strtoupper($representation->getResize());
                $qualities[$key]["kilobitrate"] = $representation->getKiloBitrate();
            }
        }

        return $qualities;
    }

    private function getGeneralMetadata()
    {
        $video_path = $this->export->getMedia()->getPath();
        $metadata["path_to_video"] = $video_path;
        $metadata["dir_path_to_video"] = pathinfo($video_path)["dirname"];
        $metadata["basename_of_video"] = pathinfo($video_path)["basename"];
        $metadata["extension_of_video"] = pathinfo($video_path)["extension"];
        $metadata["mime_content_type_of_video"] = !is_file($video_path) ?: mime_content_type($video_path);
        $metadata["size_of_video"] = !is_file($video_path) ?: filesize($video_path);

        $stream_path = $this->export->getPathInfo();
        $metadata["dir_path_to_stream"] = $stream_path["dirname"];
        $metadata["path_to_stream"] = $stream_path["dirname"] . DIRECTORY_SEPARATOR . $stream_path["basename"];
        $metadata["size_of_stream_dir"] = FileManager::directorySize($stream_path["dirname"]);
        $metadata["datetime"] = date("Y-m-d H:i:s");
        $metadata["time"] = time();

        return $metadata;
    }
}