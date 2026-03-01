<?php

namespace fpcm\modules\nkorg\slider\migrations;

class v101 extends \fpcm\module\migration
{

    protected function alterTablesAfter(): bool
    {
        $addIndeices = method_exists($this->getDB(), 'addTableIndices');
        if (!$addIndeices) {
            return false;
        }

        if (method_exists($this->getObject() ,  'getTableObject' ) ) {
            $tab = $this->getObject()->getTableObject('images');
        }
        else {
            $t = $this->getObject()->getConfigPathFromCurrent('tables' . DIRECTORY_SEPARATOR . 'images.yml');
            if (!file_exists($t)) {
                return false;
            }

            $tab = $this->getObject()->getYaTdlObject($t);
        }

        if (!is_object($tab) || !$tab->parse()) {
            trigger_error('Unable to parase table definition file!');
            return false;
        }

        return $this->getDB()->addTableCols($tab) && $this->getDB()->addTableIndices($tab);
    }

}
