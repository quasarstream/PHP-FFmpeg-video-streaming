# 📼 PHP FFmpeg Video Streaming
[![Build Status](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming.svg?branch=master)](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming)
[![Build status](https://img.shields.io/appveyor/ci/aminyazdanpanah/PHP-FFmpeg-video-streaming/master.svg?style=flat&logo=appveyor)](https://ci.appveyor.com/project/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/aminyazdanpanah/php-ffmpeg-video-streaming?color=success)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE)

## Overview
This library is a wrapper around **[PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)** and packages media content for online streaming such as DASH and HLS. You can also use **[DRM](https://en.wikipedia.org/wiki/Digital_rights_management)** for HLS packaging. There are several options to open a file from a cloud and save files to clouds as well.
- **[Full Documentation](https://video.aminyazdanpanah.com/)** is available describing all features and components.
- For using encryption and DRM, I **recommend** trying **[Shaka PHP](https://github.com/aminyazdanpanah/shaka-php)**, which is a great tool for this use case.

**Contents**
- [Requirements](#requirements)
- [Installation](#installation)
- [Quickstart](#quickstart)
  - [Configuration](#configuration)
  - [Opening a Resource](#opening-a-resource)
  - [DASH](#dash)
  - [HLS](#hls)
    - [Encryption(DRM)](#encryptiondrm)
    - [Subtitles](#subtitles)
  - [Transcoding](#transcoding)
  - [Saving Files](#saving-files)
  - [Metadata](#metadata)
  - [Conversion](#conversion)
  - [Other Advanced Features](#other-advanced-features)
- [Asynchronous Task Execution](#asynchronous-task-execution)
- [Several Open Source Players](#several-open-source-players)
- [FAQs](#faqs)
- [Contributing and Reporting Bugs](#contributing-and-reporting-bugs)
- [Credits](#credits)
- [License](#license)

## Requirements
1. This version of the package is only compatible with **[PHP 7.2](https://www.php.net/releases/)** or higher.

2. To use this package, you need to **[install the FFmpeg](https://ffmpeg.org/download.html)**. You will need both FFmpeg and FFProbe binaries to use it.

## Installation
Install the package via **[composer](https://getcomposer.org/)**:
``` bash
composer require aminyazdanpanah/php-ffmpeg-video-streaming
```
Alternatively, add the dependency directly to your `composer.json` file:
``` json
"require": {
    "aminyazdanpanah/php-ffmpeg-video-streaming": "^1.2"
}
```

## Quickstart
First of all, you need to include the package in your code:
```php
require 'vendor/autoload.php'; // path to the autoload file
```

### Configuration
This package will autodetect FFmpeg and FFprobe binaries. If you want to give binary paths explicitly, you can pass an array as configuration. A Psr\Logger\LoggerInterface can also be passed to log binary executions.

```php
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$config = [
    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
    'ffprobe.binaries' => '/usr/bin/ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFmpeg should use
];

$log = new Logger('FFmpeg_Streaming');
$log->pushHandler(new StreamHandler('/var/log/ffmpeg-streaming.log')); // path to log file
    
$ffmpeg = Streaming\FFMpeg::create($config, $log);
```

### Opening a Resource
There are several ways to open a resource.

#### 1. From an FFmpeg supported resource
You can pass a local path of video(or a supported resource) to the `open` method:
```php
$video = $ffmpeg->open('/var/media/video.mp4');
```

See **[FFmpeg Protocols Documentation](https://ffmpeg.org/ffmpeg-protocols.html)** for more information about supported resources such as HTTP, FTP, and etc.

**For example:** 
```php
$video = $ffmpeg->open('https://www.aminyazdanpanah.com/?"PATH TO A VIDEO FILE" or "PATH TO A LIVE HTTP STREAM"');
```

#### 2. From Clouds
You can open a file from a cloud by passing an array of cloud configuration to the `openFromCloud` method. 

```php
$video = $ffmpeg->openFromCloud($from_google_cloud);
```
Visit **[this page](https://video.aminyazdanpanah.com/start/clouds?r=open)** to see some examples of opening a file from **[Amazon S3](https://aws.amazon.com/s3)**, **[Google Cloud Storage](https://console.cloud.google.com/storage)**, **[Microsoft Azure Storage](https://azure.microsoft.com/en-us/features/storage-explorer/)**, and a custom cloud.

#### 3. Capture Webcam or Screen (Live Streaming)
You can pass the name of a supported, connected capture device(i.e. the name of a webcam, camera, screen and etc) to the `capture` method to stream a live media over network. 

 ```php
 $capture = $ffmpeg->capture("CAMERA NAME OR SCREEN NAME");
 ```
To list the supported, connected capture devices, see **[FFmpeg Capture Webcam](https://trac.ffmpeg.org/wiki/Capture/Webcam)** and **[FFmpeg Capture Desktop](https://trac.ffmpeg.org/wiki/Capture/Desktop)**.
 
 
### DASH
**[Dynamic Adaptive Streaming over HTTP (DASH)](http://dashif.org/)**, also known as MPEG-DASH, is an adaptive bitrate streaming technique that enables high-quality streaming of media content over the Internet delivered from conventional HTTP web servers. [Learn more](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP)

Create DASH files:
```php
$video->dash()
    ->x264() // Format of the video. Alternatives: hevc() and vp9()
    ->autoGenerateRepresentations() // Auto generate representations
    ->save(); // It can be passed a path to the method or it can be null
```
Generate representations manually:
```php
use Streaming\Representation;

$r_144p  = (new Representation)->setKiloBitrate(95)->setResize(256, 144);
$r_240p  = (new Representation)->setKiloBitrate(150)->setResize(426, 240);
$r_360p  = (new Representation)->setKiloBitrate(276)->setResize(640, 360);
$r_480p  = (new Representation)->setKiloBitrate(750)->setResize(854, 480);
$r_720p  = (new Representation)->setKiloBitrate(2048)->setResize(1280, 720);
$r_1080p = (new Representation)->setKiloBitrate(4096)->setResize(1920, 1080);
$r_2k    = (new Representation)->setKiloBitrate(6144)->setResize(2560, 1440);
$r_4k    = (new Representation)->setKiloBitrate(17408)->setResize(3840, 2160);

$video->dash()
    ->x264()
    ->addRepresentations([$r_144p, $r_240p, $r_360p, $r_480p, $r_720p, $r_1080p, $r_2k, $r_4k])
    ->save('/var/media/dash-stream.mpd');
```
See **[DASH section](https://video.aminyazdanpanah.com/start?r=dash#dash)** in the documentation, for more examples.

### HLS
**[HTTP Live Streaming (also known as HLS)](https://developer.apple.com/streaming/)** is an HTTP-based adaptive bitrate streaming communications protocol implemented by Apple Inc. as part of its QuickTime, Safari, OS X, and iOS software. Client implementations are also available in Microsoft Edge, Firefox, and some versions of Google Chrome. Support is widespread in streaming media servers. [Learn more](https://en.wikipedia.org/wiki/HTTP_Live_Streaming)

Create HLS files:
```php
$video->hls()
    ->x264()
    ->autoGenerateRepresentations([720, 360]) // You can limit the number of representatons
    ->save();
```
Generate representations manually:
```php
use Streaming\Representation;

$r_360p  = (new Representation)->setKiloBitrate(276)->setResize(640, 360);
$r_480p  = (new Representation)->setKiloBitrate(750)->setResize(854, 480);
$r_720p  = (new Representation)->setKiloBitrate(2048)->setResize(1280, 720);

$video->hls()
    ->x264()
    ->addRepresentations([$r_360p, $r_480p, $r_720p])
    ->save();
```
See **[HLS section](https://video.aminyazdanpanah.com/start?r=hls#hls)** in the documentation, for more examples such as Fragmented MP4, live from camera/screen and so on.

#### Encryption(DRM)
The encryption process requires some kind of secret (key) together with an encryption algorithm. HLS uses AES in cipher block chaining (CBC) mode. This means each block is encrypted using the ciphertext of the preceding block. [Learn more](https://en.wikipedia.org/wiki/Block_cipher_mode_of_operation)

You must specify a path to save a random key to your local machine and also specify an URL(or a path) to access the key on your website(the key you will save must be accessible from your website). You must pass both these parameters to the `encryption` method:

##### Single Key
The following code generates a key for all segment files.

```php
//A path you want to save a random key to your local machine
$save_to = '/home/public_html/"PATH TO THE KEY DIRECTORY"/key';

//An URL (or a path) to access the key on your website
$url = 'https://www.aminyazdanpanah.com/?"PATH TO THE KEY DIRECTORY"/key';
// or $url = '/"PATH TO THE KEY DIRECTORY"/key';

$video->hls()
    ->encryption($save_to, $url)
    ->x264()
    ->autoGenerateRepresentations([1080, 480, 240])
    ->save('/var/media/hls-stream.m3u8');
```

##### Key Rotation
An integer as a "key rotation period" can also be passed to the `encryption` method (i.e. `encryption($save_to, $url, 10)`) to use a different key for each set of segments, rotating to a new key after this many segments. For example, if 10 segment files have been generated then it will generate a new key. If you set this value to **`1`**, each segment file will be encrypted with a new encryption key. This can improve security and allows for more flexibility. 

See **[the example](https://video.aminyazdanpanah.com/start?r=enc-hls#hls-encryption)** for more information.

**IMPORTANT:** It is very important to protect your key(s) on your website. For example, you can use a token(using a Get or Post HTTP method) to check if the user is eligible to access the key or not. You can also use a session(or cookie) on your website to restrict access to the key(s)(**It is highly recommended**).    

##### DRM
However FFmpeg supports AES encryption for HLS packaging, which you can encrypt your content, it is not a full **[DRM](https://en.wikipedia.org/wiki/Digital_rights_management)** solution. If you want to use a full DRM solution, I recommend trying **[FairPlay Streaming](https://developer.apple.com/streaming/fps/)** solution which then securely exchange keys, and protect playback on devices.

**Besides [Apple's FairPlay](https://developer.apple.com/streaming/fps/)** DRM system, you can also use other DRM systems such as **[Microsoft's PlayReady](https://www.microsoft.com/playready/overview/)** and **[Google's Widevine](https://www.widevine.com/)**.

#### Subtitles
You can add subtitles to a HLS stream using `subtitle` method.
```php
use Streaming\HLSSubtitle;

$persian = new HLSSubtitle('/var/subtitles/subtitles_fa.vtt', 'فارسی', 'fa');
$persian->default();
$english = new HLSSubtitle('/var/subtitles/subtitles_en.vtt', 'english', 'en');
$german  = new HLSSubtitle('/var/subtitles/subtitles_de.vtt', 'Deutsch', 'de');
$chinese = new HLSSubtitle('/var/subtitles/subtitles_zh.vtt', '中文', 'zh');
$spanish = new HLSSubtitle('/var/subtitles/subtitles_es.vtt', 'Español', 'es');

$video->hls()
    ->subtitles([$persian, $english, $german, $chinese, $spanish])
    ->x264()
    ->autoGenerateRepresentations([1080, 720])
    ->save('/var/media/hls-stream.m3u8');
```
**NOTE:** All m3u8 files will be generated using rules based on **[RFC 8216](https://tools.ietf.org/html/rfc8216#section-3.5)**. Only **[WebVTT](https://www.w3.org/TR/webvtt1/)** files are acceptable for now. 

### Transcoding
A format can also extend `FFMpeg\Format\ProgressableInterface` to get realtime information about the transcoding. 
```php
$format = new Streaming\Format\X264();
$format->on('progress', function ($video, $format, $percentage){
    // You can update a field in your database or can log it to a file
    // You can also create a socket connection and show a progress bar to users
    echo sprintf("\rTranscoding...(%s%%) [%s%s]", $percentage, str_repeat('#', $percentage), str_repeat('-', (100 - $percentage)));
});

$video->dash()
    ->setFormat($format)
    ->autoGenerateRepresentations()
    ->save();
```

##### Output From a Terminal:
![transcoding](https://github.com/aminyazdanpanah/aminyazdanpanah.github.io/blob/master/video-streaming/transcoding.gif?raw=true "transcoding" )

### Saving Files
There are several ways to save files.

#### 1. To a Local Path
You can pass a local path to the `save` method. If there was no directory in the path, then the package auto makes the directory.
```php
$dash = $video->dash()
            ->x264()
            ->autoGenerateRepresentations()
            
$dash->save('/var/media/dash-stream.mpd');
```
It can also be null. The default path to save files is the input path.
```php
$hls = $video->hls()
            ->x264()
            ->autoGenerateRepresentations();
            
$hls->save();
```
**NOTE:** If you open a file from a cloud and do not pass a path to save the file to your local machine, you will have to pass a local path to the `save` method.

#### 2. To Clouds
You can save your files to a cloud by passing an array of cloud configuration to the `save` method. 

```php
$dash->save(null, [$to_aws_cloud, $to_google_cloud, $to_microsoft_azure, $to_custom_cloud]);
``` 
A path can also be passed to save a copy of files to your local machine.
```php
$hls->save('/var/media/hls-stream.m3u8', [$to_google_cloud, $to_custom_cloud]);
```

Visit **[this page](https://video.aminyazdanpanah.com/start/clouds?r=save)** to see some examples of saving files to **[Amazon S3](https://aws.amazon.com/s3)**, **[Google Cloud Storage](https://console.cloud.google.com/storage)**, **[Microsoft Azure Storage](https://azure.microsoft.com/en-us/features/storage-explorer/)**, and a custom cloud. 

**NOTE:** This option(Save To Clouds) is only valid for **[VOD](https://en.wikipedia.org/wiki/Video_on_demand)** (it does not support live streaming).

**Schema:** The relation is `one-to-many`

<p align="center"><img src="https://github.com/aminyazdanpanah/aminyazdanpanah.github.io/blob/master/video-streaming/video-streaming.gif?raw=true" width="100%"></p>

#### 3. To a Server Instantly
You can pass a URL(or a supported resource like `FTP`) to live method to upload all the segments files to the HTTP server(or other protocols) using the HTTP PUT method and update the manifest files every refresh times.

```php
// DASH
$dash->live('http://YOUR-WEBSITE.COM/live-stream/out.mpd');

// HLS
$hls
    ->setMasterPlaylist('/var/www/stream/live-master-manifest.m3u8')
    ->live('http://YOUR-WEBSITE.COM/live-stream/out.m3u8');
```
**NOTE:** In the HLS format, you must upload the master playlist to the server manually. (Upload the `/var/www/stream/live-master-manifest.m3u8` file to the `http://YOUR-WEBSITE.COM`)

See **[FFmpeg Protocols Documentation](https://ffmpeg.org/ffmpeg-protocols.html)** for more information.

### Metadata
You can get information from multimedia streams and the video file using the following code.
```php
$hls = $hls->save();
$metadata = $hls->metadata()->export();

print_r($metadata);
```

See **[the example](https://video.aminyazdanpanah.com/start?r=metadata#metadata)** for more information.

### Conversion
You can convert your stream to a file or to another stream protocols. You should pass a manifest of the stream to the `open` method:

#### 1. HLS To DASH
```php
$stream = $ffmpeg->open('https://www.aminyazdanpanah.com/?PATH/TO/HLS-MANIFEST.M3U8');

$stream->dash()
    ->x264()
    ->addRepresentations([$r_360p, $r_480p]) 
    ->save('/var/media/dash-stream.mpd');
```

#### 2. DASH To HLS
```php
$stream = $ffmpeg->open('https://www.aminyazdanpanah.com/?PATH/TO/DASH-MANIFEST.MPD');

$stream->hls()
           ->x264()
           ->autoGenerateRepresentations([720, 360])
           ->save('/var/media/hls-stream.m3u8');
```

#### 3. Stream(DASH or HLS) To File
```php
$format = new Streaming\Format\x264();
$format->on('progress', function ($video, $format, $percentage){
    echo sprintf("\rTranscoding...(%s%%) [%s%s]", $percentage, str_repeat('#', $percentage), str_repeat('-', (100 - $percentage)));
});

$stream->stream2file()
           ->setFormat($format)
           ->save('/var/media/new-video.mp4');
```

### Other Advanced Features
You can easily use other advanced features in the **[PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)** library. In fact, when you open a file with the `open` method(or `openFromCloud`), it holds the Media object that belongs to the PHP-FFMpeg.
```php
$ffmpeg = Streaming\FFMpeg::create();
$video = $ffmpeg->openFromCloud($from_cloud, '/var/media/new/video.mp4');
```

#### Extracting image
You can extract a frame at any timecode using the `FFMpeg\Media\Video::frame` method.
```php
$frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(42));
$frame->save('/var/media/poster.jpg');
```
**NOTE:** You can use the image as a video's poster.

#### Gif
A gif is an animated image extracted from a sequence of the video.

You can save gif files using the FFMpeg\Media\Gif::save method.

```php
$video
    ->gif(FFMpeg\Coordinate\TimeCode::fromSeconds(2), new FFMpeg\Coordinate\Dimension(640, 480), 3)
    ->save('/var/media/animated_image.gif');
```
This method has a third optional boolean parameter, which is the duration of the animation. If you don't set it, you will get a fixed gif image.

**NOTE:** You can use the gif as a video's thumbnail.

To see more examples, visit the **[PHP-FFMpeg Documentation](https://github.com/PHP-FFMpeg/PHP-FFMpeg)**  page.

## Asynchronous Task Execution
The packaging process might take a while and it is recommended to run it in the background(or in a cloud e.g. AWS). There are some libraries that you can use for this use case.
- **[Symphony(The Console Component)](https://github.com/symfony/console):** You can use this library to create command-line commands. Your console commands can be used for any recurring task, such as cronjobs, imports, or other batch jobs. [Learn more](https://symfony.com/doc/current/components/console.html#learn-more)

- **[Laravel(Queues)](https://github.com/illuminate/queue):** If you are using Laravel for development, Laravel Queues is a wonderful tool for this use case. It allows you to create a job and dispatch it. [Learn more](https://laravel.com/docs/7.x/queues)

**NOTE:** You can also create a script and run it in your cronjob.  

## Several Open Source Players
You can use these libraries to play your streams.
- **WEB**
    - DASH and HLS: 
        - **[Video.js 7](https://github.com/videojs/video.js) (Recommended) - [videojs-http-streaming (VHS)](https://github.com/videojs/http-streaming)**
        - **[Plyr](https://github.com/sampotts/plyr)**
        - **[DPlayer](https://github.com/MoePlayer/DPlayer)**
        - **[MediaElement.js](https://github.com/mediaelement/mediaelement)**
        - **[Clappr](https://github.com/clappr/clappr)**
        - **[Shaka Player](https://github.com/google/shaka-player)**
        - **[Flowplayer](https://github.com/flowplayer/flowplayer)**
    - DASH:
        - **[dash.js](https://github.com/Dash-Industry-Forum/dash.js)**
    - HLS: 
        - **[hls.js](https://github.com/video-dev/hls.js)**
- **Android**
    - DASH and HLS: 
        - **[ExoPlayer](https://github.com/google/ExoPlayer) (Recommended)**
        - **[VLC for Android](https://github.com/videolan/vlc-android)**
- **IOS**
    - DASH: 
        - **[MPEGDASH-iOS-Player](https://github.com/MPEGDASHPlayer/MPEGDASH-iOS-Player)**
    - HLS: 
        - **[Player](https://github.com/piemonte/Player)**
- **Android and IOS**
    - DASH and HLS:
        - **[ijkplayer](https://github.com/bilibili/ijkplayer)**
- **Windows, Linux, and macOS**
    - DASH and HLS:
        - **[FFmpeg(ffplay)](https://github.com/FFmpeg/FFmpeg) (Recommended)**
        - **[VLC media player](https://github.com/videolan/vlc)**

## FAQs
**I created stream files and now what should I pass to a player?**
You must pass a **master playlist(manifest) URL**(e.x. `https://www.aminyazdanpanah.com/?"PATH TO STREAM DIRECTORY"/dash-stream.mpd` or `/PATH_TO_STREAM_DIRECTORY/hls-stream.m3u8` ) to a player. 
See the demo page of these players for more information(**[hls.js Demo](https://hls-js.netlify.app/demo/)**, **[dash.js Demo](https://reference.dashif.org/dash.js/v3.1.2/samples/dash-if-reference-player/index.html)**, **[videojs Demo](https://videojs.com/advanced?video=elephantsdream)** and etc).  

**My player does not show the quality selector button to change the video quality?**
Some Players do not have an embedded quality selector button to change this option and you should install(or add) the plugin for this use case. For example, if you are using Videojs to play your stream, you can install **[videojs-hls-quality-selector](https://github.com/chrisboustead/videojs-hls-quality-selector)** to show the quality selector. For adding a plugin to other players, you can easily Google it!

**I uploaded my stream files to a cloud but it does not play on my website?**
If you save your stream content to a cloud(i.e. **[Amazon S3](https://aws.amazon.com/s3)**), make sure your contents are **PUBLIC**. If they are not, you must change the access control. 

**Does [IOS](https://www.apple.com/ios) support the DASH stream?**
No, IOS does not have native support for DASH. Although there are some libraries such as **[Viblast](https://github.com/Viblast/ios-player-sdk)** and **[MPEGDASH-iOS-Player](https://github.com/MPEGDASHPlayer/MPEGDASH-iOS-Player)** to support this technique, I have never tested them. So maybe some of them will not work properly.

See [this page](https://video.aminyazdanpanah.com/start?r=faq#faq) for more FAQs.

## Contributing and Reporting Bugs
I'd love your help in improving, correcting, adding to the specification. Please **[file an issue](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/issues)** or **[submit a pull request](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/pulls)**.
- See **[Contributing File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/CONTRIBUTING.md)** for more information.
- If you discover a security vulnerability within this package, please see **[SECURITY File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/SECURITY.md)** for more information.

## Credits
- **[Amin Yazdanpanah](https://www.aminyazdanpanah.com/?u=github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming)**
- **[All Contributors](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/graphs/contributors)**

## License
The MIT License (MIT). See **[License File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE)** for more information.
