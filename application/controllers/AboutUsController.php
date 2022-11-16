<?php

class AboutUsController extends MyAppController
{
    public function index()
    {
        $AboutUsContent = AboutusContentBlock::getBlockContent(AboutusContentBlock::BLOCK_UT_ABOUTUS_CONTENT_BLOCK, $this->siteLangId);
        $HistoryPhilosophyContent = AboutusContentBlock::getBlockContent(AboutusContentBlock::BLOCK_UT_HISTORY_PHILOSOPHY_CONTENT_BLOCK, $this->siteLangId);
        $SupportPhilanthropy = AboutusContentBlock::getBlockContent(AboutusContentBlock::BLOCK_UT_SUPPORT_PHILANTHROPY_CONTENT_BLOCK, $this->siteLangId);

        /* Our Vision */
        $OurVisionSection = new SearchBase('tbl_our_vision');
        $OurVisionSection->addCondition('our_vision_deleted', '=', 0);
        $OurVisionSection->addCondition('our_vision_active', '=', 1);
        $OurVision_categories = $OurVisionSection->getResultSet();
        $OurVisionCategoriesList = FatApp::getDb()->fetchAll($OurVision_categories);
        foreach ($OurVisionCategoriesList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_OUR_VISION_IMAGE, $value['our_vision_id'], 0, -1);
            $value['our_vision_image'] = $testimonialImages;
            $Visionrecords[$key] = $value;
        }


        /* Our Mission */
        $OurMissionSection = new SearchBase('tbl_our_mission');
        $OurMissionSection->addCondition('our_mission_deleted', '=', 0);
        $OurMissionSection->addCondition('our_mission_active', '=', 1);
        $OurMission_categories = $OurMissionSection->getResultSet();
        $OurMissionCategoriesList = FatApp::getDb()->fetchAll($OurMission_categories);
        foreach ($OurMissionCategoriesList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_OUR_MISSION_IMAGE, $value['our_mission_id'], 0, -1);
            $value['our_mission_image'] = $testimonialImages;
            $Missionrecords[$key] = $value;
        }

        /* Meet The Team - Exexcutive */
        $ExecutiveLeadershipSection = new SearchBase('tbl_meet_the_team');
        $ExecutiveLeadershipSection->addCondition('meet_the_team_deleted', '=', 0);
        $ExecutiveLeadershipSection->addCondition('meet_the_team_active', '=', 1);
        $ExecutiveLeadershipSection->addCondition('meet_the_team_positions_listing', '=', 'Executive Leadership');
        $ExecutiveLeadership_categories = $ExecutiveLeadershipSection->getResultSet();
        $ExecutiveLeadershipCategoriesList = FatApp::getDb()->fetchAll($ExecutiveLeadership_categories);
        foreach ($ExecutiveLeadershipCategoriesList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE, $value['meet_the_team_id'], 0, -1);
            $value['meet_the_team_image'] = $testimonialImages;
            $ExecutiveLeadershiprecords[$key] = $value;
        }

        /* Meet The Team - Senior Board Advisors */
        $SeniorBoardAdvisorsSection = new SearchBase('tbl_meet_the_team');
        $SeniorBoardAdvisorsSection->addCondition('meet_the_team_deleted', '=', 0);
        $SeniorBoardAdvisorsSection->addCondition('meet_the_team_active', '=', 1);
        $SeniorBoardAdvisorsSection->addCondition('meet_the_team_positions_listing', '=', 'Senior Board Advisors');
        $SeniorBoardAdvisors_categories = $SeniorBoardAdvisorsSection->getResultSet();
        $SeniorBoardAdvisorsCategoriesList = FatApp::getDb()->fetchAll($SeniorBoardAdvisors_categories);
        foreach ($SeniorBoardAdvisorsCategoriesList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE, $value['meet_the_team_id'], 0, -1);
            $value['meet_the_team_image'] = $testimonialImages;
            $SeniorBoardAdvisorsrecords[$key] = $value;
        }

        /* Meet The Team - Directors & Senior Managers */
        $DirectorsSeniorManagersSection = new SearchBase('tbl_meet_the_team');
        $DirectorsSeniorManagersSection->addCondition('meet_the_team_deleted', '=', 0);
        $DirectorsSeniorManagersSection->addCondition('meet_the_team_active', '=', 1);
        $DirectorsSeniorManagersSection->addCondition('meet_the_team_positions_listing', '=', 'Directors & Senior Managers');
        $DirectorsSeniorManagers_categories = $DirectorsSeniorManagersSection->getResultSet();
        $DirectorsSeniorManagersCategoriesList = FatApp::getDb()->fetchAll($DirectorsSeniorManagers_categories);
        foreach ($DirectorsSeniorManagersCategoriesList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE, $value['meet_the_team_id'], 0, -1);
            $value['meet_the_team_image'] = $testimonialImages;
            $DirectorsSeniorManagersrecords[$key] = $value;
        }


        $this->set('AboutUsListing', $Aboutsrecords);
        $this->set('DirectorsSeniorManagersCategoriesList', $DirectorsSeniorManagersrecords);
        $this->set('SeniorBoardAdvisorsCategoriesList', $SeniorBoardAdvisorsrecords);
        $this->set('ExecutiveLeadershipCategoriesList', $ExecutiveLeadershiprecords);
        $this->set('OurMissionCategoriesList', $Missionrecords);
        $this->set('OurVisionCategoriesList', $Visionrecords);
        $this->set('AboutUsContent', $AboutUsContent);
        $this->set('HistoryPhilosophyContent', $HistoryPhilosophyContent);
        $this->set('SupportPhilanthropy', $SupportPhilanthropy);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->addCss('css/aboutus.css');
        $this->_template->addCss('css/bootstrap.min.css');
        $this->_template->render();
    }

    public function aboutusimage($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE || !false == $res && $res['afile_type'] == AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
        }
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'MINITHUMB':
                $w = 40;
                $h = 40;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'THUMB':
                $w = 50;
                $h = 50;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'MEDIUM':
                $w = 150;
                $h = 150;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'HIGH':
                $w = 400;
                $h = 400;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }


    public function meettheteamimage($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE || !false == $res && $res['afile_type'] == AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_MEET_THE_TEAM_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
        }
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'MINITHUMB':
                $w = 40;
                $h = 40;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'THUMB':
                $w = 50;
                $h = 50;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'MEDIUM':
                $w = 150;
                $h = 150;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'HIGH':
                $w = 400;
                $h = 400;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function ourvisionimage($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_OUR_VISION_IMAGE || !false == $res && $res['afile_type'] == AttachedFile::FILETYPE_OUR_VISION_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_OUR_VISION_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
        }
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'MINITHUMB':
                $w = 40;
                $h = 40;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'THUMB':
                $w = 50;
                $h = 50;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'MEDIUM':
                $w = 150;
                $h = 150;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'HIGH':
                $w = 400;
                $h = 400;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function ourmissionimage($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_OUR_MISSION_IMAGE || !false == $res && $res['afile_type'] == AttachedFile::FILETYPE_OUR_MISSION_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_OUR_MISSION_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
        }
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'MINITHUMB':
                $w = 40;
                $h = 40;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'THUMB':
                $w = 50;
                $h = 50;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'MEDIUM':
                $w = 150;
                $h = 150;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'HIGH':
                $w = 400;
                $h = 400;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }
}
