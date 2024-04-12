DeviceDetector
==============

[![Latest Stable Version](https://poser.pugx.org/matomo/device-detector/v/stable)](https://packagist.org/packages/matomo/device-detector)
[![Total Downloads](https://poser.pugx.org/matomo/device-detector/downloads)](https://packagist.org/packages/matomo/device-detector)
[![License](https://poser.pugx.org/matomo/device-detector/license)](https://packagist.org/packages/matomo/device-detector)

## Code Status

![PHPUnit](https://github.com/matomo-org/device-detector/workflows/PHPUnit/badge.svg?branch=master)
![PHPStan](https://github.com/matomo-org/device-detector/workflows/PHPStan%20check/badge.svg?branch=master)
![PHPCS](https://github.com/matomo-org/device-detector/workflows/PHPCS%20check/badge.svg?branch=master)
![YAML Lint](https://github.com/matomo-org/device-detector/workflows/YAML%20Lint/badge.svg?branch=master)
[![Validate regular Expressions](https://github.com/matomo-org/device-detector/actions/workflows/regular_expressions.yml/badge.svg)](https://github.com/matomo-org/device-detector/actions/workflows/regular_expressions.yml)

[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/matomo-org/device-detector.svg)](http://isitmaintained.com/project/matomo-org/device-detector "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/matomo-org/device-detector.svg)](http://isitmaintained.com/project/matomo-org/device-detector "Percentage of issues still open")

## Description

The Universal Device Detection library that parses User Agents and Browser Client Hints to detect devices (desktop, tablet, mobile, tv, cars, console, etc.), clients (browsers, feed readers, media players, PIMs, ...), operating systems, brands and models.

## Usage

Using DeviceDetector with composer is quite easy. Just add `matomo/device-detector` to your projects requirements.

```
composer require matomo/device-detector
```

And use some code like this one:


```php
require_once 'vendor/autoload.php';

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

// OPTIONAL: Set version truncation to none, so full versions will be returned
// By default only minor versions will be returned (e.g. X.Y)
// for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

$userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse
$clientHints = ClientHints::factory($_SERVER); // client hints are optional

$dd = new DeviceDetector($userAgent, $clientHints);

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

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

// OPTIONAL: Set version truncation to none, so full versions will be returned
// By default only minor versions will be returned (e.g. X.Y)
// for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

$userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse
$clientHints = ClientHints::factory($_SERVER); // client hints are optional

$dd = new DeviceDetector($userAgent, $clientHints);

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

// Example with Laravel
$dd->setCache(
    new \DeviceDetector\Cache\LaravelCache()
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
- **Java** https://github.com/deevvicom/device-detector
- **Java** https://github.com/PaniniGelato/java-device-detector
- **Rust** https://github.com/simplecastapps/rust-device-detector
- **Rust** https://github.com/stry-rs/device-detector
- **Go** https://github.com/gamebtc/devicedetector
- **Go** https://github.com/umutbasal/device-detector-go
- **Go** https://github.com/robicode/device-detector

## Icon packs

If you are looking for icons to use alongside Device Detector, these repositories can be of use:
- Official [Matomo](https://github.com/matomo-org/matomo-icons/) pack
- Unofficial [Simbiat](https://github.com/Simbiat/DeviceDetectorIcons) pack

## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: 2024/04/12*

### List of detected operating systems:

AIX, Android, Android TV, Alpine Linux, Amazon Linux, AmigaOS, Armadillo OS, tvOS, Arch Linux, AOSC OS, ASPLinux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Bliss OS, Brew, BrightSignOS, Caixa Mágica, CentOS, CentOS Stream, Clear Linux OS, ClearOS Mobile, Chrome OS, Chromium OS, China OS, CyanogenMod, Debian, Deepin, DragonFly, DVKBuntu, ElectroBSD, EulerOS, Fedora, Fenix, Firefox OS, Fire OS, Foresight Linux, Freebox, FreeBSD, FRITZ!OS, FydeOS, Fuchsia, Gentoo, GridOS, Google TV, HP-UX, Haiku OS, iPadOS, HarmonyOS, HasCodingOS, HELIX OS, IRIX, Inferno, Java ME, KaiOS, Kali, Kanotix, Knoppix, KreaTV, Kubuntu, GNU/Linux, LindowsOS, Linspire, Lineage OS, Liri OS, Loongnix, Lubuntu, Lumin OS, LuneOS, VectorLinux, Mac, Maemo, Mageia, Mandriva, MeeGo, MocorDroid, moonOS, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, MRE, Nintendo, Nintendo Mobile, Nova, OS/2, OSF1, OpenBSD, OpenVZ, OpenWrt, Opera TV, Oracle Linux, Ordissimo, Pardus, PCLinuxOS, PICO OS, Plasma Mobile, PlayStation Portable, PlayStation, Proxmox VE, PureOS, Raspberry Pi OS, Raspbian, Red Hat, RedOS, Revenge OS, RISC OS, Rocky Linux, Roku OS, Rosa, RouterOS, Remix OS, Resurrection Remix OS, REX, RazoDroiD, Sabayon, SUSE, Sailfish OS, Scientific Linux, SeewoOS, SerenityOS, Sirin OS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, TencentOS, ThreadX, Tizen, TiVo OS, TmaxOS, Ubuntu, UOS, VIDAA, watchOS, Wear OS, WebTV, Whale OS, Windows, Windows CE, Windows IoT, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOS, Zenwalk, ZorinOS, iOS, palmOS, Webian, webOS

### List of detected browsers:

Via, Pure Mini Browser, Pure Lite Browser, Raise Fast Browser, Rabbit Private Browser, Fast Browser UC Lite, Fast Explorer, Lightning Browser, Cake Browser, IE Browser Fast, Vegas Browser, OH Browser, OH Private Browser, XBrowser Mini, Sharkee Browser, Lark Browser, Pluma, Anka Browser, Azka Browser, Dragon Browser, Easy Browser, Dark Web Browser, Dark Browser, 18+ Privacy Browser, 115 Browser, 1DM Browser, 1DM+ Browser, 2345 Browser, 360 Secure Browser, 360 Phone Browser, 7654 Browser, Avant Browser, ABrowse, Acoo Browser, AdBlock Browser, Adult Browser, Airfind Secure Browser, ANT Fresco, ANTGalio, Aloha Browser, Aloha Browser Lite, ALVA, Amaya, Amaze Browser, Amerigo, Amigo, Android Browser, AOL Explorer, AOL Desktop, AOL Shield, AOL Shield Pro, Aplix, AppBrowzer, APUS Browser, Arora, Arctic Fox, Amiga Voyager, Amiga Aweb, APN Browser, Arachne, Arc, Arvin, Ask.com, Asus Browser, Atom, Atomic Web Browser, Atlas, Avast Secure Browser, AVG Secure Browser, Avira Secure Browser, AwoX, Basic Web Browser, Beaker Browser, Beamrise, BF Browser, BlackBerry Browser, Bluefy, BrowseHere, Browser Hup Pro, Baidu Browser, Baidu Spark, Bang, Bangla Browser, Basilisk, Belva Browser, Beyond Private Browser, Beonex, Berry Browser, Bitchute Browser, BizBrowser, BlackHawk, Bloket, Bunjalloo, B-Line, Black Lion Browser, Blue Browser, Bonsai, Borealis Navigator, Brave, BriskBard, BroKeep Browser, Browspeed Browser, BrowseX, Browzar, Browlser, BrowsBit, Biyubi, Byffox, BXE Browser, Camino, Catalyst, Catsxp, Cave Browser, CCleaner, CG Browser, ChanjetCloud, Chedot, Cherry Browser, Centaury, Cliqz, Coc Coc, CoolBrowser, Colibri, Columbus Browser, Comodo Dragon, Coast, Charon, CM Browser, CM Mini, Chrome Frame, Headless Chrome, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, Chowbo, Classilla, CoolNovo, Colom Browser, CometBird, Comfort Browser, COS Browser, Cornowser, Chim Lac, ChromePlus, Chromium, Chromium GOST, Cyberfox, Cheshire, Crow Browser, Crusta, Craving Explorer, Crazy Browser, Cunaguaro, Chrome Webview, CyBrowser, dbrowser, Peeps dBrowser, Dark Web, Dark Web Private, Debuggable Browser, Decentr, Deepnet Explorer, deg-degan, Deledao, Delta Browser, Desi Browser, DeskBrowse, Dezor, Diigo Browser, DoCoMo, Dolphin, Dolphin Zero, Dorado, Dot Browser, Dooble, Dillo, DUC Browser, DuckDuckGo Privacy Browser, East Browser, Ecosia, Edge WebView, Every Browser, Epic, Elinks, EinkBro, Element Browser, Elements Browser, Eolie, Explore Browser, eZ Browser, EudoraWeb, EUI Browser, GNOME Web, G Browser, Espial TV Browser, fGet, Falkon, Faux Browser, Fire Browser, Fiery Browser, Firefox Mobile iOS, Firebird, Fluid, Fennec, Firefox, Firefox Focus, Firefox Reality, Firefox Rocket, Firefox Klar, Float Browser, Flock, Floorp, Flow, Flow Browser, Firefox Mobile, Fireweb, Fireweb Navigator, Flash Browser, Flast, Flyperlink, FreeU, Freedom Browser, Frost, Frost+, Fulldive, Galeon, Gener8, Ghostery Privacy Browser, GinxDroid Browser, Glass Browser, Godzilla Browser, Good Browser, Google Earth, Google Earth Pro, GOG Galaxy, GoBrowser, GoKu, GO Browser, GreenBrowser, Habit Browser, Harman Browser, HasBrowser, Hawk Turbo Browser, Hawk Quick Browser, Helio, Hexa Web Browser, Hi Browser, hola! Browser, Holla Web Browser, HotBrowser, HotJava, HTC Browser, Huawei Browser Mobile, Huawei Browser, HUB Browser, iBrowser, iBrowser Mini, IBrowse, iDesktop PC Browser, iCab, iCab Mobile, Iridium, Iron Mobile, IceCat, IceDragon, Isivioo, IVVI Browser, Iceweasel, Impervious Browser, Incognito Browser, Inspect Browser, Insta Browser, Internet Explorer, Internet Browser Secure, Intune Managed Browser, Indian UC Mini Browser, InBrowser, Involta Go, IE Mobile, Iron, Japan Browser, Jasmine, JavaFX, Jelly, Jig Browser, Jig Browser Plus, JioSphere, JUZI Browser, K.Browser, Keepsafe Browser, Kids Safe Browser, Kindle Browser, K-meleon, K-Ninja, Konqueror, Kapiko, Keyboard Browser, Kinza, Kitt, Kiwi, Kode Browser, KUTO Mini Browser, Kylo, Kazehakase, Cheetah Browser, Ladybird, Lagatos Browser, Legan Browser, Lexi Browser, Lenovo Browser, LieBaoFast, LG Browser, Light, Lightning Browser Plus, Lilo, Links, Liri Browser, LogicUI TV Browser, Lolifox, Lotus, Lovense Browser, LT Browser, LuaKit, LUJO TV Browser, Lulumi, Lunascape, Lunascape Lite, Lynx, Lynket Browser, Mandarin, MarsLab Web Browser, MaxBrowser, mCent, MicroB, NCSA Mosaic, Meizu Browser, Mercury, Me Browser, Mobile Safari, Midori, Midori Lite, MixerBox AI, Mobicip, MIUI Browser, Mobile Silk, Mogok Browser, Minimo, Mint Browser, Maxthon, MaxTube Browser, Maelstrom, Mmx Browser, MxNitro, Mypal, Monument Browser, MAUI WAP Browser, Navigateur Web, Naked Browser, Naked Browser Pro, NFS Browser, Ninetails, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, Norton Private Browser, Nox Browser, NOMone VR Browser, NetSurf, NetFront, NetFront Life, NetPositive, Netscape, NextWord Browser, NTENT Browser, Nuanti Meta, Nuviu, Ocean Browser, Oculus Browser, Odd Browser, Opera Mini iOS, Obigo, Odin, Odin Browser, OceanHero, Odyssey Web Browser, Off By One, Office Browser, OhHai Browser, OnBrowser Lite, ONE Browser, Onion Browser, Opera Crypto, Opera GX, Opera Neon, Opera Devices, Opera Mini, Opera Mobile, Opera, Opera Next, Opera Touch, Orbitum, Orca, Ordissimo, Oregano, Origin In-Game Overlay, Origyn Web Browser, OrNET Browser, Openwave Mobile Browser, OpenFin, Open Browser, Open Browser 4U, Open Browser fast 5G, Open TV Browser, OmniWeb, Otter Browser, Owl Browser, Palm Blazer, Pale Moon, Polypane, Oppo Browser, Opus Browser, Palm Pre, Puffin, Puffin Web Browser, Palm WebPro, Palmscape, Pawxy, Perfect Browser, Phantom.me, Phantom Browser, Phoenix, Phoenix Browser, Photon, PirateBrowser, PICO Browser, PlayFree Browser, PocketBook Browser, Polaris, Polarity, PolyBrowser, PrivacyWall, Privacy Explorer Fast Safe, Private Internet Browser, Proxy Browser, Proxyium, Proxynet, Pi Browser, PronHub Browser, PSI Secure Browser, Reqwireless WebViewer, Microsoft Edge, Qazweb, Qiyu, QJY TV Browser, Qmamu, Quick Search TV, QQ Browser Lite, QQ Browser Mini, QQ Browser, Quick Browser, Qutebrowser, Quark, QupZilla, Qwant Mobile, QtWebEngine, Rakuten Browser, Rakuten Web Search, Raspbian Chromium, Realme Browser, Rekonq, RockMelt, Roku Browser, Samsung Browser, Samsung Browser Lite, Sailfish Browser, SberBrowser, Seewo Browser, SEMC-Browser, Sogou Explorer, Sogou Mobile Browser, SOTI Surf, Soul Browser, Soundy Browser, Safari, Safari Technology Preview, Safe Exam Browser, SalamWeb, Savannah Browser, SavySoda, Secure Browser, SFive, Shiira, Sidekick, SimpleBrowser, SilverMob US, Sizzy, Skye, Skyfire, SkyLeap, Seraphic Sraf, SiteKiosk, Sleipnir, SlimBoat, Slimjet, SP Browser, Sony Small Browser, Secure Private Browser, SecureX, Stampy Browser, 7Star, Smart Browser, Smart Search & Web Browser, Smart Lenovo Browser, Smooz, Snowshoe, Spectre Browser, Splash, Sputnik Browser, Sunrise, Sunflower Browser, SuperBird, Super Fast Browser, SuperFast Browser, Sushi Browser, surf, Surf Browser, Surfy Browser, Stargon, START Internet Browser, Stealth Browser, Steam In-Game Overlay, Streamy, Swiftfox, Seznam Browser, Sweet Browser, SX Browser, T+Browser, T-Browser, t-online.de Browser, TalkTo, Tao Browser, tararia, Thor, Tor Browser, TenFourFox, Tenta Browser, Tesla Browser, Tizen Browser, Tint Browser, TrueLocation Browser, TUC Mini Browser, Tungsten, ToGate, TweakStyle, TV Bro, U Browser, UBrowser, UC Browser, UC Browser HD, UC Browser Mini, UC Browser Turbo, Ui Browser Mini, UR Browser, Uzbl, Ume Browser, vBrowser, Vast Browser, VD Browser, Venus Browser, Vewd Browser, VibeMate, Nova Video Downloader Pro, Viasat Browser, Vivaldi, vivo Browser, Vivid Browser Mini, Vision Mobile Browser, Vertex Surf, VMware AirWatch, Vonkeror, Vuhuv, Wear Internet Browser, Web Explorer, Web Browser & Explorer, Webian Shell, WebDiscover, WebPositive, Weltweitimnetz Browser, Wexond, Waterfox, Wave Browser, Wavebox, Whale Browser, wOSBrowser, w3m, WeTab Browser, World Browser, Wolvic, Wyzo, YAGI, Yahoo! Japan Browser, Yandex Browser, Yandex Browser Lite, Yaani Browser, Yo Browser, Yolo Browser, YouCare, YouBrowser, Yuzu Browser, xBrowser, X Browser Lite, X-VPN, xBrowser Pro Super Fast, XNX Browser, XtremeCast, xStand, Xiino, Xooloo Internet, Xvast, Zetakey, Zvu, Zirco Browser, Zordo Browser

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Elektra, Presto, Clecko, Gecko, KHTML, NetFront, Edge, NetSurf, Servo, Goanna, EkiohFlow, Arachne, LibWeb

### List of detected libraries:

aiohttp, Akka HTTP, Android License Verification Library, AnyEvent HTTP, Apache HTTP Client, Aria2, Artifactory, Axios, Azure Data Factory, Babashka HTTP Client, Buildah, BuildKit, C++ REST SDK, CakePHP, CarrierWave, Containerd, containers, cPanel HTTP Client, cpp-httplib, cri-o, curl, Dart, Deno, docker, Down, Electron Fetch, Emacs, Embarcadero URI Client, ESP32 HTTP Client, Faraday, fasthttp, ffmpeg, FFUF, FileDownloader, Free Download Manager, GeoIP Update, git-annex, go-container registry, Go-http-client, Google HTTP Java Client, got, GRequests, gRPC-Java, GStreamer, Guzzle (PHP HTTP Client), gvfs, hackney, Harbor registry client, Helm, HTML Parser, http.rb, HTTP:Tiny, HTTPie, httplib2, httprs, HTTPX, HTTP_Request2, Insomnia REST Client, iOS Application, Jakarta Commons HttpClient, Java, Java HTTP Client, jsdom, KaiOS Downloader, Kiwi TCMS, Kiwi TCMS API, libdnf, libpod, libsoup, Libsyn, LUA OpenResty NGINX, Mandrill PHP, Mechanize, Mikrotik Fetch, Msray-Plus, Node Fetch, OKDownload Library, OkHttp, Open Build Service, Pa11y, Perl, Perl REST::Client, PHP cURL Class, Podgrab, Postman Desktop, PRDownloader, Python Requests, Python urllib, QbHttp, quic-go, r-curl, Radio Downloader, ReactorNetty, req, request, Requests, reqwest, REST Client for Ruby, RestSharp, Resty, resty-requests, ruby, Safari View Service, ScalaJ HTTP, Skopeo, SlimerJS, Slim Framework, Stealer, superagent, Symfony, Typhoeus, uclient-fetch, Ultimate Sitemap Parser, undici, Unirest for Java, urlgrabber (yum), uTorrent, Wget, Windows HTTP, WinHttp WinHttpRequest, WWW-Mechanize

### List of detected media players:

Alexa, Amarok, Audacious, Banshee, Boxee, Clementine, Deezer, DIGA, Downcast, FlyCast, Foobar2000, foobar2000, Google Podcasts, HTC Streaming Player, Hubhopper, iTunes, JHelioviewer, JRiver Media Center, Juice, Just Audio, Kasts, Kodi, MediaMonkey, Miro, MixerBox, MPlayer, mpv, MusicBee, Music Player Daemon, NexPlayer, Nightingale, QuickTime, Songbird, SONOS, Sony Media Go, Stagefright, StudioDisplay, SubStream, VLC, Winamp, Windows Media Player, XBMC, Xtream Player, YouView

### List of detected mobile apps:

'sodes, +Simple, 1Password, 2tch, ActionExtension, Adobe Creative Cloud, Adobe IPM, Adobe NGL, Adobe Synchronizer, Adori, Agora, Aha Radio 2, AIDA64, Airr, Airsonic, AliExpress, Alipay, AllHitMusicRadio, All You Can Books, Amazon Fire, Amazon Music, Amazon Shopping, Ameba, Anchor, AnchorFM, AndroidDownloadManager, Anghami, AntennaPod, AntiBrowserSpy, Anybox, Anytime Podcast Player, APK Downloader, Apollo, Apple iMessage, Apple News, Apple Podcasts, Apple Reminders, Apple TV, Arvocast, ASUS Updater, Audacy, Audials, Audible, Audio, Audiobooks, Audio Now, Autoplius.lt, Avid Link, Awasu, Background Intelligent Transfer Service, Baidu Box App, Baidu Input, Ballz, Bank Millenium, Battle.net, BB2C, BBC News, Bear, Be Focused, BetBull, BeyondPod, Bible, Bible KJV, Binance, Bitcoin Core, Bitsboard, Bitwarden, Blackboard, Blitz, Blue Proxy, BlueStacks, Bolt, BonPrix, Bookmobile, Bookshelf, Boom, Boomplay, Bose Music, Bose SoundTouch, bPod, Breez, Broadcast, Broadway Podcast Network, Browser-Anonymizer, Browser app, BrowserPlus, Bullhorn, BuzzVideo, CamScanner, Capital, capsule.fm, Castamatic, Castaway, CastBox, Castro, Castro 2, CCleaner, CGN, ChMate, Chrome Update, Ciisaa, Citrix Workspace, Classic FM, Client, Clipbox+, Clovia, COAF SMART Citizen, Coinbase, Cooler, Copied, Cortana, Cosmicast, Covenant Eyes, CPod, CPU-Z, CrosswalkApp, Crypto.com DeFi Wallet, CSDN, Damus, Daum, De Standaard, De Telegraaf, DevCasts, DeviantArt, DingTalk, DIRECTV, Discord, DManager, Dogecoin Core, DoggCatcher, Don't Waste My Time!, douban App, DoubleTwist CloudPlayer, Doughnut, Downcast, Downie, Downloader, Dr. Watson, DStream Air, Edge Update, EMAudioPlayer, Emby Theater, Epic Games Launcher, ESET Remote Administrator, eToro, Evolve Podcast, Expedia, Expo, F-Secure SAFE, Facebook, Facebook Audience Network, Facebook Groups, Facebook Lite, Facebook Messenger, Facebook Messenger Lite, faidr, Fathom, FeedR, FeedStation, Files, Flipboard App, Flipp, FM WhatsApp, Focus Keeper, Focus Matrix, Fountain, Freespoke, Gaana, Garmin fenix 5X, Garmin Forerunner, GBWhatsApp, GetPodcast, Git, GitHub Desktop, GlobalProtect, Gold, GoldenPod, GoLoud, GoNative, Goodpods, GoodReader, Google Assistant, Google Drive, Google Fiber TV, Google Go, Google Lens, Google Nest Hub, Google Photos, Google Play Newsstand, Google Plus, Google Podcasts, Google Search App, Google Tag Manager, GroupMe, Guacamole, Hago, Hammel, HandBrake, HardCast, Hark Audio, Heart, HermesPod, HeyTapBrowser, HiCast, HideX, Hik-Connect, Himalaya, HiSearch, HisThumbnail, HistoryHound, Hotels.com, HP Smart, HTTP request maker, Huawei Mobile Services, HyperCatcher, iCatcher, iHeartRadio, IMO HD Video Calls & Chat, IMO International Calls & Chat, Instabridge, Instacast, Instagram App, Instapaper, IPTV, IPTV Pro, iVoox, Jam, JaneStyle, JioSaavn, Jitsi Meet, JJ2GO, Jungle Disk, Just Listen, Kajabi, KakaoTalk, Keeper Password Manager, Kids Listen, KidsPod, Kik, KKBOX, Klarna, KPN Veilig, L.A. Times, Landis+Gyr AIM Browser, Lark, Laughable, Lazada, LBC, LG Player, Line, LinkedIn, Listen, LiSTNR, Liulo, Logi Options+, LoseIt!, Luminary, Macrium Reflect, MBolsa, Megaphone, MEmpresas, Menucast, Mercantile Bank of Michigan, MessengerX, Meta Business Suite, Metacast, MetaMask, MetaTrader, Microsoft Bing, Microsoft Copilot, Microsoft Lync, Microsoft Office, Microsoft Office $1, Microsoft Office Mobile, Microsoft OneDrive, Microsoft Start, Microsoft Store, Mimir, mobile.de, MobileSMS, MoonFM, mowPod, Moya, MX Player, My Bentley, MyTuner, My Watch Party, My World, Naver, NAVER Dictionary, NET.mede, Netflix, NewsArticle App, Newsly, Nextcloud, NPR, NRC, NRC Audio, NTV Mobil, NuMuKi Browser, OBS Studio, Odnoklassniki, OfferUp, Opal Travel, Opera News, Opera Updater, Orange Radio, Outcast, Overcast, Overhaul FM, Paint by Number, Palco MP3, Pandora, Papers, PeaCast, Petal Search, Pic Collage, Pinterest, Player FM, PLAYit, Plex Media Server, Pocket Casts, Podbay, Podbean, Podcast & Radio Addict, Podcast App, Podcast Guru, Podcastly, Podcast Player, Podcast Republic, Podcat, Podcatcher Deluxe, Podchaser, Podclipper, PodCruncher, Podeo, Podfriend, Podhero, Podimo, PodKast, Podkicker$1, Podkicker Pro, PodLP, PodMe, PodMN, PodNL, Podopolo, Podplay, Pods, PodTrapper, podU, Podurama, Podverse, Podvine, Podyssey, PowerShell, Procast, PugPig Bolt, Q-municate, qBittorrent, QQMusic, QuickCast, Quicksilver, Quora, R, radio.$1, RadioApp, Radio Italiane, Radioline, Radio Next, RadioPublic, Rave Social, Razer Synapse, RDDocuments, Reddit, Redditor, rekordbox, Repod, Rhythmbox, RNPS Action Cards, Roblox, RoboForm, Rocket Chat, RSSDemon, RSSRadio, Rutube, SachNoi, Safari Search Helper, SafeIP, Samsung Magician, Samsung Podcasts, SearchCraft, ServeStream, Shadow, Shadowrocket, Shopee, ShowMe, Signal, Sina Weibo, Siri, SiriusXM, Skyeng, Skyeng Teachers, Skype, Skype for Business, Slack, Snapchat, Snipd, SogouSearch App, SohuNews, Soldier, Sonnet, Sony PlayStation 5, SoundOn, SoundWaves, SPORT1, Spotify, Spreaker, Startsiden, Stitcher, StoryShots, Streamlabs OBS, Stream Master, Strimio, Surfshark, Swinsian, Swoot, Taobao, Teams, The New York Times, The Wall Street Journal, Theyub, Thunder, tieba, TikTok, TiviMate, TopBuzz, TradingView, Treble.fm, TuneIn Radio, TuneIn Radio Pro, Turtlecast, Tuya Smart Life, TVirl, twinkle, Twitch Studio, Twitter, Twitterrific, U-Cursos, Ubook Player, UCast, Uconnect LIVE, Uforia, Unibox, UnityPlayer, Viber, Victor Reader Stream 3, Victor Reader Stream New Generation, Victor Reader Stream Trek, Virgin Radio, Visual Studio Code, Vodacast, Vuhuv, Vuze, waipu.tv, Wattpad, Wayback Machine, WebDAV, WeChat, WeChat Share Extension, Whatplay, WhatsApp, WhatsApp+2, WhatsApp Business, Whisper, WH Questions, Windows Antivirus, Windows CryptoAPI, Windows Delivery Optimization, Windows Push Notification Services, Windows Update Agent, Wireshark, Wirtschafts Woche, Word Cookies!, WPS Office, Wynk Music, Xiao Yu Zhou, XSplit Broadcaster, Y8 Browser, Yahoo! Japan, Yahoo OneSearch, YakYak, Yandex, Yandex Music, Yapa, Yelp Mobile, YouTube, Youtube Music, Yo WhatsApp, Zalo, Zen, ZEPETO, Zoho Chat, Zune and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Apple Mail, Barca, Basecamp, BathyScaphe, BlueMail, DAVdroid, eM Client, Evernote, Foxmail, Franz, JaneView, Live5ch, Lotus Notes, MailBar, Mailbird, Mail Master, Mailspring, Microsoft Outlook, NAVER Mail, Notion, Outlook Express, Postbox, Raindrop.io, Rambox Pro, SeaMonkey, The Bat!, Thunderbird, Windows Mail, Yahoo! Mail, Yahoo Mail

### List of detected feed readers:

Akregator, Apple PubSub, BashPodder, Breaker, castero, castget, FeedDemon, Feeddler RSS Reader, gPodder, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, Newsboat, Playapod, PodPuppy, PritTorrent, Pulp, QuiteRSS, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

2E, 3GNET, 3GO, 3Q, 4Good, 4ife, 5IVE, 7 Mobile, 10moons, 360, 8848, A&K, A1, A95X, Accent, Accesstyle, Ace, Acer, Acteck, actiMirror, Adronix, Advan, Advance, Advantage Air, AEEZO, AFFIX, AfriOne, AGM, AG Mobile, AIDATA, Ainol, Airis, Airness, AIRON, Airpha, Airtel, Airties, AirTouch, AIS, Aiuto, Aiwa, Ajib, Akai, AKIRA, Alba, Alcatel, Alcor, ALDI NORD, ALDI SÜD, Alfawise, Aligator, AllCall, AllDocube, ALLINmobile, Allview, Allwinner, Alps, Altech UEC, Altice, Altimo, altron, Altus, AMA, Amazon, Amazon Basics, AMCV, AMGOO, Amigoo, Amino, Amoi, Andowl, Angelcare, Anker, Anry, ANS, ANXONIT, AOC, Aocos, AOpen, Aoro, Aoson, AOYODKG, Apple, Aquarius, Archos, Arian Space, Ark, ArmPhone, Arnova, ARRIS, Artel, Artizlee, ArtLine, Arçelik, Asano, Asanzo, Ask, Aspera, ASSE, Assistant, Astro, Asus, AT&T, Athesi, Atlantic Electrics, Atmaca Elektronik, ATMAN, ATOL, Atom, Attila, Atvio, Audiovox, AUPO, AURIS, Autan, AUX, Avaya, Avenzo, AVH, Avvio, Awow, AWOX, AXEN, Axioo, AXXA, Axxion, AYA, AYYA, Azumi Mobile, b2m, Backcell, BAFF, BangOlufsen, Barnes & Noble, BARTEC, BASE, BAUHN, BBK, BB Mobile, BDF, BDQ, BDsharing, Beafon, Becker, Beeline, Beelink, Beetel, Beista, Beko, Bellphone, Benco, Benesse, BenQ, BenQ-Siemens, BenWee, Benzo, Beyond, Bezkam, BGH, Bigben, BIHEE, BilimLand, Billion, Billow, BioRugged, Bird, Bitel, Bitmore, Bittium, Bkav, Black Bear, Black Box, Black Fox, Blackpcs, Blackview, Blaupunkt, Bleck, BLISS, Blloc, Blow, Blu, Bluboo, Bluebird, Bluedot, Bluegood, BlueSky, Bluewave, BluSlate, BMAX, Bmobile, BMW, BMXC, Bobarry, bogo, Bolva, Bookeen, Boost, Botech, Boway, bq, BrandCode, Brandt, BRAVE, Bravis, BrightSign, Brigmton, Brondi, BROR, BS Mobile, Bubblegum, Bundy, Bush, BuzzTV, C5 Mobile, CAGI, Camfone, Canal+, Canal Digital, Canguro, Capitel, Captiva, Carbon Mobile, Carrefour, Casio, Casper, Cat, Cavion, CCIT, Cecotec, Ceibal, Celcus, Celkon, Cell-C, Cellacom, CellAllure, Cellution, CENTEK, Centric, CG Mobile, CGV, Chainway, Changhong, Cherry Mobile, Chico Mobile, ChiliGreen, China Mobile, China Telecom, Chuwi, CipherLab, Citycall, CKK Mobile, Claresta, Clarmin, CLAYTON, ClearPHONE, Clementoni, Cloud, Cloudfone, Cloudpad, Clout, CnM, Cobalt, Coby Kyros, Colors, Comio, Compal, Compaq, COMPUMAX, ComTrade Tesla, Conceptum, Concord, ConCorde, Condor, Connectce, Connex, Conquest, Continental Edison, Contixo, COOD-E, Coolpad, Coopers, CORN, Cosmote, Covia, Cowon, COYOTE, CreNova, Crescent, Cricket, Crius Mea, Crony, Crosscall, Crown, Ctroniq, Cube, CUBOT, CVTE, Cwowdefu, Cyrus, D-Link, D-Tech, Daewoo, Danew, DangcapHD, Dany, DASS, Datalogic, Datamini, Datang, Datawind, Datsun, Dazen, DbPhone, Dbtel, Dcode, DEALDIG, Dell, Denali, Denver, Desay, DeWalt, DEXP, DEYI, DF, DGTEC, DIALN, Dialog, Dicam, Digi, Digicel, DIGICOM, Digidragon, DIGIFORS, Digihome, Digiland, Digit4G, Digma, DIJITSU, DIKOM, DIMO, Dinalink, Dinax, DING DING, DIORA, DISH, Disney, Ditecma, Diva, DiverMax, Divisat, DIXON, DL, DMM, DNS, DoCoMo, Doffler, Dolamee, Dom.ru, Doogee, Doopro, Doov, Dopod, Doppio, DORLAND, Doro, DPA, DRAGON, Dragon Touch, Dreamgate, DreamStar, DreamTab, Droidlogic, Droxio, DSDevices, DSIC, Dtac, Dune HD, DUNNS Mobile, Durabook, Duubee, Dyon, E-Boda, E-Ceros, E-tel, Eagle, EAS Electric, Easypix, EBEN, EBEST, Echo Mobiles, ecom, ECON, ECOO, ECS, Edenwood, EE, EFT, EGL, Einstein, EKINOX, EKO, Eks Mobility, EKT, ELARI, Elecson, Electroneum, ELECTRONIA, Elekta, Elektroland, Element, Elenberg, Elephone, Elevate, Elong Mobile, Eltex, Ematic, Emporia, ENACOM, Energizer, Energy Sistem, Engel, ENIE, Enot, eNOVA, Entity, Envizen, Ephone, Epic, Epik One, Epson, Equator, Ergo, Ericsson, Ericy, Erisson, Essential, Essentielb, eSTAR, ETOE, Eton, eTouch, Etuline, Eurocase, Eurostar, Evercoss, Everest, Everex, Evertek, Evolio, Evolveo, Evoo, EVPAD, EvroMedia, EWIS, EXCEED, Exmart, ExMobile, EXO, Explay, Express LUCK, Extrem, Eyemoo, EYU, Ezio, Ezze, F&U, F+, F2 Mobile, F150, Facebook, Facetel, Facime, Fairphone, Famoco, Famous, Fantec, FaRao Pro, Farassoo, FarEasTone, Fengxiang, Fenoti, FEONAL, Fero, FFF SmartLife, Figgers, FiGi, FiGO, FiiO, Filimo, FILIX, FinePower, Finlux, FireFly Mobile, FISE, FITCO, Fluo, Fly, FLYCAT, FMT, FNB, FNF, Fobem, Fondi, Fonos, FOODO, FORME, Formuler, Forstar, Fortis, FOSSiBOT, Fourel, Four Mobile, Foxconn, FoxxD, FPT, free, Freetel, FreeYond, Frunsi, Fuego, Fujitsu, Funai, Fusion5, Future Mobile Technology, Fxtec, G-TiDE, G-Touch, Galactic, Galaxy Innovations, Gamma, Garmin-Asus, Gateway, Gazer, Geanee, Geant, Gear Mobile, Gemini, General Mobile, Genesis, GEOFOX, Geotel, Geotex, GEOZON, Getnord, GFive, Gfone, Ghia, Ghong, Ghost, Gigabyte, Gigaset, Gini, Ginzzu, Gionee, GIRASOLE, Globex, Globmall, Glofiish, GLONYX, GLX, GOCLEVER, Gocomma, GoGEN, GoldMaster, Gol Mobile, Goly, Gome, GoMobile, GOODTEL, Google, Goophone, Gooweel, Gplus, Gradiente, Graetz, Grape, Great Asia, Gree, Green Lion, Green Orange, Greentel, Gresso, Gretel, GroBerwert, Grundig, Gtel, GTMEDIA, GTX, Guophone, H96, H133, Hafury, Haier, Haipai, Hamlet, Hammer, Handheld, HannSpree, Hanseatic, HAOQIN, HAOVM, Hardkernel, Harper, Hartens, Hasee, Hathway, HDC, HeadWolf, Helio, HERO, HexaByte, Hezire, Hi, Hi-Level, Hiberg, High Q, Highscreen, HiGrace, HiHi, HiKing, HiMax, Hi Nova, HIPER, Hipstreet, Hiremco, Hisense, Hitachi, Hitech, HKC, HKPro, HLLO, HOFER, Hoffmann, Homatics, Hometech, Homtom, Honeywell, Hoozo, Horizon, Horizont, Hosin, Hotel, Hot Pepper, HOTREALS, Hotwav, How, HP, HTC, Huadoo, Huagan, Huavi, Huawei, Hugerock, Humax, Hurricane, Huskee, Hykker, Hyrican, Hytera, Hyundai, Hyve, i-Cherry, I-INN, i-Joy, i-mate, i-mobile, I-Plus, iBall, iBerry, ibowin, iBrit, IconBIT, iData, iDino, iDroid, iGet, iHunt, Ikea, IKI Mobile, iKoMo, iKon, iKonia, IKU Mobile, iLA, iLepo, iLife, iMan, Imaq, iMars, iMI, IMO Mobile, Imose, Impression, iMuz, iNavi, INCAR, Inch, Inco, iNew, Infiniton, Infinix, InFocus, InfoKit, Infomir, InFone, Inhon, Inka, Inkti, InnJoo, Innos, Innostream, Inoi, iNo Mobile, iNOVA, INQ, Insignia, INSYS, Intek, Intel, Intex, Invens, Inverto, Invin, iOcean, IOTWE, iOutdoor, iPEGTOP, iPro, iQ&T, IQM, IRA, Irbis, iReplace, Iris, iRobot, iRola, iRulu, iSafe Mobile, iStar, iSWAG, IT, iTel, iTruck, IUNI, iVA, iView, iVooMi, ivvi, iWaylink, iXTech, iYou, iZotron, JAY-Tech, Jedi, Jeep, Jeka, Jesy, JFone, Jiake, Jiayu, Jinga, Jio, Jivi, JKL, Jolla, Joy, JoySurf, JPay, JREN, Jumper, Juniper Systems, Just5, JVC, JXD, K-Lite, K-Touch, Kaan, Kaiomy, Kalley, Kanji, Kapsys, Karbonn, Kata, KATV1, Kazam, Kazuna, KDDI, Kempler & Strauss, Kenbo, Kendo, Keneksi, Kenxinda, Khadas, Kiano, Kingbox, Kingstar, Kingsun, KINGZONE, Kinstone, Kiowa, Kivi, Klipad, KN Mobile, Kocaso, Kodak, Kogan, Komu, Konka, Konrow, Koobee, Koolnee, Kooper, KOPO, Koridy, Koslam, Kraft, KREZ, KRIP, KRONO, Krüger&Matz, KT-Tech, KUBO, KuGou, Kuliao, Kult, Kumai, Kurio, Kvant, Kydos, Kyocera, Kyowon, Kzen, KZG, L-Max, LAIQ, Land Rover, Landvo, Lanin, Lanix, Lark, Laurus, Lava, LCT, Leader Phone, Leagoo, Leben, LeBest, Lectrus, Ledstar, LeEco, Leelbox, Leff, Legend, Leke, Lemco, LEMFO, Lemhoov, Lenco, Lenovo, Leotec, Le Pan, Lephone, Lesia, Lexand, Lexibook, LG, Liberton, Lifemaxx, Lime, Lingwin, Linnex, Linsar, Linsay, Listo, LNMBBS, Loewe, Logic, Logic Instrument, Logicom, Logik, LOKMAT, Loview, Lovme, LPX-G, LT Mobile, Lumigon, Lumitel, Lumus, Luna, Luxor, LYF, M-Horse, M-Tech, M.T.T., M3 Mobile, M4tel, MAC AUDIO, Macoox, Mafe, Magicsee, Magnus, Majestic, Malata, Mango, Manhattan, Mann, Manta Multimedia, Mantra, Mara, Marshal, Mascom, Massgo, Masstel, Master-G, Mastertech, Matco Tools, Matrix, Maunfeld, Maxcom, Maxfone, Maximus, Maxtron, MAXVI, Maxwell, Maxwest, MAXX, Maze, Maze Speed, MBI, MBOX, MDC Store, MDTV, meanIT, Mecer, Mecool, Mediacom, MediaTek, Medion, MEEG, MegaFon, MEGA VISION, Meitu, Meizu, Melrose, MeMobile, Memup, MEO, Meta, Metz, MEU, MicroMax, Microsoft, Microtech, Minix, Mint, Mintt, Mio, Mione, Miray, Mitchell & Brown, Mito, Mitsubishi, Mitsui, MIVO, MIWANG, MIXC, MiXzo, MLAB, MLLED, MLS, MMI, Mobell, Mobicel, MobiIoT, Mobiistar, Mobile Kingdom, Mobiola, Mobistel, MobiWire, Mobo, Mobvoi, Modecom, Mofut, Mosimosi, Motiv, Motorola, Movic, MOVISUN, Movitel, Moxee, mPhone, Mpman, MSI, MStar, MTC, MTN, Multilaser, MultiPOS, MwalimuPlus, MYFON, MyGica, MygPad, Mymaga, MyMobile, MyPhone, Myria, Myros, Mystery, MyTab, MyWigo, N-one, Nabi, NABO, Nanho, Naomi Phone, NASCO, National, Navcity, Navitech, Navitel, Navon, NavRoad, NEC, Necnot, Nedaphone, Neffos, NEKO, Neo, neoCore, Neolix, Neomi, Neon IQ, NetBox, Netgear, Netmak, NeuImage, NeuTab, NEVIR, New Balance, New Bridge, Newgen, Newland, Newman, Newsday, NewsMy, Nexa, NEXBOX, Nexian, NEXON, NEXT, Next & NextStar, Nextbit, NextBook, NextTab, NGM, NG Optics, NGpon, Nikon, NINETEC, Nintendo, nJoy, NOA, Noain, Nobby, Noblex, NOBUX, noDROPOUT, NOGA, Nokia, Nomi, Nomu, Noontec, Nordmende, NORMANDE, NorthTech, Nos, Nothing Phone, Nous, Novacom, Novex, Novey, NOVO, NTT West, NuAns, Nubia, NUU Mobile, NuVision, Nuvo, Nvidia, NYX Mobile, O+, O2, Oale, Oangcc, OASYS, Obabox, Ober, Obi, OCEANIC, Odotpad, Odys, OINOM, Ok, Okapia, Oking, OKSI, OKWU, Olax, Olkya, Ollee, OLTO, Olympia, OMIX, Onda, OneClick, OneLern, OnePlus, Onida, Onix, Onkyo, ONN, ONVO, ONYX BOOX, Ookee, Ooredoo, OpelMobile, Openbox, Ophone, OPPO, Opsson, Optoma, Orange, Orava, Orbic, Orbita, Orbsmart, Ordissimo, Orion, OSCAL, OTTO, OUJIA, Ouki, Oukitel, OUYA, Overmax, Ovvi, Owwo, OYSIN, Oysters, Oyyu, OzoneHD, P-UP, Pacific Research Alliance, Packard Bell, PAGRAER, Paladin, Palm, Panacom, Panasonic, Panavox, Pano, Panodic, Panoramic, Pantech, PAPYRE, Parrot Mobile, Partner Mobile, PCBOX, PCD, PCD Argentina, PC Smart, PEAQ, Pelitt, Pendoo, Penta, Pentagram, Perfeo, Phicomm, Philco, Philips, Phonemax, phoneOne, Pico, PINE, Pioneer, Pioneer Computers, PiPO, PIRANHA, Pixela, Pixelphone, Pixus, Planet Computers, Platoon, Play Now, Ployer, Plum, PlusStyle, Pluzz, PocketBook, POCO, Point Mobile, Point of View, Polar, PolarLine, Polaroid, Polestar, PolyPad, Polytron, Pomp, Poppox, POPTEL, Porsche, Positivo, Positivo BGH, PPTV, Premier, Premio, Prestigio, PRIME, Primepad, Primux, Pritom, Prixton, PROFiLO, Proline, Prology, ProScan, PROSONIC, Protruly, ProVision, PULID, Punos, Purism, Q-Box, Q-Touch, Q.Bell, QFX, Qilive, QLink, QMobile, Qnet Mobile, QTECH, Qtek, Quantum, Quatro, Qubo, Quechua, Quest, Quipus, Qumo, Qware, QWATT, R-TV, Rakuten, Ramos, Raspberry, Ravoz, Raylandz, Razer, RCA Tablets, Reach, Readboy, Realme, RED, Redbean, Redfox, RedLine, Redway, Reeder, REGAL, RelNAT, Remdun, Retroid Pocket, Revo, Revomovil, Ricoh, Rikomagic, RIM, Rinno, Ritmix, Ritzviva, Riviera, Rivo, Rizzen, ROADMAX, Roadrover, Roam Cat, ROiK, Rokit, Roku, Rombica, Ross&Moor, Rover, RoverPad, Royole, RoyQueen, RT Project, RugGear, RuggeTech, Ruggex, Ruio, Runbo, Rupa, Ryte, S-TELL, S2Tel, Saba, Safaricom, Sagem, Sagemcom, Saiet, SAILF, Salora, Samsung, Samtech, Samtron, Sanei, Sankey, Sansui, Santin, SANY, Sanyo, Savio, Sber, SCBC, Schneider, Schok, Scosmos, Seatel, SEBBE, Seeken, SEEWO, SEG, Sega, SEHMAX, Selecline, Selenga, Selevision, Selfix, SEMP TCL, Sencor, Sendo, Senkatel, Senseit, Senwa, Seuic, Sewoo, SFR, SGIN, Shanling, Sharp, Shift Phones, Shivaki, Shtrikh-M, Shuttle, Sico, Siemens, Sigma, Silelis, Silent Circle, Silva Schneider, Simbans, simfer, Simply, Singtech, Siragon, Sirin Labs, SK Broadband, SKG, SKK Mobile, Sky, Skyline, SkyStream, Skytech, Skyworth, Smadl, Smailo, Smart, Smartab, SmartBook, SMARTEC, Smart Electronic, Smartex, Smartfren, Smartisan, Smart Kassel, Smarty, Smooth Mobile, Smotreshka, SNAMI, SobieTech, Soda, Softbank, Soho Style, Solas, SOLE, SOLO, Solone, Sonim, SONOS, Sony, Sony Ericsson, SOSH, Soundmax, Soyes, Spark, Sparx, SPC, Spectralink, Spectrum, Spice, Sprint, SPURT, SQOOL, SSKY, Star, Starlight, Starmobile, Starway, Starwind, STF Mobile, STG Telecom, STK, Stonex, Storex, StrawBerry, Stream, STRONG, Stylo, Subor, Sugar, Sumvision, Sunmax, Sunmi, Sunny, Sunstech, SunVan, Sunvell, SUNWIND, SuperBOX, Super General, SuperSonic, SuperTab, Supra, Supraim, Surge, Suzuki, Sveon, Swipe, SWISSMOBILITY, Swisstone, Switel, Syco, SYH, Sylvania, Symphony, Syrox, System76, T-Mobile, T96, TADAAM, TAG Tech, Taiga System, Takara, Talius, Tambo, Tanix, TAUBE, TB Touch, TCL, TD Systems, TD Tech, TeachTouch, Technicolor, Technika, TechniSat, Technopc, TechnoTrend, TechPad, TechSmart, Techstorm, Techwood, Teclast, Tecno Mobile, TecToy, TEENO, Teknosa, Tele2, Telefunken, Telego, Telenor, Telia, Telit, Telkom, Telly, Telma, TeloSystems, Telpo, TENPLUS, Teracube, Tesco, Tesla, TETC, Tetratab, teXet, ThL, Thomson, Thuraya, TIANYU, Tibuta, Tigers, Time2, Timovi, TIMvision, Tinai, Tinmo, TiPhone, TiVo, TJC, TOKYO, Tolino, Tone, TOOGO, Tooky, TopDevice, TOPDON, Topelotek, Top House, Toplux, TOPSHOWS, Topsion, Topway, Torex, Torque, TOSCIDO, Toshiba, Touchmate, Touch Plus, TOX, TPS, Transpeed, TrekStor, Trevi, TriaPlay, Trident, Trifone, Trio, Tronsmart, True, True Slim, Tsinghua Tongfang, TTEC, TTfone, TTK-TV, TuCEL, Tunisie Telecom, Turbo, Turbo-X, TurboKids, TurboPad, Turkcell, TVC, TwinMOS, TWM, Twoe, TWZ, Tymes, Türk Telekom, U-Magic, U.S. Cellular, UD, UE, UGINE, Ugoos, Uhans, Uhappy, Ulefone, Umax, UMIDIGI, Unblock Tech, Uniden, Unihertz, Unimax, Uniqcell, Uniscope, Unistrong, Unitech, UNIWA, Unknown, Unnecto, Unnion Technologies, UNNO, Unonu, Unowhy, UOOGOU, Urovo, UTime, UTOK, UTStarcom, UZ Mobile, V-Gen, V-HOME, V-HOPE, v-mobile, VAIO, VALEM, VALTECH, VANGUARD, Vankyo, VANWIN, Vargo, Vastking, VAVA, VC, VDVD, Vega, Vekta, Venso, Venstar, Venturer, VEON, Verico, Verizon, Vernee, Verssed, Versus, Vertex, Vertu, Verykool, Vesta, Vestel, VETAS, Vexia, VGO TEL, ViBox, Victurio, VIDA, Videocon, Videoweb, ViewSonic, VIIPOO, Vinabox, Vinga, Vinsoc, Vios, Viper, Vipro, Virzo, Vision Touch, Visual Land, Vitelcom, Vityaz, Viumee, Vivax, VIVIMAGE, Vivo, VIWA, Vizio, Vizmo, VK Mobile, VKworld, Vodacom, Vodafone, VOGA, VOLIA, VOLKANO, Volla, Volt, Vonino, Vontar, Vorago, Vorcom, Vorke, Vormor, Vortex, Voto, VOX, Voxtel, Voyo, Vsmart, Vsun, VUCATIMES, Vue Micro, Vulcan, VVETIME, Völfen, WAF, Walker, Walton, Waltter, Wanmukang, WANSA, WE, We. by Loewe., Webfleet, Web TV, WeChip, Wecool, Weelikeit, Weimei, WellcoM, WELLINGTON, Western Digital, Westpoint, Wexler, White Mobile, Wieppo, Wigor, Wiko, Wileyfox, Winds, Wink, Winmax, Winnovo, Winstar, Wintouch, Wiseasy, WIWA, WizarPos, Wizz, Wolder, Wolfgang, Wolki, WONDER, Wonu, Woo, Wortmann, Woxter, X-AGE, X-BO, X-Mobile, X-TIGI, X-View, X.Vision, X88, X96, X96Q, Xcell, XCOM, Xcruiser, XElectron, XGEM, XGIMI, Xgody, Xiaodu, Xiaolajiao, Xiaomi, Xion, Xolo, Xoro, Xshitou, Xtouch, Xtratech, Xwave, XY Auto, Yandex, Yarvik, YASIN, YELLYOUTH, YEPEN, Yes, Yestel, Yezz, Yoka TV, Yooz, Yota, YOTOPT, Youin, Youwei, Ytone, Yu, Yuandao, YU Fly, YUHO, YUMKEM, YUNDOO, Yuno, YunSong, Yusun, Yxtel, Z-Kai, Zaith, Zamolxe, Zatec, Zealot, Zeblaze, Zebra, Zeeker, Zeemi, Zen, Zenek, Zentality, Zfiner, ZH&K, Zidoo, ZIFRO, Zigo, ZIK, Zinox, Ziox, Zonda, Zonko, Zoom, ZoomSmart, Zopo, ZTE, Zuum, Zync, ZYQ, Zyrex, öwn

### List of detected bots:

2GDPR, 2ip, 360 Monitoring, 360JK, 360Spider, Abonti, Aboundexbot, AccompanyBot, Acoon, AdAuth, Adbeat, AddThis.com, ADMantX, ADmantX Service Fetcher, Adsbot, Adscanner, AdsTxtCrawler, AdsTxtCrawlerTP, adstxtlab.com, aHrefs Bot, AhrefsSiteAudit, aiHitBot, Alexa Crawler, Alexa Site Audit, Allloadin Favicon Bot, Amazon AdBot, Amazon Bot, Amazon ELB, Amazon Route53 Health Check, Amorank Spider, Analytics SEO Crawler, Ant, Anthropic AI, ApacheBench, Applebot, AppSignalBot, Arachni, archive.org bot, ArchiveBot, ArchiveBox, Arquivo.pt, ARSNova Filter System, Asana, Ask Jeeves, AspiegelBot, Automattic Analytics, Awario, Backlink-Check.de, BacklinkCrawler, BackupLand, Baidu Spider, Barkrowler, Barracuda Sentinel, BazQux Reader, BBC Forge URL Monitor, BBC Page Monitor, BDCbot, Better Uptime Bot, BingBot, Birdcrawlerbot, BitlyBot, BitSight, Blekkobot, BLEXBot Crawler, Bloglovin, Blogtrottr, BoardReader, BoardReader Blog Indexer, Botify, Bountii Bot, BrandVerity, BrightBot, BrightEdge, Browsershots, BUbiNG, Buck, BuiltWith, Butterfly Robot, Bytespider, CareerBot, Castro 2, Catchpoint, CATExplorador, ccBot crawler, CensysInspect, Charlotte, Chartable, ChatGPT, CheckHost, CheckMark Network, Choosito, Chrome Privacy Preserving Prefetch Proxy, Cincraw, CISPA Web Analyzer, ClaudeBot, Clickagy, Cliqzbot, CloudFlare Always Online, CloudFlare AMP Fetcher, Cloudflare Custom Hostname Verification, Cloudflare Diagnostics, Cloudflare Health Checks, Cloudflare Security Insights, Cloudflare SSL Detector, Cloudflare Traffic Manager, Cocolyzebot, Cohere AI, Collectd, colly, CommaFeed, COMODO DCV, Comscore, ContentKing, Cookiebot, Crawldad, Crawlson, CriteoBot, CrowdTangle, CSS Certificate Spider, CSSCheck, Cyberscan, Cypress, Cốc Cốc Bot, DaspeedBot, Datadog Agent, DataForSeoBot, datagnionbot, Datanyze, Dataprovider, DataXu, Daum, Dazoobot, deepnoc, Detectify, Diffbot, Discobot, Discord Bot, Disqus, DNSResearchBot, DomainAppender, DomainCrawler, Domain Re-Animator Bot, Domain Research Project, Domains Project, DomainStatsBot, DomCop Bot, DotBot, Dotcom Monitor, Dubbotbot, DuckDuckGo Bot, DynatraceSynthetic, Easou Spider, eCairn-Grabber, EFF Do Not Track Verifier, EMail Exractor, EmailWolf, Embedly, Entfer, evc-batch, Everyfeed, ExaBot, ExactSeek Crawler, Example3, Exchange check, Expanse, EyeMonit, Ezgif, Ezooms, eZ Publish Link Validator, FacebookBot, Facebook External Hit, Faveeo, Feedbin, FeedBurner, Feedly, Feedspot, Feed Wrangler, Femtosearch, Fever, Findxbot, Flipboard, FontRadar, fragFINN, FreeWebMonitoring, FreshRSS, GDNP, GeedoBot, Generic Bot, Genieo Web filter, Ghost Inspector, Gigablast, Gigabot, GitCrawlerBot, GitHubCopilotChat, Gluten Free Crawler, Gmail Image Proxy, Gobuster, Goo, Google Apps Script, Google Area 120 Privacy Policy Fetcher, Googlebot, Googlebot News, Google Cloud Scheduler, Google Favicon, Google PageSpeed Insights, Google Partner Monitoring, Google Search Console, Google Stackdriver Monitoring, Google StoreBot, Google Structured Data Testing Tool, Google Transparency Report, Gowikibot, Gozle, GPTBot, Grammarly, Grapeshot, Gregarius, GTmetrix, GumGum Verity, hackermention, Hatena Bookmark, Hatena Favicon, Headline, Heart Rails Capture, Heritrix, Heureka Feed, htmlyse, HTTPMon, httpx, HuaweiWebCatBot, HubPages, HubSpot, ICC-Crawler, ichiro, IDG/IT, Iframely, IIS Site Analysis, ImageSift, Inetdex Bot, Infegy, InfoTigerBot, Inktomi Slurp, inoreader, Intelligence X, Interactsh, InternetMeasurement, IONOS Crawler, IP-Guide Crawler, IPIP, IPS Agent, IsItWP, JobboerseBot, JungleKeyThumbnail, K6, KadoBot, Kaspersky, KeyCDN Tools, Keys.so, Kiwi TCMS GitOps, KlarnaBot, KomodiaBot, Konturbot, Kouio, Kozmonavt, l9explore, l9tcpid, LAC IA Harvester, Larbin web crawler, LastMod Bot, LCC, LeakIX, Let's Encrypt Validation, LetSearch, Lighthouse, Linespider, Linkdex Bot, LinkedIn Bot, LinkpadBot, LinkPreview, LinkWalker, LTX71, Lumar, LumtelBot, Lycos, MaCoCu, MADBbot, Magpie-Crawler, MagpieRSS, Mail.Ru Bot, MariaDB/MySQL Knowledge Base, masscan, masscan-ng, Mastodon Bot, Matomo, Meanpath Bot, Mediatoolkit Bot, MegaIndex, MeltwaterNews, MetaInspector, MetaJobBot, MicroAdBot, Microsoft Preview, Mixnode, Mixrank Bot, MJ12 Bot, Mnogosearch, MojeekBot, Monitor.Us, Monitor Backlinks, Monsidobot, Montastic Monitor, MoodleBot Linkchecker, Morningscore Bot, MTRobot, Munin, MuscatFerret, Nagios check_http, NalezenCzBot, nbertaupete95, Neevabot, Netcraft Survey Bot, netEstate, NetLyzer FastProbe, Netpeak Checker, NetResearchServer, NetSystemsResearch, NetTrack, Netvibes, NETZZAPPEN, NewsBlur, NewsGator, Newslitbot, NiceCrawler, Nimbostratus Bot, NLCrawler, Nmap, Notify Ninja, Nutch-based Bot, Nuzzel, oBot, Octopus, Odin, Odnoklassniki Bot, Omgili bot, OmtrBot, Onalytica, OnlineOrNot Bot, Openindex Spider, OpenLinkProfiler, OpenWebSpider, Orange Bot, OSZKbot, Outbrain, Overcast Podcast Sync, OWLer, Pageburst, Page Modified Pinger, PagePeeker, PageThing, Panscient, PaperLiBot, Paqlebot, parse.ly, PATHspider, PayPal IPN, PDR Labs, PerplexityBot, Petal Bot, Phantomas, phpMyAdmin, PHP Server Monitor, Picsearch bot, PingAdmin.Ru, Pingdom Bot, Pinterest, PiplBot, Plesk Screenshot Service, Plukkie, Pocket, Podroll Analyzer, PodUptime, Pompos, PritTorrent, Probely, Project Patchwatch, Project Resonance, Prometheus, PRTG Network Monitor, Quantcast, QuerySeekerSpider, Quora Bot, Quora Link Preview, Qwantify, Rainmeter, RamblerMail Image Proxy, RavenCrawler, Reddit Bot, RenovateBot, Repo Lookout, ReqBin, Research Scan, Riddler, Robozilla, RocketMonitorBot, Rogerbot, ROI Hunter, RSSRadio Bot, RuxitSynthetic, Ryowl, SabsimBot, SafeDNSBot, Sandoba//Crawler, Scamadviser External Hit, Scooter, ScoutJet, Scraping Robot, Scrapy, Screaming Frog SEO Spider, ScreenerBot, Sectigo DCV, security.txt scanserver, Seekport, Sellers.Guide, Semantic Scholar Bot, SemrushBot, Semrush Reputation Management, Sensika Bot, Sentry Bot, Senuto, Seobility, SEOENGBot, SEOkicks, SEOkicks-Robot, seolyt, Seolyt Bot, Seoscanners.net, Serendeputy Bot, Serenety, serpstatbot, Server Density, Seznam Bot, Seznam Email Proxy, Seznam Zbozi.cz, sfFeedReader, ShopAlike, Shopify Partner, ShopWiki, SilverReader, SimplePie, Sirdata, SISTRIX Crawler, SISTRIX Optimizer, Site24x7 Defacement Monitor, Site24x7 Website Monitoring, SiteAuditBot, Sitebulb, Siteimprove, SitemapParser-VIPnytt, SiteOne Crawler, SiteScore, SiteSucker, Sixy.ch, Skype URI Preview, Slackbot, SMTBot, Snapchat Ads, Snapchat Proxy, Snap URL Preview Service, Sogou Spider, Soso Spider, Sparkler, Spawning AI, SpazioDati, Speedy, Spinn3r, SplitSignalBot, Spotify, Sprinklr, Sputnik Bot, Sputnik Favicon Bot, Sputnik Image Bot, sqlmap, SSL Labs, start.me, Startpagina Linkchecker, StatOnline.ru, StatusCake, Steve Bot, Stract, Sublinq, Superfeedr Bot, SurdotlyBot, Survey Bot, Swiftbot, Synapse, t3versions, Taboolabot, TactiScout, Tag Inspector, Tarmot Gezgin, tchelebi, TelegramBot, TestCrawler, The Knowledge AI, theoldreader, The Trade Desk Content, ThinkChaos, ThousandEyes, TigerBot, TinEye, TinEye Crawler, Tiny Tiny RSS, TLSProbe, TraceMyFile, Trendiction Bot, Trendsmap, Turnitin, TurnitinBot, TweetedTimes Bot, Tweetmeme Bot, Twingly Recon, Twitterbot, Twurly, UCSB Network Measurement, UkrNet Mail Proxy, uMBot, UniversalFeedParser, Uptime-Kuma, Uptimebot, UptimeRobot, Uptimia, URLAppendBot, URLinspector, URLSuMaBot, Vagabondo, Velen Public Web Crawler, Vercel Bot, VeryHip, Viber Url Downloader, VirusTotal Cloud, Visual Site Mapper Crawler, VK Robot, VK Share Button, Vuhuv Bot, W3C CSS Validator, W3C I18N Checker, W3C Link Checker, W3C Markup Validation Service, W3C MobileOK Checker, W3C P3P Validator, W3C Unified Validator, Wappalyzer, WebbCrawler, WebCEO, WebDataStats, WebMon, Weborama, WebPageTest, WebPros, WebSitePulse, WebThumbnail, webtru, Webwiki, WellKnownBot, WeSEE:Search, WeViKaBot, WhatCMS, WhatsMyIP.org, WhereGoes, Who.is Bot, Wibybot, WikiDo, Willow Internet Crawler, WireReaderBot, WooRank, WordPress, Wotbox, XenForo, XoviBot, YaCy, Yahoo! Cache System, Yahoo! Japan, Yahoo! Japan ASR, Yahoo! Japan BRW, Yahoo! Japan WSC, Yahoo! Link Preview, Yahoo! Mail Proxy, Yahoo! Slurp, Yahoo Gemini, YaK, Yandex Bot, Yeti/Naverbot, Yottaa Site Monitor, YouBot, Youdao Bot, Yourls, Yunyun Bot, Zaldamo, Zao, Ze List, Zeno, zgrab, Zookabot, ZoomBot, ZoominfoBot, ZumBot
