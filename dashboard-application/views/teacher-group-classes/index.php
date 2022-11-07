<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$serachForm->setFormTagAttribute('id', 'serachForm');
$serachForm->setFormTagAttribute('class', 'form');
$serachForm->developerTags['colClassPrefix'] = 'col-md-';
$serachForm->developerTags['fld_default_col'] = 4;
$serachForm->setFormTagAttribute('onsubmit', 'searchGroupClasses(this); return(false);');
$btnReset = $serachForm->getField('btn_reset');
$btnReset->addFieldTagAttribute('onclick', 'clearSearch()');
?>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1><?php echo Label::getLabel('LBL_Group_Classes'); ?></h1>
            </div>
            <div class="col-sm-auto">
                <div class="buttons-group d-flex align-items-center">
                    <a href="javascript:void(0)" class="btn bg-secondary slide-toggle-js">
                        <svg class="icon icon--clock icon--small margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use>
                        </svg>
                        <?php echo Label::getLabel('Lbl_Search'); ?>
                    </a>
                    <a href="javascript:void(0);" onclick="form(0);" class="btn color-secondary btn--bordered margin-left-4"><?php echo Label::getLabel('LBL_Add'); ?></a>
                </div>
            </div>
        </div>
        <!-- [ FILTERS ========= -->
        <div class="search-filter slide-target-js" style="display: none;">
            <?php echo $serachForm->getFormHtml(); ?>
        </div>
        <!-- ] ========= -->
    </div>
    <div class="page__body">
        <!-- [ PAGE PANEL ========= -->
        <div class="page-content" id="listItems">
        </div>
        <!-- ] -->
    </div>