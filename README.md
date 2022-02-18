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
Methods check device type:
```php
$dd->isSmartphone();
$dd->isFeaturePhone();
$dd->isTablet();
$dd->isPhablet();
$dd->isConsole();
$dd->isPortableMediaPlayer();
$dd->isCarBrowser();
$dd->isTV();
$dd->isSmartDisplay();
$dd->isSmartSpeaker();
$dd->isCamera();
$dd->isWearable();
$dd->isPeripheral();
```
Methods check client type:
```php
$dd->isBrowser();
$dd->isFeedReader();
$dd->isMobileApp();
$dd->isPIM();
$dd->isLibrary();
$dd->isMediaPlayer();
```
Get OS family:
```php
use DeviceDetector\Parser\OperatingSystem;

$osFamily = OperatingSystem::getOsFamily($dd->getOs('name'));
```
Get browser family:
```php
use DeviceDetector\Parser\Client\Browser;

$browserFamily = Browser::getBrowserFamily($dd->getClient('name'));
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
- **NodeJS** https://github.com/sanchezzzhak/node-device-detector
- **Python 3** https://github.com/thinkwelltwd/device_detector
- **Crystal** https://github.com/creadone/device_detector
- **Elixir** https://github.com/elixir-inspector/ua_inspector
- **Java** https://github.com/mngsk/device-detector


## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: 2022/02/18*

### List of detected operating systems:

AIX, Android, AmigaOS, tvOS, Arch Linux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Brew, Caixa Mágica, CentOS, Chrome OS, CyanogenMod, Debian, Deepin, DragonFly, DVKBuntu, Fedora, Fenix, Firefox OS, Fire OS, Freebox, FreeBSD, FydeOS, Gentoo, GridOS, Google TV, HP-UX, Haiku OS, iPadOS, HarmonyOS, HasCodingOS, IRIX, Inferno, Java ME, KaiOS, Knoppix, KreaTV, Kubuntu, GNU/Linux, Lubuntu, Lumin OS, VectorLinux, Mac, Maemo, Mageia, Mandriva, MeeGo, MocorDroid, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, MRE, Nintendo, Nintendo Mobile, OS/2, OSF1, OpenBSD, OpenWrt, Opera TV, Ordissimo, PCLinuxOS, PlayStation Portable, PlayStation, Red Hat, RISC OS, Roku OS, Rosa, Remix OS, REX, RazoDroiD, Sabayon, SUSE, Sailfish OS, SeewoOS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, ThreadX, Tizen, TmaxOS, Ubuntu, watchOS, WebTV, Whale OS, Windows, Windows CE, Windows IoT, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOS, iOS, palmOS, webOS

### List of detected browsers:

115 Browser, 2345 Browser, 360 Phone Browser, 360 Browser, 7654 Browser, Avant Browser, ABrowse, ANT Fresco, ANTGalio, Aloha Browser, Aloha Browser Lite, Amaya, Amigo, Android Browser, AOL Desktop, AOL Shield, Arora, Arctic Fox, Amiga Voyager, Amiga Aweb, Arvin, Atom, Atomic Web Browser, Avast Secure Browser, AVG Secure Browser, Avira Scout, AwoX, Beaker Browser, Beamrise, BlackBerry Browser, Baidu Browser, Baidu Spark, Basilisk, Beonex, BlackHawk, Bunjalloo, B-Line, Blue Browser, Bonsai, Borealis Navigator, Brave, BriskBard, BrowseX, Browzar, Biyubi, Byffox, Camino, CCleaner, ChanjetCloud, Chedot, Centaury, Coc Coc, CoolBrowser, Colibri, Comodo Dragon, Coast, Charon, CM Browser, Chrome Frame, Headless Chrome, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, CoolNovo, CometBird, COS Browser, Cornowser, Chim Lac, ChromePlus, Chromium, Chromium GOST, Cyberfox, Cheshire, Crusta, Craving Explorer, Crazy Browser, Cunaguaro, Chrome Webview, dbrowser, Peeps dBrowser, Deepnet Explorer, deg-degan, Deledao, Delta Browser, DeskBrowse, Dolphin, Dorado, Dot Browser, Dooble, Dillo, DuckDuckGo Privacy Browser, Ecosia, Edge WebView, Epic, Elinks, Element Browser, Elements Browser, eZ Browser, EUI Browser, GNOME Web, Espial TV Browser, Falkon, Faux Browser, Firefox Mobile iOS, Firebird, Fluid, Fennec, Firefox, Firefox Focus, Firefox Reality, Firefox Rocket, Flock, Flow, Firefox Mobile, Fireweb, Fireweb Navigator, Flash Browser, Flast, FreeU, Galeon, Ghostery Privacy Browser, GinxDroid Browser, Glass Browser, Google Earth, Google Earth Pro, GOG Galaxy, GoBrowser, Harman Browser, HasBrowser, Hawk Turbo Browser, Hawk Quick Browser, Helio, hola! Browser, HotJava, Huawei Browser Mobile, Huawei Browser, iBrowser, iBrowser Mini, IBrowse, iCab, iCab Mobile, Iridium, Iron Mobile, IceCat, IceDragon, Isivioo, Iceweasel, Internet Explorer, IE Mobile, Iron, Japan Browser, Jasmine, JavaFX, Jig Browser, Jig Browser Plus, Jio Browser, K.Browser, Kindle Browser, K-meleon, Konqueror, Kapiko, Kinza, Kiwi, Kode Browser, Kylo, Kazehakase, Cheetah Browser, Lagatos Browser, LieBaoFast, LG Browser, Light, Links, Lolifox, Lovense Browser, LT Browser, LuaKit, Lulumi, Lunascape, Lunascape Lite, Lynx, Mandarin, mCent, MicroB, NCSA Mosaic, Meizu Browser, Mercury, Mobile Safari, Midori, Mobicip, MIUI Browser, Mobile Silk, Minimo, Mint Browser, Maxthon, Maelstrom, MxNitro, Mypal, Monument Browser, MAUI WAP Browser, Navigateur Web, NFS Browser, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, Nox Browser, NetSurf, NetFront, NetFront Life, NetPositive, Netscape, NTENT Browser, Oculus Browser, Opera Mini iOS, Obigo, Odin, OceanHero, Odyssey Web Browser, Off By One, OhHai Browser, ONE Browser, Opera GX, Opera Neon, Opera Devices, Opera Mini, Opera Mobile, Opera, Opera Next, Opera Touch, Orca, Ordissimo, Oregano, Origin In-Game Overlay, Origyn Web Browser, Openwave Mobile Browser, OpenFin, OmniWeb, Otter Browser, Palm Blazer, Pale Moon, Polypane, Oppo Browser, Palm Pre, Puffin, Palm WebPro, Palmscape, Perfect Browser, Phantom Browser, Phoenix, Phoenix Browser, PlayFree Browser, Polaris, Polarity, PolyBrowser, PrivacyWall, PSI Secure Browser, Reqwireless WebViewer, Microsoft Edge, Qazweb, QQ Browser Lite, QQ Browser Mini, QQ Browser, Qutebrowser, Quark, QupZilla, Qwant Mobile, QtWebEngine, Realme Browser, Rekonq, RockMelt, Samsung Browser, Sailfish Browser, Seewo Browser, SEMC-Browser, Sogou Explorer, Safari, Safari Technology Preview, Safe Exam Browser, SalamWeb, Secure Browser, SFive, Shiira, SimpleBrowser, Sizzy, Skyfire, Seraphic Sraf, SiteKiosk, Sleipnir, Slimjet, SP Browser, Stampy Browser, 7Star, Smart Lenovo Browser, Smooz, Snowshoe, Sogou Mobile Browser, Spectre Browser, Splash, Sputnik Browser, Sunrise, SuperBird, Super Fast Browser, Sushi Browser, surf, Stargon, START Internet Browser, Steam In-Game Overlay, Streamy, Swiftfox, Seznam Browser, T+Browser, T-Browser, t-online.de Browser, Tao Browser, TenFourFox, Tenta Browser, Tizen Browser, Tungsten, ToGate, TweakStyle, TV Bro, UBrowser, UC Browser, UC Browser HD, UC Browser Mini, UC Browser Turbo, UR Browser, Uzbl, Venus Browser, Vivaldi, vivo Browser, Vision Mobile Browser, VMware AirWatch, Wear Internet Browser, Web Explorer, WebPositive, Waterfox, Whale Browser, wOSBrowser, WeTab Browser, Yahoo! Japan Browser, Yandex Browser, Yandex Browser Lite, Yaani Browser, Yolo Browser, xStand, Xiino, Xvast, Zetakey, Zvu

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Elektra, Presto, Gecko, KHTML, NetFront, Edge, NetSurf, Servo, Goanna, EkiohFlow

### List of detected libraries:

aiohttp, Akka HTTP, Aria2, C++ REST SDK, curl, Embarcadero URI Client, Faraday, Go-http-client, Google HTTP Java Client, GRequests, Guzzle (PHP HTTP Client), gvfs, HTTPie, HTTP_Request2, Jakarta Commons HttpClient, Java, libdnf, LUA OpenResty NGINX, Mechanize, Mikrotik Fetch, Node Fetch, OkHttp, Perl, Perl REST::Client, Postman Desktop, Python Requests, Python urllib, ReactorNetty, REST Client for Ruby, RestSharp, ScalaJ HTTP, SlimerJS, uclient-fetch, Unirest for Java, urlgrabber (yum), uTorrent, Wget, WinHttp WinHttpRequest, WWW-Mechanize

### List of detected media players:

Audacious, Banshee, Boxee, Clementine, Deezer, Downcast, FlyCast, Foobar2000, foobar2000, Google Podcasts, HTC Streaming Player, iTunes, Kodi, MediaMonkey, Miro, MPlayer, mpv, Music Player Daemon, NexPlayer, Nightingale, QuickTime, Songbird, SONOS, Sony Media Go, Stagefright, SubStream, VLC, Winamp, Windows Media Player, XBMC

### List of detected mobile apps:

1Password, 2tch, Adobe Creative Cloud, Adobe Synchronizer, Aha Radio 2, AIDA64, Alexa Media Player, AliExpress, AndroidDownloadManager, AntennaPod, Apple News, ASUS Updater, Avid Link, Background Intelligent Transfer Service, Baidu Box App, Bank Millenium, Battle.net, BB2C, BBC News, Be Focused, BetBull, BeyondPod, Bible KJV, Binance, BingWebApp, Bitsboard, Blackboard, Blitz, Blue Proxy, BlueStacks, Bookshelf, Bose Music, bPod, CastBox, Castro, Castro 2, CCleaner, CGN, ChMate, Chrome Update, Ciisaa, Citrix Workspace, Clovia, COAF SMART Citizen, Copied, Cortana, Covenant Eyes, CPU-Z, CrosswalkApp, DevCasts, DeviantArt, DingTalk, Discord, DoggCatcher, douban App, Downcast, Dr. Watson, DStream Air, Edge Update, Emby Theater, Epic Games Launcher, ESET Remote Administrator, eToro, Evolve Podcast, Expedia, F-Secure SAFE, Facebook, Facebook Audience Network, Facebook Groups, Facebook Lite, Facebook Messenger, Facebook Messenger Lite, FeedR, Flipboard App, Flipp, Focus Keeper, Focus Matrix, Gaana, Git, GitHub Desktop, GlobalProtect, GoNative, Google Drive, Google Fiber TV, Google Go, Google Play Newsstand, Google Plus, Google Podcasts, Google Search App, Google Tag Manager, GroupMe, HandBrake, HeyTapBrowser, Hik-Connect, HiSearch, HP Smart, iCatcher, IMO HD Video Calls & Chat, IMO International Calls & Chat, Instabridge, Instacast, Instagram App, Instapaper, JaneStyle, Jungle Disk, KakaoTalk, Keeper Password Manager, Kik, Landis+Gyr AIM Browser, Line, LinkedIn, Logi Options+, MBolsa, MEmpresas, Mercantile Bank of Michigan, Meta Business Suite, MetaTrader, Microsoft Bing Search, Microsoft Office $1, Microsoft Office Mobile, Microsoft OneDrive, Microsoft Store, My Bentley, My World, Naver, NET.mede, Netflix, NewsArticle App, Nextcloud, NPR One, NTV Mobil, NuMuKi Browser, Odnoklassniki, OfferUp, Opal Travel, Opera News, Orange Radio, Overcast, Paint by Number, Pandora, Papers, Pic Collage, Pinterest, Player FM, Pocket Casts, Podbean, Podcast & Radio Addict, Podcaster, Podcast Republic, Podcasts, Podcat, Podcatcher Deluxe, Podimo, Podkicker$1, PowerShell, Procast, Q-municate, qBittorrent, QQMusic, QuickCast, Quick Search TV, Radio Italiane, RadioPublic, Rave Social, Razer Synapse, RDDocuments, rekordbox, Roblox, RoboForm, Rocket Chat, RSSRadio, Safari Search Helper, Samsung Magician, Shopee, ShowMe, Sina Weibo, Siri, Skyeng, Skyeng Teachers, Skype, Skype for Business, Slack, Snapchat, SogouSearch App, SPORT1, Streamlabs OBS, Strimio, Swoot, Teams, The Wall Street Journal, Theyub, Thunder, tieba, TikTok, TopBuzz, TradingView, TuneIn Radio, TuneIn Radio Pro, TVirl, twinkle, Twitter, Twitterrific, U-Cursos, Uconnect LIVE, Unibox, UnityPlayer, Viber, Visual Studio Code, Vuhuv, Wattpad, Wayback Machine, WebDAV, WeChat, WeChat Share Extension, WhatsApp, Whisper, WH Questions, Windows CryptoAPI, Windows Delivery Optimization, Windows Push Notification Services, Windows Update Agent, Wireshark, Wirtschafts Woche, Word Cookies!, Y8 Browser, Yahoo! Japan, YakYak, Yandex, Yelp Mobile, YouTube, Zalo, ZEPETO, Zoho Chat and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Barca, Basecamp, BathyScaphe, DAVdroid, Evernote, Franz, JaneView, Live5ch, Lotus Notes, MailBar, Mailbird, Mailspring, Microsoft Outlook, Notion, Outlook Express, Postbox, Raindrop.io, Rambox Pro, SeaMonkey, The Bat!, Thunderbird, Windows Mail, Yahoo Mail

### List of detected feed readers:

Akregator, Apple PubSub, BashPodder, Breaker, FeedDemon, Feeddler RSS Reader, gPodder, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, PritTorrent, Pulp, QuiteRSS, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

2E, 3GNET, 3GO, 3Q, 4Good, 4ife, 7 Mobile, 360, 8848, A1, Accent, Ace, Acer, Acteck, Adronix, Advan, Advance, AfriOne, AGM, AG Mobile, AIDATA, Ainol, Airness, AIRON, Airties, AIS, Aiuto, Aiwa, Akai, AKIRA, Alba, Alcatel, Alcor, ALDI NORD, ALDI SÜD, Alfawise, Aligator, AllCall, AllDocube, Allview, Allwinner, Alps, Altech UEC, Altice, altron, Amazon, AMCV, AMGOO, Amigoo, Amino, Amoi, Andowl, Anker, Anry, ANS, AOC, Aocos, AOpen, Aoson, AOYODKG, Apple, Archos, Arian Space, Ark, ArmPhone, Arnova, ARRIS, Artel, Artizlee, ArtLine, Asano, Asanzo, Ask, Aspera, Assistant, Astro, Asus, AT&T, Atmaca Elektronik, Atom, Atvio, Audiovox, AURIS, Avenzo, AVH, Avvio, Awow, Axioo, Axxion, Azumi Mobile, b2m, BangOlufsen, Barnes & Noble, BBK, BB Mobile, BDF, BDQ, Becker, Beeline, Beelink, Beetel, Beista, Bellphone, Benco, BenQ, BenQ-Siemens, Benzo, Beyond, Bezkam, BGH, Bigben, BIHEE, BilimLand, Billion, BioRugged, Bird, Bitel, Bitmore, Bkav, Black Bear, Black Fox, Blackview, Blaupunkt, Bleck, Blloc, Blow, Blu, Bluboo, Bluebird, Bluedot, Bluegood, Bluewave, BMAX, Bmobile, Bobarry, bogo, Boway, bq, Brandt, Bravis, BrightSign, Brondi, BS Mobile, Bubblegum, Bundy, Bush, CAGI, Camfone, Canal Digital, Capitel, Captiva, Carrefour, Casio, Casper, Cat, Cavion, Celcus, Celkon, Cell-C, CellAllure, Cellution, Centric, CG Mobile, CGV, Changhong, Cherry Mobile, Chico Mobile, China Mobile, Chuwi, Claresta, Clarmin, Clementoni, Cloud, Cloudfone, Cloudpad, Clout, CnM, Cobalt, Coby Kyros, Colors, Comio, Compal, Compaq, ComTrade Tesla, Concord, ConCorde, Condor, Connectce, Connex, Conquest, Contixo, Coolpad, CORN, Cosmote, Covia, Cowon, CreNova, Crescent, Cricket, Crius Mea, Crony, Crosscall, Crown, Cube, CUBOT, CVTE, Cyrus, Daewoo, Danew, Datalogic, Datamini, Datang, Datawind, Datsun, Dazen, Dbtel, Dell, Denver, Desay, DeWalt, DEXP, DF, Dialog, Dicam, Digi, Digicel, DIGIFORS, Digihome, Digiland, Digma, DING DING, DISH, Ditecma, Diva, Divisat, DIXON, DMM, DNS, DoCoMo, Doffler, Dolamee, Doogee, Doopro, Doov, Dopod, Doro, Dragon Touch, Dreamgate, Droxio, Dune HD, DUNNS Mobile, E-Boda, E-Ceros, E-tel, Eagle, Easypix, EBEST, Echo Mobiles, ECON, ECS, EE, Einstein, EKO, Eks Mobility, EKT, ELARI, Electroneum, ELECTRONIA, Elekta, Element, Elenberg, Elephone, Eltex, Ematic, Energizer, Energy Sistem, Engel, Enot, Epik One, Epson, Ergo, Ericsson, Ericy, Erisson, Essential, Essentielb, eSTAR, Eton, eTouch, Etuline, Eurocase, Eurostar, Evercoss, Evertek, Evolio, Evolveo, Evoo, EVPAD, EvroMedia, EWIS, EXCEED, Exmart, ExMobile, EXO, Explay, Extrem, Ezio, Ezze, F&U, F2 Mobile, F150, Facebook, Fairphone, Famoco, Fantec, FaRao Pro, FarEasTone, Fengxiang, FEONAL, Fero, FiGi, FiGO, FiiO, FinePower, Finlux, FireFly Mobile, FISE, Fly, FLYCAT, FMT, FNB, FNF, Fondi, Fonos, FORME, Formuler, Forstar, Fortis, Fourel, Four Mobile, Foxconn, Freetel, Fuego, Fujitsu, Funai, Fusion5, G-TiDE, G-Touch, Galaxy Innovations, Garmin-Asus, Gateway, Gemini, General Mobile, Genesis, GEOFOX, Geotel, Geotex, GFive, Ghia, Ghong, Ghost, Gigabyte, Gigaset, Gini, Ginzzu, Gionee, Globex, Glofiish, GLONYX, GLX, GOCLEVER, Gocomma, GoGEN, Gol Mobile, Goly, Gome, GoMobile, Google, Goophone, Gooweel, Gplus, Gradiente, Grape, Gree, Greentel, Gresso, Gretel, Grundig, Gtel, H96, Hafury, Haier, HannSpree, Hardkernel, Hasee, Helio, HERO, Hezire, Hi, Hi-Level, High Q, Highscreen, HiMax, Hipstreet, Hisense, Hitachi, Hitech, HKPro, Hoffmann, Hometech, Homtom, Honeywell, Hoozo, Horizon, Horizont, Hosin, Hotel, Hot Pepper, Hotwav, How, HP, HTC, Huadoo, Huavi, Huawei, Humax, Hurricane, Huskee, Hyrican, Hyundai, Hyve, i-Cherry, i-Joy, i-mate, i-mobile, iBall, iBerry, iBrit, IconBIT, iData, iDroid, iGet, iHunt, Ikea, IKI Mobile, iKoMo, iKon, IKU Mobile, iLA, iLife, iMan, iMars, IMO Mobile, Impression, INCAR, Inch, Inco, iNew, Infinix, InFocus, InfoKit, Inkti, InnJoo, Innos, Innostream, Inoi, INQ, Insignia, INSYS, Intek, Intex, Invens, Inverto, Invin, iOcean, iOutdoor, iPro, iQ&T, IQM, IRA, Irbis, Iris, iRola, iRulu, iSafe Mobile, iSWAG, IT, iTel, iTruck, IUNI, iVA, iView, iVooMi, ivvi, iZotron, JAY-Tech, Jedi, Jeka, Jesy, JFone, Jiake, Jiayu, Jinga, Jivi, JKL, Jolla, Juniper Systems, Just5, JVC, K-Touch, Kaan, Kaiomy, Kalley, Kanji, Karbonn, Kata, KATV1, Kazam, Kazuna, KDDI, Kempler & Strauss, Keneksi, Kenxinda, Kiano, Kingbox, Kingsun, KINGZONE, Kiowa, Kivi, Klipad, Kocaso, Kodak, Kogan, Komu, Konka, Konrow, Koobee, Koolnee, Kooper, KOPO, Koridy, Koslam, KREZ, KRIP, KRONO, Krüger&Matz, KT-Tech, KUBO, Kuliao, Kult, Kumai, Kurio, Kvant, Kyocera, Kyowon, Kzen, L-Max, LAIQ, Land Rover, Landvo, Lanix, Lark, Laurus, Lava, LCT, Leader Phone, Leagoo, Leben, Ledstar, LeEco, Leff, LEMFO, Lemhoov, Lenco, Lenovo, Leotec, Le Pan, Lephone, Lesia, Lexand, Lexibook, LG, Liberton, Lifemaxx, Lingwin, Linnex, Linsar, Loewe, Logic, Logicom, Loview, LT Mobile, Lumigon, Lumus, Luna, Luxor, LYF, M-Horse, M-Tech, M.T.T., M4tel, MAC AUDIO, Macoox, Mafe, Magicsee, Magnus, Majestic, Malata, Manhattan, Mann, Manta Multimedia, Mantra, Mara, Massgo, Masstel, Mastertech, Matrix, Maxcom, Maximus, Maxtron, MAXVI, Maxwest, Maze, Maze Speed, MBOX, MDC Store, MDTV, meanIT, Mecer, Mecool, Mediacom, MediaTek, Medion, MEEG, MegaFon, Meitu, Meizu, Melrose, Memup, Metz, MEU, MicroMax, Microsoft, Microtech, Minix, Mintt, Mio, Mione, Miray, Mito, Mitsubishi, Mitsui, MIVO, MIXC, MiXzo, MLLED, MLS, MMI, Mobicel, MobiIoT, Mobiistar, Mobiola, Mobistel, MobiWire, Mobo, Modecom, Mofut, Motorola, Movic, mPhone, Mpman, MSI, MStar, MTC, MTN, Multilaser, MYFON, MyGica, Mymaga, MyPhone, Myria, Myros, Mystery, MyTab, MyWigo, Nabi, Naomi Phone, National, Navcity, Navitech, Navitel, Navon, NavRoad, NEC, Necnot, Neffos, Neo, Neomi, Neon IQ, Netgear, NeuImage, New Balance, Newgen, Newland, Newman, Newsday, NewsMy, NEXBOX, Nexian, NEXON, Nextbit, NextBook, NextTab, NGM, NG Optics, Nikon, Nintendo, NOA, Noain, Nobby, Noblex, NOBUX, NOGA, Nokia, Nomi, Nomu, Nordmende, NorthTech, Nos, Nous, Novex, NuAns, Nubia, NUU Mobile, Nuvo, Nvidia, NYX Mobile, O+, O2, Oale, OASYS, Obabox, Obi, Oculus, Odys, OINOM, Ok, Okapia, OKSI, OKWU, OMIX, Onda, OnePlus, Onix, ONN, ONYX BOOX, Ookee, OpelMobile, Openbox, OPPO, Opsson, Orange, Orbic, Orbita, Ordissimo, Orion, Ouki, Oukitel, OUYA, Overmax, Ovvi, Owwo, Oysters, Oyyu, OzoneHD, P-UP, Packard Bell, Paladin, Palm, Panacom, Panasonic, Pantech, Parrot Mobile, PCBOX, PCD, PCD Argentina, PEAQ, Pendoo, Pentagram, Perfeo, Phicomm, Philco, Philips, Phonemax, phoneOne, Pico, Pioneer, PiPO, Pixela, Pixelphone, Pixus, Planet Computers, Ployer, Plum, Pluzz, PocketBook, POCO, Point of View, Polar, PolarLine, Polaroid, Polestar, PolyPad, Polytron, Pomp, Poppox, POPTEL, Porsche, Positivo, Positivo BGH, PPTV, Premio, Prestigio, Primepad, Primux, Prixton, PROFiLO, Proline, Prology, ProScan, Protruly, ProVision, PULID, Q-Box, Q-Touch, Q.Bell, Qilive, QMobile, Qnet Mobile, QTECH, Qtek, Quantum, Qubo, Quechua, Qumo, R-TV, Rakuten, Ramos, Raspberry, Ravoz, Razer, RCA Tablets, Reach, Readboy, Realme, RED, Redfox, Reeder, REGAL, Remdun, Retroid Pocket, Revo, Rikomagic, RIM, Rinno, Ritmix, Ritzviva, Riviera, Rivo, Roadrover, Rokit, Roku, Rombica, Ross&Moor, Rover, RoverPad, RoyQueen, RT Project, RugGear, Ruio, Runbo, Ryte, S-TELL, S2Tel, Saba, Safaricom, Sagem, Saiet, Salora, Samsung, Sanei, Sansui, Santin, Sanyo, Savio, SCBC, Schneider, Seatel, Seeken, SEG, Sega, Selecline, Selenga, Selevision, Selfix, SEMP TCL, Sencor, Sendo, Senkatel, Senseit, Senwa, Seuic, SFR, Sharp, Shift Phones, Shivaki, Shtrikh-M, Shuttle, Sico, Siemens, Sigma, Silelis, Silent Circle, Simbans, Simply, Singtech, Siragon, Sirin labs, SK Broadband, SKG, Sky, Skyworth, Smadl, Smailo, Smart, Smartab, SmartBook, SMARTEC, Smart Electronic, Smartfren, Smartisan, Smarty, Smooth Mobile, Smotreshka, Softbank, Soho Style, SOLE, SOLO, Solone, Sonim, SONOS, Sony, Sony Ericsson, Soundmax, Soyes, Spark, SPC, Spectralink, Spectrum, Spice, Sprint, SQOOL, Star, Starlight, Starmobile, Starway, Starwind, STF Mobile, STG Telecom, STK, Stonex, Storex, StrawBerry, STRONG, Stylo, Subor, Sugar, Sumvision, Sunmi, Sunny, Sunstech, SunVan, Sunvell, SUNWIND, SuperSonic, SuperTab, Supra, Suzuki, Swipe, SWISSMOBILITY, Swisstone, SWTV, Symphony, Syrox, T-Mobile, TAG Tech, Taiga System, Takara, Tambo, Tanix, TB Touch, TCL, TD Systems, Technicolor, Technika, TechniSat, Technopc, TechnoTrend, TechPad, Techwood, Teclast, Tecno Mobile, TEENO, Teknosa, Tele2, Telefunken, Telego, Telenor, Telia, Telit, Telpo, Tesco, Tesla, Tetratab, teXet, ThL, Thomson, Thuraya, TIANYU, Tigers, Time2, Timovi, Tinai, Tinmo, TiPhone, TiVo, TOKYO, Tolino, Tone, Tooky, Topelotek, Top House, Toplux, Topway, Torex, TOSCIDO, Toshiba, Touchmate, Transpeed, TrekStor, Trevi, Trident, Trifone, Trio, Tronsmart, True, True Slim, TTEC, TuCEL, Tunisie Telecom, Turbo, Turbo-X, TurboKids, TurboPad, Turkcell, TVC, TWM, Twoe, TWZ, Tymes, Türk Telekom, U.S. Cellular, Ugoos, Uhans, Uhappy, Ulefone, Umax, UMIDIGI, Unihertz, Unimax, Uniscope, UNIWA, Unknown, Unnecto, Unonu, Unowhy, Urovo, UTime, UTOK, UTStarcom, UZ Mobile, v-mobile, VAIO, Vankyo, Vargo, Vastking, VAVA, VC, Vega, Venso, Venturer, VEON, Verico, Verizon, Vernee, Verssed, Vertex, Vertu, Verykool, Vesta, Vestel, Vexia, VGO TEL, Videocon, Videoweb, ViewSonic, Vinabox, Vinga, Vinsoc, Vipro, Virzo, Vision Touch, Vitelcom, Viumee, Vivax, Vivo, VIWA, Vizio, VK Mobile, VKworld, Vodacom, Vodafone, Vonino, Vontar, Vorago, Vorke, Voto, VOX, Voxtel, Voyo, Vsmart, Vsun, Vulcan, VVETIME, Walton, WE, Web TV, Weimei, WellcoM, WELLINGTON, Western Digital, Westpoint, Wexler, Wieppo, Wigor, Wiko, Wileyfox, Winds, Wink, Winmax, Winnovo, Wintouch, Wiseasy, WIWA, Wizz, Wolder, Wolfgang, Wolki, Wonu, Woo, Wortmann, Woxter, X-BO, X-TIGI, X-View, X.Vision, XGIMI, Xgody, Xiaolajiao, Xiaomi, Xion, Xolo, Xoro, Xshitou, Xtouch, Xtratech, Yandex, Yarvik, YASIN, Yes, Yezz, Yoka TV, Yota, YOTOPT, Ytone, Yu, Yuandao, YUHO, Yuno, Yusun, Yxtel, Zaith, Zatec, Zebra, Zeemi, Zen, Zenek, Zentality, Zfiner, ZH&K, Zidoo, ZIFRO, Ziox, Zonda, Zopo, ZTE, Zuum, Zync, ZYQ, Zyrex, öwn

### List of detected bots:

360Spider, Aboundexbot, Acoon, Adbeat, AddThis.com, ADMantX, ADmantX Service Fetcher, Adsbot, aHrefs Bot, Alexa Crawler, Alexa Site Audit, Amazon Bot, Amazon Route53 Health Check, Amorank Spider, Analytics SEO Crawler, ApacheBench, Applebot, AppSignalBot, Arachni, archive.org bot, ArchiveBox, Ask Jeeves, AspiegelBot, Awario, Awario, Backlink-Check.de, BacklinkCrawler, Baidu Spider, Barkrowler, BazQux Reader, BDCbot, Better Uptime Bot, BingBot, BitlyBot, Blekkobot, BLEXBot Crawler, Bloglovin, Blogtrottr, BoardReader, BoardReader Blog Indexer, Bountii Bot, BrandVerity, Browsershots, BUbiNG, Buck, BuiltWith, Butterfly Robot, Bytespider, CareerBot, Castro 2, Catchpoint, CATExplorador, ccBot crawler, CensysInspect, Charlotte, Choosito, Cincraw, CISPA Web Analyzer, Cliqzbot, CloudFlare Always Online, CloudFlare AMP Fetcher, Cloudflare Diagnostics, Cocolyzebot, Collectd, colly, CommaFeed, Comscore, ContentKing, Cookiebot, Crawldad, CSS Certificate Spider, Cốc Cốc Bot, Datadog Agent, DataForSeoBot, datagnionbot, Datanyze, Dataprovider, DataXu, Daum, Dazoobot, Discobot, Discord Bot, Domain Re-Animator Bot, Domains Project, DotBot, Dotcom Monitor, DuckDuckGo Bot, Easou Spider, eCairn-Grabber, EMail Exractor, EmailWolf, Embedly, evc-batch, ExaBot, ExactSeek Crawler, Expanse, Ezooms, eZ Publish Link Validator, Facebook External Hit, Feedbin, FeedBurner, Feedly, Feedspot, Feed Wrangler, Fever, Findxbot, Flipboard, FreshRSS, GDNP, Generic Bot, Generic Bot, Genieo Web filter, Gigablast, Gigabot, Gluten Free Crawler, Gmail Image Proxy, Gobuster, Goo, Googlebot, Google Cloud Scheduler, Google Favicon, Google PageSpeed Insights, Google Partner Monitoring, Google Search Console, Google Stackdriver Monitoring, Google StoreBot, Google Structured Data Testing Tool, Gowikibot, Grammarly, Grapeshot, GTmetrix, Hatena Favicon, Heart Rails Capture, Heritrix, Heureka Feed, HTTPMon, httpx, HuaweiWebCatBot, HubPages, HubSpot, ICC-Crawler, ichiro, IDG/IT, IIS Site Analysis, Inktomi Slurp, inoreader, IONOS Crawler, IP-Guide Crawler, IPS Agent, JungleKeyThumbnail, K6, Kaspersky, KomodiaBot, Kouio, l9tcpid, Larbin web crawler, LCC, Let's Encrypt Validation, Lighthouse, Linespider, Linkdex Bot, LinkedIn Bot, LinkpadBot, LinkPreview, LTX71, LumtelBot, Lycos, Magpie-Crawler, MagpieRSS, Mail.Ru Bot, masscan, Mastodon Bot, Meanpath Bot, Mediatoolkit Bot, MegaIndex, MetaInspector, MetaJobBot, MicroAdBot, Mixrank Bot, MJ12 Bot, Mnogosearch, MojeekBot, Monitor.Us, MTRobot, Munin, MuscatFerret, Nagios check_http, NalezenCzBot, nbertaupete95, Neevabot, Netcraft Survey Bot, netEstate, NetLyzer FastProbe, NetResearchServer, NetSystemsResearch, Netvibes, NewsBlur, NewsGator, Nimbostratus Bot, NLCrawler, Nmap, Notify Ninja, Nutch-based Bot, Nuzzel, oBot, Octopus, Odnoklassniki Bot, Omgili bot, Openindex Spider, OpenLinkProfiler, OpenWebSpider, Orange Bot, Outbrain, PagePeeker, PageThing, PaperLiBot, parse.ly, Petal Bot, Phantomas, PHP Server Monitor, Picsearch bot, PingAdmin.Ru, Pingdom Bot, Pinterest, PiplBot, Plukkie, PocketParser, Pompos, PritTorrent, Project Resonance, PRTG Network Monitor, QuerySeekerSpider, Quora Bot, Quora Link Preview, Qwantify, Rainmeter, RamblerMail Image Proxy, Reddit Bot, Riddler, Robozilla, RocketMonitorBot, Rogerbot, ROI Hunter, RSSRadio Bot, Ryowl, SabsimBot, SafeDNSBot, Scooter, ScoutJet, Scrapy, Screaming Frog SEO Spider, ScreenerBot, security.txt scanserver, Seekport, Semantic Scholar Bot, Semrush Bot, Sensika Bot, Sentry Bot, Seobility, SEOENGBot, SEOkicks, SEOkicks-Robot, seolyt, Seoscanners.net, Serendeputy Bot, serpstatbot, Server Density, Seznam Bot, Seznam Email Proxy, Seznam Zbozi.cz, ShopAlike, Shopify Partner, ShopWiki, SilverReader, SimplePie, SISTRIX Crawler, SISTRIX Optimizer, Site24x7 Website Monitoring, Siteimprove, SitemapParser-VIPnytt, SiteSucker, Sixy.ch, Skype URI Preview, Slackbot, SMTBot, Snapchat Proxy, Sogou Spider, Soso Spider, Sparkler, Speedy, Spinn3r, Spotify, Sprinklr, Sputnik Bot, Sputnik Favicon Bot, Sputnik Image Bot, sqlmap, SSL Labs, Startpagina Linkchecker, StatusCake, Superfeedr Bot, SurdotlyBot, Survey Bot, Tarmot Gezgin, TelegramBot, TestCrawler, The Knowledge AI, theoldreader, ThinkChaos, TigerBot, TinEye Crawler, Tiny Tiny RSS, TLSProbe, TraceMyFile, Trendiction Bot, Turnitin, TurnitinBot, TweetedTimes Bot, Tweetmeme Bot, Twingly Recon, Twitterbot, UkrNet Mail Proxy, UniversalFeedParser, Uptimebot, Uptime Robot, URLAppendBot, Vagabondo, Velen Public Web Crawler, Vercel Bot, VeryHip, Visual Site Mapper Crawler, VK Share Button, W3C CSS Validator, W3C I18N Checker, W3C Link Checker, W3C Markup Validation Service, W3C MobileOK Checker, W3C Unified Validator, Wappalyzer, WebbCrawler, WebDataStats, Weborama, WebPageTest, WebSitePulse, WebThumbnail, WellKnownBot, WeSEE:Search, WeViKaBot, WhatCMS, WikiDo, Willow Internet Crawler, WooRank, WooRank, WordPress, Wotbox, XenForo, YaCy, Yahoo! Cache System, Yahoo! Japan BRW, Yahoo! Link Preview, Yahoo! Mail Proxy, Yahoo! Slurp, Yahoo Gemini, YaK, Yandex Bot, Yeti/Naverbot, Yottaa Site Monitor, Youdao Bot, Yourls, Yunyun Bot, Zao, Ze List, zgrab, Zookabot, ZoominfoBot, ZumBot
