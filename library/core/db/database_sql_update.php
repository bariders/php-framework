<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DatabaseSqlUpdate extends DatabaseSql {

    public function action()
    {
        $sql = $this->_createSQL();
        $dbh = Database::getInstance();
        if ($dbh->query($sql, PDO::FETCH_ASSOC)) {
            $this->_lastId = $dbh->lastInsertId();
        } else {
            throw new DatabaseException("DatabaseSqlUpdate: DatabaseObject konnte nicht in der "
                    . "Tabelle '$this->_tableName' akktuallisiert werden -> " . print_r($dbh->errorInfo(), true));
        }
    }

    private function _createSQL()
    {
        $sql = 'UPDATE `' . $this->_tableName . '` SET ';

        //Spalten-Namen hinzufuegen
        $count = 0;
        foreach ($this->_structure as $keyCamelCase => $definitions) {
            $value = $this->_getDatabaseObjectAttribte($keyCamelCase);
            $value = $this->_castValue($value, $definitions[0]);
            
            $sql .= ' `' . NamingConvention::camelCaseToSnakeCase($keyCamelCase) . '` = ' . $value;
            if (++$count < count($this->_structure)) {
               $sql .= ', ';
            }

        }
        $sql .= ' WHERE id = ' . (int) $this->_databaseObject->getId();
        return $sql;
    }
}
