<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_Redeem_Giftcard'); ?></h2>
    <?php
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-sm-';
    $frm->developerTags['fld_default_col'] = 12;
    $frm->setFormTagAttribute('onsubmit', 'giftcardRedeem(this); return(false);');
    echo $frm->getFormHtml();
    ?>
</div>