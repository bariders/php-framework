<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class Cache {
    //Caching Ein oder Ausschalten.
    private $_caching = false;

    public function save($site)
    {
        $filepath = $this->hashFilePath();

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        if ($this->_caching == true) {
            $this->_save($site, $filepath);
        }
    }

    private function _save($site, $filepath)
    {
        $fh = fopen($filepath, 'w');
        fwrite($fh, $site);
        fflush($fh);
        fclose($fh);
    }

    public function load()
    {
        $filepath = $this->hashFilePath();
        if ($this->upToDate($filepath) == true) {
            return file_get_contents($filepath);
        } else {
            return '';
        }
    }

    public function upToDate($filepath)
    {
        if (!file_exists($filepath)) {
            return false;
        }
        $time   = filectime($filepath);
        $hour   = (int) date('H', $time);
        $min    = (int) date('i', $time);

        $hourNow    = (int) date('H');
        $minNow     = (int) date('i');
        if ($min != $minNow || $hour != $hourNow) {
            return false;
        } else {
            return true;
        }
    }

    public function makeHash()
    {
        //echo $_SERVER['REQUEST_URI'];
        return md5($_SERVER['REQUEST_URI']);
    }

    public function hashFilePath()
    {
        return DOCUMENT_ROOT . CACHE_PATH . '/' . $this->makeHash() . '.html';
    }
}
