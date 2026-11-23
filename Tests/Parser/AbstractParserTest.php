<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Tests\Parser;

use DeviceDetector\Parser\AbstractParser;
use PHPUnit\Framework\TestCase;

class AbstractParserTest extends TestCase
{
    public function testRegexesForUserAgentReturnsAllRegexesIfNotMoreThanChunkSize(): void
    {
        $regexes = ['a' => ['regex' => 'foo'], 'b' => ['regex' => 'bar']];
        $parser  = $this->createParser($regexes, 2);

        $this->assertSame($regexes, $parser->regexesForUserAgent('nothing'));
    }

    public function testRegexesForUserAgentSkipsChunksBeforeFirstMatchingChunk(): void
    {
        $regexes = [
            'a' => ['regex' => 'one'],
            'b' => ['regex' => 'two'],
            'c' => ['regex' => 'three'],
            'd' => ['regex' => 'four'],
            'e' => ['regex' => 'five'],
        ];
        $parser  = $this->createParser($regexes, 2);

        $this->assertSame($regexes, $parser->regexesForUserAgent('two; five'));
        $this->assertSame(
            ['c' => $regexes['c'], 'd' => $regexes['d'], 'e' => $regexes['e']],
            $parser->regexesForUserAgent('four; five')
        );
        $this->assertSame(['e' => $regexes['e']], $parser->regexesForUserAgent('five'));
        $this->assertSame([], $parser->regexesForUserAgent('six'));
    }

    public function testRegexesForUserAgentKeepsFirstMatchOfLinearScan(): void
    {
        $regexes = [];

        foreach (\range(1, 25) as $number) {
            $regexes[] = ['regex' => \sprintf('v%d(?:[;)]|$)', $number)];
        }

        $parser = $this->createParser($regexes, 4);

        foreach (['v1', 'v4', 'v5', 'v13', 'v24; v2', 'v25', 'v7)', 'v26'] as $userAgent) {
            $this->assertSame(
                $parser->firstMatch($regexes, $userAgent),
                $parser->firstMatch($parser->regexesForUserAgent($userAgent), $userAgent),
                $userAgent
            );
        }
    }

    public function testRegexesForUserAgentFallsBackToSingleRegexesOnPcreError(): void
    {
        $regexes = [
            ['regex' => '(?:a+)+$'],
            ['regex' => 'nomatch'],
            ['regex' => 'other'],
            ['regex' => 'b'],
        ];
        $parser  = $this->createParser($regexes, 2);

        $jit       = \ini_get('pcre.jit');
        $backtrack = \ini_get('pcre.backtrack_limit');
        \ini_set('pcre.jit', '0');
        \ini_set('pcre.backtrack_limit', '1000');

        try {
            $result = $parser->regexesForUserAgent(\str_repeat('a', 30) . ' b');
        } finally {
            \ini_set('pcre.jit', (string) $jit);
            \ini_set('pcre.backtrack_limit', (string) $backtrack);
        }

        $this->assertSame($regexes, $result);
    }

    private function createParser(array $regexes, int $chunkSize): AbstractParser
    {
        return new class($regexes, $chunkSize) extends AbstractParser {
            protected $parserName = 'abstractparsertest';

            public function __construct(array $regexes, int $chunkSize)
            {
                parent::__construct();
                // unique name per regex list, as cached data is shared between parsers of the same name
                $this->parserName     = 'abstractparsertest' . \md5(\serialize($regexes));
                $this->regexList      = $regexes;
                $this->regexChunkSize = $chunkSize;
            }

            public function parse(): ?array
            {
                return null;
            }

            public function regexesForUserAgent(string $userAgent): array
            {
                $this->setUserAgent($userAgent);

                return $this->getRegexesForUserAgent();
            }

            public function firstMatch(array $regexes, string $userAgent): ?string
            {
                $this->setUserAgent($userAgent);

                foreach ($regexes as $regex) {
                    if ($this->matchUserAgent($regex['regex'])) {
                        return $regex['regex'];
                    }
                }

                return null;
            }
        };
    }
}
