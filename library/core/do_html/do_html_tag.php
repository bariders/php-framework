<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DOHtmlTag
{
    protected $_tag;
    protected $_attributes;

    
    public function setTag($value)
    {
        $this->_tag = $value;
    }

    public function getTag()
    {
        return $this->_tag;
    }
    

    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    public function getAttribute($name)
    {
        return $this->_attributes[$name];
    }


    public function renderAttribute($name)
    {
        $value = $this->_attributes[$name];
        if (!$value) {
            return '';
        }

        if (is_bool($value) && $value) {
            return $name;
        }

        if ($value) {
            return $name . '="' . $value . '"';
        }
    }

    public function renderAttributes($sort = true)
    {
        if ($sort) {
            ksort($this->_attributes);
        }

        foreach($this->_attributes as $key => $value) {
            $fragment =  $this->renderAttribute($key);
            $str .= $fragment;
            if ($fragment && ++$count < count($this->_attributes)) {
                $str .= ' ';
            }
        }
        return $str;
    }

    
    public function renderOpen($class = null) {
        if ($class) {
            $tempClass = $class;
            $this->_attributes['class'] = $class;
        }

        $attributesStr = $this->renderAttributes();
        if ($attributesStr) {
            $attributesStr = ' ' . $attributesStr;
        }

        if ($class) {
            $this->_attributes['class'] = $tempClass;
        }

        return '<' . $this->_tag . $attributesStr . '>';
    }

    public function renderClose() {
        return '</' . $this->_tag . '>';
    }
}