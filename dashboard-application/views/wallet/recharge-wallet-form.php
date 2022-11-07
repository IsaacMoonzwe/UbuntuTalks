<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmRechargeWallet->setFormTagAttribute('onSubmit', 'setUpWalletRecharge(this); return false;');
$frmRechargeWallet->setFormTagAttribute('class', 'form');
$frmRechargeWallet->developerTags['colClassPrefix'] = 'col-sm-';
$frmRechargeWallet->developerTags['fld_default_col'] = 12;
?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_Recharge_Wallet'); ?></h2>
    <?php echo $frmRechargeWallet->getFormHtml(); ?>
</div>