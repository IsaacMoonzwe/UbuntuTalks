<?php
$frmSrch->setFormTagAttribute('onSubmit', 'threadListing(this); return false;');
$frmSrch->setFormTagAttribute('class', 'form form--small');
$frmSrch->developerTags['colClassPrefix'] = 'col-md-';
$frmSrch->developerTags['fld_default_col'] = 12;
$fld = $frmSrch->getField('keyword');
$fld->setWrapperAttribute('class', 'col-md-12');
$fld->addFieldTagAttribute('placeholder', $fld->getCaption());
$fld->changeCaption('');
$fld = $frmSrch->getField('is_unread');
$fld->setWrapperAttribute('class', 'col-md-12');
$fld->changeCaption('');
$submitFld = $frmSrch->getField('btn_submit');
$submitFld->setWrapperAttribute('class', 'col-md-12');
?>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="window">
    <div class="window__container">
        <div class="window__left">
            <div class="window__search">
                <a href="javascript:void(0)" class="window__search-field window__search-field-js">
                    <svg class="icon icon--search icon--small margin-right-4">
                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use>
                    </svg><?php echo Label::getLabel('LBL_Search'); ?>
                </a>
                <div class="window__search-form window__search-form-js">
                    <a href="javascript:void(0);" class="-link-close window__search-field-js"></a>
                    <?php echo $frmSrch->getFormHtml(); ?>
                </div>
            </div>
            <div class="scrollbar scrollbar-js">
                <div class="msg-list-container" id="threadListing">
                </div>
            </div>
        </div>
        <div class="window__right">
            <div class="message-display message-display--positioned">
                <div class="message-display__icon">
                    <svg viewBox="0 -26 512 512">
                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#chat'; ?>"></use>
                    </svg>
                </div>
                <p class="-color-light"><?php echo Label::getLabel('LBL_Click_on_message_to_see_details'); ?></p>
            </div>
            <div class="message-details message-details-js" style="display: none;">
            </div>
        </div>
    </div>
</div>