<?php

class EventCheckoutController extends FatController
{

    private $cartObj;

    public function __construct($action)
    {
        parent::__construct($action);
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        // $this->cartObj = new EventCart(EventUserAuthentication::getLoggedUserId());
    }

    public function index()
    {
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        // $cartData = $this->cartObj->getCart($this->siteLangId,$fromKids);
        echo "<pre>";
        print_r($_SESSION['cart']);
        $cartData=$_SESSION['cart'];

        $criteria = ['isUserLogged' => true, 'hasItems' => true];
        if (!$this->isEligibleForNextStep($criteria)) {
            if (!Message::getErrorCount()) {
                Message::addErrorMessage(Label::getLabel('MSG_Something_went_wrong,_please_try_after_some_time.'));
            }
            FatApp::redirectUser(CommonHelper::generateUrl());
        }
       
        $confirmForm = $this->getConfirmFormWithNoAmount($this->siteLangId);
        if ($cartData['orderNetAmount'] <= 0) {
            $confirmForm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Confirm_Order'));
        }
        $cartForm = $this->addToCartForm();
        $this->set('confirmForm', $confirmForm);
        $this->set('cartData', $cartData);
        $this->set('cartForm', $cartForm);
        $this->_template->render();
    }

    public function listFinancialSummary()
    {
        $json = [];
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        // $cart = $this->cartObj->getCart($this->siteLangId,$fromKids);
        $cart=$_SESSION['cart'];
        $userWalletBalance = EventUser::getUserBalance(EventUserAuthentication::getLoggedUserId());
        $this->set('userWalletBalance', $userWalletBalance);
        $this->set('cart', $cart);
        $json['html'] = $this->_template->render(false, false, 'checkout/list-financial-summary.php', true, false);
        $json['couponApplied'] = isset($cart['cartDiscounts']['coupon_discount_total']) ? $cart['cartDiscounts']['coupon_discount_total'] : 0;
        FatUtility::dieJsonSuccess($json);
    }

    public function getCouponForm()
    {
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        // $cartSummary = $this->cartObj->getCart($this->siteLangId,$fromKids);

        $couponsList = DiscountCoupons::getValidCoupons($loggedUserId, $this->siteLangId, '', $orderId);
        $this->set('couponsList', $couponsList);
        // $this->set('cartSummary', $cartSummary);
        $PromoCouponsFrm = $this->getPromoCouponsForm($this->siteLangId);
        $this->set('PromoCouponsFrm', $PromoCouponsFrm);
        $this->_template->render(false, false);
    }
    public function selectionBookingForm()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $fromBack = FatApp::getPostedData('fromBack', FatUtility::VAR_INT, 0);      
        if($fromBack<=0){
            unset($_SESSION['fName']);
            unset($_SESSION['lName']);
            unset($_SESSION['kidsCount']);
            $fNames='';
            $lNames='';
        }
        else{
            $fNames = $_SESSION['fName'];
            $lNames = $_SESSION['lName'];
           $kidsCount=$_SESSION['kidsCount'];
        }
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        if (1 > $teacherId || $teacherId == $loggedUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher = $this->checkTeacherIsValid($teacherId);
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userTeachLanguage = new UserTeachLanguage($teacherId);
        $getUserTeachlanguages = $userTeachLanguage->getUserTeachlanguages($this->siteLangId, true, 'INNER JOIN');
        $getUserTeachlanguages->doNotCalculateRecords();
        $getUserTeachlanguages->addCondition('ustelgpr_price', '>', 0);
        $getUserTeachlanguages->addMultipleFields(['IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name', 'tlanguage_id']);
        $getUserTeachlanguages->addGroupBy('tlanguage_id');
        $teachLanguages = FatApp::getDb()->fetchAll($getUserTeachlanguages->getResultSet(), 'tlanguage_id');
        if (empty($teachLanguages)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_THIS_TEACHER_DOES_NOT_HAVE_ANY_TEACH_LANGUAGES'));
        }
        if (empty($languageId) || !array_key_exists($languageId, $teachLanguages)) {
            $languageId = key($teachLanguages);
        }
        $this->set('teachLanguages', $teachLanguages);
        $this->set('teacher', $teacher);
        $this->set('languageId', $languageId);
        $this->set('contactFrm', $contactFrm);
        $this->set('fNames', $fNames);
        $this->set('kidsCount', $kidsCount);
        $this->set('lNames', $lNames);
        $this->_template->render(false,false);

    }
    public function groupBookingForm()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $fromBack = FatApp::getPostedData('fromBack', FatUtility::VAR_INT, 0);      
        if($fromBack<=0){
            unset($_SESSION['fName']);
            unset($_SESSION['lName']);
            unset($_SESSION['kidsCount']);
            $fNames='';
            $lNames='';
        }
        else{
            $fNames = $_SESSION['fName'];
            $lNames = $_SESSION['lName'];
           $kidsCount=$_SESSION['kidsCount'];
        }
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $grpclsId = FatApp::getPostedData('grpclsId', FatUtility::VAR_INT, 0);
        $group_class=new SearchBase('tbl_group_classes');
        $group_class->addCondition('grpcls_id',"=",$grpclsId);
        $selected_group_class=FatApp::getDb()->fetchAll($group_class->getResultSet());
        $max_learners=$selected_group_class[0]['grpcls_max_learner'];
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        if (1 > $teacherId || $teacherId == $loggedUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher = $this->checkTeacherIsValid($teacherId);
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userTeachLanguage = new UserTeachLanguage($teacherId);
        $getUserTeachlanguages = $userTeachLanguage->getUserTeachlanguages($this->siteLangId, true, 'INNER JOIN');
        $getUserTeachlanguages->doNotCalculateRecords();
        $getUserTeachlanguages->addCondition('ustelgpr_price', '>', 0);
        $getUserTeachlanguages->addMultipleFields(['IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name', 'tlanguage_id']);
        $getUserTeachlanguages->addGroupBy('tlanguage_id');
        $teachLanguages = FatApp::getDb()->fetchAll($getUserTeachlanguages->getResultSet(), 'tlanguage_id');
        if (empty($teachLanguages)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_THIS_TEACHER_DOES_NOT_HAVE_ANY_TEACH_LANGUAGES'));
        }
        if (empty($languageId) || !array_key_exists($languageId, $teachLanguages)) {
            $languageId = key($teachLanguages);
        }
        $this->set('num_of_learners',$max_learners);
        $this->set('teachLanguages', $teachLanguages);
        $this->set('teacher', $teacher);
        $this->set('languageId', $languageId);
        $this->set('contactFrm', $contactFrm);
        $this->set('fNames', $fNames);
        $this->set('kidsCount', $kidsCount);
        $this->set('lNames', $lNames);
        $this->_template->render(false,false);
    }

    public function kidsBookingForm()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $fromBack = FatApp::getPostedData('fromBack', FatUtility::VAR_INT, 0);      
        if($fromBack<=0){
            unset($_SESSION['fName']);
            unset($_SESSION['lName']);
            unset($_SESSION['kidsCount']);
            $fNames='';
            $lNames='';
        }
        else{
            $fNames = $_SESSION['fName'];
            $lNames = $_SESSION['lName'];
           $kidsCount=$_SESSION['kidsCount'];
        }
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        if (1 > $teacherId || $teacherId == $loggedUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher = $this->checkTeacherIsValid($teacherId);
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userTeachLanguage = new UserTeachLanguage($teacherId);
        $getUserTeachlanguages = $userTeachLanguage->getUserTeachlanguages($this->siteLangId, true, 'INNER JOIN');
        $getUserTeachlanguages->doNotCalculateRecords();
        $getUserTeachlanguages->addCondition('ustelgpr_price', '>', 0);
        $getUserTeachlanguages->addMultipleFields(['IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name', 'tlanguage_id']);
        $getUserTeachlanguages->addGroupBy('tlanguage_id');
        $teachLanguages = FatApp::getDb()->fetchAll($getUserTeachlanguages->getResultSet(), 'tlanguage_id');
        if (empty($teachLanguages)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_THIS_TEACHER_DOES_NOT_HAVE_ANY_TEACH_LANGUAGES'));
        }
        if (empty($languageId) || !array_key_exists($languageId, $teachLanguages)) {
            $languageId = key($teachLanguages);
        }
        $this->set('teachLanguages', $teachLanguages);
        $this->set('teacher', $teacher);
        $this->set('languageId', $languageId);
        $this->set('contactFrm', $contactFrm);
        $this->set('fNames', $fNames);
        $this->set('kidsCount', $kidsCount);
        $this->set('lNames', $lNames);
        $this->_template->render(false,false);
    }

    private function contactUsForm(int $langId)
    {
        $frm = new Form('frmContact');
        $number_of_children = ['1' => '1', '2' => '2', '3' => '3','4' => '4','5' => '5','6' => '6','7' => '7','8' => '8','9' => '9','10' => '10'];
        $frm->addRequiredField(Label::getLabel('LBL_First_Name', $langId), 'firstname', '');
        $frm->addRequiredField(Label::getLabel('LBL_Last_Name', $langId), 'lastname', '');
        $frm->addSelectBox(Label::getLabel('LBL_Active_Users', $langId), 'child', $number_of_children, -1, [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }

    public function getUserTeachLangues()
    {
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        if (1 > $teacherId || $teacherId == $loggedUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher = $this->checkTeacherIsValid($teacherId);
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userTeachLanguage = new UserTeachLanguage($teacherId);
        $getUserTeachlanguages = $userTeachLanguage->getUserTeachlanguages($this->siteLangId, true, 'INNER JOIN');
        $getUserTeachlanguages->doNotCalculateRecords();
        $getUserTeachlanguages->addCondition('ustelgpr_price', '>', 0);
        $getUserTeachlanguages->addMultipleFields(['IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name', 'tlanguage_id']);
        $getUserTeachlanguages->addGroupBy('tlanguage_id');
        $teachLanguages = FatApp::getDb()->fetchAll($getUserTeachlanguages->getResultSet(), 'tlanguage_id');
        if (empty($teachLanguages)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_THIS_TEACHER_DOES_NOT_HAVE_ANY_TEACH_LANGUAGES'));
        }
        if (empty($languageId) || !array_key_exists($languageId, $teachLanguages)) {
            $languageId = key($teachLanguages);
        }
        $this->set('teachLanguages', $teachLanguages);
        $this->set('teacher', $teacher);
        $this->set('languageId', $languageId);
        $this->_template->render(false, false);
    }

    public function getSlotDuration()
    {
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $lessonDuration = FatApp::getPostedData('lessonDuration', FatUtility::VAR_INT, 0);
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        if (1 > $teacherId || 1 > $languageId || $teacherId == $loggedUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher = $this->checkTeacherIsValid($teacherId);
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userTeachLanguage = new UserTeachLanguage($teacherId);
        $getUserTeachlanguages = $userTeachLanguage->getUserTeachlanguages($this->siteLangId, true, 'INNER JOIN');
        $getUserTeachlanguages->doNotCalculateRecords();
        $getUserTeachlanguages->addCondition('tlanguage_id', '=', $languageId);
        $getUserTeachlanguages->addCondition('ustelgpr_price', '>', 0);
        $getUserTeachlanguages->addMultipleFields(['ustelgpr_slot', 'IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name', 'tlanguage_id']);
        $getUserTeachlanguages->addGroupBy('ustelgpr_slot');
        $slotDurations = FatApp::getDb()->fetchAll($getUserTeachlanguages->getResultSet(), 'ustelgpr_slot');
        if (empty($slotDurations)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_THIS_TEACHER_DOES_NOT_HAVE_ANY_SLOT'));
        }
        if (empty($lessonDuration) || !array_key_exists($lessonDuration, $slotDurations)) {
            $lessonDuration = key($slotDurations);
        }
        $teachLangName = $slotDurations[$lessonDuration]['tlanguage_name'];
        $this->set('slotDurations', $slotDurations);
        $this->set('teachLangName', $teachLangName);
        $this->set('teacher', $teacher);
        $this->set('languageId', $languageId);
        $this->set('lessonDuration', $lessonDuration);
        $this->_template->render(false, false);
    }

    public function getTeacherPriceSlabs()
    {
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $lessonDuration = FatApp::getPostedData('lessonDuration', FatUtility::VAR_INT, 0);
        $lessonQty = FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 0);
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        if (1 > $lessonDuration || 1 > $teacherId || 1 > $languageId || $teacherId == $loggedUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher = $this->checkTeacherIsValid($teacherId);
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userTeachLanguage = new UserTeachLanguage($teacherId);
        $getUserTeachlanguages = $userTeachLanguage->getUserTeachlanguages($this->siteLangId, true, 'INNER JOIN');
        $getUserTeachlanguages->doNotCalculateRecords();
        $getUserTeachlanguages->joinTable(TeacherOfferPrice::DB_TBL, 'LEFT JOIN', 'top.top_teacher_id = utl.utl_user_id and top.top_learner_id = ' . $loggedUserId . ' and top.top_lesson_duration = ustelgpr.ustelgpr_slot', 'top');
        $getUserTeachlanguages->addCondition('tlanguage_id', '=', $languageId);
        $getUserTeachlanguages->addCondition('ustelgpr_slot', '=', $lessonDuration);
        $getUserTeachlanguages->addCondition('ustelgpr_price', '>', 0);
        $getUserTeachlanguages->addMultipleFields([
            'IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name',
            'CONCAT(ustelgpr_min_slab,"-", ustelgpr_max_slab) as minMaxKey',
            'IFNULL(top_percentage,0) as top_percentage',
            'ustelgpr_min_slab',
            'ustelgpr_max_slab',
            'ustelgpr_price',
            'ustelgpr_slot',
            'tlanguage_id',
        ]);
        $getUserTeachlanguages->addGroupBy('minMaxKey');
        $slabs = FatApp::getDb()->fetchAll($getUserTeachlanguages->getResultSet(), 'minMaxKey');
        if (empty($slabs)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_THIS_TEACHER_DOES_NOT_HAVE_ANY_SLABS'));
        }
        $minValue = min(array_column($slabs, 'ustelgpr_min_slab', 'ustelgpr_min_slab'));
        $maxValue = max(array_column($slabs, 'ustelgpr_max_slab', 'ustelgpr_max_slab'));
        if (empty($lessonQty)) {
            $lessonQty = $minValue;
        }
        $firstkey = key($slabs);
        $teachLangName = $slabs[$firstkey]['tlanguage_name'];
        $this->set('slabs', $slabs);
        $this->set('minValue', $minValue);
        $this->set('maxValue', $maxValue);
        $this->set('lessonQty', $lessonQty);
        $this->set('teachLangName', $teachLangName);
        $this->set('teacher', $teacher);
        $this->set('lessonDuration', $lessonDuration);
        $this->set('postedData', FatApp::getPostedData());
        $this->_template->render(false, false);
    }

    public function addFirstName(){
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $lessonDuration = FatApp::getPostedData('lessonDuration', FatUtility::VAR_INT, 0);
        $lessonQty = FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 0);
        $fName = FatApp::getPostedData('fName', FatUtility::VAR_STRING, '');
        $data = [
            'msg' => '',
            'fName' => $fName,
            // 'priceLabel' => sprintf(Label::getLabel('LBL_TOTAL_PRICE_:_%s'), CommonHelper::displayMoneyFormat($price)),
        ];
        FatUtility::dieJsonSuccess($data);
    }
    public function addLastName(){
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $lessonDuration = FatApp::getPostedData('lessonDuration', FatUtility::VAR_INT, 0);
        $lessonQty = FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 0);
        $lName = FatApp::getPostedData('lName', FatUtility::VAR_STRING, '');
        $data = [
            'msg' => '',
            'lName' => $lName,
            // 'priceLabel' => sprintf(Label::getLabel('LBL_TOTAL_PRICE_:_%s'), CommonHelper::displayMoneyFormat($price)),
        ];
        FatUtility::dieJsonSuccess($data);
    }
    public function addKidsCount(){
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $lessonDuration = FatApp::getPostedData('lessonDuration', FatUtility::VAR_INT, 0);
        $lessonQty = FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 0);
        $count = FatApp::getPostedData('kidsCount', FatUtility::VAR_INT, 1);
        $data = [
            'msg' => '',
            'count' => $count,
            // 'priceLabel' => sprintf(Label::getLabel('LBL_TOTAL_PRICE_:_%s'), CommonHelper::displayMoneyFormat($price)),
        ];
        FatUtility::dieJsonSuccess($data);
    }
    public function getLessonQtyPrice()
    {
        $lessonQty = FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 0);
        $plan_id = FatApp::getPostedData('planId', FatUtility::VAR_INT, 0);
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        if (1 > $lessonQty || 1 > $lessonDuration || 1 > $teacherId || 1 > $languageId || $teacherId == $loggedUserId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher = $this->checkTeacherIsValid($teacherId);
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
       $planData = new SearchBase('tbl_sponsorshipcategories');
        $planData->addCondition('sponsorshipcategories_deleted', '=', 0);
        $planData->addCondition('sponsorshipcategories_id', '=', $plan_id);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
       
        $price = FatUtility::float($planResult['sponsorshipcategories_plan_price']);
         $price = ($price) * $lessonQty;
        $data = [
            'msg' => '',
            'price' => $price,
            'priceLabel' => sprintf(Label::getLabel('LBL_TOTAL_PRICE_:_%s'), CommonHelper::displayMoneyFormat($price)),
        ];
        FatUtility::dieJsonSuccess($data);
    }

    public function getPaymentSummary()
    {
        $criteria = ['isUserLogged' => true, 'hasItems' => true];
        $error = '';
        if (!$this->isEligibleForNextStep($criteria, $error)) {
            FatUtility::dieJsonError($error);
        }
        $teacherId = EventUserAuthentication::getLoggedUserId();
        $oneOnOne=$_SESSION['oneOneOne'];
      
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);

        $_SESSION['fromKids']=$fromKids;
        $fName=FatApp::getPostedData('fName', FatUtility::VAR_STRING, '');
        $_SESSION['fName']=$fName;

        $lName=FatApp::getPostedData('lName', FatUtility::VAR_STRING, '');
        $_SESSION['lName']=$lName;
    
        $kidsCount=FatApp::getPostedData('kidsCount', FatUtility::VAR_INT, 1);
        $_SESSION['kidsCount']=$kidsCount;
       
        // $cartData = $this->cartObj->getCart($this->siteLangId,$fromKids,$kidsCount);
        $cartData=$_SESSION['cart'];
        if (empty($cartData)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_Something_went_wrong,_please_try_after_some_time.'));
        }
        $userWalletBalance = EventUser::getUserBalance($teacherId);
        $paymentMethods = [];
        if ($cartData['orderPaymentGatewayCharges'] > 0) {
            /* Payment Methods[ */
            $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
            $pmSrch->doNotCalculateRecords();
            $pmSrch->doNotLimitRecords();
            $pmSrch->addMultipleFields([
                'pmethod_id',
                'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name',
                'pmethod_code',
                'pmethod_description'
            ]);
            $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
            $pmRs = $pmSrch->getResultSet();
            $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
            /* ] */
        }
        
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        $couponsList = DiscountCoupons::getValidCoupons($teacherId, $this->siteLangId, '', $orderId);
        $this->set('oneOnOne', $oneOnOne);
        $this->set('userWalletBalance', $userWalletBalance);
        $this->set('couponsList', $couponsList);
        $this->set('paymentMethods', $paymentMethods);
        $this->set('fName', $fName);
        $this->set('lName', $lName);
        $this->set('kidsCount', $kidsCount);
        $this->set('cartData', $cartData);
        $this->_template->render(false, false);
    }

    public function paymentSummary()
    {
        $criteria = ['isUserLogged' => true, 'hasItems' => true];
        if (!$this->isEligibleForNextStep($criteria)) {
            if (Message::getErrorCount()) {
                $errMsg = Message::getHtml();
            } else {
                Message::addErrorMessage(Label::getLabel('MSG_Something_went_wrong,_please_try_after_some_time.'));
                $errMsg = Message::getHtml();
            }
            if (true == FatUtility::isAjaxCall()) {
                $json['errorMsg'] = $errMsg;
                $json['redirectUrl'] = CommonHelper::generateUrl('Checkout');
                FatUtility::dieJsonError($json);
            }
            FatUtility::dieWithError($errMsg);
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $userWalletBalance = EventUser::getUserBalance($userId);
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        $cartData = $this->cartObj->getCart($this->siteLangId,$fromKids);
        $cartData=$_SESSION['cart'];
        $WalletPaymentForm = $this->getWalletPaymentForm();
        if ((FatUtility::convertToType($userWalletBalance, FatUtility::VAR_FLOAT) >= FatUtility::convertToType($cartData['orderNetAmount'], FatUtility::VAR_FLOAT)) && $cartData['cartWalletSelected']) {
            $WalletPaymentForm->setFormTagAttribute('onsubmit', 'confirmOrder(this); return(false);');
            $WalletPaymentForm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Pay_Now'));
        }
        // Payment Methods[
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addMultipleFields([
            'pmethod_id',
            'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name',
            'pmethod_code',
            'pmethod_description',
        ]);
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
        // ]
        $confirmForm = $this->getConfirmFormWithNoAmount($this->siteLangId);
        if ($cartData['orderPaymentGatewayCharges'] <= 0) {
            $confirmForm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Confirm_Order'));
        }
        $this->set('confirmForm', $confirmForm);
        $this->set('paymentMethods', $paymentMethods);
        $this->set('userWalletBalance', $userWalletBalance);
        $this->set('cartData', $cartData);
        $this->set('WalletPaymentForm', $WalletPaymentForm);
        $this->_template->render(false, false);
    }

    public function walletSelection()
    {
        $payFromWallet = FatApp::getPostedData('payFromWallet', FatUtility::VAR_INT, 0);
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        // $this->cartObj->updateCartWalletOption($payFromWallet,$fromKids);
        FatUtility::dieJsonSuccess('');
    }

    public function paymentTab($pmethod_id, $order_id = '')
    {
        $pmethodId = FatUtility::int($pmethod_id);
        if (!$pmethodId) {
            FatUtility::dieWithError(Label::getLabel('MSG_Invalid_Request!', $this->siteLangId));
        }
        // if (!UserAuthentication::isUserLogged()) {
        //     FatUtility::dieWithError(Label::getLabel('MSG_Your_Session_seems_to_be_expired.', $this->siteLangId));
        // }
        $orderInfo = [];
        $netAmmount = 0;
        if (!empty($order_id)) {
            $srch = Order::getSearchObject();
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $srch->addCondition('order_id', '=', $order_id);
            $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PENDING);
            $rs = $srch->getResultSet();
            $orderInfo = FatApp::getDb()->fetch($rs);
            if (!$orderInfo) {
                FatUtility::dieWithError(Label::getLabel('MSG_INVALID_ORDER_PAID_CANCELLED', $this->siteLangId));
            }
            $netAmmount = $orderInfo['order_net_amount'];
        }
        // [
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addMultipleFields(['pmethod_id', 'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name', 'pmethod_code', 'pmethod_description']);
        $pmSrch->addCondition('pmethod_id', '=', $pmethodId);
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethod = FatApp::getDb()->fetch($pmRs);
        if (!$paymentMethod) {
            FatUtility::dieWithError(Label::getLabel('MSG_Selected_Payment_method_not_found!', $this->siteLangId));
        }
        $this->set('paymentMethod', $paymentMethod);
        // ]
        // [
        $frm = $this->getPaymentTabForm($this->siteLangId);
        if (!empty($order_id)) {
            $frm->fill(['order_id' => $order_id, 'order_type' => $orderInfo['order_type']]);
        }
        $frm->fill(['pmethod_id' => $pmethodId]);
        $this->set('frm', $frm);
        // ]
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        $cartData = [];
        if (empty($order_id)) {
            // $cartData = $this->cartObj->getCart($this->siteLangId,$fromKids);
            $cartData=$_SESSION['cart'];
            $netAmmount = $cartData['orderPaymentGatewayCharges'];
            $this->set('cartData', $cartData);
        }
        $this->set('netAmmount', $netAmmount);
        $this->_template->render(false, false, '', false, false);
    }

    public function confirmOrder()
    {
        $order_type = FatApp::getPostedData('order_type', FatUtility::VAR_INT, 0);
        $pmethodId = FatApp::getPostedData('pmethod_id', FatUtility::VAR_INT, 0);
        $order_id = FatApp::getPostedData('order_id', FatUtility::VAR_STRING, '');
        $referralName=FatApp::getPostedData('referralName', FatUtility::VAR_STRING, '');
        if ($pmethodId > 0) {
            $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
            $pmSrch->doNotCalculateRecords();
            $pmSrch->doNotLimitRecords();
            $pmSrch->addMultipleFields(['pmethod_id', 'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name', 'pmethod_code', 'pmethod_description']);
            $pmSrch->addCondition('pmethod_id', '=', $pmethodId);
            $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
            $pmRs = $pmSrch->getResultSet();
            $paymentMethod = FatApp::getDb()->fetch($pmRs);
            if (!$paymentMethod) {
                Message::addErrorMessage(Label::getLabel('MSG_Selected_Payment_method_not_found!'));
                FatUtility::dieWithError(Message::getHtml());
            }
        }
        // ]
        // Loading Money to wallet[
        if (Order::TYPE_WALLET_RECHARGE == $order_type || Order::TYPE_GIFTCARD == $order_type) {
            $criteria = ['isUserLogged' => true];
            if (!$this->isEligibleForNextStep($criteria)) {
                if (Message::getErrorCount()) {
                    $errMsg = Message::getHtml();
                } else {
                    Message::addErrorMessage(Label::getLabel('MSG_Something_went_wrong,_please_try_after_some_time.'));
                    $errMsg = Message::getHtml();
                }
                FatUtility::dieWithError($errMsg);
            }
            $user_id = EventUserAuthentication::getLoggedUserId();
            if ('' == $order_id) {
                Message::addErrorMessage(Label::getLabel('MSG_INVALID_Request'));
                FatUtility::dieWithError(Message::getHtml());
            }
            $orderObj = new Order();
            $srch = Order::getSearchObject();
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $srch->addCondition('order_id', '=', $order_id);
            $srch->addCondition('order_user_id', '=', $user_id);
            $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PENDING);
            $srch->addCondition('order_type', '=', $order_type);
            $rs = $srch->getResultSet();
            $orderInfo = FatApp::getDb()->fetch($rs);
            if (!$orderInfo) {
                Message::addErrorMessage(Label::getLabel('MSG_INVALID_ORDER_PAID_CANCELLED'));
                FatUtility::dieWithError(Message::getHtml());
            }
            $orderObj->updateOrderInfo($order_id, ['order_pmethod_id' => $pmethodId]);
            $controller = $paymentMethod['pmethod_code'] . 'Pay';
            $redirectUrl = CommonHelper::generateUrl($controller, 'charge', [$order_id]);
            $this->set('msg', Label::getLabel('LBL_Processing...'));
            $this->set('redirectUrl', $redirectUrl);
            $this->_template->render(false, false, 'json-success.php');
        }
        $fromKids=FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        // $cartData = $this->cartObj->getCart($this->siteLangId,$fromKids);
        $cartData=$_SESSION['cartData'];
        $criteria = ['isUserLogged' => true, 'hasItems' => true];
        if (!$this->isEligibleForNextStep($criteria)) {
            $errMsg = Label::getLabel('MSG_Something_went_wrong,_please_try_after_some_time.');
            if (Message::getErrorCount()) {
                $errMsg = Message::getHtml();
            }
            $json['msg'] = $errMsg;
            $json['redirectUrl'] = CommonHelper::generateUrl('Checkout');
            FatUtility::dieJsonError($json);
        }
        if (0 == $cartData['orderPaymentGatewayCharges'] && $pmethodId) {
            Message::addErrorMessage(Label::getLabel('MSG_Amount_for_payment_gateway_must_be_greater_than_zero.'));
            FatUtility::dieWithError(Message::getHtml());
        }
        // addOrder[
        $order_id = isset($_SESSION['shopping_cart']['order_id']) ? $_SESSION['shopping_cart']['order_id'] : false;
        $orderNetAmount = $cartData['orderNetAmount'];
        $walletAmountCharge = $cartData['walletAmountCharge'];
        $orderNetAmount = $cartData["orderNetAmount"];
        $walletAmountCharge = $cartData["walletAmountCharge"];
        $coupon_discount_total = FatUtility::float($cartData['cartDiscounts']['coupon_discount_total'] ?? 0);
        $orderData = [
            'order_id' => $order_id,
            'order_type' => Order::TYPE_LESSON_BOOKING,
            'order_user_id' => EventUserAuthentication::getLoggedUserId(),
            'order_is_paid' => Order::ORDER_IS_PENDING,
            'order_net_amount' => $orderNetAmount,
            'order_is_wallet_selected' => $cartData['cartWalletSelected'],
            'order_wallet_amount_charge' => $walletAmountCharge,
            'order_currency_id' => CommonHelper::getCurrencyId(),
            'order_currency_code' => CommonHelper::getCurrencyCode(),
            'order_currency_value' => CommonHelper::getCurrencyValue(),
            'order_pmethod_id' => $pmethodId,
            'order_discount_coupon_code' => $cartData['cartDiscounts']['coupon_code'] ?? '',
            'order_discount_total' => $coupon_discount_total,
            'order_discount_info' => $cartData['cartDiscounts']['coupon_info'] ?? '',
            'order_referral'=>$referralName,
        ];
        $languageRow = Language::getAttributesById($this->siteLangId);
        $orderData['order_language_id'] = $languageRow['language_id'];
        $orderData['order_language_code'] = $languageRow['language_code'];
        // [
        $op_lesson_duration = $cartData['lessonDuration']; //FatApp::getConfig('conf_paid_lesson_duration', FatUtility::VAR_INT, 60);
        $cartData['op_commission_charged'] = 0;
        $cartData['op_commission_percentage'] = 0;
        if ($cartData['isFreeTrial']) {
            $op_lesson_duration = FatApp::getConfig('conf_trial_lesson_duration', FatUtility::VAR_INT, 30);
        } else {
            $commissionDetails = Commission::getTeacherCommission($cartData['teacherId'], $cartData['grpclsId']);
            if ($commissionDetails) {
                $cartData['op_commission_percentage'] = $commissionDetails['commsetting_fees'];
                $teacherCommission = ((100 - $commissionDetails['commsetting_fees']) * $cartData['itemPrice']) / 100;
            } else {
                $teacherCommission = $cartData['itemPrice'];
            }
            $teacherCommission = $teacherCommission;
            $cartData['op_commission_charged'] = $teacherCommission;
        }
      
        $products = [
            'op_grpcls_id' => $cartData['grpclsId'],
            'op_lpackage_is_free_trial' => $cartData['isFreeTrial'],
            'op_lesson_duration' => $op_lesson_duration,
            'op_teacher_id' => $cartData['user_id'],
            'op_qty' => 0 == $cartData['grpclsId'] ? $cartData['lessonQty'] : 1,
            'op_commission_charged' => $cartData['op_commission_charged'],
            'op_commission_percentage' => $cartData['op_commission_percentage'],
            'op_unit_price' => $cartData['itemPrice'],
            'op_tlanguage_id' => $cartData['languageId'],
        ];
        if(isset($_SESSION) && $_SESSION['fromKids']>0){
            $products['op_qty']=$cartData['lessonQty'];
        }
        $productsLangData = [];
        $products['productsLangData'] = $productsLangData;
        $orderData['products'][] = $products;
        $order = new Order();
        if (!$order->addUpdate($orderData)) {
            Message::addErrorMessage($order->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        // ]
        $redirectUrl = '';
        $_SESSION['fromKids']=$fromKids;
        $teacherId=FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $_SESSION['teacherId']=$teacherId;
        $msg = Label::getLabel('LBL_Processing...', $this->siteLangId);
        if (0 >= $orderNetAmount) {
            $redirectUrl = CommonHelper::generateUrl('FreePay', 'Charge', [$order->getOrderId()], CONF_WEBROOT_FRONT_URL);
            FatUtility::dieJsonSuccess(['redirectUrl' => $redirectUrl, 'msg' => $msg]);
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $teacherId=FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $userData=EventUser::getAttributesById($userId);
        $userWalletBalance = EventUser::getUserBalance($userId);
      
        if ($orderNetAmount > 0 && $cartData['cartWalletSelected'] && ($userWalletBalance >= $orderNetAmount) && !$pmethodId) {
            $redirectUrl = CommonHelper::generateUrl('WalletPay', 'Charge', [$order->getOrderId()], CONF_WEBROOT_FRONTEND);
            FatUtility::dieJsonSuccess(['redirectUrl' => $redirectUrl, 'msg' => $msg]);
        }
        if ($pmethodId > 0) {
            $controller = $paymentMethod['pmethod_code'] . 'Pay';
            $redirectUrl = CommonHelper::generateUrl($controller, 'charge', [$order->getOrderId()], CONF_WEBROOT_FRONTEND);

            // $this->cartObj->clear();
            // if($fromKids==0){
            // $this->cartObj->updateEventUserCart();
            // }
            // else{
            //     $this->cartObj->updateUserKidsCart();
            // }
            FatUtility::dieJsonSuccess(['redirectUrl' => $redirectUrl, 'msg' => $msg]);
        }
        Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
        FatUtility::dieWithError(Message::getHtml());
    }

    private function getReferralForm($langId)
    {
        $langId = FatUtility::int($langId);
        $frm = new Form('frmReferral');
        $fld = $frm->addTextBox(Label::getLabel('LBL_Referral', $langId), 'referral', '', ['placeholder' => Label::getLabel('LBL_Enter_Referral_Name', $langId)]);
        return $frm;
    }

    private function getPromoCouponsForm($langId)
    {
        $langId = FatUtility::int($langId);
        $frm = new Form('frmPromoCoupons');
        $fld = $frm->addTextBox(Label::getLabel('LBL_Coupon_code', $langId), 'coupon_code', '', ['placeholder' => Label::getLabel('LBL_Enter_Your_code', $langId)]);
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Apply', $langId));
        return $frm;
    }

    private function getPaymentTabForm($langId = 0)
    {
        $frm = new Form('frmPaymentTabForm');
        $frm->setFormTagAttribute('id', 'frmPaymentTabForm');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Confirm_Payment', $langId));
        $frm->addHiddenField('', 'order_type');
        $frm->addHiddenField('', 'order_id');
        $frm->addHiddenField('', 'pmethod_id');
        return $frm;
    }

    private function getWalletPaymentForm()
    {
        return new Form('frmWalletPayment');
    }

    private function isEligibleForNextStep($criteria = [], &$error = '')
    {
    
        if (empty($criteria)) {
            return true;
        }
        foreach ($criteria as $key => $val) {
            switch ($key) {
                case 'isUserLogged':
                    if (!EventUserAuthentication::isUserLogged()) {
                        $key = false;
                        $error = Label::getLabel('MSG_Your_Session_seems_to_be_expired.');
                        Message::addErrorMessage(Label::getLabel('MSG_Your_Session_seems_to_be_expired.'));
                        return false;
                    }
                    break;
                case 'hasItems':
                    if (!isset($_SESSION['cart'])) {
                        $key = false;
                        $error = Label::getLabel('MSG_Teacher_booking_selection_is_not_yet_been_selected,_Please_try_selecting_the_appropriate_teacher_and_start_booking_lesson.');
                        Message::addErrorMessage(Label::getLabel('MSG_Teacher_booking_selection_is_not_yet_been_selected,_Please_try_selecting_the_appropriate_teacher_and_start_booking_lesson.'));
                        return false;
                    }
                    break;
            }
        }
        return true;
    }

    private function getConfirmFormWithNoAmount()
    {
        $frm = new Form('frmConfirmForm');
        $frm->addFormTagAttribute('onSubmit', 'return confirmOrder(this);');
        return $frm;
    }

    private function getTeacherTeachLanguages(int $teacherId): SearchBase
    {
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        $userTeachLanguage = new UserTeachLanguage($teacherId);
        $getUserTeachLanguages = $userTeachLanguage->getUserTeachlanguages($this->siteLangId, true);
        $getUserTeachLanguages->joinTable(TeacherOfferPrice::DB_TBL, 'LEFT JOIN', 'top.top_teacher_id = utl.utl_user_id and top.top_learner_id = ' . $loggedUserId . ' and top.top_lesson_duration = ustelgpr.ustelgpr_slot', 'top');
        $getUserTeachLanguages->doNotCalculateRecords();
        $getUserTeachLanguages->addMultipleFields([
            'IFNULL(tlanguage_name, tlanguage_identifier) as teachLangName',
            'utl_id',
            'utl_tlanguage_id',
            'ustelgpr_slot',
            'ustelgpr_price',
            'ustelgpr_min_slab',
            'ustelgpr_max_slab',
            'ustelgpr_price',
            'IFNULL(top_percentage,0) as top_percentage',
        ]);
        return $getUserTeachLanguages;
    }

    private function pricSlabForm(): Form
    {
        $form = new Form('pricSlabsForm');
        $languageId = $form->addIntegerField(Label::getLabel('Lbl_Teach_language'), 'languageId');
        $languageId->requirements()->setRequired();
        $languageId->requirements()->setRange(1, 99999999);
        $durations = CommonHelper::getPaidLessonDurations();
        $lessonDuration = $form->addSelectBox(Label::getLabel('Lbl_Duration'), 'lessonDuration', array_flip($durations));
        $lessonDuration->requirements()->setRequired(true);
        return $form;
    }

    private function checkTeacherIsValid(int $teacherId): array
    {
        $teacherSearch = new TeacherSearch($this->siteLangId);
        $teacherSearch->applyPrimaryConditions();
        $teacherSearch->joinSettingTabel();
        $teacherSearch->addCondition('user_id', '=', $teacherId);
        $teacherSearch->setPageSize(1);
        $teacherSearch->addMultipleFields(['user_id', 'user_first_name', 'user_last_name', 'us_is_trial_lesson_enabled', 'us_booking_before']);
        $teacher = FatApp::getDb()->fetch($teacherSearch->getResultSet());
        if (!empty($teacher)) {
            return $teacher;
        }
        return [];
    }

}
