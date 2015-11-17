<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
load_file('/app/view/public/standard_public_view.php');

class IndexPublicView extends StandardPublicView
{
    public function showIndex()
    {
        $this->loadTmplVar('CONTENT', '/app/tmpl/haml/public/index/index.haml');
        $this->showTmpl();
    }

    public function show()
    {
    }
}
