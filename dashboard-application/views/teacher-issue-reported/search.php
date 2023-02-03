<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

if (count($lessons) > 0) {
    $userTimezone = MyDate::getUserTimeZone();
    $systemTimezone = MyDate::getTimeZone();
    $issueStatusArr = ReportedIssue::getStatusArr();
    ?>
    <div class="table-scroll">
        <table class="table table--styled table--responsive table--aligned-middle">
            <tr class="title-row">
                <th><?php echo $learnerLabel = Label::getLabel('LBL_Learner'); ?></th>
                <th><?php echo $lessonDetailLabel = Label::getLabel('LBL_Lesson_Detail'); ?></th>
                <th><?php echo $issueStatus = Label::getLabel('LBL_Issue_Status'); ?></th>
                <th><?php echo $actionLabel = Label::getLabel('LBL_Actions'); ?></th>
            </tr>
            <?php foreach ($lessons as $issue) { ?>
                <tr>
                    <td>
                        <div class="flex-cell">
                            <div class="flex-cell__label"><?php echo $learnerLabel; ?></div>
                            <div class="flex-cell__content">
                                <div class="profile-meta">
                                    <div class="profile-meta__media">
                                        <span class="avtar avtar--small" data-title="<?php echo CommonHelper::getFirstChar($issue['learnerFname']); ?>">
                                            <?php
                                            if (true == User::isProfilePicUploaded($issue['learnerId'])) {
                                                $img = CommonHelper::generateUrl('Image', 'user', [$issue['learnerId'], 'SMALL'], CONF_WEBROOT_FRONT_URL) . '?' . time();
                                                echo '<img src="' . $img . '"  alt="' . $issue['learnerFname'] . '"/>';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="profile-meta__details">
                                        <p class="bold-600 color-black"><?php echo $issue['learnerFname']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex-cell">
                            <div class="flex-cell__label"><?php echo $lessonDetailLabel; ?></div>
                            <div class="flex-cell__content">
                                <div class="data-group">
                                    <?php
                                    if ($issue['slesson_date'] != "0000-00-00") {
                                        $date = MyDate::changeDateTimezone($issue['slesson_date'] . ' ' . $issue['slesson_start_time'], $systemTimezone, $userTimezone);
                                        ?>
                                        <span><?php echo '<b>' . Label::getLabel('LBL_Schedule') . '</b> - ' . $date; ?></span><br>
                                    <?php } ?>
                                    <span><?php echo '<b>' . Label::getLabel('LBL_Status') . '</b> - ' . $statusArr[$issue['slesson_status']]; ?></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex-cell">
                            <div class="flex-cell__label"><?php echo $statusLabel; ?></div>
                            <div class="flex-cell__content"><span class="badge color-secondary badge--curve"><?php echo $issueStatusArr[$issue['repiss_status']]; ?></span></div>
                        </div>
                    </td>
                    <td>
                        <div class="flex-cell">
                            <div class="flex-cell__label"><?php echo $actionLabel; ?></div>
                            <div class="flex-cell__content">
                                <div class="actions-group">
                                    <a href="javascript:void(0);" onclick="issueDetails('<?php echo $issue['repiss_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--issue icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#view'; ?>"></use></svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel("LBL_View_detail"); ?></div>
                                    </a>
                                    <?php if ($issue['repiss_status'] == ReportedIssue::STATUS_PROGRESS) { ?>
                                        <a href="javascript:void(0);" onclick="resolveForm('<?php echo $issue['repiss_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                            <svg class="icon icon--issue icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#resolve-issue'; ?>"></use></svg>
                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel("LBL_Resolve_Issue"); ?></div>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php
    echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmTeacherStudentsSearchPaging']);
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
} else {
    $this->includeTemplate('_partial/no-record-found.php');
}
