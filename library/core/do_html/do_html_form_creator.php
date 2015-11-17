<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class DOHtmlFormCreator
{
    static public function input($name, $type, $value = null, $attributes = [])
    {
        $input = new DOHtmlFormInput($name, $type, $value, $attributes);
        return $input->render();
    }

    static public function hidden($name, $value = null, $attributes = [])
    {
        $input = new DOHtmlFormInput($name, 'hidden', $value, $attributes);
        return $input->render();
    }

    static public function select($name, $values = [], $selectedValue, $attributes = [])
    {
        $select = new DOHtmlFormSelect($name, $values, $selectedValue, $attributes);
        return $select->render();
    }

    static public function textArea($name, $text = null, $attributes = [])
    {
        $textArea = new DOHtmlFormTextArea($name, $text, $attributes);
        return $textArea->render();
    }

    static public function checkBox($name, $value)
    {
        if ($value && $value != 'false') {
            $checkBox = new DOHtmlFormCheckBox($name, 'true', true);
        } else {
            $checkBox = new DOHtmlFormCheckBox($name, 'true');
        }
        return $checkBox->render();
    }

    static public function radioButton()
    {

    }

    static public function progress()
    {

    }

    static public function submit($text, $attributes = [])
    {
        $button = new DOHtmlFormButton('submit', $text, $attributes);
        return $button->render();
    }

    static function open($name, $action, $method = 'post', $attributes = [])
    {
        $htmlForm = new DOHtmlForm($name, $action, $method, $attributes);
        return $htmlForm->renderOpen();
    }

    static function close()
    {
        return '</form>';
    }    
}
