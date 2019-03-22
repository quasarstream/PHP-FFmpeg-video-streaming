# PHP FFMPEG Video Streaming

[![Build Status](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming.svg?branch=master)](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat-square)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Total Downloads](https://img.shields.io/packagist/dt/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat-square)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Build status](https://img.shields.io/appveyor/ci/aminyazdanpanah/PHP-FFmpeg-video-streaming/master.svg?style=flat-square&logo=appveyor)](https://ci.appveyor.com/project/aminyazdanpanah/php-ffmpeg-video-streaming)

This package provides an integration with [PHP-FFmpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) and packages well-known live streaming techniques such as DASH and HLS. Also you can use DRM for HLS packaging.

**NOTE:**

- For DRM and encryption DASH and HLS, I **strongly recommend** to try [Shaka PHP](https://github.com/aminyazdanpanah/shaka-php), which is a great tool for this use case.

## Features
* Easily package your videos to DASH and HLS live technique.
* Super easy wrapper around [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg), including support for filters and other advanced features.
* Automatically detects video size and bitrate and packages DASH and HLS with multi-qualities.
* PHP >= 7.1.0.

## Installation

This version of the package is only compatible with PHP 7.1.0 and later.

Install the package via composer:

``` bash
composer require aminyazdanpanah/php-ffmpeg-video-streaming
```

## Basic Usage

``` php
//if you use frameworks that require autoload, you do not need to require it
require_once 'vendor/autoload.php'; 

$input_path = '/var/www/media/videos/test.mp4';// the path to the video

//You can transcode videos using a callback method.
//If you do not want to transcode video, do not pass it(it can be null).
//This value is optional.
$listener = function ($audio, $format, $percentage) {
    echo "$percentage % is transcoded\n";
};

//The path you would like to save your files.
//Also, it can be null-the default path is the input path.
//These values are optional
$output_path_dash = '/var/www/media/videos/test/dash/output.mpd'; //or null
$output_path_hls = null; //or '/var/www/media/videos/test/hls/output.m3u8'

//The path to the key info for encryption hls.
//This value is optional.
$hls_key_info = __DIR__ . "/enc.keyinfo";

$result_dash = dash($input_path, $output_path_dash, $listener); //Create dash files.
$result_hls = hls($input_path, $output_path_hls, $listener, $hls_key_info); //Create hls files.

//dupm the results
var_dump($result_dash, $result_hls);
```

## Documentation

It is recommended to browse the source code as it is self-documented.

### Required Libraries

This library requires a working FFMpeg install. You will need both FFMpeg and FFProbe binaries to use it.

For installing FFmpeg and FFprobe, just Google "install ffmpeg on" + `your operation system`

### Configuration

FFMpeg will autodetect ffmpeg and ffprobe binaries. If you want to give binary paths explicitly, you can pass an array as configuration. A Psr\Logger\LoggerInterface can also be passed to log binary executions.

``` php
$config = [
    'ffmpeg.binaries'  => '/opt/local/ffmpeg/bin/ffmpeg',
    'ffprobe.binaries' => '/opt/local/ffmpeg/bin/ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
    ];
    
$ffmpeg = AYazdanpanah\FFMpegStreaming\FFMpeg::create($config);
```


### DASH
**[Dynamic Adaptive Streaming over HTTP (DASH)](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP)**, also known as MPEG-DASH, is an adaptive bitrate streaming technique that enables high quality streaming of media content over the Internet delivered from conventional HTTP web servers.

To create an MPD file use `DASH` method and export video into Dash.

 
As of version 1.1.0, the ```autoGenerateRepresentations``` method has been added. This method allows you to create a multi-representations MPD file automatically based on the video size and bit rate:
#### Auto Create DASH Files
``` php
AYazdanpanah\FFMpegStreaming\FFMpeg::create()// it can pass the configuration and logger to the method  or it can be null
    ->open('/var/www/media/videos/test.mp4') // the path to the video
    ->DASH()
    ->HEVC() // the format of the video.for use another formats, see Traits\Formats
    ->autoGenerateRepresentations() // auto generate representations
    ->setAdaption('id=0,streams=v id=1,streams=a') // set the adaption.
    ->save(); // it can pass a path to the method or it can be null
```


#### Create Representations Manually

Also you can add representation manually by using  ```addRepresentation``` method:

``` php
$rep_1 = (new Representation())->setKiloBitrate(800);
$rep_2 = (new Representation())->setKiloBitrate(300)->setResize(320 , 170);

AYazdanpanah\FFMpegStreaming\FFMpeg::create()// it can pass the configuration and logger to the method or it can be null
    ->open('/var/www/media/videos/test.mp4') // the path to the video
    ->DASH()
    ->HEVC() // the format of the video.for use another formats, see Traits\Formats
    ->addRepresentation($rep_1) // add representation
    ->addRepresentation($rep_2) // add representation
    ->setAdaption('id=0,streams=v id=1,streams=a') // set the adaption.
    ->save(); // it can pass a path to the method or it can be null

```


#### Transcoding

You can transcode videos using the `on` method in formats class.
 
 Transcoding progress can be monitored in realtime, see Format documentation
in [FFMpeg documentation](https://github.com/PHP-FFMpeg/PHP-FFMpeg#documentation) for more information.
Please note that audio and video bitrate are set on the format.

``` php
$format = new AYazdanpanah\FFMpegStreaming\Format\HEVC();

$format->on('progress', function ($video, $format, $percentage) {
    echo "$percentage % is transcoded";
});

AYazdanpanah\FFMpegStreaming\FFMpeg::create()
    ->open('/var/www/media/videos/test.mp4')
    ->DASH()
    ->setFormat($format)
    ->autoGenerateRepresentations()
    ->setAdaption('id=0,streams=v id=1,streams=a')
    ->save('/var/www/media/videos/dash/test.mpd');

```



For more information about [FFMpeg](https://ffmpeg.org/) and its dash options please [click here](https://ffmpeg.org/ffmpeg-formats.html#dash-2).
### HLS

**[HTTP Live Streaming (also known as HLS)](https://en.wikipedia.org/wiki/HTTP_Live_Streaming)** is an HTTP-based media streaming communications protocol implemented by [Apple Inc](https://www.apple.com/).

To create an M3U8 playlist to do HLS, just use `HLS` method.

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
    ->setHlsTime(5) // set Hls Time. the default value is 5 
    ->setHlsAllowCache(false) // set Hls that is allowed to cache files. the default value is true 
    ->save(); // it can pass a path to the method or it can be null
```
For more information about which value you should pass to these methods and also HLS options please [click here](https://ffmpeg.org/ffmpeg-formats.html#hls-2).

#### Transcoding

``` php
$format = new AYazdanpanah\FFMpegStreaming\Format\X264();

$format->on('progress', function ($video, $format, $percentage) {
    echo "$percentage % is transcoded";
});

AYazdanpanah\FFMpegStreaming\FFMpeg::create()
    ->open('/var/www/media/videos/test.mp4')
    ->HLS()
    ->setFormat($format)
    ->autoGenerateRepresentations()
    ->save('/var/www/media/videos/dash/test.m3u8');
```

#### Encryption HLS

The encryption process requires some kind of secret (key) together with an encryption algorithm.

HLS uses AES in cipher block chaining (CBC) mode. This means each block is encrypted using the cipher text of the preceding block. [read more](http://hlsbook.net/how-to-encrypt-hls-video-with-ffmpeg/)

Before we can encrypt our videos, we need an encryption key. I’m going to use OpenSSL to create the key, which we can do like so:

``` bash 
openssl rand 16 > enc.key
```

The next step is to generate an IV. This step is optional. (If no value is provided, the segment sequence number will be used instead.)
``` bash
openssl rand -hex 16
ecd0d06eaf884d8226c33928e87efa33
```

Make a note of the output as you’ll need it shortly.

To encrypt the video we need to tell ffmpeg what encryption key to use, the URI of the key, and so on. We use `setHlsKeyInfoFile` method and passing the location of a key info file. The file must be in the following format:

``` bash
Key URI
Path to key file
IV (optional)
```

The first line specifies the URI of the key, which will be written to the playlist. The second line is the path to the file containing the encryption key, and the (optional) third line contains the initialisation vector. Here’s an example (enc.keyinfo):

``` bash
https://example.com/enc.key
enc.key
ecd0d06eaf884d8226c33928e87efa33
```

Now that we have everything we need, run the following code to encrypt the video segments:

``` php
AYazdanpanah\FFMpegStreaming\FFMpeg::create()
    ->open('/var/www/media/videos/test.mp4')
    ->HLS()
    ->X264()
    ->setHlsKeyInfoFile('/var/www/enc.keyinfo')
    ->autoGenerateRepresentations()
    ->save('/var/www/media/videos/hls/test.m3u8');
```

Reference: http://hlsbook.net/

### Other Advanced Features
You can easily use other advanced features in the [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) library. In fact, when you open a file with `open` method, it holds the Media object that belongs to the PHP-FFMpeg.

``` php
$ffmpeg = AYazdanpanah\FFMpegStreaming\FFMpeg::create()
$video = $ffmpeg->open('/var/www/media/videos/test.mp4')
```
#### Extracting image
ou can extract a frame at any timecode using the `FFMpeg\Media\Video::frame` method.

``` php
$video
    ->filters()
    ->extractMultipleFrames(FFMpeg\Filters\Video\ExtractMultipleFramesFilter::FRAMERATE_EVERY_10SEC, '/path/to/destination/folder/')
    ->synchronize();

$video
    ->save(new FFMpeg\Format\Video\X264(), '/path/to/new/file');
```

#### Clip
Cuts the video at a desired point. Use input seeking method. It is faster option than use filter clip.

``` php
$clip = $video->clip(FFMpeg\Coordinate\TimeCode::fromSeconds(30), FFMpeg\Coordinate\TimeCode::fromSeconds(15));
$clip->filters()->resize(new FFMpeg\Coordinate\Dimension(320, 240), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_INSET, true);
$clip->save(new FFMpeg\Format\Video\X264(), 'video.avi');
```

#### Watermark
Watermark a video with a given image.

``` php
$video
    ->filters()
    ->watermark($watermarkPath, array(
        'position' => 'absolute',
        'x' => 1180,
        'y' => 620,
    ));
```

#### Extracting Media Metadata
You can also use `getFirstStream` method to extract media metadata.

``` php
$metadata = AYazdanpanah\FFMpegStreaming\FFMpeg::create()
    ->open('/var/www/media/videos/test.mp4')
    ->getFirstStream()
    ->all();
```
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
