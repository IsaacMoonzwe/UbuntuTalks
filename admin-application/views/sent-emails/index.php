<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Sent_Emails_List', $adminLangId); ?> </h5>
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
                            $srchFrm->setFormTagAttribute('onsubmit', 'searchSentEmails(this,1); return(false);');
                            $srchFrm->setFormTagAttribute('class', 'web_form');
                            $srchFrm->developerTags['colClassPrefix'] = 'col-md-';
                            $srchFrm->developerTags['fld_default_col'] = 6;
                            $fld = $srchFrm->getField('btn_clear');
                            $fld->addFieldTagAttribute('onclick', 'clearUserSearch()');
                            echo $srchFrm->getFormHtml();
                        ?>
                    </div>
                </section>
                <section class="section">
                    <!-- <div class="sectionhead">
                        <h4><?php //echo Label::getLabel('LBL_Sent_Emails_List', $adminLangId); ?> </h4>
                    </div> -->
                    <div class="sectionbody">
                        <div class="tablewrap" >
                            <div id="emails-list"> <?php echo Label::getLabel('LBL_Processing...', $adminLangId); ?></div>
                        </div> 
                    </div>
                </section>
            </div>		
        </div>
    </div>
</div>
