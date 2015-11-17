<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
load_file('/library/core/console/creator/file_creator.php');

class MigrationFileCreator extends FileCreator {

    public function createMigrationCreateFile($objName, $structure)
    {
        foreach($structure as $name => $definitions) {
            $strMigrationAttributes[$name] = $this->_createStrMigrationAttribute($name, $definitions);
        }


        $last = count($strMigrationAttributes);
        foreach($strMigrationAttributes as $strMigrationAttribute) {
            $strMigrationAttributeResult .= $strMigrationAttribute;
            if (++$count != $last) {
                $strMigrationAttributeResult .= ",\n";
            } else {
                $strMigrationAttributeResult .= "]";
            }
        }


        $migrationFileName = date('YmdHis') . '_create_' . NamingConvention::camelCaseToSnakeCase($objName) . '.php';
        $migrationPath = ROOT . '/db/migrate/' . $migrationFileName;

        $migrationTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_migration_create.tmpl');
        $migrationTmpl = str_replace('{CLASS_NAME}', $objName, $migrationTmpl);
        $migrationTmpl = str_replace('{TABLE_NAME}', NamingConvention::camelCaseToSnakeCase($objName), $migrationTmpl);
        $migrationTmpl = str_replace('{ATTRIBUTES}', $strMigrationAttributeResult, $migrationTmpl);

        Debug::log('creating: ' . $migrationPath);
        if (!file_exists($migrationPath)) {
            file_put_contents($migrationPath, $migrationTmpl);
        }
    }

    public function createMigrationRemoveFile($objName, $name, $definition)
    {
        $objNameSnakeCase = NamingConvention::camelCaseToSnakeCase($objName);
        $nameSnakeCase = NamingConvention::camelCaseToSnakeCase($name);

        $migrationFileName = date('YmdHis') . '_remove_' . $nameSnakeCase . '_from_' . $objNameSnakeCase . '.php';
        $migrationPath = ROOT . '/db/migrate/' . $migrationFileName;
        $migrationClassName = 'Remove' . ucfirst($name) . 'From' . $objName;

        $strUp = $this->_getStrRemoveColumn($objName, $name, $definition);
        $strDown = $this->_getStrAddColumn($objName, $name, $definition);

        $migrationTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_migration_update.tmpl');
        $migrationTmpl = str_replace('{CLASS_NAME}',    $migrationClassName,    $migrationTmpl);
        $migrationTmpl = str_replace('{UP}',            $strUp,                 $migrationTmpl);
        $migrationTmpl = str_replace('{DOWN}',          $strDown,               $migrationTmpl);

        Debug::log('creating: ' . $migrationPath);
        if (!file_exists($migrationPath)) {
            file_put_contents($migrationPath, $migrationTmpl);
        }
    }

    public function createMigrationAddFile($objName, $name, $definition)
    {
        $objNameSnakeCase = NamingConvention::camelCaseToSnakeCase($objName);
        $nameSnakeCase = NamingConvention::camelCaseToSnakeCase($name);

        $migrationFileName = date('YmdHis') . '_add_' . $nameSnakeCase . '_to_' . $objNameSnakeCase . '.php';
        $migrationPath = ROOT . '/db/migrate/' . $migrationFileName;
        $migrationClassName = 'Add' . ucfirst($name) . 'To' . $objName;

        $strUp = $this->_getStrAddColumn($objName, $name, $definition);
        $strDown = $this->_getStrRemoveColumn($objName, $name, $definition);

        $migrationTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_migration_update.tmpl');
        $migrationTmpl = str_replace('{CLASS_NAME}',    $migrationClassName,    $migrationTmpl);
        $migrationTmpl = str_replace('{UP}',            $strUp,                 $migrationTmpl);
        $migrationTmpl = str_replace('{DOWN}',          $strDown,               $migrationTmpl);

        Debug::log('creating: ' . $migrationPath);
        if (!file_exists($migrationPath)) {
            file_put_contents($migrationPath, $migrationTmpl);
        }
    }

    public function createMigrationRenameFile($objName, $nameOld, $definitionOld, $nameNew, $definitionNew)
    {
        $objNameSnakeCase = NamingConvention::camelCaseToSnakeCase($objName);

        $nameOldSnakeCase = NamingConvention::camelCaseToSnakeCase($nameOld);
        $nameNewSnakeCase = NamingConvention::camelCaseToSnakeCase($nameNew);

        $migrationFileName = date('YmdHis') . '_rename_' . $nameOldSnakeCase . '_to_' . $nameNewSnakeCase . '_in_' . $objNameSnakeCase . '.php';
        $migrationPath = ROOT . '/db/migrate/' . $migrationFileName;
        $migrationClassName = 'Rename' . ucfirst($nameOld) . 'To' . ucfirst($nameNew) . 'In' . $objName;

        $strUp = $this->_getStrRenameColumn($objName, $nameOld, $definitionOld, $nameNew, $definitionNew);
        $strDown = $this->_getStrRenameColumn($objName, $nameNew, $definitionNew, $nameOld, $definitionOld);

        $migrationTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_migration_update.tmpl');
        $migrationTmpl = str_replace('{CLASS_NAME}',    $migrationClassName,    $migrationTmpl);
        $migrationTmpl = str_replace('{UP}',            $strUp,                 $migrationTmpl);
        $migrationTmpl = str_replace('{DOWN}',          $strDown,               $migrationTmpl);

        Debug::log('creating: ' . $migrationPath);
        if (!file_exists($migrationPath)) {
            file_put_contents($migrationPath, $migrationTmpl);
        }
    }

    private function _getStrAddColumn($objName, $name, $definition)
    {
        $objNameSnakeCase   = NamingConvention::camelCaseToSnakeCase($objName);
        $nameSnakeCase      = NamingConvention::camelCaseToSnakeCase($name);
        $type               = $definition[0];
        $objNameSnakeCase   = "'" . $objNameSnakeCase . "'";
        $nameSnakeCase      = "'" . $nameSnakeCase . "'";
        $type               = "'" . $type . "'";

        $spaces = 8;
        $str .= $this->_writeLine('$this->addColumn(' . $objNameSnakeCase . ', ' . $nameSnakeCase . ', ' . $type. ');', $spaces, false);
        return $str;
    }

    private function _getStrRenameColumn($objName, $nameOld, $definitionOld, $nameNew, $definitionNew)
    {
        $objNameSnakeCase   = NamingConvention::camelCaseToSnakeCase($objName);
        $nameOldSnakeCase   = NamingConvention::camelCaseToSnakeCase($nameOld);
        $nameNewSnakeCase   = NamingConvention::camelCaseToSnakeCase($nameNew);
        $typeNew            = $definitionNew[0];

        $objNameSnakeCase   = "'" . $objNameSnakeCase . "'";
        $nameOldSnakeCase   = "'" . $nameOldSnakeCase . "'";
        $nameNewSnakeCase   = "'" . $nameNewSnakeCase . "'";
        $typeNew            = "'" . $typeNew . "'";

        $spaces = 8;
        $str .= $this->_writeLine('$this->renameColumn(' . $objNameSnakeCase . ', ' . $nameOldSnakeCase . ', ' . $nameNewSnakeCase . ', ' . $typeNew. ');', $spaces, false);
        return $str;
    }
    // $this->renameColumn('companyversion', 'memorialized', 'archived', 'date');

    private function _getStrRemoveColumn($objName, $name, $definition)
    {
        $objNameSnakeCase   = NamingConvention::camelCaseToSnakeCase($objName);
        $nameSnakeCase      = NamingConvention::camelCaseToSnakeCase($name);
        $objNameSnakeCase   = "'" . $objNameSnakeCase . "'";
        $nameSnakeCase      = "'" . $nameSnakeCase . "'";

        $spaces = 8;
        $str .= $this->_writeLine('$this->removeColumn(' . $objNameSnakeCase . ', ' . $nameSnakeCase . ');', $spaces, false);
        return $str;
    }

    private function _createStrMigrationAttribute($name, $definitions)
    {
        $type = $definitions[0];
        $spaces = 12;
        $str .= $this->_writeLine("['" . NamingConvention::camelCaseToSnakeCase($name) . "', '" . $type . "']", $spaces, false);
        return $str;
    }
}
