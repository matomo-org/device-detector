DeviceDetector
==============

[![Latest Stable Version](https://poser.pugx.org/piwik/device-detector/v/stable)](https://packagist.org/packages/piwik/device-detector)
[![Latest Unstable Version](https://poser.pugx.org/piwik/device-detector/v/unstable)](https://packagist.org/packages/piwik/device-detector)
[![Total Downloads](https://poser.pugx.org/piwik/device-detector/downloads)](https://packagist.org/packages/piwik/device-detector)
[![License](https://poser.pugx.org/piwik/device-detector/license)](https://packagist.org/packages/piwik/device-detector)

## Code Status

[![Build Status](https://travis-ci.org/matomo-org/device-detector.svg?branch=master)](https://travis-ci.org/matomo-org/device-detector)
[![Code Coverage](https://coveralls.io/repos/piwik/device-detector/badge.png)](https://coveralls.io/r/piwik/device-detector)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/matomo-org/device-detector.svg)](http://isitmaintained.com/project/matomo-org/device-detector "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/matomo-org/device-detector.svg)](http://isitmaintained.com/project/matomo-org/device-detector "Percentage of issues still open")
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

$userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse

$dd = new DeviceDetector($userAgent);

// OPTIONAL: Set caching method
// By default static cache is used, which works best within one php process (memory array caching)
// To cache across requests use caching in files or memcache
// $dd->setCache(new Doctrine\Common\Cache\PhpFileCache('./tmp/'));

// OPTIONAL: Set custom yaml parser
// By default Spyc will be used for parsing yaml files. You can also use another yaml parser.
// You may need to implement the Yaml Parser facade if you want to use another parser than Spyc or [Symfony](https://github.com/symfony/yaml)
// $dd->setYamlParser(new DeviceDetector\Yaml\Symfony());

// OPTIONAL: If called, getBot() will only return true if a bot was detected  (speeds up detection a bit)
// $dd->discardBotInformation();

// OPTIONAL: If called, bot detection will completely be skipped (bots will be detected as regular devices then)
// $dd->skipBotDetection();

$dd->parse();

if ($dd->isBot()) {
  // handle bots,spiders,crawlers,...
  $botInfo = $dd->getBot();
} else {
  $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
  $osInfo = $dd->getOs();
  $device = $dd->getDeviceName();
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

## Using without composer

Alternatively to using composer you can also use the included `autoload.php`.
This script will register an autoloader to dynamically load all classes in `DeviceDetector` namespace.

Device Detector requires a YAML parser. By default `Spyc` parser is used.
As this library is not included you need to include it manually or use another YAML parser.

```php
<?php

include_once 'path/to/spyc/Spyc.php';
include_once 'path/to/device-detector/autoload.php';

use DeviceDetector\DeviceDetector;

$deviceDetector = new DeviceDetector();

// ...

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
zcat ~/path/to/access/logs* | awk -F'"' '{print $6}' | sort | uniq -c | sort -rn | head -n20000 > /home/matomo/top-user-agents.txt
```

### Contributors
Created by the [Matomo team](http://matomo.org/team/), Stefan Giehl, Matthieu Aubry, Michał Gaździk,
Tomasz Majczak, Grzegorz Kaszuba, Piotr Banaszczyk and contributors.

Together we can build the best Device Detection library.

We are looking forward to your contributions and pull requests!

## Tests

See also: [QA at Matomo](http://matomo.org/qa/)

### Running tests

```
cd /path/to/device-detector
curl -sS https://getcomposer.org/installer | php
php composer.phar install
phpunit
```

## Device Detector for other languages

There are already a few ports of this tool to other languages:

- **.NET** https://github.com/totpero/DeviceDetector.NET
- **Ruby** https://github.com/podigee/device_detector
- **Node.JS** https://github.com/sanchezzzhak/node-device-detector
- **Python 3** https://github.com/thinkwelltwd/device_detector


## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: 2018/07/31*

### List of detected operating systems:

AIX, Android, AmigaOS, Apple TV, Arch Linux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Brew, CentOS, Chrome OS, CyanogenMod, Debian, DragonFly, Fedora, Firefox OS, Fire OS, FreeBSD, Gentoo, Google TV, HP-UX, Haiku OS, IRIX, Inferno, KaiOS, Knoppix, Kubuntu, GNU/Linux, Lubuntu, VectorLinux, Mac, Maemo, Mandriva, MeeGo, MocorDroid, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, Nintendo, Nintendo Mobile, OS/2, OSF1, OpenBSD, PlayStation Portable, PlayStation, Red Hat, RISC OS, Remix OS, RazoDroiD, Sabayon, SUSE, Sailfish OS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, ThreadX, Tizen, Ubuntu, WebTV, Windows, Windows CE, Windows IoT, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOs, iOS, palmOS, webOS

### List of detected browsers:

360 Phone Browser, 360 Browser, Avant Browser, ABrowse, ANT Fresco, ANTGalio, Aloha Browser, Amaya, Amigo, Android Browser, Arora, Amiga Voyager, Amiga Aweb, Atomic Web Browser, Avast Secure Browser, BlackBerry Browser, Baidu Browser, Baidu Spark, Beonex, Bunjalloo, B-Line, Brave, BriskBard, BrowseX, Camino, Coc Coc, Comodo Dragon, Coast, Charon, Chrome Frame, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, CoolNovo, CometBird, ChromePlus, Chromium, Cyberfox, Cheshire, Cunaguaro, dbrowser, Deepnet Explorer, Dolphin, Dorado, Dooble, Dillo, Epic, Elinks, Element Browser, GNOME Web, Espial TV Browser, Firebird, Fluid, Fennec, Firefox, Firefox Focus, Flock, Firefox Mobile, Fireweb, Fireweb Navigator, Galeon, Google Earth, HotJava, Iceape, IBrowse, iCab, iCab Mobile, Iridium, IceDragon, Isivioo, Iceweasel, Internet Explorer, IE Mobile, Iron, Jasmine, Jig Browser, Kindle Browser, K-meleon, Konqueror, Kapiko, Kylo, Kazehakase, Liebao, LG Browser, Links, LuaKit, Lunascape, Lynx, MicroB, NCSA Mosaic, Mercury, Mobile Safari, Midori, MIUI Browser, Mobile Silk, Maxthon, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, NetSurf, NetFront, NetFront Life, NetPositive, Netscape, Obigo, Odyssey Web Browser, Off By One, ONE Browser, Opera Mini, Opera Mobile, Opera, Opera Next, Oregano, Openwave Mobile Browser, OmniWeb, Otter Browser, Palm Blazer, Pale Moon, Oppo Browser, Palm Pre, Puffin, Palm WebPro, Palmscape, Phoenix, Polaris, Polarity, Microsoft Edge, QQ Browser, Qutebrowser, QupZilla, Rekonq, RockMelt, Samsung Browser, Sailfish Browser, SEMC-Browser, Sogou Explorer, Safari, Shiira, Skyfire, Seraphic Sraf, Sleipnir, SeaMonkey, Snowshoe, Sunrise, SuperBird, Streamy, Swiftfox, Tizen Browser, TweakStyle, UC Browser, Vivaldi, Vision Mobile Browser, WebPositive, Waterfox, wOSBrowser, WeTab Browser, Yandex Browser, Xiino

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Elektra, Presto, Gecko, KHTML, NetFront, Edge, NetSurf

### List of detected libraries:

aiohttp, curl, Faraday, Go-http-client, Google HTTP Java Client, Guzzle (PHP HTTP Client), HTTP_Request2, Java, Mechanize, OkHttp, Perl, Python Requests, Python urllib, Wget, WWW-Mechanize

### List of detected media players:

Audacious, Banshee, Boxee, Clementine, Deezer, FlyCast, Foobar2000, Instacast, iTunes, Kodi, MediaMonkey, Miro, NexPlayer, Nightingale, QuickTime, Songbird, Stagefright, SubStream, VLC, Winamp, Windows Media Player, XBMC

### List of detected mobile apps:

AndroidDownloadManager, AntennaPod, Apple News, BeyondPod, bPod, Castro, Castro 2, DoggCatcher, Facebook, Facebook Messenger, FeedR, Google Play Newsstand, Google Plus, iCatcher, Instacast, Line, Overcast, Pinterest, Player FM, Pocket Casts, Podcast & Radio Addict, Podcast Republic, Podcasts, Podcat, Podcatcher Deluxe, Podkicker, Sina Weibo, WeChat, WhatsApp, Yelp Mobile, YouTube  and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Barca, DAVdroid, Lotus Notes, MailBar, Microsoft Outlook, Outlook Express, Postbox, The Bat!, Thunderbird

### List of detected feed readers:

Akregator, Apple PubSub, BashPodder, Downcast, FeedDemon, Feeddler RSS Reader, gPodder, Instacast, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, PritTorrent, Pulp, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

3Q, 4Good, Acer, Ainol, Airness, Airties, Aiwa, Alcatel, Allview, Altech UEC, Amazon, Amoi, Apple, Archos, Arnova, ARRIS, Asus, Audiovox, Avvio, Axxion, Azumi Mobile, BangOlufsen, Barnes & Noble, BBK, Becker, Beetel, BenQ, BenQ-Siemens, BGH, Bird, Bitel, Blackview, Blaupunkt, Blu, Bmobile, Boway, bq, Bravis, Brondi, Bush, Capitel, Captiva, Carrefour, Casio, Cat, Celkon, Changhong, Cherry Mobile, China Mobile, CnM, Coby Kyros, Compal, Compaq, ConCorde, Condor, Coolpad, Cowon, CreNova, Cricket, Crius Mea, Crosscall, Cube, CUBOT, Cyrus, Danew, Datang, Dbtel, Dell, Denver, Desay, DEXP, Dicam, Digma, DMM, DNS, DoCoMo, Doogee, Doov, Dopod, Doro, Dune HD, E-Boda, Easypix, EBEST, ECS, EKO, Elephone, Energy Sistem, Ericsson, Ericy, Essential, Eton, eTouch, Evertek, Evolveo, Explay, Ezio, Ezze, Fairphone, Fly, Foxconn, Freetel, Fujitsu, Garmin-Asus, Gateway, Gemini, Gigabyte, Gigaset, Gionee, GOCLEVER, Goly, Google, Gradiente, Grundig, Haier, HannSpree, Hasee, Hi-Level, Hisense, Homtom, Hosin, HP, HTC, Huawei, Humax, Hyrican, Hyundai, i-Joy, i-mate, i-mobile, iBall, iBerry, IconBIT, Ikea, iKoMo, iNew, Infinix, Inkti, Innostream, INQ, Intek, Intex, Inverto, iOcean, iTel, JAY-Tech, Jiayu, Jolla, K-Touch, Karbonn, Kazam, KDDI, Kiano, Kingsun, Kogan, Komu, Konka, Konrow, Koobee, KOPO, Koridy, KT-Tech, Kumai, Kyocera, Landvo, Lanix, Lava, LCT, LeEco, Lenco, Lenovo, Le Pan, Lexand, Lexibook, LG, Lingwin, Loewe, Logicom, LYF, M.T.T., Majestic, Manta Multimedia, Mecer, Mediacom, MediaTek, Medion, MEEG, Meizu, Memup, Metz, MEU, MicroMax, Microsoft, Mio, Mitsubishi, MIXC, MLLED, Mobiistar, Mobistel, Modecom, Mofut, Motorola, Mpman, MSI, MyPhone, NEC, Neffos, Netgear, Newgen, Nexian, NextBook, NGM, Nikon, Nintendo, Noain, Noblex, Nokia, Nomi, Nous, Nvidia, O2, Obi, Odys, Onda, OnePlus, OPPO, Opsson, Orange, Ouki, OUYA, Overmax, Oysters, Palm, Panasonic, Pantech, PEAQ, Pentagram, Philips, phoneOne, Pioneer, Ployer, Point of View, Polaroid, PolyPad, Pomp, Positivo, PPTV, Prestigio, ProScan, PULID, Qilive, QMobile, Qtek, Quechua, Ramos, RCA Tablets, Readboy, Rikomagic, RIM, Roku, Rover, Sagem, Samsung, Sanyo, Sega, Selevision, Sencor, Sendo, Senseit, SFR, Sharp, Siemens, Skyworth, Smart, Smartfren, Smartisan, Softbank, Sony, Sony Ericsson, Spice, Star, STK, Stonex, Storex, Sumvision, SunVan, SuperSonic, Supra, Symphony, T-Mobile, TB Touch, TCL, TechniSat, TechnoTrend, Teclast, Tecno Mobile, Telefunken, Telenor, Telit, Tesco, Tesla, teXet, ThL, Thomson, TIANYU, TiPhone, Tolino, Toplux, Toshiba, TrekStor, Trevi, Tunisie Telecom, Turbo-X, TVC, Ulefone, UMIDIGI, Uniscope, Unknown, Unowhy, UTStarcom, Vastking, Vernee, Vertu, Verykool, Vestel, Videocon, Videoweb, ViewSonic, Vitelcom, Vivo, Vizio, VK Mobile, Vodafone, Vonino, Voto, Voxtel, Walton, Web TV, WellcoM, Wexler, Wiko, Wileyfox, Wolder, Wolfgang, Wonu, Woxter, Xiaomi, Xolo, Yarvik, Ytone, Yuandao, Yusun, Zeemi, Zen, Zonda, Zopo, ZTE

### List of detected bots:

360Spider, Aboundexbot, Acoon, AddThis.com, ADMantX, aHrefs Bot, Alexa Crawler, Amorank Spider, Analytics SEO Crawler, ApacheBench, Applebot, archive.org bot, Ask Jeeves, Backlink-Check.de, BacklinkCrawler, Baidu Spider, BazQux Reader, BingBot, BitlyBot, Blekkobot, BLEXBot Crawler, Bloglovin, Blogtrottr, Bountii Bot, Browsershots, BUbiNG, Butterfly Robot, CareerBot, Castro 2, Catchpoint, ccBot crawler, Charlotte, Cliqzbot, CloudFlare Always Online, CloudFlare AMP Fetcher, Collectd, CommaFeed, CSS Certificate Spider, Cốc Cốc Bot, Datadog Agent, Dataprovider, Daum, Dazoobot, Discobot, Domain Re-Animator Bot, DotBot, DuckDuckGo Bot, Easou Spider, EMail Exractor, EmailWolf, evc-batch, ExaBot, ExactSeek Crawler, Ezooms, Facebook External Hit, Feedbin, FeedBurner, Feedly, Feedspot, Feed Wrangler, Fever, Findxbot, Flipboard, Generic Bot, Generic Bot, Genieo Web filter, Gigablast, Gigabot, Gluten Free Crawler, Gmail Image Proxy, Goo, Googlebot, Google PageSpeed Insights, Google Partner Monitoring, Google Structured Data Testing Tool, Grapeshot, Heritrix, Heureka Feed, HTTPMon, HubPages, HubSpot, ICC-Crawler, ichiro, IIS Site Analysis, Inktomi Slurp, IP-Guide Crawler, IPS Agent, Kouio, Larbin web crawler, Let's Encrypt Validation, Lighthouse, Linkdex Bot, LinkedIn Bot, LTX71, Lycos, Magpie-Crawler, MagpieRSS, Mail.Ru Bot, masscan, Meanpath Bot, MetaInspector, MetaJobBot, Mixrank Bot, MJ12 Bot, Mnogosearch, MojeekBot, Monitor.Us, Munin, Nagios check_http, NalezenCzBot, Netcraft Survey Bot, netEstate, NetLyzer FastProbe, NetResearchServer, Netvibes, NewsBlur, NewsGator, NLCrawler, Nmap, Nutch-based Bot, Octopus, Omgili bot, Openindex Spider, OpenLinkProfiler, OpenWebSpider, Orange Bot, Outbrain, PagePeeker, PaperLiBot, Phantomas, PHP Server Monitor, Picsearch bot, Pingdom Bot, Pinterest, PocketParser, Pompos, PritTorrent, QuerySeekerSpider, Qwantify, Rainmeter, RamblerMail Image Proxy, Reddit Bot, Riddler, Rogerbot, ROI Hunter, SafeDNSBot, Scooter, ScoutJet, Scrapy, Screaming Frog SEO Spider, ScreenerBot, Semrush Bot, Sensika Bot, Sentry Bot, SEOENGBot, SEOkicks-Robot, Seoscanners.net, Server Density, Seznam Bot, Seznam Email Proxy, Seznam Zbozi.cz, ShopAlike, ShopWiki, SilverReader, SimplePie, SISTRIX Crawler, Site24x7 Website Monitoring, SiteSucker, Sixy.ch, Skype URI Preview, Slackbot, Sogou Spider, Soso Spider, Sparkler, Speedy, Spinn3r, Sputnik Bot, sqlmap, SSL Labs, StatusCake, Superfeedr Bot, Survey Bot, Tarmot Gezgin, TelgramBot, TinEye Crawler, Tiny Tiny RSS, TLSProbe, Trendiction Bot, TurnitinBot, TweetedTimes Bot, Tweetmeme Bot, Twitterbot, UkrNet Mail Proxy, UniversalFeedParser, Uptimebot, Uptime Robot, URLAppendBot, Vagabondo, Visual Site Mapper Crawler, W3C CSS Validator, W3C I18N Checker, W3C Link Checker, W3C Markup Validation Service, W3C MobileOK Checker, W3C Unified Validator, Wappalyzer, WebbCrawler, WebSitePulse, WebThumbnail, WeSEE:Search, Willow Internet Crawler, WordPress, Wotbox, YaCy, Yahoo! Cache System, Yahoo! Link Preview, Yahoo! Slurp, Yahoo Gemini, Yandex Bot, Yeti/Naverbot, Yottaa Site Monitor, Youdao Bot, Yourls, Yunyun Bot, Zao, zgrab, Zookabot, ZumBot
