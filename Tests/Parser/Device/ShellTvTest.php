<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Tests\Parser\Device;

use DeviceDetector\Parser\Device\ShellTv;
use PHPUnit\Framework\TestCase;

class ShellTvTest extends TestCase
{
    public function testIsShellTv(): void
    {
        $dd = new ShellTv();
        $dd->setUserAgent('Leff Shell LC390TA2A');
        $this->assertEquals($dd->isShellTv(), true);
        $dd->setUserAgent('Leff Shell');
        $this->assertEquals($dd->isShellTv(), false);
    }
}
