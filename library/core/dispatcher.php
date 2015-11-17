<?php
/**
 * Copyright (c) 2015 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class Dispatcher extends Controller {
    private $_errorApp;
    private $_errorModule;
    private $_errorAction;
    private $_indexApp    = 'public';
    private $_indexModule = 'index';
    private $_indexAction = 'index';

    public function setIndexApp($value)
    {
        $this->_indexApp = $value;
    }

    public function setIndexModule($value)
    {
        $this->_indexModule = $value;
    }

    public function setIndexAction($value)
    {
        $this->_indexAction = $value;
    }

    public function invoke()
    {
        $this->redirectAddWWW301();

        $query      = $this->_getQuery();
        $errorQuery = $this->_getErrorQuery();

        $this->_checkQueryNamingConvention($query);
        $this->_checkQueryNamingConvention($errorQuery);

        if ($this->_loadController($query)) {
            try {
                if ($this->_invoke($query)) {
                    return;
                } else {
                    $error = 'Dispatcher could not invoke action: ' . $query['app'] . '->' . $query['module'] . '->' . $query['action'];
                }
            } catch (Exception $e) {
                $error = 'Dispatcher could not load controller: ' . $query['app'] . '->' . $query['module'];
            }
        } else {
            $error = 'Dispatcher could not load controller: ' . $query['app'] . '->' . $query['module'];
        }

        if ($errorQuery) {
            if ($this->_loadController($errorQuery)) {
                try {
                    if ($this->_invoke($errorQuery)) {
                        return;
                    } else {
                        $error = 'Dispatcher could not invoke action: ' . $errorQuery['app'] . '->' . $errorQuery['module'] . '->' . $errorQuery['action'];
                    }
                } catch (Exception $e) {
                    $error = 'Dispatcher could not load controller: ' . $errorQuery['app'] . '->' . $errorQuery['module'];
                }
            } else {
                $error = 'Dispatcher could not load controller: ' . $errorQuery['app'] . '->' . $errorQuery['module'];
            }
        }

        Debug::out($error);
    }

    private function _getQuery()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        if (!isset($query['app']) && !isset($query['module']) && !isset($query['action'])) {
            $query['app']       = $this->_indexApp;
            $query['module']    = $this->_indexModule;
            $query['action']    = $this->_indexAction;
        }

        if (!isset($query['app'])) {
            $query['app']       = $this->_indexApp;
        }

        if (!isset($query['module'])) {
            $query['module']    = $this->_indexModule;
        }

        if (!isset($query['action']) && !isset($query['id'])) {
            $query['action']    = $this->_indexAction;
        }

        return $query;
    }

    private function _getErrorQuery()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        if ($this->_errorApp && $this->_errorModule) {
            $query['app']       = $this->_errorApp;
            $query['module']    = $this->_errorModule;
            $query['action']    = $this->_errorAction;
            return $query;
        }
        return null;
    }

    private function _checkQueryNamingConvention($query)
    {
        if ($query['app'] && !NamingConvention::isSnakeCase($query['app'])) {
            Debug::out('Dispatcher NamingConventionError: app=' . $query['app'] . ' has to be snake_case.');
            die();
        }

        if ($query['module'] && !NamingConvention::isSnakeCase($query['module'])) {
            Debug::out('Dispatcher NamingConventionError: module=' . $query['module'] . ' has to be snake_case.');
            die();
        }

        if ($query['action'] && !NamingConvention::isSnakeCase($query['action'])) {
            Debug::out('Dispatcher NamingConventionError: action=' . $query['action'] . ' has to be snake_case.');
            die();
        }
    }

    private function _queryToPath($query, $prefix = '/app/controller/')
    {
        $path = $prefix . strtolower($query['app']) . '/' . strtolower($query['module'])
              . '_' . strtolower($query['app']) . EXT;
        return $path;
    }

    private function _loadController($query)
    {
        $path = $this->_queryToPath($query);
        return !load_file($path, false);
    }

    private function _invoke($query)
    {
        $invokeFunction = 'invoke' . NamingConvention::snakeCaseToCamelCaseFirstUpper($query['action']);
        $classNameSnakeCase = $query['module'] . '_' . $query['app'] . '_controller';
        $rc = new ReflectionClass(NamingConvention::snakeCaseToCamelCaseFirstUpper($classNameSnakeCase));
        $controller = $rc->newInstance();

        $hasFunction = false;
        $methods = get_class_methods($controller);
        foreach ($methods as $method) {
            if ($method == $invokeFunction) {
                $controller->$invokeFunction();
                return true;
            }
        }
        return false;
    }
}
