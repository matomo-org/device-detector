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

## Icon packs

If you are looking for icons to use alongside Device Detector, these repositories can be of use:
- Official [Matomo](https://github.com/matomo-org/matomo-icons/) pack
- Unofficial [Simbiat](https://github.com/Simbiat/DeviceDetectorIcons) pack

## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: 2023/10/01*

### List of detected operating systems:

AIX, Android, Android TV, Amazon Linux, AmigaOS, tvOS, Arch Linux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Bliss OS, Brew, Caixa Mágica, CentOS, CentOS Stream, ClearOS Mobile, Chrome OS, Chromium OS, China OS, CyanogenMod, Debian, Deepin, DragonFly, DVKBuntu, Fedora, Fenix, Firefox OS, Fire OS, Foresight Linux, Freebox, FreeBSD, FydeOS, Fuchsia, Gentoo, GridOS, Google TV, HP-UX, Haiku OS, iPadOS, HarmonyOS, HasCodingOS, IRIX, Inferno, Java ME, KaiOS, Kanotix, Knoppix, KreaTV, Kubuntu, GNU/Linux, LindowsOS, Linspire, Lineage OS, Lubuntu, Lumin OS, VectorLinux, Mac, Maemo, Mageia, Mandriva, MeeGo, MocorDroid, moonOS, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, MRE, Nintendo, Nintendo Mobile, Nova, OS/2, OSF1, OpenBSD, OpenWrt, Opera TV, Ordissimo, Pardus, PCLinuxOS, Plasma Mobile, PlayStation Portable, PlayStation, PureOS, Red Hat, RedOS, Revenge OS, RISC OS, Roku OS, Rosa, RouterOS, Remix OS, Resurrection Remix OS, REX, RazoDroiD, Sabayon, SUSE, Sailfish OS, SeewoOS, Sirin OS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, TencentOS, ThreadX, Tizen, TmaxOS, Ubuntu, watchOS, Wear OS, WebTV, Whale OS, Windows, Windows CE, Windows IoT, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOS, Zenwalk, ZorinOS, iOS, palmOS, webOS

### List of detected browsers:

Via, Pure Mini Browser, Pure Lite Browser, Raise Fast Browser, Rabbit Private Browser, Fast Browser UC Lite, Fast Explorer, Lightning Browser, Cake Browser, IE Browser Fast, Vegas Browser, OH Browser, OH Private Browser, XBrowser Mini, Sharkee Browser, Lark Browser, Pluma, Anka Browser, Azka Browser, Dragon Browser, Easy Browser, Dark Web Browser, 18+ Privacy Browser, 115 Browser, 1DM Browser, 1DM+ Browser, 2345 Browser, 360 Browser, 360 Phone Browser, 7654 Browser, Avant Browser, ABrowse, AdBlock Browser, Adult Browser, ANT Fresco, ANTGalio, Aloha Browser, Aloha Browser Lite, ALVA, Amaya, Amaze Browser, Amerigo, Amigo, Android Browser, AOL Desktop, AOL Shield, AOL Shield Pro, AppBrowzer, APUS Browser, Arora, Arctic Fox, Amiga Voyager, Amiga Aweb, APN Browser, Arvin, Ask.com, Asus Browser, Atom, Atomic Web Browser, Atlas, Avast Secure Browser, AVG Secure Browser, Avira Secure Browser, AwoX, Beaker Browser, Beamrise, BlackBerry Browser, BrowseHere, Browser Hup Pro, Baidu Browser, Baidu Spark, Bangla Browser, Basilisk, Belva Browser, Beyond Private Browser, Beonex, Berry Browser, Bitchute Browser, BlackHawk, Bloket, Bunjalloo, B-Line, Black Lion Browser, Blue Browser, Bonsai, Borealis Navigator, Brave, BriskBard, Browspeed Browser, BrowseX, Browzar, Browlser, BrowsBit, Biyubi, Byffox, BF Browser, Camino, CCleaner, CG Browser, ChanjetCloud, Chedot, Cherry Browser, Centaury, Coc Coc, CoolBrowser, Colibri, Comodo Dragon, Coast, Charon, CM Browser, CM Mini, Chrome Frame, Headless Chrome, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, Chowbo, CoolNovo, CometBird, Comfort Browser, COS Browser, Cornowser, Chim Lac, ChromePlus, Chromium, Chromium GOST, Cyberfox, Cheshire, Crusta, Craving Explorer, Crazy Browser, Cunaguaro, Chrome Webview, CyBrowser, dbrowser, Peeps dBrowser, Debuggable Browser, Decentr, Deepnet Explorer, deg-degan, Deledao, Delta Browser, Desi Browser, DeskBrowse, Dolphin, Dolphin Zero, Dorado, Dot Browser, Dooble, Dillo, DUC Browser, DuckDuckGo Privacy Browser, Ecosia, Edge WebView, Epic, Elinks, EinkBro, Element Browser, Elements Browser, Explore Browser, eZ Browser, EUI Browser, GNOME Web, G Browser, Espial TV Browser, Falkon, Faux Browser, Fiery Browser, Firefox Mobile iOS, Firebird, Fluid, Fennec, Firefox, Firefox Focus, Firefox Reality, Firefox Rocket, Firefox Klar, Float Browser, Flock, Floorp, Flow, Flow Browser, Firefox Mobile, Fireweb, Fireweb Navigator, Flash Browser, Flast, Flyperlink, FreeU, Frost+, Fulldive, Galeon, Gener8, Ghostery Privacy Browser, GinxDroid Browser, Glass Browser, Google Earth, Google Earth Pro, GOG Galaxy, GoBrowser, Harman Browser, HasBrowser, Hawk Turbo Browser, Hawk Quick Browser, Helio, Hexa Web Browser, Hi Browser, hola! Browser, HotJava, HTC Browser, Huawei Browser Mobile, Huawei Browser, HUB Browser, iBrowser, iBrowser Mini, IBrowse, iDesktop PC Browser, iCab, iCab Mobile, Iridium, Iron Mobile, IceCat, IceDragon, Isivioo, IVVI Browser, Iceweasel, Inspect Browser, Internet Explorer, Internet Browser Secure, Indian UC Mini Browser, IE Mobile, Iron, Japan Browser, Jasmine, JavaFX, Jelly, Jig Browser, Jig Browser Plus, Jio Browser, JioPages, K.Browser, Keepsafe Browser, Kids Safe Browser, Kindle Browser, K-meleon, Konqueror, Kapiko, Kinza, Kiwi, Kode Browser, KUTO Mini Browser, Kylo, Kazehakase, Cheetah Browser, Lagatos Browser, Lexi Browser, Lenovo Browser, LieBaoFast, LG Browser, Light, Lilo, Links, LogicUI TV Browser, Lolifox, Lovense Browser, LT Browser, LuaKit, LUJO TV Browser, Lulumi, Lunascape, Lunascape Lite, Lynx, Lynket Browser, Mandarin, mCent, MicroB, NCSA Mosaic, Meizu Browser, Mercury, Me Browser, Mobile Safari, Midori, Midori Lite, Mobicip, MIUI Browser, Mobile Silk, Minimo, Mint Browser, Maxthon, MaxTube Browser, Maelstrom, Mmx Browser, MxNitro, Mypal, Monument Browser, MAUI WAP Browser, Navigateur Web, Naked Browser, Naked Browser Pro, NFS Browser, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, Norton Secure Browser, Nox Browser, NOMone VR Browser, NetSurf, NetFront, NetFront Life, NetPositive, Netscape, NextWord Browser, NTENT Browser, Oculus Browser, Odd Browser, Opera Mini iOS, Obigo, Odin, Odin Browser, OceanHero, Odyssey Web Browser, Off By One, Office Browser, OhHai Browser, ONE Browser, Opera Crypto, Opera GX, Opera Neon, Opera Devices, Opera Mini, Opera Mobile, Opera, Opera Next, Opera Touch, Orca, Ordissimo, Oregano, Origin In-Game Overlay, Origyn Web Browser, OrNET Browser, Openwave Mobile Browser, OpenFin, Open Browser, Open Browser 4U, Open Browser fast 5G, Open TV Browser, OmniWeb, Otter Browser, Palm Blazer, Pale Moon, Polypane, Oppo Browser, Opus Browser, Palm Pre, Puffin, Puffin Web Browser, Palm WebPro, Palmscape, Pawxy, Perfect Browser, Phantom.me, Phantom Browser, Phoenix, Phoenix Browser, PlayFree Browser, PocketBook Browser, Polaris, Polarity, PolyBrowser, PrivacyWall, Privacy Explorer Fast Safe, Pi Browser, PronHub Browser, PSI Secure Browser, Reqwireless WebViewer, Microsoft Edge, Qazweb, QQ Browser Lite, QQ Browser Mini, QQ Browser, Quick Browser, Qutebrowser, Quark, QupZilla, Qwant Mobile, QtWebEngine, Realme Browser, Rekonq, RockMelt, Samsung Browser, Samsung Browser Lite, Sailfish Browser, Seewo Browser, SEMC-Browser, Sogou Explorer, Sogou Mobile Browser, SOTI Surf, Soul Browser, Soundy Browser, Safari, Safari Technology Preview, Safe Exam Browser, SalamWeb, Savannah Browser, SavySoda, Secure Browser, SFive, Shiira, Sidekick, SimpleBrowser, SilverMob US, Sizzy, Skyfire, Seraphic Sraf, SiteKiosk, Sleipnir, Slimjet, SP Browser, Sony Small Browser, Secure Private Browser, Stampy Browser, 7Star, Smart Browser, Smart Search & Web Browser, Smart Lenovo Browser, Smooz, Snowshoe, Spectre Browser, Splash, Sputnik Browser, Sunrise, SuperBird, Super Fast Browser, SuperFast Browser, Sushi Browser, surf, Surf Browser, Stargon, START Internet Browser, Steam In-Game Overlay, Streamy, Swiftfox, Seznam Browser, Sweet Browser, SX Browser, T+Browser, T-Browser, t-online.de Browser, Tao Browser, TenFourFox, Tenta Browser, Tesla Browser, Tizen Browser, Tint Browser, TUC Mini Browser, Tungsten, ToGate, TweakStyle, TV Bro, U Browser, UBrowser, UC Browser, UC Browser HD, UC Browser Mini, UC Browser Turbo, Ui Browser Mini, UR Browser, Uzbl, Ume Browser, vBrowser, Vast Browser, Venus Browser, Nova Video Downloader Pro, Viasat Browser, Vivaldi, vivo Browser, Vivid Browser Mini, Vision Mobile Browser, VMware AirWatch, Wear Internet Browser, Web Explorer, Web Browser & Explorer, WebPositive, Waterfox, Wave Browser, Wavebox, Whale Browser, wOSBrowser, WeTab Browser, Wolvic, YAGI, Yahoo! Japan Browser, Yandex Browser, Yandex Browser Lite, Yaani Browser, Yo Browser, Yolo Browser, YouCare, Yuzu Browser, xBrowser, X Browser Lite, X-VPN, xBrowser Pro Super Fast, XNX Browser, XtremeCast, xStand, Xiino, Xooloo Internet, Xvast, Zetakey, Zvu, Zirco Browser

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Elektra, Presto, Gecko, KHTML, NetFront, Edge, NetSurf, Servo, Goanna, EkiohFlow

### List of detected libraries:

aiohttp, Akka HTTP, AnyEvent HTTP, Apache HTTP Client, Aria2, Artifactory, Axios, Azure Data Factory, Buildah, BuildKit, C++ REST SDK, Containerd, containers, cPanel HTTP Client, cri-o, curl, Dart, docker, Embarcadero URI Client, Faraday, fasthttp, GeoIP Update, go-container registry, Go-http-client, Google HTTP Java Client, got, GRequests, gRPC-Java, Guzzle (PHP HTTP Client), gvfs, hackney, Harbor registry client, Helm, HTTPie, httplib2, HTTPX, HTTP_Request2, Insomnia REST Client, Jakarta Commons HttpClient, Java, Java HTTP Client, jsdom, libdnf, libpod, LUA OpenResty NGINX, Mechanize, Mikrotik Fetch, Node Fetch, OkHttp, Open Build Service, Pa11y, Perl, Perl REST::Client, PHP cURL Class, Postman Desktop, Python Requests, Python urllib, quic-go, r-curl, ReactorNetty, req, REST Client for Ruby, RestSharp, Resty, ScalaJ HTTP, Skopeo, SlimerJS, Typhoeus, uclient-fetch, Ultimate Sitemap Parser, Unirest for Java, urlgrabber (yum), uTorrent, Wget, Windows HTTP, WinHttp WinHttpRequest, WWW-Mechanize

### List of detected media players:

Audacious, Banshee, Boxee, Clementine, Deezer, Downcast, FlyCast, Foobar2000, foobar2000, Google Podcasts, HTC Streaming Player, iTunes, JHelioviewer, Kodi, MediaMonkey, Miro, MPlayer, mpv, Music Player Daemon, NexPlayer, Nightingale, QuickTime, Songbird, SONOS, Sony Media Go, Stagefright, StudioDisplay, SubStream, VLC, Winamp, Windows Media Player, XBMC

### List of detected mobile apps:

1Password, 2tch, Adobe Creative Cloud, Adobe IPM, Adobe NGL, Adobe Synchronizer, Aha Radio 2, AIDA64, Alexa Media Player, AliExpress, Alipay, Amazon Music, Amazon Shopping, AndroidDownloadManager, AntennaPod, AntiBrowserSpy, Apple News, Apple TV, ASUS Updater, Audible, Avid Link, Background Intelligent Transfer Service, Baidu Box App, Baidu Input, Ballz, Bank Millenium, Battle.net, BB2C, BBC News, Be Focused, BetBull, BeyondPod, Bible KJV, Binance, Bing iPad, BingWebApp, Bitcoin Core, Bitsboard, Blackboard, Blitz, Blue Proxy, BlueStacks, BonPrix, Bookshelf, Bose Music, bPod, CastBox, Castro, Castro 2, CCleaner, CGN, ChMate, Chrome Update, Ciisaa, Citrix Workspace, Clovia, COAF SMART Citizen, Copied, Cortana, Covenant Eyes, CPU-Z, CrosswalkApp, Daum, DevCasts, DeviantArt, DingTalk, DIRECTV, Discord, Dogecoin Core, DoggCatcher, Don't Waste My Time!, douban App, Downcast, Dr. Watson, DStream Air, Edge Update, Emby Theater, Epic Games Launcher, ESET Remote Administrator, eToro, Evolve Podcast, Expedia, F-Secure SAFE, Facebook, Facebook Audience Network, Facebook Groups, Facebook Lite, Facebook Messenger, Facebook Messenger Lite, FeedR, Flipboard App, Flipp, Focus Keeper, Focus Matrix, Gaana, GBWhatsApp, Git, GitHub Desktop, GlobalProtect, GoNative, Google Drive, Google Fiber TV, Google Go, Google Photos, Google Play Newsstand, Google Plus, Google Podcasts, Google Search App, Google Tag Manager, GroupMe, Hago, HandBrake, HeyTapBrowser, Hik-Connect, HiSearch, HisThumbnail, HP Smart, HTTP request maker, iCatcher, IMO HD Video Calls & Chat, IMO International Calls & Chat, Instabridge, Instacast, Instagram App, Instapaper, JaneStyle, Jitsi Meet, JJ2GO, Jungle Disk, KakaoTalk, Keeper Password Manager, Kik, Klarna, Landis+Gyr AIM Browser, Lazada, Line, LinkedIn, Logi Options+, LoseIt!, Macrium Reflect, MBolsa, MEmpresas, Mercantile Bank of Michigan, Meta Business Suite, MetaTrader, Microsoft Bing Search, Microsoft Lync, Microsoft Office, Microsoft Office $1, Microsoft Office Mobile, Microsoft OneDrive, Microsoft Start, Microsoft Store, mobile.de, My Bentley, My Watch Party, My World, Naver, NET.mede, Netflix, NewsArticle App, Nextcloud, NPR One, NTV Mobil, NuMuKi Browser, Odnoklassniki, OfferUp, Opal Travel, Opera News, Opera Updater, Orange Radio, Overcast, Paint by Number, Pandora, Papers, Petal Search App, Pic Collage, Pinterest, Player FM, Plex Media Server, Pocket Casts, Podbean, Podcast & Radio Addict, Podcaster, Podcast Republic, Podcasts, Podcat, Podcatcher Deluxe, Podimo, Podkicker$1, PowerShell, Procast, Q-municate, qBittorrent, QQMusic, QuickCast, Quick Search TV, Quora, R, RadioApp, Radio Italiane, RadioPublic, Rave Social, Razer Synapse, RDDocuments, Reddit, rekordbox, RNPS Action Cards, Roblox, RoboForm, Rocket Chat, RSSRadio, Rutube, Safari Search Helper, SafeIP, Samsung Magician, Shopee, ShowMe, Sina Weibo, Siri, Skyeng, Skyeng Teachers, Skype, Skype for Business, Slack, Snapchat, SogouSearch App, SohuNews, Soldier, SPORT1, Spotify, Startsiden, Streamlabs OBS, Strimio, Surfshark, Swoot, Taobao, Teams, The Wall Street Journal, Theyub, Thunder, tieba, TikTok, TopBuzz, TradingView, TuneIn Radio, TuneIn Radio Pro, Tuya Smart Life, TVirl, twinkle, Twitter, Twitterrific, U-Cursos, Uconnect LIVE, Unibox, UnityPlayer, Viber, Visual Studio Code, Vuhuv, Vuze, Wattpad, Wayback Machine, WebDAV, WeChat, WeChat Share Extension, WhatsApp, WhatsApp+2, Whisper, WH Questions, Windows Antivirus, Windows CryptoAPI, Windows Delivery Optimization, Windows Push Notification Services, Windows Update Agent, Wireshark, Wirtschafts Woche, Word Cookies!, WPS Office, Y8 Browser, Yahoo! Japan, Yahoo OneSearch, YakYak, Yandex, Yelp Mobile, YouTube, Zalo, ZEPETO, Zoho Chat and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Barca, Basecamp, BathyScaphe, DAVdroid, eM Client, Evernote, Franz, JaneView, Live5ch, Lotus Notes, MailBar, Mailbird, Mailspring, Microsoft Outlook, NAVER Mail, Notion, Outlook Express, Postbox, Raindrop.io, Rambox Pro, SeaMonkey, The Bat!, Thunderbird, Windows Mail, Yahoo Mail

### List of detected feed readers:

Akregator, Apple PubSub, BashPodder, Breaker, FeedDemon, Feeddler RSS Reader, gPodder, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, PritTorrent, Pulp, QuiteRSS, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

2E, 3GNET, 3GO, 3Q, 4Good, 4ife, 5IVE, 7 Mobile, 10moons, 360, 8848, A1, A95X, Accent, Accesstyle, Ace, Acer, Acteck, actiMirror, Adronix, Advan, Advance, Advantage Air, AEEZO, AFFIX, AfriOne, AGM, AG Mobile, AIDATA, Ainol, Airis, Airness, AIRON, Airpha, Airtel, Airties, AIS, Aiuto, Aiwa, Akai, AKIRA, Alba, Alcatel, Alcor, ALDI NORD, ALDI SÜD, Alfawise, Aligator, AllCall, AllDocube, ALLINmobile, Allview, Allwinner, Alps, Altech UEC, Altice, altron, AMA, Amazon, AMCV, AMGOO, Amigoo, Amino, Amoi, Andowl, Angelcare, Anker, Anry, ANS, ANXONIT, AOC, Aocos, AOpen, Aoro, Aoson, AOYODKG, Apple, Aquarius, Archos, Arian Space, Ark, ArmPhone, Arnova, ARRIS, Artel, Artizlee, ArtLine, Asano, Asanzo, Ask, Aspera, ASSE, Assistant, Astro, Asus, AT&T, Athesi, Atmaca Elektronik, ATMAN, ATOL, Atom, Attila, Atvio, Audiovox, AURIS, Autan, AUX, Avaya, Avenzo, AVH, Avvio, Awow, Axioo, AXXA, Axxion, AYYA, Azumi Mobile, b2m, Backcell, BAFF, BangOlufsen, Barnes & Noble, BARTEC, BBK, BB Mobile, BDF, BDQ, BDsharing, Beafon, Becker, Beeline, Beelink, Beetel, Beista, Bellphone, Benco, Benesse, BenQ, BenQ-Siemens, BenWee, Benzo, Beyond, Bezkam, BGH, Bigben, BIHEE, BilimLand, Billion, Billow, BioRugged, Bird, Bitel, Bitmore, Bittium, Bkav, Black Bear, Black Fox, Blackpcs, Blackview, Blaupunkt, Bleck, BLISS, Blloc, Blow, Blu, Bluboo, Bluebird, Bluedot, Bluegood, BlueSky, Bluewave, BluSlate, BMAX, Bmobile, BMXC, Bobarry, bogo, Bolva, Bookeen, Boost, Boway, bq, BrandCode, Brandt, BRAVE, Bravis, BrightSign, Brigmton, Brondi, BROR, BS Mobile, Bubblegum, Bundy, Bush, BuzzTV, C5 Mobile, CAGI, Camfone, Canal Digital, Canguro, Capitel, Captiva, Carbon Mobile, Carrefour, Casio, Casper, Cat, Cavion, Ceibal, Celcus, Celkon, Cell-C, Cellacom, CellAllure, Cellution, Centric, CG Mobile, CGV, Chainway, Changhong, Cherry Mobile, Chico Mobile, ChiliGreen, China Mobile, China Telecom, Chuwi, CipherLab, Citycall, Claresta, Clarmin, ClearPHONE, Clementoni, Cloud, Cloudfone, Cloudpad, Clout, CnM, Cobalt, Coby Kyros, Colors, Comio, Compal, Compaq, COMPUMAX, ComTrade Tesla, Conceptum, Concord, ConCorde, Condor, Connectce, Connex, Conquest, Contixo, Coolpad, Coopers, CORN, Cosmote, Covia, Cowon, COYOTE, CreNova, Crescent, Cricket, Crius Mea, Crony, Crosscall, Crown, Ctroniq, Cube, CUBOT, CVTE, Cwowdefu, Cyrus, D-Link, D-Tech, Daewoo, Danew, DangcapHD, Dany, DASS, Datalogic, Datamini, Datang, Datawind, Datsun, Dazen, DbPhone, Dbtel, Dcode, DEALDIG, Dell, Denali, Denver, Desay, DeWalt, DEXP, DEYI, DF, DGTEC, Dialog, Dicam, Digi, Digicel, DIGICOM, Digidragon, DIGIFORS, Digihome, Digiland, Digit4G, Digma, DIJITSU, DIMO, Dinax, DING DING, DISH, Disney, Ditecma, Diva, DiverMax, Divisat, DIXON, DL, DMM, DNS, DoCoMo, Doffler, Dolamee, Dom.ru, Doogee, Doopro, Doov, Dopod, Doppio, DORLAND, Doro, DPA, DRAGON, Dragon Touch, Dreamgate, DreamStar, DreamTab, Droxio, DSDevices, DSIC, Dtac, Dune HD, DUNNS Mobile, Durabook, Duubee, E-Boda, E-Ceros, E-tel, Eagle, Easypix, EBEN, EBEST, Echo Mobiles, ecom, ECON, ECOO, ECS, EE, EFT, EGL, Einstein, EKINOX, EKO, Eks Mobility, EKT, ELARI, Elecson, Electroneum, ELECTRONIA, Elekta, Element, Elenberg, Elephone, Elevate, Elong Mobile, Eltex, Ematic, Emporia, ENACOM, Energizer, Energy Sistem, Engel, ENIE, Enot, eNOVA, Entity, Envizen, Ephone, Epic, Epik One, Epson, Equator, Ergo, Ericsson, Ericy, Erisson, Essential, Essentielb, eSTAR, Eton, eTouch, Etuline, Eurocase, Eurostar, Evercoss, Everest, Everex, Evertek, Evolio, Evolveo, Evoo, EVPAD, EvroMedia, EWIS, EXCEED, Exmart, ExMobile, EXO, Explay, Extrem, EYU, Ezio, Ezze, F&U, F+, F2 Mobile, F150, Facebook, Facetel, Facime, Fairphone, Famoco, Famous, Fantec, FaRao Pro, Farassoo, FarEasTone, Fengxiang, FEONAL, Fero, FFF SmartLife, Figgers, FiGi, FiGO, FiiO, FILIX, FinePower, Finlux, FireFly Mobile, FISE, Fluo, Fly, FLYCAT, FMT, FNB, FNF, Fondi, Fonos, FOODO, FORME, Formuler, Forstar, Fortis, FOSSiBOT, Fourel, Four Mobile, Foxconn, FoxxD, FPT, Freetel, Frunsi, Fuego, Fujitsu, Funai, Fusion5, Future Mobile Technology, Fxtec, G-TiDE, G-Touch, Galactic, Galaxy Innovations, Gamma, Garmin-Asus, Gateway, Gazer, Geanee, Geant, Gear Mobile, Gemini, General Mobile, Genesis, GEOFOX, Geotel, Geotex, GEOZON, GFive, Gfone, Ghia, Ghong, Ghost, Gigabyte, Gigaset, Gini, Ginzzu, Gionee, GIRASOLE, Globex, Glofiish, GLONYX, GLX, GOCLEVER, Gocomma, GoGEN, GoldMaster, Gol Mobile, Goly, Gome, GoMobile, GOODTEL, Google, Goophone, Gooweel, Gplus, Gradiente, Grape, Great Asia, Gree, Green Orange, Greentel, Gresso, Gretel, GroBerwert, Grundig, Gtel, GTMEDIA, Guophone, H96, H133, Hafury, Haier, Haipai, Hamlet, Hammer, Handheld, HannSpree, HAOQIN, HAOVM, Hardkernel, Harper, Hartens, Hasee, Hathway, HDC, HeadWolf, Helio, HERO, HexaByte, Hezire, Hi, Hi-Level, Hiberg, High Q, Highscreen, HiHi, HiKing, HiMax, Hi Nova, HIPER, Hipstreet, Hisense, Hitachi, Hitech, HKPro, HLLO, Hoffmann, Hometech, Homtom, Honeywell, Hoozo, Horizon, Horizont, Hosin, Hotel, Hot Pepper, HOTREALS, Hotwav, How, HP, HTC, Huadoo, Huagan, Huavi, Huawei, Hugerock, Humax, Hurricane, Huskee, Hykker, Hyrican, Hytera, Hyundai, Hyve, i-Cherry, I-INN, i-Joy, i-mate, i-mobile, iBall, iBerry, ibowin, iBrit, IconBIT, iData, iDino, iDroid, iGet, iHunt, Ikea, IKI Mobile, iKoMo, iKon, IKU Mobile, iLA, iLepo, iLife, iMan, iMars, iMI, IMO Mobile, Imose, Impression, iMuz, iNavi, INCAR, Inch, Inco, iNew, Infiniton, Infinix, InFocus, InfoKit, InFone, Inhon, Inkti, InnJoo, Innos, Innostream, Inoi, iNo Mobile, iNOVA, INQ, Insignia, INSYS, Intek, Intel, Intex, Invens, Inverto, Invin, iOcean, iOutdoor, iPEGTOP, iPro, iQ&T, IQM, IRA, Irbis, iReplace, Iris, iRobot, iRola, iRulu, iSafe Mobile, iStar, iSWAG, IT, iTel, iTruck, IUNI, iVA, iView, iVooMi, ivvi, iWaylink, iXTech, iYou, iZotron, JAY-Tech, Jedi, Jeka, Jesy, JFone, Jiake, Jiayu, Jinga, Jio, Jivi, JKL, Jolla, Joy, JoySurf, JPay, JREN, Jumper, Juniper Systems, Just5, JVC, JXD, K-Lite, K-Touch, Kaan, Kaiomy, Kalley, Kanji, Kapsys, Karbonn, Kata, KATV1, Kazam, Kazuna, KDDI, Kempler & Strauss, Kenbo, Keneksi, Kenxinda, Khadas, Kiano, Kingbox, Kingstar, Kingsun, KINGZONE, Kinstone, Kiowa, Kivi, Klipad, KN Mobile, Kocaso, Kodak, Kogan, Komu, Konka, Konrow, Koobee, Koolnee, Kooper, KOPO, Koridy, Koslam, Kraft, KREZ, KRIP, KRONO, Krüger&Matz, KT-Tech, KUBO, KuGou, Kuliao, Kult, Kumai, Kurio, Kvant, Kyocera, Kyowon, Kzen, KZG, L-Max, LAIQ, Land Rover, Landvo, Lanin, Lanix, Lark, Laurus, Lava, LCT, Leader Phone, Leagoo, Leben, LeBest, Lectrus, Ledstar, LeEco, Leelbox, Leff, Legend, Leke, LEMFO, Lemhoov, Lenco, Lenovo, Leotec, Le Pan, Lephone, Lesia, Lexand, Lexibook, LG, Liberton, Lifemaxx, Lime, Lingwin, Linnex, Linsar, Linsay, Listo, LNMBBS, Loewe, Logic, Logic Instrument, Logicom, LOKMAT, Loview, Lovme, LPX-G, LT Mobile, Lumigon, Lumitel, Lumus, Luna, Luxor, LYF, M-Horse, M-Tech, M.T.T., M3 Mobile, M4tel, MAC AUDIO, Macoox, Mafe, Magicsee, Magnus, Majestic, Malata, Mango, Manhattan, Mann, Manta Multimedia, Mantra, Mara, Marshal, Mascom, Massgo, Masstel, Master-G, Mastertech, Matrix, Maxcom, Maxfone, Maximus, Maxtron, MAXVI, Maxwest, MAXX, Maze, Maze Speed, MBI, MBOX, MDC Store, MDTV, meanIT, Mecer, Mecool, Mediacom, MediaTek, Medion, MEEG, MegaFon, Meitu, Meizu, Melrose, Memup, Meta, Metz, MEU, MicroMax, Microsoft, Microtech, Minix, Mint, Mintt, Mio, Mione, Miray, Mito, Mitsubishi, Mitsui, MIVO, MIWANG, MIXC, MiXzo, MLAB, MLLED, MLS, MMI, Mobell, Mobicel, MobiIoT, Mobiistar, Mobile Kingdom, Mobiola, Mobistel, MobiWire, Mobo, Mobvoi, Modecom, Mofut, Mosimosi, Motiv, Motorola, Movic, MOVISUN, Movitel, Moxee, mPhone, Mpman, MSI, MStar, MTC, MTN, Multilaser, MultiPOS, MwalimuPlus, MYFON, MyGica, MygPad, Mymaga, MyMobile, MyPhone, Myria, Myros, Mystery, MyTab, MyWigo, Nabi, Nanho, Naomi Phone, NASCO, National, Navcity, Navitech, Navitel, Navon, NavRoad, NEC, Necnot, Nedaphone, Neffos, NEKO, Neo, neoCore, Neolix, Neomi, Neon IQ, Netgear, Netmak, NeuImage, NeuTab, New Balance, New Bridge, Newgen, Newland, Newman, Newsday, NewsMy, Nexa, NEXBOX, Nexian, NEXON, NEXT, Nextbit, NextBook, NextTab, NGM, NG Optics, NGpon, Nikon, NINETEC, Nintendo, nJoy, NOA, Noain, Nobby, Noblex, NOBUX, noDROPOUT, NOGA, Nokia, Nomi, Nomu, Noontec, Nordmende, NorthTech, Nos, Nothing Phone, Nous, Novex, Novey, NOVO, NTT West, NuAns, Nubia, NUU Mobile, NuVision, Nuvo, Nvidia, NYX Mobile, O+, O2, Oale, Oangcc, OASYS, Obabox, Ober, Obi, Odotpad, Odys, OINOM, Ok, Okapia, Oking, OKSI, OKWU, Olax, Olkya, Ollee, OLTO, Olympia, OMIX, Onda, OneClick, OneLern, OnePlus, Onix, Onkyo, ONN, ONYX BOOX, Ookee, OpelMobile, Openbox, Ophone, OPPO, Opsson, Optoma, Orange, Orbic, Orbita, Orbsmart, Ordissimo, Orion, OSCAL, OTTO, OUJIA, Ouki, Oukitel, OUYA, Overmax, Ovvi, Owwo, OYSIN, Oysters, Oyyu, OzoneHD, P-UP, Packard Bell, Paladin, Palm, Panacom, Panasonic, Pano, Panoramic, Pantech, PAPYRE, Parrot Mobile, Partner Mobile, PCBOX, PCD, PCD Argentina, PC Smart, PEAQ, Pelitt, Pendoo, Pentagram, Perfeo, Phicomm, Philco, Philips, Phonemax, phoneOne, Pico, PINE, Pioneer, Pioneer Computers, PiPO, PIRANHA, Pixela, Pixelphone, Pixus, Planet Computers, Platoon, Ployer, Plum, PlusStyle, Pluzz, PocketBook, POCO, Point Mobile, Point of View, Polar, PolarLine, Polaroid, Polestar, PolyPad, Polytron, Pomp, Poppox, POPTEL, Porsche, Positivo, Positivo BGH, PPTV, Premier, Premio, Prestigio, PRIME, Primepad, Primux, Pritom, Prixton, PROFiLO, Proline, Prology, ProScan, Protruly, ProVision, PULID, Punos, Purism, Q-Box, Q-Touch, Q.Bell, QFX, Qilive, QLink, QMobile, Qnet Mobile, QTECH, Qtek, Quantum, Quatro, Qubo, Quechua, Quest, Quipus, Qumo, Qware, R-TV, Rakuten, Ramos, Raspberry, Ravoz, Raylandz, Razer, RCA Tablets, Reach, Readboy, Realme, RED, Redbean, Redfox, RedLine, Redway, Reeder, REGAL, RelNAT, Remdun, Retroid Pocket, Revo, Revomovil, Ricoh, Rikomagic, RIM, Rinno, Ritmix, Ritzviva, Riviera, Rivo, Rizzen, ROADMAX, Roadrover, Roam Cat, ROiK, Rokit, Roku, Rombica, Ross&Moor, Rover, RoverPad, Royole, RoyQueen, RT Project, RugGear, RuggeTech, Ruggex, Ruio, Runbo, Rupa, Ryte, S-TELL, S2Tel, Saba, Safaricom, Sagem, Saiet, Salora, Samsung, Samtech, Samtron, Sanei, Sankey, Sansui, Santin, SANY, Sanyo, Savio, Sber, SCBC, Schneider, Schok, Scosmos, Seatel, SEBBE, Seeken, SEEWO, SEG, Sega, Selecline, Selenga, Selevision, Selfix, SEMP TCL, Sencor, Sendo, Senkatel, Senseit, Senwa, Seuic, Sewoo, SFR, SGIN, Shanling, Sharp, Shift Phones, Shivaki, Shtrikh-M, Shuttle, Sico, Siemens, Sigma, Silelis, Silent Circle, Simbans, Simply, Singtech, Siragon, Sirin Labs, SK Broadband, SKG, SKK Mobile, Sky, Skyline, SkyStream, Skyworth, Smadl, Smailo, Smart, Smartab, SmartBook, SMARTEC, Smart Electronic, Smartex, Smartfren, Smartisan, Smart Kassel, Smarty, Smooth Mobile, Smotreshka, SNAMI, SobieTech, Soda, Softbank, Soho Style, SOLE, SOLO, Solone, Sonim, SONOS, Sony, Sony Ericsson, SOSH, Soundmax, Soyes, Spark, SPC, Spectralink, Spectrum, Spice, Sprint, SQOOL, SSKY, Star, Starlight, Starmobile, Starway, Starwind, STF Mobile, STG Telecom, STK, Stonex, Storex, StrawBerry, Stream, STRONG, Stylo, Subor, Sugar, Sumvision, Sunmax, Sunmi, Sunny, Sunstech, SunVan, Sunvell, SUNWIND, SuperBOX, SuperSonic, SuperTab, Supra, Supraim, Surge, Suzuki, Sveon, Swipe, SWISSMOBILITY, Swisstone, Switel, SWTV, Syco, SYH, Sylvania, Symphony, Syrox, T-Mobile, T96, TAG Tech, Taiga System, Takara, Talius, Tambo, Tanix, TB Touch, TCL, TD Systems, TD Tech, TeachTouch, Technicolor, Technika, TechniSat, Technopc, TechnoTrend, TechPad, TechSmart, Techwood, Teclast, Tecno Mobile, TecToy, TEENO, Teknosa, Tele2, Telefunken, Telego, Telenor, Telia, Telit, Telma, TeloSystems, Telpo, TENPLUS, Teracube, Tesco, Tesla, TETC, Tetratab, teXet, ThL, Thomson, Thuraya, TIANYU, Tibuta, Tigers, Time2, Timovi, TIMvision, Tinai, Tinmo, TiPhone, TiVo, TJC, TOKYO, Tolino, Tone, TOOGO, Tooky, TopDevice, TOPDON, Topelotek, Top House, Toplux, TOPSHOWS, Topsion, Topway, Torex, Torque, TOSCIDO, Toshiba, Touchmate, Touch Plus, TOX, TPS, Transpeed, TrekStor, Trevi, Trident, Trifone, Trio, Tronsmart, True, True Slim, TTEC, TTK-TV, TuCEL, Tunisie Telecom, Turbo, Turbo-X, TurboKids, TurboPad, Turkcell, TVC, TWM, Twoe, TWZ, Tymes, Türk Telekom, U-Magic, U.S. Cellular, UE, Ugoos, Uhans, Uhappy, Ulefone, Umax, UMIDIGI, Unblock Tech, Uniden, Unihertz, Unimax, Uniqcell, Uniscope, Unistrong, Unitech, UNIWA, Unknown, Unnecto, Unnion Technologies, UNNO, Unonu, Unowhy, UOOGOU, Urovo, UTime, UTOK, UTStarcom, UZ Mobile, V-Gen, V-HOME, V-HOPE, v-mobile, VAIO, VALEM, VALTECH, Vankyo, Vargo, Vastking, VAVA, VC, VDVD, Vega, Vekta, Venso, Venstar, Venturer, VEON, Verico, Verizon, Vernee, Verssed, Versus, Vertex, Vertu, Verykool, Vesta, Vestel, VETAS, Vexia, VGO TEL, ViBox, Victurio, VIDA, Videocon, Videoweb, ViewSonic, VIIPOO, Vinabox, Vinga, Vinsoc, Vios, Viper, Vipro, Virzo, Vision Touch, Visual Land, Vitelcom, Vityaz, Viumee, Vivax, VIVIMAGE, Vivo, VIWA, Vizio, Vizmo, VK Mobile, VKworld, Vodacom, Vodafone, VOGA, VOLKANO, Volt, Vonino, Vontar, Vorago, Vorcom, Vorke, Vormor, Vortex, Voto, VOX, Voxtel, Voyo, Vsmart, Vsun, VUCATIMES, Vue Micro, Vulcan, VVETIME, Völfen, WAF, Walton, Waltter, Wanmukang, WANSA, WE, Webfleet, Web TV, Wecool, Weelikeit, Weimei, WellcoM, WELLINGTON, Western Digital, Westpoint, Wexler, White Mobile, Wieppo, Wigor, Wiko, Wileyfox, Winds, Wink, Winmax, Winnovo, Winstar, Wintouch, Wiseasy, WIWA, WizarPos, Wizz, Wolder, Wolfgang, Wolki, Wonu, Woo, Wortmann, Woxter, X-AGE, X-BO, X-Mobile, X-TIGI, X-View, X.Vision, X88, X96, X96Q, Xcell, XCOM, Xcruiser, XElectron, XGIMI, Xgody, Xiaodu, Xiaolajiao, Xiaomi, Xion, Xolo, Xoro, Xshitou, Xtouch, Xtratech, Xwave, XY Auto, Yandex, Yarvik, YASIN, YELLYOUTH, YEPEN, Yes, Yestel, Yezz, Yoka TV, Yooz, Yota, YOTOPT, Youin, Youwei, Ytone, Yu, Yuandao, YU Fly, YUHO, YUMKEM, YUNDOO, Yuno, YunSong, Yusun, Yxtel, Zaith, Zamolxe, Zatec, Zealot, Zeblaze, Zebra, Zeeker, Zeemi, Zen, Zenek, Zentality, Zfiner, ZH&K, Zidoo, ZIFRO, Zigo, ZIK, Zinox, Ziox, Zonda, Zonko, Zoom, ZoomSmart, Zopo, ZTE, Zuum, Zync, ZYQ, Zyrex, öwn

### List of detected bots:

2ip, 360 Monitoring, 360Spider, Abonti, Aboundexbot, Acoon, AdAuth, Adbeat, AddThis.com, ADMantX, ADmantX Service Fetcher, Adsbot, AdsTxtCrawler, adstxtlab.com, aHrefs Bot, AhrefsSiteAudit, aiHitBot, Alexa Crawler, Alexa Site Audit, Allloadin Favicon Bot, Amazon Bot, Amazon ELB, Amazon Route53 Health Check, Amorank Spider, Analytics SEO Crawler, Ant, ApacheBench, Applebot, AppSignalBot, Arachni, archive.org bot, ArchiveBox, Asana, Ask Jeeves, AspiegelBot, Awario, Backlink-Check.de, BacklinkCrawler, Baidu Spider, Barkrowler, BazQux Reader, BDCbot, Better Uptime Bot, BingBot, Birdcrawlerbot, BitlyBot, BitSight, Blekkobot, BLEXBot Crawler, Bloglovin, Blogtrottr, BoardReader, BoardReader Blog Indexer, Bountii Bot, BrandVerity, BrightEdge, Browsershots, BUbiNG, Buck, BuiltWith, Butterfly Robot, Bytespider, CareerBot, Castro 2, Catchpoint, CATExplorador, ccBot crawler, CensysInspect, Charlotte, ChatGPT, Choosito, Chrome Privacy Preserving Prefetch Proxy, Cincraw, CISPA Web Analyzer, Cliqzbot, CloudFlare Always Online, CloudFlare AMP Fetcher, Cloudflare Diagnostics, Cloudflare Health Checks, Cocolyzebot, Collectd, colly, CommaFeed, COMODO DCV, Comscore, ContentKing, Cookiebot, Crawldad, Crawlson, CriteoBot, CrowdTangle, CSS Certificate Spider, Cyberscan, Cốc Cốc Bot, Datadog Agent, DataForSeoBot, datagnionbot, Datanyze, Dataprovider, DataXu, Daum, Dazoobot, deepnoc, Diffbot, Discobot, Discord Bot, Disqus, DNSResearchBot, DomainAppender, DomainCrawler, Domain Re-Animator Bot, Domains Project, DomainStatsBot, DotBot, Dotcom Monitor, DuckDuckGo Bot, Easou Spider, eCairn-Grabber, EFF Do Not Track Verifier, EMail Exractor, EmailWolf, Embedly, Entfer, evc-batch, Everyfeed, ExaBot, ExactSeek Crawler, Exchange check, Expanse, Ezgif, Ezooms, eZ Publish Link Validator, Facebook External Hit, Faveeo, Feedbin, FeedBurner, Feedly, Feedspot, Feed Wrangler, Femtosearch, Fever, Findxbot, Flipboard, FreeWebMonitoring, FreshRSS, GDNP, Generic Bot, Genieo Web filter, Gigablast, Gigabot, GitCrawlerBot, Gluten Free Crawler, Gmail Image Proxy, Gobuster, Goo, Googlebot, Google Cloud Scheduler, Google Favicon, Google PageSpeed Insights, Google Partner Monitoring, Google Search Console, Google Stackdriver Monitoring, Google StoreBot, Google Structured Data Testing Tool, Gowikibot, GPTBot, Grammarly, Grapeshot, Gregarius, GTmetrix, GumGum Verity, hackermention, Hatena Bookmark, Hatena Favicon, Headline, Heart Rails Capture, Heritrix, Heureka Feed, HTTPMon, httpx, HuaweiWebCatBot, HubPages, HubSpot, ICC-Crawler, ichiro, IDG/IT, Iframely, IIS Site Analysis, Inetdex Bot, Infegy, InfoTigerBot, Inktomi Slurp, inoreader, Intelligence X, InternetMeasurement, IONOS Crawler, IP-Guide Crawler, IPIP, IPS Agent, JobboerseBot, JungleKeyThumbnail, K6, Kaspersky, KlarnaBot, KomodiaBot, Kouio, Kozmonavt, l9explore, l9tcpid, Larbin web crawler, LastMod Bot, LCC, LeakIX, Let's Encrypt Validation, Lighthouse, Linespider, Linkdex Bot, LinkedIn Bot, LinkpadBot, LinkPreview, LinkWalker, LTX71, Lumar, LumtelBot, Lycos, MaCoCu, Magpie-Crawler, MagpieRSS, Mail.Ru Bot, masscan, masscan-ng, Mastodon Bot, Meanpath Bot, Mediatoolkit Bot, MegaIndex, MetaInspector, MetaJobBot, MicroAdBot, Mixrank Bot, MJ12 Bot, Mnogosearch, MojeekBot, Monitor.Us, MoodleBot Linkchecker, Morningscore Bot, MTRobot, Munin, MuscatFerret, Nagios check_http, NalezenCzBot, nbertaupete95, Neevabot, Netcraft Survey Bot, netEstate, NetLyzer FastProbe, NetResearchServer, NetSystemsResearch, Netvibes, NETZZAPPEN, NewsBlur, NewsGator, Newslitbot, NiceCrawler, Nimbostratus Bot, NLCrawler, Nmap, Notify Ninja, Nutch-based Bot, Nuzzel, oBot, Octopus, Odnoklassniki Bot, Omgili bot, Onalytica, OnlineOrNot Bot, Openindex Spider, OpenLinkProfiler, OpenWebSpider, Orange Bot, Outbrain, Overcast Podcast Sync, Pageburst, Page Modified Pinger, PagePeeker, PageThing, Panscient, PaperLiBot, parse.ly, PATHspider, PayPal IPN, PDR Labs, Petal Bot, Phantomas, PHP Server Monitor, Picsearch bot, PingAdmin.Ru, Pingdom Bot, Pinterest, PiplBot, Plukkie, Pocket, Pompos, PritTorrent, Project Patchwatch, Project Resonance, PRTG Network Monitor, QuerySeekerSpider, Quora Bot, Quora Link Preview, Qwantify, Rainmeter, RamblerMail Image Proxy, Reddit Bot, RenovateBot, Repo Lookout, ReqBin, Research Scan, Riddler, Robozilla, RocketMonitorBot, Rogerbot, ROI Hunter, RSSRadio Bot, Ryowl, SabsimBot, SafeDNSBot, Scamadviser External Hit, Scooter, ScoutJet, Scraping Robot, Scrapy, Screaming Frog SEO Spider, ScreenerBot, Sectigo DCV, security.txt scanserver, Seekport, Sellers.Guide, Semantic Scholar Bot, Semrush Bot, SEMrush Reputation Management, Sensika Bot, Sentry Bot, Seobility, SEOENGBot, SEOkicks, SEOkicks-Robot, seolyt, Seolyt Bot, Seoscanners.net, Serendeputy Bot, serpstatbot, Server Density, Seznam Bot, Seznam Email Proxy, Seznam Zbozi.cz, sfFeedReader, ShopAlike, Shopify Partner, ShopWiki, SilverReader, SimplePie, SISTRIX Crawler, SISTRIX Optimizer, Site24x7 Website Monitoring, Siteimprove, SitemapParser-VIPnytt, SiteSucker, Sixy.ch, Skype URI Preview, Slackbot, SMTBot, Snapchat Proxy, Snap URL Preview Service, Sogou Spider, Soso Spider, Sparkler, Speedy, Spinn3r, Spotify, Sprinklr, Sputnik Bot, Sputnik Favicon Bot, Sputnik Image Bot, sqlmap, SSL Labs, start.me, Startpagina Linkchecker, StatusCake, Sublinq, Superfeedr Bot, SurdotlyBot, Survey Bot, t3versions, Taboolabot, Tag Inspector, Tarmot Gezgin, tchelebi, TelegramBot, TestCrawler, The Knowledge AI, theoldreader, ThinkChaos, TigerBot, TinEye Crawler, Tiny Tiny RSS, TLSProbe, TraceMyFile, Trendiction Bot, Turnitin, TurnitinBot, TweetedTimes Bot, Tweetmeme Bot, Twingly Recon, Twitterbot, UkrNet Mail Proxy, uMBot, UniversalFeedParser, Uptime-Kuma, Uptimebot, Uptime Robot, URLAppendBot, URLinspector, Vagabondo, Velen Public Web Crawler, Vercel Bot, VeryHip, Visual Site Mapper Crawler, VK Share Button, Vuhuv Bot, W3C CSS Validator, W3C I18N Checker, W3C Link Checker, W3C Markup Validation Service, W3C MobileOK Checker, W3C Unified Validator, Wappalyzer, WebbCrawler, WebDataStats, Weborama, WebPageTest, WebPros, WebSitePulse, WebThumbnail, Webwiki, WellKnownBot, WeSEE:Search, WeViKaBot, WhatCMS, WhereGoes, WikiDo, Willow Internet Crawler, WooRank, WordPress, Wotbox, XenForo, XoviBot, YaCy, Yahoo! Cache System, Yahoo! Japan BRW, Yahoo! Japan WSC, Yahoo! Link Preview, Yahoo! Mail Proxy, Yahoo! Slurp, Yahoo Gemini, YaK, Yandex Bot, Yeti/Naverbot, Yottaa Site Monitor, Youdao Bot, Yourls, Yunyun Bot, Zaldamo, Zao, Ze List, zgrab, Zookabot, ZoominfoBot, ZumBot
