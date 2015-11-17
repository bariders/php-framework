<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DatabaseSql {
    protected $_tableName;      //SnakeCase
    protected $_objName;        //CamelCase
    protected $_structure;
    protected $_databaseObject;
    protected $_lastId = -1;

    public function setTableName($value)
    {
        $this->_tableName = $value;
    }

    public function setObjName($value)
    {
        $this->_objName = $value;
    }

    public function setDatabaseObject($value)
    {
        $this->_databaseObject = $value;
    }

    public function setStructure($value)
    {
        $this->_structure = $value;
    }

    protected function _getDatabaseObjectAttribte($attributName)
    {
        $method = 'get' . ucfirst($attributName);
        return $this->_databaseObject->$method();
    }

    public function getLastId()
    {
        return $this->_lastId;
    }

    protected function _castValue($value, $type)
    {
        switch ($type) {
            case TYPE_INT:
            case TYPE_PRIMARY:
                return (int) $value;
                break;
            case TYPE_FLOAT:
                return (float) $value;
                break;
            case TYPE_STRING:
            case TYPE_TEXT:
            case TYPE_DATE_TIME:
            case TYPE_DATE:
            case TYPE_TIME:
                return DatabaseSql::dbQuote($value);
                break;
            default:
                return DatabaseSql::dbQuote($value);
                break;
        }
    }

    public static function dbQuote($str)
    {
        return "'" . addslashes($str) . "'";
    }
}
