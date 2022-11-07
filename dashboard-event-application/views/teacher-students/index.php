<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmSrch->setFormTagAttribute('onsubmit', 'searchStudents(this); return(false);');
$frmSrch->setFormTagAttribute('class', 'form form--small');
$frmSrch->developerTags['colClassPrefix'] = 'col-md-';
$frmSrch->developerTags['fld_default_col'] = 4;
$btnReset = $frmSrch->getField('btn_reset');
$btnReset->addFieldTagAttribute('onclick', 'clearSearch()');
$btnSubmit = $frmSrch->getField('btn_submit');
$btnSubmit->attachField($btnReset);
?>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1> <?php echo Label::getLabel('Lbl_My_Students'); ?></h1>
            </div>
            <div class="col-sm-auto">
                <div class="buttons-group d-flex align-items-center">
                    <a href="javascript:void(0)" class="btn bg-secondary slide-toggle-js">
                        <svg class="icon icon--clock icon--small margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use>
                        </svg>
                        <?php echo Label::getLabel('Lbl_Search'); ?>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ FILTERS ========= -->
        <div class="search-filter slide-target-js" style="display: none;">
            <?php echo $frmSrch->getFormHtml(); ?>
        </div>
        <!-- ] ========= -->
    </div>
    <div class="page__body">
        <!-- [ PAGE PANEL ========= -->
        <div class="page-content" id="listItems">
        </div>
        <!-- ] -->
    </div>