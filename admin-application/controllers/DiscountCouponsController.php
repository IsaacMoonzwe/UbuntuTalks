<?php

class DiscountCouponsController extends AdminBaseController
{

    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewDiscountCoupons($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditDiscountCoupons($this->admin_id, true);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $frmSearch = $this->getSearchForm();
        $this->set("frmSearch", $frmSearch);
        $this->_template->render();
    }

    public function search()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $srch = DiscountCoupons::getSearchObject($this->adminLangId, false);
        if (!empty($post['keyword'])) {
            $cnd = $srch->addCondition('dc.coupon_identifier', 'like', '%' . $post['keyword'] . '%');
            $cnd->attachCondition('dc.coupon_code', 'like', '%' . $post['keyword'] . '%');
            $cnd->attachCondition('dc_l.coupon_title', 'like', '%' . $post['keyword'] . '%');
        }
        $srch->addOrder('datediff(coupon_end_date,"' . date('Y-m-d') . '")', 'DESC');
        $srch->addOrder('coupon_id', 'DESC');
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set('activeInactiveArr', $activeInactiveArr);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $frm = $this->getForm($this->adminLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $coupon_id = $post['coupon_id'];
        unset($post['coupon_id']);
        $record = new DiscountCoupons($coupon_id);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($coupon_id > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = DiscountCoupons::getAttributesByLangId($langId, $coupon_id)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $coupon_id = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', Label::getLabel('MSG_Coupon_Setup_Successful.', $this->adminLangId));
        $this->set('couponId', $coupon_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $post = FatApp::getPostedData();
        $coupon_id = $post['coupon_id'];
        $lang_id = $post['lang_id'];
        if ($coupon_id == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($coupon_id, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['coupon_id']);
        unset($post['lang_id']);
        $data = [
            'couponlang_lang_id' => $lang_id,
            'couponlang_coupon_id' => $coupon_id,
            'coupon_title' => $post['coupon_title'],
            'coupon_description' => $post['coupon_description'],
        ];
        $obj = new DiscountCoupons($coupon_id);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = DiscountCoupons::getAttributesByLangId($langId, $coupon_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', Label::getLabel('MSG_Coupon_Setup_Successful.', $this->adminLangId));
        $this->set('couponId', $coupon_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function form($coupon_id = 0)
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $coupon_id = FatUtility::int($coupon_id);
        $frm = $this->getForm($this->adminLangId);
        if (0 < $coupon_id) {
            $data = DiscountCoupons::getAttributesById($coupon_id);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        } else {
            $frm->fill(['coupon_id' => $coupon_id]);
        }
        $this->set('couponDiscountIn', isset($data['coupon_discount_in_percent']) ? $data['coupon_discount_in_percent'] : applicationConstants::PERCENTAGE);
        $this->set('languages', Language::getAllNames());
        $this->set('coupon_id', $coupon_id);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function langForm($coupon_id = 0, $lang_id = 0)
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $coupon_id = FatUtility::int($coupon_id);
        $lang_id = FatUtility::int($lang_id);
        if ($coupon_id == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($coupon_id, $lang_id);
        $langData = DiscountCoupons::getAttributesByLangId($lang_id, $coupon_id);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $bannerImage = AttachedFile::getAttachment(AttachedFile::FILETYPE_DISCOUNT_COUPON_IMAGE, $coupon_id, 0, $lang_id);
        $this->set('bannerImage', $bannerImage);
        $this->set('languages', Language::getAllNames());
        $this->set('coupon_id', $coupon_id);
        $this->set('coupon_lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $coupon_id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($coupon_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $data = DiscountCoupons::getAttributesById($coupon_id);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $obj = new DiscountCoupons($coupon_id);
        $obj->assignValues(array(DiscountCoupons::tblFld('deleted') => 1));
        if (!$obj->save()) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    public function usesHistory($coupon_id)
    {
        $coupon_id = FatUtility::int($coupon_id);
        if (1 > $coupon_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $couponData = DiscountCoupons::getAttributesById($coupon_id, ['coupon_code']);
        if ($couponData == false) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $post = FatApp::getPostedData();
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : $post['page'];
        $page = (empty($page) || $page <= 0) ? 1 : FatUtility::int($page);
        $srch = CouponHistory::getSearchObject();
        $srch->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'user_id = couponhistory_user_id');
        $srch->joinTable(User::DB_TBL_CRED, 'LEFT OUTER JOIN', 'credential_user_id = user_id');
        $srch->addCondition('couponhistory_coupon_id', '=', $coupon_id);
        $srch->addMultipleFields(['couponhistory_id', 'couponhistory_coupon_id', 'couponhistory_order_id', 'couponhistory_user_id', 'couponhistory_amount', 'couponhistory_added_on', 'credential_username']);
        $srch->addOrder('couponhistory_added_on', 'DESC');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set('couponId', $coupon_id);
        $this->set('couponData', $couponData);
        $this->_template->render(false, false);
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditDiscountCoupons();
        $couponId = FatApp::getPostedData('couponId', FatUtility::VAR_INT, 0);
        if (0 >= $couponId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = DiscountCoupons::getAttributesById($couponId, ['coupon_id', 'coupon_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $status = ($data['coupon_active'] == applicationConstants::ACTIVE) ? applicationConstants::INACTIVE : applicationConstants::ACTIVE;
        $obj = new DiscountCoupons($couponId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    private function getSearchForm()
    {
        $frm = new Form('frmCouponSearch');
        $f1 = $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('MSG_Clear_Search', $this->adminLangId), ['onclick' => 'clearSearch()']);
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    private function getForm(int $langId)
    {
        $frm = new Form('frmCoupon');
        $frm->addHiddenField('', 'coupon_id');
        $frm->addRequiredField(Label::getLabel('LBL_Coupon_Identifier', $langId), 'coupon_identifier');
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Coupon_Code', $langId), 'coupon_code');
        $fld->setUnique(DiscountCoupons::DB_TBL, 'coupon_code', 'coupon_id', 'coupon_id', 'coupon_id');
        $frm->addSelectBox(Label::getLabel('LBL_Discount_in', $langId), 'coupon_discount_in_percent', applicationConstants::getPercentageFlatArr($langId), '', [], '');
        $frm->addFloatField(Label::getLabel('LBL_Discount_Value', $langId), 'coupon_discount_value')->requirements()->setFloatPositive();
        $frm->addFloatField(Label::getLabel('LBL_Min_Order_Value', $langId), 'coupon_min_order_value');
        $frm->addFloatField(Label::getLabel('LBL_Max_Discount_Value', $langId), 'coupon_max_discount_value');
        $frm->addDateField(Label::getLabel('LBL_Date_From', $langId), 'coupon_start_date', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender']);
        $frm->addDateField(Label::getLabel('LBL_Date_To', $langId), 'coupon_end_date', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender']);
        $frm->addIntegerField(Label::getLabel('LBL_Uses_Per_Coupon', $langId), 'coupon_uses_count', 1)->requirements()->setIntPositive();
        $frm->addIntegerField(Label::getLabel('LBL_Uses_Per_Customer', $langId), 'coupon_uses_coustomer', 1)->requirements()->setIntPositive();
        $frm->addSelectBox(Label::getLabel('LBL_Coupon_Status', $langId), 'coupon_active', applicationConstants::getActiveInactiveArr($langId), '', [], '');
        $couponMinOrderValueReqTrue = new FormFieldRequirement('coupon_min_order_value', 'value');
        $couponMinOrderValueReqTrue->setRequired();
        $couponMinOrderValueReqFalse = new FormFieldRequirement('coupon_min_order_value', 'value');
        $couponMinOrderValueReqFalse->setRequired(false);
        $couponMaxDiscountValueReqTrue = new FormFieldRequirement('coupon_max_discount_value', 'value');
        $couponMaxDiscountValueReqTrue->setRequired();
        $couponMaxDiscountValueReqTrue->setFloatPositive();
        $couponMaxDiscountValueReqTrue->setRange('0.01', '9999999999');
        $couponMaxDiscountValueReqFalse = new FormFieldRequirement('coupon_max_discount_value', 'value');
        $couponMaxDiscountValueReqFalse->setRequired(false);
        $discAmtPercLimit = new FormFieldRequirement('coupon_discount_value', Label::getLabel('LBL_Discount_Value', $langId));
        $discAmtPercLimit->setRange('0.01', '100');
        $discAmtFlatLimit = clone $discAmtPercLimit;
        $discAmtFlatLimit->setRange('0.01', '9999999999');
        $discTypFldReq = $frm->getField('coupon_discount_in_percent')->requirements();
        $discTypFldReq->addOnChangerequirementUpdate(applicationConstants::PERCENTAGE, 'eq', 'coupon_max_discount_value', $couponMaxDiscountValueReqTrue);
        $discTypFldReq->addOnChangerequirementUpdate(applicationConstants::FLAT, 'eq', 'coupon_max_discount_value', $couponMaxDiscountValueReqFalse);
        $discTypFldReq->addOnChangerequirementUpdate(applicationConstants::PERCENTAGE, 'eq', 'coupon_discount_value', $discAmtPercLimit);
        $discTypFldReq->addOnChangerequirementUpdate(applicationConstants::FLAT, 'eq', 'coupon_discount_value', $discAmtFlatLimit);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $langId));
        return $frm;
    }

    private function getLangForm($coupon_id = 0, $lang_id = 0)
    {
        $coupon_id = FatUtility::int($coupon_id);
        $lang_id = FatUtility::int($lang_id);
        $frm = new Form('frmCouponLang');
        $frm->addHiddenField('', 'coupon_id', $coupon_id);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Coupon_title', $this->adminLangId), 'coupon_title');
        $frm->addTextArea(Label::getLabel('LBL_Coupon_Description', $this->adminLangId), 'coupon_description')->requirements()->setLength(0, 250);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
