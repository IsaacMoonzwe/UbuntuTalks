<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
MyDate::setUserTimeZone();
$user_timezone = MyDate::getUserTimeZone();
$date = new DateTime("now", new DateTimeZone($user_timezone));
$curDate = $date->format('Y-m-d');
$nextDate = date('Y-m-d', strtotime('+1 days', strtotime($curDate)));
$passedLabel = Label::getLabel('LBL_PASSED');

?>
<div class="table-scroll">
    <table class="table table--styled table--responsive">
        <tr class="title-row">
            <th><?php echo $detailLabel = Label::getLabel('LBL_Details'); ?></th>
            <th><?php echo $startAtLabel = Label::getLabel('LBL_Start_At'); ?></th>
            <th><?php echo $endAtLabel = Label::getLabel('LBL_End_At'); ?></th>
            <th><?php echo $statusLabel = Label::getLabel('LBL_Status'); ?></th>
            <th><?php echo $actionLabel = Label::getLabel('LBL_Actions'); ?></th>
        </tr>
        <?php foreach ($classes as $i => $class) {
            $statusLabel = $classStatusArr[$class['grpcls_status']];
            // if ($class['grpcls_status'] == TeacherKidsClasses::STATUS_ACTIVE) {
            //     $statusLabel = $passedLabel;
            // }
        ?>
            <tr>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $detailLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="data-group">
                                <span class="bold-600"> <?php echo $class['grpcls_title']; ?></span><br>
                                <span><?php echo Label::getLabel('LBL_Booked_Seats'); ?> - <?php echo $class['total_learners']; ?></span><br>
                                <span><?php echo Label::getLabel('LBL_Entry_fee'); ?> - <?php echo CommonHelper::displayMoneyFormat($class['grpcls_entry_fee']); ?></span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $startAtLabel; ?> </div>
                        <div class="flex-cell__content"><?php echo MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i', $class['grpcls_start_datetime'], true, $user_timezone); ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $endAtLabel; ?></div>
                        <div class="flex-cell__content"><?php echo MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i', $class['grpcls_end_datetime'], true, $user_timezone); ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $statusLabel; ?></div>
                        <div class="flex-cell__content"><span class="badge color-secondary badge--curve"><?php echo $statusLabel; ?></span></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $actionLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="actions-group">
                                <?php
                                if ($class['slesson_id']) {
                                //    if ($class['grpcls_end_datetime'] > date('Y-m-d H:i:s')) {
                                ?>
                                        <a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons', 'view', array($class['slesson_id'])); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                            <svg class="icon icon--issue icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#enter'; ?>"></use>
                                            </svg>
                                            <div class="tooltip tooltip--top bg-black"><?php echo ($class['is_joined'] ? Label::getLabel("LBL_View_Class") : Label::getLabel("LBL_Start_Class")); ?></div>
                                        </a>
                                    <?php //}
                                    if ($class['repiss_id'] > 0) { ?>
                                        <a href="<?php echo CommonHelper::generateUrl('TeacherIssueReported', 'index', [$class['grpcls_id']]) ?>" target="_blank" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                            <svg class="icon icon--issue icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#view-report'; ?>"></use>
                                            </svg>
                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel("LBL_Class_Issue_details") ?></div>
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                                        <a ref="javascript:void(0);" onclick="form('<?php echo $class['grpcls_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                            <svg class="icon icon--issue icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#edit'; ?>"></use>
                                            </svg>
                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel("LBL_Edit"); ?></div>
                                        </a>
                                        <a href="javascript:void(0);" onclick="cancelClass('<?php echo $class['grpcls_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                            <svg class="icon icon--issue icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#cancel'; ?>"></use>
                                            </svg>
                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel("LBL_Cancel") ?></div>
                                        </a>
                                   
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php
if (empty($classes)) {
    $this->includeTemplate('_partial/no-record-found.php');
} else {
    echo FatUtility::createHiddenFormFromData($postedData, array(
        'name' => 'frmSearchPaging'
    ));
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
}
?>