<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DOHtmlFormSelect extends DoHtmlForm
{
    private $_values;
    private $_selecteValue;

    public function __construct($name = null, $values = [], $selectedValue = null, $attributes = [])
    {
        $this->_attributes = $attributes;
        $this->name($name);
        $this->setTag('select');
        $this->_values = $values;
        $this->_selecteValue = $selectedValue;
    }

    public function addValue($text, $value = null)
    {
        if ($value) {
            $this->_values[$value] = $text;
        } else {
            $this->_values[] = $text;
        }
    }

    public function setSelectedValue($value)
    {
        $this->_selecteValue = $value;
    }

    public function setOnChange($onChangeUrl)
    {
        $x = "'";
        $url = $onChangeUrl . '&' . $this->getAttribute('name') . '=';
        $onChange = 'location=' . $x . $url . $x . ' + ' . 'this.options[this.selectedIndex].value;';
        $this->setAttribute('onchange', $onChange);
    }

    public function renderOption($value, $text, $selceted = false)
    {
        if ($selceted) {
            $selcetedStr = 'selected ';
        }
        return '<option ' . $selcetedStr . 'value="' . $value . '">' . $text . '</option>';
    }

    public function renderOptions()
    {
        foreach ($this->_values as $value => $text) {
            if ($value == $this->_selecteValue) {
                $result .= $this->renderOption($value, $text, true);
            } else {
                $result .= $this->renderOption($value, $text, false);
            }
        }
        return $result;
    }

    public function render($class = null)
    {
        if ($this->_labelText) {
            $result .= $this->renderLabel();
        }
        $result .= $this->renderOpen($class);
        $result .= $this->renderOptions();
        $result .= $this->renderClose();
        return $result;
    }

    public function renderBootstrap()
    {
        return $this->render('form-control');
    }
}
