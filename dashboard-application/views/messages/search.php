<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (empty($arr_listing)) {
    $this->includeTemplate('_partial/no-record-found.php');
} else {
    $loggedUserId = UserAuthentication::getLoggedUserId();
    $userTimeZone = MyDate::getUserTimeZone();
    foreach ($arr_listing as $sn => $row) {
        $imgUserId = $row['message_from_user_id'];
        $imgUserName = $row['message_from_name'];
        if ($row['message_from_user_id'] == $loggedUserId) {
            $imgUserId = $row['message_to_user_id'];
            $imgUserName = $row['message_to_name'];
        }
        $liClass = 'is-read';
        if ($row['message_is_unread'] == Thread::MESSAGE_IS_UNREAD && $row['message_to'] == $loggedUserId) {
            $liClass = '';
        }
        ?>
        <div class="msg-list">
            <div class="msg-list__left">
                <div class="avtar avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($imgUserName); ?>">
                    <?php
                    if (true == User::isProfilePicUploaded($imgUserId)) {
                        echo '<img src="' . CommonHelper::generateUrl('Image', 'user', array($imgUserId), CONF_WEBROOT_FRONT_URL) . '?' . time() . '" alt="' . $imgUserName . '" />';
                    }
                    ?>
                </div>
            </div>
            <div class="msg-list__right">
                <h6><?php echo $imgUserName; ?></h6>
                <p><?php echo CommonHelper::truncateCharacters($row['message_text'], 280); ?></p>
                <date><?php echo MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $row['message_date'], true, $userTimeZone); ?></date>
            </div>
            <a href="javascript:void(0);" onclick="getThread(<?php echo $row['thread_id']; ?>, 1);" class="msg-list__action msg-list__action-js"></a>
        </div>
        <?php
    }
}
