<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$frm->setFormTagAttribute('class', 'form form--normal');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'confirmOrder(this); return(false);');
$fldSubmit = $frm->getField('btn_submit');
$fldSubmit->setFieldTagAttribute('class', 'btn btn--secondary btn--large');
?>
<div>
    <h5>
        <?php
        $headingStr = Label::getLabel('LBL_Pay_using_{payment-method-name}');
        echo str_replace('{payment-method-name}', $paymentMethod["pmethod_name"], $headingStr);
        ?>
    </h5><br>
    <p><?php echo $paymentMethod["pmethod_description"] ?><br /><br /></p>
    <h6><?php
        echo Label::getLabel('LBL_Net_Payable_:');
        echo CommonHelper::displayMoneyFormat($netAmmount);
        ?>
        <?php if (CommonHelper::getCurrencyId() != CommonHelper::getSystemCurrencyId()) { ?>
            <br><br>
            <p class="-color-secondary"><?php echo CommonHelper::currencyDisclaimer($siteLangId, $netAmmount); ?></p>
        <?php } ?>
    </h6>
    <span class="-gap"></span>
    <?php
    if (!isset($error)) {
        echo $frm->getFormHtml();
    }
    ?>
</div>
<script>
    $("document").ready(function () {
<?php if (isset($error)) { ?>
            $.systemMessage(<?php echo $error; ?>);
<?php } ?>
    });
</script>