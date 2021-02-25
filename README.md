DeviceDetector
==============

[![Latest Stable Version](https://poser.pugx.org/matomo/device-detector/v/stable)](https://packagist.org/packages/matomo/device-detector)
[![Latest Unstable Version](https://poser.pugx.org/matomo/device-detector/v/unstable)](https://packagist.org/packages/matomo/device-detector)
[![Total Downloads](https://poser.pugx.org/matomo/device-detector/downloads)](https://packagist.org/packages/matomo/device-detector)
[![License](https://poser.pugx.org/matomo/device-detector/license)](https://packagist.org/packages/matomo/device-detector)

## Code Status

![PHPUnit](https://github.com/matomo-org/device-detector/workflows/PHPUnit/badge.svg?branch=master)
![PHPStan](https://github.com/matomo-org/device-detector/workflows/PHPStan%20check/badge.svg?branch=master)
![PHPCS](https://github.com/matomo-org/device-detector/workflows/PHPCS%20check/badge.svg?branch=master)
![YAML Lint](https://github.com/matomo-org/device-detector/workflows/YAML%20Lint/badge.svg?branch=master)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/matomo-org/device-detector.svg)](http://isitmaintained.com/project/matomo-org/device-detector "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/matomo-org/device-detector.svg)](http://isitmaintained.com/project/matomo-org/device-detector "Percentage of issues still open")

## Description

The Universal Device Detection library that parses User Agents and detects devices (desktop, tablet, mobile, tv, cars, console, etc.), clients (browsers, feed readers, media players, PIMs, ...), operating systems, brands and models.

## Usage

Using DeviceDetector with composer is quite easy. Just add `matomo/device-detector` to your projects requirements.

```
composer require matomo/device-detector
```

And use some code like this one:


```php
require_once 'vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

// OPTIONAL: Set version truncation to none, so full versions will be returned
// By default only minor versions will be returned (e.g. X.Y)
// for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

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

* You can create a class that implement `DeviceDetector\Cache\CacheInterface`
* Or if your project uses a [PSR-6](http://www.php-fig.org/psr/psr-6/) or [PSR-16](http://www.php-fig.org/psr/psr-16/) compliant caching system (like [symfony/cache](https://github.com/symfony/cache) or [matthiasmullie/scrapbook](https://github.com/matthiasmullie/scrapbook)), you can inject them the following way:

```php
// Example with PSR-6 and Symfony
$cache = new \Symfony\Component\Cache\Adapter\ApcuAdapter();
$dd->setCache(
    new \DeviceDetector\Cache\PSR6Bridge($cache)
);

// Example with PSR-16 and ScrapBook
$cache = new \MatthiasMullie\Scrapbook\Psr16\SimpleCache(
    new \MatthiasMullie\Scrapbook\Adapters\Apc()
);
$dd->setCache(
    new \DeviceDetector\Cache\PSR16Bridge($cache)
);

// Example with Doctrine
$cache = new \Doctrine\Common\Cache\ApcuCache();
$dd->setCache(
    new \DeviceDetector\Cache\DoctrineBridge($cache)
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

- **.NET** https://github.com/totpero/DeviceDetector.NET
- **Ruby** https://github.com/podigee/device_detector
- **JavaScript/TypeScript/NodeJS** https://github.com/etienne-martin/device-detector-js
- **Python 3** https://github.com/thinkwelltwd/device_detector
- **Crystal** https://github.com/creadone/device_detector
- **Elixir** https://github.com/elixir-inspector/ua_inspector
- **Java** https://github.com/mngsk/device-detector


## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: 2021/02/25*

### List of detected operating systems:

AIX, Android, AmigaOS, Apple TV, Arch Linux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Brew, Caixa Mágica, CentOS, Chrome OS, CyanogenMod, Debian, Deepin, DragonFly, Fedora, Fenix, Firefox OS, Fire OS, Freebox, FreeBSD, FydeOS, Gentoo, GridOS, Google TV, HP-UX, Haiku OS, HasCodingOS, IRIX, Inferno, KaiOS, Knoppix, Kubuntu, GNU/Linux, Lubuntu, VectorLinux, Mac, Maemo, Mageia, Mandriva, MeeGo, MocorDroid, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, MRE, Nintendo, Nintendo Mobile, OS/2, OSF1, OpenBSD, Ordissimo, PCLinuxOS, PlayStation Portable, PlayStation, Red Hat, RISC OS, Rosa, Remix OS, RazoDroiD, Sabayon, SUSE, Sailfish OS, SeewoOS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, ThreadX, Tizen, TmaxOS, Ubuntu, watchOS, WebTV, Whale OS, Windows, Windows CE, Windows IoT, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOs, iOS, palmOS, webOS

### List of detected browsers:

115 Browser, 2345 Browser, 360 Phone Browser, 360 Browser, Avant Browser, ABrowse, ANT Fresco, ANTGalio, Aloha Browser, Aloha Browser Lite, Amaya, Amigo, Android Browser, AOL Desktop, AOL Shield, Arora, Arctic Fox, Amiga Voyager, Amiga Aweb, Atom, Atomic Web Browser, Avast Secure Browser, AVG Secure Browser, Avira Scout, AwoX, Beaker Browser, Beamrise, BlackBerry Browser, Baidu Browser, Baidu Spark, Basilisk, Beonex, BlackHawk, Bunjalloo, B-Line, Blue Browser, Borealis Navigator, Brave, BriskBard, BrowseX, Browzar, Biyubi, Byffox, Camino, CCleaner, Chedot, Centaury, Coc Coc, CoolBrowser, Colibri, Comodo Dragon, Coast, Charon, CM Browser, Chrome Frame, Headless Chrome, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, CoolNovo, CometBird, COS Browser, Cornowser, Chim Lac, ChromePlus, Chromium, Chromium GOST, Cyberfox, Cheshire, Crusta, Craving Explorer, Crazy Browser, Cunaguaro, Chrome Webview, dbrowser, Deepnet Explorer, Deledao, Delta Browser, DeskBrowse, Dolphin, Dorado, Dot Browser, Dooble, Dillo, DuckDuckGo Privacy Browser, Ecosia, Epic, Elinks, Element Browser, Elements Browser, eZ Browser, EUI Browser, GNOME Web, Espial TV Browser, Falkon, Faux Browser, Firefox Mobile iOS, Firebird, Fluid, Fennec, Firefox, Firefox Focus, Firefox Reality, Firefox Rocket, Flock, Flow, Firefox Mobile, Fireweb, Fireweb Navigator, Flast, FreeU, Galeon, Ghostery Privacy Browser, GinxDroid Browser, Glass Browser, Google Earth, GOG Galaxy, HasBrowser, Hawk Turbo Browser, hola! Browser, HotJava, Huawei Browser, IBrowse, iCab, iCab Mobile, Iridium, Iron Mobile, IceCat, IceDragon, Isivioo, Iceweasel, Internet Explorer, IE Mobile, Iron, Japan Browser, Jasmine, JavaFX, Jig Browser, Jig Browser Plus, Jio Browser, K.Browser, Kindle Browser, K-meleon, Konqueror, Kapiko, Kinza, Kiwi, Kode Browser, Kylo, Kazehakase, Cheetah Browser, LieBaoFast, LG Browser, Light, Links, Lolifox, Lovense Browser, LuaKit, Lulumi, Lunascape, Lunascape Lite, Lynx, mCent, MicroB, NCSA Mosaic, Meizu Browser, Mercury, Mobile Safari, Midori, Mobicip, MIUI Browser, Mobile Silk, Minimo, Mint Browser, Maxthon, MxNitro, Mypal, Monument Browser, MAUI WAP Browser, Navigateur Web, NFS Browser, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, Nox Browser, NetSurf, NetFront, NetFront Life, NetPositive, Netscape, NTENT Browser, Oculus Browser, Opera Mini iOS, Obigo, Odin, Odyssey Web Browser, Off By One, OhHai Browser, ONE Browser, Opera GX, Opera Neon, Opera Devices, Opera Mini, Opera Mobile, Opera, Opera Next, Opera Touch, Orca, Ordissimo, Oregano, Origin In-Game Overlay, Origyn Web Browser, Openwave Mobile Browser, OmniWeb, Otter Browser, Palm Blazer, Pale Moon, Polypane, Oppo Browser, Palm Pre, Puffin, Palm WebPro, Palmscape, Perfect Browser, Phantom Browser, Phoenix, Phoenix Browser, PlayFree Browser, Polaris, Polarity, PolyBrowser, PrivacyWall, Microsoft Edge, QQ Browser Lite, QQ Browser Mini, QQ Browser, Qutebrowser, Quark, QupZilla, Qwant Mobile, QtWebEngine, Realme Browser, Rekonq, RockMelt, Samsung Browser, Sailfish Browser, Seewo Browser, SEMC-Browser, Sogou Explorer, Safari, Safe Exam Browser, SalamWeb, SFive, Shiira, SimpleBrowser, Sizzy, Skyfire, Seraphic Sraf, Sleipnir, Slimjet, SP Browser, 7Star, Smart Lenovo Browser, Smooz, Snowshoe, Sogou Mobile Browser, Splash, Sputnik Browser, Sunrise, SuperBird, Super Fast Browser, surf, Stargon, START Internet Browser, Steam In-Game Overlay, Streamy, Swiftfox, Seznam Browser, T-Browser, t-online.de Browser, Tao Browser, TenFourFox, Tenta Browser, Tizen Browser, Tungsten, ToGate, TweakStyle, TV Bro, UBrowser, UC Browser, UC Browser HD, UC Browser Mini, UC Browser Turbo, UR Browser, Uzbl, Venus Browser, Vivaldi, vivo Browser, Vision Mobile Browser, VMware AirWatch, Wear Internet Browser, Web Explorer, WebPositive, Waterfox, Whale Browser, wOSBrowser, WeTab Browser, Yahoo! Japan Browser, Yandex Browser, Yandex Browser Lite, Yaani Browser, Yolo Browser, xStand, Xiino, Xvast, Zetakey, Zvu

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Elektra, Presto, Gecko, KHTML, NetFront, Edge, NetSurf, Servo, Goanna, EkiohFlow

### List of detected libraries:

aiohttp, curl, Faraday, Go-http-client, Google HTTP Java Client, Guzzle (PHP HTTP Client), HTTPie, HTTP_Request2, Jakarta Commons HttpClient, Java, libdnf, Mechanize, Node Fetch, OkHttp, Perl, Perl REST::Client, Postman Desktop, Python Requests, Python urllib, ReactorNetty, REST Client for Ruby, RestSharp, ScalaJ HTTP, urlgrabber (yum), Wget, WinHttp WinHttpRequest, WWW-Mechanize

### List of detected media players:

Audacious, Banshee, Boxee, Clementine, Deezer, FlyCast, Foobar2000, Google Podcasts, iTunes, Kodi, MediaMonkey, Miro, mpv, Music Player Daemon, NexPlayer, Nightingale, QuickTime, Songbird, SONOS, Stagefright, SubStream, VLC, Winamp, Windows Media Player, XBMC

### List of detected mobile apps:

, 1Password, Alexa Media Player, AndroidDownloadManager, AntennaPod, Apple News, Baidu Box App, BetBull, BeyondPod, BingWebApp, Bitsboard, bPod, CastBox, Castro, Castro 2, CGN, Clovia, Copied, Covenant Eyes, CrosswalkApp, DeviantArt, DingTalk, Discord, DoggCatcher, douban App, Evolve Podcast, Facebook, Facebook Messenger, Facebook Messenger Lite, FeedR, Flipboard App, Google Go, Google Play Newsstand, Google Plus, Google Search App, HeyTapBrowser, HP Smart, iCatcher, Instacast, Instagram App, Instapaper, KakaoTalk, Keeper Password Manager, Kik, Line, LinkedIn, Microsoft Office $1, Microsoft Office Mobile, Microsoft OneDrive, Naver, NewsArticle App, Opal Travel, Overcast, Papers, Pic Collage, Pinterest, Player FM, Pocket Casts, Podbean, Podcast & Radio Addict, Podcaster, Podcast Republic, Podcasts, Podcat, Podcatcher Deluxe, Podimo, Podkicker, Procast, Roblox, RoboForm, RSSRadio, Shopee, ShowMe, Sina Weibo, Siri, Skyeng Teachers, Skype for Business, Slack, Snapchat, SogouSearch App, SPORT1, Swoot, The Wall Street Journal, Thunder, tieba, TikTok, TopBuzz, TuneIn Radio, TuneIn Radio Pro, Twitter, U-Cursos, UnityPlayer, Viber, Wattpad, WeChat, WeChat Share Extension, WhatsApp, Whisper, WH Questions, Yahoo! Japan, Yandex, Yelp Mobile, YouTube, Zalo, Zoho Chat and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Barca, DAVdroid, Lotus Notes, MailBar, Microsoft Outlook, Outlook Express, Postbox, SeaMonkey, The Bat!, Thunderbird

### List of detected feed readers:

Akregator, Apple PubSub, BashPodder, Breaker, Downcast, FeedDemon, Feeddler RSS Reader, gPodder, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, PritTorrent, Pulp, QuiteRSS, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

2E, 3Q, 4Good, 4ife, 360, 8848, A1, Accent, Ace, Acer, Acteck, Advan, Advance, AfriOne, AGM, Ainol, Airness, Airties, AIS, Aiuto, Aiwa, Akai, Alba, Alcatel, Alcor, ALDI NORD, ALDI SÜD, Alfawise, Aligator, AllCall, AllDocube, Allview, Allwinner, Altech UEC, Altice, altron, Amazon, AMGOO, Amigoo, Amoi, Andowl, Anry, ANS, AOC, Aoson, Apple, Archos, Arian Space, Ark, ArmPhone, Arnova, ARRIS, Artel, Artizlee, Asano, Asanzo, Ask, Assistant, Asus, AT&T, Atom, Atvio, Audiovox, Avenzo, AVH, Avvio, Axxion, Azumi Mobile, BangOlufsen, Barnes & Noble, BBK, BB Mobile, BDF, Becker, Beeline, Beelink, Beetel, Bellphone, BenQ, BenQ-Siemens, Beyond, Bezkam, BGH, Bigben, BIHEE, Billion, BioRugged, Bird, Bitel, Bitmore, Bkav, Black Bear, Black Fox, Blackview, Blaupunkt, Blu, Bluboo, Bluedot, Bluegood, Bluewave, Bmobile, Bobarry, bogo, Boway, bq, Brandt, Bravis, Brondi, Bush, CAGI, Camfone, Capitel, Captiva, Carrefour, Casio, Casper, Cat, Cavion, Celcus, Celkon, Cell-C, CellAllure, Centric, CG Mobile, Changhong, Cherry Mobile, CHIA, Chico Mobile, China Mobile, Chuwi, Claresta, Clarmin, Clementoni, Cloudfone, Cloudpad, Clout, CnM, Coby Kyros, Colors, Comio, Compal, Compaq, ComTrade Tesla, Concord, ConCorde, Condor, Connectce, Connex, Conquest, Contixo, Coolpad, CORN, Cosmote, Cowon, CreNova, Crescent, Cricket, Crius Mea, Crony, Crosscall, Cube, CUBOT, CVTE, Cyrus, Daewoo, Danew, Datang, Datawind, Datsun, Dbtel, Dell, Denver, Desay, DeWalt, DEXP, Dialog, Dicam, Digi, Digicel, Digihome, Digiland, Digma, Diva, Divisat, DMM, DNS, DoCoMo, Doffler, Dolamee, Doogee, Doopro, Doov, Dopod, Doro, Droxio, Dune HD, E-Boda, E-Ceros, E-tel, Eagle, Easypix, EBEST, Echo Mobiles, ECS, EE, Einstein, EKO, Eks Mobility, EKT, ELARI, Electroneum, ELECTRONIA, Element, Elenberg, Elephone, Eltex, Energizer, Energy Sistem, Enot, Epik One, Ergo, Ericsson, Ericy, Essential, Essentielb, eSTAR, Eton, eTouch, Etuline, Eurostar, Evercoss, Evertek, Evolio, Evolveo, EvroMedia, EWIS, EXCEED, ExMobile, EXO, Explay, Extrem, Ezio, Ezze, F&U, Facebook, Fairphone, Famoco, FarEasTone, Fengxiang, Fero, FiGO, FinePower, Finlux, FireFly Mobile, Fly, FNB, Fondi, Fonos, FORME, Formuler, Forstar, Fortis, Foxconn, Freetel, Fuego, Fujitsu, G-TiDE, Garmin-Asus, Gateway, Gemini, General Mobile, GEOFOX, Geotel, Ghia, Ghong, Gigabyte, Gigaset, Gini, Ginzzu, Gionee, Globex, GLX, GOCLEVER, GoGEN, Gol Mobile, Goly, Gome, GoMobile, Google, Goophone, Gradiente, Grape, Gree, Grundig, Hafury, Haier, HannSpree, Hardkernel, Hasee, Helio, Hezire, Hi-Level, High Q, Highscreen, Hipstreet, Hisense, Hitachi, Hoffmann, Hometech, Homtom, Honeywell, Hoozo, Horizon, Hosin, Hotel, Hotwav, How, HP, HTC, Huadoo, Huawei, Humax, Hurricane, Hyrican, Hyundai, Hyve, i-Cherry, i-Joy, i-mate, i-mobile, iBall, iBerry, iBrit, IconBIT, iDroid, iGet, iHunt, Ikea, IKI Mobile, iKoMo, IKU Mobile, iLA, iLife, iMars, IMO Mobile, Impression, Inco, iNew, Infinix, InFocus, Inkti, InnJoo, Innos, Innostream, Inoi, INQ, Insignia, Intek, Intex, Invens, Inverto, Invin, iOcean, iPro, IQM, Irbis, Iris, iRola, iRulu, iSWAG, iTel, iTruck, IUNI, iVA, iView, iVooMi, iZotron, JAY-Tech, Jesy, JFone, Jiayu, Jinga, Jivi, JKL, Jolla, Just5, JVC, K-Touch, Kaan, Kaiomy, Kalley, Kanji, Karbonn, Kata, KATV1, Kazam, KDDI, Kempler & Strauss, Keneksi, Kenxinda, Kiano, Kingsun, Kivi, Klipad, Kocaso, Kodak, Kogan, Komu, Konka, Konrow, Koobee, Koolnee, Kooper, KOPO, Koridy, KRONO, Krüger&Matz, KT-Tech, KUBO, Kuliao, Kult, Kumai, Kyocera, Kzen, LAIQ, Land Rover, Landvo, Lanix, Lark, Laurus, Lava, LCT, Leader Phone, Leagoo, Ledstar, LeEco, Leff, Lemhoov, Lenco, Lenovo, Leotec, Le Pan, Lephone, Lesia, Lexand, Lexibook, LG, Lifemaxx, Lingwin, Linsar, Loewe, Logic, Logicom, Lumigon, Lumus, Luna, Luxor, LYF, M.T.T., M4tel, Macoox, Magnus, Majestic, Manhattan, Mann, Manta Multimedia, Masstel, Matrix, Maxcom, Maxtron, MAXVI, Maxwest, Maze, MDC Store, meanIT, Mecer, Mecool, Mediacom, MediaTek, Medion, MEEG, MegaFon, Meitu, Meizu, Melrose, Memup, Metz, MEU, MicroMax, Microsoft, Minix, Mintt, Mio, Miray, Mito, Mitsubishi, MIVO, MIXC, MiXzo, MLLED, MLS, Mobicel, Mobiistar, Mobiola, Mobistel, MobiWire, Mobo, Modecom, Mofut, Motorola, Movic, Mpman, MSI, MStar, MTC, MTN, Multilaser, MYFON, MyPhone, Myria, Myros, Mystery, MyTab, MyWigo, National, Navitech, Navon, NEC, Necnot, Neffos, Neomi, Netgear, NeuImage, Newgen, Newland, Newman, NewsMy, NEXBOX, Nexian, NEXON, Nextbit, NextBook, NextTab, NGM, NG Optics, Nikon, Nintendo, NOA, Noain, Nobby, Noblex, Nokia, Nomi, Nomu, Nordmende, NorthTech, Nos, Nous, NuAns, NUU Mobile, Nuvo, Nvidia, NYX Mobile, O+, O2, Oale, Obi, Odys, Ok, Okapia, OKWU, Onda, OnePlus, Onix, ONN, OpelMobile, Openbox, OPPO, Opsson, Orange, Orbic, Ordissimo, Ouki, Oukitel, OUYA, Overmax, Ovvi, Owwo, Oysters, Oyyu, OzoneHD, P-UP, Palm, Panacom, Panasonic, Pantech, PCBOX, PCD, PCD Argentina, PEAQ, Pentagram, Phicomm, Philco, Philips, Phonemax, phoneOne, Pioneer, Pixelphone, Pixus, Planet Computers, Ployer, Plum, Pluzz, PocketBook, POCO, Point of View, Polaroid, PolyPad, Polytron, Pomp, Poppox, Positivo, Positivo BGH, PPTV, Premio, Prestigio, Primepad, Primux, Prixton, PROFiLO, Proline, ProScan, Protruly, PULID, Q-Touch, Q.Bell, Qilive, QMobile, Qtek, Quantum, Quechua, Qumo, R-TV, Ramos, Ravoz, Razer, RCA Tablets, Reach, Readboy, Realme, RED, Reeder, REGAL, Rikomagic, RIM, Rinno, Ritmix, Ritzviva, Riviera, Roadrover, Rokit, Roku, Rombica, Ross&Moor, Rover, RoverPad, RT Project, RugGear, Ruio, Runbo, Ryte, S-TELL, Saba, Safaricom, Sagem, Salora, Samsung, Sanei, Sansui, Santin, Sanyo, Savio, SCBC, Schneider, Seeken, SEG, Sega, Selenga, Selevision, Selfix, SEMP TCL, Sencor, Sendo, Senkatel, Senseit, Senwa, Seuic, SFR, Sharp, Shift Phones, Shtrikh-M, Shuttle, Sico, Siemens, Sigma, Silelis, Silent Circle, Simbans, Simply, Siragon, Sky, Skyworth, Smadl, Smailo, Smart, SMARTEC, Smartfren, Smartisan, Softbank, Solone, Sonim, SONOS, Sony, Sony Ericsson, Soundmax, Soyes, Spark, SPC, Spectrum, Spice, SQOOL, Star, Starlight, Starmobile, Starway, STF Mobile, STK, Stonex, Storex, Sugar, Sumvision, Sunstech, SunVan, Sunvell, SuperSonic, Supra, Swipe, SWISSMOBILITY, Swisstone, SWTV, Symphony, Syrox, T-Mobile, Takara, Tanix, TB Touch, TCL, TD Systems, Technicolor, Technika, TechniSat, TechnoTrend, TechPad, Techwood, Teclast, Tecno Mobile, Teknosa, Tele2, Telefunken, Telego, Telenor, Telit, Tesco, Tesla, Tetratab, teXet, ThL, Thomson, Thuraya, TIANYU, Time2, Timovi, Tinai, Tinmo, TiPhone, TOKYO, Tolino, Tone, Tooky, Top House, Toplux, Torex, Toshiba, Touchmate, Transpeed, TrekStor, Trevi, Trifone, Trio, Tronsmart, True, TTEC, Tunisie Telecom, Turbo, Turbo-X, TurboKids, TVC, TWM, Twoe, TWZ, Tymes, U.S. Cellular, Ugoos, Uhans, Uhappy, Ulefone, Umax, UMIDIGI, Unihertz, Unimax, Uniscope, UNIWA, Unknown, Unnecto, Unonu, Unowhy, UTOK, UTStarcom, VAIO, Vastking, VC, Venso, Verico, Verizon, Vernee, Vertex, Vertu, Verykool, Vesta, Vestel, VGO TEL, Videocon, Videoweb, ViewSonic, Vinga, Vinsoc, Vipro, Vitelcom, Viumee, Vivax, Vivo, Vizio, VK Mobile, VKworld, Vodacom, Vodafone, Vonino, Vontar, Vorago, Vorke, Voto, VOX, Voxtel, Voyo, Vsmart, Vsun, Vulcan, VVETIME, Walton, WE, Web TV, Weimei, WellcoM, WELLINGTON, Western Digital, Westpoint, Wexler, Wieppo, Wigor, Wiko, Wileyfox, Winds, Wink, Winmax, Winnovo, Wintouch, Wiseasy, Wizz, Wolder, Wolfgang, Wonu, Woo, Wortmann, Woxter, X-BO, X-TIGI, X-View, X.Vision, Xgody, Xiaolajiao, Xiaomi, Xion, Xolo, Xoro, Xshitou, Xtouch, Yandex, Yarvik, Yes, Yezz, Yoka TV, Yota, Ytone, Yu, Yuandao, Yusun, Yxtel, Zatec, Zebra, Zeemi, Zen, Zenek, Zentality, Zfiner, ZH&K, Zidoo, Ziox, Zonda, Zopo, ZTE, Zuum, Zync, ZYQ, öwn

### List of detected bots:

360Spider, Aboundexbot, Acoon, Adbeat, AddThis.com, ADMantX, ADmantX Service Fetcher, aHrefs Bot, Alexa Crawler, Alexa Site Audit, Amazon Route53 Health Check, Amorank Spider, Analytics SEO Crawler, ApacheBench, Applebot, Arachni, archive.org bot, ArchiveBox, Ask Jeeves, AspiegelBot, Awario, Awario, Backlink-Check.de, BacklinkCrawler, Baidu Spider, Barkrowler, BazQux Reader, BDCbot, BingBot, BitlyBot, Blekkobot, BLEXBot Crawler, Bloglovin, Blogtrottr, BoardReader, BoardReader Blog Indexer, Bountii Bot, BrandVerity, Browsershots, BUbiNG, Buck, BuiltWith, Butterfly Robot, Bytespider, CareerBot, Castro 2, Catchpoint, CATExplorador, ccBot crawler, Charlotte, Choosito, Cliqzbot, CloudFlare Always Online, CloudFlare AMP Fetcher, Cloudflare Diagnostics, Cocolyzebot, Collectd, CommaFeed, ContentKing, CSS Certificate Spider, Cốc Cốc Bot, Datadog Agent, datagnionbot, Datanyze, Dataprovider, DataXu, Daum, Dazoobot, Discobot, Domain Re-Animator Bot, Domains Project, DotBot, DuckDuckGo Bot, Easou Spider, eCairn-Grabber, EMail Exractor, EmailWolf, Embedly, evc-batch, ExaBot, ExactSeek Crawler, Expanse, Ezooms, eZ Publish Link Validator, Facebook External Hit, Feedbin, FeedBurner, Feedly, Feedspot, Feed Wrangler, Fever, Findxbot, Flipboard, FreshRSS, Generic Bot, Generic Bot, Genieo Web filter, Gigablast, Gigabot, Gluten Free Crawler, Gmail Image Proxy, Goo, Googlebot, Google Cloud Scheduler, Google Favicon, Google PageSpeed Insights, Google Partner Monitoring, Google Search Console, Google Stackdriver Monitoring, Google Structured Data Testing Tool, Grammarly, Grapeshot, GTmetrix, Heart Rails Capture, Heritrix, Heureka Feed, HTTPMon, httpx, HubPages, HubSpot, ICC-Crawler, ichiro, IDG/IT, IIS Site Analysis, Inktomi Slurp, inoreader, IP-Guide Crawler, IPS Agent, Kaspersky, Kouio, Larbin web crawler, LCC, Let's Encrypt Validation, Lighthouse, Linkdex Bot, LinkedIn Bot, LinkpadBot, LTX71, Lycos, Magpie-Crawler, MagpieRSS, Mail.Ru Bot, masscan, Mastodon Bot, Meanpath Bot, MetaInspector, MetaJobBot, MicroAdBot, Mixrank Bot, MJ12 Bot, Mnogosearch, MojeekBot, Monitor.Us, Munin, MuscatFerret, Nagios check_http, NalezenCzBot, nbertaupete95, Netcraft Survey Bot, netEstate, NetLyzer FastProbe, NetResearchServer, Netvibes, NewsBlur, NewsGator, Nimbostratus Bot, NLCrawler, Nmap, Notify Ninja, Nutch-based Bot, Nuzzel, oBot, Octopus, Omgili bot, Openindex Spider, OpenLinkProfiler, OpenWebSpider, Orange Bot, Outbrain, PagePeeker, PageThing, PaperLiBot, parse.ly, Petal Bot, Phantomas, PHP Server Monitor, Picsearch bot, PingAdmin.Ru, Pingdom Bot, Pinterest, PocketParser, Pompos, PritTorrent, Project Resonance, PRTG Network Monitor, QuerySeekerSpider, Quora Bot, Quora Link Preview, Qwantify, Rainmeter, RamblerMail Image Proxy, Reddit Bot, Riddler, Robozilla, Rogerbot, ROI Hunter, RSSRadio Bot, SafeDNSBot, Scooter, ScoutJet, Scrapy, Screaming Frog SEO Spider, ScreenerBot, Semantic Scholar Bot, Semrush Bot, Sensika Bot, Sentry Bot, Seobility, SEOENGBot, SEOkicks-Robot, Seoscanners.net, Serendeputy Bot, Server Density, Seznam Bot, Seznam Email Proxy, Seznam Zbozi.cz, ShopAlike, Shopify Partner, ShopWiki, SilverReader, SimplePie, SISTRIX Crawler, SISTRIX Optimizer, Site24x7 Website Monitoring, Siteimprove, SiteSucker, Sixy.ch, Skype URI Preview, Slackbot, SMTBot, Snapchat Proxy, Sogou Spider, Soso Spider, Sparkler, Speedy, Spinn3r, Spotify, Sprinklr, Sputnik Bot, sqlmap, SSL Labs, Startpagina Linkchecker, StatusCake, Superfeedr Bot, Survey Bot, Tarmot Gezgin, TelegramBot, The Knowledge AI, theoldreader, TinEye Crawler, Tiny Tiny RSS, TLSProbe, TraceMyFile, Trendiction Bot, TurnitinBot, TweetedTimes Bot, Tweetmeme Bot, Twingly Recon, Twitterbot, UkrNet Mail Proxy, UniversalFeedParser, Uptimebot, Uptime Robot, URLAppendBot, Vagabondo, Velen Public Web Crawler, Vercel Bot, VeryHip, Visual Site Mapper Crawler, VK Share Button, W3C CSS Validator, W3C I18N Checker, W3C Link Checker, W3C Markup Validation Service, W3C MobileOK Checker, W3C Unified Validator, Wappalyzer, WebbCrawler, WebDataStats, Weborama, WebPageTest, WebSitePulse, WebThumbnail, WeSEE:Search, WhatCMS, WikiDo, Willow Internet Crawler, WooRank, WordPress, Wotbox, XenForo, YaCy, Yahoo! Cache System, Yahoo! Japan BRW, Yahoo! Link Preview, Yahoo! Slurp, Yahoo Gemini, Yandex Bot, Yeti/Naverbot, Yottaa Site Monitor, Youdao Bot, Yourls, Yunyun Bot, Zao, Ze List, zgrab, Zookabot, ZumBot
