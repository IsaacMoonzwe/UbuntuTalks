<?php

class OrderSearch extends SearchBase
{

    private $isUserJoined;

    public function __construct($doNotCalculateRecords = true, $doNotLimitRecords = true)
    {
        parent::__construct(Order::DB_TBL, 'o');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
        if (true === $doNotLimitRecords) {
            $this->doNotLimitRecords();
        }
        $this->isUserJoined = false;
    }

    public function joinOrderProduct($langId = 0)
    {
        $langId = FatUtility::int($langId);
        $this->joinTable(OrderProduct::DB_TBL, 'INNER JOIN', 'o.order_id = op.op_order_id', 'op');
        if ($langId > 1) {
            $this->joinTable(OrderProduct::DB_TBL . '_lang', 'LEFT JOIN', 'op_id = oplang_op_id AND oplang_lang_id = ' . $langId);
        }
    }

    public function joinUser()
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'o.order_user_id = u.user_id', 'u');
        $this->isUserJoined = true;
    }
     public function joinEventUser()
    {
        $this->joinTable(EventUser::DB_TBL, 'INNER JOIN', 'o.order_user_id = u.user_id', 'u');
        $this->isUserJoined = true;
    }

    public function joinUserCredentials()
    {
        if (false === $this->isUserJoined) {
            trigger_error("Please first use joinUser function", E_USER_ERROR);
        }
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'u.user_id = cred.credential_user_id', 'cred');
    }

 public function joinEventUserCredentials()
    {
        if (false === $this->isUserJoined) {
            trigger_error("Please first use joinUser function", E_USER_ERROR);
        }
        $this->joinTable(EventUser::DB_TBL_CRED, 'INNER JOIN', 'u.user_id = cred.credential_user_id', 'cred');
    }

    public function joinTeacher($langId = 0)
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'op.op_teacher_id = t.user_id', 't');
        $this->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 't.user_id = us.us_user_id', 'us');
        $this->joinTable(SpokenLanguage::DB_TBL, 'INNER JOIN', 'us.us_teach_slanguage_id = s.slanguage_id', 's');
        if ($langId > 0) {
            $this->joinTable(SpokenLanguage::DB_TBL_LANG, 'LEFT OUTER JOIN', 'sl.slanguagelang_slanguage_id = s.slanguage_id AND slanguagelang_lang_id = ' . $langId, 'sl');
        }
        $this->isUserJoined = true;
    }

    public function joinTeacherLessonLanguage($langId = 0)
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'op.op_teacher_id = t.user_id', 't');
        $this->joinTable(ScheduledLessonDetails::DB_TBL, 'INNER JOIN', 'o.order_id = sld.sldetail_order_id', 'sld');
        $this->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'sld.sldetail_slesson_id = slns.slesson_id', 'slns');
        $this->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 'slns.slesson_slanguage_id = tlang.tlanguage_id', 'tlang');
        if ($langId > 0) {
            $this->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT OUTER JOIN', 'sl.tlanguagelang_tlanguage_id = tlang.tlanguage_id AND tlanguagelang_lang_id = ' . $langId, 'sl');
        }
        $this->isUserJoined = true;
    }

    public function joinOrderPaymentMethod($langId = 0)
    {
        $this->joinTable(PaymentMethods::DB_TBL, 'INNER JOIN', 'o.order_pmethod_id = pm.pmethod_id', 'pm');
        if ($langId) {
            $this->joinTable(PaymentMethods::DB_LANG_TBL, 'LEFT OUTER JOIN', 'pm.pmethod_id = pm_l.pmethodlang_pmethod_id AND pm_l.pmethodlang_lang_id = ' . $langId, 'pm_l');
        }
    }

    public function joinGiftCardBuyer()
    {
        $this->joinTable(Giftcard::DB_TBL_GIFTCARD_BUYER, 'LEFT OUTER JOIN', 'o.order_id = gcbuyer.gcbuyer_order_id', 'gcbuyer');
    }

    public function joinGiftcardRecipient()
    {
        $this->joinTable(Giftcard::DB_TBL_GIFTCARD_RECIPIENT, 'LEFT OUTER JOIN', 'op.op_id = gcrecipient.gcrecipient_op_id', 'gcrecipient');
    }

    public function joinOrderBuyerUser()
    {
        $this->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'o.order_user_id = buyer.user_id', 'buyer');
        $this->joinTable(User::DB_TBL_CRED, 'LEFT OUTER JOIN', 'buyer.user_id = buyer_cred.credential_user_id', 'buyer_cred');
        $this->isOrderBuyerUserJoined = true;
    }
     public function joinOrderBuyerEventUser()
    {
        $this->joinTable(EventUser::DB_TBL, 'LEFT OUTER JOIN', 'o.order_user_id = buyer.user_id', 'buyer');
        $this->joinTable(EventUser::DB_TBL_CRED, 'LEFT OUTER JOIN', 'buyer.user_id = buyer_cred.credential_user_id', 'buyer_cred');
        $this->isOrderBuyerUserJoined = true;
    }

    public function joinGiftcards()
    {
        $this->joinTable(Giftcard::DB_TBL, 'INNER JOIN', 'gift.giftcard_op_id = op.op_id', 'gift');
    }

    public function joinScheduledLessonDetail()
    {
        $this->joinTable(ScheduledLessonDetails::DB_TBL, 'LEFT OUTER JOIN', 'sld.sldetail_order_id = o.order_id', 'sld');
    }

    public function joinScheduledLesson(bool $addIsPaidCheck = true)
    {
        $onCondition = 'sl.slesson_id = sld.sldetail_slesson_id';
        if ($addIsPaidCheck) {
            $onCondition .= ' AND sld.sldetail_is_teacher_paid = ' . applicationConstants::YES;
        }
        $this->joinTable(ScheduledLesson::DB_TBL, 'LEFT OUTER JOIN', $onCondition, 'sl');
    }

    public function joinGroupClass(int $langId)
    {
        $this->joinTable(TeacherGroupClasses::DB_TBL, 'LEFT OUTER JOIN', 'grpcls.grpcls_id = slns.slesson_grpcls_id', 'grpcls');
        $this->joinTable(TeacherGroupClasses::DB_TBL_LANG, 'LEFT OUTER JOIN', 'grpcls.grpcls_id = grpcls_l.grpclslang_grpcls_id and grpcls_l.grpclslang_lang_id=' . $langId, 'grpcls_l');
        $this->joinTable(TeacherKidsClasses::DB_TBL, 'LEFT OUTER JOIN', 'grpcls.grpcls_id = slns.slesson_grpcls_id', 'grpcls');
        $this->joinTable(TeacherKidsClasses::DB_TBL_LANG, 'LEFT OUTER JOIN', 'grpcls.grpcls_id = grpcls_l.grpclslang_grpcls_id and grpcls_l.grpclslang_lang_id=' . $langId, 'grpcls_l');
    
    }

}
