<?php

namespace fpcm\modules\nkorg\slider\events;

final class api extends \fpcm\module\api {

    public function getSliderData()
    {
        return (new \fpcm\modules\nkorg\slider\models\images())->getVisible($this->data['args'][0] ?? 0);
    }

}
