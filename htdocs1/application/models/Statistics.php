<?php

class Statistics extends MyAppModel
{

    const TYPE_TODAY = 1;
    const TYPE_LAST_WEEK = 2;
    const TYPE_THIS_MONTH = 3;
    const TYPE_LAST_MONTH = 4;
    const TYPE_LAST_YEAR = 5;
    const TYPE_ALL = 6;
    const TYPE_THIS_YEAR = 7;
    const REPORT_EARNING = 1;
    const REPORT_SOLD_LESSONS = 2;

    public function __construct(int $userId = 0)
    {
        $this->db = FatApp::getDb();
        $this->userId = $userId;
    }

    public static function getDurationTypesArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::TYPE_TODAY => Label::getLabel('LBL_Today', $langId),
            static::TYPE_LAST_WEEK => Label::getLabel('LBL_Last_Week', $langId),
            static::TYPE_THIS_MONTH => Label::getLabel('LBL_This_Month', $langId),
            static::TYPE_LAST_MONTH => Label::getLabel('LBL_Last_Month', $langId),
            static::TYPE_LAST_YEAR => Label::getLabel('LBL_Last_Year', $langId),
            static::TYPE_THIS_YEAR => Label::getLabel('LBL_This_Year', $langId),
        ];
    }

    /**
     * @todo to be updated
     */
    public function getEarning($type, $forGraph = false)
    {
        $type = strtolower($type);
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $nowDate = MyDate::changeDateTimezone(date('Y-m-d H:i:s'), $systemTimeZone, $user_timezone);

        $nowDateTimestamp = strtotime($nowDate);
        $endDate = date('Y-m-d H:i:s');
        $srch = new OrderSearch();
        $srch->joinOrderProduct();
        $srch->joinScheduledLessonDetail();
        $srch->joinScheduledLesson();
        $srch->addCondition('op_teacher_id', '=', $this->userId);
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $srch->addCondition('sld.sldetail_is_teacher_paid', '=', applicationConstants::YES);
        $srch->addMultipleFields(['IFNULL(sum(op_commission_charged),0) as earning', 'order_date_added']);
        switch ($type) {
            case static::TYPE_TODAY:
                $startDate = MyDate::changeDateTimezone(date('Y-m-d', $nowDateTimestamp) . ' 00:00:00', $user_timezone, $systemTimeZone);

                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added,'%h:%i %p') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%H:%i')");
                }
                break;
            case static::TYPE_LAST_WEEK:
                $startDate = date('Y-m-d H:i:s', strtotime('monday last week', $nowDateTimestamp));
                $startDate = MyDate::changeDateTimezone($startDate, $user_timezone, $systemTimeZone);
                $endDate = date('Y-m-d H:i:s', strtotime('sunday last week', $nowDateTimestamp));
                $endDate = MyDate::changeDateTimezone($endDate, $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%Y-%m-%d') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%Y-%m-%d')");
                }
                break;
            case static::TYPE_THIS_MONTH:
                $startDate = MyDate::changeDateTimezone(date('Y-m', $nowDateTimestamp) . '-01 00:00:00', $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%Y-%m-%d') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%Y-%m-%d')");
                }
                break;
            case static::TYPE_LAST_MONTH:
                $startDate = date('Y-m-d', strtotime('first day of previous month', $nowDateTimestamp)) . ' 00:00:00';
                $endDate = date('Y-m-d', strtotime('last day of previous month +1 days', $nowDateTimestamp)) . ' 00:00:00';

                $startDate = MyDate::changeDateTimezone($startDate, $user_timezone, $systemTimeZone);
                $endDate = MyDate::changeDateTimezone($endDate, $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%Y-%m-%d') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%Y-%m-%d')");
                }
                break;
            case static::TYPE_THIS_YEAR:
                $startDate = date('Y-m-d', strtotime('first day of january this year', $nowDateTimestamp));
                $startDate = MyDate::changeDateTimezone($startDate, $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%m-%Y') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%m-%Y')");
                }
                break;
            case static::TYPE_LAST_YEAR:
                $startDate = date('Y-m-d', strtotime('last year January 1st', $nowDateTimestamp)) . ' 00:00:00';
                $endDate = date('Y-m-d', strtotime('last year December 31st +1 days', $nowDateTimestamp)) . ' 00:00:00';
                $startDate = MyDate::changeDateTimezone($startDate, $user_timezone, $systemTimeZone);
                $endDate = MyDate::changeDateTimezone($endDate, $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%m-%Y') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%m-%Y')");
                }
                break;
            case static::TYPE_ALL:
            default:
                $endDate = $nowDate;
                $startDate = '';
                $srch->addMultipleFields(['MIN(date(order_date_added)) as fromDate, MAX(date(order_date_added)) as toDate']);
                $srch->addCondition('order_date_added', '<=', $endDate, 'AND', true);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%Y-%m-%d') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%Y-%m-%d')");
                }
                break;
        }
        if ($type != static::TYPE_ALL) {
            $srch->addCondition('order_date_added', '>=', $startDate, 'AND', true);
            $srch->addCondition('order_date_added', '<=', $endDate, 'AND', true);
        }

        $data['fromDate'] = $startDate;
        $data['toDate'] = $endDate;
        $data['earningData'] = [];
        if ($forGraph) {
            $earningData = $this->db->fetchAll($srch->getResultSet(), 'groupDate');
            $data['earningData'] = $earningData;
            $data['earning'] = array_sum(array_column($earningData, 'earning'));
        } else {
            $earningData = $this->db->fetch($srch->getResultSet());
            $data['earningData'] = $earningData;
            $data['earning'] = $earningData['earning'];
        }

        return $data;
    }

    public function getSoldlessons($type, $forGraph = false)
    {
        $type = strtolower($type);
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $nowDate = MyDate::changeDateTimezone(date('Y-m-d H:i:s'), $systemTimeZone, $user_timezone);
        $nowDateTimestamp = strtotime($nowDate);
        $endDate = date('Y-m-d H:i:s');
        $srch = new ScheduledLessonSearch();
        $srch->joinOrder();
        $srch->addCondition('slesson_teacher_id', '=', $this->userId);
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $srch->addMultipleFields(['count(slesson_id) as lessonCount, MIN(order_date_added) as fromDate, MAX(order_date_added) as toDate', 'order_date_added']);
        switch ($type) {
            case static::TYPE_TODAY:
                $startDate = MyDate::changeDateTimezone(date('Y-m-d', $nowDateTimestamp) . ' 00:00:00', $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%h:%i %p') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%H:%i')");
                }
                break;
            case static::TYPE_LAST_WEEK:
                $startDate = date('Y-m-d H:i:s', strtotime('monday last week', $nowDateTimestamp));
                $startDate = MyDate::changeDateTimezone($startDate, $user_timezone, $systemTimeZone);
                $endDate = date('Y-m-d H:i:s', strtotime('sunday last week', $nowDateTimestamp));
                $endDate = MyDate::changeDateTimezone($endDate, $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%Y-%m-%d') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%Y-%m-%d')");
                }
                break;
            case static::TYPE_THIS_MONTH:
                $startDate = MyDate::changeDateTimezone(date('Y-m', $nowDateTimestamp) . '-01 00:00:00', $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%Y-%m-%d') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%Y-%m-%d')");
                }
                break;
            case static::TYPE_LAST_MONTH:
                $startDate = date('Y-m-d', strtotime('first day of previous month', $nowDateTimestamp)) . ' 00:00:00';
                $endDate = date('Y-m-d', strtotime('last day of previous month +1 days', $nowDateTimestamp)) . ' 00:00:00';
                $startDate = MyDate::changeDateTimezone($startDate, $user_timezone, $systemTimeZone);
                $endDate = MyDate::changeDateTimezone($endDate, $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%Y-%m-%d') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%Y-%m-%d')");
                }
                break;
            case static::TYPE_THIS_YEAR:
                $startDate = date('Y-m-d', strtotime('first day of january this year', $nowDateTimestamp));
                $startDate = MyDate::changeDateTimezone($startDate, $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%m-%Y') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%m-%Y')");
                }
                break;
            case static::TYPE_LAST_YEAR:
                $startDate = date('Y-m-d', strtotime('last year January 1st', $nowDateTimestamp));
                $endDate = date('Y-m-d', strtotime('first day of january this year', $nowDateTimestamp));
                $startDate = MyDate::changeDateTimezone($startDate, $user_timezone, $systemTimeZone);
                $endDate = MyDate::changeDateTimezone($endDate, $user_timezone, $systemTimeZone);
                if ($forGraph) {
                    $srch->addMultipleFields(["DATE_FORMAT(order_date_added, '%m-%Y') as groupDate"]);
                    $srch->addGroupBy("DATE_FORMAT(order_date_added, '%m-%Y')");
                }
                break;
        }
        $srch->addCondition('order_date_added', '>=', $startDate, 'AND', true);
        $srch->addCondition('order_date_added', '<=', $endDate, 'AND', true);
        $data['fromDate'] = $startDate;
        $data['toDate'] = $nowDate;
        $data['lessonData'] = [];
        if ($forGraph) {
            $lessonData = $this->db->fetchAll($srch->getResultSet(), 'groupDate');
            $data['lessonData'] = $lessonData;
            $data['lessonCount'] = array_sum(array_column($lessonData, 'lessonCount'));
        } else {
            $lessonData = $this->db->fetch($srch->getResultSet());
            $data['lessonData'] = $lessonData;
            $data['lessonCount'] = $lessonData['lessonCount'];
        }
        return $data;
    }

}
