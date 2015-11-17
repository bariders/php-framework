<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class SimpleBootstrapForm {
    private $_obj;
    private $_labels;
    private $_languageClass;

    public function __construct($obj, $labels = array(), $languageClass = null)
    {
        $this->_obj = $obj;
        $this->_labels = $labels;
        $this->_languageClass = $languageClass;
    }

    public function setLabels($labels)
    {
        $this->_labels = $labels;
    }

    public function getAction($values = [])
    {
        return Button::change($values);
    }

    public function isEdit()
    {
        if ($this->_obj->getId() != -1) {
            return true;
        } else {
            return false;
        }
    }

    public function getObj()
    {
        return $this->_obj;
    }


    public function render($varName, $formatedValue = null, $attributes = [], $inputName = null)
    {
        $nameSnakeCase = $this->_getNameSnakeCase($varName, $inputName);
        $labelName = $this->_getLabelText($varName, $inputName);
        $html .= $this->renderLabel($nameSnakeCase, $labelName);
        $html .= $this->renderInput($nameSnakeCase, $formatedValue, $attributes);
        return $html;
    }

    public function renderInputGroup($varName, $formatedValue = null, $attributes = [],
        $inputName = null, $wrapperClass = 'input-group', $addon = null)
    {
        $nameSnakeCase = $this->_getNameSnakeCase($varName, $inputName);
        $labelName = $this->_getLabelText($varName, $inputName);
        $html .= $this->renderLabel($nameSnakeCase, $labelName);
        $html .= '<div class=" ' . $wrapperClass . ' ">';
        $html .= $this->renderInput($nameSnakeCase, $formatedValue, $attributes);
        $html .= $addon;
        $html .= '</div>';
        return $html;
    }

    public function renderTextArea($varName, $formatedValue = null, $attributes = [], $inputName = null)
    {
        $nameSnakeCase = $this->_getNameSnakeCase($varName, $inputName);
        $labelName = $this->_getLabelText($varName, $inputName);
        $attributes['id'] = $nameSnakeCase;
        if (!$attributes['class']) {
            $attributes['class'] = 'form-control';
        }
        $html .= $this->renderLabel($nameSnakeCase, $labelName);
        $html .= DOHtmlFormCreator::textArea($nameSnakeCase, $formatedValue, $attributes);
        return $html;
    }

    public function renderLabel($nameSnakeCase, $labelName)
    {
        $html = '<label for="' . $nameSnakeCase . '">' . $labelName . '</label>';
        return $html;
    }

    public function renderInput($nameSnakeCase, $formatedValue, $attributes)
    {
        $attributes['id'] = $nameSnakeCase;
        if (!$attributes['class']) {
            $attributes['class'] = 'form-control';
        }
        return DOHtmlFormCreator::input($nameSnakeCase, 'text', $formatedValue, $attributes);
    }

    private function _getNameSnakeCase($varName, $inputName)
    {
        if ($inputName) {
            $name = $inputName;
        } else {
            $name = $varName;
        }
        return NamingConvention::camelCaseToSnakeCase($name);
    }

    private function _getValue($varName)
    {
        if (is_object($this->_obj)) {
            $function = 'get' . $varName;
            $value = $this->_obj->$function();
            return $value;
        } else {
            return $this->_obj[$varName];
        }
    }

    private function _getLabelText($varName, $inputName)
    {
        if ($inputName) {
            $name = $inputName;
        } else {
            $name = $varName;
        }
        $labelName = $this->_labels[$name];
        if (!$labelName) {
            $labelName = NamingConvention::camelCaseToSnakeCase($varName);
        }
        return $labelName;
    }

    public function getCurrency($varName, $attributes = [], $nullValue = 'kostenlos', $inputName = null)
    {
        $value = $this->_getValue($varName);
        $formatedValue = DOService::currency($value, $nullValue, '');
        $addon = '<div class="input-group-addon">
                    <span class="glyphicon glyphicon-euro" aria-hidden="true">
                  </span></div>';
        return $this->renderInputGroup($varName, $formatedValue, $attributes, $inputName, 'input-group', $addon);
    }

    public function getPercent($varName, $attributes = [], $inputName = null)
    {
        $value = $this->_getValue($varName);
        $formatedValue = DOService::percent($value);
        $addon = '<div class="input-group-addon">%</div>';
        return $this->renderInputGroup($varName, $formatedValue, $attributes, $inputName, 'input-group', $addon);
    }

    public function getDate($varName, $attributes = [], $inputName = null)
    {
        $value = $this->_getValue($varName);
        if (DODateTime::isDateTimeDate($value)) {
            $formatedValue = DODateTime::shortDate($value);
        } else {
            $formatedValue = '';
        }
        $attributes['readonly'] = 'ture';
        if (!$attributes['class']) {
            $attributes['class'] = 'form-control noreadonly';
        }
        $addon = '<div class="input-group-addon"><i class="glyphicon glyphicon-th"></i></div>';
        return $this->renderInputGroup($varName, $formatedValue, $attributes, $inputName, 'input-group date', $addon);
    }

    public function getTime($varName, $attributes = [], $inputName = null)
    {
        $value = $this->_getValue($varName);
        if (DODateTime::isDateTimeTime($value)) {
            $formatedValue = DODateTime::shortTime($value);
        } else {
            $formatedValue = '';
        }
        $addon = '<div class="input-group-addon"><i class="glyphicon glyphicon-time"></i></div>';
        return $this->renderInputGroup($varName, $formatedValue, $attributes, $inputName, 'input-group', $addon);
    }

    public function getText($varName, $attributes = [], $inputName = null)
    {
        $formatedValue = $this->_getValue($varName);
        return $this->renderTextArea($varName, $formatedValue, $attributes, $inputName = null);
    }

    public function get($varName, $attributes = [], $inputName = null)
    {
        $formatedValue = $this->_getValue($varName);
        return $this->render($varName, $formatedValue, $attributes, $inputName = null);
    }

    public function getFloat($varName, $attributes = [], $inputName = null)
    {
        $formatedValue = DOService::float($this->_getValue($varName));
        return $this->render($varName, $formatedValue, $attributes, $inputName = null);
    }
}
