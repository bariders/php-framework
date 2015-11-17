<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DOService {

    static public function self($para) {
        return $_SERVER[´PHP_SELF´] . $para;
    }


    static public function isPhone() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $phone = stripos($agent, 'iPhone');

        if ($phone !== false) {
                return true;
        } else {
                return false;
        }
    }

    static public function isIpad() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ipad = stripos($agent, 'iPad');

        if ($ipad !== false) {
                return true;
        } else {
                return false;
        }
    }

    //////////////////////////////////
    //DATEI-/VERWALLTUNGS-FUNKTIONEN//
    //////////////////////////////////

    /*Diese Function schaut, wieviele Dateien sich in dem
    angebenen Verzeichnis (Ordner) befinden und nutzt die
    naechst hoehere Zahl als Postfix als Dateinamen
    Z.B.: Bild.jpg wird zu Bild_001.jpg, wenn sich null Dateien
    im Verziechnis befinden*/
    static public function fileNameNext($name, $path)
    {
        $obj = explode(".", $name);
        $name = $obj[0];
        $postfix = $obj[1]; //z.B.: "jpg"
        $num = self::countFiles($path);
        $num = self::addZero($num+1);
        $filename = $name ."_" .$num ."." .$postfix;
        return $filename;
    }

    /*Die Funktion liefert die Anzahl an Dateien, die sich in einem
    Verzeichnis (Ordner) befinden. ./ sowie ../ werden nicht mitgezaehlt.*/
    static public function countFiles($path) {
        $filecount = 0;
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while ($file = readdir($dh) !== false) {
                    $filecount++;
                }
                $filecount -= 2;
                return $filecount;
            }
        }
    }


    static public function currency($value, $free = 'kostenlos', $unknown = 'unbekannt')
    {
        if ($value == 0 || $value == '') {
            return $free;
        } elseif ($value < 0) {
            return $unknown;
        } else {
            return number_format((double)$value, 2, ',', '.') . ' €';
        }
    }

    static public function parseCurrency($value)
    {
        if ($value == '' || $value == 'unbekannt') {
            return -1;
        }
        $str = trim($value);
        $str = str_replace('.', '', $str);
        $str = str_replace(',', '.', $str);
        return (float) $str;
    }

    static public function percent($value, $decimals = 2)
    {
        return number_format((double)$value, $decimals, ',', '.') . ' %';
    }

    static public function parsePercent($value)
    {
        $str = trim($value);
        $str = str_replace('.', '', $str);
        $str = str_replace(',', '.', $str);
        return $str;
    }

    static public function float($value)
    {
        return str_replace('.', ',', $value);
    }

    static public function parseFloat($value)
    {
        $str = trim($value);
        $str = str_replace('.', '', $str);
        $str = str_replace(',', '.', $str);
        return $str;
    }

    static public function zipCityQuator($zipCode, $city, $quartor)
    {
        $result = $zipCode . ' ' . $city;
        if ($quartor && $quartor != $city) {
            $result .=  ' / ' . $quartor;
        }
        return $result;
    }

    static public function urlName($str, $dateTime = '', $withTime = false)
    {
        $str = trim($str);
        $str = str_replace('ä', 'ae', $str);
        $str = str_replace('ö', 'oe', $str);
        $str = str_replace('ü', 'ue', $str);
        $str = str_replace('Ä', 'Ae', $str);
        $str = str_replace('Ö', 'Oe', $str);
        $str = str_replace('Ü', 'Ue', $str);
        $str = str_replace('ß', 'ss', $str);
        $str = str_replace('ß', 'ss', $str);
        $str = preg_replace('/[^a-zA-Z0-9]/', '-', $str);
        $str = str_replace('---', '-', $str);
        $str = str_replace('--', '-', $str);
        $str = trim($str, '-');

        if ($dateTime) {
            if ($withTime) {
                $str = $dateTime . '-' . $str;
            } else {
                $str = substr($dateTime, 0, 10) . '-' . $str;
            }
        }

        return $str;
    }


    static public function fullPhoneNo($fon)
    {
        $fullFon = preg_replace('/\s+/', '', $fon);
        if (substr($fullFon, 0, 1) == '0') {
            $fullFon = substr($fullFon, 1);
        }
        return '+49' . $fullFon;
    }

    ///////////////////////
    //SONSTIGE-FUNKTIONEN//
    ///////////////////////

    /*Diese Funktion erzeugt eine ufaellige Zeichenfolge. '$count' gibt dabei die Anzahl der Zeichen an*/
    static public function nameRandom($count) {
        for($i=0; $i<$count; $i++) {
            $n = rand(1,3);
            if ($n == 1) {
                $num = rand(48,57); //48-57 0-9
            } else if ($n == 2) {
                $num = rand(65,90); //65-90 A-Z
            } else {
                $num = rand(97,122); //97-122 a-z
            }

            $letter = chr($num);
            $name .= $letter;
        }
        return $name;
    }

    /*Diese Funktion setzt vor die uebergebene Zahl, Nullen fuer jede fehlende Dezimalstelle.
    Es werden nur drei Stellen betrachtet. Fuer beliebige Anzahl an Stellen siehe: add_prefixZeros($number, $length)
    Z.B.: 1 wird zu 001, 23 wird zu 023, 154 beilbt 154.*/
    static public function addZero($zahl) {
        if ($zahl < 10) {
            $new = "00". $zahl;
        } elseif  ($zahl < 100) {
            $new = "0". $zahl;
        } elseif  ($zahl < 1000) {
            $new = $zahl;
        }
        return $new;
    }

    /*Fuegt eine Zahl '$number' so viele Nullen auf der linken Seite an, bis die gesamte Zeichnenlaenge
    dem Wert '$length' entspricht*/
    static public function prefixZerosAdd($number, $length) {
        $precount = $length - strlen($number);
        $newstr = "";
        for($i=0; $i < $precount; $i++) {
            $newstr .= "0";
        }
        return $newstr . $number;
    }

    static function urlStripWWW($url)
    {
        $url = trim($url);
        $url = str_replace('www.', '', $url);
        return $url;
    }

    static function urlStripProtocol($url)
    {
        $url = trim($url);
        $url = str_replace('http://', '', $url);
        $url = str_replace('https://', '', $url);
        return $url;
    }

    static function urlStripBoth($url)
    {
        $url = self::urlStripProtocol($url);
        $url = self::urlStripWWW($url);
        return $url;
    }
}
