<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DatabaseSqlInsert extends DatabaseSql {

    public function action()
    {
        $sql = $this->_createSQL();
        $dbh = Database::getInstance();
        if ($dbh->query($sql, PDO::FETCH_ASSOC)) {
            $this->_lastId = $dbh->lastInsertId();
        } else {
			$error = "DatabaseSqlInsert: DatabaseObject konnte nicht in die Tabelle
                '$this->_tableName' geschrieben werden -> " . print_r($dbh->errorInfo(), true);
            throw new DatabaseException($error);
        }
    }

    private function _createSQL()
    {
        $sql = 'INSERT INTO `' . $this->_tableName . '` (';

        //Spalten-Namen hinzufuegen
        $count = 0;
        foreach ($this->_structure as $keyCamelCase => $definitions) {
            $sql .= ' `' . NamingConvention::camelCaseToSnakeCase($keyCamelCase) . '` ';
            if (++$count < count($this->_structure)) {
               $sql .= ', ';
            }
        }
        $sql .= ') VALUES (';

        //Spaten-Werte hinzufuegen
        $count = 0;
        foreach ($this->_structure as $keyCamelCase => $definitions) {
            $value = $this->_getDatabaseObjectAttribte($keyCamelCase);
            $value = $this->_castValue($value, $definitions[0]);

            $sql .= $value;
            if (++$count < count($this->_structure)) {
               $sql .= ', ';
            }
        }
        $sql .= ')';
        return $sql;
    }
}
