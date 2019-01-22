# PHP FFMPEG Video Streaming

This package provides an integration with FFmpeg and exports well-known video streaming such as DASH, HLS, and Live Streaming.

## Features
* Easily exports all your videos into DASH, HLS, and Live video streaming.
* Super easy wrapper around [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg), including support for filters and other advanced features.
* Only PHP 7.1.0 and above.

## Installation

This version of the package is only compatible with php 7.1.0 and above.

Install the package via composer:

``` bash
composer require
```


## Usage

Use and initialize the FFMpeg:

``` php
use AYazdanpanah\FFMpegStreaming\FFMpeg
```

``` php
$config = [
'ffmpeg.binaries' => '/usr/local/bin/ffmpeg', // the path to the FFMpeg binary
'ffprobe.binaries' => '/usr/local/bin/ffprobe', // the path to the FFProbe binary
'timeout' => 3600, // the timeout for the underlying process
'ffmpeg.threads' => 12, // the number of threads that FFMpeg should use
];

$ffmpeg = new FFMpeg($config);
```

## DASH

You can create an MPD playlist to do [DASH](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP).

``` php
try {
$rep_1 = (new Representation())->setKiloBitrate(800);
$rep_2 = (new Representation())->setKiloBitrate(300)->setResize(320 , 170);
$ffmpeg->open('/var/www/media/videos/test.mp4')
->DASH()
->X264()
->addRepresentation($rep_1)
->addRepresentation($rep_2)
->setAdaption('id=0,streams=v id=1,streams=a')
->save('/var/www/media/videos/test.mpd');
} catch (Exception $e) {
echo $e->getMassege();
}
```

## HLS

Create an M3U8 playlist to do [HLS](https://en.wikipedia.org/wiki/HTTP_Live_Streaming).

``` php
try {
$rep_1 = (new Representation())->setKiloBitrate(1000);
$rep_2 = (new Representation())->setKiloBitrate(500)->setResize(640 , 360);
$rep_3 = (new Representation())->setKiloBitrate(200)->setResize(480 , 240);
$ffmpeg->open('/var/www/media/videos/test.mp4')
->HLS()
->X264()
->addRepresentation($rep_1)
->addRepresentation($rep_2)
->addRepresentation($rep_3)
->setStreamMap('v:0,a:0 v:1,a:1')
->save('/var/www/media/videos/test.m3u8');
} catch (Exception $e) {
echo $e->getMassege();
}
```

## Live Streaming

Soon!

## Demo and Documentation

Please check out [my website](http://video.aminyazdanpanah.com) to see more examples and demos.

## Contributing

I'd love your help in improving, correcting, adding to the specification.
Please [file an issue](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/issues)
or [submit a pull request](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/pulls).

## Security

If you discover a security vulnerability within this package, please send an e-mail to Amin Yazdanpanah via contact [AT] aminyazdanpanah . com.
## Credits

- [Amin Yazdanpanah](http://www.aminyazdanpanah.com/?u=github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.