<?php /* @var $theView \fpcm\view\viewVars */ ?>
<?php /* @var $obj fpcm\modules\nkorg\slider\models\image */ ?>

<div class="row row-cols-4 py-2">
    <div class="col"></div>
    <div class="col"></div>
    <div class="col"></div>
    <div class="col">
        <div class="row g-2">
            <?php
            $theView->textInput('obj[number]')
                    ->setText('MODULE_NKORGSLIDER_GUI_NUMBER')
                    ->setValue($obj->getSliderId())
                    ->setLabelTypeFloat()
                    ->setType('number')
                    ->setBottomSpace('');
            ?>
        </div>
    </div>
</div>
<div class="row my-2">
    <div class="col">
        <div class="row g-2">
            <?php
            $theView->textInput('obj[headline]')
                    ->setText('MODULE_NKORGSLIDER_GUI_TEXT')
                    ->setValue($obj->getHeadline())
                    ->setLabelTypeFloat();
            ?>
        </div>

        <div class="row g-2">
            <?php
            $theView->textInput('obj[description]')
                    ->setText('MODULE_NKORGSLIDER_GUI_DESCRIPTION')
                    ->setValue($obj->getDescription())
                    ->setLabelTypeFloat();
            ?>
        </div>
    </div>
</div>

<div class="row row-cols-2 my-2">

    <div class="col">
        <div class="row">
            <?php
            $theView->boolSelect("obj[visible]")
                    ->setText('MODULE_NKORGSLIDER_GUI_VISIBLE')
                    ->setSelected($obj->getVisible());
            ?>
        </div>
    </div>

    <div class="col">
        <div class="row">
            <?php
            $theView->textInput('obj[position]')
                    ->setText('MODULE_NKORGSLIDER_GUI_POSITION')
                    ->setValue($obj->getPosition())
                    ->setType('number');
            ?>
        </div>
    </div>
</div>


<div class="row row-cols-2 py-2">

    <div class="col">
        <div class="row">
            <?php
            $theView->dateTimeInput('obj[start]')
                    ->setText('MODULE_NKORGSLIDER_GUI_DATE_START')
                    ->setValue($obj->getStarttime(true))
                    ->setNativeDate();
            ?>
        </div>
    </div>

    <div class="col">
        <div class="row">
            <?php
            $theView->dateTimeInput('obj[stop]')
                    ->setText('MODULE_NKORGSLIDER_GUI_DATE_STOP')
                    ->setValue($obj->getStoptime(true))
                    ->setNativeDate();
            ?>
        </div>
    </div>
</div>

<div class="row row-cols-2 py-2">
    <div class="col">
        <div class="row">
            <?php
            $theView->select("obj[cropping]")
                    ->setText('MODULE_NKORGSLIDER_GUI_CROPPING')
                    ->setSelected($obj->getCropping())
                    ->setOptions(['left' => 0, 'center' => 1, 'right' => 2])
                    ->setFirstOption(\fpcm\view\helper\select::FIRST_OPTION_DISABLED);
            ?>
        </div>
    </div>
</div>

<div class="row row-cols-2 py-2">
    <div class="col">
        <div class="row">
            <?php
            $theView->textInput('obj[url]')
                    ->setText('MODULE_NKORGSLIDER_GUI_URL')
                    ->setValue($obj->getImagepath());
            ?>
        </div>
    </div>
    <div class="col">
        <div class="row">
            <?php if ($obj->getId()) : ?>
            <div class="col">
                <input type="file" name="entry_img" class="form-control" accept="image/png, image/jpeg">                
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $imgP = $obj->getImagepath(true); ?>
<?php if ($imgP) : ?>
<div class="row py-2">
    <div class="col text-center">
        <img src="<?php print $imgP; ?>" role="presentation" class="rounded mx-auto d-block" >
    </div>
</div>

<div class="row py-2">
    <div class="col text-center">
        <img src="<?php print $obj->getSmallImagePath($imgP); ?>" role="presentation" class="rounded mx-auto d-block" >
    </div>
</div>
<?php endif; ?>