<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class Object {

    //Liefert ein Array in dem alle Methodennamen stehen. Erbt eine
    //Klasse von Objet so gibt diese Funktion auch die Namen alle
    //Kind und Eltern Methoden aus.
    private function _getMethods() {
        return get_class_methods($this->getThisClassType());
    }

    //Liefert ein Array in dem alle Methodennamen stehen, die
    //mit 'set' anfangen. Erbt eine
    //Klasse von Objcet so gibt diese Funktion auch die Namen alle
    //Kind und Eltern Methoden aus.
    private function _getSetMethods() {
        $methods = $this->_getMethods();
        if ($methods[0] != '') {
            foreach ($methods as $method) {
                if ($this->_isSetMethod($method)) {
                    $setMethods[] = $method;
                }
            }
        }
        return $setMethods;
    }

    //Ueberprueft ob eine String bzw eine Methode mit 'set' beginnt.
    private function _isSetMethod($method) {
        if (substr($method, 0, 3) == 'set') {
            return true;
        } else {
            return false;
        }
    }

    //Uebergibt man eine SetMethode so wird alles hinter dem 'set'
    //zurueckgegeben.
    private function _extractSetMethodName($method) {
        return substr($method, 3, strlen($method) - 3);
    }

    //Liefet den Typ diser Klasse, er eine Klasse von Object wird nicht Object
    //als Typ angegebn sondern der Name der Klasse die von Object erbt.
    public function getThisClassType() {
        return get_class($this);
    }

    //Nimmt die KeyIndizes von array und schaut ob es dazu eine passende set-Methode
    //gibt, wenn ja wird die setMethode aufgerufen mit dem Wert aus dem Array.
    public function loadFromArray($array) {
        $setMethods = $this->_getSetMethods();
        if ($setMethods[0] != '') {
            foreach ($setMethods as $setMethod) {

                $this->_setFromArray($array, $this->_extractSetMethodName($setMethod));
                //echo $this->_extractSetMethodName($setMethod) . '<br>';
            }
        }
    }

    public function loadFromPOST()
    {
        $post = $_POST;
        $array = array();
        foreach($post as $key => $value) {
            $camelCaseKey = NamingConvention::snakeCaseToCamelCase($key);
            $array[$camelCaseKey] = $value;
        }
        $this->loadFromArray($array);
    }

    public function loadFromObj($obj, $notOverwrite = '') {
        //Testen ob die Objekte vom selben Typ sind.
        if ($this->getThisClassType() == $obj->getThisClassType()) {
            $setMethods = $this->_getSetMethods();
            if ($setMethods[0] != '') {
                foreach ($setMethods as $setMethod) {
                    $overwrite = true;
                    if ($notOverwrite[0] != '') {
                        foreach ($notOverwrite as $saveMethodName) {
                            if ($this->_extractSetMethodName($setMethod) == $saveMethodName) {
                                $overwrite = false;
                                break;
                            }
                        }
                    }
                    if ($overwrite) {
                        $getMethod = 'get' . $this->_extractSetMethodName($setMethod);
                        $this->$setMethod($obj->$getMethod());
                        //echo '$this->' . $setMethod . '($obj->' . $getMethod . '());<br>';
                    }
                }
            }
        }
    }

    //Ruft eine setMethode nur dann auf, wenn es einen passenden Keyindex eintrag
    //ein einem Array gibt mit dem Wert aus dem Array.
    private function _setFromArray($array, $setMethodName) {
        $arraKeyName = NamingConvention::camelCaseToSnakeCase($setMethodName);
        if (isset($array[lcfirst($arraKeyName)])) {
            $setMethod = 'set' . $setMethodName;
            $this->$setMethod(stripslashes($array[lcfirst($arraKeyName)]));
        }
    }

}
