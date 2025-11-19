<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Parser\Device;

/**
 * Class Mobile
 * I have doubts about the correctness of the actions to return models from 2014 to 2021 back to Huawei
 * It's not difficult, just a couple of days' work.
 * Выглядит это максимально странно я уже начал возвращать
 * Бала целая линейка Play в Huawei? потом продолжение уже в Honor, тоже самое Magic, Pad
 * Device parser for mobile detection
 */
class Mobile extends AbstractDeviceParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/device/mobiles.yml';

    /**
     * @var string
     */
    protected $parserName = 'mobile';
}
