<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
load_file('/app/view/public/index_public_view.php');

class IndexPublicController extends Controller {

    public function invoke()
    {
        $view = new IndexPublicView();
        $view->showIndex();
    }
}
