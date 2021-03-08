<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser\Device;

use \Spyc;
use DeviceDetector\Parser\Device\Notebook;
use PHPUnit\Framework\TestCase;

class NotebookTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse(string $useragent, array $device): void
    {
        $notebookParser = new Notebook();
        $notebookParser->setUserAgent($useragent);
        $this->assertNotNull($notebookParser->parse());
        $this->assertEquals($device['type'], Notebook::getDeviceName($notebookParser->getDeviceType()));
        $this->assertEquals($device['brand'], $notebookParser->getBrand());
        $this->assertEquals($device['model'], $notebookParser->getModel());
    }

    public function getFixtures(): array
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/notebook.yml');

        return $fixtureData;
    }
}
