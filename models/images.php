<?php

namespace fpcm\modules\nkorg\slider\models;

class images extends \fpcm\model\abstracts\tablelist
{
    use \fpcm\module\tools;

    public function __construct() {
        $this->table = $this->getObject()->getFullPrefix('images');
        return parent::__construct();
    }

    public function getAll(int $filter) {

        if (isset($this->data[__FUNCTION__.$filter])) {
            return $this->data[__FUNCTION__];
        }

        $this->data[__FUNCTION__.$filter] = [];

        $obj = new \fpcm\model\dbal\selectParams($this->table);
        $obj->setFetchAll(true);

        switch ($filter) {
            case 1:
                $where = ' visible = 1';
                break;
            case 0:
                $where = ' visible = 0';
                break;
            default:
                $where = 'id > 0';
                break;
        }

        $obj->setWhere($where);

        $result = $this->dbcon->selectFetch($obj);
        if (!is_array($result) || !count($result)) {
            return $this->data[__FUNCTION__.$filter];
        }

        foreach ($result as $value) {
            $item = new image;
            $item->createFromDbObject($value);
            $this->data[__FUNCTION__.$filter][$item->getId()] = $item;
        }

        return $this->data[__FUNCTION__.$filter];
    }

    public function getFiles(string $term) : array
    {
        if (isset($this->data[__FUNCTION__])) {
            return $this->data[__FUNCTION__];
        }

        $term = preg_replace('/[^\w\d\.\-\_]*/i', '', $term);
        
        if (!trim($term)) {
            return [];
        }

        $files = glob(sprintf("%s*%s*", $this->getObject()->getDataPath(), $term));
        if (!is_array($files)) {
            return [];
        }

        $files = array_filter($files, fn($f) => !str_contains($f, '.sm.'));
        
        $this->data[__FUNCTION__.$term] = $files;

        return $this->data[__FUNCTION__.$term];
    }

    public function getVisible(int $id = 0) : array
    {

        if (isset($this->data[__FUNCTION__])) {
            return $this->data[__FUNCTION__];
        }

        $this->data[__FUNCTION__] = [];

        $t = time();

        $obj = new \fpcm\model\dbal\selectParams($this->table);
        $obj->setFetchAll(true);
        $obj->setWhere('slider_id = ? AND visible = 1 AND ( (starttime = 0 OR starttime <= ?) AND (stoptime = 0 OR stoptime >= ?) )' . $this->dbcon->orderBy(['position ASC']));
        $obj->setParams([$id, $t, $t]);

        $result = $this->dbcon->selectFetch($obj);
        if (!is_array($result) || !count($result)) {
            return $this->data[__FUNCTION__];
        }

        foreach ($result as $value) {
            $item = new image;
            $item->createFromDbObject($value);
            $this->data[__FUNCTION__][] = $item;
        }

        return $this->data[__FUNCTION__];
    }

}
