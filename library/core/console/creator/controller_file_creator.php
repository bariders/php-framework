<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
load_file('/library/core/console/creator/file_creator.php');

class ControllerFileCreator extends FileCreator {

    public function createModelControllerFile($objName, $app)
    {
        $app = ucfirst($app);
        if ($app == '') {
            $app = 'Public';
        }
        $objName .= 's';

        $controllerTmpl = file_get_contents(ROOT . '/library/core/console/tmpl/obj_controller.tmpl');
        $controllerTmpl = str_replace('{CLASS_NAME}',     $objName,     $controllerTmpl);
        $controllerTmpl = str_replace('{APP}',            $app,         $controllerTmpl);

        $controllerName = NamingConvention::camelCaseToSnakeCase($objName) . '_' .  lcfirst($app) . '.php';
        $controllerPath = ROOT . '/app/controller/' . lcfirst($app) . '/' . $controllerName;

        Debug::out('create: ' . $controllerPath);
        file_put_contents($controllerPath, $controllerTmpl);
    }
}
