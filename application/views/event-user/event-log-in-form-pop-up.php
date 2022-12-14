<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$userTypeArray = array('userType' => EventUser::USER_TYPE_LEANER);
if (isset($userType) && !empty($userType)) {
    $userTypeArray = array('userType' => $userType);
}
$fldPassword = $frm->getField('password');
$fldPassword->changeCaption('');
$fldPassword->captionWrapper = array(Label::getLabel('LBL_Password'), '<a onClick="toggleLoginPassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password') . '</a>');
?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_Event_Login_Page'); ?></h2>
    <div class="header__logo login-Registration-logo">
        <a href="/">
            <img src="https://ubuntutalks.com/image/site-logo/1" alt="">
        </a>
    </div>
    <span class="-gap"></span>
    <div class="-align-center">
        <p class="account-title"><?php echo Label::getLabel('LBL_Don\'t_have_an_account?'); ?> <a href="javascript:void(0)" onclick="EventSignUpFormPopUp();" class="-link-underline link-color account-signUp"><?php echo Label::getLabel('LBL_Sign_Up'); ?></a></p>
    </div>
    <?php $this->includeTemplate('event-user/_partial/learner-social-media-signup.php', $userTypeArray); ?>
    <?php
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-sm-';
    $frm->developerTags['fld_default_col'] = 12;
    $frm->setFormTagAttribute('onsubmit', 'EventSetUpLogin(this); return(false);');
    echo $frm->getFormHtml();
    ?>
    <div class="-align-center">
        <a href="<?php echo CommonHelper::generateUrl('EventUser', 'ForgotPasswordForm'); ?>" class="-link-underline link-color"><?php echo Label::getLabel('LBL_Forgot_Password?'); ?></a>
    </div>
    <hr>

</div>