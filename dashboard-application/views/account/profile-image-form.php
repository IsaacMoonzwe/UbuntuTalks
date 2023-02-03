<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$profileImgFrm->setFormTagAttribute('action', CommonHelper::generateUrl('Account', 'setUpProfileImage'));
$profileImgFrm->setFormTagAttribute('onsubmit', 'sumbmitProfileImage(false); return(false);');
$profileImgFrm->setFormTagAttribute('id', 'frmProfile');
$profileImgFrm->setFormTagAttribute('class', 'form form--horizontal');
$profileImageField = $profileImgFrm->getField('user_profile_image');
if ($profileImgFrm->getField('us_video_link')) {
    $videoLinkField = $profileImgFrm->getField('us_video_link');
    $videoLinkField->addFieldTagAttribute('onblur', 'validateVideolink(this);');
    $videoLinkField->addFieldTagAttribute('placeholder', Label::getLabel('LBL_VIDEO_LINK_PLACEHOLDER'));
}
$nextButton = $profileImgFrm->getField('btn_next');
$nextButton->addFieldTagAttribute('onClick', 'sumbmitProfileImage(true); return(false);');
?>
<div class="padding-6">
    <div class="max-width-80">
        <?php
        echo $profileImgFrm->getFormTag();
        echo $profileImgFrm->getFieldHtml('update_profile_img');
        echo $profileImgFrm->getFieldHtml('rotate_left');
        echo $profileImgFrm->getFieldHtml('rotate_right');
        echo $profileImgFrm->getFieldHtml('remove_profile_img');
        echo $profileImgFrm->getFieldHtml('action');
        echo $profileImgFrm->getFieldHtml('img_data');
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label"><?php echo $profileImageField->getCaption(); ?>
                            <?php if ($profileImageField->requirement->isRequired()) { ?>
                                <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                        <small class="margin-0"><?php echo Label::getLabel('LBL_PROFILE_IMAGE_FIELD_INFO_TEXT'); ?></small>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <div class="profile-media">
                                <div class="avtar avtar--xlarge" data-title="<?php echo CommonHelper::getFirstChar($userFirstName); ?>">
                                    <?php
                                    if ($isProfilePicUploaded) {
                                        echo '<img src="' . CommonHelper::generateUrl('Image', 'user', array($userId, 'MEDIUM'), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"  alt="' . $userFirstName . '" />';
                                    }
                                    ?>
                                </div>
                                <div class="buttons-group margin-top-4">
                                    <span class="btn btn--bordered color-primary btn--small btn--fileupload btn--wide margin-right-2">
                                        <?php
                                        echo $profileImageField->getHTML();
                                        echo ($isProfilePicUploaded) ? Label::getLabel('LBL_Edit') : Label::getLabel('LBL_Add');
                                        ?>
                                    </span>
                                    <?php if (true == $isProfilePicUploaded) { ?>
                                        <a class="btn btn--bordered color-red btn--small btn--wide" href="javascript:void(0);" onClick="removeProfileImage()"><?php echo Label::getLabel('LBL_Remove'); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (isset($videoLinkField)) { ?>
            <div class="row margin-top-5 margin-bottom-5">
                <div class="col-md-12">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label"><?php echo $videoLinkField->getCaption(); ?> 
                                <?php if ($videoLinkField->requirement->isRequired()) { ?>
                                    <span class="spn_must_field">*</span>
                                <?php } ?>
                            </label>
                            <small class="margin-0"><?php echo Label::getLabel('LBL_PROFILE_VIDEO_FIELD_INFO'); ?></small>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $videoLinkField->getHTML(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row submit-row">
            <div class="col-sm-auto">
                <div class="field-set">
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php
                            echo $profileImgFrm->getFieldHtml('btn_submit');
                            echo $nextButton->getHTML('btn_next');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php echo $profileImgFrm->getExternalJS(); ?>
    </div>
</div>