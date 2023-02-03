<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="content-panel">
    <div class="content-panel__head border-bottom margin-bottom-5">
        <div class="d-flex align-items-center justify-content-between">
            <div><h5><?php echo Label::getLabel('LBL_Delete_Account', $siteLangId); ?></h5></div>
            <div></div>
        </div>
    </div>
    <div class="content-panel__body">
        <div class="form">
            <div class="form__body">
                <div class="account-deactivation-info">
                    <h6 class="margin-bottom-2"><?php echo Label::getLabel('LBL_delete_Account_confirmation', $siteLangId); ?></h6>
                    <p><?php echo Label::getLabel('LBL_Delete_Account_Description', $siteLangId) ?></p>				 
                    <span class="-gap"></span>
                    <div class="btns-group">
                        <a href="javascript:void(0)" onclick="deleteAccount();" class="btn bg-primary"><?php echo Label::getLabel('LBL_Delete_My_Account', $siteLangId); ?></a>
                    </div>
                    <span class="-gap"></span>
                </div>
            </div>		
        </div>	
    </div>
</div>