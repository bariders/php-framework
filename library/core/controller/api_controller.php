<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class ApiController extends Controller
{
    public function invoke()
    {

    }

    protected function _format($array)
    {
        if ($_GET['format'] == 'xml') {
            header('Content-Type: text/xml');
            echo $this->_echoAsXML($array);
        } else {
            echo $this->_echoASJSON($array);
        }
    }



    protected function _echoAsXMLOLD($array)
    {
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($array, array($this, '_flip'), $xml);
        return $xml->asXML();
    }

    protected function _echoAsXML($array) {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");
        $this->_echoAsXMLWrapper($array, $xml);
        return $xml->asXML();
    }

    protected function _echoAsXMLWrapper($array, &$xml)
    {

        foreach($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml->addChild("$key");
                    $this->_echoAsXMLWrapper($value, $subnode);
                } else{
                    $this->_echoAsXMLWrapper($value, $xml);
                }
            } else {
                $xml->addChild("$key","$value");
            }
        }

    }


    protected function _echoASJSON($array)
    {
        $temp = json_encode($array);
        return str_replace('\u0000', '', $temp);
    }

    private function _flip($value, $key, $xml)
    {
        if (is_numeric($key)) {
            $key = 'entry';
        }
        $value = str_replace('&', '&amp;', $value);
        $xml->addChild($key, $value);
    }
}
