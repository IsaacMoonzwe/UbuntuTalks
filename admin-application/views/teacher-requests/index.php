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
                            <h5><?php echo Label::getLabel('LBL_Manage_Teacher_Requests', $adminLangId); ?> </h5>
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
                        $frmSrch->setFormTagAttribute('onsubmit', 'searchTeacherRequests(this,1); return(false);');
                        $frmSrch->setFormTagAttribute('class', 'web_form');
                        $frmSrch->developerTags['colClassPrefix'] = 'col-md-';
                        $frmSrch->developerTags['fld_default_col'] = 6;
                        $fld = $frmSrch->getField('btn_clear');
                        $fld->addFieldTagAttribute('onclick', 'clearTeacherRequestSearch()');
                        echo $frmSrch->getFormHtml();
                        ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing">
                                <?php echo Label::getLabel('LBL_Processing...', $adminLangId); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<script >
    var STATUS_APPROVED = <?php echo TeacherRequest::STATUS_APPROVED; ?>;
    var STATUS_CANCELLED = <?php echo TeacherRequest::STATUS_CANCELLED; ?>;
</script>