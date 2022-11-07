<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmOnlineContact->setFormTagAttribute('onSubmit', 'contactSubmit(this); return false;');
$frmOnlineContact->setFormTagAttribute('class', 'form');
$frmOnlineContact->developerTags['colClassPrefix'] = 'col-sm-';
$frmOnlineContact->developerTags['fld_default_col'] = 6;
?>
    <div class="box box--narrow">
        <h2 class="-align-center"><?php echo Label::getLabel('LBL_Contact_Form'); ?></h2>
        <?php //echo $frmOnlineContact->getFormHtml(); ?>
            <?php echo $frmOnlineContact->getFormTag() ?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo Label::getLabel('LBL_First_Name', $siteLangId) ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('first_name'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo Label::getLabel('LBL_Last_Name', $siteLangId) ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('last_name'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo Label::getLabel('LBL_Email_Address', $siteLangId) ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('email_address'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo Label::getLabel('LBL_Phone_No', $siteLangId) ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('phone_number'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo Label::getLabel('LBL_Group_Size', $siteLangId) ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('group_size'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo Label::getLabel('LBL_Group_Type', $siteLangId) ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('group_type'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo Label::getLabel('LBL_Language', $siteLangId) ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('Language'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo Label::getLabel('LBL_Others', $siteLangId) ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('others'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '') { ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="field-set">
                            <div class="field-wraper">
                                <div class="g-recaptcha" data-sitekey="<?php echo FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, ''); ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="field-set">
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $frmOnlineContact->getFieldHTML('btn_submit'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $frmOnlineContact->getExternalJS(); ?>
                            </form>
    </div>
    <script src='https://www.google.com/recaptcha/api.js'></script>