<?php

namespace fpcm\modules\nkorg\slider\controller;

final class edit extends base {

    public function request()
    {
        parent::request();
        if (!$this->id || !$this->obj->exists()) {
            return $this->redirect('slider/list');
        }

        return true;
    }

    public function process()
    {
        $this->view->setFormAction('slider/edit&id='.$this->id);
        $this->view->addButton( (new \fpcm\view\helper\linkButton('toOverview'))->setUrl($this->getControllerLink('slider/list'))->setText('GLOBAL_BACK')->setIcon('chevron-circle-left')->setIconOnly(true) );
        parent::process();
    }

}
