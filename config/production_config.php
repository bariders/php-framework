<?php
//B A S I C - S E T T I N G S
define('HAML_CACHE_PATH', 		'/cache/haml/');
define('COMPRESSION_GZIP',		'OFF');

//D A T A B A S E - S E T T I N G S (mySQL)
define('DB_SERVER',   'localhost');
define('DB_NAME',     'databaseName');
define('DB_USER',     'root');
define('DB_PASSWORD', '');

//E R R O R - S E T T I N G S
define('DEBUG_MODE',            'OFF');
ini_set('display_errors',       0);
error_reporting(E_ALL ^ E_NOTICE);
