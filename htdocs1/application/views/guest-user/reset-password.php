<?php session_start(); defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--page">
    <div class="container container--fixed">
        <div class="row justify-content-center">
            <div class="col-sm-9 col-lg-5 col-xl-5">
                <div class="box -skin">
                    <div class="box__head -align-center">
                            <h4 class="-border-title">
                                <?php echo $_SESSION['email']; ?>
                            </h4>
                        <h4 class="-border-title"><?php echo Label::getLabel('LBL_Reset_Password?'); ?></h4>
                        <p><?php echo Label::getLabel('LBL_Change_or_reset_your_password.'); ?></p>
                    </div>
                    <div class="box__body -padding-40">
                        <?php
                        $frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
                        $frm->setFormTagAttribute('class', 'form');
                        $frm->setValidatorJsObjectName('resetValObj');
                        $frm->developerTags['colClassPrefix'] = 'col-md-';
                        $frm->developerTags['fld_default_col'] = 12;
                        $frm->setFormTagAttribute('action', '');
                        $frm->setFormTagAttribute('onSubmit', 'resetpwd(this, resetValObj); return(false);');
                        echo $frm->getFormHtml();
                        echo $frm->getExternalJs();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>