<?php

namespace fpcm\modules\nkorg\slider\controller;

final class autocomplete extends \fpcm\controller\abstracts\module\ajaxController {

    public function request(): bool
    {
        return true;
    }
    
    public function process()
    {
        $term = $this->request->fetchAll('term', [
            \fpcm\model\http\request::FILTER_STRIPTAGS,
            \fpcm\model\http\request::FILTER_STRIPSLASHES,
            \fpcm\model\http\request::FILTER_TRIM,
            \fpcm\model\http\request::FILTER_URLDECODE
        ]);
        
        if ($term === null) {
            $this->response->setReturnData([])->fetch();
        }
        
        $files = (new \fpcm\modules\nkorg\slider\models\images)->getFiles($term);

        $ret = [];
        
        /* @var \fpcm\model\articles\article $article */
        foreach ($files as $file) {
            
            $bn = basename($file);
            
            $ret[] = [
                'value' => $bn,
                'label' => $bn
            ];
        }        

        $this->response->setReturnData($ret)->fetch();
        return true;
    }

}
