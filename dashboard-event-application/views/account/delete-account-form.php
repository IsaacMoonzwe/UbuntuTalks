<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'delFrm');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 7;
$frm->setFormTagAttribute('autocomplete', 'off');
$frm->setFormTagAttribute('onsubmit', 'setUpGdprDelAcc(this); return(false);');
$reasonFld = $frm->getField('gdprdatareq_reason');
$submitBtn = $frm->getField('btn_submit');
$submitBtn->setFieldTagAttribute('form', $frm->getFormTagAttribute('id'));
?>
<div class="content-panel__head">
    <div class="d-flex align-items-center justify-content-between">
        <div><h5><?php echo Label::getLabel('LBL_Delete_Account_Form'); ?></h5></div>
        <div></div>
    </div>
</div>
<div class="content-panel__body">
    <div class="form">
        <div class="form__body padding-0">
            <div class="tabs-data">
                <div class="padding-6 padding-bottom-0">
                    <?php echo $frm->getFormTag(); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo $reasonFld->getCaption(); ?>
                                        <?php if ($reasonFld->requirement->isRequired()) { ?>
                                            <span class="spn_must_field">*</span>
                                        <?php } ?>
                                    </label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <?php echo $reasonFld->getHtml(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                    <?php echo $frm->getExternalJS(); ?>
                </div>
            </div>
        </div>
        <div class="form__actions">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                </div>
                <div>
                    <?php echo $frm->getFieldHTML('btn_submit'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
