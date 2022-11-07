<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$userTypeArray = array('userType' => User::USER_TYPE_LEANER);
if (isset($userType) && !empty($userType)) {
    $userTypeArray = array('userType' => $userType);
}
$fldPassword = $frm->getField('password');
$fldPassword->changeCaption('');
$fldPassword->captionWrapper = array(Label::getLabel('LBL_Password'), '<a onClick="toggleLoginPassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password') . '</a>');
?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_Login'); ?></h2>
    <?php $this->includeTemplate('guest-user/_partial/learner-social-media-signup.php', $userTypeArray); ?>
    <?php
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-sm-';
    $frm->developerTags['fld_default_col'] = 12;
    $frm->setFormTagAttribute('onsubmit', 'setUpLogin(this); return(false);');
    echo $frm->getFormHtml();
    ?>
    <div class="-align-center">
        <a href="<?php echo CommonHelper::generateUrl('GuestUser', 'ForgotPasswordForm'); ?>" class="-link-underline link-color"><?php echo Label::getLabel('LBL_Forgot_Password?'); ?></a>
    </div>
    <hr>
    <div class="-align-center">
        <p><?php echo Label::getLabel('LBL_Don\'t_have_an_account?'); ?> <a href="javascript:void(0)" onclick="signUpFormPopUp();" class="-link-underline link-color"><?php echo Label::getLabel('LBL_Sign_Up'); ?></a></p>
    </div>
</div>
