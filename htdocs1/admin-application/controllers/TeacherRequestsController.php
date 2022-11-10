<?php

class TeacherRequestsController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewTeacherApprovalRequests();
    }

    public function index()
    {
        $frmSrch = $this->getSearchForm();
        $this->set('frmSrch', $frmSrch);
        $this->_template->render();
    }

    public function search()
    {
        $srchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $srchForm->getFormDataFromArray($data);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $srch = new TeacherRequestSearch();
        $srch->joinUserCredentials();
        $srch->addMultipleFields([
            'utrequest_id',
            'utrequest_user_id',
            'utrequest_status',
            'utrequest_date',
            'utrequest_reference',
            'credential_username',
            'credential_email',
            'user_first_name',
            'user_last_name',
        ]);
        $srch->addOrder('utrequest_id', 'desc');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $laterstRecordsrch = new SearchBase('tbl_user_teacher_requests', 'str');
        $laterstRecordsrch->joinTable(User::DB_TBL, 'INNER JOIN', 'su.user_id = str.utrequest_user_id', 'su');
        $laterstRecordsrch->doNotCalculateRecords();
        $laterstRecordsrch->doNotLimitRecords();
        $laterstRecordsrch->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'su.user_id = scred.credential_user_id', 'scred');
        $laterstRecordsrch->addMultipleFields(['max(str.utrequest_id) as maxId']);
        if (!empty($post['keyword'])) {
            $cnd = $laterstRecordsrch->addCondition('utrequest_reference', '=', '%' . $post['keyword'] . '%', 'AND');
            $cnd->attachCondition('mysql_func_concat(`user_first_name`," ",`user_last_name`)', 'like', '%' . $post['keyword'] . '%', 'OR', true);
            $cnd->attachCondition('user_last_name', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cnd->attachCondition('credential_email', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cnd->attachCondition('credential_username', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cnd->attachCondition('utrequest_reference', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        $laterstRecordsrch->addGroupBy('str.utrequest_user_id');
        if (!empty($post['date_from'])) {
            if ($post['status'] > -1) {
                $laterstRecordsrch->addCondition('str.utrequest_status', '=', $post['status']);
            }
            $laterstRecordsrch->addCondition('str.utrequest_date', '>=', $post['date_from'] . ' 00:00:00');
        }
        if ($post['status'] > -1) {
            $srch->addCondition('tr.utrequest_status', '=', $post['status']);
        }
        if (!empty($post['date_to'])) {
            if ($post['status'] > -1) {
                $laterstRecordsrch->addCondition('str.utrequest_status', '=', $post['status']);
            }
            $laterstRecordsrch->addCondition('str.utrequest_date', '<=', $post['date_to'] . ' 23:59:59');
        }
        $srch->joinTable("(" . $laterstRecordsrch->getQuery() . ")", 'INNER JOIN', 'latestR.maxId = tr.utrequest_id', 'latestR');
        $rows = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('canEditTeacherApprovalRequests', $this->objPrivilege->canEditTeacherApprovalRequests(AdminAuthentication::getLoggedAdminId(), true));
        $this->set('reqStatusArr', TeacherRequest::getStatusArr($this->adminLangId));
        $this->set('arr_listing', $rows);
        $this->set('postedData', $post);
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $pagingArr = [
            'page' => $page,
            'pageCount' => $srch->pages(),
            'recordCount' => $srch->recordCount(),
            'adminLangId' => $this->adminLangId
        ];
        $this->set('pagingArr', $pagingArr);
        $this->_template->render(false, false);
    }

    public function view($utrequest_id)
    {
        $utrequest_id = FatUtility::int($utrequest_id);
        if ($utrequest_id < 1) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Request', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $srch = new TeacherRequestSearch();
        $srch->addCondition('utrequest_id', '=', $utrequest_id);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addMultipleFields([
            'utrequest_id',
            'utrequest_user_id',
            'utrequest_reference',
            'utrequest_date',
            'utrequest_attempts',
            'utrequest_comments',
            'utrequest_status',
            'utrequest_first_name',
            'utrequest_last_name',
            'utrequest_gender',
            'utrequest_phone_number',
            'utrequest_phone_code',
            'utrequest_video_link',
            'utrequest_profile_info',
            'utrequest_teach_slanguage_id',
            'utrequest_language_speak',
            'utrequest_language_speak_proficiency'
        ]);
        $srch->addGroupBy('utrequest_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!$row) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Request', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $row['utrequest_teach_slanguage_id'] = (!empty($row['utrequest_teach_slanguage_id'])) ? json_decode($row['utrequest_teach_slanguage_id'], true) : [];
        $row['utrequest_language_speak'] = (!empty($row['utrequest_language_speak'])) ? json_decode($row['utrequest_language_speak'], true) : [];
        $row['utrequest_language_speak_proficiency'] = (!empty($row['utrequest_language_speak_proficiency'])) ? json_decode($row['utrequest_language_speak_proficiency'], true) : [];
        $this->set('row', $row);
        /* photoId Proof row[ */
        $photo_id_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_TEACHER_APPROVAL_USER_APPROVAL_PROOF, $row['utrequest_user_id']);
        $this->set('photo_id_row', $photo_id_row);
        /* ] */
        /* previous request  data[ */
        $srch = new TeacherRequestSearch();
        $srch->addCondition('utrequest_user_id', '=', $row['utrequest_user_id']);
        $srch->addCondition('utrequest_id', '!=', $utrequest_id);
        $srch->addOrder('utrequest_id', 'desc');
        $rs = $srch->getResultSet();
        $otherRequest = FatApp::getDb()->fetchAll($rs);
        /* ] */
        $this->set('spokenLanguagesArr', SpokenLanguage::getAllLangs($this->adminLangId));
        $this->set('TeachingLanguagesArr', TeachingLanguage::getAllLangs($this->adminLangId));
        $this->set('spokenLanguageProfArr', SpokenLanguage::getProficiencyArr($this->adminLangId));
        $this->set('otherRequest', $otherRequest);
        $this->_template->render(false, false);
    }

    public function viewProfilePic($userId)
    {
        $userId = FatUtility::int($userId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_TEACHER_APPROVAL_USER_PROFILE_IMAGE, $userId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $w = 100;
        $h = 100;
        AttachedFile::displayImage($image_name, $w, $h);
    }

    public function teacherRequestUpdateForm($utrequest_id)
    {
        $utrequest_id = FatUtility::int($utrequest_id);
        if ($utrequest_id < 1) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Request', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        /*  */
        $srch = new TeacherRequestSearch();
        $srch->addCondition('utrequest_id', '=', $utrequest_id);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addMultipleFields(['utrequest_id']);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        /* ] */
        if (!$row) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Request', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getTeacherRequestUpdateForm();
        $frm->fill(['utrequest_id' => $utrequest_id]);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setUpTeacherRequestStatus()
    {
        $this->objPrivilege->canEditTeacherApprovalRequests();
        $frm = $this->getTeacherRequestUpdateForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $utrequest_id = FatApp::getPostedData('utrequest_id', FatUtility::VAR_INT, 0);
        $comment = FatApp::getPostedData('utrequest_comments', FatUtility::VAR_STRING, '');
        /* [ */
        $srch = new TeacherRequestSearch();
        $srch->joinUserCredentials();
        $srch->joinTable(UserSetting::DB_TBL, 'LEFT JOIN', 'us.us_user_id = u.user_id', 'us');
        $srch->addCondition('utrequest_id', '=', $utrequest_id);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addMultipleFields([
            'us_site_lang',
            'utrequest_status',
            'utrequest_user_id',
            'utrequest_comments',
            'utrequest_reference',
            'user_first_name',
            'user_last_name',
            'credential_email',
            'utrequest_first_name',
            'utrequest_last_name',
            'utrequest_gender',
            'utrequest_phone_number',
            'utrequest_phone_code',
            'utrequest_profile_info',
            'utrequest_video_link',
            'utrequest_teach_slanguage_id',
            'utrequest_language_speak',
            'utrequest_language_speak_proficiency'
        ]);
        $rs = $srch->getResultSet();
        $requestRow = FatApp::getDb()->fetch($rs);
        if (!$requestRow) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Request', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* ] */
        $requestRow['utrequest_comments'] = $comment;
        $statusArr = TeacherRequest::getStatusArr($this->adminLangId);
        unset($statusArr[TeacherRequest::STATUS_PENDING]);
        if ($requestRow['utrequest_status'] != TeacherRequest::STATUS_PENDING) {
            Message::addErrorMessage(Label::getLabel('LBL_Request_Status_Already_Changed', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        if ($post['utrequest_status'] == $requestRow['utrequest_status']) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Status_Request', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $post['utrequest_status_change_date'] = date('Y-m-d H:i:s');
        $TeacherRequest = new TeacherRequest($utrequest_id);
        $TeacherRequest->assignValues($post);
        if (!$TeacherRequest->save()) {
            Message::addErrorMessage($TeacherRequest->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        /* User is marked as teacher and request data synced with profile data[ */
        if ($post['utrequest_status'] == TeacherRequest::STATUS_APPROVED && $requestRow['utrequest_status'] != TeacherRequest::STATUS_APPROVED) {
            /* syncing user common data[ */
            $user = new User($requestRow['utrequest_user_id']);
            $userUpdateDataArr = [
                'user_is_teacher' => 1,
                'user_preferred_dashboard' => User::USER_TEACHER_DASHBOARD,
                'user_first_name' => $requestRow['utrequest_first_name'],
                'user_last_name' => $requestRow['utrequest_last_name'],
                'user_gender' => $requestRow['utrequest_gender'],
                'user_phone' => $requestRow['utrequest_phone_number'],
                'user_phone_code' => $requestRow['utrequest_phone_code'],
                'user_profile_info' => $requestRow['utrequest_profile_info'],
            ];
            $user->assignValues($userUpdateDataArr);
            if (true != $user->save()) {
                Message::addErrorMessage($user->getError());
                FatUtility::dieWithError(Message::getHtml());
            }
            /* ] */
            /* syncing user profile pic[ */
            $requestedProfilePicRow = AttachedFile::getAttachment(AttachedFile::FILETYPE_TEACHER_APPROVAL_USER_PROFILE_IMAGE, $requestRow['utrequest_user_id']);
            if (!empty($requestedProfilePicRow['afile_physical_path'] ?? '')) {
                $profilePicDataArr = [
                    'afile_type' => AttachedFile::FILETYPE_USER_PROFILE_IMAGE,
                    'afile_record_id' => $requestRow['utrequest_user_id'],
                    'afile_record_subid' => 0,
                    'afile_lang_id' => 0,
                    'afile_screen' => 0,
                    'afile_physical_path' => $requestedProfilePicRow['afile_physical_path'],
                    'afile_name' => $requestedProfilePicRow['afile_name'],
                    'afile_display_order' => $requestedProfilePicRow['afile_display_order'],
                ];
                $db = FatApp::getDb();
                if (!$db->insertFromArray(AttachedFile::DB_TBL, $profilePicDataArr, false, [], $profilePicDataArr)) {
                    Message::addErrorMessage($db->getError());
                    FatUtility::dieWithError(Message::getHtml());
                }
            }
            /* ] */
            /* syncing teacher settings[ */
            $userSetting = new UserSetting($requestRow['utrequest_user_id']);
            $settingDataArr = [
                'us_user_id' => $requestRow['utrequest_user_id'],
                'us_video_link' => $requestRow['utrequest_video_link'],
            ];
            if (true != $userSetting->saveData($settingDataArr)) {
                Message::addErrorMessage($userSetting->getError());
                FatUtility::dieWithError(Message::getHtml());
            }
            /* ] */
            /* syncing teach languages[ */
            $db = FatApp::getDb();
            $teachLanguagesArr = json_decode($requestRow['utrequest_teach_slanguage_id'], true);
            if (!empty($teachLanguagesArr)) {
                foreach ($teachLanguagesArr as $key => $slanguage_id) {
                    $dataArr = [
                        'utl_user_id' => $requestRow['utrequest_user_id'],
                        'utl_tlanguage_id' => $slanguage_id,
                    ];
                    if (!$db->insertFromArray(UserToLanguage::DB_TBL_TEACH, $dataArr, false, [], $dataArr)) {
                        Message::addErrorMessage($db->getError());
                        FatUtility::dieWithError(Message::getHtml());
                    }
                }
            }
            /* ] */
            /* syncing spoken languages[ */
            $db = FatApp::getDb();
            $spokenLanguagesArr = json_decode($requestRow['utrequest_language_speak'], true);
            $spokenLanguageProfArr = json_decode($requestRow['utrequest_language_speak_proficiency'], true);
            if (!empty($spokenLanguagesArr)) {
                foreach ($spokenLanguagesArr as $key => $slanguage_id) {
                    $dataArr = [
                        'utsl_user_id' => $requestRow['utrequest_user_id'],
                        'utsl_slanguage_id' => $slanguage_id,
                        'utsl_proficiency' => $spokenLanguageProfArr[$key]
                    ];
                    if (!$db->insertFromArray(User::DB_TBL_USER_TO_SPOKEN_LANGUAGES, $dataArr, false, [], $dataArr)) {
                        Message::addErrorMessage($db->getError());
                        FatUtility::dieWithError(Message::getHtml());
                    }
                }
            }
            /* ] */
            /* activating qualifications[ */
            $dataArr = ['uqualification_active' => 1];
            if (!$db->updateFromArray(UserQualification::DB_TBL, $dataArr, ['smt' => 'uqualification_user_id = ?', 'vals' => [$requestRow['utrequest_user_id']]])) {
                Message::addErrorMessage($db->getError());
                FatUtility::dieWithError(Message::getHtml());
            }
            /* ] */
            $userNotification = new UserNotifications($requestRow['utrequest_user_id']);
            $userNotification->sendTeacherApprovalNotification($requestRow['us_site_lang']);
            /* Update Teacher's Stat */
            $stat = new TeacherStat($requestRow['utrequest_user_id']);
            $stat->setTeachLangPrices();
            $stat->setSpeakLang();
            $stat->setQualification();
        }
        /* ] */
        /* email sending[ */
        $email = new EmailHandler();
        $requestRow['utrequest_status'] = $post['utrequest_status'];
        if (!$email->SendTeacherRequestStatusChangeNotification($this->adminLangId, $requestRow)) {
            Message::addErrorMessage(Label::getLabel('LBL_Email_Could_Not_Be_Sent', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* ] */
        $this->set('msg', Label::getLabel('LBL_Status_Updated_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function searchQualifications($user_id)
    {
        $user_id = FatUtility::int($user_id);
        if ($user_id < 1) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Request', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $srch = new UserQualificationSearch();
        $srch->addCondition('uqualification_user_id', '=', $user_id);
        $rs = $srch->getResultSet();
        $arr_listing = FatApp::getDb()->fetchAll($rs);
        $this->set('arr_listing', $arr_listing);
        $this->_template->render(false, false);
    }

    private function getTeacherRequestUpdateForm()
    {
        $frm = new Form('frmTeacherRequestUpdateForm');
        $fld = $frm->addHiddenField('', 'utrequest_id', 0);
        $fld->requirements()->setInt();
        $statusArr = TeacherRequest::getStatusArr($this->adminLangId);
        unset($statusArr[TeacherRequest::STATUS_PENDING]);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'utrequest_status', $statusArr, '')->requirements()->setRequired();
        $frm->addTextArea('', 'utrequest_comments', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update', $this->adminLangId));
        return $frm;
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setRequired();
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword', '');
        $statusArr = ['-1' => Label::getLabel('LBL_All', $this->adminLangId)] + TeacherRequest::getStatusArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'status', $statusArr, '', [], '');
        $frm->addDateField(Label::getLabel('LBL_Date_From', $this->adminLangId), 'date_from', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addDateField(Label::getLabel('LBL_Date_To', $this->adminLangId), 'date_to', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    public function photoIdFile($recordId)
    {
        $recordId = FatUtility::int($recordId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_TEACHER_APPROVAL_USER_APPROVAL_PROOF, $recordId, 0);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        AttachedFile::displayOriginalImage($image_name);
    }

    public function downloadResume($userId, $subRecordId)
    {
        $subRecordId = FatUtility::int($subRecordId);
        if ($subRecordId < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatApp::redirectUser(CommonHelper::generateUrl('TeacherRequests'));
        }
        $fileRow = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_QUALIFICATION_FILE, $userId, $subRecordId);
        if (!$fileRow || $fileRow['afile_physical_path'] == "") {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatApp::redirectUser(CommonHelper::generateUrl('TeacherRequests'));
        }
        AttachedFile::downloadFile($fileRow['afile_name'], $fileRow['afile_physical_path']);
    }
}
