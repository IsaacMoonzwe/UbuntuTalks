<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='container container-fluid'>
        <div class="row">
            <div class="col-lg-12 col-md-12 space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Commision_Report', $adminLangId); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...', $adminLangId); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap">
                        <?php
                        $frm->setFormTagAttribute('onsubmit', 'searchCommissionReport(this); return false;');
                        $frm->setFormTagAttribute('class', 'web_form');
                        echo $frm->getFormHtml();
                        ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Commission_Report', $adminLangId); ?> </h4>
                        <div class="label--note text-right">
                            <strong class="-color-secondary span-right">
                                <?php echo Label::getLabel('LBL_REFUNDS_/_CANCELLATIONS_COMMISSION_REPORT_NOTE') ?>
                                <span class="spn_must_field">*</span>
                            </strong>
                        </div>
                    </div>
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="commisionReportList"> <?php echo Label::getLabel('LBL_Processing...', $adminLangId); ?></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>