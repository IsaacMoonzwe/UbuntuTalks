<?php
$arr_flds = array(
    'check' => '',
    'profile' => '',
    'noti_title' => Label::getLabel('LBL_Notification_TITLE', $siteLangId),
    'noti_sent_on' => Label::getLabel('LBL_Notification_Sent_ON', $siteLangId),
);
$user_timezone = MyDate::getUserTimeZone();
$tbl = new HtmlElement('table', array('class' => 'table-listing'));
foreach ($list as $sn => $order) {
    $notiTrcls = ($order['noti_is_read'] == applicationConstants::NO) ? '' : 'is-read';
    $tr = $tbl->appendElement('tr', array('class' => $notiTrcls));
    $bell = '/images/bell-colored.svg';
    $notificationUrl = CommonHelper::generateUrl('notifications', 'readNotification', array($order['noti_id']));
    foreach ($arr_flds as $key => $val) {
        $tdClass = ($key == 'check') ? 'td--check' : ( ($key == 'profile') ? 'td--avtar' : '' );
        $td = $tr->appendElement('td', array('class' => $tdClass));
        switch ($key) {
            case 'check':
                $td->appendElement('plaintext', array('class' => 'td--check'), '<label class="checkbox"><input type="checkbox" class="check-record" rel=' . $order['noti_id'] . '><i class="input-helper"></i></label>', true);
                break;
            case 'profile':
                $div = $td->appendElement('div', array('class' => 'avtar avtar--xsmall', 'data-title' => 'A'));
                if ($order['noti_type'] != UserNotifications::NOTICATION_FOR_TEACHER_APPROVAL) {
                    $picId = ($order['noti_sub_record_id'] == 0) ? UserAuthentication::getLoggedUserId() : $order['noti_sub_record_id'];
                    if (true == User::isProfilePicUploaded($order['noti_sub_record_id'])) {
                        $div->appendElement('img', array('src' => CommonHelper::generateUrl('Image', 'user', array($picId, 'MINI', true), CONF_WEBROOT_FRONT_URL)));
                    }
                }
                break;
            case 'noti_title':
                $txt = '<div class="listing__desc"><a href="' . $notificationUrl . '"><strong>' . $order[$key] . '</strong>';
                $txt .= '<br>' . $order['noti_desc'] . '</a></div>';
                $td->appendElement('plaintext', array(), $txt, true);
                break;
            case 'noti_sent_on':
                $dateTime = explode(' ', $order['noti_sent_on']);
                $txt = '<span class="date"> ' . MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $order['noti_sent_on'], true, $user_timezone) . ' <span class="time">' . MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $order['noti_sent_on'], true, $user_timezone) . '</span></span>';
                $td->appendElement('plaintext', array(), $txt, true);
                break;
            default:
                $td->appendElement('plaintext', array(), '<span class="caption--td">' . $val . '</span>' . $order[$key], true);
                break;
        }
    }
}
if (empty($list)) {
    $this->includeTemplate('_partial/no-record-found.php', array('siteLangId' => $siteLangId));
} else {
    ?>
    <!-- [ PAGE CONTROLS ========= -->
    <div class="page-controls">
        <div class="row justify-content-between">
            <aside class="col-md-auto col-sm-7">
                <ul class="controls">
                    <li>
                        <span>
                            <label class="checkbox">
                                <input type="checkbox" class="check-all"><i class="input-helper"></i>
                            </label>
                        </span>
                    </li>
                </ul>
                <ul class="controls">
                    <li>
                        <a href="javascript:void(0);" onclick="deleteRecords();" class="btn btn--bordered is-hover">
                            <span class="svg-icon">
                                <svg class="icon icon--messaging">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#trash'; ?>"></use>
                                </svg>
                            </span>
                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Delete'); ?></div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" onclick="searchNotification(document.frmNotificationSrch);" class="btn btn--bordered is-hover">
                            <span class="svg-icon"><svg class="icon icon--messaging">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#refresh'; ?>"></use>
                                </svg></span>
                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_refresh'); ?></div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" onclick="changeStatus(0);" class="btn btn--bordered is-hover">
                            <span class="svg-icon"><svg class="icon icon--messaging">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#closed-envelope'; ?>"></use>
                                </svg></span>
                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Mark_as_Unread'); ?></div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" onclick="changeStatus(1);" class="btn btn--bordered is-hover">
                            <span class="svg-icon">
                                <svg class="icon icon--messaging">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#open-envelope'; ?>"></use>
                                </svg>
                            </span>
                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Mark_as_Read'); ?></div>
                        </a>
                    </li>
                </ul>
            </aside>
            <?php
            $postedData['page'] = $page;
            echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmNotificationSrch'));
            $pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'listCount' => count($list), 'pageSize' => $pagesize, 'recordCount' => $recordCount, 'callBackJsFunc' => 'goToNotificationSearchPage');
            $this->includeTemplate('_partial/notification-pagination.php', $pagingArr);
            ?>
        </div>
    </div>
    <!-- ] ========= -->
    <!-- [ NOTIFICATONS ========= -->
    <?php echo $tbl->getHtml(); ?>
<?php } ?>
<script>
    $(".check-all").on('click', function () {
        if ($(this).prop('checked') == true) {
            $('.check-record').prop('checked', true);
        } else {
            $('.check-record').prop('checked', false);
        }
    });
</script>