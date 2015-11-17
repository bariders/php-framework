<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DOHtmlForm extends DoHtmlTag
{
    protected $_labelText;
    
	/**
	 * Konstrukor
	 * @param string $name Der Html-Tag Name
	 * @param string $action Die Url an die das Formular gesend wird
	 * @param string $method Default ist "post"
	 * @param array $attributs Tag-Attribute als Name => Wert. Default ist []
	 */
	public function __construct($name, $action, $method = 'post', $attributes = [])
    {
        $this->name($name);
        $this->setAttribute('action', $action);
        $this->setAttribute('method', $method);
        $this->setTag('form');
    }

    public function autocomplete($value = true)
    {
        if ($value == true || $value == 1 || $value = 'on') {
            $this->_attributes['autocomplete'] = 'on';
        } else {
            $this->_attributes['autocomplete'] = 'off';
        }
    }

    // der cursor wandert beim Laden der Seite automatisch auf dieses Feld.
    public function autofocus($value = true)
    {
        if ($value == true || $value == 1 || $value = 'on') {
            $this->_attributes['autofocus'] = true;
        } else {
            $this->_attributes['autofocus'] = false;
        }
    }

    // für type="checkbox" oder type="radio" kann ein Inputfeld vorausgewählt werden
    public function checked()
    {
    }

    // ausgegraute Felder, die erst nach einer bestimmten Bedingung aktiv geschaltet werden
    public function disabled($value = true)
    {
        if ($value == true || $value == 1 || $value = 'on') {
            $this->_attributes['disabled'] = true;
        } else {
            $this->_attributes['disabled'] = false;
        }
    }


    // spezifiziert, für welches Formular der Button gelten soll
    public function form($value)
    {
        $this->_attributes['form'] = $value;
    }

    // (nur für type="submit") , spezifiziert, wohin die Antwort geschickt werden soll
    public function formaction($value)
    {
        $this->_attributes['formaction'] = $value;
    }

    // (nur für type="submit") , soll nicht vorher ausgewertet werden
    public function formnovalidate($value = true)
    {
        if ($value == true || $value == 1 || $value = 'on') {
            $this->_attributes['formnovalidate'] = true;
        } else {
            $this->_attributes['formnovalidate'] = false;
        }
    }

    // (nur für type="submit") , spezifiziert, wo die Antwort ausgegeben werden soll
    public function formtarget($value)
    {
        $this->_attributes['formtarget'] = $value;
    }

    // Angabe von mehreren Optionen mit <datalist>
    /*
    public function list($value)
    {
    }
    */

    // Maximalwert
    public function max($value)
    {
        $this->_attributes['max'] = $value;
    }

    // maximale Länge
    public function maxlength($value)
    {
        $this->_attributes['maxlength'] = $value;
    }

    // Minimalwert
    public function min($value)
    {
        $this->_attributes['min'] = $value;
    }

    // Mehrfacheingaben
    public function multiple($value)
    {
    }

    // Feldname
    public function name($value)
    {
        $this->_attributes['name'] = NamingConvention::camelCaseToSnakeCase($value);
    }

    // Suchmuster
    public function pattern($value)
    {
        $this->_attributes['pattern'] = $value;
    }

    // Hinweis
    public function placeholder($value)
    {
        $this->_attributes['placeholder'] = $value;
    }

    // readonly
    public function readonly($value = true)
    {
        if ($value == true || $value == 1 || $value = 'on') {
            $this->_attributes['readonly'] = true;
        } else {
            $this->_attributes['readonly'] = false;
        }
    }

    // Pflichtangaben
    public function required($value = true)
    {
        if ($value == true || $value == 1 || $value = 'on') {
            $this->_attributes['required'] = true;
        } else {
            $this->_attributes['required'] = false;
        }
    }

    // Breite (in Zeichen)
    public function size($value)
    {
        $this->_attributes['size'] = $value;
    }

    // Intervalle (Schritte)
    public function step($value)
    {
    }

    // Angabe eines Werts
    public function value($value)
    {
        $this->_attributes['value'] = $value;
    }

    public function setLabel($labelText)
    {
        $this->_labelText = $labelText;
    }

    public function renderLabel()
    {
        return '<label for="' . $this->_attributes['name'] . '">' . $this->_labelText . '</label>';
    }
}
