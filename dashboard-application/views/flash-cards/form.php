<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'setUp(this); return(false);');
$fldTitle = $frm->getField('flashcard_title');
$fldTitle->developerTags['col'] = 6;
$fldSLanguageId = $frm->getField('flashcard_slanguage_id');
$fldSLanguageId->developerTags['col'] = 6;
$fldDefination = $frm->getField('flashcard_defination');
$fldDefination->developerTags['col'] = 6;
$fldDefSLanguageId = $frm->getField('flashcard_defination_slanguage_id');
$fldDefSLanguageId->developerTags['col'] = 6;
?>
<div class="box -padding-20">
    <h4><?php echo Label::getLabel('LBL_Set_Up_Flashcard'); ?></h4>
    <?php echo $frm->getFormHtml(); ?>
</div>