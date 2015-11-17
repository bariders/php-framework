<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
load_file('/library/core/console/creator/file_creator.php');
load_file('/library/core/console/creator/dependency.php');

class ModelFileCreator extends FileCreator {
    private $_modelFileStr;
    private $_modelBaseFileStr;
    private $_strLoadFiles          = [];
    private $_strAttributes         = [];
    private $_strSetFunctions       = [];
    private $_strGetFunctions       = [];
    private $_strGetObjFunctions    = [];
    private $_strGetObjsFunctions   = [];

    public function createModelFile($objName, $structure)
    {
        $this->_createModelFileStr($objName, $structure);
        $modelFileName = NamingConvention::camelCaseToSnakeCase($objName) . '.php';
        $modelPath = ROOT . '/app/model/' . $modelFileName;
        Debug::out('creating: ' . $modelPath);
        if (!file_exists($modelPath)) {
            file_put_contents($modelPath, $this->_modelFileStr);
        }
    }

    public function createModelBaseFile($objName, $structure)
    {
        $this->_createComponents($objName, $structure);
        $this->_createModelBaseFileStr($objName, $structure);
        $modelBaseFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base.php';
        $modelBasePath = ROOT . '/app/model/base/' . $modelBaseFileName;

        //Debug::out('creating: ' . $modelBasePath);
        if (!file_exists($modelBasePath)) {
            file_put_contents($modelBasePath, $this->_modelBaseFileStr);
        }
    }

    public function updateModelBaseFile($objName, $structure)
    {
        $dependency = new Dependency();
        $dependencies = $dependency->getDependencies($objName);

        $this->_createComponents($objName, $structure, $dependencies);
        $this->_createModelBaseFileStr($objName, $structure);
        $modelBaseFileName = NamingConvention::camelCaseToSnakeCase($objName) . '_base.php';
        $modelBasePath = ROOT . '/app/model/base/' . $modelBaseFileName;

        Debug::out('update: ' . $modelBasePath);
        file_put_contents($modelBasePath, $this->_modelBaseFileStr);
    }


    private function _createComponents($objName, $structure, $dependencies = [])
    {
        foreach($structure as $name => $definitions) {
            $this->_strLoadFiles[$name]         = $this->_createStrLoadFile($name, $definitions);
            $this->_strGetObjFunctions[$name]   = $this->_createStrGetObjFunction($name, $definitions);
            $this->_strAttributes[$name]        = $this->_createStrAttribute($name, $definitions);
            $this->_strSetFunctions[$name]      = $this->_createStrSetFunction($name, $definitions);
            $this->_strGetFunctions[$name]      = $this->_createStrGetFunction($name, $definitions);
        }

        foreach ($dependencies as $dependency) {
            $this->_strLoadFiles[]              = $this->_createStrLoadFileDependency($dependency);
            $this->_strGetObjsFunctions[]       = $this->_createStrGetObjsFunction($objName, $dependency);
        }
    }

    private function _createModelFileStr($objName, $structure)
    {
        $classNameSnakeCase = NamingConvention::camelCaseToSnakeCase($objName);
        $modelTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_model.tmpl');
        $modelTmpl = str_replace('{LOAD_FILES}',            '',                     $modelTmpl);
        $modelTmpl = str_replace('{CLASS_NAME}',            $objName,               $modelTmpl);
        $modelTmpl = str_replace('{CLASS_NAME_SNAKE_CASE}', $classNameSnakeCase,    $modelTmpl);
        $this->_modelFileStr = $modelTmpl;
    }

    private function _createModelBaseFileStr($objName, $structure)
    {
        foreach($this->_strLoadFiles as $strLoadFile) {
            if ($strLoadFile) {
                $strLoadFileResult .= $strLoadFile;
            }
        }
        foreach($this->_strAttributes as $strAttribute) {
            $strAttributeResult .= $strAttribute;
        }
        foreach($this->_strSetFunctions as $strSetFunction) {
            $strSetFunctionResult .= $strSetFunction . "\n";
        }
        foreach($this->_strGetFunctions as $strGetFunction) {
            $strGetFunctionResult .= $strGetFunction . "\n";
        }
        foreach($this->_strGetObjFunctions as $strGetObjFunction) {
            if ($strGetObjFunction) {
                $strGetObjFunctionResult .= $strGetObjFunction . "\n";
            }
        }
        foreach($this->_strGetObjsFunctions as $strGetObjsFunction) {
            $strGetObjsFunctionResult .= $strGetObjsFunction . "\n";
        }

        $modelBaseTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_model_base.tmpl');
        $modelBaseTmpl = str_replace('{LOAD_FILES}',            $strLoadFileResult,         $modelBaseTmpl);
        $modelBaseTmpl = str_replace('{CLASS_NAME}',            $objName,                   $modelBaseTmpl);
        $modelBaseTmpl = str_replace('{ATTRIBUTES}',            $strAttributeResult,        $modelBaseTmpl);
        $modelBaseTmpl = str_replace('{SET_FUNCTIONS}',         $strSetFunctionResult,      $modelBaseTmpl);
        $modelBaseTmpl = str_replace('{GET_FUNCTIONS}',         $strGetFunctionResult,      $modelBaseTmpl);
        $modelBaseTmpl = str_replace('{GET_OBJ_FUNCTIONS}',     $strGetObjFunctionResult,   $modelBaseTmpl);
        $modelBaseTmpl = str_replace('{GET_OBJS_FUNCTIONS}',    $strGetObjsFunctionResult,  $modelBaseTmpl);
        $this->_modelBaseFileStr = $modelBaseTmpl;
    }

    private function _createStrAttribute($name, $definition)
    {
        $spaces = 4;
        $str .= $this->_writeLine('protected $_' .  $name .';', $spaces);
        return $str;
    }

    private function _createStrSetFunction($name, $definition)
    {
        $spaces = 4;
        $str .= $this->_writeLine('public function set' . ucfirst($name) . '($value)',              $spaces);
        $str .= $this->_writeLine('{',                                                              $spaces);
        if ($definition[0] == TYPE_FLOAT) {
            $str .= $this->_writeLine('    $this->_' . $name . ' = DOService::parseFloat($value);', $spaces);
        } else {
            $str .= $this->_writeLine('    $this->_' . $name . ' = $value;',                        $spaces);
        }
        $str .= $this->_writeLine('}',                                                              $spaces);
        return $str;
    }

    private function _createStrGetFunction($name, $definition)
    {
        $spaces = 4;
        $str .= $this->_writeLine('public function get' . ucfirst($name) . '()',    $spaces);
        $str .= $this->_writeLine('{',                                              $spaces);
        $str .= $this->_writeLine('    return $this->_' . $name . ';',              $spaces);
        $str .= $this->_writeLine('}',                                              $spaces);
        return $str;
    }

    private function _createStrLoadFile($name, $definition)
    {
        $object = $definition[1];
        if ($object) {
            $spaces = 0;
            $str .= $this->_writeLine("load_file('/app/repository/"
                    . NamingConvention::camelCaseToSnakeCase($object). "_repo.php');", $spaces);
            return $str;
        }
    }

    private function _createStrLoadFileDependency($dependency)
    {
        $str .= $this->_writeLine("load_file('/app/repository/"
                . NamingConvention::camelCaseToSnakeCase($dependency[0]). "_repo.php');", $spaces);
        return $str;
    }

    private function _createStrGetObjFunction($name, $definition)
    {
        $object = $definition[1];
        if ($object) {
            $name = substr($name, 0, strlen($name) -2);
            $spaces = 4;
            $str .= $this->_writeLine('public function get' . ucfirst($name) . '($cache = true)',                               $spaces);
            $str .= $this->_writeLine('{',                                                                                      $spaces);
            $str .= $this->_writeLine('    if (!$this->_' . $name . ' || $cache == false) {',                                   $spaces);
            $str .= $this->_writeLine('        $repo = new ' . ucfirst($object) . 'Repository();',                                $spaces);
            $str .= $this->_writeLine('        $this->_' . $name . ' = $repo->get($this->get' . ucfirst($name). 'Id());',       $spaces);
            $str .= $this->_writeLine('    }',                                                                                  $spaces);
            $str .= $this->_writeLine('    return $this->_' . $name . ';',                                                      $spaces);
            $str .= $this->_writeLine('}',                                                                                      $spaces);
            return $str;
        }
    }

    private function _createStrGetObjsFunction($objName, $dependency)
    {
        // Company:creatorId  -> User:getCompanysAsCreator();
        // Company:changerId  -> User:getCompanysAsChanger();
        // Company:userId     -> User:getCompanys();

        //varNameId -> VarName
        $foreignVarName = $dependency[1];
        $foreignSqlVarName = NamingConvention::camelCaseToSnakeCase($foreignVarName);
        $foreignSqlVarNameSave = "'" . $foreignSqlVarName . "'";
        $foreignVarObjName = substr($foreignVarName, 0, strlen($foreignVarName) - 2);
        $foreignVarObjName = ucfirst($foreignVarObjName);

        //Build functionname
        $foreignObjName = $dependency[0];
        $functionName = $dependency[0] . 's';
        if ($objName != $foreignVarObjName) {
            $functionName .= 'As' . $foreignVarObjName;
        }

        //Build Varname
        $varName = lcfirst($functionName);

        $name = substr($name, 0, strlen($name) -2);
        $spaces = 4;
        $str .= $this->_writeLine('public function get' . $functionName . '($orderBy = null, $cache = true)',                                $spaces);
        $str .= $this->_writeLine('{',                                                                                      $spaces);
        $str .= $this->_writeLine('    if (!$this->_' . $varName . ' || $cache == false) {',                                   $spaces);
        $str .= $this->_writeLine('        $repo = new ' . $foreignObjName . 'Repository();',                                $spaces);
        $str .= $this->_writeLine('        $repo->orderBy($orderBy);',                                                        $spaces);
        $str .= $this->_writeLine('        $this->_' . $varName . ' = $repo->getAllBy(' . $foreignSqlVarNameSave . ', $this->getId());',       $spaces);
        $str .= $this->_writeLine('    }',                                                                                  $spaces);
        $str .= $this->_writeLine('    return $this->_' . $varName . ';',                                                      $spaces);
        $str .= $this->_writeLine('}',                                                                                      $spaces);
        return $str;
    }
}
