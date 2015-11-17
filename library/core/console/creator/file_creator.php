<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
class FileCreator {
    protected function _writeLine($str, $spaces, $newLine = true)
    {
        for($i =0 ; $i < $spaces; $i++) {
            $strSpaces .= ' ';
        }
        if ($newLine) {
            return $strSpaces . $str . "\n";
        } else {
            return $strSpaces . $str;
        }
    }
}
