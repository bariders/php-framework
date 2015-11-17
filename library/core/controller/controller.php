<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

abstract class Controller
{
    /**
     * Fuehrt einen redirect aus. Verwendet aber nicht den StatusCode 301
     * sondern 302.
     * Das heißt es handelt sich nicht um eine permanete Umleitung. Die alte
     * und die neue Url werden z.B. als zwei unterschiedliche Seiten
     * von Suchmaschiene behandelt.
     *
     * @param string $url Die URL zu der umgeleitet werden soll.
     */
    static public function redirect($url)
    {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri$url");
        exit;
    }

    static public function redirect404($url)
    {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri$url");
        exit;
    }


    /**
     * Fuehrt eine Seitenumleiteung durch. Vewendet wird der Statuscode 301.
     * Das bedeutet, dass es sich um eine "echte" Umleitung handelt.
     * Beide Seiten, die alte und die neue, erscheinen fuer den Aufrufer, wie
     * zum Beispiel google, als die selbe Seite.
     *
     * @param string $url Die url zu der umgeleitet werden soll.
     */
    public function redirect301($url)
    {
        header ("HTTP/1.1 301 Moved Permanently");
        header ("Location: $url");
        exit();
    }

    /**
     * Fuert einen 301 Redirect aus, wenn die URL kein www am Anfang
     * beinhaltet, so das die URL danach ein www hat.
     */
    public function redirectAddWWW301()
    {
        $servername = $_SERVER['SERVER_NAME'];
        $url = 'http://www.' . $_SERVER['SERVER_NAME']
                . $_SERVER['REQUEST_URI'];
        if ($servername != 'localhost' && substr_count($servername, '.') == 1) {
            if (substr($servername, 0 , 3) != 'www') {
                $this->redirect301($url);
            }
        }
    }


    public function goToUrl($url)
    {
        self::redirect($url);
    }

    public function goToModule($module = '', $app = '')
    {
        $button = new Button($module, $app);
        self::redirect($button->getLink());
    }

    static public function goToAction($action, $module = '', $app = '')
    {
        //$button = new Button($module, $app);
        //$button->setAction($action);
        //self::redirect($button->getLink());
    }

    public function goToButton($button)
    {
        $this->redirect($button->getLink());
    }


    /*
    protected function _getSessionValue($name, $environment = 'global')
    {
        //PHP 5.5 only
//        if (session_status() == PHP_SESSION_NONE) {
//            session_start();
//        }

        //PHP 5.2
        session_start();

        if (isset($_SESSION[$environment][$name])) {
            return $_SESSION[$environment][$name];
        }
    }
    */

    static function getSessionValue($name, $environment = 'global')
    {
        session_start();
        if (isset($_SESSION[$environment][$name])) {
            return $_SESSION[$environment][$name];
        }
    }

    /*
    protected function _setSessionValue($value, $name, $environment = 'global')
    {
        session_start();
        return $_SESSION[$environment][$name] = $value;
    }
    */


    static function setSessionValue($value, $name, $environment = 'global')
    {
        session_start();
        return $_SESSION[$environment][$name] = $value;
    }


    static function getValue($name, $environment = 'global')
    {
        if (isset($_GET[$name])) {
            self::setSessionValue($_GET[$name], $name, $environment);
        }
        return self::getSessionValue($name, $environment);
    }

    /**
     * Muss von einem anderen Controller implementiert werden. Die
     * Hauptmethode die aufgrufen werden soll.
     *
     * @param void
     */
    abstract public function invoke();




    public function invokeDefault()
    {
        $objClassname = $this->_getObjClassname();
        $repoClassname = $objClassname . 'Repository';

        load_file('/app/repository/' . strtolower($objClassname) . '_repo.php');

        $repo = new $repoClassname;
        $obj = $repo->get($_GET['id']);
        $objStruct = $repo->getStructure();

        foreach($objStruct as $key => $value) {
            $columnNames[] = $key;
        }

        $values['id'] = $obj->getId();
        foreach($columnNames as $param) {
            $getFunktion = 'get' . $param;
            $values[$param] = $obj->$getFunktion();
        }

        $view = new DefaultView();
        $view->showDefault($objClassname, $values);
    }


    private function _getObjClassname()
    {
        $classname = get_class($this);;
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $classname, $matches);
        for ($i = 0; $i < count($matches[0])-2; $i++) {
            $objClassname .= $matches[0][$i];
        }
        $objClassname = substr($objClassname, 0, -1);
        return $objClassname;
    }

    public function invokeDefaultIndex()
    {
        $objClassname = $this->_getObjClassname();
        load_file('/app/repository/' . strtolower($objClassname) . '_repo.php');

        $repoClassname = $objClassname . 'Repository';
        $repo = new $repoClassname;
        $objs = $repo->getAll();
        $objStruct = $repo->getStructure();

        foreach($objStruct as $key => $value) {
            $columnNames[] = $key;
        }

        $rows = array();
        foreach($objs as $obj) {
            $value = array();
            $value[] = $obj->getId();
            foreach($columnNames as $param) {
                $getFunktion = 'get' . $param;
                $value[] = $obj->$getFunktion();
            }
            $rows[] = $value;
        }

        $view = new DefaultView();
        $view->showDefaultIndex($objClassname, $columnNames, $rows);
    }

    public function invokeDefaultNew($url = '')
    {
        $objClassname = $this->_getObjClassname();
        $repoClassname = $objClassname . 'Repository';
        load_file('/app/repository/' . strtolower($objClassname) . '_repo.php');

        $obj = new $objClassname();
        $repo = new $repoClassname;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $obj->loadFromArray($_POST);
            $id = $repo->add($obj);

            if ($url) {
                $this->goTo($url);
            } else {
                $this->goToUrl(Button::module($objClassname . 's'));
            }
        }

        $objStruct = $repo->getStructure();
        $view = new DefaultView();
        $view->addHamlVar('objForm', new SimpleBootstrapForm($obj));

        foreach($objStruct as $key => $value) {
            $columnNames[] = $key;
        }

        $view->showDefaultNew($objClassname, $columnNames);
    }

    public function invokeDefaultEdit($url = '')
    {
        $objClassname = $this->_getObjClassname();
        $repoClassname = $objClassname . 'Repository';
        load_file('/app/repository/' . strtolower($objClassname) . '_repo.php');

        $repo = new $repoClassname;
        $obj = $repo->get($_GET['id']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $obj->loadFromArray($_POST);
            $id = $repo->update($obj);

            if ($url) {
                $this->goTo($url);
            } else {
                $this->goToUrl(Button::module($objClassname . 's'));
            }
        }

        $objStruct = $repo->getStructure();
        $view = new DefaultView();
        $view->addHamlVar('objForm', new SimpleBootstrapForm($obj));

        foreach($objStruct as $key => $value) {
            $columnNames[] = $key;
        }

        $view->showDefaultEdit($objClassname, $columnNames);
    }

    public function invokeDefaultDelete($url = '')
    {
        $objClassname = $this->_getObjClassname();
        $repoClassname = $objClassname . 'Repository';

        load_file('/app/repository/' . strtolower($objClassname) . '_repo.php');

        $repo = new $repoClassname;
        $obj = $repo->get($_GET['id']);
        $repo->delete($obj);

        if ($url) {
            $this->goTo($url);
        } else {
            $this->goToUrl(Button::module($objClassname . 's'));
        }
    }
}
