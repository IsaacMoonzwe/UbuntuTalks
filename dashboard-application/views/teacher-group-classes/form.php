<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'groupClassesFrm');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 2;
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$dateformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT', FatUtility::VAR_STRING, 'Y-m-d');
$timeformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT_TIME', FatUtility::VAR_STRING, 'H:i');
$frm->getField('grpcls_start_datetime')->setFieldTagAttribute('data-fatdatetimeformat', $dateformat . ' ' . $timeformat);
$getTimeSlotsField =  $frm->getField('grpcls_duration');
$submit = $frm->getField('submit');
$submit->developerTags['col'] = 12;
$fld = $frm->getField('grpcls_max_learner');
$fld->developerTags['col'] = 12;
$fld = $frm->getField('grpcls_classes_type');
$fld->developerTags['col'] = 6;
$fld = $frm->getField('grpcls_tlanguage_id');
$fld->developerTags['col'] = 6;
$fld = $frm->getField('grpcls_entry_fee');
$fld->developerTags['col'] = 6;
$fld = $frm->getField('grpcls_start_datetime');
$fld->developerTags['col'] = 6;
$fld = $frm->getField('grpcls_duration');
$fld->developerTags['col'] = 12;
$fld = $frm->getField('grpcls_title');
$fld->developerTags['col'] = 12;
$fld = $frm->getField('grpcls_slug');
if (!is_null($fld)) {
    $fld->developerTags['col'] = 12;
}
$fld = $frm->getField('grpcls_description');
$fld->developerTags['col'] = 12;
?>
<div class="popup">
    <div class="popup__head">
        <h4><?php echo Label::getLabel("LBL_Add_Group_Class") ?></h4>
        <div class="tabs tabs--line border-bottom-0">
            <ul>
                <li class="is-active"><a href="javascript:void(0)"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                <?php foreach ($languages as $langId => $language) { ?>
                    <li><a href="javascript:void(0)" <?php if ($grpclsId > 0) { ?> onclick="editGroupClassLangForm(<?php echo $grpclsId ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $language['language_name']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="popup__body">
        <?php echo $frm->getFormHtml(); ?>
    </div>
</div>
<script>
    var currentDate = '<?php echo $currentTime ?>';
    var currentTime = '<?php echo date('H:i', strtotime($currentTime)) ?>';
</script>