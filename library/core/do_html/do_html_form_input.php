<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DOHtmlFormInput extends DoHtmlForm
{
    public function __construct($name = null, $type = null, $value = null, $attributes = [])
    {
        $this->_attributes = $attributes;
        $this->setAttribute('name', $name);
        $this->setAttribute('type', $type);
        $this->setAttribute('value', $value);
        $this->setTag('input');
    }

    public function render($class = null)
    {
        return $this->renderOpen($class);
    }
}
