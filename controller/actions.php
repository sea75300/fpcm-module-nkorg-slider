<?php

namespace fpcm\modules\nkorg\slider\controller;

final class actions extends \fpcm\controller\abstracts\module\ajaxController {

    /**
     *
     * @var \fpcm\view\message
     */
    protected $msg;

    public function process()
    {
        $res = $this->processByParam();
        if ($res === self::ERROR_PROCESS_BYPARAMS) {
            $this->response->setCode(400)->fetch();
        }

        $this->response->setReturnData($this->msg)->fetch();
        return true;
    }

    protected function processDelete() {

        $id = $this->request->fromPOST('id', [ \fpcm\model\http\request::FILTER_CASTINT ]);
        if (!$id) {
            $this->msg = new \fpcm\view\message($this->addLangVarPrefix('MSG_ERROR_DELETE'), \fpcm\view\message::TYPE_ERROR);
            return false;
        }

        $obj = new \fpcm\modules\nkorg\slider\models\image($id);
        if (!$obj->exists()) {
            $this->msg = new \fpcm\view\message($this->addLangVarPrefix('MSG_ERROR_DELETE'), \fpcm\view\message::TYPE_ERROR);
            return false;
        }

        if (!$obj->delete()) {
            $this->msg = new \fpcm\view\message($this->addLangVarPrefix('MSG_ERROR_DELETE'), \fpcm\view\message::TYPE_ERROR);
            return false;
        }

        $this->msg = new \fpcm\view\message($this->addLangVarPrefix('MSG_SUCCESS_DELETE'), \fpcm\view\message::TYPE_NOTICE);
        return true;
    }

}
