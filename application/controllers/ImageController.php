<?php

class ImageController extends FatController
{

    public function __construct()
    {
        CommonHelper::initCommonVariables();
    }

    public function siteLogo($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_FRONT_LOGO, $recordId, 0, $lang_id, false);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'ORIG':
                AttachedFile::displayOriginalImage($image_name, $default_image);
                break;
            default:
                $h = 46;
                $w = 160;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function siteWhiteLogo($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_FRONT_WHITE_LOGO, $recordId, 0, $lang_id, false);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 37;
                $w = 168;
                AttachedFile::displayOriginalImage($image_name, $default_image);
                break;
        }
    }

    public function emailLogo($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_EMAIL_LOGO, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = 'no_image.jpg';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $w = 100;
                $h = 100;
                if ($image_name == '' || empty($image_name)) {
                    AttachedFile::displayImage($image_name, $w, $h, $default_image);
                } else {
                    AttachedFile::displayOriginalImage($image_name, $default_image);
                }
                break;
        }
    }

    public function socialFeed($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_SOCIAL_FEED_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 120;
                $h = 80;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 240;
                $w = 160;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function SocialPlatform($splatform_id, $sizeType = '')
    {
        $default_image = '';
        $splatform_id = FatUtility::int($splatform_id);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_SOCIAL_PLATFORM_IMAGE, $splatform_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $w = 30;
                $h = 30;
                AttachedFile::displayImage($image_name, $w, $h, $default_image, '', ImageResize::IMG_RESIZE_EXTRA_CROP);
                break;
        }
    }

    public function watermarkImage($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_WATERMARK_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                AttachedFile::displayOriginalImage($image_name, $default_image);
                break;
        }
    }

    public function appleTouchIcon($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_APPLE_TOUCH_ICON, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'MINI':
                $w = 72;
                $h = 72;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'SMALL':
                $w = 114;
                $h = 114;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                AttachedFile::displayOriginalImage($image_name, $default_image);
                break;
        }
    }

    public function mobileLogo($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_MOBILE_LOGO, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 82;
                $w = 268;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function blog($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_BLOG_PAGE_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 480;
                $w = 1900;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function MedicalCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_MEDICAL_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 400;
                $w = 1409;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function EducationCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_EDUCATION_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 400;
                $w = 1409;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function FaithGroupsCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_FAITH_GROUPS_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 400;
                $w = 1409;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function BusinessCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_BUSINESS_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 400;
                $w = 1409;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function ReferralCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_REFERRAL_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 400;
                $w = 1409;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function EventCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 750;
                $w = 2114;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function EventSecondSliderCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_SECOND_SLIDER_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 750;
                $w = 2114;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function EventThirdSliderCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_THIRD_SLIDER_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 750;
                $w = 2114;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function EventFourthSliderCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_FOURTH_SLIDER_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 750;
                $w = 2114;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function EventFifthSliderCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_FIFTH_SLIDER_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 750;
                $w = 2114;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function VirtualSessionCampaign($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_VIRTUAL_SESSION_CAMPAIGN_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 750;
                $w = 2114;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function kids($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_KIDS_PAGE_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 900;
                $w = 2000;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    // public function service($lang_id = 0, $sizeType = '')
    // {
    //     $lang_id = FatUtility::int($lang_id);
    //     $recordId = 0;
    //     $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_SERVICE_PAGE_IMAGE, $recordId, 0, $lang_id);
    //     $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
    //     $default_image = '';
    //     switch (strtoupper($sizeType)) {
    //         case 'THUMB':
    //             $w = 200;
    //             $h = 60;
    //             AttachedFile::displayImage($image_name, $w, $h, $default_image);
    //             break;
    //         default:
    //             $h = 480;
    //             $w = 1900;
    //             AttachedFile::displayImage($image_name, $w, $h, $default_image);
    //             break;
    //     }
    // }

    public function quote($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_QUOTE_PAGE_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 461;
                $w = 1305;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function contact($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_CONTACT_PAGE_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                 $h = 461;
                 $w = 1305;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function lesson($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_LESSON_PAGE_IMAGE, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 900;
                $w = 2000;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function applyToTeachBanner($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_APPLY_TO_TEACH_BANNER, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 200;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 900;
                $w = 2000;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function paymentPageLogo($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_PAYMENT_PAGE_LOGO, $recordId, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $w = 268;
                $h = 82;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function allowedPaymentGatewayImage(int $langId = 0, string $sizeType = '')
    {
        $langId = FatUtility::int($langId);
        $recordId = 0;
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_ALLOWED_PAYMENT_GATEWAYS_IMAGE, $recordId, 0, $langId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 300;
                $h = 34;
                break;
            default:
                $w = 500;
                $h = 67;
                break;
        }
        AttachedFile::displayImage($image_name, $w, $h, $default_image);
    }

    public function favicon($lang_id = 0, $sizeType = '')
    {
        if ($file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_FAVICON, 0, 0, $lang_id, false)) {
            $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
            AttachedFile::displayOriginalImage($image_name);
        }
    }

    public function KidsPlanImage($lessonPlanId = 0, $subRecordId = 0, $sizeType = '')
    {
        $recordId = FatUtility::int($lessonPlanId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_KIDS_PLAN_IMAGE, $recordId, $subRecordId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            default:
                $w = 538;
                $h = 283;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
        }
    }

    public function user($userId, $sizeType = 'default', $requestedForCroppedImage = 1)
    {
        $userId = FatUtility::int($userId);
        $requestedForCroppedImage = FatUtility::int($requestedForCroppedImage);
        if ($userId < 1) {
            trigger_error("User Id is not passed", E_USER_ERROR);
        }
        $default_image = 'no_image.jpg';
        $fileRow = false;
        if (1 === $requestedForCroppedImage || true === $requestedForCroppedImage) {
            $fileRow = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PROFILE_CROPED_IMAGE, $userId);
        }
        if (false === $fileRow || null == $fileRow) {
            $fileRow = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PROFILE_IMAGE, $userId);
        }
        if (false == $fileRow || $fileRow['afile_physical_path'] == "") {
            AttachedFile::displayImage('', '', '', $default_image);
        }
        switch (strtoupper($sizeType)) {
            case 'ORIGINAL':
                $w = '';
                $h = '';
                break;
            case 'MEDIUM':
                $w = 275;
                $h = 275;
                break;
            case 'SMALL':
                $w = 60;
                $h = 60;
                break;
            case 'EXTRASMALL':
                $w = 42;
                $h = 42;
                break;
            default:
                $w = 100;
                $h = 100;
                break;
        }
        if ($w && $h) {
            AttachedFile::displayImage($fileRow['afile_physical_path'], $w, $h, '', '', ImageResize::IMG_RESIZE_EXTRA_ADDSPACE, false, true);
        } else {
            AttachedFile::displayOriginalImage($fileRow['afile_physical_path'], '', '', true);
        }
    }

    public function userFull($userId)
    {
        $userId = FatUtility::int($userId);
        $fileRow = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PROFILE_IMAGE, $userId);
        if (false == $fileRow || $fileRow['afile_physical_path'] == "") {
            AttachedFile::displayImage('', '', '', 'no_image.jpg');
        }
        AttachedFile::displayOriginalImage($fileRow['afile_physical_path'], '', '', true);
    }

    public function countryFlag($countryId, $sizeType = 'default')
    {
        $countryId = FatUtility::int($countryId);
        $fileRow = AttachedFile::getAttachment(AttachedFile::FILETYPE_COUNTRY_FLAG, $countryId);
        $imageName = $fileRow['afile_physical_path'] ?? '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                AttachedFile::displayImage($imageName, 100, 100);
                break;
            case 'DEFAULT':
                AttachedFile::displayImage($imageName, 30, 20);
                break;
            default:
                AttachedFile::displayOriginalImage($imageName);
                break;
        }
    }

    public function blogPostAdmin($postId, $langId = 0, $size_type = '', $subRecordId = 0, $afile_id = 0)
    {
        $this->blogPost($postId, $langId, $size_type, $subRecordId, $afile_id, false);
    }

    public function blogPostFront($postId, $langId = 0, $size_type = '', $subRecordId = 0, $afile_id = 0)
    {
        $this->blogPost($postId, $langId, $size_type, $subRecordId, $afile_id);
    }

    public function blogPost($postId, $langId = 0, $size_type = '', $subRecordId = 0, $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'post_default_image.jpg';
        $langId = FatUtility::int($langId);
        $afile_id = FatUtility::int($afile_id);
        $postId = FatUtility::int($postId);
        $subRecordId = FatUtility::int($subRecordId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_BLOG_POST_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_BLOG_POST_IMAGE, $postId, $subRecordId, $langId, $displayUniversalImage);
        }
        $image_name = isset($file_row['afile_physical_path']) ? AttachedFile::FILETYPE_BLOG_POST_IMAGE_PATH . $file_row['afile_physical_path'] : '';
        switch (strtoupper($size_type)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'SMALL':
                $w = 200;
                $h = 200;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'MEDIUM':
                $w = 600;
                $h = 450;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'BANNER':
                $w = 945;
                $h = 710;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 400;
                $w = 400;
                AttachedFile::displayOriginalImage($image_name);
                break;
        }
    }

    public function slide($slide_id, $screen = 0, $lang_id, $sizeType = '', $displayUniversalImage = true)
    {
        $default_image = 'brand_deafult_image.jpg';
        $slide_id = FatUtility::int($slide_id);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slide_id, 0, $lang_id, $displayUniversalImage, $screen);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $cacheKey = $_SERVER['REQUEST_URI'];
        $str = FatCache::get($cacheKey, null, '.jpg');
        if (false == $str && !CONF_USE_FAT_CACHE) {
            $cacheKey = false;
        }
        if ($sizeType) {
            switch (strtoupper($sizeType)) {
                case 'THUMB':
                    $w = 200;
                    $h = 100;
                    break;
                case 'MOBILE':
                    $w = 800;
                    $h = 600;
                    break;
                case 'TABLET':
                    $w = 1200;
                    $h = 800;
                    break;
                case 'DESKTOP':
                    $w = 2000;
                    $h = 900;
                    break;
                default:
                    $w = 2000;
                    $h = 900;
                    break;
            }
            AttachedFile::displayImage($image_name, $w, $h, $default_image, '', ImageResize::IMG_RESIZE_EXTRA_CROP, false, true);
        } else {
            AttachedFile::displayOriginalImage($image_name, $default_image, '', true);
        }
    }

    public function cpageBackgroundImage($cpageId, $langId = 0, $sizeType = '')
    {
        $cpageId = FatUtility::int($cpageId);
        $langId = FatUtility::int($langId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE, $cpageId, 0, $langId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            case 'COLLECTION_PAGE':
                $w = 45;
                $h = 41;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            default:
                $w = 2000;
                $h = 900;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
        }
    }

    public function testimonial($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_TESTIMONIAL_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_TESTIMONIAL_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
        }
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'MINITHUMB':
                $w = 42;
                $h = 52;
                break;
            case 'THUMB':
                $w = 128;
                $h = 80;
                break;
            case 'NORMAL':
                $w = 625;
                $h = 391;
                break;
            case 'MEDIUM':
                $w = 275;
                $h = 275;
                break;
            default:
                $h = 500;
                $w = 800;
                break;
        }
        AttachedFile::displayImage($image_name, $w, $h, $default_image, '', ImageResize::IMG_RESIZE_EXTRA_ADDSPACE, false, true);
    }

    public function showBanner($bannerId, $langId, $type = 1, $secondary = false)
    {
        $bannerId = FatUtility::int($bannerId);
        $langId = FatUtility::int($langId);
        switch ($type) {
            case BannerLocation::BLOCK_HOW_IT_WORKS:
                $w = 500;
                $h = 500;
                break;
        }
        if ($secondary) {
            $imgType = AttachedFile::FILETYPE_BANNER_SECOND_IMAGE;
        } else {
            $imgType = AttachedFile::FILETYPE_BANNER;
        }
        $fileRow = AttachedFile::getAttachment($imgType, $bannerId, 0, $langId, true, 0);
        $image_name = isset($fileRow['afile_physical_path']) ? $fileRow['afile_physical_path'] : '';
        $default_image = 'no_image_user.jpg';
        AttachedFile::displayImage($image_name, $w, $h, $default_image, '', ImageResize::IMG_RESIZE_EXTRA_CROP, false, true);
    }

    public function showLanguageImage($sLanguageId, $sizeType = '', $langId = 0)
    {
        $sLanguageId = FatUtility::int($sLanguageId);
        $langId = FatUtility::int($langId);
        $default_image = 'no_image_user.jpg';
        $imgType = AttachedFile::FILETYPE_TEACHING_LANGUAGES;
        $fileRow = AttachedFile::getAttachment($imgType, $sLanguageId, 0, $langId, true, 0);
        $image_name = isset($fileRow['afile_physical_path']) ? $fileRow['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'NORMAL':
                $w = 357;
                $h = 277;
                break;
            default:
                $w = 470;
                $h = 367;
                break;
        }
        AttachedFile::displayImage($image_name, $w, $h, $default_image, '', ImageResize::IMG_RESIZE_EXTRA_ADDSPACE, false, true);
    }

    public function showLanguageFlagImage($sLanguageId, $sizeType = '', $langId = 0)
    {
        $sLanguageId = FatUtility::int($sLanguageId);
        $langId = FatUtility::int($langId);
        $default_image = 'no_image_user.jpg';
        $imgType = AttachedFile::FILETYPE_FLAG_TEACHING_LANGUAGES;
        $fileRow = AttachedFile::getAttachment($imgType, $sLanguageId, 0, $langId, true, 0);
        $image_name = isset($fileRow['afile_physical_path']) ? $fileRow['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'SMALL':
                $w = 120;
                $h = 120;
                AttachedFile::displayImage($image_name, $w, $h, $default_image, '', ImageResize::IMG_RESIZE_EXTRA_ADDSPACE, false, true);
                break;
            default:
                AttachedFile::displayOriginalImage($image_name, $default_image, '', true);
                break;
        }
    }

    public function showSpokenLangFlagImage(int $speakLangId, string $sizeType = '', int $langId = 0)
    {
        $speakLangId = FatUtility::int($speakLangId);
        $langId = FatUtility::int($langId);
        $default_image = 'no_image_user.jpg';
        $imgType = AttachedFile::FILETYPE_FLAG_SPOKEN_LANGUAGES;
        $fileRow = AttachedFile::getAttachment($imgType, $speakLangId, 0, $langId, true, 0);
        $image_name = isset($fileRow['afile_physical_path']) ? $fileRow['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'SMALL':
                $w = 40;
                $h = 40;
                AttachedFile::displayImage($image_name, $w, $h, $default_image, '', ImageResize::IMG_RESIZE_EXTRA_ADDSPACE, false, true);
                break;
            default:
                AttachedFile::displayOriginalImage($image_name, $default_image, '', true);
                break;
        }
    }

    public function editorImage($fileNamewithPath)
    {
        AttachedFile::displayOriginalImage('editor/' . $fileNamewithPath);
    }

    public function editorImages($dirPath, $fileNamewithPath)
    {
        AttachedFile::displayOriginalImage('editor/' . $fileNamewithPath);
    }

    public function pwaIcon(int $size = 100)
    {
        $image_name = '';
        if ($file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_PWA_APP_ICON, 0, 0, 0, true)) {
            $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        }
        switch ($size) {
            case '144':
                $w = $h = 144;
                break;
            case '512':
                $w = $h = 512;
                break;
            default:
                $w = $h = 100;
                break;
        }
        AttachedFile::displayImage($image_name, $w, $h);
    }

    public function pwaSplashIcon(int $size = 100)
    {
        $image_name = '';
        if ($file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_PWA_SPLASH_ICON, 0, 0, 0, true)) {
            $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        }
        switch ($size) {
            case '144':
                $w = $h = 144;
                break;
            case '512':
                $w = $h = 512;
                break;
            default:
                $w = $h = 100;
                break;
        }
        AttachedFile::displayImage($image_name, $w, $h);
    }

    public function openGraphImage($metaId, $lang_id, $sizeType = 'NORMAL')
    {
        $metaId = FatUtility::int($metaId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_OPENGRAPH_IMAGE, $metaId, 0, $lang_id, false, 0);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        if ($sizeType) {
            switch (strtoupper($sizeType)) {
                case 'SMALL':
                    $w = 200;
                    $h = 100;
                    break;
                case 'NORMAL':
                    $w = 1200;
                    $h = 630;
                    break;
                default:
                    $w = 1200;
                    $h = 630;
                    break;
            }
            AttachedFile::displayImage($image_name, $w, $h, '', '', ImageResize::IMG_RESIZE_RESET_DIMENSIONS, false, true);
        } else {
            AttachedFile::displayOriginalImage($image_name, '', '', true);
        }
    }

}
