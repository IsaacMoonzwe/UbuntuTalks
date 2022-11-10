<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$langFrm->setFormTagAttribute('id', 'groupClassesLangFrm');
$langFrm->setFormTagAttribute('class', 'form');
$langFrm->developerTags['colClassPrefix'] = 'col-md-';
$langFrm->developerTags['fld_default_col'] = 6;
$langFrm->setFormTagAttribute('onsubmit', 'setupGroupClassLang(this); return(false);');
$submit = $langFrm->getField('submit');
$submit->developerTags['col'] = 12;
$submit = $langFrm->getField('grpclslang_grpcls_title');
$submit->developerTags['col'] = 12;
$submit = $langFrm->getField('grpclslang_grpcls_description');
$submit->developerTags['col'] = 12;
?>
<div class="box -padding-20">
    <!--page-head start here-->
    <div class="tabs-small tabs-offset tabs-scroll-js">
        <ul>
            <li><a href="javascript:void(0)" onclick="form('<?php echo $grpclsId ?>')"><?php echo Label::getLabel('LBL_General'); ?></a></li>
            <?php foreach ($languages as $lang_id => $language) { ?>
                <li class="<?php echo ($lang_id == $langId) ? 'is-active' : '' ?>"><a href="javascript:void(0)" onclick="editGroupClassLangForm('<?php echo $grpclsId ?>', '<?php echo $lang_id; ?>')"><?php echo $language; ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4><?php echo Label::getLabel("LBL_Add_Group_Class") ?></h4>
        </div>
        <div><a class="btn btn--small" href="javascript:void(0);" onclick="searchGroupClasses(document.frmSrch);"><?php echo Label::getLabel("LBL_Cancel") ?></a></div>
    </div>
    <span class="-gap"></span>
    <!--page-head end here-->
    <?php echo $langFrm->getFormHtml(); ?>
</div>