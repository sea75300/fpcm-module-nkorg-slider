<?php

namespace fpcm\modules\nkorg\slider\events\navigation;

final class add extends \fpcm\module\event {

    public function run() : \fpcm\module\eventResult
    {
        $item = (new \fpcm\model\theme\navigationItem())
                ->setDescription($this->addLangVarPrefix('HEADLINE'))
                ->setIcon('images')
                ->setUrl('slider/list');

        $this->data->add(\fpcm\model\theme\navigationItem::AREA_AFTER, $item);
        
        return (new \fpcm\module\eventResult())->setData($this->data);
    }

    public function init()
    {
        return true;
    }

}

