<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DOHtmlFormCheckBox extends DoHtmlForm
{
    public function __construct($name = null, $value = null, $checked = null, $attributes = [])
    {
        $this->_attributes = $attributes;
        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'checkbox');
        $this->setAttribute('value', 'true');
        $this->setAttribute('checked', $checked);
        $this->setTag('input');
    }

    public function render($class = null)
    {
        return $this->renderOpen($class);
    }
}
