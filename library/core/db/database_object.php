<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DatabaseObject extends Object {
    protected $_id = -1;

    public function __construct($values = null)
    {
        if ($values) {
            $this->setId($values['id']);
            foreach ($values as $key => $value) {
                $name = '_' . $key;
                $this->$name = $value;
            }
        }
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function myType()
    {
        return get_class($this);
    }
}
