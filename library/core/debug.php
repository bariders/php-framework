<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
class Debug {
    
    static public function out($mix)
    {
        if (DEBUG_MODE == 'ON') {
            if (CONSOLE_EXC == 'ON') {
                print_r($mix);
                echo "\n";
            } else {
                echo '<pre>';
                print_r($mix);
                echo '</pre>';
            }
        }
    }

    static public function log($mix)
    {
        echo $mix . "\n";
    }
}
