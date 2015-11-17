<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
define('CONTROLLER_MAIN_FILE', '/index.php');

class Button
{
    static function app($app, $values = [], $anker = '')
    {
        $values['app'] = $app;
        return self::url($values, $anker);
    }

    static function module($module, $values = [], $anker = '')
    {
        $values['app'] = $_GET['app'];
        $values['module'] = $module;
        return self::url($values, $anker);
    }
    
    static function moduleId($module, $id, $values = [], $anker = '')
    {
        $values['app']      = $_GET['app'];
        $values['module']   = $module;
        $values['id']       = $id;
        return self::url($values, $anker);
    }
    
    static function action($action, $values = [], $anker = '')
    {
        $values['app']      = $_GET['app'];
        $values['module']   = $_GET['module'];
        $values['action']   = $action;
        return self::url($values, $anker);
    }
    
    static function actionId($action, $id, $values = [], $anker = '')
    {
        $values['app']      = $_GET['app'];
        $values['module']   = $_GET['module'];
        $values['action']   = $action;
        $values['id']       = $id;
        return self::url($values, $anker);
    }
    
    static function id($id, $values = [], $anker = '')
    {
        $values['app']      = $_GET['app'];
        $values['module']   = $_GET['module'];
        $values['action']   = $_GET['action'];
        $values['id']       = $id;
        return self::url($values, $anker);
    }

    
    static function change($values = [], $anker = '')
    {
        $newValues = $_GET;
        foreach($values as $key => $value) {
            if ($value == null) {
                unset($newValues[$key]);
            } else {
                $newValues[$key] = $value;
            }
        }
        return self::url($newValues, $anker);
    }

    static function repeat()
    {
        return self::change([]);
    }
    
    static public function url($values, $anker = '')
    {
        $values = self::_stdValuesToSnakeCase($values);
        $mainfile = CONTROLLER_MAIN_FILE;
        $last = count($values);
        foreach ($values as $key => $value) {
            $count++;
            if ($value != null) {
                $keySnakeCase = NamingConvention::camelCaseToSnakeCase($key);
                $urlValues .= $keySnakeCase .'=' . $value;
                if($count != $last) {
                    $urlValues .= '&';
                }
            }
        }
        $url = $mainfile . '?' . $urlValues;
        if ($anker) {
            $url .= '#' . $anker;
        }
        return $url;
    }

    static private function _stdValuesToSnakeCase($values)
    {
        if ($values['app']) {
            $values['app'] = NamingConvention::camelCaseToSnakeCase($values['app']);
        }
        if ($values['module']) {
            $values['module'] = NamingConvention::camelCaseToSnakeCase($values['module']);
        }
        if ($values['action']) {
            $values['action'] = NamingConvention::camelCaseToSnakeCase($values['action']);
        }
        return $values;
    }
}
