<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser\Device;

use DeviceDetector\Parser\Device\HbbTv;
use PHPUnit\Framework\TestCase;

class HbbTvTest extends TestCase
{
    public function testIsHbbTv()
    {
        $hbbTvParser = new HbbTv;
        $hbbTvParser->setUserAgent('Opera/9.80 (Linux mips ; U; HbbTV/1.1.1 (; Philips; ; ; ; ) CE-HTML/1.0 NETTV/3.2.1; en) Presto/2.6.33 Version/10.70');
        $this->assertEquals($hbbTvParser->isHbbTv(), '1.1.1');
    }

}
