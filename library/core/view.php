<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class View {
    private $_tempResult;
    private $_tmpl;
    private $_hamlVars = array();

    public function __construct($path = '')
    {
        if ($path) {
            $this->setTemplate($path);
        }
    }

    public function setTemplate($path)
    {
        if (!$this->_tmpl) {
            $this->_tmpl = new Template();
        }
        $this->_tmpl->loadTmpl($path);
    }

    public function flashMessage()
    {
        $success = Controller::getSessionValue('flash_success', 'flash');
        $error = Controller::getSessionValue('flash_error', 'flash');
        Controller::setSessionValue('', 'flash_success', 'flash');
        Controller::setSessionValue('', 'flash_error', 'flash');
        $this->loadTmplVar('FLASH', '/app/tmpl/haml/flash.haml');
        $this->addHamlVar('flashSuccess', $success);
        $this->addHamlVar('flashError', $error);
    }

    public function addTmplVar($name, $value)
    {
        if (!$this->_tmpl) {
            $this->_tmpl = new Template();
        }
        $this->_tmpl->setVar($name, $value);
    }

    public function addHamlVar($name, $value)
    {
        $this->_hamlVars[$name] = $value;
    }

    public function addTmplVars($prefix, $varArray)
    {
        if ($varArray) {
            foreach ($varArray as $key => $value) {
                $this->addTmplVar($prefix  .  strtoupper($key), $value);
            }
        }
    }

    public function getSubTmpl($name)
    {
        return $this->_tmpl->getSubTmpl($name);
    }

    public function loadTmplVar($varname, $path)
    {
        if (!$this->_tmpl) {
            $this->_tmpl = new Template();
        }
        $this->_tmpl->loadTmplVar($varname, $path);
    }


    public function showTmpl($debug = false)
    {
        $this->addHamlVar('view', $this);
        $this->flashMessage();

        $this->_tempResult =  $this->_tmpl->render();

        global $parser;
        $content = $parser->evaluate($this->_tempResult, $this->_hamlVars);
        if (COMPRESSION_GZIO == 'ON') {
            ob_start("ob_gzhandler");
        }
        echo $content;
    }

    public function render()
    {
        $this->addHamlVar('view', $this);
        $this->_tempResult =  $this->_tmpl->render();
        global $parser;
        $content = $parser->evaluate($this->_tempResult, $this->_hamlVars);
        return $content;
    }

    public function cacheView()
    {
        if ($this->_tempResult == '') {
           $this->_tempResult = $this->_tmpl->render();
        }
        $cache = new Cache();
        $cache->save($this->_tempResult);
    }

    public function getautoPath($root = '', $postfix, $sufix = 'tmpl.html')
    {
        $name = explode($postfix, get_class($this));
        return strtolower($root . $name[0] . '.' .$sufix);
    }

    public function renderImage($image, $attributes)
    {
        $imageFrame = $image->newAutoFillFrame($attributes['width'], $attributes['height']);
        return $this->renderImageFrame($imageFrame, $attributes);
    }

    public function renderImageFromFrame($imageFrame, $attributes)
    {
        if ($imageFrame) {
            if ($attributes['mode'] == 'fit') {
                $imageFrame = $imageFrame->getImage()->OnlineShop($attributes['width'], $attributes['height']);
            } else {
                $imageFrame = $imageFrame->getImage()->newAutoFillFrame($attributes['width'], $attributes['height']);
            }
        }
        return $this->renderImageFrame($imageFrame, $attributes);
    }


    public function renderImageFrame($imageFrame, $attributes)
    {
        if (!$attributes['scale']) {
            $attributes['scale'] = 1;
        }
        $width  = $attributes['width'] * $attributes['scale'];
        $height = $attributes['height'] * $attributes['scale'];

        if ($imageFrame) {
            if (!$attributes['alt']) {
                $attributes['alt'] = $imageFrame->getName();
            }
            $imgSrc = $imageFrame->render($width, $height, false, false, $attributes['type']);
            if (!$imgSrc) {
                $imgSrc = $attributes['default'];
            }
        } else {
            $imgSrc = $attributes['default'];
        }

        if ($attributes['domain']) {
            $imgSrc = 'http://' . $attributes['domain'] . $imgSrc;
        }

        if ($attributes['class']) {
            $class = 'class="' . $attributes['class'] . '"';
        }

        if ($attributes['alt']) {
            $alt = 'alt="' . $attributes['alt'] . '"';
        }

        $style = 'style = "width: ' . $width .'px; "';
        return '<img  ' . $style . ' ' . $class . ' src="' . $imgSrc . '" '  . $alt .'>';
    }
}
