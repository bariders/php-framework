<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

abstract class StandardPublicView extends View {

    public function showTmpl($debug = false)
    {
        $this->setTemplate('/app/tmpl/haml/public/standard_tmpl.haml');
        parent::showTmpl($debug);
        $this->cacheView();
    }
}
