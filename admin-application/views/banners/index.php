<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmSearch->setFormTagAttribute('class', 'web_form last_td_nowrap');
$frmSearch->setFormTagAttribute('onsubmit', 'searchLocations(this); return(false);');
$frmSearch->developerTags['colClassPrefix'] = 'col-md-';
$frmSearch->developerTags['fld_default_col'] = 4;
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Banner_Locations', $adminLangId); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php echo $frmSearch->getFormHtml(); ?>    
                    </div>
                </section>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Banner_Locations_List', $adminLangId); ?> </h4>
                    </div>
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing"> <?php echo Label::getLabel('LBL_Processing...', $adminLangId); ?></div>
                        </div> 
                    </div>
                </section>
            </div>		
        </div>
    </div></div>
