<?php
/**
 * Copyright (c) 2015 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
require_once('../library/core.php');

/*****************
* SCRIPT - START *
******************/
$dispatcher = new Dispatcher();
$dispatcher->setDefaultRoute(['app','module','id','action']);
$dispatcher->setIndexApp('public');
$dispatcher->setIndexModule('index');
$dispatcher->invoke();
