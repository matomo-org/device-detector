<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Parser;

/**
 * Class IndexerClient
 * This is a helper class that allows you to find regular expression key numbers,
 * so that in the future we would not do complete pass through the array, but partial one
 */
class IndexerClient extends AbstractParser
{
    public const BROWSER = 1;
    public const APP     = 2;

    /**
     * @var string
     */
    protected $parserName = 'indexer client';

    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/client/indexes.yml';

    /**
     * Creating data for the index and for searching by index
     * @param string $userAgent
     *
     * @return array
     */
    public static function createDataIndex(string $userAgent): array
    {
        $tokens = self::createTokens($userAgent);
        $groups = self::createGroups($tokens);

        $parts = [];

        foreach ($groups as $key => $value) {
            if (!\is_string($value) || !$value) {
                continue;
            }

            if ($key && 0 === \strpos((string) $key, '#')) {
                if (!\preg_match('/[\/;]/', $value) && !\preg_match('/^\s*[\d.]+/', $value)) {
                    $parts[] = \strtolower($value);

                    continue;
                }

                continue;
            }

            $parts[] = \strtolower((string) $key);
        }

        $hash = \str_replace('-', '', 'i' . self::createHash(\implode('.', $parts)));
        $path = \implode('.', $parts);

        return \compact('tokens', 'groups', 'hash', 'path');
    }

    /**
     * @return array
     */
    public function parse(): array
    {
        if (false === \is_file($this->getRegexesFilePath())) {
            return [];
        }

        $dataIndex = self::createDataIndex($this->userAgent);
        $hash      = $dataIndex['hash'] ?? null;
        $data      = $this->getRegexes()[$hash] ?? [];

        return \compact('hash', 'data');
    }

    /***
     * Get sub data index for parse name
     *
     * @param string $parserName
     *
     * @return void
     */
    public function getDataId(string $parserName): ?int
    {
        $parsers = [
            'browser'    => self::BROWSER,
            'mobile app' => self::APP,
        ];

        return $parsers[$parserName] ?? null;
    }

    /**
     * Will create a simple hash based on the string
     * @param string $str
     *
     * @return string
     */
    private static function createHash(string $str): string
    {
        $hash = 0;
        $len  = \strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $hash = (($hash << 5) - $hash + \ord($str[$i])) & 0xFFFFFFFF;
        }

        return \dechex($hash);
    }

    /**
     * Splits useragent string into an array of tokens
     * @param string $userAgent
     *
     * @return array
     */
    private static function createTokens(string $userAgent): array
    {
        $tokens = \preg_split('/ (?![^(]*\))/', $userAgent);

        return false !== $tokens ? $tokens : [];
    }

    /**
     * Forming groups from an array of tokens
     * @param array $tokens
     *
     * @return array
     */
    private static function createGroups(array $tokens): array
    {
        $groupIndex = 0;

        return \array_reduce($tokens, static function ($group, $token) use (&$groupIndex) {
            if ('' === $token) {
                return $group;
            }

            \preg_match('/^\((.*)\)$/', $token, $data);

            if (!empty($data)) {
                $groupIndex++;
                $group["#{$groupIndex}"] = \preg_split('/[;,] /', $data[1]);

                return $group;
            }

            $rowSlash = \explode('/', $token);

            if (2 === \count($rowSlash)) {
                $group[$rowSlash[0]] = $rowSlash[1];

                return $group;
            }

            $groupIndex++;
            $group["#{$groupIndex}"] = $token;

            return $group;
        }, []);
    }
}
