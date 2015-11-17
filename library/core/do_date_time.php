<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DODateTime {
    /*
     **********************
     *    DATUM FORMAT    *
     **********************
     */

    static public function weekDayNameLong($weekDay)
    {
        $dayNames = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
        return $dayNames[$weekDay];
    }

    static public function dayName($datetime, $today = false)
    {
        $dayNames = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
        $timeStamp = self::dateTimeToTimeStamp($datetime);
        $dateToday = date('d.m.Y');
        $dateTimeDate = date('d.m.Y', $timeStamp);
        if ($dateToday == $dateTimeDate && $today) {
            return 'Heute';
        } else {
            return $dayNames[date('w', $timeStamp)];
        }
    }

    /*Gibt ein UNIX Timestamp im Long-Formart aus.
    Z.B.: 17. Julie 2008 09:34:21*/
    static public function longDateTime($datetime)
    {
        if (self::isDateTime($datetime)) {
            $str = self::dateTimeToTimeStamp($datetime);
            $string = date("d. F Y - H:i ", $str);
            return $string;
        } else {
            return null;
        }
    }

    /*Gibt ein UNIX Timestamp im Short-Formart aus.
    Z.B.: 13.12.2009 21:33*/
    static public function shortDateTime($datetime)
    {
        if (self::isDateTime($datetime)) {
            $str = self::dateTimeToTimeStamp($datetime);
            $string = date("d.m.Y \u\m H:i", $str);
            return $string;
        } else {
            return null;
        }
    }

    static public function shortDateTimeWithDay($datetime)
    {
        if (self::isDateTime($datetime)) {
            $str = self::dateTimeToTimeStamp($datetime);
            $string = date("d.m.Y \u\m H:i", $str);
            $dayName = self::dayName($datetime, true);
            return $dayName . '. ' . $string;
        } else {
            return null;
        }
    }

    static public function shortDateWithDay($datetime)
    {
        if (self::isDateTime($datetime)) {
            $str = self::dateTimeToTimeStamp($datetime);
            $string = date("d.m.Y", $str);
            $dayName = self::dayName($datetime, true);
            return $dayName . '. ' . $string;
        } else {
            return null;
        }
    }

    static public function veryShortDateTime($datetime)
    {
        if (self::isDateTime($datetime)) {
            $str = self::dateTimeToTimeStamp($datetime);
            $string = date("d.m.Y H:i", $str);
            return $string;
        } else {
            return null;
        }
    }

    /*Gibt ein UNIX Timestamp im Date-Short-Formart aus.
    Z.B.: 13.12.2009*/
    static public function shortDate($date)
    {
        if (self::isDate($date)) {
            $str = self::dateTimeToTimeStamp($date);
            $string = date("d.m.Y", $str);
            return $string;
        } else {
            return null;
        }
    }

    /*Gibt ein UNIX Timestamp im Time-Short-Formart aus.
    Z.B.: 13.12.2009 21:33*/
    static public function shortTime($time)
    {
        if (self::isTime($time)) {
            $str = self::dateTimeToTimeStamp($time);
            $string = date("H:i", $str);
            return $string;
        } else {
            return null;
        }
    }


    static public function schemaDateTime($datetime)
    {
        if (!self::isDateTimeTime($datetime)) {
            return date('Y-m-d', strtotime($datetime));
        } else {
            return date('Y-m-d\TH:i:s', strtotime($datetime));
        }
    }

    static public function dbDateTimeNow()
    {
        return date('Y-m-d H:i:s');
    }


    /*
     *****************************
     *    DATUM PARSE / INPUT    *
     *****************************
     */


    //17.07.1997 12:54:03 -> 1987-07-17 12:54:03
    static public function parseDateTime($input)
    {
        $input = trim($input);
        if ($input) {
            $dateTime = strtotime($input);
            return date('Y-m-d H:i:s', $dateTime);
        } else {
            return '0000-00-00 00:00:00';
        }
    }

    //17.07.1997 12:54:03 -> 1987-07-17
    static public function parseDate($input)
    {
        if ($input) {
            $dateTime = strtotime($input);
            return date('Y-m-d', $dateTime);
        } else {
            return '0000-00-00';
        }
    }

    //17.07.1997 12:54:03 -> 12:54:03
    static public function parseTime($input)
    {
        if ($input) {
            if (strlen($input) == 2) {
                $input .= ':00:00';
            }

            if (strlen($input) == 1) {
                $input = '0' . $input . ':00:00';
            }

            $dateTime = strtotime($input);
            return date('H:i:s', $dateTime);
        } else {
            return '00:00:00';
        }
    }

    //17.07.1997 12:54:03 -> 1987-07-17 00:00:00
    static public function parseDateTimeDate($input)
    {
        if ($input) {
            $dateTime = strtotime($input);
            return date('Y-m-d', $dateTime) . ' 00:00:00';
        } else {
            return '0000-00-00 00:00:00';
        }
    }

    //17.07.1997 12:54:03 -> 0000-00-00 12:54::03
    static public function parseDateTimeTime($input)
    {
        if ($input) {
            $dateTime = strtotime($input);
            return '0000-00-00 ' . self::parseTime($input);
        } else {
            return '0000-00-00 00:00:00';
        }
    }


    static public function parseDateTimeDateAndTime($dateInput, $timeInput)
    {
        return self::parseDate($dateInput) . ' ' . self::parseTime($timeInput);
    }

    /*
     ********************
     *    DATUM TEST    *
     ********************
     */

    static public function isDate($value) {
        if (!trim($value)) {
            return false;
        }

        if (trim($value) == '0000-00-00') {
            return false;
        }

        return true;
    }

    static public function isTime($value) {
        if (!trim($value)) {
            return false;
        }
        if (trim($value) == '00:00:00') {
            return false;
        }
        return true;
    }

    static public function isDateTime($value) {
        if (!trim($value)) {
            return false;
        }
        if (trim($value) == '0000-00-00 00:00:00') {
            return false;
        }
        return true;
    }

    static public function isDateTimeDate($value) {
        if (!trim($value)) {
            return false;
        }
        if (substr(trim($value), 0, 10) == '0000-00-00') {
            return false;
        }
        return true;
    }

    static public function isDateTimeTime($value) {
        $value = trim($value);
        if (!$value) {
            return false;
        }
        if (substr($value, 11, 8) == '00:00:00') {
            return false;
        }
        return true;
    }

    /*
     ***************************
     *    DATUM KALKULATION    *
     ***************************
     */
    static public function diffDateTimeSeconds($dateTimeStart, $dateTimeEnd)
    {
        $ts1 = strtotime($dateTimeStart);
        $ts2 = strtotime($dateTimeEnd);
        $secondsDiff = $ts2 - $ts1;
        return $secondsDiff;
    }

    static public function diffDateTimeDays($dateTimeStart, $dateTimeEnd)
    {
        $secondsDiff = self::diffDateTimeSeconds($dateTimeStart, $dateTimeEnd);
        $daysDiff = $secondsDiff / 3600 / 24;
        return $daysDiff;
    }

    static public function diffDateTimeSecondsToNow($dateTime)
    {
        $secondsDiff = self::diffDateTimeSeconds(date('Y-m-d H:i:s'), $dateTime);
        return $secondsDiff;
    }

    static public function diffDateTimeDaysToNow($dateTime)
    {
        $daysDiff = self::diffDateTimeDays(date('Y-m-d'), $dateTime);
        return $daysDiff;
    }


    static public function modifyDateTime($dateTime, $value)
    {
        $date = new DateTime($dateTime);
        $date->modify($value);
        return $date->format("Y-m-d H:i:s");
    }

    static public function formatDiffDays($days, $zeroDays = 'heute')
    {
        $days = floor($days);
        if (abs($days) == 0) {
            return $zeroDays;
        } else if (abs($days) == 1) {
            $postfix = ' Tag';
        } else {
            $postfix = ' Tage';
        }
        if ($days < 0 ) {
            $str = '-' . abs($days) . $postfix;
        } else {
            $str = $days . $postfix;
        }
        return $str;
    }


    /*
     ******************************
     *    DATUM HILFSFUNTIONEN    *
     ******************************
     */
    static public function cutTime($dateTime) {
        return substr($dateTime, 0, 10);
    }

    static public function cutDate($dateTime) {
        return substr($dateTime, 11, 8);
    }

    /*Gibt ein DateTime Obj zurueck passend fuer die Datenbank*/
    static public function makeDateTime($day, $month, $year, $hour, $min, $sec) {
        return $year ."-". $month ."-". $day ." ". $hour .":". $min .":". $sec;
    }


    /*Macht aus einem DateTime Objekt aus einer Datenbank ein UNIX Timestamp
    Ein UNIX Timestamp kann dann mit date in das gewuenschte Format gebracht
    werden oder mit den folgenen Funktionen*/
    static public function dateTimeToTimeStamp($datetime)
    {
        $year   = substr($datetime,0,4);
        $month  = substr($datetime,5,2);
        $day    = substr($datetime,8,2);
        $hour   = substr($datetime,11,2);
        $minute = substr($datetime,14,2);
        $second = substr($datetime,17,2);

        date_default_timezone_set("Europe/Berlin");
        $string = mktime($hour, $minute, $second, $month, $day, $year);

        return $string;
    }
}
