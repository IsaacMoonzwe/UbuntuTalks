<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-sm-';
$frm->developerTags['fld_default_col'] = 12;
$fldPassword = $frm->getField('password');
$fldPassword->changeCaption('');
$fldPassword->captionWrapper = array(Label::getLabel('LBL_Password'), '<a onClick="toggleLoginPassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password') . '</a>');
$frm->setFormTagAttribute('onsubmit', 'EventSetUpLogin(this); return(false);');
?>
<?php
$userTypeArray = array('userType' => User::USER_TYPE_LEANER);
if (isset($userType) && !empty($userType)) {
    $userTypeArray = array('userType' => $userType);
}
?>
<section class="section section--gray section--page">
    <div class="container container--fixed">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-lg-7 col-xl-6">
                <div class="box -skin">
                    <div class="box__head -align-center">
                        <h4 class="-border-title"><?php echo Label::getLabel('LBL_Login'); ?></h4>
                    </div>
                    <div class="box__body -padding-40 div-login-form">
                        <?php $this->includeTemplate('event-user/_partial/learner-social-media-signup.php', $userTypeArray); ?>
                        <?php echo $frm->getFormHtml(); ?>
                        <div class="-align-center">
                            <a href="<?php echo CommonHelper::generateUrl('EventUser', 'ForgotPasswordForm'); ?>" class="-link-underline"><?php echo Label::getLabel('LBL_Forgot_Password?'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
