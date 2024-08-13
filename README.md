# 📼 PHP FFmpeg - Video Streaming
[![Total Downloads](https://img.shields.io/packagist/dt/aminyazdanpanah/php-ffmpeg-video-streaming.svg?style=flat)](https://packagist.org/packages/aminyazdanpanah/php-ffmpeg-video-streaming)

<p align="center"><img src="https://github.com/quasarstream/quasarstream.github.io/blob/master/video-streaming/video-streaming-v2.gif?raw=true" width="100%"></p>

This package utilizes **[FFmpeg](https://ffmpeg.org)**  to bundle media content for online streaming, including DASH and
HLS. Additionally, it provides the capability to implement **[DRM](https://en.wikipedia.org/wiki/Digital_rights_management)** for HLS packaging. The program offers a range of
options to open files from cloud storage and save files to cloud storage as well.

## Documentation

**[Full Documentation](https://www.quasarstream.com/op/php/ffmpeg-streaming/)** is available describing all features
and components.

## Basic Usage

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

## Get from Basic and Pro packages for Video Streaming

<p align="center"><img src="https://github.com/quasarstream/quasarstream.github.io/blob/master/video-streaming/video-streaming-screen-hots.gif?raw=true" width="100%"></p>

Our platform empowers businesses to expand their reach globally by delivering exceptional video streaming experiences. Enjoy unmatched reliability, scalability, and high-definition quality across a diverse range of devices, ensuring your content captivates audiences worldwide.


### Plans

<div style="align-content: center">
<table style="margin: auto">
    <thead>
        <tr>
            <th>Features / Plans</th>
            <th>Basic</th>
            <th>Pro</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Authentication</strong></td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Access-control list</strong> (ACL)</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Video On-Demand</strong> (HLS and DASH)</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>HLS Encryption</strong>(Single key and key rotation)</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Video Quality Settings</strong>: Manually Choose from 144p to 4k or auto mode</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Real-Time Progress Monitoring</strong>: progress bar to display the live upload and transcoding progress</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Dark and light theme</strong></td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Live Streaming</strong>: From Browser Webcam, IP Cameras, Live Streaming Software</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Bespoke player design</strong>: Crafted to perfectly align with your brand identity and user preferences.</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Add Subtitles and Audios</strong>: add different subtitle and audio files to stream</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Monetization</strong>: Subscriptons/pay-per-view/ads</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Advanced Analytics</strong>: Views/Watched hours/Visited countries and more</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Robust DRM Systems</strong>: Widevine, FairPlay Streaming and PlayReady</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Social Media Integration</strong>: Like, Comment, Share and embed videos</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Cloud-based CDN</strong>: Accelerates content delivery worldwide through integration with major cloud storage providers such as Amazon S3, Google Cloud Storage, and Microsoft Azure.</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Tailored features</strong>: We can integrate any specific functionality you require into your platform.</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td><strong>Support</strong></td>
            <td align="center">3 Months</td>
            <td align="center">Customizable</td>
        </tr>
        <tr>
            <td><strong>Online Demo</strong></td>
            <td align="center"> <strong><a target="_blank" href="https://quasarstream.com/vs-demo?s=php&u=php-ffmpeg"> See Online Demo</a></strong> </td>
            <td align="center"> <strong><a target="_blank" href="https://quasarstream.com/book-demo?u=php-ffmpeg"> Book Free Demo</a></strong> </td>
        </tr>
        <tr>
            <td><strong>Get</strong></td>
            <td align="center"> <strong><a target="_blank" href="https://quasarstream.com/video-streaming-basic?s=php&u=php-ffmpeg"> GET </a></strong> </td>
            <td align="center"> <strong><a target="_blank" href="https://quasarstream.com/contact?u=php-ffmpeg"> CONTACT US</a></strong> </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th align="left" colspan="4">
                We tailor OTT platforms to exact client specifications, offering flexible and affordable pricing.
            </th>
        </tr>
    </tfoot>
</table>
</div>

## Contributors

Your contribution is crucial to our success, regardless of its size. We appreciate your support and encourage you to
read our **[CONTRIBUTING](https://github.com/quasarstream/php-ffmpeg-video-streaming/blob/master/CONTRIBUTING.md)**
guide for detailed instructions on how to get involved. Together, we can make a significant impact.

<a href="https://github.com/quasarstream/php-ffmpeg-video-streaming/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=quasarstream/php-ffmpeg-video-streaming" />
</a>

Made with [contrib.rocks](https://contrib.rocks).

## License

The MIT License (MIT). See **[License File](https://github.com/quasarstream/php-ffmpeg-video-streaming/blob/master/LICENSE)** for more
information.

