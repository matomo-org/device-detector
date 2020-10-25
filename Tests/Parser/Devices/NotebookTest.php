<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser\Devices;

use DeviceDetector\Parser\Device\Notebook;
use \Spyc;
use PHPUnit\Framework\TestCase;

class NotebookTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $device)
    {
        $NotebookParser = new Notebook();
        $NotebookParser->setUserAgent($useragent);
        $this->assertNotNull($NotebookParser->parse());
        $this->assertEquals($device['type'], $NotebookParser->getDeviceType());
        $this->assertEquals($device['brand'], $NotebookParser->getBrand());
        $this->assertEquals($device['model'], $NotebookParser->getModel());
    }

    public function getFixtures()
    {
        $fixtureData = \Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/notebook.yml');
        return $fixtureData;
    }
}
