<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 


?>
<style>
    input[type="text"] {
        width: 100%;
        padding: 1px 10px;
        height: 48px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-shadow: 0px 2px 0px #d2d2d2;
    }
    .field_label {
        font-size: 15px;
    }

    span.registratio-title {
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .hide_conf {
        display: none;
    }

    .selection-tabs {
        overflow-x: hidden;
        display: block !important;
        width: 100% !important;
    }

    /* Style the tab */
    .tab {
        overflow: hidden;
        text-align: center;
    }

    /* Style the buttons inside the tab */
    .tab button {
        border: 1px solid #f0f0f0 !important;
        box-shadow: 0 2px 0px #f0f0f0;
        font-weight: 500;
        margin: 0px 15px 8px 0px;
        padding: 10px 20px 10px 20px;
        width: 150px;
        font-size: 18px;
        float: left;
        outline: none;
        transition: 0.3s;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background: #ce4400;
        color: #fff;
        cursor: pointer;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background: #006313 !important;
        color: #fff !important;
    }

    /* Style the tab content */
    .tabcontent {
        /* height: 300px; */
        display: none;
        padding: 6px 12px;
        border-top: none;
    }
</style>

<div class="box box--checkout">
    <div class="box__head">
        <a href="javascript:void(0);" onclick="GetEventDonation(1,eventCart.props.donationAmount);" class="btn btn--bordered color-black btn--back">
            <svg class="icon icon--back">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK'); ?>
        </a>
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf sponsorship-tabs">
            <?php if ($userId <= 0) { ?> <div class="tab">
                    <button class="tablinks active" onclick="openCity(event, 'Login')">Login</button>
                    <button class="tablinks" onclick="openCity(event, 'Registration')">Registration</button>
                </div>
            <?php } ?>

            <div id="Login" class="tabcontent">
                <?php
                // $loginFrm->setFormTagAttribute('class', 'web_form form_horizontal');
                if ($userId <= 0) {
                    $loginFrm->developerTags['colClassPrefix'] = 'col-md-';
                    $loginFrm->developerTags['fld_default_col'] = 12;
                    $loginFrm->setFormTagAttribute('id', 'loginForm');
                    $loginFrm->setFormTagAttribute('name', 'loginForm');
                    $loginFrm->setFormTagAttribute('class', 'form');
                    $loginFrm->developerTags['colClassPrefix'] = 'col-sm-';
                    $loginFrm->developerTags['fld_default_col'] = 12;
                    $fldPassword = $loginFrm->getField('password');
                    $fldPassword->changeCaption('');
                    //   $fldConfirmPassword = $loginFrm->getField('conf_new_password');
                    $fldPassword->captionWrapper = array(Label::getLabel('LBL_Password'), '<a onClick="toggleLoginPassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password') . '</a>');
                    /* [ */

                    echo $loginFrm->getFormHtml();
                }
                ?>
            </div>

            <div id="Registration" class="tabcontent">
                <?php
                if ($userId <= 0) {
                    $frm->setFormTagAttribute('class', 'web_form form_horizontal');
                    $frm->developerTags['colClassPrefix'] = 'col-md-';
                    $frm->developerTags['fld_default_col'] = 12;
                    $frm->setFormTagAttribute('class', 'form');
                    $frm->setFormTagAttribute('id', 'registerForm');
                    $frm->setFormTagAttribute('name', 'registerForm');
                    $frm->developerTags['colClassPrefix'] = 'col-sm-';
                    $frm->developerTags['fld_default_col'] = 12;
                    $fldFirstName = $frm->getField('user_first_name');
                    $fldFirstName->developerTags['col'] = 6;
                    $fldLastName = $frm->getField('user_last_name');
                    $fldLastName->developerTags['col'] = 6;
                    if ($userId <= 0) {
                        $fldPassword = $frm->getField('user_password');
                        $fldPassword->changeCaption('');
                        $fldConfirmPassword = $frm->getField('conf_new_password');
                        $fldPassword->captionWrapper = (array(Label::getLabel('LBL_Password') . '<span class="spn_must_field">*</span><a onClick="togglePassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password'), '</a>'));
                        /* [ */

                        /* Confrim Password*/
                        $fldConfirmPassword->captionWrapper = (array(Label::getLabel('LBL_Confirm_Password') . '<span class="spn_must_field">*</span><a onClick="toggleConfirmPassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">', '</a>'));
                        $termLink = ' <a target="_blank" class = "-link-underline link-color" href="' . $termsAndConditionsLinkHref . '">' . Label::getLabel('LBL_TERMS_AND_CONDITION') . '</a> and <a href="' . $privacyPolicyLinkHref . '" target="_blank" class = "-link-underline link-color" >' . Label::getLabel('LBL_Privacy_Policy') . '</a>';
                        $terms_caption = '<span>' . $termLink . '</span>';
                        $frm->getField('agree')->addWrapperAttribute('class', 'terms_wrap');
                        $frm->getField('agree')->htmlAfterField = $terms_caption;
                    }
                ?>
                    <div>
                        <div><span class="registratio-title">Tell us about yourself.</span></div>
                        <div>
                            <h2>Billing Information</h2>
                        </div>
                    </div>
                <?php
                    echo $frm->getFormHtml();
                }
                ?>
            </div>
            <?php
            if ($userId > 0) {
                $frm->setFormTagAttribute('class', 'web_form form_horizontal');
                $frm->developerTags['colClassPrefix'] = 'col-md-';
                $frm->developerTags['fld_default_col'] = 12;
                $frm->setFormTagAttribute('class', 'form');
                $frm->setFormTagAttribute('id', 'registerForm');
                $frm->setFormTagAttribute('name', 'registerForm');
                $frm->developerTags['colClassPrefix'] = 'col-sm-';
                $frm->developerTags['fld_default_col'] = 12;
                $fldFirstName = $frm->getField('user_first_name');
                $fldFirstName->developerTags['col'] = 6;
                $fldLastName = $frm->getField('user_last_name');
                $fldLastName->developerTags['col'] = 6;
            ?>
                <h3>Donation Billing Information</h3><br>
            <?php
                echo $frm->getFormHtml();
            }
            ?>
        </div>
    </div>
    <div class="box-foot">
        <div class="box-foot__left" style="display: none;">
            <div class="teacher-profile">
                <div class="teacher__media">
                    <div class="avtar avtar-md">
                        <img src="<?php echo CommonHelper::generateUrl('Image', 'user', array($teacher['user_id'])) . '?' . time(); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?>">
                    </div>
                </div>
                <div class="teacher__name"><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></div>
            </div>
            <div class="step-breadcrumb">
                <ul>
                    <li><a href="javascript:void(0);"><?php echo $teachLangName; ?></a></li>
                </ul>
            </div>
        </div>
        <div class="box-foot__right">
            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventDonationPaymentSummary(eventCart.props.donationAmount);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>


<script>
    eventCart.props.lessonQty = parseInt('<?php echo $lessonQty; ?>');
    var user = parseInt('<?php echo $userId; ?>');
    console.log('eventCart.props.eventUserSelectedStaus==', eventCart.props.eventUserSelectedStaus);
    if (user <= 0) {
        if (eventCart.props.eventUserSelectedStaus == undefined) {
            eventCart.props.eventUserSelectedStaus = 'Login';
        }
        document.getElementById(eventCart.props.eventUserSelectedStaus).style.display = "block";
    }

    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        eventCart.props.eventUserSelectedStaus = cityName;
        tabcontent = document.getElementsByClassName("tabcontent");

        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }
</script>