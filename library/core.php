<?php
/**
 * Copyright (c) 2015 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
/**************************
* D E F I N I T I O N E N *
***************************/

define('EXT', '.php');
if ($_SERVER['DOCUMENT_ROOT']) {
    define('ROOT',          $_SERVER['DOCUMENT_ROOT'] . '/..');
    define('APP_ROOT',      $_SERVER['DOCUMENT_ROOT'] . '/../app');
    define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
}

/****************************
* I N C L U D E - F I L E S *
*****************************/

//Config laden
load_file('/config/config.php');

//Libary laden
load_file('/library/core/debug.php');
load_file('/library/core/object.php');

load_file('/library/core/db/database.php');
load_file('/library/core/db/database_exception.php');
load_file('/library/core/db/database_sql.php');
load_file('/library/core/db/database_sql_insert.php');
load_file('/library/core/db/database_sql_update.php');
load_file('/library/core/db/database_object.php');
load_file('/library/core/db/database_object_creator.php');
load_file('/library/core/db/repository.php');

load_file('/library/core/controller/controller.php');
load_file('/library/core/controller/api_controller.php');

load_file('/library/core/view.php');
load_file('/library/core/default_view.php');
load_file('/library/core/template.php');
load_file('/library/core/cache.php');
load_file('/library/core/mobile.php');

load_file('/library/core/button.php');

load_file('/library/core/do_html/do_html_tag.php');
load_file('/library/core/do_html/do_html_form.php');
load_file('/library/core/do_html/do_html_form_input.php');
load_file('/library/core/do_html/do_html_form_check_box.php');
load_file('/library/core/do_html/do_html_form_select.php');
load_file('/library/core/do_html/do_html_form_text_area.php');
load_file('/library/core/do_html/do_html_form_button.php');
load_file('/library/core/do_html/do_html_form_creator.php');

load_file('/library/core/simple_bootstrap_form.php');

load_file('/library/core/do_date_time.php');
load_file('/library/core/do_service.php');
load_file('/library/core/mail.php');

load_file('/library/core/naming_convention.php');
load_file('/library/core/dispatcher.php');

load_file('/library/vendor/HamlPHP/HamlPHP.php');
load_file('/library/vendor/HamlPHP/Storage/FileStorage.php');

/************
* FUNCTIONS *
*************/

/*
function __autoload($className)
{
    if (Dispatcher::loadClass($className)) {
        echo 'Warning: __autoload could not find "' . $className . '"<br>';
    };
}
*/

function load_file($relativPath, $showError = true)
{
    $absolutePath = ROOT . $relativPath;
    if (file_exists($absolutePath)) {
        require_once($absolutePath);
        return false;
    } else if ($showError) {
        if (class_exists('Debug')) {
            Debug::out('Warning: load_file() could not find "' . $absolutePath . '"<br> in '
            . debug_backtrace()[0]['file'] . ' on line ' . debug_backtrace()[0]['line']);
        } else {
            echo 'Warning: load_file() could not find "' . $absolutePath . '" in '
            . debug_backtrace()[0]['file'] . ' on line ' . debug_backtrace()[0]['line'];
        }
    }
    return true;
}


/****************************
* Error Handling via E-MAIL *
*****************************/
function do_error_handler($number, $message, $file, $line, $vars)
{
    $mail = "
        <p>An error ($number) occurred on line
        <strong>$line</strong> and in the <strong>file: $file.</strong>
        <p> $message </p>";
    $mail .= "<pre>" . print_r($vars, 1) . "</pre>";

    $headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    if ( ($number !== E_NOTICE) && ($number < 2048) ) {
        error_log($mail, 1, 'wieschendorf@deinoyten.de', $headers);
        die("Ein fehler ist aufgetreten. Bitte schaue später noch einmal vorbei.");
    }
}
//set_error_handler('do_error_handler');

//HamlPHP
$parser = new HamlPHP(new FileStorage(ROOT . HAML_CACHE_PATH));
