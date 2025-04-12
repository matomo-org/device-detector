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

use DeviceDetector\Parser\Device\Notebook;
use PHPUnit\Framework\TestCase;
use Spyc;

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

    public static function getFixtures(): array
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/notebook.yml');

        $fixtureData = \array_map(static function (array $item): array {
            return ['useragent' => $item['user_agent'], 'device' => $item['device']];
        }, $fixtureData);

        return $fixtureData;
    }
}
