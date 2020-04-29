<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser;

/**
 * Class BotParserAbstract
 *
 * Abstract class for all bot parsers
 *
 * @package DeviceDetector\Parser
 */
abstract class BotParserAbstract extends ParserAbstract
{
    /**
     * Enables information discarding
     */
    abstract public function discardDetails();
}