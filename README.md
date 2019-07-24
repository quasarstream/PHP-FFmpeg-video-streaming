# 📼 PHP FFMPEG Video Streaming

[![Build Status](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming.svg?branch=master)](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat-square)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Total Downloads](https://img.shields.io/packagist/dt/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat-square)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Build status](https://img.shields.io/appveyor/ci/aminyazdanpanah/PHP-FFmpeg-video-streaming/master.svg?style=flat-square&logo=appveyor)](https://ci.appveyor.com/project/aminyazdanpanah/php-ffmpeg-video-streaming)

This package provides an integration with [PHP-FFmpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) and packages well-known live streaming techniques such as DASH and HLS. Also you can use DRM for HLS packaging.


- [See Full Documentation](https://video.aminyazdanpanah.com/)

****NOTE:****  For DRM and encryption DASH and HLS, I **strongly recommend** to try **[Shaka PHP](https://github.com/aminyazdanpanah/shaka-php)**, which is a great tool for this use case.

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
    echo "$percentage% is transcoded\n";
};

//The path you would like to save your files.
//Also, it can be null-the default path is the input path.
//These values are optional
$output_path_dash = '/var/www/media/videos/test/dash/output.mpd'; //or null
$output_path_hls = null; //or '/var/www/media/videos/test/hls/output.m3u8'

//The path to the key info for encrypted hls.
//This value is optional.
$url_to_key = "https://www.aminyazdanpanah.com/enc.key"; //Path to get the key on your website
$path_to_save_key = "/var/www/media/keys/my_key/enc.key"; //Path to save the random key on your server
$hls_key_info = new Streaming\KeyInfo($url_to_key, $path_to_save_key);

$result_dash = dash($input_path, $output_path_dash, $listener); //Create dash files.
$result_hls = hls($input_path, $output_path_hls, $listener, $hls_key_info); //Create hls files.

//dupm the results
var_dump($result_dash, $result_hls);
```

## Documentation

- [See Full Documentation](http://video.aminyazdanpanah.com/)

### Required Libraries

This library requires a working FFMpeg. You will need both FFMpeg and FFProbe binaries to use it.

- Getting FFmpeg: https://ffmpeg.org/download.html

### Configuration

FFMpeg will autodetect ffmpeg and ffprobe binaries. If you want to give binary paths explicitly, you can pass an array as configuration. A Psr\Logger\LoggerInterface can also be passed to log binary executions.

``` php
$config = [
    'ffmpeg.binaries'  => '/opt/local/ffmpeg/bin/ffmpeg',
    'ffprobe.binaries' => '/opt/local/ffmpeg/bin/ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
    ];
    
$ffmpeg = Streaming\FFMpeg::create($config);
```


### DASH
**[Dynamic Adaptive Streaming over HTTP (DASH)](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP)**, also known as MPEG-DASH, is an adaptive bitrate streaming technique that enables high quality streaming of media content over the Internet delivered from conventional HTTP web servers.

To create an MPD file use `DASH` method and export video into Dash.

 
As of version 1.1.0, the ```autoGenerateRepresentations``` method has been added. This method allows you to create a multi-representations MPD file automatically based on the video size and bit rate:
#### Auto Create DASH Files
``` php
Streaming\FFMpeg::create()// It can pass the configuration and logger to the method  or it can be null
    ->open('/var/www/media/videos/test.mp4') // Path to a video
    ->DASH()
    ->HEVC() // Format of the video. For Using another format, see Traits\Formats
    ->autoGenerateRepresentations() // Auto generate representations
    ->setAdaption('id=0,streams=v id=1,streams=a') // Set the adaption.
    ->save(); // It can pass a path to the method or it can be null
```


#### Create Representations Manually

Also you can add representation manually by using  ```addRepresentation``` method:

``` php
$rep_1 = (new Representation())->setKiloBitrate(800)->setResize(1080 , 720);
$rep_2 = (new Representation())->setKiloBitrate(300)->setResize(320 , 170);

Streaming\FFMpeg::create()// It can pass the configuration and logger to the method or it can be null
    ->open('/var/www/media/videos/test.mp4') // Path to the video
    ->DASH()
    ->HEVC() // Format of the video.For Using another format, see Traits\Formats
    ->addRepresentation($rep_1) // Add representation
    ->addRepresentation($rep_2) // Add representation
    ->setAdaption('id=0,streams=v id=1,streams=a') // Set the adaption.
    ->save(); // It can pass a path to the method or it can be null

```


#### Transcoding

You can transcode videos using the `on` method in formats class.
 
 Transcoding progress can be monitored in realtime, see Format documentation
in [FFMpeg documentation](https://github.com/PHP-FFMpeg/PHP-FFMpeg#documentation) for more information.
Please note that audio and video bitrate are set on the format.

``` php
$format = new Streaming\Format\HEVC();

$format->on('progress', function ($video, $format, $percentage) {
    echo "$percentage% is transcoded\n";
});

Streaming\FFMpeg::create()
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
Streaming\FFMpeg::create()// It can pass the configuration and logger to the method or it can be null
    ->open('/var/www/media/videos/test.mp4') // Path to the video
    ->HLS()
    ->X264() // Format of the video.For Using another format, see Traits\Formats
    ->autoGenerateRepresentations() // Auto generate representations
    ->save(); // It can pass a path to the method or it can be null
```

or you can add representation manually by using  ```addRepresentation``` method:

``` php
$rep_1 = (new Representation())->setKiloBitrate(1000)->setResize(1080 , 720);
$rep_2 = (new Representation())->setKiloBitrate(500)->setResize(640 , 360);
$rep_3 = (new Representation())->setKiloBitrate(200)->setResize(480 , 270);

Streaming\FFMpeg::create()// it can pass the configuration and logger to the method or it can be null
    ->open('/var/www/media/videos/test.mp4') // Path to a video
    ->HLS()
    ->X264() // Format of the video.For Using another formats, see Traits\Formats
    ->addRepresentation($rep_1) // Add representation
    ->addRepresentation($rep_2) // Add representation
    ->addRepresentation($rep_3) // Add representation
    ->setHlsTime(5) // Set Hls Time. Default value is 5 
    ->setHlsAllowCache(false) // Set Hls that is allowed to cache files. Default value is true 
    ->save(); // It can pass a path to the method or it can be null
```
For more information about which value you should pass to these methods and also HLS options please [click here](https://ffmpeg.org/ffmpeg-formats.html#hls-2).

#### Transcoding

``` php
$format = new Streaming\Format\X264();

$format->on('progress', function ($video, $format, $percentage) {
    echo "$percentage% is transcoded\n";
});

Streaming\FFMpeg::create()
    ->open('/var/www/media/videos/test.mp4')
    ->HLS()
    ->setFormat($format)
    ->autoGenerateRepresentations()
    ->save('/var/www/media/videos/dash/test.m3u8');
```

#### Encrypted HLS

The encryption process requires some kind of secret (key) together with an encryption algorithm.

HLS uses AES in cipher block chaining (CBC) mode. This means each block is encrypted using the cipher text of the preceding block. [read more](http://hlsbook.net/how-to-encrypt-hls-video-with-ffmpeg/)

Before we can encrypt videos, we need an encryption key. However you can use any software that can generate key, this package requires a working OpenSSL to create the key:

``` php 
$url_to_key = "https://www.aminyazdanpanah.com/enc.key"; //Path to get the key on your website
$path_to_save_key = "/var/www/media/keys/my_key/enc.key"; //Path to save the random key on your server
$hls_key_info = new Streaming\KeyInfo($url_to_key, $path_to_save_key);
```
 - **NOTE:** It is recommended to protect your key on your website using a token or a session/cookie.

The next step is to pass key info to `setHlsKeyInfoFile` method:
``` php
Streaming\FFMpeg::create()
    ->open('/var/www/media/videos/test.mp4')
    ->HLS()
    ->X264()
    ->setHlsKeyInfoFile($hls_key_info)
    ->autoGenerateRepresentations()
    ->save('/var/www/media/videos/hls/test.m3u8');
```
- **Note:** Alternatively, you can generate a key info using another library and pass the path to the method.

### Other Advanced Features
You can easily use other advanced features in the [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) library. In fact, when you open a file with `open` method, it holds the Media object that belongs to the PHP-FFMpeg.

``` php
$ffmpeg = Streaming\FFMpeg::create()
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
$metadata = Streaming\FFMpeg::create()
    ->open('/var/www/media/videos/test.mp4')
    ->getFirstStream()
    ->all();
```

## Several Open Source Players

 - DASH and HLS on Web: [Flowplayer](https://flowplayer.com/)
    
 - DASH and HLS on Web: [Shaka Player](https://github.com/google/shaka-player)
 
 - DASH and HLS on Web: [videojs-http-streaming (VHS)](https://github.com/videojs/http-streaming)

 - DASH on Web: [dash.js](https://github.com/Dash-Industry-Forum/dash.js)
 
 - HLS on Web: [hls.js](https://github.com/video-dev/hls.js)

 - DASH and HLS on Android: [ExoPlayer](https://github.com/google/ExoPlayer)

## Contributing

I'd love your help in improving, correcting, adding to the specification.
Please [file an issue](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/issues)
or [submit a pull request](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/pulls).

Please see [Contributing File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/CONTRIBUTING.md) for more information.

## Security

If you discover a security vulnerability within this package, please send an e-mail to Amin Yazdanpanah via:
contact [AT] aminyazdanpanah • com.
## Credits

- [Amin Yazdanpanah](https://www.aminyazdanpanah.com/?u=github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming)

## License

The MIT License (MIT). Please see [License File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE) for more information.
