<?php

namespace DeviceDetector\Parser\Client;

class MediaPlayer extends ClientParserAbstract {

    protected $fixtureFile = 'regexes/client/mediaplayers.yml';
    protected $parserName = 'media player';

    /**
     * Parses the current UA and checks whether it contains feed reader information
     *
     * @see feed_readers.yml for list of detected bots
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in feed_readers.yml and try to match every one
     * -> Set the matched data to $bot
     *
     * NOTE: Doing the big match before matching every single regex speeds up the detection
     */
    public function parse()
    {
        if (!$this->preMatchOverall()) {
            return null;
        }

        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);
            if ($matches)
                break;
        }

        if (!$matches) {
            return null;
        }

        return array(
            'type'       => 'mediaplayer',
            'name'       => $this->buildByMatch($regex['name'], $matches),
            'version'    => $this->buildVersion($regex['version'], $matches)
        );
    }
}