# 📼 PHP FFmpeg Video Streaming
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

## Get from Basic, Pro, and Enterprise packages for Video Streaming

<p align="center"><img src="https://github.com/quasarstream/quasarstream.github.io/blob/master/video-streaming/video-streaming-screen-hots.gif?raw=true" width="100%"></p>


Our service enables you to save a significant amount of time and resources, allowing you to concentrate on the essential
features of your OTT platform without worrying about time-consuming boilerplate code. Our cost-effective solution starts
at **$78**, giving you the flexibility to focus on your core competencies and accelerate your development process. By
utilizing our service, you can improve your productivity, reduce your development time, and deliver top-quality results.
Don't let the burden of writing boilerplate code slow you down; let us help you streamline your development process and
take your OTT platform to the next level.

### Project information

- **BACKEND:** PHP - Laravel v11
- **FRONTEND:** Javascript ES6 - React v18
- **CONTAINER:** Docker

### Plans

<div style="align-content: center">
<table style="margin: auto">
    <thead>
        <tr>
            <th>Features / Plans</th>
            <th>Basic</th>
            <th>Pro</th>
            <th>Enterprise</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>OAuth 2.0 (Login, Register)</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Access-control list (ACL)</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Video On-Demand</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>HLS</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>DASH</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>HLS Encryption(Single key and key rotation)</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Video Quality Settings (Choose from 144p to 4k and auto mode)</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Real-Time Progress Monitoring (progress bar to show the live upload and transcoding progress)</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Dark and light theme</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Live Streaming (From Browser Webcam, IP Cameras, Live Streaming Software)</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Custom player skin</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Subtitle</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Audio(add different audio file to stream)</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Monetization: Subscriptons/pay-per-view/ads</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Advanced Analytics: Views/Watched hours/Visited countries and more</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Robust DRM Systems: Widevine, FairPlay Streaming and PlayReady</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Social Media Integration(Like, Comment, Share videos)</td>
            <td align="center">⛔️</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Cloud CDN (Content Delivery Network to Clouds Like Amazon S3, Google Cloud Storage, Microsoft Azure and more)</td>
            <td align="center">⛔️</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Email Service</td>
            <td align="center">⛔️</td>
            <td align="center">⛔️</td>
            <td align="center">✅</td>
        </tr>
        <tr>
            <td>Support</td>
            <td align="center">3 Months</td>
            <td align="center">6 Months</td>
            <td align="center">Customizable</td>
        </tr>
        <tr>
            <td>Price</td>
            <td align="center"><strong title="One-time fee">$78</strong></td>
            <td align="center" colspan="2"><strong title="Start at $198 (monthly fee)">Custom Pricing Available</strong></td>
        </tr>
        <tr>
            <td>Get</td>
            <td align="center"> <strong><a target="_blank" href="https://quasarstream.com/video-streaming-basic?s=php&u=php-ffmpeg"> GET THE BASIC PACKAGES</a></strong> </td>
            <td align="center"  colspan="2"> <strong><a target="_blank" href="https://quasarstream.com/contact?u=php-ffmpeg"> CONTACT US</a></strong> </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th align="left" colspan="4">
                We have demos available. Please <a target="_blank" href="https://quasarstream.com/contact?u=php-ffmpeg"> CONTACT US</a> to request one.
            </th>
        </tr>
        <tr>
            <th align="left" colspan="4">
                    If you have any questions or doubts, please don't hesitate to contact Amin Yazdanpanah (admin) using <a target="_blank" href="https://aminyazdanpanah.com/?u=php-ffmpeg">this link</a>.            
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

