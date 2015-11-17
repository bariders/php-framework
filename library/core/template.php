<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class TemplateTest {

    public function test($path)
    {

        $tmpl = new Template();
        $tmpl->loadTmpl($path);

        $subTmpl = $tmpl->getSubTmpl('EVENT_VERSION1');
        $subTmpl->setVar('TITLE', 'Das ist ein Test');
        $list = $subTmpl->render();

        $tmpl->setVar('EVENTLIST', $list);
        echo $tmpl->render();

    }
}

class Template {

    private $_varList;
    private $_varListType;
    private $_analyzer;
    private $_elmentList = array();
    private $_subtmplList = array();

    public function setVarRaw($varname, $value = true)
    {
        $this->_varList[$varname] = $value;
        $this->_varListType[$varname] = 'TEXT';
    }

    public function setVar($varname, $value = true)
    {
        $analyzer = new Analyzer();
        $analyzer->analyzeString($value);
        $elementList = $analyzer->getElementList();
        $this->_subtmplList = array_merge($this->_subtmplList, $analyzer->getSubTmplList());
        $this->_varList[$varname] = $elementList;
        $this->_varListType[$varname] = 'ELEMENTLIST';
    }

    public function setTmplMultiVars($prefix, $varArray)
    {
        if ($varArray) {
            foreach ($varArray as $key => $value) {
                $this->setVar($prefix  .  strtoupper($key), $value);
            }
        }
    }

    public function loadTmplVar($varname, $path)
    {
        $analyzer = new Analyzer();
        $analyzer->analyzeFile($path);
        $elementList = $analyzer->getElementList();
        $this->_subtmplList = array_merge($this->_subtmplList, $analyzer->getSubTmplList());
        $this->_varList[$varname] = $elementList;
        $this->_varListType[$varname] = 'ELEMENTLIST';
    }

    public function getSubTmpl($name)
    {
        $tmpl = new Template();
        $tmpl->_elmentList = $this->_getSubTmplElementList($name);
        return $tmpl;
    }

    private function _getSubTmplElementList($name)
    {
        foreach ($this->_subtmplList as $element) {
            if ($element['ident'] == $name) {
                return $element['list'];
            }
        }
    }

    public function loadTmpl($path)
    {
        $this->_analyzer = new Analyzer();
        $this->_analyzer->analyzeFile($path);
        $this->_elmentList = $this->_analyzer->getElementList();
        $this->_subtmplList = $this->_analyzer->getSubTmplList();
    }

    public function render()
    {
        return $this->_renderElementList($this->_elmentList);
    }


    private function _renderElementList($list)
    {
        if ($list) {
            $str = '';
            foreach ($list as $element) {
                $type = $element['type'];
                $value = $element['value'];

                if ($type == 'STATIC') {
                    $str .=  $value;
                } elseif ($type == 'IF') {
                    if (isset($this->_varList[$element['ident']])) {
                        $str .= $this->_renderElementList($element['list']);
                    }
                } elseif ($type == 'IFNOT') {
                    if (!isset($this->_varList[$element['ident']])) {
                        $str .= $this->_renderElementList($element['list']);
                    }
                } elseif ($type == 'VAR') {
                    if (isset($this->_varListType[$element['ident']]) &&
                            $this->_varListType[$element['ident']] == 'TEXT') {
                        $str .= $this->_varList[$element['ident']];
                    } elseif(isset($this->_varListType[$element['ident']]) &&
                           $this->_varListType[$element['ident']] == 'ELEMENTLIST') {
                        $str .= $this->_renderElementList($this->_varList[$element['ident']]);
                    }
                }
            }
        }
        return $str;
    }

}

class Analyzer {

    private $_tokenizer;
    private $_subtmplList = array();
    private $_elementList = array();

    public function getElementList()
    {
        return $this->_elementList;
    }

    public function getSubTmplList()
    {
        return $this->_subtmplList;
    }

    public function analyzeFile($path)
    {
        $this->_tokenizer = new Tokenizer();
        $this->_tokenizer->openTemplateFile($path);
        $this->_elementList = $this->_makeElementList();
    }

    public function analyzeString($string)
    {
        $this->_tokenizer = new Tokenizer();
        $this->_tokenizer->setString($string);
        $this->_elementList = $this->_makeElementList();
    }

    private function _makeElementList()
    {
        $htmlElementList = array();
        $state = 0;
        while($this->_tokenizer->EOF == false) {
            $token = $this->_tokenizer->getNextToken();
            $type = $token['type'];
            $value = $token['value'];

            if ($state == 0 && $type == 'HTML') {
                $htmlElementList[] = $this->makeElement('STATIC', '', $value, '');
            } elseif ($state == 0 && $type == 'IDENT') {
                $htmlElementList[] = $this->makeElement('VAR', $value, '', '');
            } elseif ($state == 0 && $type == 'KEYWORD' && $value == 'IF') {
                $elemntType = 'IF';
                $state = 2;
            } elseif ($state == 0 && $type == 'KEYWORD' && $value == 'IFNOT') {
                $elemntType = 'IFNOT';
                $state = 2;
            } elseif ($state == 0 && $type == 'KEYWORD' && $value == 'SUBTMPL') {
                $state = 3;
            } elseif ($state == 3 && $type == 'IDENT') {
                $this->_subtmplList[] = $this->makeElement('SUBTMPL', $value, '', $this->_makeElementList());
                $state = 0;
            } elseif ($state == 2 && $type == 'IDENT') {
                $htmlElementList[] = $this->makeElement($elemntType, $value, '', $this->_makeElementList());
                $state = 0;
            } elseif ($state == 0 && $type == 'KEYWORD' && $value == 'ENDIF') {
                return $htmlElementList;
            } elseif ($state == 0 && $type == 'KEYWORD' && $value == 'ENDSUBTMPL') {
                return $htmlElementList;
            }
        }
        return $htmlElementList;
    }

    public function makeElement($type, $ident, $value, $elementList)
    {
        $element['type']    = $type;
        $element['ident']   = $ident;
        $element['value']   = $value;
        $element['list']    = $elementList;
        return $element;
    }
}


class Tokenizer {

    public $EOF;
    private $_fileContent;
    private $_filePointer;
    private $_fileLength;
    private $_state;

    public function openTemplateFile($path)
    {
        $this->_filePointer = -1;
        $this->EOF = false;

        if (substr($path, 0, 1) == '/') {
            $path = ROOT . $path;
        }

        if (file_exists($path)) {
            global $parser;
            $content = $parser->parseFile($path);
            $this->_fileContent = $content; //file_get_contents($path);
            $this->_fileLength = strlen($this->_fileContent);

            return 0;
        } else {
            return -1;
        }
    }

    public function setString($string)
    {
        $this->_filePointer = -1;
        $this->EOF = false;
        $this->_fileContent = $string;
        $this->_fileLength = strlen($this->_fileContent);
    }

    public function getNextToken()
    {
        $this->_filePointer++;
        $tokenValue = '';
        while ($this->_filePointer < $this->_fileLength) {
            $char = substr($this->_fileContent, $this->_filePointer, 1);
            if ($this->_state == 0 && $char != '{') {
                $tokenValue .= $char;
            } elseif ($this->_state == 0 && $char == '{') {
                $this->_state = 1;
                $token['type']  = 'HTML';
                $token['value'] = $tokenValue;
                return $token;
            } elseif ($this->_state == 1 && $char == '#') {
                $this->_state = 2;
            } elseif ($this->_state == 1 && !ctype_space($char)) {
                $tokenValue .= $char;
                $this->_state = 3;
            } elseif ($this->_state == 1 && ctype_space($char)) {
                $this->_state = 0;
                $tokenValue .= '{' . $char;
            } elseif ($this->_state == 1 && $char != '#') {
                $this->_state = 3;
            } elseif ($this->_state == 2 && ctype_space($char)) {
                $this->_state = 3;
                $token['type']  = 'KEYWORD';
                $token['value'] = $tokenValue;
                return $token;
            } elseif ($this->_state == 2 && $char == '}') {
                $this->_state = 0;
                $token['type']  = 'KEYWORD';
                $token['value'] = $tokenValue;
                return $token;
            } elseif ($this->_state == 2 && !ctype_space($char)) {
                $tokenValue .= $char;
            } elseif ($this->_state == 3 && $char != '}') {
                $tokenValue .= $char;
            } elseif ($this->_state == 3 && $char == '}') {
                $this->_state = 0;
                $token['type']  = 'IDENT';
                $token['value'] = $tokenValue;
                return $token;
            }

            $this->_filePointer++;
        }

        $token['type']  = 'HTML';
        $token['value'] = $tokenValue;
        $this->EOF = true;
        return $token;
    }
}
