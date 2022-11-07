<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_Request_Withdrawal'); ?></h2>
    <?php
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-lg-6 col-md-';
    $frm->developerTags['fld_default_col'] = 6;
    $frm->setFormTagAttribute('onsubmit', 'setupWithdrawalReq(this); return(false);');
    $withdrawalAmount = $frm->getField('withdrawal_amount');
    $withdrawalAmount->addFieldTagAttribute('placeholder', CommonHelper::getPlaceholderForAmtField());
    $methodTypeFld = $frm->getField('withdrawal_payment_method_id');
    $methodTypeFld->setOptionListTagAttribute('class', 'list-inline list-inline--onehalf');
    $methodTypeFld->setWrapperAttribute('class', 'col-sm-12 col-lg-12');
    $methodTypeFld->developerTags['col'] = 12;
    $methodTypeFld->addFieldTagAttribute('onChange', 'getWithdrwalRequestForm(this.value);');
    $pmethodCodeFld = $frm->getField('pmethod_code');
    $pmethodCode = $pmethodCodeFld->value;
    switch ($pmethodCode) {
        case PaymentMethods::BANK_PAYOUT_KEY:
            $ifscFld = $frm->getField('ub_ifsc_swift_code');
            $ifscFld->setWrapperAttribute('class', 'col-sm-12 col-lg-12');
            $ifscFld->developerTags['col'] = 12;
            break;
        case PaypalPayout::KEY_NAME:
            $commentFld = $frm->getField('withdrawal_comments');
            $commentFld->setWrapperAttribute('class', 'col-sm-12 col-lg-12');
            $commentFld->developerTags['col'] = 12;
            break;
    }
    $submitBtnFld = $frm->getField('btn_submit');
    $cancelBtnFld = $frm->getField('btn_cancel');
    $cancelBtnFld->setFieldTagAttribute('onClick', 'closeForm()');
    echo $frm->getFormHtml();
    ?>
</div>