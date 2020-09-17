<?php


namespace Streaming;


use Streaming\Exception\InvalidArgumentException;

class HLSSubtitle
{
    /** @const string */
    private const S_TAG = "#EXTM3U";

    /** @const string */
    private const E_TAG = "#EXT-X-ENDLIST";

    /** @const string */
    private const SUPPORTED_EXTS = ['vtt'];

    /** @var string */
    private $path;

    /** @var string */
    private $language_name;

    /** @var string */
    private $language_code;

    /** @var string */
    private $uri = null;

    /** @var string */
    private $m3u8_uri;

    /** @var bool */
    private $default = false;

    /** @var bool */
    private $auto_select = false;

    /** @var bool */
    private $force = false;

    /** @var int */
    private $hls_version = 3;

    /** @var int */
    private $media_sequence = 1;

    /** @var string */
    private $media_type = "VOD";

    /** @var string */
    private $group_id = "subs";

    /**
     * HLSSubtitle constructor.
     * @param string $path
     * @param string $language_name
     * @param string $language_code
     */
    public function __construct(string $path, string $language_name, string $language_code)
    {
        $this->path = $path;
        $this->language_name = $language_name;
        $this->language_code = $language_code;

        if(!in_array(pathinfo($path, PATHINFO_EXTENSION), static::SUPPORTED_EXTS)){
            throw new InvalidArgumentException(sprintf("Unsupported input! Only %s files are acceptable.", implode(",", static::SUPPORTED_EXTS)));
        }
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getLanguageName(): string
    {
        return $this->language_name;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->language_code;
    }
    /**
     * @return string|null
     */
    public function getUri(): string
    {
        return $this->uri ?? $this->getBaseName();
    }

    /**
     * @return string
     */
    private function getFileName(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * @return string
     */
    private function getBaseName(): string
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    /**
     * @param string|null $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @param bool $default
     */
    public function default(bool $default = true): void
    {
        $this->default = $default;
    }

    /**
     * @return bool
     */
    public function isAutoSelect(): bool
    {
        return $this->auto_select;
    }

    /**
     * @param bool $auto_select
     */
    public function autoSelect(bool $auto_select = true): void
    {
        $this->auto_select = $auto_select;
    }

    /**
     * @return bool
     */
    public function isForce(): bool
    {
        return $this->force;
    }

    /**
     * @param bool $force
     */
    public function force(bool $force = true): void
    {
        $this->force = $force;
    }


    /**
     * @param int $hls_version
     */
    public function setHlsVersion(int $hls_version): void
    {
        $this->hls_version = $hls_version;
    }

    /**
     * @param int $media_sequence
     */
    public function setMediaSequence(int $media_sequence): void
    {
        $this->media_sequence = $media_sequence;
    }

    /**
     * @param string $media_type
     */
    public function setMediaType(string $media_type): void
    {
        $this->media_type = $media_type;
    }

    /**
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->group_id;
    }

    /**
     * @param string $group_id
     */
    public function setGroupId(string $group_id): void
    {
        $this->group_id = $group_id;
    }

    /**
     * @return string
     */
    public function getM3u8Uri(): string
    {
        return $this->m3u8_uri;
    }

    /**
     * @param string $m3u8_uri
     */
    public function setM3u8Uri(string $m3u8_uri): void
    {
        $this->m3u8_uri = $m3u8_uri;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $s_ext = "#EXT-X-MEDIA:";

        $ext = [
            "TYPE"          => "SUBTITLES",
            "GROUP-ID"      => "\"" . $this->group_id . "\"",
            "NAME"          => "\"" . $this->language_name . "\"",
            "DEFAULT"       => Utiles::convertBooleanToYesNo($this->isDefault()),
            "AUTOSELECT"    => Utiles::convertBooleanToYesNo($this->isAutoSelect()),
            "FORCED"        => Utiles::convertBooleanToYesNo($this->isForce()),
            "LANGUAGE"      => "\"" . $this->language_code . "\"",
            "URI"           => "\"" . $this->getM3u8Uri(). "\"",
        ];
        Utiles::concatKeyValue($ext, "=");

        return $s_ext . implode(",", $ext);
    }

    public function generateM3U8File(string $path, float $duration, array $description = [], array $info = []): void
    {
        $ext_x = array_merge($description, [
            "#EXT-X-TARGETDURATION" => intval($duration),
            "#EXT-X-VERSION"        => $this->hls_version,
            "#EXT-X-MEDIA-SEQUENCE" => $this->media_sequence,
            "#EXT-X-PLAYLIST-TYPE"  => $this->media_type,
            "#EXTINF"               => implode(",", array_merge([number_format($duration, 1, '.', '')], $info))
        ]);

        Utiles::concatKeyValue($ext_x, ":");
        File::put($path, implode(PHP_EOL, array_merge([static::S_TAG], $ext_x, [$this->getUri(), static::E_TAG])));

        if(!$this->m3u8_uri){
            $this->setM3u8Uri(pathinfo($path, PATHINFO_BASENAME));
        }

        if(!$this->uri){
            File::copy($this->path, implode(DIRECTORY_SEPARATOR, [dirname($path), $this->getBaseName()]));
        }
    }
}