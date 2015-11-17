<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
class Dependency {
    private $_baseRepoDir = '/app/repository/base/';

    public function getDependencies($objName)
    {
        $dependencies = [];
        $baseRepoFileNames = $this->_getBaseRepoFileNames();
        foreach ($baseRepoFileNames as $baseRepoFileName) {
            $foreignObjName = $this->_getObjNameFromFileName($baseRepoFileName);
            $structure = $this->_getStructureFromBaseRepoFile($baseRepoFileName);
            $depndenciesFromStructure = $this->_getDependenciesFromStructure($foreignObjName, $objName, $structure);
            $dependencies = array_merge($dependencies, $depndenciesFromStructure);
        }
        return $dependencies;
    }

    private function _getDependenciesFromStructure($foreignObjName, $objName, $structure)
    {
        $result = [];
        foreach($structure as $keyName => $definition) {
            if ($definition[1] == $objName) {
                $result[]  = [$foreignObjName, $keyName, $definition[0], $definition[1]];
            }
        }
        return $result;
    }

    private function _getObjNameFromFileName($fileName)
    {
        $name = str_replace('_base_repo.php', '', $fileName);
        $name = NamingConvention::snakeCaseToCamelCaseFirstUpper($name);
        return $name;
    }

    private function _getStructureFromBaseRepoFile($fileName)
    {
        $objBaseRepositoryName = str_replace('.php', '', $fileName);
        $objBaseRepositoryName = NamingConvention::snakeCaseToCamelCaseFirstUpper($objBaseRepositoryName) . 'sitory';
        load_file($this->_baseRepoDir . $fileName);
        $objRepository = new $objBaseRepositoryName;
        return $objRepository->getStructure();
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
}
