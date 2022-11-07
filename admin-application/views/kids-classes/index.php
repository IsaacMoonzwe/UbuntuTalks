<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon">
                                <i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Kids_Classes', $adminLangId); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...', $adminLangId); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php
                        $frmSrch->setFormTagAttribute('onsubmit', 'searchGroupClasses(this); return(false);');
                        $frmSrch->setFormTagAttribute('class', 'web_form');
                        $frmSrch->developerTags['colClassPrefix'] = 'col-md-';
                        $frmSrch->developerTags['fld_default_col'] = 4;
                        $submitBtnFld = $frmSrch->getField('btn_submit');
                        $submitBtnFld->setFieldTagAttribute('class', 'btn--block');
                        $btnReset = $frmSrch->getField('btn_reset');
                        $btnReset->addFieldTagAttribute('onclick', 'clearSearch()');
                        echo $frmSrch->getFormHtml();
                        ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Kids_Classes_List', $adminLangId); ?> </h4>
                    </div>
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listItems">
                                <?php echo Label::getLabel('LBL_Processing...', $adminLangId); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>