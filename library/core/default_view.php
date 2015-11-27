<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DefaultView extends View
{
    private $_standardTmpl = '/app/tmpl/haml/public/standard_tmpl.haml';

    public function show()
    {

    }

    public function showDefault($objClassname, $values)
    {
        $this->setTemplate($this->_standardTmpl);
        $this->loadTmplVar('CONTENT', '/app/tmpl/haml/public/default/show.haml');
        $this->addHamlVar('objClassname', $objClassname);
        $this->addHamlVar('moduleName', NamingConvention::camelCaseToSnakeCase($objClassname) . 's');
        $this->addHamlVar('values', $values);
        $this->showTmpl();
    }

    public function showDefaultIndex($objClassname, $structure, $rows)
    {
        $this->setTemplate($this->_standardTmpl);
        $this->loadTmplVar('CONTENT', '/app/tmpl/haml/public/default/index.haml');
        $this->addHamlVar('objClassname', $objClassname);
        $this->addHamlVar('moduleName', NamingConvention::camelCaseToSnakeCase($objClassname) . 's');
        $this->addHamlVar('structure', $structure);
        $this->addHamlVar('rows', $rows);
        $this->showTmpl();
    }


    public function showDefaultNew($objClassname, $columnNames)
    {
        $this->setTemplate($this->_standardTmpl);
        $this->loadTmplVar('CONTENT', '/app/tmpl/haml/public/default/new_edit.haml');
        $this->addHamlVar('objClassname', $objClassname);
        $this->addHamlVar('moduleName', NamingConvention::camelCaseToSnakeCase($objClassname) . 's');
        $this->addHamlVar('columnNames', $columnNames);
        $this->addHamlVar('submitvalue', 'Add');
        $this->showTmpl();
    }

    public function showDefaultEdit($objClassname, $columnNames)
    {
        $this->setTemplate($this->_standardTmpl);
        $this->loadTmplVar('CONTENT', '/app/tmpl/haml/public/default/new_edit.haml');
        $this->addHamlVar('objClassname', $objClassname);
        $this->addHamlVar('moduleName', NamingConvention::camelCaseToSnakeCase($objClassname) . 's');
        $this->addHamlVar('columnNames', $columnNames);
        $this->addHamlVar('submitvalue', 'Change');
        $this->showTmpl();
    }
}
