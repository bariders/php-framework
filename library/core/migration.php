<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class Migration {
    private $_statusFile;
    private $_rootPath;

    public function __construct()
    {
        $this->_rootPath = ROOT;
        $this->_statusFile = $this->_rootPath . '/db/migration.status';
    }

    public function saveStatus($status)
    {
        file_put_contents($this->_statusFile, $status);
    }


    public function loadStatus()
    {
        if (file_exists($this->_statusFile)) {
            return trim(file_get_contents($this->_statusFile));
        } else {
            return 0;
        }
    }

    public function getMigrationFileNames($sort = 'ASC')
    {
        $dir = $this->_rootPath . '/db/migrate/';
        $files = scandir($dir);
        if ($sort == 'DESC') {
            rsort($files);
        } else {
            sort($files);
        }
        $migrationFileNames = array();
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != '.svn') {
                $migrationFileNames[] = $file;
            }
        }
        return $migrationFileNames;
    }

    public function getMigrationsToDoFileNames()
    {
        $migrationFileNames = $this->getMigrationFileNames();
        $statusMigraionFileName = $this->loadStatus();
        $migrationToDoFileNames = array();
        foreach ($migrationFileNames as $migrationFileName) {
            if ($collecting) {
                $migrationToDoFileNames[] = $migrationFileName;
            }
            if ($migrationFileName == $statusMigraionFileName) {
                $collecting = true;
            }
        }
        if ($collecting) {
            return $migrationToDoFileNames;
        } else {
            return $migrationFileNames;
        }
    }


    public function up()
    {
        $activeRecordFileNames = $this->getMigrationsToDoFileNames();
        if (!count($activeRecordFileNames)) {
            Debug::log($this->_addPostfix('== nothing to do ', '=', 70));
            return;
        } else {
            foreach($activeRecordFileNames as $activeRecordFileName) {
                $obj = $this->getActivRecordObj($activeRecordFileName);
                $this->_up($obj, $activeRecordFileName);
            }
            $this->saveStatus($activeRecordFileName);
        }
    }

    public function rollback()
    {
        Debug::log($this->_addPostfix('== rollback ', '=', 70));
        $activeRecordFileName = $this->loadStatus();
        $obj = $this->getActivRecordObj($activeRecordFileName);
        $this->_down($obj, $activeRecordFileName);
        $this->saveStatus($this->_getPreviousActiveRecordFileName($activeRecordFileName));
    }

    private function _getPreviousActiveRecordFileName($currentActiveRecordFileName)
    {
        $activeRecordFileNames = $this->getMigrationFileNames('DESC');
        $next = false;
        foreach($activeRecordFileNames as $activeRecordFileName) {
            if ($next == true) {
                break;
            }
            if ($activeRecordFileName == $currentActiveRecordFileName) {
                $next = true;
            }
        }
        return $activeRecordFileName;
    }


    private function _up($obj, $activeRecordFileName)
    {
        $className = $this->_getActiveRecordClassName($activeRecordFileName);
        Debug::log($this->_addPostfix('== ' . $className .': migrating ', '=', 70));
        $obj->up();
    }

    private function _down($obj, $activeRecordFileName)
    {
        $className = $this->_getActiveRecordClassName($activeRecordFileName);
        Debug::log($this->_addPostfix('== ' . $className .': migrating ', '=', 70));
        $obj->down();
    }


    public function getActivRecordObj($activeRecordFileName)
    {
        $namespace =  $this->_getActiveRecordNameSpace($activeRecordFileName);
        $this->loadActiveRecordFileIntoNamespace($activeRecordFileName, $namespace);
        $classNameCamelCase .= $this->_getActiveRecordClassName($activeRecordFileName);
        $rc = new ReflectionClass('\\' . $namespace . '\\' . $classNameCamelCase);
        $obj = $rc->newInstance();
        return $obj;
    }

    public function loadActiveRecordFileIntoNamespace($activeRecordFileName, $namespace) {
        //echo $namespace;
        eval('namespace ' . $namespace . '; ?>'
            . file_get_contents($this->_rootPath . '/db/migrate/' . $activeRecordFileName));
    }


    private function _getActiveRecordId($activeRecordFileName)
    {
        return substr($activeRecordFileName, 0, 14);
    }

    private function _getActiveRecordNameSpace($activeRecordFileName)
    {
        return 'active_record_' . substr($activeRecordFileName, 0, 14);
    }

    private function _getActiveRecordClassName($activeRecordFileName)
    {
        $classNameSnakeCase = substr($activeRecordFileName, 15, strlen($activeRecordFileName) - (16+3));
        $classNameCamelCase = NamingConvention::snakeCaseToCamelCaseFirstUpper($classNameSnakeCase);
        return $classNameCamelCase . 'Migration';
    }

    private function _addPostfix($str, $postfix, $totalLength)
    {
        $result = $str;
        $count = $totalLength - strlen($str);
        for($i=0; $i<$count;  $i++) {
            $result .= $postfix;
        }
        return $result;
    }


    public function printStatus($rows = 0)
    {
        $migrationFileNames = $this->getMigrationFileNames('DESC');
        $currentMigrationFileName = $this->loadStatus();

        echo 'Current-File: ' . $currentMigrationFileName . "\n";

        $headings = $this->_addPostfix('Status   Migration ID      Migration Name', ' ', 70);
        $line = $this->_addPostfix('', '-', 70);
        Debug::log($headings);
        Debug::log($line);
        $count = 0;
        foreach($migrationFileNames as $migrationFileName) {
            $id = $this->_getActiveRecordId($migrationFileName);
            $id = $this->_addPostfix($id, ' ', 18);
            if ($currentMigrationFileName == $migrationFileName) {
                $status = ' up';
            } else {
                $status = ' down';
            }
            $status =  $this->_addPostfix($status, ' ', 9);
            $name = $this->_getActiveRecordClassName($migrationFileName);
            Debug::log($status . $id . $name);

            if (++$count == $rows) {
                break;
            }
        }
    }
}
