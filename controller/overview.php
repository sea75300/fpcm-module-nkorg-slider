<?php

namespace fpcm\modules\nkorg\slider\controller;

final class overview extends \fpcm\controller\abstracts\module\controller {

    use \fpcm\controller\traits\common\dataView;

    /**
     *
     * @var \fpcm\components\dataView\dataView
     */
    protected $dataView;

    public function process()
    {
        $this->view->addButtons([
            (new \fpcm\view\helper\linkButton('add'))
                ->setText('GLOBAL_NEW')
                ->setIcon('calendar-plus')
                ->setUrl(\fpcm\classes\tools::getControllerLink('slider/add')),
        ]);

        $this->view->addJsFiles([
            \fpcm\module\module::getJsDirByKey($this->getModuleKey(), 'module.js')
        ]);

        $filter = $this->request->fromGET('active');
        if ($filter === null) {
            $filter = -1;
        }

        $this->view->addTabs('nkorgslider', [
            (new \fpcm\view\helper\tabItem('main'))
                ->setText($this->addLangVarPrefix('HEADLINE'))
                ->setFile( \fpcm\view\view::PATH_COMPONENTS . 'dataview__inline.php' )
        ]);

        $this->items = (new \fpcm\modules\nkorg\slider\models\images)->getAll($filter);
        $this->initDataView();

        $this->view->assign('headline', $this->addLangVarPrefix('HEADLINE'));
        $this->view->render();
        return true;
    }

    protected function getDataViewCols(): array
    {
        return [
            (new \fpcm\components\dataView\column('button', ''))->setSize(1)->setAlign('center'),
            (new \fpcm\components\dataView\column('headline', $this->addLangVarPrefix('GUI_TEXT')))->setSize(4),
            (new \fpcm\components\dataView\column('position', $this->addLangVarPrefix('GUI_POSITION')))->setSize(1)->setAlign('center'),
            (new \fpcm\components\dataView\column('visible', $this->addLangVarPrefix('GUI_VISIBLE')))->setSize(1)->setAlign('center'),
            (new \fpcm\components\dataView\column('start', $this->addLangVarPrefix('GUI_DATE_START')))->setSize(2)->setAlign('center'),
            (new \fpcm\components\dataView\column('stop', $this->addLangVarPrefix('GUI_DATE_STOP')))->setSize(2)->setAlign('center'),
            (new \fpcm\components\dataView\column('sid', $this->addLangVarPrefix('GUI_NUMBER')))->setSize(1)->setAlign('center'),
        ];
    }

    /**
     *
     * @param \fpcm\modules\nkorg\slider\models\image $item
     * @return \fpcm\components\dataView\row
     */
    protected function initDataViewRow($item)
    {
        $buttons = [
            (new \fpcm\view\helper\editButton('edit'.$item->getId()))->setUrl($this->getControllerLink('slider/edit', ['id' => $item->getId()])),
            (new \fpcm\view\helper\deleteButton('delete'.$item->getId()))->setData(['id' => $item->getId(), 'fn' => 'delete']),
        ];
        
        
        $start = $item->getStarttime() ? (new \fpcm\view\helper\dateText( $item->getStarttime() ) ) : '-';
        $stop = $item->getStoptime() ? (new \fpcm\view\helper\dateText( $item->getStoptime() ) ) : '-';

        return new \fpcm\components\dataView\row([
            new \fpcm\components\dataView\rowCol('button', implode(' ', $buttons) ),
            new \fpcm\components\dataView\rowCol('headline', '<span class="d-block text-truncate" title="'.$item->getHeadline().'">'.$item->getHeadline().'</span>' ),
            new \fpcm\components\dataView\rowCol('position', $item->getPosition()),
            new \fpcm\components\dataView\rowCol('visible', (new \fpcm\view\helper\boolToText('visible'.$item->getId()))->setValue($item->getVisible()) ),
            new \fpcm\components\dataView\rowCol('start', $start ),
            new \fpcm\components\dataView\rowCol('stop', $stop ),
            new \fpcm\components\dataView\rowCol('sid', $item->getSliderId() ),

        ]);
    }

    protected function getDataViewName()
    {
        return 'nkorgslider';
    }

    protected function getViewPath() : string
    {
        return \fpcm\view\view::PATH_COMPONENTS.'dataview';
    }

    public function isAccessible(): bool
    {
        return true;
    }

}
