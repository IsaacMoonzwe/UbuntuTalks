<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'esclateIssueForm');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'esclateSetup(this); return(false);');
?>
<div class="box -padding-20">
    <h4><?php echo Label::getLabel('LBL_ESCLATE_ISSUE_TO_SUPPORT_TEAM'); ?></h4>    <br/>
    <?php echo $frm->getFormHtml(); ?>
</div>
