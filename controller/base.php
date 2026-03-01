<?php

namespace fpcm\modules\nkorg\slider\controller;

class base extends \fpcm\controller\abstracts\module\controller
implements \fpcm\controller\interfaces\requestFunctions {

    /**
     *
     * @var \fpcm\modules\nkorg\slider\models\image
     */
    protected $obj;

    /**
     *
     * @var int
     */
    protected $id;

    public function request() {

        $this->id = $this->request->getID();
        if (!$this->id) {
            $this->id = null;
        }
        
        $this->obj = new \fpcm\modules\nkorg\slider\models\image($this->id);

        return true;
    }

    public function process()
    {
        $this->view->addButton(new \fpcm\view\helper\saveButton('save'));
        
        if ($this->obj->getImagepath()) {
            $this->view->addButton(
                    (new \fpcm\view\helper\submitButton('deleteImage'))
                    ->setText($this->addLangVarPrefix('GUI_DELETE_IMAGE') )
                    ->setIcon('trash')
            );
        }

        $this->view->addTabs('slider-editor', [
            (new \fpcm\view\helper\tabItem('editor'))
                ->setModulekey($this->getModuleKey())
                ->setFile( \fpcm\view\view::PATH_MODULE . 'editor')
                ->setText($this->addLangVarPrefix('HEADLINE'))
        ], 'ui-tabs-function-autoinit');

        $this->view->addJsFiles([
            \fpcm\module\module::getJsDirByKey($this->getModuleKey(), 'editor.js')
        ]);

        $this->view->assign('obj', $this->obj);
        
        return true;
    }
    
    protected function onDeleteImage()
    {
        if (!$this->obj->deleteImage()) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_DELETE_FILE'));
            return false;            
        }

        $this->obj->setImagepath('');
        if (!$this->obj->update()) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_SAVE'));
            return false;
        }

        $this->view->addNoticeMessage($this->addLangVarPrefix('MSG_SUCCESS_SAVE'));
        return true;        
        
    }

    protected function onSave()
    {
        $data = $this->request->fromPOST('obj', [
            \fpcm\model\http\request::FILTER_TRIM,
            \fpcm\model\http\request::FILTER_STRIPSLASHES,
            \fpcm\model\http\request::FILTER_STRIPTAGS
        ]);

        if (!is_array($data) || !count($data)) {
            $this->redirect('timeline/list');
            return false;
        }

        $this->obj = new \fpcm\modules\nkorg\slider\models\image($this->id ? $this->id : null);

        $data['start'] = trim($data['start']) ? strtotime($data['start']) : 0;
        $data['stop'] = trim($data['stop']) ? strtotime($data['stop']) : 0;

        if ($data['start'] && $data['stop'] && $data['start'] >= $data['stop']) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_SAVE_DATE'));
            return false;
        }
        
        $this->obj
                ->setHeadline($data['headline'])
                ->setDescription($data['description'])
                ->setImagepath($data['url'])
                ->setPosition((int) $data['position'])
                ->setSliderId((int) $data['number'])
                ->setVisible((bool) $data['visible'])
                ->setStarttime($data['start'])
                ->setStoptime($data['stop'])
                ->setCropping((int) $data['cropping']);

        if (!$this->obj->getId()) {

            if (!$this->obj->save()) {
                $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_SAVE'));
                return false;
            }

            $this->id = $this->obj->getId();

            $this->redirect('slider/edit', [
                'id' => $this->id
            ]);

            return true;
        }

        $this->id = $this->obj->getId();
        $this->uploadImage();

        if (!$this->obj->update()) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_SAVE'));
            return false;
        }

        $this->view->addNoticeMessage($this->addLangVarPrefix('MSG_SUCCESS_SAVE'));
        return true;
    }

    public function isAccessible(): bool
    {
        return true;
    }

    protected function getViewPath() : string
    {
        return 'editor';
    }

    private function uploadImage() : bool
    {
        $img = $this->request->fromFiles('entry_img');
        if ($this->id == 0 || $img === null) {
            return false;
        }

        $tmpFile = $img['tmp_name'] ?? false;
        if ($tmpFile === false || !is_uploaded_file($tmpFile)) {
            return false;
        }

        $realType = \fpcm\model\files\image::retrieveRealType($tmpFile);
        if ($realType !== $img['type']) {
            return false;
        }

        $ext = match ($realType) {
            'image/png' => '.png',
            'image/jpeg' => '.jpg',
            default => false
        };

        if (!$ext) {
            return false;
        }

        $to = $this->getObject()->getDataPath() . $img['name'];

        if (!\fpcm\model\files\ops::isValidDataFolder($to)) {
            return false;
        }

        if (file_exists($to) && !unlink($to)) {
            return false;
        }

        $moved = move_uploaded_file($tmpFile, $to);
        if (!$moved) {
            trigger_error(sprintf("Unable to move temp file %s to %s", $tmpFile, $to));
            return false;
        }

        $this->obj->setImagepath($img['name']);
        
        $this->obj->resize($to, '', $this->getObject()->getOption('img_width_lg'), $this->getObject()->getOption('img_height_lg'));

        $this->obj->createSmallImage();
        
        return true;
    }

}
