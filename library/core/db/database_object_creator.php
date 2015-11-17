<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DatabaseObjectCreator extends DatabaseSql
{

    public function createDatabaseObjects($dbResult)
    {
        $databaseObjects = [];
        foreach($dbResult as $row) {
            $dataArray = $this->_makeDataArray($row);
            $obj = new $this->_objName($dataArray);
            $databaseObjects[] = $obj;
        }
        return $databaseObjects;
    }

    private function _makeDataArray($row)
    {
        $dataArray = [];
        foreach ($row as $keySnakeCase => $value) {
            $keyCamelCase = NamingConvention::snakeCaseToCamelCase($keySnakeCase);
            $definitions = $this->_structure[$keyCamelCase];
            switch ($definitions[0]) {
                case TYPE_INT:
                case TYPE_PRIMARY:
                    $dataArray[$keyCamelCase] = (int) $value;
                    break;
                case TYPE_FLOAT:
                    $dataArray[$keyCamelCase] = (float) $value;
                    break;
                case TYPE_STRING:
                case TYPE_TEXT:
                case TYPE_DATE_TIME:
                case TYPE_DATE:
                case TYPE_TIME:
                    $dataArray[$keyCamelCase] = stripslashes($value);
                    break;
                default:
                    $dataArray[$keyCamelCase] = stripslashes($value);
                    break;
            }
        }
        return $dataArray;
    }
}
