# PHP FFMPEG Video Streaming

This package provides integration with [PHP-FFmpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) and exports well-known video streaming techniques such as DASH, HLS, and Live Streaming(DASH and HLS live streaming).

## Features
* Easily converts most types of videos into DASH, HLS, and Live video streaming.
* Super easy wrapper around [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg), including support for filters and other advanced features.
* Automatically detects video size and bitrate and creates multi-representations MPD or M3U8 file.
* PHP > 7.1.0.

## Installation

This version of the package is only compatible with PHP 7.1.0 or newer.

Install the package via composer:

``` bash
composer require aminyazdanpanah/php-ffmpeg-video-streaming
```

## Basic Usage

``` php
require 'vendor/autoload.php'; // if you use frameworks that require autoload, you do not need to require it

$path = ''/var/www/media/videos/test.mp4'';// the path to the video

dash($path);// auto create dash MPD file based on original video
hls($path);// auto create HLS M3U8 file based on original video
```

## Documentation

### UML Diagram
This is how it works:


![UML](/docs/uml-class1.png?raw=true "UML")


### DASH
You can create an MPD playlist to do [DASH](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP).

As of version 1.1.0, the ```autoGenerateRepresentations``` method has been added. This method allows you to create a multi-representations MPD file automatically based on the video size and bit rate:

``` php
AYazdanpanah\FFMpegStreaming\FFMpeg::create()// it can pass the configuration and logger to the method  or it can be null
    ->open('/var/www/media/videos/test.mp4') // the path to the video
    ->DASH()
    ->X264() // the format of the video.for use another formats, see Traits\Formats
    ->autoGenerateRepresentations() // auto generate representations
    ->setAdaption('id=0,streams=v id=1,streams=a') // set the adaption.
    ->save(); // it can pass a path to the method or it can be null
```

or you can add representation manually by using  ```addRepresentation``` method:

``` php
$rep_1 = (new Representation())->setKiloBitrate(800);
$rep_2 = (new Representation())->setKiloBitrate(300)->setResize(320 , 170);

AYazdanpanah\FFMpegStreaming\FFMpeg::create()// it can pass the configuration and logger to the method or it can be null
    ->open('/var/www/media/videos/test.mp4') // the path to the video
    ->DASH()
    ->X264() // the format of the video.for use another formats, see Traits\Formats
    ->addRepresentation($rep_1) // add representation
    ->addRepresentation($rep_2) // add representation
    ->setAdaption('id=0,streams=v id=1,streams=a') // set the adaption.
    ->save(); // it can pass a path to the method or it can be null

```

For more information about [FFMpeg](https://ffmpeg.org/) and its dash options please [click here](https://ffmpeg.org/ffmpeg-formats.html#dash-2).
### HLS

Create an M3U8 playlist to do [HLS](https://en.wikipedia.org/wiki/HTTP_Live_Streaming).

As of version 1.1.0, the ```autoGenerateRepresentations``` method has been added. This method allows you to create a multi-formats M3U8 file automatically based on original video size and bit rate:

``` php
AYazdanpanah\FFMpegStreaming\FFMpeg::create()// it can pass the configuration and logger to the method or it can be null
    ->open('/var/www/media/videos/test.mp4') // the path to the video
    ->HLS()
    ->X264() // the format of the video.for use another formats, see Traits\Formats
    ->autoGenerateRepresentations() // auto generate representations
    ->save(); // it can pass a path to the method or it can be null
```

or you can add representation manually by using  ```addRepresentation``` method:

``` php
$rep_1 = (new Representation())->setKiloBitrate(1000)->setResize(1080 , 720);
$rep_2 = (new Representation())->setKiloBitrate(500)->setResize(640 , 360);
$rep_3 = (new Representation())->setKiloBitrate(200)->setResize(480 , 240);

AYazdanpanah\FFMpegStreaming\FFMpeg::create()// it can pass the configuration and logger to the method or it can be null
    ->open('/var/www/media/videos/test.mp4') // the path to the video
    ->HLS()
    ->X264() // the format of the video.for use another formats, see Traits\Formats
    ->addRepresentation($rep_1) // add representation
    ->addRepresentation($rep_2) // add representation
    ->addRepresentation($rep_3) // add representation
    ->setStreamMap('v:0 v:1 v:2') // set the StreamMap.
    ->save(); // it can pass a path to the method or it can be null
```
For more information about `setStreamMap` method and its input and also HLS options please [click here](https://ffmpeg.org/ffmpeg-formats.html#hls-2).

## Live Streaming

Soon!

## Contributing

I'd love your help in improving, correcting, adding to the specification.
Please [file an issue](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/issues)
or [submit a pull request](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/pulls).

Please see [Contributing File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/CONTRIBUTING.md) for more information.

## Security

If you discover a security vulnerability within this package, please send an e-mail to Amin Yazdanpanah via:
contact [AT] aminyazdanpanah • com.
## Credits

- [Amin Yazdanpanah](http://www.aminyazdanpanah.com/?u=github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming)

## License

The MIT License (MIT). Please see [License File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE) for more information.
