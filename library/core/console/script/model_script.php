<?php
/**
 * Copyright (c) 2015 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
load_file('/library/core/console/script/script.php');
load_file('/library/core/console/creator/model_file_creator.php');
load_file('/library/core/console/creator/repository_file_creator.php');
load_file('/library/core/console/creator/migration_file_creator.php');
load_file('/library/core/console/creator/controller_file_creator.php');

class ModelScript extends Script
{
    private $_baseRepoDir = '/app/repository/base/';

    public function action($argv)
    {
        $command = $argv[1];
        if ($command == 'create' || $command == '-c') {
            Debug::out('Command: create');
            $this->_createModel($argv);
        } elseif ($command == 'add-att' || $command == '-att') {
            Debug::out('Command: add-att');
            $this->_addAttributeToModel($argv);
        } elseif ($command == 'remove-att' || $command == '-ratt') {
            Debug::out('Command: remove-att');
            $this->_removeAttributeFromModel($argv);
        } elseif ($command == 'rename-att' || $command == '-reatt') {
            Debug::out('Command: rename-att');
            $this->_renameAttributeInModel($argv);
        } elseif ($command == 'update' || $command == '-u') {
            Debug::out('Command: update');
            $this->_updateModel($argv);
        } elseif ($command == 'update-all' || $command == '-ua') {
            Debug::out('Command: update-all');
            $this->_updateModels($argv);
        } elseif ($command == 'create-controller' || $command == '-co') {
            Debug::out('Command: create-controller');
            $this->_createController($argv);
        } elseif ($command === 'help' || $command == '-h') {
            Debug::out('Command: -h (help)');
            Debug::out('create or -c ObjName attName:TYPE:[ObjectName] ...');
            Debug::out('addatt or -aatt ObjName attName:TYPE:[ObjectName]');
            Debug::out('removeatt or -ratt ObjName attName');
            Debug::out('renameatt or -reatt ObjName attNameOld attNameNew:TYPE:[ObjectName]');
            Debug::out('update or -u ObjName');
            Debug::out('update-all or -ua');
            Debug::out('create-controller or -co ObjName [App]');
        } else {
            Debug::out('Command unknown. Try -h for help.');
        }
    }

    private function _createController($argv)
    {
        $objName = $argv[2];
        $app = $argv[3];
        $controllerFileCreator = new ControllerFileCreator();
        $controllerFileCreator->createModelControllerFile($objName, $app);
    }

    private function _createModel($argv)
    {
        $objName = $argv[2];
        $structure = $this->_parseStructure($argv, 3);

        $modelFileCreator = new ModelFileCreator();
        $modelFileCreator->createModelBaseFile($objName, $structure);
        $modelFileCreator->createModelFile($objName, $structure);
        $repositoryFileCreator = new RepositoryFileCreator();
        $repositoryFileCreator->createRepositoryBaseFile($objName, $structure);
        $repositoryFileCreator->createRepositoryFile($objName, $structure);
        $migrationFileCreator = new MigrationFileCreator();
        $migrationFileCreator->createMigrationCreateFile($objName, $structure);
    }

    private function _updateModel($argv)
    {
        $objName = $argv[2];

        $objBaseRepositoryName = $objName . 'BaseRepository';
        $objBaseRepositoryFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base_repo.php';
        load_file($this->_baseRepoDir . $objBaseRepositoryFileName);
        $objRepository = new $objBaseRepositoryName;
        $structure = $objRepository->getStructure();

        $modelFileCreator = new ModelFileCreator();
        $modelFileCreator->updateModelBaseFile($objName, $structure);
    }

    private function _updateModels($argv)
    {
        $baseRepoFiles = $this->_getBaseRepoFileNames();
        foreach($baseRepoFiles as $baseRepoFile) {
            $objName = str_replace('_base_repo.php', '', $baseRepoFile);
            $objName = NamingConvention::snakeCaseToCamelCaseFirstUpper($objName);

            $objBaseRepositoryName = $objName . 'BaseRepository';
            $objBaseRepositoryFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base_repo.php';
            load_file($this->_baseRepoDir . $objBaseRepositoryFileName);
            $objRepository = new $objBaseRepositoryName;
            $structure = $objRepository->getStructure();

            $modelFileCreator = new ModelFileCreator();
            $modelFileCreator->updateModelBaseFile($objName, $structure);
        }
    }

    private function _getBaseRepoFileNames($sort = 'ASC')
    {
        $dir = ROOT . $this->_baseRepoDir;
        $files = scandir($dir);
        if ($sort == 'DESC') {
            rsort($files);
        } else {
            sort($files);
        }
        $fileNames = array();
        foreach ($files as $file) {
            if (substr($file, 0, 1) != '.') {
                $fileNames[] = $file;
            }
        }
        return $fileNames;
    }


    private function _addAttributeToModel($argv)
    {
        $objName = $argv[2];
        $structureAdd = $this->_parseStructure($argv, 3);
        $attName = key($structureAdd);
        $definitions = $structureAdd[$attName];

        $objBaseRepositoryName = $objName . 'BaseRepository';
        $objBaseRepositoryFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base_repo.php';
        load_file($this->_baseRepoDir . $objBaseRepositoryFileName);
        $objRepository = new $objBaseRepositoryName;
        $structure = $objRepository->getStructure();
        $structure[$attName] = $definitions;

        $modelFileCreator = new ModelFileCreator();
        $modelFileCreator->updateModelBaseFile($objName, $structure);
        $repositoryFileCreator = new RepositoryFileCreator();
        $repositoryFileCreator->updateRepositoryBaseFile($objName, $structure);
        $migrationFileCreator = new MigrationFileCreator();
        $migrationFileCreator->createMigrationAddFile($objName, $attName, $definitions);
    }

    private function _removeAttributeFromModel($argv)
    {
        $objName = $argv[2];
        $attName = $argv[3];

        $objBaseRepositoryName = $objName . 'BaseRepository';
        $objBaseRepositoryFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base_repo.php';
        load_file($this->_baseRepoDir . $objBaseRepositoryFileName);
        $objRepository = new $objBaseRepositoryName;
        $structure = $objRepository->getStructure();
        $definitions = $structure[$attName];
        unset($structure[$attName]);

        $modelFileCreator = new ModelFileCreator();
        $modelFileCreator->updateModelBaseFile($objName, $structure);
        $repositoryFileCreator = new RepositoryFileCreator();
        $repositoryFileCreator->updateRepositoryBaseFile($objName, $structure);
        $migrationFileCreator = new MigrationFileCreator();
        $migrationFileCreator->createMigrationRemoveFile($objName, $attName, $definitions);
    }

    private function _renameAttributeInModel($argv)
    {
        $objName = $argv[2];
        $attNameOld = $argv[3];
        $structureNew = $this->_parseStructure($argv, 4);
        $attNameNew = key($structureNew);
        $definitionsNew = $structureNew[$attNameNew];

        $objBaseRepositoryName = $objName . 'BaseRepository';
        $objBaseRepositoryFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base_repo.php';
        load_file($this->_baseRepoDir . $objBaseRepositoryFileName);
        $objRepository = new $objBaseRepositoryName;
        $structure = $objRepository->getStructure();
        $definitionsOld = $structure[$attNameOld];
        unset($structure[$attNameOld]);
        $structure[$attNameNew] = $definitionsNew;

        $modelFileCreator = new ModelFileCreator();
        $modelFileCreator->updateModelBaseFile($objName, $structure);
        $repositoryFileCreator = new RepositoryFileCreator();
        $repositoryFileCreator->updateRepositoryBaseFile($objName, $structure);
        $migrationFileCreator = new MigrationFileCreator();
        $migrationFileCreator->createMigrationRenameFile($objName, $attNameOld, $definitionsOld, $attNameNew, $definitionsNew);
    }

    private function _parseStructure($argv, $start)
    {
        $structure = [];
        $count = count($argv);
        for ($i=$start; $i<$count; $i++) {
            $array = explode(':', $argv[$i]);
            $type = $this->_paraseType($array[1]);
            $structure[$array[0]] = [$type , $array[2]];
        }
        return $structure;
    }

    private function _paraseType($type)
    {
        if ($type == '') {
            return 'TYPE_INT';
        }

        $typeDef['TYPE_INT']        = TYPE_INT;
        $typeDef['type_int']        = TYPE_INT;
        $typeDef['integer']         = TYPE_INT;
        $typeDef['int']             = TYPE_INT;

        $typeDef['TYPE_FLOAT']      = TYPE_FLOAT;
        $typeDef['type_float']      = TYPE_FLOAT;
        $typeDef['float']           = TYPE_FLOAT;

        $typeDef['TYPE_PRIMARY']    = TYPE_PRIMARY;
        $typeDef['type_primary']    = TYPE_PRIMARY;
        $typeDef['primay']          = TYPE_PRIMARY;

        $typeDef['TYPE_STRING']     = TYPE_STRING;
        $typeDef['type_string']     = TYPE_STRING;
        $typeDef['string']          = TYPE_STRING;

        $typeDef['TYPE_TEXT']       = TYPE_TEXT;
        $typeDef['type_text']       = TYPE_TEXT;
        $typeDef['text']            = TYPE_TEXT;

        $typeDef['TYPE_DATE_TIME']  = TYPE_DATE_TIME;
        $typeDef['type_date_time']  = TYPE_DATE_TIME;
        $typeDef['datetime']        = TYPE_DATE_TIME;

        $typeDef['TYPE_DATE']       = TYPE_DATE;
        $typeDef['type_date']       = TYPE_DATE;
        $typeDef['date']            = TYPE_DATE;

        $typeDef['TYPE_TIME']       = TYPE_TIME;
        $typeDef['type_time']       = TYPE_TIME;
        $typeDef['time']            = TYPE_TIME;

        return $typeDef[$type];
    }
}
