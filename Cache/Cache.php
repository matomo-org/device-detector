<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Cache;

interface Cache
{
    function fetch($id);

    function contains($id);

    function save($id, $data, $lifeTime = 0);

    function delete($id);

    function flushAll();
}
