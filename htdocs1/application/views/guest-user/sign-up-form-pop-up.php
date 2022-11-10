<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$userTypeArray = array('userType' => User::USER_TYPE_LEANER);
if (isset($userType) && !empty($userType)) {
    $userTypeArray = array('userType' => $userType);
}
?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_Register'); ?></h2>
    <?php $this->includeTemplate('guest-user/_partial/learner-social-media-signup.php', $userTypeArray); ?>
    <?php
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-sm-';
    $frm->developerTags['fld_default_col'] = 12;
    $frm->setFormTagAttribute('onsubmit', 'setUpSignUp(this); return(false);');
    $fldFirstName = $frm->getField('user_first_name');
    $fldFirstName->developerTags['col'] = 6;
    $fldLastName = $frm->getField('user_last_name');
    $fldLastName->developerTags['col'] = 6;
    $fldPassword = $frm->getField('user_password');
    $fldPassword->changeCaption('');
    $fldConfirmPassword = $frm->getField('conf_new_password');
    $fldPassword->captionWrapper = (array(Label::getLabel('LBL_Password') . '<span class="spn_must_field">*</span><a onClick="togglePassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password'), '</a>'));

    /* Confrim Password*/
    $fldConfirmPassword->captionWrapper = (array(Label::getLabel('LBL_Confirm_Password') . '<span class="spn_must_field">*</span><a onClick="toggleConfirmPassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">','</a>'));

    /* [ */
    $termLink = ' <a target="_blank" class = "-link-underline link-color" href="' . $termsAndConditionsLinkHref . '">' . Label::getLabel('LBL_TERMS_AND_CONDITION') . '</a> and <a href="' . $privacyPolicyLinkHref . '" target="_blank" class = "-link-underline link-color" >' . Label::getLabel('LBL_Privacy_Policy') . '</a>';
    $terms_caption = '<span>' . $termLink . '</span>';
    $frm->getField('agree')->addWrapperAttribute('class', 'terms_wrap');
    $frm->getField('agree')->htmlAfterField = $terms_caption;
    echo $frm->getFormHtml();
    ?>
    <div class="-align-center">
        <p><?php echo Label::getLabel('LBL_Already_have_an_account?'); ?> <a href="javascript:void(0);" onClick="logInFormPopUp()" class="-link-underline link-color"><?php echo Label::getLabel('LBL_Sign_In'); ?></a></p>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#termLabelWrapper label').addClass('field_resp_block');
        $('#termLabelWrapper label').append('<?php echo $termLink; ?>');
    })
</script>