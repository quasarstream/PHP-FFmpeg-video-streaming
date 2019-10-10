# 📼 PHP FFMPEG Video Streaming
[![Build Status](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming.svg?branch=master)](https://travis-ci.org/aminyazdanpanah/PHP-FFmpeg-video-streaming)
[![Build status](https://img.shields.io/appveyor/ci/aminyazdanpanah/PHP-FFmpeg-video-streaming/master.svg?style=flat&logo=appveyor)](https://ci.appveyor.com/project/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminyazdanpanah/PHP-FFmpeg-video-streaming/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Latest Version on Packagist](https://img.shields.io/packagist/vpre/aminyazdanpanah/PHP-FFmpeg-video-streaming?color=success)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE)

## Overview
This package provides integration with **[PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)** and packages media content for online streaming such as DASH and HLS. You can also use **[DRM](https://en.wikipedia.org/wiki/Digital_rights_management)** for HLS packaging. There are several options to open a file from clouds and save files to them as well.
- This package uses FFmpeg, so before you get started, take the time to read the **[FFmpeg documentation](https://ffmpeg.org/ffmpeg-formats.html)**.
- **[Full Documentation](https://video.aminyazdanpanah.com/)** is available describing all features and components.
- For using DRM and encryption, I **recommend** trying **[Shaka PHP](https://github.com/aminyazdanpanah/shaka-php)**, which is a great tool for this use case.

**Contents**
- [Requirements](#requirements)
- [Installation](#installation)
- [Quickstart](#quickstart)
  - [Configuration](#configuration)
  - [Opening a File](#opening-a-file)
  - [DASH](#dash)
  - [HLS](#hls)
    - [Encrypted HLS](#encrypted-hls)
  - [Transcoding](#transcoding)
  - [Saving Files](#saving-files)
  - [Metadata Extraction](#metadata-extraction)
  - [Other Advanced Features](#other-advanced-features)
- [Asynchronous Task Execution](#asynchronous-task-execution)
- [Several Open Source Players](#several-open-source-players)
- [Contributing and Reporting Bug](#contributing-and-reporting-bugs)
- [Credits](#credits)
- [License](#license)

## Requirements
1. This version of the package is only compatible with PHP 7.2 or higher.

2. To use this package, you need to install the **[FFmpeg](https://ffmpeg.org/download.html)**. You will need both FFmpeg and FFProbe binaries to use it.

## Installation
Install the package via **[composer](https://getcomposer.org/)**:
``` bash
composer require aminyazdanpanah/php-ffmpeg-video-streaming
```
Alternatively, add the dependency directly to your `composer.json` file:
``` json
"require": {
    "aminyazdanpanah/php-ffmpeg-video-streaming": "^1.1"
}
```

## Quickstart
First of all, you need to include the package in Your Code:
``` php
require 'vendor/autoload.php'; // path to the autoload file
```
**Note:** If you are using such a framework(e.g. **[Laravel](https://github.com/laravel/laravel)**) that auto-include the autoload in your code, then you can skip this step.

### Configuration
This package will autodetect FFmpeg and FFprobe binaries. If you want to give binary paths explicitly, you can pass an array as configuration. A Psr\Logger\LoggerInterface can also be passed to log binary executions.

``` php
$config = [
    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
    'ffprobe.binaries' => '/usr/bin/ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFmpeg should use
];
    
$ffmpeg = Streaming\FFMpeg::create($config);
```

### Opening a File
There are several ways to open a file:

#### 1. From a Local Path
You can pass a local path of video to the `open` method:
``` php
$video = $ffmpeg->open('/var/www/media/videos/test.mp4');
```

#### 2. From Clouds
You can open a file from a cloud by passing an array of cloud configuration to the `openFromCloud` method. There are some options to open file from **[Amazon Web Services (AWS)](https://aws.amazon.com/)**, **[Google Cloud Storage](https://console.cloud.google.com/storage)**, **[Microsoft Azure Storage](https://azure.microsoft.com/en-us/features/storage-explorer/)**, and custom cloud. 

Please go to the **[Wiki page](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/wiki/Open-a-File-From-Clouds)** to see more examples and usage of these clouds.
``` php
$video = $ffmpeg->openFromCloud($from_google_cloud);
```

### DASH
**[Dynamic Adaptive Streaming over HTTP (DASH)](http://dashif.org/)**, also known as MPEG-DASH, is an adaptive bitrate streaming technique that enables high quality streaming of media content over the Internet delivered from conventional HTTP web servers.

Similar to Apple's HTTP Live Streaming (HLS) solution, MPEG-DASH works by breaking the content into a sequence of small HTTP-based file segments, each segment containing a short interval of playback time of content that is potentially many hours in duration, such as a movie or the live broadcast of a sports event. The content is made available at a variety of different bit rates, i.e., alternative segments encoded at different bit rates covering aligned short intervals of playback time. While the content is being played back by an MPEG-DASH client, the client uses a bit rate adaptation (ABR) algorithm to automatically select the segment with the highest bit rate possible that can be downloaded in time for playback without causing stalls or re-buffering events in the playback. The current MPEG-DASH reference client dash.js offers both buffer-based (BOLA) and hybrid (DYNAMIC) bit rate adaptation algorithms. Thus, an MPEG-DASH client can seamlessly adapt to changing network conditions and provide high quality playback with fewer stalls or re-buffering events. [Learn more](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP)
 
Create DASH Files:
``` php
$video->DASH()
    ->HEVC() // Format of the video. Alternatives: X264() and VP9()
    ->autoGenerateRepresentations() // Auto generate representations
    ->setAdaption('id=0,streams=v id=1,streams=a') // Set the adaption.
    ->save(); // It can be passed a path to the method or it can be null
```
You can also create multi-representations video files using the `Representation` object:
``` php
use Streaming\Representation;

$rep_1 = (new Representation())->setKiloBitrate(800)->setResize(1280 , 720);
$rep_2 = (new Representation())->setKiloBitrate(300)->setResize(640 , 360);

$video->DASH()
    ->HEVC()
    ->addRepresentation($rep_1) // Add a representation
    ->addRepresentation($rep_2) 
    ->setAdaption('id=0,streams=v id=1,streams=a') // Set a adaption.
    ->save('/var/www/media/videos/dash/test.mpd');
```
See **[DASH options](https://ffmpeg.org/ffmpeg-formats.html#dash-2)** for more information.

### HLS
**[HTTP Live Streaming (also known as HLS)](https://developer.apple.com/streaming/)** is an HTTP-based adaptive bitrate streaming communications protocol implemented by Apple Inc. as part of its QuickTime, Safari, OS X, and iOS software. Client implementations are also available in Microsoft Edge, Firefox and some versions of Google Chrome. Support is widespread in streaming media servers.

HLS resembles MPEG-DASH in that it works by breaking the overall stream into a sequence of small HTTP-based file downloads, each download loading one short chunk of an overall potentially unbounded transport stream. A list of available streams, encoded at different bit rates, is sent to the client using an extended M3U playlist. [Learn more](https://en.wikipedia.org/wiki/HTTP_Live_Streaming)
 
Create HLS files based on original video(auto-generate qualities).
``` php
$video->HLS()
    ->X264()
    ->autoGenerateRepresentations([720, 360]) // You can limit the numbers of representatons
    ->save();
```
Generate `Representation` object(set bit-rate and size manually):
``` php
use Streaming\Representation;

$rep_1 = (new Representation())->setKiloBitrate(1000)->setResize(1280 , 720);
$rep_2 = (new Representation())->setKiloBitrate(500)->setResize(854 , 480);
$rep_3 = (new Representation())->setKiloBitrate(200)->setResize(640 , 360);

$video->HLS()
    ->X264()
    ->setHlsBaseUrl('https://bucket.s3-us-west-1.amazonaws.com/videos') // Add a base URL
    ->addRepresentation($rep_1)
    ->addRepresentation($rep_2)
    ->addRepresentation($rep_3)
    ->setHlsTime(5) // Set Hls Time. Default value is 10 
    ->setHlsAllowCache(false) // Default value is true 
    ->save();
```
**NOTE:** You cannot use HEVC and VP9 formats for HLS packaging.

#### Encrypted HLS
The encryption process requires some kind of secret (key) together with an encryption algorithm. HLS uses AES in cipher block chaining (CBC) mode. This means each block is encrypted using the ciphertext of the preceding block. [Learn more](https://en.wikipedia.org/wiki/Block_cipher_mode_of_operation)

You need to pass both `URL to the key` and `path to save a random key` to the `generateRandomKeyInfo` method:
``` php
//A path you want to save a random key on your server
$save_to = '/var/www/my_website_project/keys/enc.key';

//A URL (or a path) to access the key on your website
$url = 'https://www.aminyazdanpanah.com/keys/enc.key';// or '/keys/enc.key';

$video->HLS()
    ->X264()
    ->setTsSubDirectory('ts_files')// put all ts files in a subdirectory
    ->generateRandomKeyInfo($url, $save_to)
    ->autoGenerateRepresentations([1080, 480, 240])
    ->save('/var/www/media/videos/hls/test.m3u8');
```
**NOTE:** It is very important to protect your key on your website using a token or a session/cookie(****It is highly recommended****).    

See **[HLS options](https://ffmpeg.org/ffmpeg-formats.html#hls-2)** for more information.

### Transcoding
A format can also extend `FFMpeg\Format\ProgressableInterface` to get realtime information about the transcoding. 

``` php
$format = new Streaming\Format\HEVC();
$current_percentage = 0;

$format->on('progress', function ($video, $format, $percentage) use (&$current_percentage) {
    $percentage = intval($percentage);
    if ($current_percentage !== $percentage) {
        // You can update a field in your database or log it into a file
        // You can also create a socket connection and show the progress to users
        echo sprintf("\r Transcoding... (%s%%)[%s%s]", $percentage, str_repeat('#', $percentage), str_repeat('-', (99 - $percentage)));
        $current_percentage = $percentage;
    }
});

$video->DASH()
    ->setFormat($format)
    ->autoGenerateRepresentations()
    ->setAdaption('id=0,streams=v id=1,streams=a')
    ->save();
```

##### Output From a Terminal:
![transcoding](/docs/transcoding.gif?raw=true "transcoding" )

### Saving Files
There are several options to save your files.

#### 1. To a Local Path
You can pass a local path to the `save` method. If there was no directory in the path, then the package auto makes the directory.
``` php
$dash = $video->DASH()
            ->HEVC()
            ->autoGenerateRepresentations()
            ->setAdaption('id=0,streams=v id=1,streams=a');
            
$dash->save('/var/www/media/videos/dash/test.mpd');
```
It can also be null. The default path to save files is the input path.
``` php
$hls = $video->HLS()
            ->X264()
            ->autoGenerateRepresentations();
            
$hls->save();
```
**NOTE:** If you open a file from cloud and did not pass a path to save a file, you will have to pass a local path to the `save` method.

#### 2. To Clouds
You can save your files to a cloud by passing an array of cloud configuration to the `save` method. There are some options to save files to **[Amazon Web Services (AWS)](https://aws.amazon.com/)**, **[Google Cloud Storage](https://console.cloud.google.com/storage)**, **[Microsoft Azure Storage](https://azure.microsoft.com/en-us/features/storage-explorer/)**, and a custom cloud. 

Please go to the **[Wiki page](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/wiki/Save-Files-To-Clouds)** to see more examples and usage of these clouds.
``` php
$dash->save(null, [$to_aws_cloud, $to_google_cloud, $to_microsoft_azure, $to_custom_cloud]);
``` 
A path can also be passed to save a copy of files on your local machine.
``` php
$hls->save('/var/www/media/videos/hls/test.m3u8', [$to_google_cloud, $to_custom_cloud]);
```

**NOTE:** You can open a file from your local machine(or a cloud) and save files to a local path or a cloud(or multiple clouds).   

![schema](/docs/schema.gif?raw=true "schema" )

### Metadata Extraction
After saving files(wherever you saved them), you can extract the metadata from the video and streams. You can save these metadata to your database.
``` php
$metadata = $hls->save();

echo $metadata['filename']; // path to metadata.json
var_dump($metadata['metadata']); // dump all metadata
```
**NOTE:** It won't save metadata to clouds because of some security concerns.

### Other Advanced Features
You can easily use other advanced features in the **[PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)** library. In fact, when you open a file with the `open` method(or `openFromCloud`), it holds the Media object that belongs to the PHP-FFMpeg.
``` php
$ffmpeg = Streaming\FFMpeg::create()
$video = $$ffmpeg->openFromCloud($from_cloud, '/var/wwww/media/my/new/video.mp4');
```

#### Example(Extracting image)
You can extract a frame at any timecode using the `FFMpeg\Media\Video::frame` method.
``` php
$frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(42));
$frame->save('image.jpg');
```
To see more example, please go to the **[PHP-FFMpeg Documentation](https://github.com/PHP-FFMpeg/PHP-FFMpeg)** 

## Asynchronous Task Execution
Packaging process will may take a while and it is recommended to run it in the background(or in a cloud e.g. Google Cloud). There are some libraries that you can use.
- **[Symphony(The Console Component)](https://github.com/symfony/console):** You can use this library to create command-line commands. Your console commands can be used for any recurring task, such as cronjobs, imports, or other batch jobs. [Learn more](https://symfony.com/doc/current/components/console.html#learn-more)

- **[Laravel(Queues)](https://github.com/illuminate/queue):** If you are using Laravel for development, Laravel Queues is a wonderful tool for this use case. It allows you to create a job and dispatch it. [Learn more](https://laravel.com/docs/6.0/queues)

- **[Google Cloud Tasks](https://github.com/googleapis/google-cloud-php-tasks):** Google Cloud Tasks is a fully managed service that allows you to manage the execution, dispatch, and delivery of a large number of distributed tasks. You can asynchronously perform work outside of a user request. [Learn more](https://cloud.google.com/tasks/)

**NOTE:** It is not necessary to use these libraries. It is just a suggestion. You can also create a script to create packaged video files and run a job in the cron job.  

## Several Open Source Players
You can use these libraries to play your streams.
- **WEB**
    - DASH and HLS: **[video.js](https://github.com/videojs/video.js)**
    - DASH and HLS: **[DPlayer](https://github.com/MoePlayer/DPlayer)**
    - DASH and HLS: **[Plyr](https://github.com/sampotts/plyr)**
    - DASH and HLS: **[MediaElement.js](https://github.com/mediaelement/mediaelement)**
    - DASH and HLS: **[Clappr](https://github.com/clappr/clappr)**
    - DASH and HLS: **[Flowplayer](https://github.com/flowplayer/flowplayer)**
    - DASH and HLS: **[Shaka Player](https://github.com/google/shaka-player)**
    - DASH and HLS: **[videojs-http-streaming (VHS)](https://github.com/videojs/http-streaming)**
    - DASH: **[dash.js](https://github.com/Dash-Industry-Forum/dash.js)**
    - HLS: **[hls.js](https://github.com/video-dev/hls.js)**
- **Android**
    - DASH and HLS: **[ExoPlayer](https://github.com/google/ExoPlayer)**
- **Windows, Linux, and macOS**
    - DASH and HLS: **[VLC media player](https://github.com/videolan/vlc)**

**NOTE:** You should pass a manifest of streams(e.g. `https://www.aminyazdanpanah.com/videos/dash/lesson-1/test.mpd` or `/videos/hls/lesson-2/test.m3u8` ) to these players.

## Contributing and Reporting Bugs
I'd love your help in improving, correcting, adding to the specification.
Please **[file an issue](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/issues)** or **[submit a pull request](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/pulls)**.
- Please see **[Contributing File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/CONTRIBUTING.md)** for more information.
- If you have any questions or you want to report a bug, please just **[file an issue](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/issues)**
- If you discover a security vulnerability within this package, please see **[SECURITY File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/SECURITY.md)** for more information.

**NOTE:** If you have any questions about this package or FFmpeg, please **DO NOT** send an email to me (or submit the contact form on my website). Emails regarding these issues **will be ignored**.

## Credits
- **[Amin Yazdanpanah](https://www.aminyazdanpanah.com/?u=github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming)**

## License
The MIT License (MIT). Please see **[License File](https://github.com/aminyazdanpanah/PHP-FFmpeg-video-streaming/blob/master/LICENSE)** for more information.