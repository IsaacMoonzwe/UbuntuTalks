<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal layout--' . $formLayout);
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = '12';
if ($lang_id > 0) {
    $frm->setFormTagAttribute('onsubmit', 'setupLang(this); return(false);');
} else {
    $frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
}
if (!$canEdit) {
    $submitBtn = $frm->getField('btn_submit');
    $frm->removeField($submitBtn);
}
$fldZoomKeyHead = $frm->getField('zoom_api_key');
if ($fldZoomKeyHead) {
    $fldZoomKeyHead->htmlBeforeField = '
	<div class="info__icon">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 23.625 23.625" style="enable-background:new 0 0 23.625 23.625;" xml:space="preserve">
			<g>
				<path style="fill:#c1c1c1;" d="M11.812,0C5.289,0,0,5.289,0,11.812s5.289,11.813,11.812,11.813s11.813-5.29,11.813-11.813   S18.335,0,11.812,0z M14.271,18.307c-0.608,0.24-1.092,0.422-1.455,0.548c-0.362,0.126-0.783,0.189-1.262,0.189   c-0.736,0-1.309-0.18-1.717-0.539s-0.611-0.814-0.611-1.367c0-0.215,0.015-0.435,0.045-0.659c0.031-0.224,0.08-0.476,0.147-0.759   l0.761-2.688c0.067-0.258,0.125-0.503,0.171-0.731c0.046-0.23,0.068-0.441,0.068-0.633c0-0.342-0.071-0.582-0.212-0.717   c-0.143-0.135-0.412-0.201-0.813-0.201c-0.196,0-0.398,0.029-0.605,0.09c-0.205,0.063-0.383,0.12-0.529,0.176l0.201-0.828   c0.498-0.203,0.975-0.377,1.43-0.521c0.455-0.146,0.885-0.218,1.29-0.218c0.731,0,1.295,0.178,1.692,0.53   c0.395,0.353,0.594,0.812,0.594,1.376c0,0.117-0.014,0.323-0.041,0.617c-0.027,0.295-0.078,0.564-0.152,0.811l-0.757,2.68   c-0.062,0.215-0.117,0.461-0.167,0.736c-0.049,0.275-0.073,0.485-0.073,0.626c0,0.356,0.079,0.599,0.239,0.728   c0.158,0.129,0.435,0.194,0.827,0.194c0.185,0,0.392-0.033,0.626-0.097c0.232-0.064,0.4-0.121,0.506-0.17L14.271,18.307z    M14.137,7.429c-0.353,0.328-0.778,0.492-1.275,0.492c-0.496,0-0.924-0.164-1.28-0.492c-0.354-0.328-0.533-0.727-0.533-1.193   c0-0.465,0.18-0.865,0.533-1.196c0.356-0.332,0.784-0.497,1.28-0.497c0.497,0,0.923,0.165,1.275,0.497   c0.353,0.331,0.53,0.731,0.53,1.196C14.667,6.703,14.49,7.101,14.137,7.429z"/>
			</g>
		</svg>
		<div class="classic">' . Label::getLabel("LBL_Zoom_Version") . ':' . CONF_ZOOM_VERSION . '</div>
	</div>';
}
$tbid = isset($tabId) ? $tabId : 'tabs_' . $frmType;
switch ($frmType) {
    case Configurations::FORM_GENERAL:
        break;
    case Configurations::FORM_MEDIA:
        $frm->developerTags['fld_default_col'] = '6';
        $adminLogoFld = $frm->getField('admin_logo');
        $desktopLogoFld = $frm->getField('front_logo');
        $desktopWhiteLogoFld = $frm->getField('front_white_logo');
        $emailLogoFld = $frm->getField('email_logo');
        $socialFeedImgFld = $frm->getField('social_feed_image');
        $faviconFld = $frm->getField('favicon');
        $paymentPageLogo = $frm->getField('payment_page_logo');
        $appleTouchIcon = $frm->getField('apple_touch_icon');
        $mobileLogo = $frm->getField('mobile_logo');
        $blogImg = $frm->getField('blog_img');
        $ReferralCampaignImg = $frm->getField('referral_campaign_img');
        $EventCampaignImg = $frm->getField('event_campaign_img');
        $EventSecondSliderCampaignImg = $frm->getField('event_second_slider_campaign_img');
        $EventThirdSliderCampaignImg = $frm->getField('event_third_slider_campaign_img');
        $EventFourthSliderCampaignImg = $frm->getField('event_fourth_slider_campaign_img');
        $EventFifthSliderCampaignImg = $frm->getField('event_fifth_slider_campaign_img');
        $VirtualSessionCampaignImg = $frm->getField('event_virtual_session_campaign_img');
        $MedicalCampaignImg = $frm->getField('medical_campaign_img');
        $FaithGroupsCampaignImg = $frm->getField('faith_groups_campaign_img');
        $EducationCampaignImg = $frm->getField('education_campaign_img');
        $BusinessCampaignImg = $frm->getField('business_campaign_img');
        $kidsImg = $frm->getField('kids_img');
        $serviceImg = $frm->getField('service_img');
        $quoteImg = $frm->getField('quote_img');
        $contactImg = $frm->getField('contact_img');
        $lessonImg = $frm->getField('lesson_img');
        $applyToTeachForm = $frm->getField('apply_to_teach_banner');
        $allowedPaymentGatewayImg = $frm->getField('allowed_payment_gateways_img');
        if ($canEdit) {
            $adminLogoFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '142*45');
            $desktopLogoFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '168*37');
            $desktopWhiteLogoFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '168*37');
            $emailLogoFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '168*37');
            $socialFeedImgFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '160*240');
            $faviconFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '16*16');
            $mobileLogo->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '168*37');
            $blogImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1600*480');
            $ReferralCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1409*400');
            $EventCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2114*750');
            $EventSecondSliderCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2114*750');
            $EventThirdSliderCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2114*750');
            $EventFourthSliderCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2114*750');
            $EventFifthSliderCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2114*750');
            $VirtualSessionCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2114*750');
            $MedicalCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1409*400');
            $FaithGroupsCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1409*400');
            $EducationCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1409*400');
            $BusinessCampaignImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1409*400');
            $kidsImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2000*900');
            $serviceImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1600*480');
            $quoteImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1305*461');
            $contactImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '1305*461');
            $lessonImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2000*900');
            $appleTouchIcon->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '16*16');
            $paymentPageLogo->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '168*37');
            $allowedPaymentGatewayImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '500*67');
            $applyToTeachForm->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s', $adminLangId), '2000*900');
        } else {
            $adminLogoFld->setFieldTagAttribute('class', 'hide');
            $desktopLogoFld->setFieldTagAttribute('class', 'hide');
            $desktopWhiteLogoFld->setFieldTagAttribute('class', 'hide');
            $emailLogoFld->setFieldTagAttribute('class', 'hide');
            $socialFeedImgFld->setFieldTagAttribute('class', 'hide');
            $faviconFld->setFieldTagAttribute('class', 'hide');
            $mobileLogo->setFieldTagAttribute('class', 'hide');
            $appleTouchIcon->setFieldTagAttribute('class', 'hide');
            $paymentPageLogo->setFieldTagAttribute('class', 'hide');
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_ADMIN_LOGO, 0, 0, $lang_id)) {
            $adminLogoFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'siteAdminLogo', array($lang_id)) . '?' . time() . '"> ';
            if ($canEdit) {
                $adminLogoFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeSiteAdminLogo(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $adminLogoFld->htmlAfterField .= '</div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_FRONT_WHITE_LOGO, 0, 0, $lang_id)) {
            $desktopWhiteLogoFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'siteWhiteLogo', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"> ';
            if ($canEdit) {
                $desktopWhiteLogoFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeDesktopWhiteLogo(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $desktopWhiteLogoFld->htmlAfterField .= '</div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_FRONT_LOGO, 0, 0, $lang_id)) {
            $desktopLogoFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'siteLogo', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"> ';
            if ($canEdit) {
                $desktopLogoFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeDesktopLogo(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $desktopLogoFld->htmlAfterField .= '</div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_PAYMENT_PAGE_LOGO, 0, 0, $lang_id)) {
            $paymentPageLogo->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'paymentPageLogo', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"> ';
            if ($canEdit) {
                $paymentPageLogo->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removePaymentPageLogo(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $paymentPageLogo->htmlAfterField .= '</div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_EMAIL_LOGO, 0, 0, $lang_id)) {
            $emailLogoFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'emailLogo', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"> ';
            if ($canEdit) {
                $emailLogoFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeEmailLogo(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $emailLogoFld->htmlAfterField .= '</div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_FAVICON, 0, 0, $lang_id)) {
            $faviconFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'favicon', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"> ';
            if ($canEdit) {
                $faviconFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeFavicon(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $faviconFld->htmlAfterField .= '</div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_SOCIAL_FEED_IMAGE, 0, 0, $lang_id)) {
            $socialFeedImgFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'socialFeed', array($lang_id, 'THUMB'), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"> ';
            if ($canEdit) {
                $socialFeedImgFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeSocialFeedImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $socialFeedImgFld->htmlAfterField .= '</div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_APPLE_TOUCH_ICON, 0, 0, $lang_id)) {
            $appleTouchIcon->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'appleTouchIcon', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"> ';
            if ($canEdit) {
                $appleTouchIcon->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeAppleTouchIcon(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $appleTouchIcon->htmlAfterField .= '</div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_MOBILE_LOGO, 0, 0, $lang_id)) {
            $mobileLogo->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'mobileLogo', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"> ';
            if ($canEdit) {
                $mobileLogo->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeMobileLogo(' . $lang_id . ')" ><i class="ion-close-round"></i></a>';
            }
            $mobileLogo->htmlAfterField .= '</div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_BLOG_PAGE_IMAGE, 0, 0, $lang_id)) {
            $blogImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'blog', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeBlogImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_MEDICAL_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $MedicalCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'MedicalCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeMedicalCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_FAITH_GROUPS_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $FaithGroupsCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'FaithGroupsCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeFaithGroupsCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_EDUCATION_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $EducationCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'EducationCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeEducationCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_BUSINESS_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $BusinessCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'BusinessCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeBusinessCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_REFERRAL_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $ReferralCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'ReferralCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeReferralCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $EventCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'EventCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeEventCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_SECOND_SLIDER_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $EventSecondSliderCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'EventSecondSliderCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeEventSecondSliderCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_THIRD_SLIDER_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $EventThirdSliderCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'EventThirdSliderCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeEventThirdSliderCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_FOURTH_SLIDER_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $EventFourthSliderCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'EventFourthSliderCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeEventFourthSliderCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_FIFTH_SLIDER_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $EventFifthSliderCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'EventFifthSliderCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeEventFifthSliderCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_VIRTUAL_SESSION_CAMPAIGN_IMAGE, 0, 0, $lang_id)) {
            $VirtualSessionCampaignImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'VirtualSessionCampaign', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeVirtualSessionCampaignImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_KIDS_PAGE_IMAGE, 0, 0, $lang_id)) {
            $kidsImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'kids', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeKidsImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        // if (AttachedFile::getAttachment(AttachedFile::FILETYPE_SERVICE_PAGE_IMAGE, 0, 0, $lang_id)) {
        //     $serviceImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'service', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeServiceImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        // }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_QUOTE_PAGE_IMAGE, 0, 0, $lang_id)) {
            $quoteImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'quote', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeQuoteImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_CONTACT_PAGE_IMAGE, 0, 0, $lang_id)) {
            $contactImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'contact', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeContactImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }

        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_LESSON_PAGE_IMAGE, 0, 0, $lang_id)) {
            $lessonImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'lesson', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeLessonImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_APPLY_TO_TEACH_BANNER, 0, 0, $lang_id)) {
            $applyToTeachForm->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . FatUtility::generateFullUrl('Image', 'applyToTeachBanner', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeApplyToTeachBannerImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }
        if (AttachedFile::getAttachment(AttachedFile::FILETYPE_ALLOWED_PAYMENT_GATEWAYS_IMAGE, 0, 0, $lang_id)) {
            $allowedPaymentGatewayImg->htmlAfterField .= '<div class="uploaded--image"><img src="' . FatUtility::generateFullUrl('Image', 'allowedPaymentGatewayImage', array($lang_id), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"><a class="remove--img" href="javascript:void(0);" onclick="removeAllowedPaymentGatewayImage(' . $lang_id . ')" ><i class="ion-close-round"></i></a></div><br>';
        }
        break;
}
?>
<ul class="tabs_nav innerul">
    <?php if (!in_array($frmType, [Configurations::FORM_MEDIA, Configurations::FORM_SECURITY_SETTINGS])) { ?>
        <li><a href="javascript:void(0)" class="<?php echo ($lang_id == 0) ? 'active' : ''; ?>" onClick="getForm(<?php echo $frmType; ?>, '<?php echo $tbid; ?>')"><?php echo Label::getLabel('LBL_BASIC') ?></a></li>
    <?php } ?>
    <?php
    if ($dispLangTab) {
        foreach ($languages as $langId => $langName) {
    ?>
            <li><a href="javascript:void(0);" class="<?php echo ($lang_id == $langId) ? 'active' : ''; ?>" onClick="getLangForm(<?php echo $frmType; ?>,<?php echo $langId; ?>, '<?php echo $tbid; ?>')"><?php echo $langName; ?></a></li>
    <?php
        }
    }
    ?>
</ul>
<div class="tabs_panel_wrap">
    <?php echo $frm->getFormHtml(); ?>
</div>