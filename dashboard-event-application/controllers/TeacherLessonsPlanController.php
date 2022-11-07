<?php

class TeacherLessonsPlanController extends LoggedUserController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $learnerAllowedActions = ['getFileById'];
        if (!User::isTeacher() && !in_array($action, $learnerAllowedActions)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
    }

    public function index()
    {
        $this->_template->addJs('js/jquery-confirm.min.js');
        $this->set('serachForm', $this->getSearchForm());
        $this->_template->render();
    }

    public function getFrm()
    {
        $frm = new Form('lessonPlanFrm');
        $frm->addRequiredField(Label::getLabel('LBl_Title'), 'tlpn_title');
        $frm->addTextarea(Label::getLabel('LBl_Description'), 'tlpn_description');
        $frm->addSelectBox(Label::getLabel('LBl_Difficulty_Level'), 'tlpn_level', LessonPlan::getDifficultyArr())->requirement->setRequired(true);
        $fld = $frm->addFileUpload(Label::getLabel('LBl_Plan_Files'), 'tlpn_file[]', ['multiple' => 'multiple', 'id' => 'tlpn_file']);
        $fld->htmlAfterField = "<small>" . Label::getLabel('LBL_NOTE:_Allowed_Lesson_File_types!') . "</small>";
        $frm->addHtml('', 'tlpn_file_display', '', ['id' => 'tlpn_file_display']);
        $fld = $frm->addTextarea(Label::getLabel('LBl_Links'), 'tlpn_links');
        $fld->htmlAfterField = "<small>" . Label::getLabel('LBl_Links') . "</small>";
        $frm->addFileUpload(Label::getLabel('LBl_Plan_Banner_Image'), 'tlpn_image');
        $frm->addHiddenField('', 'tlpn_id');
        $frm->addSubmitButton('', 'submit', 'Save');
        return $frm;
    }

    public function add($lessonPlanId = 0)
    {
        $frm = $this->getFrm();
        if ($lessonPlanId > 0) {
            $data = LessonPlan::getAttributesById($lessonPlanId);
            $frm->fill($data);
        }
        $this->set('userId', UserAuthentication::getLoggedUserId());
        $this->set('lessonPlanId', $lessonPlanId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function uploadMultipleFiles()
    {
        $lessonPlan = new LessonPlan();
        $lessonPlanId = $lessonPlan->getMainTableRecordId() + 1;
        for ($i = 0; $i < count($_FILES['tlpn_file']['name']); $i++) {
            if (!empty($_FILES['tlpn_file']['name'][$i])) {
                $fileHandlerObj = new AttachedFile();
                if (!$res = $fileHandlerObj->saveDoc($_FILES['tlpn_file']['tmp_name'][$i], AttachedFile::FILETYPE_LESSON_PLAN_FILE, $lessonPlanId, 0, $_FILES['tlpn_file']['name'][$i], 0)) {
                    Message::addErrorMessage($fileHandlerObj->getError());
                    FatUtility::dieJsonError(Message::getHtml());
                }
            }
        }
    }

    public function setup()
    {
        $maxUploadSize = min(ini_get('upload_max_filesize'), 3);
        $postData = FatApp::getPostedData();
        if (empty($postData)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_DATA_NOT_POST_OR_CHECK_YOU_FILE_SIZE'));
        }
        $frm = $this->getFrm();
        $post = $frm->getFormDataFromArray($postData);
        if ($post === false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $post['tlpn_user_id'] = UserAuthentication::getLoggedUserId();
        $lessonPlanId = FatApp::getPostedData('tlpn_id', FatUtility::VAR_INT, 0);
        $lessonPlan = new LessonPlan($lessonPlanId);
        $lessonPlan->assignValues($post);
        if (true !== $lessonPlan->save()) {
            FatUtility::dieJsonError($lessonPlan->getError());
        }
        $lessonPlanId = $lessonPlan->getMainTableRecordId();
        $_FILES['tlpn_file']['size'];
        if (!empty($_FILES['tlpn_file']) && count($_FILES['tlpn_file']['name']) > 0) {
            $size = array_sum($_FILES['tlpn_file']['size']) / 1048576;
            if ($size > 3) {
                $label = Label::getLabel('LBL_YOU_{name}_FILE_SIZE_IS_MORE_THEN_{size}_MB');
                $label = str_replace(['{name}', 'size'], [Label::getLabel('LBL_PLAN_FILES'), 3], $label);
            }
            for ($i = 0; $i < count($_FILES['tlpn_file']['name']); $i++) {
                if (!empty($_FILES['tlpn_file']['name'][$i])) {
                    $fileHandlerObj = new AttachedFile();
                    if (!$fileHandlerObj->saveDoc($_FILES['tlpn_file']['tmp_name'][$i], AttachedFile::FILETYPE_LESSON_PLAN_FILE, $lessonPlanId, 0, $_FILES['tlpn_file']['name'][$i], 0)) {
                        $db->rollbackTransaction();
                        FatUtility::dieJsonError($fileHandlerObj->getError());
                    }
                }
            }
        }

        if (!empty($_FILES['tlpn_image']['name'])) {
            $size = ($_FILES['tlpn_image']['size'] / 1048576);
            if ($_FILES['tlpn_image']['size'] > 1) {
                $label = Label::getLabel('LBL_YOU_{name}_FILE_SIZE_IS_MORE_THEN_{size}_MB');
                $label = str_replace(['{name}', 'size'], [Label::getLabel('LBL_PLAN_BANNER_IMAGE'), 1], $label);
            }
            $fileHandlerObj = new AttachedFile();
            if (!$fileHandlerObj->saveImage($_FILES['tlpn_image']['tmp_name'], AttachedFile::FILETYPE_LESSON_PLAN_IMAGE, $lessonPlanId, 0, $_FILES['tlpn_image']['name'], 0, true)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($fileHandlerObj->getError());
            }
        }
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_LESSON_PLAN_SAVED_SUCCESSFULLY!'));
    }

    public function removeLessonSetup()
    {
        $db = FatApp::getDb();
        $post = FatApp::getPostedData();
        if (isset($post['submit']) && empty($post)) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        if (!$db->deleteRecords('tbl_scheduled_lessons_to_teachers_lessons_plan', ['smt' => 'ltp_tlpn_id = ?', 'vals' => [$post['lessonPlanId']]])) {
            FatUtility::dieWithError($db->getError());
        }
        $lessonPlan = new LessonPlan($post['lessonPlanId']);
        if (!$lessonPlan->deleteRecord()) {
            FatUtility::dieWithError($db->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Student_Remove_Successfully!'));
    }

    public function removeFile($fileId)
    {
        $fileId = FatUtility::int($fileId);
        $lessonPlan = new AttachedFile($fileId);
        $lessonPlan->deleteRecord();
        if ($lessonPlan->getError()) {
            FatUtility::dieJsonError($lessonPlan->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel("Record Deleted Successfully!"));
    }

    public function getListing()
    {
        $searchForm = $this->getSearchForm();
        $post = $searchForm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($searchForm->getValidationErrors());
        }
        $srch = new LessonPlanSearch(false);
        $srch->addMultipleFields([
            'tlpn_id',
            'tlpn_title',
            'tlpn_level',
            'tlpn_user_id',
            'tlpn_description',
        ]);
        $srch->addCondition('tlpn_user_id', '=', UserAuthentication::getLoggedUserId());
        if (!empty($post['keyword'])) {
            $srch->addCondition('tlpn_title', 'like', '%' . $post['keyword'] . '%');
        }
        if (!empty($post['status'])) {
            $srch->addCondition('tlpn_level', '=', $post['status']);
        }
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $page = (0 >= $page) ? 1 : $page;
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $rs = $srch->getResultSet();
        $totalRecords = $srch->recordCount();
        $rows = FatApp::getDb()->fetchAll($rs);
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $totalRecords,
        ];
        $this->set('userId', UserAuthentication::getLoggedUserId());
        $this->set('statusArr', LessonPlan::getDifficultyArr());
        $this->set('countData', $totalRecords);
        $this->set('pagingArr', $pagingArr);
        $this->set('postedData', FatApp::getPostedData());
        $this->set('lessonsPlanData', $rows);
        $this->_template->render(false, false);
    }

    public function lessonPlanFile($lessonPlanId = 0, $subRecordId = 0, $sizeType = '')
    {
        $recordId = FatUtility::int($lessonPlanId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_LESSON_PLAN_FILE, $recordId, $subRecordId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            default:
                AttachedFile::displayOriginalImage($image_name);
                break;
        }
    }

    public function lessonPlanImage($lessonPlanId = 0, $subRecordId = 0, $sizeType = '')
    {
        $recordId = FatUtility::int($lessonPlanId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_LESSON_PLAN_IMAGE, $recordId, $subRecordId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            default:
                $w = 60;
                $h = 60;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
        }
    }

    public function getFileById($afile_id = 0, $sizeType = '')
    {
        $afile_id = FatUtility::int($afile_id);
        $file_row = AttachedFile::getAttributesById($afile_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        AttachedFile::downloadAttachment($image_name, $file_row['afile_name']);
    }

    private function getSearchForm()
    {
        $form = new Form('lessonPlanSerach');
        $form->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $form->addSelectBox(Label::getLabel('LBL_Status'), 'status', LessonPlan::getDifficultyArr(), '', [], Label::getLabel('LBL_All'))->requirements()->setInt();
        $form->addHiddenField('', 'page', 1)->requirements()->setIntPositive();
        $submitBtn = $form->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $resetField = $form->addResetButton('', 'btn_reset', Label::getLabel('LBL_Reset'));
        $submitBtn->attachField($resetField);
        return $form;
    }

}
