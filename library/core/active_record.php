<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class ActiveRecord {

    /*
     *******************
     *    FUNCTIONS    *
     *******************
     */

    public function createTable($tableName, $attributes)
    {
        Debug::log('-- create_table(:' . $tableName . ')');
        $sql = $this->_getSqlCreateTable($tableName, $attributes);
        $this->_executeSql($sql);
    }

    public function dropTable($tableName)
    {
        Debug::log('-- drop_table(:' . $tableName . ')');
        $sql = $this->_getSqlDropTable($tableName);
        $this->_executeSql($sql);
    }

    public function renameTable($tableNameOld, $tableNameNew)
    {
        Debug::log('-- rename_table(:' . $tableNameOld . ', :' . $tableNameNew . ')');
        $sql = $this->_getSqlRenameTable($tableNameOld, $tableNameNew);
        $this->_executeSql($sql);
    }


    public function addColumn($tableName, $columnName, $type)
    {
        Debug::log('-- add_column(:' . $tableName . ', :' . $columnName . ', :' . $type . ')');
        $sql = $this->_getSqlAddColumn($tableName, $columnName, $type);
        $this->_executeSql($sql);
    }

    public function removeColumn($tableName, $columnName)
    {
        Debug::log('-- revome_column(:' . $tableName . ', :' . $columnName . ')');
        $sql = $this->_getSqlRemoveColumn($tableName, $columnName);
        $this->_executeSql($sql);
    }

    public function renameColumn($tableName, $columnNameOld, $columnNameNew, $type)
    {
        Debug::log('-- rename_column(:' . $tableName . ', :' . $columnNameOld . ', :' . $columnNameNew . ')');
        $sql = $this->_getSqlRenameColumn($tableName, $columnNameOld, $columnNameNew, $type);
        $this->_executeSql($sql);
    }


    /*
     *************
     *    SQL    *
     *************
     */
    private function _getSqlCreateTable($tableName, $attributes)
    {
        $count = count($attributes);
        foreach ($attributes as $values) {
            $name = $values[0];
            $type = $this->_getType($values[1]);
            $column = '`' . $name . '`' . ' ' . $type;
            if ($values[1] == 'TYPE_PRIMARY') {
                $column .= ', primary key (' . $name . ')';
            }
            if (--$count) {
                $column .= ', ';
            }
            $columns .= $column;
        }
        $sql = 'create table `' . $tableName . '` (' . $columns . ');';
        return $sql;
    }

    private function _getSqlDropTable($tableName)
    {
        $sql = 'drop table `' . $tableName . '`;';
        return $sql;
    }

    private function _getSqlRenameTable($tableNameOld, $tableNameNew)
    {
        $sql = 'alter table `' . $tableNameOld . '` rename to `' . $tableNameNew . '`;';
        return $sql;
    }


    private function _getSqlAddColumn($tableName, $columnName, $type)
    {
        $sql = 'alter table `' . $tableName . '` add `' . $columnName . '` ' . $this->_getType($type) . ';';
        return $sql;
    }

    private function _getSqlRemoveColumn($tableName, $columnName)
    {
        $sql = 'alter table `' . $tableName . '` drop `' . $columnName . '`;';
        return $sql;
    }

    private function _getSqlRenameColumn($tableName, $columnNameOld, $columnNameNew, $type)
    {
        $sql = 'alter table `' . $tableName . '` change `' . $columnNameOld . '` `' . $columnNameNew . '` ' .  $this->_getType($type) . ';';
        return $sql;
    }



    /*
     ********************
     *    SQL HELPER    *
     ********************
     */
    private function _getType($value) {
        $types = [
            'TYPE_PRIMARY'      => 'int(11) not null auto_increment',
            'TYPE_STRING'       => 'varchar(255)',
            'TYPE_TEXT'         => 'text',
            'TYPE_INT'          => 'int',
            'TYPE_FLOAT'        => 'float',
            'TYPE_DECIMAL'      => 'decimal',
            'TYPE_DATE'         => 'date',
            'TYPE_DATE_TIME'    => 'datetime',
            'TYPE_TIME'         => 'time',
            'TYPE_BINARY'       => 'blob',
            'TYPE_BOOLEAN'      => 'tinyint(1)'
        ];
        return $types[$value];
    }



    private function _executeSql($sql)
    {
        $this->_startTimer();
        $dbh = Database::getInstance();
        //Debug::out($dbh);
        $dbResult = $dbh->query($sql, PDO::FETCH_ASSOC);
        //Debug::log($sql);
        if (!$dbResult) {
            //Debug::log($dbh->errorInfo());
        }
        $this->_printTimer();
    }


    /*
     ****************
     *    HELPER    *
     ****************
     */

    private function _startTimer()
    {
        $this->_scriptTimeStart = microtime(true);
    }

    private function _stopTimer()
    {
        $this->_scriptTimeEnd = microtime(true);
        return $this->_scriptTimeEnd - $scriptTimeStart;
    }

    private function _printTimer()
    {
        Debug::log('   -> ' . $this->_stopTimer());
    }
}
