<?php

namespace fpcm\modules\nkorg\slider\controller;

final class add extends base {

    public function process()
    {
        $this->view->setFormAction('slider/add');
        parent::process();
    }

}
