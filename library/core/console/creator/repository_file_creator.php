<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
load_file('/library/core/console/creator/file_creator.php');

class RepositoryFileCreator extends FileCreator {

    public function createRepositoryBaseFile($objName, $structure)
    {
        foreach($structure as $name => $definitions) {
            $strRepoAttributes[$name] = $this->_createStrRepoAttribute($name, $definitions);
        }

        $last = count($strRepoAttributes);
        foreach($strRepoAttributes as $strRepoAttribute) {
            $strRepoAttributeResult .= $strRepoAttribute;
            if (++$count != $last) {
                $strRepoAttributeResult .= ",\n";
            }
        }

        $repoBaseFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base_repo.php';
        $repoBasePath = ROOT . '/app/repository/base/' . $repoBaseFileName;
        $repoBaseTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_repo_base.tmpl');

        $classNameSnakeCase = NamingConvention::camelCaseToSnakeCase($objName);
        $repoBaseTmpl = str_replace('{CLASS_NAME_SNAKE_CASE}',  $classNameSnakeCase, $repoBaseTmpl);
        $repoBaseTmpl = str_replace('{CLASS_NAME}',             $objName, $repoBaseTmpl);
        $repoBaseTmpl = str_replace('{OBJ_NAME}',               $objName, $repoBaseTmpl);
        $repoBaseTmpl = str_replace('{ATTRIBUTES}',             $strRepoAttributeResult, $repoBaseTmpl);

        Debug::out('creating: ' . $repoBasePath);
        if (!file_exists($repoBasePath)) {
            file_put_contents($repoBasePath, $repoBaseTmpl);
        }
    }

    public function updateRepositoryBaseFile($objName, $structure)
    {
        foreach($structure as $name => $definitions) {
            $strRepoAttributes[$name] = $this->_createStrRepoAttribute($name, $definitions);
        }

        $last = count($strRepoAttributes);
        foreach($strRepoAttributes as $strRepoAttribute) {
            $strRepoAttributeResult .= $strRepoAttribute;
            if (++$count != $last) {
                $strRepoAttributeResult .= ",\n";
            }
        }

        $repoBaseFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base_repo.php';
        $repoBasePath = ROOT . '/app/repository/base/' . $repoBaseFileName;
        $repoBaseTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_repo_base.tmpl');

        $classNameSnakeCase = NamingConvention::camelCaseToSnakeCase($objName);
        $repoBaseTmpl = str_replace('{CLASS_NAME_SNAKE_CASE}',  $classNameSnakeCase, $repoBaseTmpl);
        $repoBaseTmpl = str_replace('{CLASS_NAME}',             $objName, $repoBaseTmpl);
        $repoBaseTmpl = str_replace('{OBJ_NAME}',               $objName, $repoBaseTmpl);
        $repoBaseTmpl = str_replace('{ATTRIBUTES}',             $strRepoAttributeResult, $repoBaseTmpl);

        Debug::out('update: ' . $repoBasePath);
        file_put_contents($repoBasePath, $repoBaseTmpl);
    }

    public function createRepositoryFile($objName, $structure)
    {
        $repoFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_repo.php';
        $repoPath = ROOT . '/app/repository/' . $repoFileName;
        $repoTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_repo.tmpl');

        $classNameSnakeCase = NamingConvention::camelCaseToSnakeCase($objName);
        $repoTmpl = str_replace('{CLASS_NAME_SNAKE_CASE}',  $classNameSnakeCase, $repoTmpl);
        $repoTmpl = str_replace('{CLASS_NAME}',             $objName, $repoTmpl);

        Debug::log('creating: ' . $repoPath);
        if (!file_exists($repoPath)) {
            file_put_contents($repoPath, $repoTmpl);
        }
    }

    private function _createStrRepoAttribute($name, $definitions)
    {
        $type = $definitions[0];
        $object = $definitions[1];
        $spaces = 12;
        $str .= $this->_writeLine("'" . $name . "' => [" . $type . ", '" . $object . "']", $spaces, false);
        return $str;
    }
}
