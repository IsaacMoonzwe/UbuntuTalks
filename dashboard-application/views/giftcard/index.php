<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmSrch->setFormTagAttribute('onSubmit', 'cartListing(this); return false;');
$frmSrch->setFormTagAttribute('class', 'form form--small');
$frmSrch->developerTags['colClassPrefix'] = 'col-md-';
$frmSrch->developerTags['fld_default_col'] = 4;
$fld = $frmSrch->getField('keyword');
$fld->setWrapperAttribute('class', 'col-md-4');
$fld = $frmSrch->getField('giftcard_status');
$fld->setWrapperAttribute('class', 'col-md-4');
$submitFld = $frmSrch->getField('btn_submit');
$submitFld->setWrapperAttribute('class', 'col-md-4');
$clearFld = $formData->getField('clear');
$clearFld->setFieldTagAttribute('class', '');
$clearFld->setFieldTagAttribute('id', 'clear');
// giftCard Purchased
$gcbuyerNameField = $formData->getField('gcbuyer_name');
$gcbuyerEmailField = $formData->getField('gcbuyer_email');
$gcbuyerPhoneField = $formData->getField('gcbuyer_phone');
$priceField = $formData->getField('giftcard_price');
$gcrecipientNameField = $formData->getField('gcrecipient_name');
$gcrecipientEmailField = $formData->getField('gcrecipient_email');
$systemCurrencyData = CommonHelper::getSystemCurrencyData();
?>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1><?php echo Label::getLabel('LBL_Gift_Card'); ?></h1>
            </div>
            <div class="col-sm-auto">
                <div class="buttons-group d-flex align-items-center">
                    <a href="javascript:void(0)" class="btn bg-secondary slide-toggle-js">
                        <svg class="icon icon--clock icon--small margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use>
                        </svg>
                        <?php echo Label::getLabel('LBL_Search'); ?>
                    </a>
                    <a href="javascript:void(0);" onclick="$('#addGiftcardFrm').toggle()" class="btn color-secondary btn--bordered margin-left-4"><?php echo Label::getLabel('LBL_Giftcards_Purchased'); ?></a>
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
        <div class="page-panel margin-top-5 padding-6" style="display:none;" id="addGiftcardFrm">
            <div class="row">
                <div class="col-md-5 col-xl-4">
                    <div class="gift-card">
                        <div class="gift-card__body">
                            <div class="card-info">
                                <div class="card-media">
                                    <img src="<?php echo FatUtility::generateFullUrl('Image', 'siteWhiteLogo', array($siteLangId), CONF_WEBROOT_FRONT_URL); ?>" alt="<?php echo Label::getLabel('LBL_Gift_card'); ?>">
                                </div>
                                <h6><?php echo Label::getLabel('LBL_Gift_Card_for'); ?></h6>
                                <a  href="javascript:void(0);" class="btn btn--light btn-round color-primary" style="margin: 2px;">
                                    <span><?php echo $systemCurrencyData['currency_symbol_left']; ?></span>
                                    <span class="giftcardPrice">00</span>
                                    <span><?php echo $systemCurrencyData['currency_symbol_right']; ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-xl-8">
                    <div class="gift-card-info">
                        <?php echo $formData->getFormTag(); ?>
                        <div class="row">
                            <div class="col-md-12 col-xl-4">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $gcbuyerNameField->getCaption(); ?>
                                            <?php if ($gcbuyerNameField->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $gcbuyerNameField->getHTML(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $gcbuyerEmailField->getCaption(); ?>
                                            <?php if ($gcbuyerEmailField->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $gcbuyerEmailField->getHTML(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $gcbuyerPhoneField->getCaption(); ?>
                                            <?php if ($gcbuyerPhoneField->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $gcbuyerPhoneField->getHTML(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $priceField->getCaption(); ?>
                                            <?php if ($priceField->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $priceField->getHTML(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $gcrecipientNameField->getCaption(); ?>
                                            <?php if ($gcrecipientNameField->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $gcrecipientNameField->getHTML(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $gcrecipientEmailField->getCaption(); ?>
                                            <?php if ($gcrecipientEmailField->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $gcrecipientEmailField->getHTML(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row submit-row">
                            <div class="col-sm-auto">
                                <div class="field-set margin-bottom-0">
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php
                                            echo $formData->getFieldHtml('save');
                                            echo $formData->getFieldHtml('clear');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                        <?php echo $formData->getExternalJs(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ PAGE PANEL ========= -->
        <div class="page-content" id="giftcardListing">
        </div>
        <!-- ] -->
    </div>
