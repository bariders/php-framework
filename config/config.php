<?php
$host       = $_SERVER['SERVER_NAME'];
$ip         = $_SERVER['SERVER_ADDR'];
$serverIp   = '127.0.0.1';

if ($ip != $serverIp) {
    require_once('develop_config.php');
} else {
    require_once('production_config.php');
}
