<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DOHtmlFormButton extends DoHtmlForm
{
    private $_text;

    public function __construct($type = null, $text = null, $attributes = [])
    {
        $this->_attributes = $attributes;
        $this->setAttribute('type', $type);
        $this->_text = $text;
        $this->setTag('button');
    }

    public function setText($text)
    {
        $this->_text = $text;
    }

    public function render($class = null)
    {
        $result .= $this->renderOpen($class);
        $result .= $this->_text;
        $result .= $this->renderClose();
        return $result;
    }
}
