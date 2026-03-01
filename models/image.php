<?php

namespace fpcm\modules\nkorg\slider\models;

include_once \fpcm\classes\loader::libGetFilePath('intervention/image');

class image extends \fpcm\model\abstracts\dataset
implements \JsonSerializable {

    use \fpcm\module\tools;

    /**
     *
     * @var string
     */
    protected string $headline = '';

    /**
     *
     * @var string
     */
    protected string $description = '';

    /**
     *
     * @var string
     */
    protected string $imagepath = '';

    /**
     *
     * @var string
     */
    protected int $cropping = 1;

    /**
     *
     * @var int
     */
    protected int $position = 1;

    /**
     *
     * @var int
     */
    protected int $starttime = 0;

    /**
     *
     * @var int
     */
    protected int $stoptime = 0;

    /**
     *
     * @var int
     */
    protected int $visible = 1;

    /**
     *
     * @var int
     */
    protected int $slider_id = 0;

    /**
     *
     * @param int|null $id
     */
    public function __construct(?int $id = null) {
        $this->table = $this->getObject()->getFullPrefix('images');
        parent::__construct($id);
        $this->data['basePath'] = $this->getObject()->getDataPath();
        $this->data['baseURL'] = \fpcm\classes\dirs::getDataUrl(rtrim($this->getObject()->getFullPrefix(), '_')  , '');
    }

    public function getHeadline(): string {
        return $this->headline;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getImagepath(bool $full = false): string {

        if ($full && $this->imagepath) {
            return $this->data['baseURL'] . $this->imagepath;
        }

        return $this->imagepath;
    }

    public function getPosition(): int {
        return $this->position;
    }

    public function getStarttime(bool $format = false): int|string {

        if ($format) {
            return $this->starttime ? date('Y-m-d', $this->starttime) : '';
        }

        return $this->starttime;
    }

    public function getStoptime(bool $format = false): int|string {

        if ($format) {
            return $this->stoptime ? date('Y-m-d', $this->stoptime) : '';
        }

        return $this->stoptime;
    }

    public function getVisible(): bool {
        return (bool) $this->visible;
    }

    public function setHeadline(string $headline) {
        $this->headline = $headline;
        return $this;
    }

    public function setDescription(string $description) {
        $this->description = $description;
        return $this;
    }

    public function setImagepath(string $imagepath) {
        $this->imagepath = $imagepath;
        return $this;
    }

    public function setPosition(int $position) {
        $this->position = $position;
        return $this;
    }

    public function setStarttime(int $starttime) {
        $this->starttime = $starttime;
        return $this;
    }

    public function setStoptime(int $stoptime) {
        $this->stoptime = $stoptime;
        return $this;
    }

    public function setVisible(bool $visible = false) {
        $this->visible = (int) $visible;
        return $this;
    }

    public function getCropping(): int {
        return $this->cropping;
    }

    public function setCropping(int $cropping) {
        $this->cropping = $cropping;
        return $this;
    }
    
    public function getSliderId(): int {
        return $this->slider_id;
    }

    public function setSliderId(int $slider_id) {
        $this->slider_id = $slider_id;
        return $this;
    }
    
    /**
     * Executes save process to database and events
     * @return bool|int
     * @since 4.1
     */
    public function save()
    {
        if (!$this->dbcon->insert($this->table, $this->getPreparedSaveParams())) {
            return false;
        }

        $this->id = $this->dbcon->getLastInsertId();
        return $this->id;
    }

    /**
     *
     * @return bool|int
     */
    public function update()
    {
        $params = $this->getPreparedSaveParams();
        $fields = array_keys($params);

        $params[] = $this->getId();

        $return = false;
        if ($this->dbcon->update(
                $this->table,
                $fields,
                array_values($params),
                'id = ?'
            )
        ) {
            $return = true;
        }

        return $return;
    }

    public function createSmallImage()
    {
        $lg = $this->data['basePath'] . $this->imagepath;
        $sm = $this->getSmallImagePath($lg);

        if (file_exists($sm) && !unlink($sm)) {
            return false;
        }

        return $this->resize($lg, $sm, $this->getObject()->getOption('img_width_sm'), $this->getObject()->getOption('img_height_sm'));
    }

    /**
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        $path = $this->getImagepath(true);

        return [
            'headline' => $this->headline,
            'description' => $this->description,
            'imagepaths' => [
                'lg' => $path,
                'sm' => $this->getSmallImagePath($path)
            ],
            'align' => $this->getCroppingAlign()
        ];
    }

    public function getSmallImagePath(string $path)
    {
        return preg_replace('/^(.*)(\.[a-z]{3})$/i', '$1.sm$2', $path);
    }

    public function deleteImage()
    {
        $lg = $this->data['basePath'] . $this->imagepath;
        $sm = $this->getSmallImagePath($lg);
        
        if (is_dir($lg) || is_dir($sm)) {
            return false;
        }
        
        if (!\fpcm\model\files\ops::isValidDataFolder($lg) ||
            !\fpcm\model\files\ops::isValidDataFolder($sm)) {
            return false;
        }
        
        return unlink($lg) && unlink($sm);
    }

    public function resize(string $from, string $to, int $w, int $h)
    {
        if (!trim($to)) {
            $to = $from;
        }

        if (!\fpcm\model\files\ops::isValidDataFolder($to)) {
            return false;
        }

        $crop = $this->getCroppingAlign();

        try {
            $mgr = new \Intervention\Image\ImageManager( \Intervention\Image\Drivers\Gd\Driver::class );
            $img = $mgr->read($from);
            $img->coverDown($w, $h, $crop);
            $img->save($to);
        } catch (Exception $exc) {
            trigger_error('Error while creating file thumbnail ' . $sm . PHP_EOL.$exc);
            return false;
        }
    }

    protected function getEventModule(): string {
        return '';
    }
    
    private function getCroppingAlign()
    {
        return match ($this->cropping) {
            1 => 'center',
            2 => 'right',
            default => 'left'
        };
    }

}
