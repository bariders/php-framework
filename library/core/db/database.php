<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class Database {
    private static $PDOINSTANCE;
    private static $HOST = DB_SERVER;
    private static $USER = DB_USER;
    private static $PASS = DB_PASSWORD;
    private static $BASE = DB_NAME;
    private static $CHAR = 'utf8';
    private static $PORT = 3306;

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!self::$PDOINSTANCE) {
            try {
                self::$PDOINSTANCE = new PDO('mysql:host=' . self::$HOST
                    . ';dbname=' . self::$BASE
                    . ';charset=' . self::$CHAR
                    . ';', self::$USER, self::$PASS);
                //self::$PDOINSTANCE->exec("SET NAMES 'utf8';");
            } catch (PDOException $e) {
                //Debug::out($e);
                Debug::out('PDO connection error: ' . $e->getMessage());
                Debug::out('Or maybe you have to set: export PATH=/Applications/MAMP/bin/php/phpX.X.X/bin/:$PATH');

                die();
                //Debug::log($dbh->errorInfo());
            }
        }
        return self::$PDOINSTANCE;
    }
}
