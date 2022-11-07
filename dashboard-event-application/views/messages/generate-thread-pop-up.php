<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_Start_Conversation'); ?></h2>	
    <?php
    $frm->setFormTagAttribute('onSubmit', 'sendMessage(this); return false;');
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-md-';
    $frm->developerTags['fld_default_col'] = 12;
    echo $frm->getFormHtml();
    ?>
</div>
