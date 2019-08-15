# 📼 PHP FFMPEG Video Streaming

[![Build Status](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming.svg?branch=master)](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming)
[![Build status](https://img.shields.io/appveyor/ci/aminyazdanpanah/PHP-FFmpeg-video-streaming/master.svg?style=flat-square&logo=appveyor)](https://ci.appveyor.com/project/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Total Downloads](https://img.shields.io/packagist/dt/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat-square)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat-square)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)

## Overview
This package provides an integration with [PHP-FFmpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) and packages well-known live streaming techniques such as DASH and HLS. Also you can use DRM for HLS packaging.

- Before you get started, please read the FFMpeg Document found **[here](https://ffmpeg.org/ffmpeg-formats.html)**.
- **[Full Documentation](https://video.aminyazdanpanah.com/)** is available describing all features and components.
- For DRM and encryption(DASH and HLS), I **strongly recommend** to try **[Shaka PHP](https://github.com/aminyazdanpanah/shaka-php)**, which is a great tool for this use case.

**Contents**
- [Installation](#installation)
  - [Required Libraries](#required-libraries)
  - [Installing Package](#installing-package)
- [Usage](#usage)
  - [Configuration](#configuration)
  - [Opening a File](#opening-a-file)
  - [DASH](#dash)
  - [HLS](#hls)
    - [Create HLS Files](#create-hls-files)
    - [Encrypted HLS](#encrypted-hls)
  - [Transcoding](#transcoding)
  - [Save to Amazon S3](#)
  - [Other Advanced Features](#other-advanced-features)
- [Several Open Source Players](#several-open-source-players)
- [Contributing](#contributing)
- [Security](#security)
- [Reporting Bugs](#reporting-bugs)
- [Credits](#credits)
- [License](#license)


## Installation

### Required Libraries

This library requires a working FFMpeg. You will need both FFMpeg and FFProbe binaries to use it.
- Getting FFmpeg: https://ffmpeg.org/download.html

Also, for HLS encryption you will need a working OpenSSL:
- Getting OpenSSL: https://www.openssl.org/source/
- Getting OpenSSL(Windows): https://slproweb.com/products/Win32OpenSSL.html


### Installing Package
This version of the package is only compatible with PHP 7.1.0 and later.

Install the package via composer:

``` bash
composer require aminyazdanpanah/php-ffmpeg-video-streaming
```

## Usage
You can find Full Documentation **[here](https://video.aminyazdanpanah.com/).**

### Configuration

FFMpeg will autodetect ffmpeg and ffprobe binaries. If you want to give binary paths explicitly, you can pass an array as configuration. A Psr\Logger\LoggerInterface can also be passed to log binary executions.

``` php
$config = [
    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
    'ffprobe.binaries' => '/usr/bin/ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
];
    
$ffmpeg = Streaming\FFMpeg::create($config);
```

### Opening a File

There are three ways to open a file:

#### 1. From Local
You can pass a local path of video to the `open` method:

``` php
$video = $ffmpeg->open('/var/www/media/videos/test.mp4');
```

#### 2. From Cloud
You can open a file by passing a URL to `fromURL` method:

``` php
$video = $ffmpeg->fromURL("https://www.aminyazdanpanah.com/my_sweetie.mp4");
```

Also, the path to save the file, the method of request, and [request options](http://docs.guzzlephp.org/en/stable/request-options.html) can be passed to the method.

#### 3. From Amazon S3
Amazon S3 or Amazon Simple Storage Service is a service offered by [Amazon Web Services (AWS)](https://aws.amazon.com/) that provides object storage through a web service interface. [learn more](https://en.wikipedia.org/wiki/Amazon_S3)

- For getting credentials, you need to have an AWS account or you can [create one](https://portal.aws.amazon.com/billing/signup#/start).
- Before you get started, please read the "AWS SDK for PHP" Document found **[here](https://aws.amazon.com/sdk-for-php/)**.

For downloading a file from Amazon S3, you need to pass an array as configuration, name of the bucket, and the key of your bucket to `fromS3` method:

``` php
$config = [
    'version' => 'latest',
    'region' => 'us-west-1',
    'credentials' => [
        'key' => 'my-access-key-id',
        'secret' => 'my-secret-access-key',
    ]
];

$bucket = "my-bucket-name";
$key = "/videos/my_sweetie.mp4";

$video = $ffmpeg->fromS3($config, $bucket, $key);
```

A path can also be passed to save the file on your local computer/server.

### DASH
**[Dynamic Adaptive Streaming over HTTP (DASH)](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP)**, also known as MPEG-DASH, is an adaptive bitrate streaming technique that enables high quality streaming of media content over the Internet delivered from conventional HTTP web servers.

Similar to Apple's HTTP Live Streaming (HLS) solution, MPEG-DASH works by breaking the content into a sequence of small HTTP-based file segments, each segment containing a short interval of playback time of content that is potentially many hours in duration, such as a movie or the live broadcast of a sports event. The content is made available at a variety of different bit rates, i.e., alternative segments encoded at different bit rates covering aligned short intervals of playback time. While the content is being played back by an MPEG-DASH client, the client uses a bit rate adaptation (ABR) algorithm to automatically select the segment with the highest bit rate possible that can be downloaded in time for playback without causing stalls or re-buffering events in the playback. The current MPEG-DASH reference client dash.js offers both buffer-based (BOLA) and hybrid (DYNAMIC) bit rate adaptation algorithms. Thus, an MPEG-DASH client can seamlessly adapt to changing network conditions and provide high quality playback with fewer stalls or re-buffering events. [Learn more](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP)
 
#### Create DASH Files
``` php
$video->DASH()
    ->HEVC() // Format of the video. Alternatives: X264() and VP9()
    ->autoGenerateRepresentations() // Auto generate representations
    ->setAdaption('id=0,streams=v id=1,streams=a') // Set the adaption.
    ->save(); // It can be passed a path to the method or it can be null
```

Also, You can create multi-representations video files using `Representation` object:

``` php
$rep_1 = (new Representation())->setKiloBitrate(800)->setResize(1080 , 720);
$rep_2 = (new Representation())->setKiloBitrate(300)->setResize(640 , 360);

$video->DASH()
    ->HEVC()
    ->addRepresentation($rep_1) // Add a representation
    ->addRepresentation($rep_2) // Add a representation
    ->setAdaption('id=0,streams=v id=1,streams=a') // Set a adaption.
    ->save('/var/www/media/videos/dash/test.mpd'); // It can be passed a path to the method or it can be null
```

For more information about **[FFMpeg](https://ffmpeg.org/)** and its **[dash options](https://ffmpeg.org/ffmpeg-formats.html#dash-2)** please visit its website.

### HLS

**[HTTP Live Streaming (also known as HLS)](https://en.wikipedia.org/wiki/HTTP_Live_Streaming)** is an HTTP-based adaptive bitrate streaming communications protocol implemented by Apple Inc. as part of its QuickTime, Safari, OS X, and iOS software. Client implementations are also available in Microsoft Edge, Firefox and some versions of Google Chrome. Support is widespread in streaming media servers.

HLS resembles MPEG-DASH in that it works by breaking the overall stream into a sequence of small HTTP-based file downloads, each download loading one short chunk of an overall potentially unbounded transport stream. A list of available streams, encoded at different bit rates, is sent to the client using an extended M3U playlist. [Learn more](https://en.wikipedia.org/wiki/HTTP_Live_Streaming)
 
#### Create HLS Files
Create HLS files based on original video(auto generate qualities).
``` php
$video->HLS()
    ->X264()
    ->autoGenerateRepresentations() // Auto generate representations
    ->save(); // It can be passed a path to the method or it can be null
```

Create multi-qualities video files using `Representation` object(set bit-rate and size manually):

``` php
$rep_1 = (new Representation())->setKiloBitrate(1000)->setResize(1080 , 720);
$rep_2 = (new Representation())->setKiloBitrate(500)->setResize(640 , 360);
$rep_3 = (new Representation())->setKiloBitrate(200)->setResize(480 , 270);

$video->HLS()
    ->X264() 
    ->addRepresentation($rep_1) // Add a representation
    ->addRepresentation($rep_2) // Add a representation
    ->addRepresentation($rep_3) // Add a representation
    ->setHlsTime(5) // Set Hls Time. Default value is 10 
    ->setHlsAllowCache(false) // Default value is true 
    ->save();
```

**NOTE:** You cannot use HEVC and VP9 formats for HLS packaging.

See [HLS options](https://ffmpeg.org/ffmpeg-formats.html#hls-2) for more information.

#### Encrypted HLS

The encryption process requires some kind of secret (key) together with an encryption algorithm.

HLS uses AES in cipher block chaining (CBC) mode. This means each block is encrypted using the cipher text of the preceding block. [Learn more](https://en.wikipedia.org/wiki/Block_cipher_mode_of_operation)

Before we can encrypt videos, we need an encryption key. However you can use any software that can generate key, this package requires a working OpenSSL to create the key:

Getting OpenSSL: https://www.openssl.org/source/

Getting OpenSSL(Windows): https://slproweb.com/products/Win32OpenSSL.html

You need to pass both 'URL to the key' and 'path to save a random key' to `generateRandomKeyInfo` method:
``` php
//A path you want to save a random key on your server
$save_to = "/var/www/my_website_project/storage/keys/enc.key";

//A URL (or a path) to access the key on your website
$url = "https://www.aminyazdanpanah.com/keys/enc.key";// or "/keys/enc.key";

$video->HLS()
    ->X264()
    ->generateRandomKeyInfo($url, $save_to)
    ->autoGenerateRepresentations()
    ->save('/var/www/media/videos/hls/test.m3u8');
```
- **Note:** Alternatively, you can generate a key info using another library and pass the path of key info to the `setHlsKeyInfoFile` method.
- **NOTE:** It is very important to protect your key on your website using a token or a session/cookie(****It is highly recommended****).    
- **NOTE:** For getting the benefit of the OpenSSL binary detection in windows, you need to add it to your system path otherwise, you have to pass the path to OpenSSL binary to the `generateRandomKeyInfo` method explicitly. 

### Transcoding

You can transcode videos using the `on` method in the format class.
 
 Transcoding progress can be monitored in realtime, see Format documentation in [FFMpeg documentation](https://github.com/PHP-FFMpeg/PHP-FFMpeg#documentation) for more information.

``` php
$format = new Streaming\Format\HEVC();

$format->on('progress', function ($video, $format, $percentage) {
    echo "$percentage% is transcoded\n";
});

$video->DASH()
    ->setFormat($format)
    ->autoGenerateRepresentations()
    ->setAdaption('id=0,streams=v id=1,streams=a')
    ->save();
```

HLS Transcoding:

``` php
$format = new Streaming\Format\X264();

$format->on('progress', function ($video, $format, $percentage) {
    echo "$percentage% is transcoded\n";
});

$video->HLS()
    ->setFormat($format)
    ->autoGenerateRepresentations()
    ->save('/var/www/media/videos/dash/test.m3u8');
```

### Save to Amazon S3
You can save and upload entire packaged video files to [Amazon S3](https://aws.amazon.com/). For uploading files, you need to have credentials.

``` php
$config = [
    'version' => 'latest',
    'region' => 'us-west-1',
    'credentials' => [
        'key' => 'my-access-key-id',
        'secret' => 'my-secret-access-key',
    ]
];

$dest = 's3://bucket'; 
```

Upload DASH files to Amazon Simple Storage Service:
``` php
$video->DASH()
    ->HEVC()
    ->autoGenerateRepresentations()
    ->setAdaption('id=0,streams=v id=1,streams=a')
    ->saveToS3($config, $dest);
```
A filename can also be passed to save files on your local computer/server.

``` php
$video->HLS()
    ->X264()
    ->autoGenerateRepresentations()
    ->saveToS3($config, $dest, '/var/www/media/videos/dash/test.m3u8');
```

For more information, please read [AWS SDK for PHP](https://aws.amazon.com/sdk-for-php/) document.

### Other Advanced Features
You can easily use other advanced features in the [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) library. In fact, when you open a file with `open` method(or `fromURL`), it holds the Media object that belongs to the PHP-FFMpeg.

For exploring other advanced features, please read the  [Full PHP-FFMpeg Documentation](https://github.com/PHP-FFMpeg/PHP-FFMpeg#documentation).

``` php
$ffmpeg = Streaming\FFMpeg::create()
$video = $$ffmpeg->fromURL("https://www.aminyazdanpanah.com/my_sweetie.mp4", "/var/wwww/media/my/new/video.mp4");
```

#### Example(Extracting image)
You can extract a frame at any timecode using the `FFMpeg\Media\Video::frame` method.

``` php
$video
    ->filters()
    ->extractMultipleFrames(FFMpeg\Filters\Video\ExtractMultipleFramesFilter::FRAMERATE_EVERY_10SEC, '/path/to/destination/folder/')
    ->synchronize();

$video
    ->save(new FFMpeg\Format\Video\X264(), '/path/to/new/file');
```

## Several Open Source Players
You can use these players to play your packaged videos
- **WEB**
    - DASH and HLS: [Plyr](https://github.com/sampotts/plyr)
    - DASH and HLS: [MediaElement.js](https://github.com/mediaelement/mediaelement)
    - DASH and HLS: [Clappr](https://github.com/clappr/clappr)
    - DASH and HLS: [Flowplayer](https://flowplayer.com/)
    - DASH and HLS: [Shaka Player](https://github.com/google/shaka-player)
    - DASH and HLS: [videojs-http-streaming (VHS)](https://github.com/videojs/http-streaming)
    - DASH: [dash.js](https://github.com/Dash-Industry-Forum/dash.js)
    - HLS: [hls.js](https://github.com/video-dev/hls.js)
    
- **Android**
    - DASH and HLS: [ExoPlayer](https://github.com/google/ExoPlayer)
    
## Contributing

I'd love your help in improving, correcting, adding to the specification.
Please [file an issue](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/issues)
or [submit a pull request](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/pulls).

Please see [Contributing File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/CONTRIBUTING.md) for more information.

## Security

If you discover a security vulnerability within this package, please send an e-mail to Amin Yazdanpanah via:
contact [AT] aminyazdanpanah • com.

## Reporting Bugs
Please for reporting bugs just [file an issue](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/issues).

## Credits

- [Amin Yazdanpanah](https://www.aminyazdanpanah.com/?u=github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming)

## License

The MIT License (MIT). Please see [License File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE) for more information.
