<?php
/**
 * Copyright (c) 2015 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
define('ROOT',          getcwd() . '/..');
define('CONSOLE_EXC',   'ON');
require_once(ROOT . '/library/core.php');
require_once(ROOT . '/library/core/console/script/model_script.php');

/*********************
*    SCRIPT-START    *
**********************/
$modelScript = new ModelScript();
$modelScript->action($argv);
