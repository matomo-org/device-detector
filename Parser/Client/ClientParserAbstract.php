<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

use DeviceDetector\Parser\ParserAbstract;

abstract class ClientParserAbstract extends ParserAbstract {

    protected function preMatchOverall()
    {
        $regexes = $this->getRegexes();

        static $overAllMatch;

        if (empty($overAllMatch)) {
            $overAllMatch = $this->getCache()->get($this->parserName.'-all');
        }

        if (empty($overAllMatch)) {
            // reverse all regexes, so we have the generic one first, which already matches most patterns
            $overAllMatch = array_reduce(array_reverse($regexes), function($val1, $val2) {
                if (!empty($val1)) {
                    return $val1.'|'.$val2['regex'];
                } else {
                    return $val2['regex'];
                }
            });
            $this->getCache()->set($this->parserName.'-all', $overAllMatch);
        }

        return $this->matchUserAgent($overAllMatch);
    }

}