<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Parser\Client;

use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\IndexerClient;

abstract class AbstractClientParser extends AbstractParser
{
    /**
     * @var string
     */
    protected $fixtureFile = '';

    /**
     * @var string
     */
    protected $parserName = '';

    /**
     * @var bool
     */
    protected $clientIndexes = false;

    /**
     * Parses the current UA and checks whether it contains any client information
     *
     * @see $fixtureFile for file with list of detected clients
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in feed_readers.yml and try to match every one
     * -> Return the matched feed reader
     *
     * NOTE: Doing the big match before matching every single regex speeds up the detection
     *
     * @return array|null
     */
    public function parse(): ?array
    {
        $result = null;

        if ($this->clientIndexes) {
            $result = $this->parseByPosition();
        }

        if (null === $result && $this->preMatchOverall()) {
            foreach ($this->getRegexes() as $regex) {
                $result = $this->parseByRegex($regex);

                if (null !== $result) {
                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * Parse the current UA by Indexes positions
     * @return array|null
     */
    public function parseByPosition(): ?array
    {
        $indexer   = new IndexerClient($this->userAgent);
        $dataId    = $indexer->getDataId($this->parserName);
        $dataIndex = $indexer->parse();

        if (null !== $dataId && !empty($dataIndex['data'][$dataId])) {
            $positions = $dataIndex['data'][$dataId];

            foreach ($positions as $position) {
                $regex  = $this->regexList[$position] ?? null;
                $result = null !== $regex ? $this->parseByRegex($regex) : null;

                if (null !== $result) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Parse the current UA by regex data from $this->regexList
     *
     * @param array $regex
     *
     * @return array|null
     */
    public function parseByRegex(array $regex): ?array
    {
        $matches = $this->matchUserAgent($regex['regex']);

        if (!$matches) {
            return null;
        }

        return  [
            'type'    => $this->parserName,
            'name'    => $this->buildByMatch($regex['name'], $matches),
            'version' => $this->buildVersion((string) $regex['version'], $matches),
        ];
    }

    /**
     * Returns all names defined in the regexes
     *
     * Attention: This method might not return all names of detected clients
     *
     * @return array
     */
    public static function getAvailableClients(): array
    {
        $instance = new static(); // @phpstan-ignore-line
        $regexes  = $instance->getRegexes();
        $names    = [];

        foreach ($regexes as $regex) {
            if ('$1' === $regex['name']) {
                continue;
            }

            $names[] = $regex['name'];
        }

        \natcasesort($names);

        return \array_unique($names);
    }

    /**
     * This method tells the class to use regex position indexes
     *
     * @param bool $stage
     *
     * @return void
     */
    public function setClientIndexer(bool $stage): void
    {
        $this->clientIndexes = $stage;
    }
}
