<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

 //F I L E D / A T T R I B U T E S - T Y P E S
 define('TYPE_PRIMARY',      'TYPE_PRIMARY');
 define('TYPE_INT',          'TYPE_INT');
 define('TYPE_FLOAT',        'TYPE_FLOAT');
 define('TYPE_STRING',       'TYPE_STRING');
 define('TYPE_TEXT',         'TYPE_TEXT');
 define('TYPE_DATE_TIME',    'TYPE_DATE_TIME');
 define('TYPE_DATE',         'TYPE_DATE');
 define('TYPE_TIME',         'TYPE_TIME');

class Repository {
    protected $_structure;
    protected $_objName;
    protected $_orderBy;

    public function getStructure()
    {
        return $this->_structure;
    }

    public function getObjName()
    {
        return $this->_objName;
    }

    public function getTableName()
    {
        return NamingConvention::camelCaseToSnakeCase($this->_objName);
    }

    public function add($obj)
    {
        if (is_object($obj)) {
            $dbSqlIntert = new DatabaseSqlInsert();
            $dbSqlIntert->setTableName($this->getTableName());
            $dbSqlIntert->setDatabaseObject($obj);
            $dbSqlIntert->setStructure($this->_structure);
            $dbSqlIntert->action();
			return $dbSqlIntert->getLastId();
        } else {
            return null;
        }
    }

    public function update($obj)
    {
        if (is_object($obj)) {
            $dbSqlUpdate = new DatabaseSqlUpdate();
            $dbSqlUpdate->setTableName($this->getTableName());
            $dbSqlUpdate->setDatabaseObject($obj);
            $dbSqlUpdate->setStructure($this->_structure);
            $dbSqlUpdate->action();
            return $obj->getId();
        } else {
            return null;
        }
    }

    public function delete($obj)
    {
        $sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE id="' . (int) $obj->getId(). '"';
        $dbh = Database::getInstance();
        $dbh->query($sql, PDO::FETCH_ASSOC);
    }

    public function deleteAllBy($column, $value)
    {
        $sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE ' . $column . ' = ' . DatabaseSql::dbQuote($value);
        $dbh = Database::getInstance();
        $dbh->query($sql, PDO::FETCH_ASSOC);
    }

    public function get($id)
    {
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` WHERE id = ' . (int) $id;
        $dbh = Database::getInstance();
        $dbResult = $dbh->query($sql, PDO::FETCH_ASSOC);

        $objs = $this->createObjs($dbResult);
        if (isset($objs[0]) && $objs[0] != null) {
            return $objs[0];
        } else {
            return null;
        }
    }

    public function getAll()
    {
        $sql = 'SELECT * FROM `' . $this->getTableName() . '`';
        if ($this->_orderBy) {
            $sql .= ' ORDER BY ' . $this->_orderBy;
        }
        $dbh = Database::getInstance();
        $dbResult = $dbh->query($sql, PDO::FETCH_ASSOC);
        if (!$dbResult) {
            Debug::out($dbh->errorInfo());
        }
        return $this->createObjs($dbResult);
    }


    public function getAllBy($column, $value)
    {
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` WHERE ' . $column . ' = ' . DatabaseSql::dbQuote($value);
        if ($this->_orderBy) {
            $sql .= ' ORDER BY ' . $this->_orderBy;
        }
        $dbh = Database::getInstance();
        $dbResult = $dbh->query($sql, PDO::FETCH_ASSOC);
        if (!$dbResult) {
            Debug::out($dbh->errorInfo());
        }
        return $this->createObjs($dbResult);
    }

    public function getAllByArray($values)
    {
        foreach ($values as $key => $value) {
            $sql .= $key  . ' = ' . DatabaseSql::dbQuote($value) . ' AND ';
        }
        $sql .= ' 1 = 1 ';
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` WHERE ' . $sql;

        if ($this->_orderBy) {
            $sql .= ' ORDER BY ' . $this->_orderBy;
        }
        $dbh = Database::getInstance();
        $dbResult = $dbh->query($sql, PDO::FETCH_ASSOC);
        if (!$dbResult) {
            Debug::out($dbh->errorInfo());
        }
        return $this->createObjs($dbResult);
    }

    public function getAllContainsArray($values)
    {
        foreach ($values as $key => $value) {
            $sql .= $key  . ' LIKE ' . DatabaseSql::dbQuote('%' . $value . '%');
            if (++$count != count($values)) {
                $sql .= ' OR ';
            }
        }
        $sql = 'SELECT * FROM `' . $this->getTableName() . '` WHERE ' . $sql;
        if ($this->_orderBy) {
            $sql .= ' ORDER BY ' . $this->_orderBy;
        }
        $dbh = Database::getInstance();
        $dbResult = $dbh->query($sql, PDO::FETCH_ASSOC);
        if (!$dbResult) {
            Debug::out($dbh->errorInfo());
        }
        return $this->createObjs($dbResult);
    }


    protected function createObjs($dbResult)
    {
        $dbObjectCreator = new DatabaseObjectCreator();
        $dbObjectCreator->setObjName($this->_objName);
        $dbObjectCreator->setStructure($this->_structure);
        return $dbObjectCreator->createDatabaseObjects($dbResult);
    }



    public function orderBy($column)
    {
        $this->_orderBy = $column;
    }

    public function getSchema()
    {
        $sql = 'SHOW full columns FROM ' . $this->getTableName();
        $dbh = Database::getInstance();
        $dbResult = $dbh->query($sql, PDO::FETCH_ASSOC);
        if (!$dbResult) {
            Debug::out($dbh->errorInfo());
        }
        foreach($dbResult as $row) {
            foreach ($row as $key => $value) {
                $schemaRow[strtolower($key)] = $value;
            }
            $schema[] = $schemaRow;
        }
        Debug::out($schema);
    }
}
