<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
// search wallet form start
$frmSrch->setFormTagAttribute('onsubmit', 'searchCredits(this); return(false);');
$frmSrch->setFormTagAttribute('class', 'form form--small');
$frmSrch->developerTags['colClassPrefix'] = 'col-md-';
$frmSrch->developerTags['fld_default_col'] = 3;
$keyFld = $frmSrch->getField('keyword');
$keyFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_Keyword'));
$keyFld = $frmSrch->getField('debit_credit_type');
$keyFld = $frmSrch->getField('date_from');
$keyFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_From_Date'));
$keyFld = $frmSrch->getField('date_to');
$keyFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_To_Date', $siteLangId));
$btnReset = $frmSrch->getField('btn_reset');
$btnReset->addFieldTagAttribute('onclick', 'clearSearch()');
?>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1><?php echo Label::getLabel('LBL_My_Wallet'); ?></h1>
            </div>
            <div class="col-sm-auto">
                <div class="buttons-group d-flex align-items-center">
                    <a href="javascript:void(0)" class="btn bg-secondary slide-toggle-js">
                        <svg class="icon icon--clock icon--small margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use>
                        </svg>
                        <?php echo Label::getLabel('LBL_Search_Transactions'); ?>
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
        <div class="page-content">
            <div class="wallet-box page-container margin-top-5 margin-bottom-5 padding-8">
                <div class="row justify-content-between align-items-center">
                    <div class="col-sm-4">
                        <div class="wallet d-flex">
                            <div class="wallet__media">
                                <svg class="icon icon--wallet icon--large margin-right-4">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#wallet-large'; ?>"></use>
                                </svg>
                            </div>
                            <div class="wallet__content">
                                <span class="margin-0"><?php echo Label::getLabel('LBL_Wallet_Balance'); ?></span>
                                <h3 class="bold-700"><?php echo CommonHelper::displayMoneyFormat($userTotalWalletBalance); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-auto col-lg-8  col-12">
                        <div class="buttons-group d-flex align-items-center">
                            <a href="javascript:void(0);" onclick="rechargeWalletForm();" class="btn btn--transparent color-primary margin-1">
                                <svg class="icon icon--issue icon--small margin-right-2">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#plus'; ?>"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_ADD_MONEY_TO_WALLET'); ?>
                            </a>
                            <a href="javascript:void(0);" onclick="redeemGiftcardForm();" class="btn btn--transparent color-primary margin-1">
                                <svg class="icon icon--gift margin-right-1">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#giftcards'; ?>"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_Redeem_Gift_Card'); ?>
                            </a>
                            <?php if ($can_withdraw) { ?>
                                <a href="javascript:void(0);" onclick="withdrwalRequestForm();"  class="btn btn--transparent color-primary margin-1">
                                    <svg class="icon icon--withdraw icon--small margin-right-2">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#withdraw'; ?>"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_Request_Withdrawal'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="creditListing" class="table-scroll">
            </div>
        </div>
        <!-- ] -->
    </div>