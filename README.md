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

The Universal Device Detection library that parses User Agents and detects devices (desktop, tablet, mobile, tv, cars, console, etc.), clients (browsers, feed readers, media players, PIMs, ...), operating systems, brands and models.

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

// OPTIONAL: Set custom yaml parser
// By default Spyc will be used for parsing yaml files. You can also use another yaml parser.
// You may need to implement the Yaml Parser facade if you want to use another parser than Spyc or [Symfony](https://github.com/symfony/yaml)
$dd->setYamlParser(new DeviceDetector\Yaml\Symfony());

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
  $brand = $dd->getBrandName();
  $model = $dd->getModel();
}
```

Instead of using the full power of DeviceDetector it might in some cases be better to use only specific parsers.
If you aim to check if a given useragent is a bot and don't require any of the other information, you can directly use the bot parser.

```php
require_once 'vendor/autoload.php';

use DeviceDetector\Parser\Bot AS BotParser;

$botParser = new BotParser();
$botParser->setUserAgent($userAgent);

// OPTIONAL: discard bot information. parse() will then return true instead of information
$botParser->discardDetails();

$result = $botParser->parse();

if (!is_null($result)) {
    // do not do anything if a bot is detected
    return;
}

// handle non-bot requests

```


### Caching

By default, DeviceDetector uses a built-in array cache. To get better performance, you can use your own caching solution:

* You can create a class that implement `DeviceDetector\Cache\Cache`
* You can directly use a Doctrine Cache object (useful if your project already uses Doctrine)
* Or if your project uses a [PSR-6](http://www.php-fig.org/psr/psr-6/) or [PSR-16](http://www.php-fig.org/psr/psr-16/) compliant caching system (like [symfony/cache](https://github.com/symfony/cache) or [matthiasmullie/scrapbook](https://github.com/matthiasmullie/scrapbook)), you can inject them the following way:

```php
// Example with PSR-6 and Symfony
$cache = new Symfony\Component\Cache\Adapter\ApcuAdapter();
$dd->setCache(
    new DeviceDetector\Cache\PSR6Bridge($cache)
);

// Example with PSR-16 and ScrapBook
$cache = new \MatthiasMullie\Scrapbook\Psr16\SimpleCache(
    new \MatthiasMullie\Scrapbook\Adapters\Apc()
);
$dd->setCache(
    new DeviceDetector\Cache\PSR16Bridge($cache)
);

// Example with Doctrine
$dd->setCache(
    new Doctrine\Common\Cache\ApcuCache()
);
```

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

*Last update: 2017/12/04*

### List of detected operating systems:

AIX, Android, AmigaOS, Apple TV, Arch Linux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Brew, CentOS, Chrome OS, CyanogenMod, Debian, DragonFly, Fedora, Firefox OS, FreeBSD, Gentoo, Google TV, HP-UX, Haiku OS, IRIX, Inferno, Knoppix, Kubuntu, GNU/Linux, Lubuntu, VectorLinux, Mac, Maemo, Mandriva, MeeGo, MocorDroid, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, Nintendo, Nintendo Mobile, OS/2, OSF1, OpenBSD, PlayStation Portable, PlayStation, Red Hat, RISC OS, Remix OS, RazoDroiD, Sabayon, SUSE, Sailfish OS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, ThreadX, Tizen, Ubuntu, WebTV, Windows, Windows CE, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOs, iOS, palmOS, webOS

### List of detected browsers:

360 Phone Browser, 360 Browser, Avant Browser, ABrowse, ANT Fresco, ANTGalio, Aloha Browser, Amaya, Amigo, Android Browser, Arora, Amiga Voyager, Amiga Aweb, Atomic Web Browser, BlackBerry Browser, Baidu Browser, Baidu Spark, Beonex, Bunjalloo, B-Line, Brave, BriskBard, BrowseX, Camino, Coc Coc, Comodo Dragon, Coast, Charon, Chrome Frame, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, CoolNovo, CometBird, ChromePlus, Chromium, Cyberfox, Cheshire, dbrowser, Deepnet Explorer, Dolphin, Dorado, Dooble, Dillo, Epic, Elinks, Element Browser, GNOME Web, Espial TV Browser, Firebird, Fluid, Fennec, Firefox, Firefox Focus, Flock, Firefox Mobile, Fireweb, Fireweb Navigator, Galeon, Google Earth, HotJava, Iceape, IBrowse, iCab, iCab Mobile, Iridium, IceDragon, Isivioo, Iceweasel, Internet Explorer, IE Mobile, Iron, Jasmine, Jig Browser, Kindle Browser, K-meleon, Konqueror, Kapiko, Kylo, Kazehakase, Liebao, LG Browser, Links, LuaKit, Lunascape, Lynx, MicroB, NCSA Mosaic, Mercury, Mobile Safari, Midori, MIUI Browser, Mobile Silk, Maxthon, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, NetFront, NetFront Life, NetPositive, Netscape, Obigo, Odyssey Web Browser, Off By One, ONE Browser, Opera Mini, Opera Mobile, Opera, Opera Next, Oregano, Openwave Mobile Browser, OmniWeb, Otter Browser, Palm Blazer, Pale Moon, Palm Pre, Puffin, Palm WebPro, Palmscape, Phoenix, Polaris, Polarity, Microsoft Edge, QQ Browser, Qutebrowser, QupZilla, Rekonq, RockMelt, Samsung Browser, Sailfish Browser, SEMC-Browser, Sogou Explorer, Safari, Shiira, Skyfire, Seraphic Sraf, Sleipnir, SeaMonkey, Snowshoe, Sunrise, SuperBird, Streamy, Swiftfox, Tizen Browser, TweakStyle, UC Browser, Vivaldi, Vision Mobile Browser, WebPositive, Waterfox, wOSBrowser, WeTab Browser, Yandex Browser, Xiino

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Elektra, Presto, Gecko, KHTML, NetFront, Edge

### List of detected libraries:

aiohttp, curl, Faraday, Go-http-client, Google HTTP Java Client, Guzzle (PHP HTTP Client), HTTP_Request2, Java, Mechanize, OkHttp, Perl, Python Requests, Python urllib, Wget, WWW-Mechanize

### List of detected media players:

Banshee, Boxee, Clementine, FlyCast, Foobar2000, Instacast, iTunes, Kodi, MediaMonkey, Miro, NexPlayer, Nightingale, QuickTime, Songbird, Stagefright, SubStream, VLC, Winamp, Windows Media Player, XBMC

### List of detected mobile apps:

AndroidDownloadManager, AntennaPod, Apple News, BeyondPod, bPod, Castro, Castro 2, DoggCatcher, Facebook, Facebook Messenger, FeedR, Google Play Newsstand, Google Plus, iCatcher, Instacast, Line, Overcast, Pinterest, Player FM, Pocket Casts, Podcasts, Podcat, Podcatcher Deluxe, Podkicker, Sina Weibo, WeChat, WhatsApp, YouTube  and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Barca, DAVdroid, Lotus Notes, MailBar, Microsoft Outlook, Outlook Express, Postbox, The Bat!, Thunderbird

### List of detected feed readers:

Akregator, Apple PubSub, BashPodder, Downcast, FeedDemon, Feeddler RSS Reader, gPodder, Instacast, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, PritTorrent, Pulp, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

3Q, 4Good, Acer, Ainol, Airness, Airties, Alcatel, Allview, Altech UEC, Amazon, Amoi, Apple, Archos, Arnova, ARRIS, Asus, Audiovox, Avvio, Axxion, BangOlufsen, Barnes & Noble, BBK, Becker, Beetel, BenQ, BenQ-Siemens, Bird, Blackview, Blaupunkt, Blu, Bmobile, Boway, bq, Bravis, Brondi, Bush, Capitel, Captiva, Carrefour, Casio, Cat, Celkon, Changhong, Cherry Mobile, China Mobile, CnM, Coby Kyros, Compal, Compaq, ConCorde, Condor, Coolpad, Cowon, CreNova, Cricket, Crius Mea, Crosscall, Cube, CUBOT, Cyrus, Danew, Datang, Dbtel, Dell, Denver, Desay, DEXP, Dicam, Digma, DMM, DNS, DoCoMo, Doogee, Doov, Dopod, Dune HD, E-Boda, Easypix, EBEST, ECS, Elephone, Energy Sistem, Ericsson, Ericy, Eton, eTouch, Evertek, Evolveo, Explay, Ezio, Ezze, Fairphone, Fly, Foxconn, Freetel, Fujitsu, Garmin-Asus, Gateway, Gemini, Gigabyte, Gigaset, Gionee, GOCLEVER, Goly, Google, Gradiente, Grundig, Haier, Hasee, Hi-Level, Hisense, Homtom, Hosin, HP, HTC, Huawei, Humax, Hyrican, Hyundai, i-Joy, i-mate, i-mobile, iBall, iBerry, IconBIT, Ikea, iKoMo, iNew, Infinix, Inkti, Innostream, INQ, Intek, Intex, Inverto, iOcean, iTel, JAY-Tech, Jiayu, Jolla, K-Touch, Karbonn, Kazam, KDDI, Kiano, Kingsun, Komu, Konka, Konrow, Koobee, KOPO, Koridy, KT-Tech, Kumai, Kyocera, Landvo, Lanix, Lava, LCT, LeEco, Lenco, Lenovo, Le Pan, Lexand, Lexibook, LG, Lingwin, Loewe, Logicom, LYF, M.T.T., Majestic, Manta Multimedia, Mecer, Mediacom, MediaTek, Medion, MEEG, Meizu, Memup, Metz, MEU, MicroMax, Microsoft, Mio, Mitsubishi, MIXC, MLLED, Mobistel, Modecom, Mofut, Motorola, Mpman, MSI, MyPhone, NEC, Neffos, Netgear, Newgen, Nexian, NextBook, NGM, Nikon, Nintendo, Noain, Nokia, Nomi, Nous, Nvidia, O2, Odys, Onda, OnePlus, OPPO, Opsson, Orange, Ouki, OUYA, Overmax, Oysters, Palm, Panasonic, Pantech, PEAQ, Pentagram, Philips, phoneOne, Pioneer, Ployer, Point of View, Polaroid, PolyPad, Pomp, Positivo, PPTV, Prestigio, ProScan, PULID, Qilive, QMobile, Qtek, Quechua, Ramos, RCA Tablets, Readboy, Rikomagic, RIM, Roku, Rover, Sagem, Samsung, Sanyo, Sega, Selevision, Sencor, Sendo, Senseit, SFR, Sharp, Siemens, Skyworth, Smart, Smartfren, Smartisan, Softbank, Sony, Sony Ericsson, Spice, Star, Stonex, Storex, Sumvision, SunVan, SuperSonic, Supra, Symphony, T-Mobile, TB Touch, TCL, TechniSat, TechnoTrend, Tecno Mobile, Telefunken, Telenor, Telit, Tesco, Tesla, teXet, ThL, Thomson, TIANYU, TiPhone, Tolino, Toplux, Toshiba, TrekStor, Trevi, Tunisie Telecom, Turbo-X, TVC, UMIDIGI, Uniscope, Unknown, Unowhy, UTStarcom, Vastking, Vertu, Vestel, Videocon, Videoweb, ViewSonic, Vitelcom, Vivo, Vizio, VK Mobile, Vodafone, Voto, Voxtel, Walton, Web TV, WellcoM, Wexler, Wiko, Wileyfox, Wolder, Wolfgang, Wonu, Woxter, Xiaomi, Xolo, Yarvik, Ytone, Yuandao, Yusun, Zeemi, Zen, Zonda, Zopo, ZTE
