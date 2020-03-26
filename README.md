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
./vendor/bin/phpunit
```

## Device Detector for other languages

There are already a few ports of this tool to other languages:

- **.NET** https://github.com/AgileFlexAgency/MatomoDeviceDetector.NET
- **.NET** https://github.com/totpero/DeviceDetector.NET
- **Ruby** https://github.com/podigee/device_detector
- **JavaScript/TypeScript/NodeJS** https://github.com/etienne-martin/device-detector-js
- **Python 3** https://github.com/thinkwelltwd/device_detector
- **Crystal** https://github.com/creadone/device_detector
- **Elixir** https://github.com/elixir-inspector/ua_inspector
- **Java** https://github.com/mngsk/device-detector


## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: 2020/03/26*

### List of detected operating systems:

AIX, Android, AmigaOS, Apple TV, Arch Linux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Brew, CentOS, Chrome OS, CyanogenMod, Debian, DragonFly, Fedora, Firefox OS, Fire OS, FreeBSD, Gentoo, Google TV, HP-UX, Haiku OS, IRIX, Inferno, KaiOS, Knoppix, Kubuntu, GNU/Linux, Lubuntu, VectorLinux, Mac, Maemo, Mandriva, MeeGo, MocorDroid, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, Nintendo, Nintendo Mobile, OS/2, OSF1, OpenBSD, Ordissimo, PlayStation Portable, PlayStation, Red Hat, RISC OS, Remix OS, RazoDroiD, Sabayon, SUSE, Sailfish OS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, ThreadX, Tizen, Ubuntu, WebTV, Windows, Windows CE, Windows IoT, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOs, iOS, palmOS, webOS

### List of detected browsers:

2345 Browser, 360 Phone Browser, 360 Browser, Avant Browser, ABrowse, ANT Fresco, ANTGalio, Aloha Browser, Aloha Browser Lite, Amaya, Amigo, Android Browser, AOL Shield, Arora, Amiga Voyager, Amiga Aweb, Atomic Web Browser, Avast Secure Browser, AVG Secure Browser, Beaker Browser, Beamrise, BlackBerry Browser, Baidu Browser, Baidu Spark, Basilisk, Beonex, BlackHawk, Bunjalloo, B-Line, Brave, BriskBard, BrowseX, Camino, CCleaner, Coc Coc, Comodo Dragon, Coast, Charon, CM Browser, Chrome Frame, Headless Chrome, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, CoolNovo, CometBird, COS Browser, ChromePlus, Chromium, Cyberfox, Cheshire, Crusta, Cunaguaro, Chrome Webview, dbrowser, Deepnet Explorer, Delta Browser, Dolphin, Dorado, Dooble, Dillo, DuckDuckGo Privacy Browser, Ecosia, Epic, Elinks, Element Browser, eZ Browser, EUI Browser, GNOME Web, Espial TV Browser, Falkon, Faux Browser, Firefox Mobile iOS, Firebird, Fluid, Fennec, Firefox, Firefox Focus, Firefox Reality, Firefox Rocket, Flock, Firefox Mobile, Fireweb, Fireweb Navigator, FreeU, Galeon, Google Earth, Hawk Turbo Browser, hola! Browser, HotJava, Huawei Browser, IBrowse, iCab, iCab Mobile, Iridium, Iron Mobile, IceCat, IceDragon, Isivioo, Iceweasel, Internet Explorer, IE Mobile, Iron, Jasmine, Jig Browser, Jio Browser, K.Browser, Kindle Browser, K-meleon, Konqueror, Kapiko, Kinza, Kiwi, Kylo, Kazehakase, Cheetah Browser, LieBaoFast, LG Browser, Links, Lovense Browser, LuaKit, Lunascape, Lynx, mCent, MicroB, NCSA Mosaic, Meizu Browser, Mercury, Mobile Safari, Midori, Mobicip, MIUI Browser, Mobile Silk, Minimo, Mint Browser, Maxthon, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, Nox Browser, NetSurf, NetFront, NetFront Life, NetPositive, Netscape, NTENT Browser, Oculus Browser, Opera Mini iOS, Obigo, Odyssey Web Browser, Off By One, ONE Browser, Opera GX, Opera Neon, Opera Devices, Opera Mini, Opera Mobile, Opera, Opera Next, Opera Touch, Ordissimo, Oregano, Origyn Web Browser, Openwave Mobile Browser, OmniWeb, Otter Browser, Palm Blazer, Pale Moon, Oppo Browser, Palm Pre, Puffin, Palm WebPro, Palmscape, Phoenix, Polaris, Polarity, Microsoft Edge, QQ Browser Mini, QQ Browser, Qutebrowser, QupZilla, Qwant Mobile, QtWebEngine, Realme Browser, Rekonq, RockMelt, Samsung Browser, Sailfish Browser, SEMC-Browser, Sogou Explorer, Safari, SalamWeb, Shiira, SimpleBrowser, Skyfire, Seraphic Sraf, Sleipnir, Snowshoe, Sogou Mobile Browser, Splash, Sputnik Browser, Sunrise, SuperBird, Super Fast Browser, START Internet Browser, Streamy, Swiftfox, Seznam Browser, t-online.de Browser, Tao Browser, TenFourFox, Tenta Browser, Tizen Browser, TweakStyle, UBrowser, UC Browser, UC Browser Mini, UC Browser Turbo, Uzbl, Vivaldi, vivo Browser, Vision Mobile Browser, Wear Internet Browser, Web Explorer, WebPositive, Waterfox, Whale Browser, wOSBrowser, WeTab Browser, Yandex Browser, Yandex Browser Lite, Xiino

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Elektra, Presto, Gecko, KHTML, NetFront, Edge, NetSurf, Servo

### List of detected libraries:

aiohttp, curl, Faraday, Go-http-client, Google HTTP Java Client, Guzzle (PHP HTTP Client), HTTPie, HTTP_Request2, Java, libdnf, Mechanize, Node Fetch, OkHttp, Perl, Perl REST::Client, Python Requests, Python urllib, REST Client for Ruby, RestSharp, ScalaJ HTTP, urlgrabber (yum), Wget, WWW-Mechanize

### List of detected media players:

Audacious, Banshee, Boxee, Clementine, Deezer, FlyCast, Foobar2000, Google Podcasts, iTunes, Kodi, MediaMonkey, Miro, mpv, Music Player Daemon, NexPlayer, Nightingale, QuickTime, Songbird, Stagefright, SubStream, VLC, Winamp, Windows Media Player, XBMC

### List of detected mobile apps:

AndroidDownloadManager, AntennaPod, Apple News, Baidu Box App, BeyondPod, BingWebApp, bPod, CastBox, Castro, Castro 2, CrosswalkApp, DoggCatcher, douban App, Facebook, Facebook Messenger, FeedR, Flipboard App, Google Go, Google Play Newsstand, Google Plus, Google Search App, iCatcher, Instacast, Instagram App, Line, NewsArticle App, Overcast, Pinterest, Player FM, Pocket Casts, Podcast & Radio Addict, Podcast Republic, Podcasts, Podcat, Podcatcher Deluxe, Podkicker, RSSRadio, Sina Weibo, SogouSearch App, tieba, WeChat, WhatsApp, Yahoo! Japan, Yelp Mobile, YouTube and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Barca, DAVdroid, Lotus Notes, MailBar, Microsoft Outlook, Outlook Express, Postbox, SeaMonkey, The Bat!, Thunderbird

### List of detected feed readers:

Akregator, Apple PubSub, BashPodder, Breaker, Downcast, FeedDemon, Feeddler RSS Reader, gPodder, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, PritTorrent, Pulp, QuiteRSS, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

3Q, 4Good, Ace, Acer, Advan, Advance, AGM, Ainol, Airness, Airties, AIS, Aiwa, Akai, Alba, Alcatel, AllCall, AllDocube, Allview, Allwinner, Altech UEC, altron, Amazon, AMGOO, Amoi, ANS, Apple, Archos, Arian Space, Ark, Arnova, ARRIS, Ask, Assistant, Asus, Atom, Audiovox, AVH, Avvio, Axxion, Azumi Mobile, BangOlufsen, Barnes & Noble, BBK, BDF, Becker, Beeline, Beetel, BenQ, BenQ-Siemens, Bezkam, BGH, Bird, Bitel, Black Fox, Blackview, Blaupunkt, Blu, Bluboo, Bluegood, Bmobile, bogo, Boway, bq, Bravis, Brondi, Bush, CAGI, Capitel, Captiva, Carrefour, Casio, Casper, Cat, Celkon, Changhong, Cherry Mobile, China Mobile, Chuwi, Clarmin, CnM, Coby Kyros, Comio, Compal, Compaq, ComTrade Tesla, Concord, ConCorde, Condor, Coolpad, Cowon, CreNova, Crescent, Cricket, Crius Mea, Crosscall, Cube, CUBOT, CVTE, Cyrus, Daewoo, Danew, Datang, Datsun, Dbtel, Dell, Denver, Desay, DeWalt, DEXP, Dialog, Dicam, Digi, Digicel, Digiland, Digma, Divisat, DMM, DNS, DoCoMo, Doogee, Doov, Dopod, Doro, Dune HD, E-Boda, E-tel, Easypix, EBEST, Echo Mobiles, ECS, EE, EKO, Eks Mobility, Elenberg, Elephone, Energizer, Energy Sistem, Ergo, Ericsson, Ericy, Essential, Essentielb, Eton, eTouch, Etuline, Eurostar, Evercoss, Evertek, Evolio, Evolveo, EvroMedia, Explay, Extrem, Ezio, Ezze, Fairphone, Famoco, Fengxiang, FiGO, FinePower, Fly, FNB, Fondi, FORME, Forstar, Foxconn, Freetel, Fujitsu, G-TiDE, Garmin-Asus, Gateway, Gemini, General Mobile, Geotel, Ghia, Ghong, Gigabyte, Gigaset, Ginzzu, Gionee, Globex, GOCLEVER, Goly, GoMobile, Google, Gradiente, Grape, Grundig, Hafury, Haier, HannSpree, Hasee, Hi-Level, Highscreen, Hisense, Hoffmann, Homtom, Hoozo, Hosin, HP, HTC, Huawei, Humax, Hyrican, Hyundai, i-Joy, i-mate, i-mobile, iBall, iBerry, IconBIT, iHunt, Ikea, iKoMo, iLA, IMO Mobile, Impression, iNew, Infinix, InFocus, Inkti, InnJoo, Innostream, Inoi, INQ, Insignia, Intek, Intex, Inverto, iOcean, iPro, Irbis, iRola, iRulu, iTel, iView, iZotron, JAY-Tech, Jiayu, Jolla, Just5, K-Touch, Kaan, Kaiomy, Kalley, Kanji, Karbonn, KATV1, Kazam, KDDI, Kempler & Strauss, Keneksi, Kiano, Kingsun, Kivi, Kocaso, Kodak, Kogan, Komu, Konka, Konrow, Koobee, KOPO, Koridy, KRONO, Krüger&Matz, KT-Tech, Kumai, Kyocera, LAIQ, Land Rover, Landvo, Lanix, Lark, Lava, LCT, Leagoo, Ledstar, LeEco, Lemhoov, Lenco, Lenovo, Leotec, Le Pan, Lephone, Lexand, Lexibook, LG, Lingwin, Loewe, Logicom, Lumus, LYF, M.T.T., M4tel, Majestic, Mann, Manta Multimedia, Masstel, Maxwest, Maze, Mecer, Mecool, Mediacom, MediaTek, Medion, MEEG, MegaFon, Meitu, Meizu, Memup, Metz, MEU, MicroMax, Microsoft, Mio, Miray, Mitsubishi, MIXC, MLLED, Mobicel, Mobiistar, Mobiola, Mobistel, Modecom, Mofut, Motorola, Movic, Mpman, MSI, MTC, MTN, MYFON, MyPhone, Myria, Mystery, MyWigo, National, Navon, NEC, Neffos, Netgear, NeuImage, Newgen, NewsMy, NEXBOX, Nexian, Nextbit, NextBook, NGM, NG Optics, Nikon, Nintendo, NOA, Noain, Nobby, Noblex, Nokia, Nomi, Nous, NUU Mobile, Nuvo, Nvidia, NYX Mobile, O+, O2, Obi, Odys, Onda, OnePlus, OPPO, Opsson, Orange, Ordissimo, Ouki, OUYA, Overmax, Oysters, Palm, Panacom, Panasonic, Pantech, PCBOX, PCD, PCD Argentina, PEAQ, Pentagram, Philips, phoneOne, Pioneer, Pixus, Ployer, Plum, Point of View, Polaroid, PolyPad, Polytron, Pomp, Positivo, PPTV, Prestigio, Primepad, Proline, ProScan, PULID, Q-Touch, Qilive, QMobile, Qtek, Quantum, Quechua, Qumo, R-TV, Ramos, RCA Tablets, Readboy, Rikomagic, RIM, Rinno, Ritmix, Ritzviva, Riviera, Roadrover, Rokit, Roku, Rombica, Ross&Moor, Rover, RoverPad, RT Project, Runbo, Safaricom, Sagem, Samsung, Sanei, Santin BiTBiZ, Sanyo, Savio, Sega, Selevision, Selfix, Sencor, Sendo, Senseit, Senwa, SFR, Sharp, Shuttle, Siemens, Sigma, Silent Circle, Simbans, Sky, Skyworth, Smart, Smartfren, Smartisan, Softbank, Sonim, Sony, Sony Ericsson, Spectrum, Spice, Star, Starway, STF Mobile, STK, Stonex, Storex, Sumvision, SunVan, Sunvell, SuperSonic, Supra, SWISSMOBILITY, Symphony, Syrox, T-Mobile, TB Touch, TCL, TechniSat, TechnoTrend, TechPad, Teclast, Tecno Mobile, Telefunken, Telego, Telenor, Telit, Tesco, Tesla, teXet, ThL, Thomson, TIANYU, Timovi, TiPhone, Tolino, Tooky, Top House, Toplux, Toshiba, Touchmate, TrekStor, Trevi, True, Tunisie Telecom, Turbo, Turbo-X, TVC, U.S. Cellular, Ugoos, Uhappy, Ulefone, Umax, UMIDIGI, Unihertz, Unimax, Uniscope, Unknown, Unnecto, Unonu, Unowhy, UTOK, UTStarcom, Vastking, Venso, Verizon, Vernee, Vertex, Vertu, Verykool, Vesta, Vestel, VGO TEL, Videocon, Videoweb, ViewSonic, Vinga, Vinsoc, Vitelcom, Vivax, Vivo, Vizio, VK Mobile, Vodafone, Vonino, Vorago, Voto, Voxtel, Vsun, Vulcan, Walton, Web TV, Weimei, WellcoM, Wexler, Wieppo, Wiko, Wileyfox, Wink, Wolder, Wolfgang, Wonu, Woo, Woxter, X-TIGI, X-View, Xiaolajiao, Xiaomi, Xion, Xolo, Xoro, Yandex, Yarvik, Yes, Yezz, Yota, Ytone, Yu, Yuandao, Yusun, Yxtel, Zeemi, Zen, Zenek, Zonda, Zopo, ZTE, Zuum, Zync, ZYQ, öwn

### List of detected bots:

360Spider, Aboundexbot, Acoon, AddThis.com, ADMantX, aHrefs Bot, Alexa Crawler, Alexa Site Audit, Amazon Route53 Health Check, Amorank Spider, Analytics SEO Crawler, ApacheBench, Applebot, Arachni, archive.org bot, Ask Jeeves, Awario, Awario, Backlink-Check.de, BacklinkCrawler, Baidu Spider, BazQux Reader, BingBot, BitlyBot, Blekkobot, BLEXBot Crawler, Bloglovin, Blogtrottr, BoardReader, BoardReader Blog Indexer, Bountii Bot, BrandVerity, Browsershots, BUbiNG, Buck, Butterfly Robot, Bytespider, CareerBot, Castro 2, Catchpoint, CATExplorador, ccBot crawler, Charlotte, Cliqzbot, CloudFlare Always Online, CloudFlare AMP Fetcher, Collectd, CommaFeed, CSS Certificate Spider, Cốc Cốc Bot, Datadog Agent, Datanyze, Dataprovider, Daum, Dazoobot, Discobot, Domain Re-Animator Bot, DotBot, DuckDuckGo Bot, Easou Spider, eCairn-Grabber, EMail Exractor, EmailWolf, Embedly, evc-batch, ExaBot, ExactSeek Crawler, Ezooms, eZ Publish Link Validator, Facebook External Hit, Feedbin, FeedBurner, Feedly, Feedspot, Feed Wrangler, Fever, Findxbot, Flipboard, FreshRSS, Generic Bot, Generic Bot, Genieo Web filter, Gigablast, Gigabot, Gluten Free Crawler, Gmail Image Proxy, Goo, Googlebot, Google Cloud Scheduler, Google Favicon, Google PageSpeed Insights, Google Partner Monitoring, Google Search Console, Google Stackdriver Monitoring, Google Structured Data Testing Tool, Grapeshot, Heritrix, Heureka Feed, HTTPMon, HubPages, HubSpot, ICC-Crawler, ichiro, IDG/IT, IIS Site Analysis, Inktomi Slurp, inoreader, IP-Guide Crawler, IPS Agent, Kaspersky, Kouio, Larbin web crawler, LCC, Let's Encrypt Validation, Lighthouse, Linkdex Bot, LinkedIn Bot, LTX71, Lycos, Magpie-Crawler, MagpieRSS, Mail.Ru Bot, masscan, Mastodon Bot, Meanpath Bot, MetaInspector, MetaJobBot, Mixrank Bot, MJ12 Bot, Mnogosearch, MojeekBot, Monitor.Us, Munin, Nagios check_http, NalezenCzBot, nbertaupete95, Netcraft Survey Bot, netEstate, NetLyzer FastProbe, NetResearchServer, Netvibes, NewsBlur, NewsGator, NLCrawler, Nmap, Nutch-based Bot, Nuzzel, oBot, Octopus, Omgili bot, Openindex Spider, OpenLinkProfiler, OpenWebSpider, Orange Bot, Outbrain, PagePeeker, PaperLiBot, Phantomas, PHP Server Monitor, Picsearch bot, Pingdom Bot, Pinterest, PocketParser, Pompos, PritTorrent, QuerySeekerSpider, Quora Link Preview, Qwantify, Rainmeter, RamblerMail Image Proxy, Reddit Bot, Riddler, Rogerbot, ROI Hunter, RSSRadio Bot, SafeDNSBot, Scooter, ScoutJet, Scrapy, Screaming Frog SEO Spider, ScreenerBot, Semrush Bot, Sensika Bot, Sentry Bot, SEOENGBot, SEOkicks-Robot, Seoscanners.net, Server Density, Seznam Bot, Seznam Email Proxy, Seznam Zbozi.cz, ShopAlike, Shopify Partner, ShopWiki, SilverReader, SimplePie, SISTRIX Crawler, SISTRIX Optimizer, Site24x7 Website Monitoring, Siteimprove, SiteSucker, Sixy.ch, Skype URI Preview, Slackbot, SMTBot, Snapchat Proxy, Sogou Spider, Soso Spider, Sparkler, Speedy, Spinn3r, Spotify, Sputnik Bot, sqlmap, SSL Labs, Startpagina Linkchecker, StatusCake, Superfeedr Bot, Survey Bot, Tarmot Gezgin, TelegramBot, The Knowledge AI, theoldreader, TinEye Crawler, Tiny Tiny RSS, TLSProbe, TraceMyFile, Trendiction Bot, TurnitinBot, TweetedTimes Bot, Tweetmeme Bot, Twingly Recon, Twitterbot, UkrNet Mail Proxy, UniversalFeedParser, Uptimebot, Uptime Robot, URLAppendBot, Vagabondo, Visual Site Mapper Crawler, VK Share Button, W3C CSS Validator, W3C I18N Checker, W3C Link Checker, W3C Markup Validation Service, W3C MobileOK Checker, W3C Unified Validator, Wappalyzer, WebbCrawler, Weborama, WebPageTest, WebSitePulse, WebThumbnail, WeSEE:Search, WikiDo, Willow Internet Crawler, WooRank, WordPress, Wotbox, YaCy, Yahoo! Cache System, Yahoo! Japan BRW, Yahoo! Link Preview, Yahoo! Slurp, Yahoo Gemini, Yandex Bot, Yeti/Naverbot, Yottaa Site Monitor, Youdao Bot, Yourls, Yunyun Bot, Zao, Ze List, zgrab, Zookabot, ZumBot