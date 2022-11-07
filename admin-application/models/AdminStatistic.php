<?php

class AdminStatistic extends MyAppModel
{

    public function __construct()
    {
        $this->db = FatApp::getDb();
    }

    public function getDashboardLast12MonthsSummary($langId = 0, $type, $userTypeArr = [], $months = 12)
    {
        $last12Months = self::getLast12MonthsDetails($months);
        $type = strtolower($type);
        switch ($type) {
            case 'sales':
                $srch = new OrderProductSearch();
                $srch->joinorders();
                $srch->addCondition('o.order_type', '=', Order::TYPE_LESSON_BOOKING);
                $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
                $srch->doNotCalculateRecords();
                $srch->doNotLimitRecords();
                foreach ($last12Months as $key => $val) {
                    $srchObj = clone $srch;
                    $srchObj->addDirectCondition("month(`order_date_added` ) = $val[monthCount] and year(`order_date_added` )= $val[year]");
                    $srchObj->addMultipleFields(['SUM((order_net_amount ) ) AS Sales,avg((order_net_amount )) AS avg_order,count(op_id) as total_orders']);
                    $rs = $srchObj->getResultSet();
                    $row = $this->db->fetch($rs);
                    $sales_data[] = ["duration" => Label::getLabel('LBL_' . $val['monthShort'], $langId) . "-" . $val['year'], "value" => round($row["Sales"], 2)];
                }
                return $sales_data;
                break;
            case 'earnings':
                $srch = new OrderProductSearch();
                $srch->joinorders();
                $srch->addCondition('o.order_type', '=', Order::TYPE_LESSON_BOOKING);
                $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
                $srch->addGroupBy('op.op_id');
                $srch->joinScheduleLessonDetails();
                $srch->joinScheduleLesson();
                $srch->doNotCalculateRecords();
                $srch->doNotLimitRecords();
                foreach ($last12Months as $key => $val) {
                    $srchObj = clone $srch;
                    $srchObj->addDirectCondition("month(`order_date_added` ) = $val[monthCount] and year(`order_date_added` )= $val[year]");
                    $srchObj->addMultipleFields(['order_net_amount - op_total_refund_amount - ((op_qty-op_refund_qty) * op_commission_charged) as  Earnings', 'op_id']);
                    $rs = $srchObj->getResultSet();
                    $row = $this->db->fetchAll($rs);
                    if ($row) {
                        $row['Earnings'] = array_sum(array_column($row, 'Earnings'));
                    } else {
                        $row['Earnings'] = 0;
                    }
                    $sales_data[] = ["duration" => Label::getLabel('LBL_' . $val['monthShort'], $langId) . "-" . $val['year'], "value" => round($row["Earnings"], 2)];
                }
                return $sales_data;
                break;
            case 'signups':
                $userObj = new User();
                $srch = $userObj->getUserSearchObj();
                $srch->doNotCalculateRecords();
                $srch->doNotLimitRecords();
                $srch->addMultipleFields(['count(user_id) AS Registrations']);
                foreach ($last12Months as $key => $val) {
                    $srchObj = clone $srch;
                    $srchObj->addDirectCondition("month(`user_added_on` ) = $val[monthCount] and year(`user_added_on` ) = $val[year]");
                    if ((isset($userTypeArr['user_is_learner']) && FatUtility::int($userTypeArr['user_is_learner']) > 0) || (isset($userTypeArr['user_is_teacher']) && FatUtility::int($userTypeArr['user_is_teacher']) > 0)) {
                        $cnd = $srchObj->addCondition('u.user_is_learner', '=', applicationConstants::YES);
                        $cnd->attachCondition('u.user_is_teacher', '=', applicationConstants::YES);
                    }
                    $rs = $srchObj->getResultSet();
                    $row = $this->db->fetch($rs);
                    $signups_data[] = ["duration" => Label::getLabel('LBL_' . $val['monthShort'], $langId) . "-" . $val['year'], "value" => round($row["Registrations"], 2)];
                }
                return $signups_data;
                break;
        }
    }

    public function getStats($type, $lessonStatus = 0)
    {
        
        $type = strtolower($type);
        switch ($type) {
            case 'total_members':
                $sql = "SELECT 1 AS num_days, count(user_id) FROM `tbl_users` WHERE user_deleted = " . applicationConstants::NO . " and  DATE(user_added_on) = DATE(NOW()) and YEAR(user_added_on) = YEAR(NOW())
                        UNION ALL
                        SELECT 7 AS num_days, count(user_id) FROM `tbl_users` WHERE  user_deleted = " . applicationConstants::NO . " and YEARWEEK(user_added_on) = YEARWEEK(NOW()) and YEAR(user_added_on) = YEAR(NOW())
                        UNION ALL
                        SELECT 30 AS num_days, count(user_id) FROM `tbl_users` WHERE user_deleted = " . applicationConstants::NO . " and MONTH(user_added_on) = MONTH(NOW()) and YEAR(user_added_on) = YEAR(NOW())
                        UNION ALL
                        SELECT 90 AS num_days, count(user_id) FROM `tbl_users` WHERE user_deleted = " . applicationConstants::NO . " and user_added_on > date_sub(date_add(date_add(LAST_DAY(now()),interval 1 DAY),interval -1 MONTH), INTERVAL 3 MONTH)
                        UNION ALL
                        SELECT -1 AS num_days, count(user_id) FROM `tbl_users` where user_deleted = " . applicationConstants::NO;
                /* buyer/seller data [ */
                $sql .= " UNION ALL
                        SELECT 'buyer_seller_1' AS num_days, count(user_id) FROM `tbl_users` WHERE user_deleted = " . applicationConstants::NO . " and DATE(user_added_on) = DATE(NOW()) AND (user_is_learner = 1 OR user_is_teacher = 1) and YEAR(user_added_on) = YEAR(NOW())
                        UNION ALL
                        SELECT 'buyer_seller_7' AS num_days, count(user_id) FROM `tbl_users` WHERE user_deleted = " . applicationConstants::NO . " and YEARWEEK(user_added_on) =  YEARWEEK(NOW()) AND (user_is_learner = 1 OR user_is_teacher = 1) and YEAR(user_added_on) = YEAR(NOW())
                        UNION ALL
                        SELECT 'buyer_seller_30' AS num_days, count(user_id) FROM `tbl_users` WHERE user_deleted = " . applicationConstants::NO . " and MONTH(user_added_on) = MONTH(NOW()) AND (user_is_learner = 1 OR user_is_teacher = 1) and YEAR(user_added_on) = YEAR(NOW())
                        UNION ALL
                        SELECT 'buyer_seller_90' AS num_days, count(user_id) FROM `tbl_users` WHERE user_deleted = " . applicationConstants::NO . " and user_added_on > date_sub(date_add(date_add(LAST_DAY(now()),interval 1 DAY),interval -1 MONTH), INTERVAL 3 MONTH) AND (user_is_learner = 1 OR user_is_teacher = 1)
                        UNION ALL
                        SELECT 'buyer_seller_all' AS num_days, count(user_id) FROM `tbl_users` WHERE user_deleted = " . applicationConstants::NO . " and (user_is_learner = 1 OR user_is_teacher = 1)";
                /* ] */
                $rs = $this->db->query($sql);
                return $this->db->fetchAllAssoc($rs);
                break;
            case 'total_lessons':
                $srch = new ScheduledLessonSearch(true);
                $srch->joinOrder();
                $srch->doNotLimitRecords();
                if ($lessonStatus > 0) {
                    $srch->addCondition('slesson_status', '=', $lessonStatus);
                }
                $srchObj1 = clone $srch;
                $srchObj1->addFld(['1 AS num_days', 'count(slesson_id)']);
                $srchObj1->addDirectCondition('DATE(slesson_added_on) = DATE(NOW())');
                $srchObj7 = clone $srch;
                $srchObj7->addFld(['7 AS num_days', 'count(slesson_id)']);
                $srchObj7->addDirectCondition('YEARWEEK(slesson_added_on) = YEARWEEK(NOW())');
                $srchObj30 = clone $srch;
                $srchObj30->addFld(['30 AS num_days', 'count(slesson_id)']);
                $srchObj30->addDirectCondition('MONTH(slesson_added_on)=MONTH(NOW())');
                $srchObj90 = clone $srch;
                $srchObj90->addFld(['90 AS num_days', 'count(slesson_id)']);
                $srchObj90->addDirectCondition('slesson_added_on>date_sub(date_add(date_add(LAST_DAY(now()),interval 1 DAY),interval -1 MONTH), INTERVAL 3 MONTH)');
                $srchObjAll = clone $srch;
                $srchObjAll->addFld(['-1 AS num_days', 'count(slesson_id)']);
                $sql = $srchObj1->getQuery() . " UNION ALL " . $srchObj7->getQuery() . " UNION ALL " . $srchObj30->getQuery() . " UNION ALL " . $srchObj90->getQuery() . " UNION ALL " . $srchObjAll->getQuery();
                $rs = $this->db->query($sql);
                return $this->db->fetchAllAssoc($rs);
                break;
            case 'total_sales':
                $srch = new OrderProductSearch();
                $srch->joinorders();
                $srch->doNotCalculateRecords();
                $srch->doNotLimitRecords();
                $srch->addCondition('o.order_type', '=', Order::TYPE_LESSON_BOOKING);
                $cnd = $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
                $srch->addMultipleFields(['SUM(order_net_amount ) - SUM(op_total_refund_amount) AS totalsales,SUM(0) totalcommission']);
                $srchObj1 = clone $srch;
                $srchObj1->addFld(['1 AS num_days']);
                $srchObj1->addDirectCondition('DATE(order_date_updated) = DATE(NOW())');
                $srchObj7 = clone $srch;
                $srchObj7->addFld(['7 AS num_days']);
                $srchObj7->addDirectCondition('YEARWEEK(order_date_updated) = YEARWEEK(NOW())');
                $srchObj30 = clone $srch;
                $srchObj30->addFld(['30 AS num_days']);
                $srchObj30->addDirectCondition('MONTH(order_date_updated)=MONTH(NOW())');
                $srchObj90 = clone $srch;
                $srchObj90->addFld(['90 AS num_days']);
                $srchObj90->addDirectCondition('order_date_updated>date_sub(date_add(date_add(LAST_DAY(now()),interval 1 DAY),interval -1 MONTH), INTERVAL 3 MONTH)');
                $srchObjAll = clone $srch;
                $srchObjAll->addFld(['-1 AS num_days']);
                $sql = $srchObj1->getQuery() . " UNION ALL " . $srchObj7->getQuery() . " UNION ALL " . $srchObj30->getQuery() . " UNION ALL " . $srchObj90->getQuery() . " UNION ALL " . $srchObjAll->getQuery();
                $rs = $this->db->query($sql);
                return $this->db->fetchAll($rs);
            case 'total_earnings':
                $srch = new OrderProductSearch();
                $srch->joinorders();
                $srch->doNotCalculateRecords();
                $srch->doNotLimitRecords();
                $srch->addCondition('o.order_type', '=', Order::TYPE_LESSON_BOOKING);
                $cnd = $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
                $srch->addGroupBy('op.op_id');
                $srch->addMultipleFields(['order_net_amount - op_total_refund_amount - ((op_qty-op_refund_qty) * op_commission_charged) as  totalEarnings']);
                $srchObj1 = clone $srch;
                $srchObj1->addFld(['1 AS num_days,op.op_id']);
                $srchObj1->addDirectCondition('DATE(order_date_updated) = DATE(NOW())');
                $srchObj7 = clone $srch;
                $srchObj7->addFld(['7 AS num_days,op.op_id']);
                $srchObj7->addDirectCondition('YEARWEEK(order_date_updated) = YEARWEEK(NOW())');
                $srchObj30 = clone $srch;
                $srchObj30->addFld(['30 AS num_days,op.op_id']);
                $srchObj30->addDirectCondition('MONTH(order_date_updated)=MONTH(NOW())');
                $srchObj90 = clone $srch;
                $srchObj90->addFld(['90 AS num_days,op.op_id']);
                $srchObj90->addDirectCondition('order_date_updated>date_sub(date_add(date_add(LAST_DAY(now()),interval 1 DAY),interval -1 MONTH), INTERVAL 3 MONTH)');
                $srchObjAll = clone $srch;
                $srchObjAll->addFld(['-1 AS num_days,op.op_id']);
                $sql = $srchObj1->getQuery() . " UNION ALL " . $srchObj7->getQuery() . " UNION ALL " . $srchObj30->getQuery() . " UNION ALL " . $srchObj90->getQuery() . " UNION ALL " . $srchObjAll->getQuery();
                $rs = $this->db->query($sql);
                $result = $this->db->fetchAll($rs);
                if ($result) {
                    $resArr = [];
                    $resArray = [];
                    foreach ($result as $res) {
                        $resArr[$res['num_days']][] = $res;
                    }
                    foreach ($resArr as $k => $resArrNew) {
                        $resArray[$k]['totalEarnings'] = array_sum(array_column($resArrNew, 'totalEarnings'));
                        $resArray[$k]['num_days'] = $k;
                    }
                    return $resArray;
                }
                return $result;
                break;
        }
    }

    public function getTopLessonLanguages($type, $langId = 0, $pageSize = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG');
        }
        $srch = new ScheduledLessonSearch();
        $srch->joinLessonLanguage($langId);
        $srch->doNotCalculateRecords();
        if ($pageSize > 0) {
            $srch->setPageSize($pageSize);
        } else {
            $srch->doNotLimitRecords();
        }
        $srch->addMultipleFields(['IFNULL(tlanguage_name , tlanguage_Identifier) as languageName', 'count(slesson_id) as lessonsSold']);
        switch (strtoupper($type)) {
            case 'TODAY':
                $srch->addDirectCondition('DATE(slesson_added_on)=DATE(NOW())');
                break;
            case 'WEEKLY':
                $srch->addDirectCondition('YEARWEEK(slesson_added_on)=YEARWEEK(NOW())');
                break;
            case 'MONTHLY':
                $srch->addDirectCondition('MONTH(slesson_added_on)=MONTH(NOW())');
                break;
            case 'YEARLY':
                $srch->addDirectCondition('YEAR(slesson_added_on)=YEAR(NOW())');
                break;
        }
        $srch->addGroupBy('slesson_slanguage_id');
        $srch->addOrder('lessonsSold', 'desc');
        $rs = $srch->getResultSet();
        return $this->db->fetchAll($rs);
    }

    public static function getLast12MonthsDetails($months = 12)
    {
        $month = date('m');
        $year = date('Y');
        $i = 1;
        $date = [];
        while ($i <= $months) {
            $timestamp = mktime(0, 0, 0, $month, 1, $year);
            $date[$i]['monthCount'] = date('m', $timestamp);
            $date[$i]['monthShort'] = date('M', $timestamp);
            $date[$i]['yearShort'] = date('y', $timestamp);
            $date[$i]['year'] = date('Y', $timestamp);
            $month--;
            $i++;
        }
        return $date;
    }

    public static function salesReportObject($langId = 0, $joinSeller = false, $attr = [])
    {
        $orderSrch = new OrderSearch();
        $orderSrch->joinOrderProduct($langId);
        $orderSrch->addMultipleFields([
            'DATE(order_date_added) as order_date', 'SUM(order_net_amount) as orderNetAmount',
            'SUM(op_total_refund_amount) as op_total_refund_amount', 'count(op_id) as totOrders',
            'SUM(order_net_amount - op_total_refund_amount - ((op.op_qty-op_refund_qty) * op_commission_charged)) as  Earnings'
        ]);
        return $orderSrch;
    }

    public static function LessonLanguagesObject($langId = 0)
    {
        $srch = new ScheduledLessonSearch(false);
        $srch->joinLessonLanguage($langId);
        return $srch;
    }

}
