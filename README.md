DeviceDetector
==============

[![Latest Stable Version](https://poser.pugx.org/piwik/device-detector/v/stable)](https://packagist.org/packages/piwik/device-detector)
[![Latest Unstable Version](https://poser.pugx.org/piwik/device-detector/v/unstable)](https://packagist.org/packages/piwik/device-detector)
[![Total Downloads](https://poser.pugx.org/piwik/device-detector/downloads)](https://packagist.org/packages/piwik/device-detector)
[![License](https://poser.pugx.org/piwik/device-detector/license)](https://packagist.org/packages/piwik/device-detector)

## Code Status

[![Build Status](https://travis-ci.org/piwik/device-detector.svg?branch=master)](https://travis-ci.org/piwik/device-detector)
[![Code Coverage](https://coveralls.io/repos/piwik/device-detector/badge.png)](https://coveralls.io/r/piwik/device-detector)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/piwik/device-detector.svg)](http://isitmaintained.com/project/piwik/device-detector "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/piwik/device-detector.svg)](http://isitmaintained.com/project/piwik/device-detector "Percentage of issues still open")
[![Dependency Status](https://gemnasium.com/piwik/device-detector.svg)](https://gemnasium.com/piwik/device-detector)

## Description

The Universal Device Detection library, that parses User Agents and detects devices (desktop, tablet, mobile, tv, cars, console, etc.), and detects clients (browsers, feed readers, media players, PIMs, ...), operating systems, devices, brands and models.

## Usage

Using DeviceDetector with composer is quite easy. Just add piwik/device-detector to your projects requirements. And use some code like this one:


```php
require_once 'vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

// OPTIONAL: Set version truncation to none, so full versions will be returned
// By default only minor versions will be returned (e.g. X.Y)
// for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

$dd = new DeviceDetector($userAgent);

// OPTIONAL: Set caching method
// By default static cache is used, which works best within one php process (memory array caching)
// To cache across requests use caching in files or memcache
$dd->setCache(new Doctrine\Common\Cache\PhpFileCache('./tmp/'));

// OPTIONAL: If called, getBot() will only return true if a bot was detected  (speeds up detection a bit)
$dd->discardBotInformation();

// OPTIONAL: If called, bot detection will completely be skipped (bots will be detected as regular devices then)
$dd->skipBotDetection();

$dd->parse();

if ($dd->isBot()) {
  // handle bots,spiders,crawlers,...
  $botInfo = $dd->getBot();
} else {
  $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
  $osInfo = $dd->getOs();
  $device = $dd->getDevice();
  $brand = $dd->getBrand();
  $model = $dd->getModel();
}
```

### Caching

:exclamation: Caching of DeviceDetector was completely redesigned in 3.0. You may need to reimplement it when updating from below.

In order to get results faster across requests, we recommend to use the additional caching possibility.
Currently DeviceDetector is able to use [doctrine/cache](https://github.com/doctrine/cache). You can simply require it in your composer.json and use it like in the example before.
For those who like to implement their own Caching there is a second possibility. Besides doctrine caches the ```setCache``` method also accepts classes implementing the ```DeviceDetector\Cache\Cache``` interface. That way you can do whatever you want without requiring doctrine/cache.

## Contributing

### Hacking the library

This is a free/libre library under license LGPL v3 or later.

Your pull requests and/or feedback is very welcome!

### Listing all user agents from your logs
Sometimes it may be useful to generate the list of most used user agents on your website,
extracting this list from your access logs using the following command:

```
zcat ~/path/to/access/logs* | awk -F'"' '{print $6}' | sort | uniq -c | sort -rn | head -n20000 > /home/piwik/top-user-agents.txt
```

### Contributors
Created by the [Piwik team](http://piwik.org/team/), Stefan Giehl, Matthieu Aubry, Michał Gaździk,
Tomasz Majczak, Grzegorz Kaszuba, Piotr Banaszczyk and contributors.

Together we can build the best Device Detection library.

We are looking forward to your contributions and pull requests!

## Tests

See also: [QA at Piwik](http://piwik.org/qa/)

### Running tests

```
cd /path/to/device-detector
curl -sS https://getcomposer.org/installer | php
php composer.phar install
phpunit
```

## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: 2016/03/27*

### List of detected operating systems:

AIX, Android, AmigaOS, Apple TV, Arch Linux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Brew, CentOS, Chrome OS, CyanogenMod, Debian, DragonFly, Fedora, Firefox OS, FreeBSD, Gentoo, Google TV, HP-UX, Haiku OS, IRIX, Inferno, Knoppix, Kubuntu, GNU/Linux, Lubuntu, VectorLinux, Mac, Maemo, Mandriva, MeeGo, MocorDroid, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, Nintendo, Nintendo Mobile, OS/2, OSF1, OpenBSD, PlayStation Portable, PlayStation, Red Hat, RISC OS, RazoDroiD, Sabayon, SUSE, Sailfish OS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, ThreadX, Tizen, Ubuntu, WebTV, Windows, Windows CE, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOs, iOS, palmOS, webOS

### List of detected browsers:

360 Phone Browser, 360 Browser, Avant Browser, ABrowse, ANT Fresco, ANTGalio, Amaya, Amigo, Android Browser, Arora, Amiga Voyager, Amiga Aweb, Atomic Web Browser, BlackBerry Browser, Baidu Browser, Baidu Spark, Beonex, Bunjalloo, Brave, BrowseX, Camino, Coc Coc, Comodo Dragon, Charon, Chrome Frame, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, CoolNovo, CometBird, ChromePlus, Chromium, Cheshire, Deepnet Explorer, Dolphin, Dillo, Elinks, Element Browser, Epiphany, Espial TV Browser, Firebird, Fluid, Fennec, Firefox, Flock, Fireweb, Fireweb Navigator, Galeon, Google Earth, HotJava, Iceape, IBrowse, iCab, IceDragon, Iceweasel, Internet Explorer, IE Mobile, Iron, Jasmine, Jig Browser, Kindle Browser, K-meleon, Konqueror, Kapiko, Kylo, Kazehakase, Liebao, LG Browser, Links, LuaKit, Lunascape, Lynx, MicroB, NCSA Mosaic, Mercury, Mobile Safari, Midori, MIUI Browser, Mobile Silk, Maxthon, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, NetFront, NetFront Life, NetPositive, Netscape, Obigo, Odyssey Web Browser, Off By One, ONE Browser, Opera Mini, Opera Mobile, Opera, Opera Next, Oregano, Openwave Mobile Browser, OmniWeb, Otter Browser, Palm Blazer, Pale Moon, Palm Pre, Puffin, Palm WebPro, Phoenix, Polaris, Microsoft Edge, QQ Browser, Rekonq, RockMelt, Sailfish Browser, SEMC-Browser, Sogou Explorer, Safari, Shiira, Skyfire, Seraphic Sraf, Sleipnir, SeaMonkey, Snowshoe, Sunrise, SuperBird, Swiftfox, Tizen Browser, UC Browser, Vivaldi, Vision Mobile Browser, WebPositive, wOSBrowser, WeTab Browser, Yandex Browser, Xiino

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Presto, Gecko, KHTML, NetFront, Edge

### List of detected libraries:

curl, Guzzle (PHP HTTP Client), Java, Perl, Python Requests, Python urllib, Wget

### List of detected media players:

Banshee, Boxee, Clementine, FlyCast, Instacast, iTunes, Kodi, MediaMonkey, Miro, NexPlayer, Nightingale, QuickTime, Songbird, Stagefright, SubStream, VLC, Winamp, Windows Media Player, XBMC

### List of detected mobile apps:

AndroidDownloadManager, Facebook, FeedR, Google Play Newsstand, Google Plus, Line, Pinterest, Sina Weibo, WeChat, WhatsApp, YouTube  and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Barca, Lotus Notes, MailBar, Microsoft Outlook, Outlook Express, Postbox, The Bat!, Thunderbird

### List of detected feed readers:

Akregator, Apple PubSub, FeedDemon, Feeddler RSS Reader, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, Pulp, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

3Q, Acer, Ainol, Airness, Alcatel, Allview, Altech UEC, Arnova, Amazon, Amoi, Apple, Archos, ARRIS, Airties, Asus, Avvio, Audiovox, Axxion, BBK, Becker, Bird, Beetel, Bmobile, Barnes & Noble, BangOlufsen, BenQ, BenQ-Siemens, Blu, Boway, bq, Brondi, Bush, CUBOT, Carrefour, Captiva, Casio, Cat, Celkon, ConCorde, Changhong, Cherry Mobile, Cricket, Crosscall, Compal, CnM, Crius Mea, CreNova, Capitel, Compaq, Coolpad, Cowon, Cube, Coby Kyros, Danew, Datang, Denver, Desay, Dbtel, DoCoMo, Dicam, Dell, DNS, DMM, Doogee, Doov, Dopod, Dune HD, E-Boda, EBEST, Ericsson, ECS, Ezio, Elephone, Easypix, Energy Sistem, Ericy, Eton, eTouch, Evertek, Ezze, Fairphone, Fly, Foxconn, Fujitsu, Garmin-Asus, Gateway, Gemini, Gionee, Gigabyte, Gigaset, GOCLEVER, Goly, Google, Gradiente, Grundig, Haier, Hasee, Hisense, Hi-Level, Hosin, HP, HTC, Huawei, Humax, Hyrican, Hyundai, Ikea, iBall, i-Joy, iBerry, iKoMo, i-mate, iOcean, iNew, Infinix, Innostream, Inkti, Intex, i-mobile, INQ, Intek, Inverto, iTel, Jiayu, Jolla, Karbonn, KDDI, Kingsun, Konka, Komu, Koobee, K-Touch, KT-Tech, KOPO, Koridy, Kumai, Kyocera, Kazam, Lava, Lanix, LCT, Lenovo, Lenco, Le Pan, LG, Lingwin, Loewe, Logicom, Lexibook, Majestic, Manta Multimedia, Mobistel, Mecer, Medion, MEEG, Meizu, Metz, MEU, MicroMax, Mediacom, MediaTek, Mio, Mpman, Mofut, Motorola, Microsoft, MSI, Memup, Mitsubishi, MLLED, M.T.T., MyPhone, NEC, Netgear, NGM, Nintendo, Noain, Nokia, Nomi, Nikon, Newgen, Nexian, NextBook, Onda, OnePlus, OPPO, Orange, O2, Ouki, OUYA, Opsson, Panasonic, PEAQ, Philips, Pioneer, Polaroid, Palm, phoneOne, Pantech, Ployer, Point of View, PolyPad, Pomp, Positivo, Prestigio, ProScan, PULID, Qilive, Qtek, QMobile, Quechua, Overmax, Oysters, Ramos, RCA Tablets, Readboy, Rikomagic, RIM, Roku, Rover, Samsung, Sega, Sony Ericsson, Sencor, Softbank, SFR, Sagem, Sharp, Siemens, Sendo, Skyworth, Smartfren, Sony, Spice, SuperSonic, Selevision, Sanyo, Symphony, Smart, Star, Storex, Stonex, SunVan, Sumvision, Tesla, TCL, Telit, ThL, TiPhone, Tecno Mobile, Tesco, TIANYU, Telefunken, Telenor, T-Mobile, Thomson, Tolino, Toplux, Toshiba, TechnoTrend, Trevi, Tunisie Telecom, Turbo-X, TVC, TechniSat, teXet, Unowhy, Uniscope, UTStarcom, Vastking, Videocon, Vertu, Vitelcom, VK Mobile, ViewSonic, Vestel, Vivo, Voto, Voxtel, Vodafone, Vizio, Videoweb, Walton, Web TV, WellcoM, Wexler, Wiko, Wolder, Wonu, Woxter, Xiaomi, Xolo, Unknown, Yarvik, Yuandao, Yusun, Ytone, Zeemi, Zonda, Zopo, ZTE
