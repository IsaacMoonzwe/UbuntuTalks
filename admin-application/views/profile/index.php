<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">  
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_My_Profile', $adminLangId); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                    <div class="row" id="profileInfoFrmBlock">
                        <?php echo Label::getLabel('LBL_Loading..', $adminLangId); ?>
                    </div>
                </div>	           
            </div>     
        </div>
    </div>
</div
