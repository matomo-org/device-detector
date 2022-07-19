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

*Last update: 2022/07/17*

### List of detected operating systems:

AIX, Android, Android TV, Amazon Linux, AmigaOS, tvOS, Arch Linux, BackTrack, Bada, BeOS, BlackBerry OS, BlackBerry Tablet OS, Bliss OS, Brew, Caixa Mágica, CentOS, CentOS Stream, ClearOS Mobile, Chrome OS, Chromium OS, China OS, CyanogenMod, Debian, Deepin, DragonFly, DVKBuntu, Fedora, Fenix, Firefox OS, Fire OS, Foresight Linux, Freebox, FreeBSD, FydeOS, Fuchsia, Gentoo, GridOS, Google TV, HP-UX, Haiku OS, iPadOS, HarmonyOS, HasCodingOS, IRIX, Inferno, Java ME, KaiOS, Kanotix, Knoppix, KreaTV, Kubuntu, GNU/Linux, LindowsOS, Linspire, Lineage OS, Lubuntu, Lumin OS, VectorLinux, Mac, Maemo, Mageia, Mandriva, MeeGo, MocorDroid, moonOS, Mint, MildWild, MorphOS, NetBSD, MTK / Nucleus, MRE, Nintendo, Nintendo Mobile, Nova, OS/2, OSF1, OpenBSD, OpenWrt, Opera TV, Ordissimo, Pardus, PCLinuxOS, Plasma Mobile, PlayStation Portable, PlayStation, PureOS, Red Hat, Revenge OS, RISC OS, Roku OS, Rosa, Remix OS, REX, RazoDroiD, Sabayon, SUSE, Sailfish OS, SeewoOS, Sirin OS, Slackware, Solaris, Syllable, Symbian, Symbian OS, Symbian OS Series 40, Symbian OS Series 60, Symbian^3, TencentOS, ThreadX, Tizen, TmaxOS, Ubuntu, watchOS, WebTV, Whale OS, Windows, Windows CE, Windows IoT, Windows Mobile, Windows Phone, Windows RT, Xbox, Xubuntu, YunOS, Zenwalk, iOS, palmOS, webOS

### List of detected browsers:

Via, Pure Mini Browser, Pure Lite Browser, Raise Fast Browser, Rabbit Private Browser, Fast Browser UC Lite, Fast Explorer, Lightning Browser, Cake Browser, IE Browser Fast, Vegas Browser, OH Browser, OH Private Browser, XBrowser Mini, Sharkee Browser, Lark Browser, Pluma, Anka Browser, Azka Browser, Dragon Browser, Easy Browser, Dark Web Browser, 115 Browser, 2345 Browser, 360 Phone Browser, 360 Browser, 7654 Browser, Avant Browser, ABrowse, AdBlock Browser, ANT Fresco, ANTGalio, Aloha Browser, Aloha Browser Lite, Amaya, Amaze Browser, Amigo, Android Browser, AOL Desktop, AOL Shield, AOL Shield Pro, APUS Browser, Arora, Arctic Fox, Amiga Voyager, Amiga Aweb, Arvin, Ask.com, Asus Browser, Atom, Atomic Web Browser, Atlas, Avast Secure Browser, AVG Secure Browser, Avira Scout, AwoX, Beaker Browser, Beamrise, BlackBerry Browser, BrowseHere, Baidu Browser, Baidu Spark, Basilisk, Belva Browser, Beonex, Berry Browser, Bitchute Browser, BlackHawk, Bloket, Bunjalloo, B-Line, Blue Browser, Bonsai, Borealis Navigator, Brave, BriskBard, Browspeed Browser, BrowseX, Browzar, Biyubi, Byffox, BF Browser, Camino, CCleaner, CG Browser, ChanjetCloud, Chedot, Cherry Browser, Centaury, Coc Coc, CoolBrowser, Colibri, Comodo Dragon, Coast, Charon, CM Browser, CM Mini, Chrome Frame, Headless Chrome, Chrome, Chrome Mobile iOS, Conkeror, Chrome Mobile, Chowbo, CoolNovo, CometBird, Comfort Browser, COS Browser, Cornowser, Chim Lac, ChromePlus, Chromium, Chromium GOST, Cyberfox, Cheshire, Crusta, Craving Explorer, Crazy Browser, Cunaguaro, Chrome Webview, CyBrowser, dbrowser, Peeps dBrowser, Debuggable Browser, Decentr, Deepnet Explorer, deg-degan, Deledao, Delta Browser, Desi Browser, DeskBrowse, Dolphin, Dolphin Zero, Dorado, Dot Browser, Dooble, Dillo, DUC Browser, DuckDuckGo Privacy Browser, Ecosia, Edge WebView, Epic, Elinks, EinkBro, Element Browser, Elements Browser, eZ Browser, EUI Browser, GNOME Web, G Browser, Espial TV Browser, Falkon, Faux Browser, Firefox Mobile iOS, Firebird, Fluid, Fennec, Firefox, Firefox Focus, Firefox Reality, Firefox Rocket, Firefox Klar, Float Browser, Flock, Floorp, Flow, Flow Browser, Firefox Mobile, Fireweb, Fireweb Navigator, Flash Browser, Flast, FreeU, Frost+, Fulldive, Galeon, Gener8, Ghostery Privacy Browser, GinxDroid Browser, Glass Browser, Google Earth, Google Earth Pro, GOG Galaxy, GoBrowser, Harman Browser, HasBrowser, Hawk Turbo Browser, Hawk Quick Browser, Helio, Hexa Web Browser, Hi Browser, hola! Browser, HotJava, Huawei Browser Mobile, Huawei Browser, HUB Browser, iBrowser, iBrowser Mini, IBrowse, iDesktop PC Browser, iCab, iCab Mobile, Iridium, Iron Mobile, IceCat, IceDragon, Isivioo, Iceweasel, Inspect Browser, Internet Explorer, Internet Browser Secure, Indian UC Mini Browser, IE Mobile, Iron, Japan Browser, Jasmine, JavaFX, Jelly, Jig Browser, Jig Browser Plus, Jio Browser, JioPages, K.Browser, Keepsafe Browser, Kids Safe Browser, Kindle Browser, K-meleon, Konqueror, Kapiko, Kinza, Kiwi, Kode Browser, KUTO Mini Browser, Kylo, Kazehakase, Cheetah Browser, Lagatos Browser, Lexi Browser, Lenovo Browser, LieBaoFast, LG Browser, Light, Lilo, Links, Lolifox, Lovense Browser, LT Browser, LuaKit, Lulumi, Lunascape, Lunascape Lite, Lynx, Lynket Browser, Mandarin, mCent, MicroB, NCSA Mosaic, Meizu Browser, Mercury, Me Browser, Mobile Safari, Midori, Midori Lite, Mobicip, MIUI Browser, Mobile Silk, Minimo, Mint Browser, Maxthon, MaxTube Browser, Maelstrom, Mmx Browser, MxNitro, Mypal, Monument Browser, MAUI WAP Browser, Navegador, Navigateur Web, Naked Browser, NFS Browser, Nokia Browser, Nokia OSS Browser, Nokia Ovi Browser, Nox Browser, NetSurf, NetFront, NetFront Life, NetPositive, Netscape, NTENT Browser, Oculus Browser, Opera Mini iOS, Obigo, Odin, Odin Browser, OceanHero, Odyssey Web Browser, Off By One, Office Browser, OhHai Browser, ONE Browser, Opera Crypto, Opera GX, Opera Neon, Opera Devices, Opera Mini, Opera Mobile, Opera, Opera Next, Opera Touch, Orca, Ordissimo, Oregano, Origin In-Game Overlay, Origyn Web Browser, Openwave Mobile Browser, OpenFin, Open Browser, Open Browser 4U, OmniWeb, Otter Browser, Palm Blazer, Pale Moon, Polypane, Oppo Browser, Palm Pre, Puffin, Puffin Web Browser, Palm WebPro, Palmscape, Perfect Browser, Phantom.me, Phantom Browser, Phoenix, Phoenix Browser, PlayFree Browser, PocketBook Browser, Polaris, Polarity, PolyBrowser, PrivacyWall, Pi Browser, PronHub Browser, PSI Secure Browser, Reqwireless WebViewer, Microsoft Edge, Qazweb, QQ Browser Lite, QQ Browser Mini, QQ Browser, Quick Browser, Qutebrowser, Quark, QupZilla, Qwant Mobile, QtWebEngine, Realme Browser, Rekonq, RockMelt, Samsung Browser, Sailfish Browser, Seewo Browser, SEMC-Browser, Sogou Explorer, Sogou Mobile Browser, SOTI Surf, Soul Browser, Safari, Safari Technology Preview, Safe Exam Browser, SalamWeb, Savannah Browser, SavySoda, Secure Browser, SFive, Shiira, Sidekick, SimpleBrowser, SilverMob US, Sizzy, Skyfire, Seraphic Sraf, SiteKiosk, Sleipnir, Slimjet, SP Browser, Secure Private Browser, Stampy Browser, 7Star, Smart Browser, Smart Search & Web Browser, Smart Lenovo Browser, Smooz, Snowshoe, Spectre Browser, Splash, Sputnik Browser, Sunrise, SuperBird, Super Fast Browser, SuperFast Browser, Sushi Browser, surf, Surf Browser, Stargon, START Internet Browser, Steam In-Game Overlay, Streamy, Swiftfox, Seznam Browser, T+Browser, T-Browser, t-online.de Browser, Tao Browser, TenFourFox, Tenta Browser, Tesla Browser, Tizen Browser, Tungsten, ToGate, TweakStyle, TV Bro, U Browser, UBrowser, UC Browser, UC Browser HD, UC Browser Mini, UC Browser Turbo, Ui Browser Mini, UR Browser, Uzbl, Ume Browser, vBrowser, Vast Browser, Venus Browser, Nova Video Downloader Pro, Viasat Browser, Vivaldi, vivo Browser, Vivid Browser Mini, Vision Mobile Browser, VMware AirWatch, Wear Internet Browser, Web Explorer, WebPositive, Waterfox, Wave Browser, Whale Browser, wOSBrowser, WeTab Browser, Yahoo! Japan Browser, Yandex Browser, Yandex Browser Lite, Yaani Browser, Yo Browser, Yolo Browser, YouCare, Yuzu Browser, X-VPN, xStand, Xiino, Xooloo Internet, Xvast, Zetakey, Zvu

### List of detected browser engines:

WebKit, Blink, Trident, Text-based, Dillo, iCab, Elektra, Presto, Gecko, KHTML, NetFront, Edge, NetSurf, Servo, Goanna, EkiohFlow

### List of detected libraries:

aiohttp, Akka HTTP, AnyEvent HTTP, Apache HTTP Client, Aria2, C++ REST SDK, cPanel HTTP Client, curl, Embarcadero URI Client, Faraday, fasthttp, GeoIP Update, Go-http-client, Google HTTP Java Client, GRequests, gRPC-Java, Guzzle (PHP HTTP Client), gvfs, hackney, HTTPie, httplib2, HTTPX, HTTP_Request2, Jakarta Commons HttpClient, Java, Java HTTP Client, jsdom, libdnf, LUA OpenResty NGINX, Mechanize, Mikrotik Fetch, Node Fetch, OkHttp, Pa11y, Perl, Perl REST::Client, PHP cURL Class, Postman Desktop, Python Requests, Python urllib, ReactorNetty, REST Client for Ruby, RestSharp, Resty, ScalaJ HTTP, SlimerJS, uclient-fetch, Ultimate Sitemap Parser, Unirest for Java, urlgrabber (yum), uTorrent, Wget, Windows HTTP, WinHttp WinHttpRequest, WWW-Mechanize

### List of detected media players:

Audacious, Banshee, Boxee, Clementine, Deezer, Downcast, FlyCast, Foobar2000, foobar2000, Google Podcasts, HTC Streaming Player, iTunes, Kodi, MediaMonkey, Miro, MPlayer, mpv, Music Player Daemon, NexPlayer, Nightingale, QuickTime, Songbird, SONOS, Sony Media Go, Stagefright, SubStream, VLC, Winamp, Windows Media Player, XBMC

### List of detected mobile apps:

1Password, 2tch, Adobe Creative Cloud, Adobe IPM, Adobe NGL, Adobe Synchronizer, Aha Radio 2, AIDA64, Alexa Media Player, AliExpress, Amazon Shopping, AndroidDownloadManager, AntennaPod, AntiBrowserSpy, Apple News, ASUS Updater, Avid Link, Background Intelligent Transfer Service, Baidu Box App, Ballz, Bank Millenium, Battle.net, BB2C, BBC News, Be Focused, BetBull, BeyondPod, Bible KJV, Binance, Bing iPad, BingWebApp, Bitcoin Core, Bitsboard, Blackboard, Blitz, Blue Proxy, BlueStacks, Bookshelf, Bose Music, bPod, CastBox, Castro, Castro 2, CCleaner, CGN, ChMate, Chrome Update, Ciisaa, Citrix Workspace, Clovia, COAF SMART Citizen, Copied, Cortana, Covenant Eyes, CPU-Z, CrosswalkApp, DevCasts, DeviantArt, DingTalk, Discord, DoggCatcher, Don't Waste My Time!, douban App, Downcast, Dr. Watson, DStream Air, Edge Update, Emby Theater, Epic Games Launcher, ESET Remote Administrator, eToro, Evolve Podcast, Expedia, F-Secure SAFE, Facebook, Facebook Audience Network, Facebook Groups, Facebook Lite, Facebook Messenger, Facebook Messenger Lite, FeedR, Flipboard App, Flipp, Focus Keeper, Focus Matrix, Gaana, Git, GitHub Desktop, GlobalProtect, GoNative, Google Drive, Google Fiber TV, Google Go, Google Photos, Google Play Newsstand, Google Plus, Google Podcasts, Google Search App, Google Tag Manager, GroupMe, Hago, HandBrake, HeyTapBrowser, Hik-Connect, HiSearch, HisThumbnail, HP Smart, iCatcher, IMO HD Video Calls & Chat, IMO International Calls & Chat, Instabridge, Instacast, Instagram App, Instapaper, JaneStyle, Jitsi Meet, Jungle Disk, KakaoTalk, Keeper Password Manager, Kik, Landis+Gyr AIM Browser, Line, LinkedIn, Logi Options+, Macrium Reflect, MBolsa, MEmpresas, Mercantile Bank of Michigan, Meta Business Suite, MetaTrader, Microsoft Bing Search, Microsoft Office $1, Microsoft Office Mobile, Microsoft OneDrive, Microsoft Start, Microsoft Store, mobile.de, My Bentley, My World, Naver, NET.mede, Netflix, NewsArticle App, Nextcloud, NPR One, NTV Mobil, NuMuKi Browser, Odnoklassniki, OfferUp, Opal Travel, Opera News, Opera Updater, Orange Radio, Overcast, Paint by Number, Pandora, Papers, Petal Search App, Pic Collage, Pinterest, Player FM, Plex Media Server, Pocket Casts, Podbean, Podcast & Radio Addict, Podcaster, Podcast Republic, Podcasts, Podcat, Podcatcher Deluxe, Podimo, Podkicker$1, PowerShell, Procast, Q-municate, qBittorrent, QQMusic, QuickCast, Quick Search TV, Radio Italiane, RadioPublic, Rave Social, Razer Synapse, RDDocuments, rekordbox, RNPS Action Cards, Roblox, RoboForm, Rocket Chat, RSSRadio, Safari Search Helper, SafeIP, Samsung Magician, Shopee, ShowMe, Sina Weibo, Siri, Skyeng, Skyeng Teachers, Skype, Skype for Business, Slack, Snapchat, SogouSearch App, Soldier, SPORT1, Startsiden, Streamlabs OBS, Strimio, Surfshark, Swoot, Teams, The Wall Street Journal, Theyub, Thunder, tieba, TikTok, TopBuzz, TradingView, TuneIn Radio, TuneIn Radio Pro, TVirl, twinkle, Twitter, Twitterrific, U-Cursos, Uconnect LIVE, Unibox, UnityPlayer, Viber, Visual Studio Code, Vuhuv, Vuze, Wattpad, Wayback Machine, WebDAV, WeChat, WeChat Share Extension, WhatsApp, Whisper, WH Questions, Windows Antivirus, Windows CryptoAPI, Windows Delivery Optimization, Windows Push Notification Services, Windows Update Agent, Wireshark, Wirtschafts Woche, Word Cookies!, WPS Office, Y8 Browser, Yahoo! Japan, Yahoo OneSearch, YakYak, Yandex, Yelp Mobile, YouTube, Zalo, ZEPETO, Zoho Chat and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

Airmail, Barca, Basecamp, BathyScaphe, DAVdroid, eM Client, Evernote, Franz, JaneView, Live5ch, Lotus Notes, MailBar, Mailbird, Mailspring, Microsoft Outlook, NAVER Mail, Notion, Outlook Express, Postbox, Raindrop.io, Rambox Pro, SeaMonkey, The Bat!, Thunderbird, Windows Mail, Yahoo Mail

### List of detected feed readers:

Akregator, Apple PubSub, BashPodder, Breaker, FeedDemon, Feeddler RSS Reader, gPodder, JetBrains Omea Reader, Liferea, NetNewsWire, Newsbeuter, NewsBlur, NewsBlur Mobile App, PritTorrent, Pulp, QuiteRSS, ReadKit, Reeder, RSS Bandit, RSS Junkie, RSSOwl, Stringer

### List of brands with detected devices:

2E, 3GNET, 3GO, 3Q, 4Good, 4ife, 7 Mobile, 360, 8848, A1, Accent, Ace, Acer, Acteck, actiMirror, Adronix, Advan, Advance, AFFIX, AfriOne, AGM, AG Mobile, AIDATA, Ainol, Airness, AIRON, Airtel, Airties, AIS, Aiuto, Aiwa, Akai, AKIRA, Alba, Alcatel, Alcor, ALDI NORD, ALDI SÜD, Alfawise, Aligator, AllCall, AllDocube, Allview, Allwinner, Alps, Altech UEC, Altice, altron, Amazon, AMCV, AMGOO, Amigoo, Amino, Amoi, Andowl, Angelcare, Anker, Anry, ANS, AOC, Aocos, AOpen, Aoro, Aoson, AOYODKG, Apple, Aquarius, Archos, Arian Space, Ark, ArmPhone, Arnova, ARRIS, Artel, Artizlee, ArtLine, Asano, Asanzo, Ask, Aspera, Assistant, Astro, Asus, AT&T, Athesi, Atmaca Elektronik, ATMAN, Atom, Atvio, Audiovox, AURIS, Autan, AUX, Avenzo, AVH, Avvio, Awow, Axioo, Axxion, AYYA, Azumi Mobile, b2m, Backcell, BangOlufsen, Barnes & Noble, BBK, BB Mobile, BDF, BDQ, BDsharing, Becker, Beeline, Beelink, Beetel, Beista, Bellphone, Benco, Benesse, BenQ, BenQ-Siemens, Benzo, Beyond, Bezkam, BGH, Bigben, BIHEE, BilimLand, Billion, BioRugged, Bird, Bitel, Bitmore, Bittium, Bkav, Black Bear, Black Fox, Blackview, Blaupunkt, Bleck, BLISS, Blloc, Blow, Blu, Bluboo, Bluebird, Bluedot, Bluegood, Bluewave, BMAX, Bmobile, Bobarry, bogo, Boway, bq, Brandt, Bravis, BrightSign, Brondi, BROR, BS Mobile, Bubblegum, Bundy, Bush, CAGI, Camfone, Canal Digital, Capitel, Captiva, Carrefour, Casio, Casper, Cat, Cavion, Celcus, Celkon, Cell-C, Cellacom, CellAllure, Cellution, Centric, CG Mobile, CGV, Changhong, Cherry Mobile, Chico Mobile, China Mobile, China Telecom, Chuwi, Claresta, Clarmin, ClearPHONE, Clementoni, Cloud, Cloudfone, Cloudpad, Clout, CnM, Cobalt, Coby Kyros, Colors, Comio, Compal, Compaq, COMPUMAX, ComTrade Tesla, Concord, ConCorde, Condor, Connectce, Connex, Conquest, Contixo, Coolpad, CORN, Cosmote, Covia, Cowon, COYOTE, CreNova, Crescent, Cricket, Crius Mea, Crony, Crosscall, Crown, Cube, CUBOT, CVTE, Cyrus, Daewoo, Danew, Datalogic, Datamini, Datang, Datawind, Datsun, Dazen, DbPhone, Dbtel, Dell, Denali, Denver, Desay, DeWalt, DEXP, DF, Dialog, Dicam, Digi, Digicel, DIGICOM, DIGIFORS, Digihome, Digiland, Digit4G, Digma, DING DING, DISH, Ditecma, Diva, Divisat, DIXON, DL, DMM, DNS, DoCoMo, Doffler, Dolamee, Doogee, Doopro, Doov, Dopod, Doppio, DORLAND, Doro, Dragon Touch, Dreamgate, DreamTab, Droxio, Dune HD, DUNNS Mobile, E-Boda, E-Ceros, E-tel, Eagle, Easypix, EBEN, EBEST, Echo Mobiles, ecom, ECON, ECS, EE, EGL, Einstein, EKO, Eks Mobility, EKT, ELARI, Electroneum, ELECTRONIA, Elekta, Element, Elenberg, Elephone, Eltex, Ematic, Emporia, ENACOM, Energizer, Energy Sistem, Engel, Enot, eNOVA, Envizen, Epik One, Epson, Ergo, Ericsson, Ericy, Erisson, Essential, Essentielb, eSTAR, Eton, eTouch, Etuline, Eurocase, Eurostar, Evercoss, Evertek, Evolio, Evolveo, Evoo, EVPAD, EvroMedia, EWIS, EXCEED, Exmart, ExMobile, EXO, Explay, Extrem, EYU, Ezio, Ezze, F&U, F2 Mobile, F150, Facebook, Facetel, Fairphone, Famoco, Famous, Fantec, FaRao Pro, FarEasTone, Fengxiang, FEONAL, Fero, FiGi, FiGO, FiiO, FinePower, Finlux, FireFly Mobile, FISE, Fly, FLYCAT, FMT, FNB, FNF, Fondi, Fonos, FOODO, FORME, Formuler, Forstar, Fortis, Fourel, Four Mobile, Foxconn, Freetel, Fuego, Fujitsu, Funai, Fusion5, Future Mobile Technology, G-TiDE, G-Touch, Galaxy Innovations, Garmin-Asus, Gateway, Gemini, General Mobile, Genesis, GEOFOX, Geotel, Geotex, GEOZON, GFive, Ghia, Ghong, Ghost, Gigabyte, Gigaset, Gini, Ginzzu, Gionee, Globex, Glofiish, GLONYX, GLX, GOCLEVER, Gocomma, GoGEN, Gol Mobile, Goly, Gome, GoMobile, Google, Goophone, Gooweel, Gplus, Gradiente, Grape, Gree, Greentel, Gresso, Gretel, Grundig, Gtel, H96, Hafury, Haier, Haipai, Hamlet, HannSpree, HAOVM, Hardkernel, Hasee, Helio, HERO, Hezire, Hi, Hi-Level, High Q, Highscreen, HiMax, Hi Nova, Hipstreet, Hisense, Hitachi, Hitech, HKPro, HLLO, Hoffmann, Hometech, Homtom, Honeywell, Hoozo, Horizon, Horizont, Hosin, Hotel, Hot Pepper, HOTREALS, Hotwav, How, HP, HTC, Huadoo, Huagan, Huavi, Huawei, Humax, Hurricane, Huskee, Hyrican, Hyundai, Hyve, i-Cherry, I-INN, i-Joy, i-mate, i-mobile, iBall, iBerry, iBrit, IconBIT, iData, iDroid, iGet, iHunt, Ikea, IKI Mobile, iKoMo, iKon, IKU Mobile, iLA, iLife, iMan, iMars, IMO Mobile, Impression, INCAR, Inch, Inco, iNew, Infiniton, Infinix, InFocus, InfoKit, InFone, Inkti, InnJoo, Innos, Innostream, Inoi, iNo Mobile, INQ, Insignia, INSYS, Intek, Intel, Intex, Invens, Inverto, Invin, iOcean, iOutdoor, iPEGTOP, iPro, iQ&T, IQM, IRA, Irbis, iReplace, Iris, iRola, iRulu, iSafe Mobile, iSWAG, IT, iTel, iTruck, IUNI, iVA, iView, iVooMi, ivvi, iWaylink, iZotron, JAY-Tech, Jedi, Jeka, Jesy, JFone, Jiake, Jiayu, Jinga, Jio, Jivi, JKL, Jolla, Joy, JPay, Jumper, Juniper Systems, Just5, JVC, JXD, K-Touch, Kaan, Kaiomy, Kalley, Kanji, Karbonn, Kata, KATV1, Kazam, Kazuna, KDDI, Kempler & Strauss, Kenbo, Keneksi, Kenxinda, Kiano, Kingbox, Kingsun, KINGZONE, Kiowa, Kivi, Klipad, Kocaso, Kodak, Kogan, Komu, Konka, Konrow, Koobee, Koolnee, Kooper, KOPO, Koridy, Koslam, KREZ, KRIP, KRONO, Krüger&Matz, KT-Tech, KUBO, Kuliao, Kult, Kumai, Kurio, Kvant, Kyocera, Kyowon, Kzen, L-Max, LAIQ, Land Rover, Landvo, Lanix, Lark, Laurus, Lava, LCT, Leader Phone, Leagoo, Leben, Ledstar, LeEco, Leff, Leke, LEMFO, Lemhoov, Lenco, Lenovo, Leotec, Le Pan, Lephone, Lesia, Lexand, Lexibook, LG, Liberton, Lifemaxx, Lingwin, Linnex, Linsar, Listo, LNMBBS, Loewe, Logic, Logic Instrument, Logicom, LOKMAT, Loview, LT Mobile, Lumigon, Lumus, Luna, Luxor, LYF, M-Horse, M-Tech, M.T.T., M4tel, MAC AUDIO, Macoox, Mafe, Magicsee, Magnus, Majestic, Malata, Manhattan, Mann, Manta Multimedia, Mantra, Mara, Massgo, Masstel, Mastertech, Matrix, Maxcom, Maximus, Maxtron, MAXVI, Maxwest, MAXX, Maze, Maze Speed, MBI, MBOX, MDC Store, MDTV, meanIT, Mecer, Mecool, Mediacom, MediaTek, Medion, MEEG, MegaFon, Meitu, Meizu, Melrose, Memup, Metz, MEU, MicroMax, Microsoft, Microtech, Minix, Mintt, Mio, Mione, Miray, Mito, Mitsubishi, Mitsui, MIVO, MIXC, MiXzo, MLLED, MLS, MMI, Mobicel, MobiIoT, Mobiistar, Mobiola, Mobistel, MobiWire, Mobo, Modecom, Mofut, Motorola, Movic, mPhone, Mpman, MSI, MStar, MTC, MTN, Multilaser, MYFON, MyGica, Mymaga, MyPhone, Myria, Myros, Mystery, MyTab, MyWigo, Nabi, Naomi Phone, National, Navcity, Navitech, Navitel, Navon, NavRoad, NEC, Necnot, Neffos, Neo, neoCore, Neolix, Neomi, Neon IQ, Netgear, NeuImage, New Balance, New Bridge, Newgen, Newland, Newman, Newsday, NewsMy, Nexa, NEXBOX, Nexian, NEXON, Nextbit, NextBook, NextTab, NGM, NG Optics, Nikon, Nintendo, NOA, Noain, Nobby, Noblex, NOBUX, NOGA, Nokia, Nomi, Nomu, Noontec, Nordmende, NorthTech, Nos, Nous, Novex, Novey, NuAns, Nubia, NUU Mobile, Nuvo, Nvidia, NYX Mobile, O+, O2, Oale, OASYS, Obabox, Obi, Oculus, Odys, OINOM, Ok, Okapia, OKSI, OKWU, OMIX, Onda, OnePlus, Onix, Onkyo, ONN, ONYX BOOX, Ookee, OpelMobile, Openbox, OPPO, Opsson, Orange, Orbic, Orbita, Ordissimo, Orion, OTTO, OUJIA, Ouki, Oukitel, OUYA, Overmax, Ovvi, Owwo, OYSIN, Oysters, Oyyu, OzoneHD, P-UP, Packard Bell, Paladin, Palm, Panacom, Panasonic, Pantech, PAPYRE, Parrot Mobile, Partner Mobile, PCBOX, PCD, PCD Argentina, PEAQ, Pelitt, Pendoo, Pentagram, Perfeo, Phicomm, Philco, Philips, Phonemax, phoneOne, Pico, Pioneer, Pioneer Computers, PiPO, Pixela, Pixelphone, Pixus, Planet Computers, Ployer, Plum, PlusStyle, Pluzz, PocketBook, POCO, Point Mobile, Point of View, Polar, PolarLine, Polaroid, Polestar, PolyPad, Polytron, Pomp, Poppox, POPTEL, Porsche, Positivo, Positivo BGH, PPTV, Premio, Prestigio, Primepad, Primux, Prixton, PROFiLO, Proline, Prology, ProScan, Protruly, ProVision, PULID, Purism, Q-Box, Q-Touch, Q.Bell, Qilive, QLink, QMobile, Qnet Mobile, QTECH, Qtek, Quantum, Qubo, Quechua, Quest, Qumo, Qware, R-TV, Rakuten, Ramos, Raspberry, Ravoz, Raylandz, Razer, RCA Tablets, Reach, Readboy, Realme, RED, Redfox, Redway, Reeder, REGAL, Remdun, Retroid Pocket, Revo, Ricoh, Rikomagic, RIM, Rinno, Ritmix, Ritzviva, Riviera, Rivo, ROADMAX, Roadrover, Rokit, Roku, Rombica, Ross&Moor, Rover, RoverPad, Royole, RoyQueen, RT Project, RugGear, Ruio, Runbo, Ryte, S-TELL, S2Tel, Saba, Safaricom, Sagem, Saiet, Salora, Samsung, Sanei, Sansui, Santin, Sanyo, Savio, SCBC, Schneider, Schok, Seatel, Seeken, SEG, Sega, Selecline, Selenga, Selevision, Selfix, SEMP TCL, Sencor, Sendo, Senkatel, Senseit, Senwa, Seuic, SFR, Shanling, Sharp, Shift Phones, Shivaki, Shtrikh-M, Shuttle, Sico, Siemens, Sigma, Silelis, Silent Circle, Simbans, Simply, Singtech, Siragon, Sirin Labs, SK Broadband, SKG, Sky, Skyworth, Smadl, Smailo, Smart, Smartab, SmartBook, SMARTEC, Smart Electronic, Smartfren, Smartisan, Smarty, Smooth Mobile, Smotreshka, SNAMI, Softbank, Soho Style, SOLE, SOLO, Solone, Sonim, SONOS, Sony, Sony Ericsson, Soundmax, Soyes, Spark, SPC, Spectralink, Spectrum, Spice, Sprint, SQOOL, Star, Starlight, Starmobile, Starway, Starwind, STF Mobile, STG Telecom, STK, Stonex, Storex, StrawBerry, STRONG, Stylo, Subor, Sugar, Sumvision, Sunmi, Sunny, Sunstech, SunVan, Sunvell, SUNWIND, SuperSonic, SuperTab, Supra, Suzuki, Swipe, SWISSMOBILITY, Swisstone, SWTV, Sylvania, Symphony, Syrox, T-Mobile, T96, TAG Tech, Taiga System, Takara, Tambo, Tanix, TB Touch, TCL, TD Systems, TD Tech, TeachTouch, Technicolor, Technika, TechniSat, Technopc, TechnoTrend, TechPad, Techwood, Teclast, Tecno Mobile, TEENO, Teknosa, Tele2, Telefunken, Telego, Telenor, Telia, Telit, Telpo, TENPLUS, Teracube, Tesco, Tesla, Tetratab, teXet, ThL, Thomson, Thuraya, TIANYU, Tigers, Time2, Timovi, Tinai, Tinmo, TiPhone, TiVo, TJC, TOKYO, Tolino, Tone, Tooky, Topelotek, Top House, Toplux, Topway, Torex, TOSCIDO, Toshiba, Touchmate, Transpeed, TrekStor, Trevi, Trident, Trifone, Trio, Tronsmart, True, True Slim, TTEC, TTK-TV, TuCEL, Tunisie Telecom, Turbo, Turbo-X, TurboKids, TurboPad, Turkcell, TVC, TWM, Twoe, TWZ, Tymes, Türk Telekom, U-Magic, U.S. Cellular, Ugoos, Uhans, Uhappy, Ulefone, Umax, UMIDIGI, Unihertz, Unimax, Uniscope, UNIWA, Unknown, Unnecto, Unnion Technologies, UNNO, Unonu, Unowhy, Urovo, UTime, UTOK, UTStarcom, UZ Mobile, v-mobile, VAIO, VALEM, Vankyo, Vargo, Vastking, VAVA, VC, VDVD, Vega, Venso, Venturer, VEON, Verico, Verizon, Vernee, Verssed, Vertex, Vertu, Verykool, Vesta, Vestel, Vexia, VGO TEL, Videocon, Videoweb, ViewSonic, Vinabox, Vinga, Vinsoc, Vios, Vipro, Virzo, Vision Touch, Vitelcom, Viumee, Vivax, Vivo, VIWA, Vizio, VK Mobile, VKworld, Vodacom, Vodafone, VOGA, Vonino, Vontar, Vorago, Vorcom, Vorke, Vortex, Voto, VOX, Voxtel, Voyo, Vsmart, Vsun, VUCATIMES, Vulcan, VVETIME, Völfen, Walton, WE, Web TV, Weimei, WellcoM, WELLINGTON, Western Digital, Westpoint, Wexler, Wieppo, Wigor, Wiko, Wileyfox, Winds, Wink, Winmax, Winnovo, Wintouch, Wiseasy, WIWA, Wizz, Wolder, Wolfgang, Wolki, Wonu, Woo, Wortmann, Woxter, X-AGE, X-BO, X-TIGI, X-View, X.Vision, X88, X96, XGIMI, Xgody, Xiaodu, Xiaolajiao, Xiaomi, Xion, Xolo, Xoro, Xshitou, Xtouch, Xtratech, Yandex, Yarvik, YASIN, YEPEN, Yes, Yezz, Yoka TV, Yota, YOTOPT, Ytone, Yu, Yuandao, YU Fly, YUHO, YUMKEM, Yuno, Yusun, Yxtel, Zaith, Zatec, Zealot, Zebra, Zeeker, Zeemi, Zen, Zenek, Zentality, Zfiner, ZH&K, Zidoo, ZIFRO, ZIK, Ziox, Zonda, Zonko, Zopo, ZTE, Zuum, Zync, ZYQ, Zyrex, öwn

### List of detected bots:

360Spider, Abonti, Aboundexbot, Acoon, AdAuth, Adbeat, AddThis.com, ADMantX, ADmantX Service Fetcher, Adsbot, adstxtlab.com, aHrefs Bot, AhrefsSiteAudit, aiHitBot, Alexa Crawler, Alexa Site Audit, Allloadin Favicon Bot, Amazon Bot, Amazon ELB, Amazon Route53 Health Check, Amorank Spider, Analytics SEO Crawler, ApacheBench, Applebot, AppSignalBot, Arachni, archive.org bot, ArchiveBox, Ask Jeeves, AspiegelBot, Awario, Awario, Backlink-Check.de, BacklinkCrawler, Baidu Spider, Barkrowler, BazQux Reader, BDCbot, Better Uptime Bot, BingBot, Birdcrawlerbot, BitlyBot, Blekkobot, BLEXBot Crawler, Bloglovin, Blogtrottr, BoardReader, BoardReader Blog Indexer, Bountii Bot, BrandVerity, Browsershots, BUbiNG, Buck, BuiltWith, Butterfly Robot, Bytespider, CareerBot, Castro 2, Catchpoint, CATExplorador, ccBot crawler, CensysInspect, Charlotte, Choosito, Cincraw, CISPA Web Analyzer, Cliqzbot, CloudFlare Always Online, CloudFlare AMP Fetcher, Cloudflare Diagnostics, Cocolyzebot, Collectd, colly, CommaFeed, Comscore, ContentKing, Cookiebot, Crawldad, CriteoBot, CrowdTangle, CSS Certificate Spider, Cốc Cốc Bot, Datadog Agent, DataForSeoBot, datagnionbot, Datanyze, Dataprovider, DataXu, Daum, Dazoobot, deepnoc, Discobot, Discord Bot, DNSResearchBot, DomainAppender, DomainCrawler, Domain Re-Animator Bot, Domains Project, DomainStatsBot, DotBot, Dotcom Monitor, DuckDuckGo Bot, Easou Spider, eCairn-Grabber, EFF Do Not Track Verifier, EMail Exractor, EmailWolf, Embedly, evc-batch, ExaBot, ExactSeek Crawler, Expanse, Ezooms, eZ Publish Link Validator, Facebook External Hit, Faveeo, Feedbin, FeedBurner, Feedly, Feedspot, Feed Wrangler, Fever, Findxbot, Flipboard, FreeWebMonitoring, FreshRSS, GDNP, Generic Bot, Generic Bot, Genieo Web filter, Gigablast, Gigabot, GitCrawlerBot, Gluten Free Crawler, Gmail Image Proxy, Gobuster, Goo, Googlebot, Google Cloud Scheduler, Google Favicon, Google PageSpeed Insights, Google Partner Monitoring, Google Search Console, Google Stackdriver Monitoring, Google StoreBot, Google Structured Data Testing Tool, Gowikibot, Grammarly, Grapeshot, GTmetrix, Hatena Favicon, Headline, Heart Rails Capture, Heritrix, Heureka Feed, HTTPMon, httpx, HuaweiWebCatBot, HubPages, HubSpot, ICC-Crawler, ichiro, IDG/IT, Iframely, IIS Site Analysis, Infegy, InfoTigerBot, Inktomi Slurp, inoreader, InternetMeasurement, IONOS Crawler, IP-Guide Crawler, IPIP, IPS Agent, JungleKeyThumbnail, K6, Kaspersky, KomodiaBot, Kouio, Kozmonavt, l9explore, l9tcpid, Larbin web crawler, LastMod Bot, LCC, Let's Encrypt Validation, Lighthouse, Linespider, Linkdex Bot, LinkedIn Bot, LinkpadBot, LinkPreview, LTX71, LumtelBot, Lycos, MaCoCu, Magpie-Crawler, MagpieRSS, Mail.Ru Bot, masscan, masscan-ng, Mastodon Bot, Meanpath Bot, Mediatoolkit Bot, MegaIndex, MetaInspector, MetaJobBot, MicroAdBot, Mixrank Bot, MJ12 Bot, Mnogosearch, MojeekBot, Monitor.Us, MTRobot, Munin, MuscatFerret, Nagios check_http, NalezenCzBot, nbertaupete95, Neevabot, Netcraft Survey Bot, netEstate, NetLyzer FastProbe, NetResearchServer, NetSystemsResearch, Netvibes, NewsBlur, NewsGator, Newslitbot, Nimbostratus Bot, NLCrawler, Nmap, Notify Ninja, Nutch-based Bot, Nuzzel, oBot, Octopus, Odnoklassniki Bot, Omgili bot, Onalytica, Openindex Spider, OpenLinkProfiler, OpenWebSpider, Orange Bot, Outbrain, Page Modified Pinger, PagePeeker, PageThing, PaperLiBot, parse.ly, PayPal IPN, Petal Bot, Phantomas, PHP Server Monitor, Picsearch bot, PingAdmin.Ru, Pingdom Bot, Pinterest, PiplBot, Plukkie, PocketParser, Pompos, PritTorrent, Project Patchwatch, Project Resonance, PRTG Network Monitor, QuerySeekerSpider, Quora Bot, Quora Link Preview, Qwantify, Rainmeter, RamblerMail Image Proxy, Reddit Bot, Riddler, Robozilla, RocketMonitorBot, Rogerbot, ROI Hunter, RSSRadio Bot, Ryowl, SabsimBot, SafeDNSBot, Scamadviser External Hit, Scooter, ScoutJet, Scrapy, Screaming Frog SEO Spider, ScreenerBot, security.txt scanserver, Seekport, Sellers.Guide, Semantic Scholar Bot, Semrush Bot, Sensika Bot, Sentry Bot, Seobility, SEOENGBot, SEOkicks, SEOkicks-Robot, seolyt, Seoscanners.net, Serendeputy Bot, serpstatbot, Server Density, Seznam Bot, Seznam Email Proxy, Seznam Zbozi.cz, ShopAlike, Shopify Partner, ShopWiki, SilverReader, SimplePie, SISTRIX Crawler, SISTRIX Optimizer, Site24x7 Website Monitoring, Siteimprove, SitemapParser-VIPnytt, SiteSucker, Sixy.ch, Skype URI Preview, Slackbot, SMTBot, Snapchat Proxy, Snap URL Preview Service, Sogou Spider, Soso Spider, Sparkler, Speedy, Spinn3r, Spotify, Sprinklr, Sputnik Bot, Sputnik Favicon Bot, Sputnik Image Bot, sqlmap, SSL Labs, Startpagina Linkchecker, StatusCake, Superfeedr Bot, SurdotlyBot, Survey Bot, Tarmot Gezgin, TelegramBot, TestCrawler, The Knowledge AI, theoldreader, ThinkChaos, TigerBot, TinEye Crawler, Tiny Tiny RSS, TLSProbe, TraceMyFile, Trendiction Bot, Turnitin, TurnitinBot, TweetedTimes Bot, Tweetmeme Bot, Twingly Recon, Twitterbot, UkrNet Mail Proxy, uMBot, UniversalFeedParser, Uptimebot, Uptime Robot, URLAppendBot, Vagabondo, Velen Public Web Crawler, Vercel Bot, VeryHip, Visual Site Mapper Crawler, VK Share Button, W3C CSS Validator, W3C I18N Checker, W3C Link Checker, W3C Markup Validation Service, W3C MobileOK Checker, W3C Unified Validator, Wappalyzer, WebbCrawler, WebDataStats, Weborama, WebPageTest, WebPros, WebSitePulse, WebThumbnail, WellKnownBot, WeSEE:Search, WeViKaBot, WhatCMS, WhereGoes, WikiDo, Willow Internet Crawler, WooRank, WooRank, WordPress, Wotbox, XenForo, YaCy, Yahoo! Cache System, Yahoo! Japan BRW, Yahoo! Link Preview, Yahoo! Mail Proxy, Yahoo! Slurp, Yahoo Gemini, YaK, Yandex Bot, Yeti/Naverbot, Yottaa Site Monitor, Youdao Bot, Yourls, Yunyun Bot, Zaldamo, Zao, Ze List, zgrab, Zookabot, ZoominfoBot, ZumBot
