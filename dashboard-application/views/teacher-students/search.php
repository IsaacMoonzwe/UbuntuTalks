<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="table-scroll">
    <table class="table table--styled table--responsive table--aligned-middle">
        <tr class="title-row">
            <th><?php echo $learnerLabel = Label::getLabel('LBL_Learner'); ?></th>
            <th><?php echo $loclLabel = Label::getLabel('LBL_Lock_Lesson_offer(%)'); ?></th>
            <th><?php echo $scheduledLabel = Label::getLabel('LBL_Scheduled'); ?></th>
            <th><?php echo $pastLabel = Label::getLabel('LBL_Past'); ?></th>
            <th><?php echo $unscheduledLabel = Label::getLabel('LBL_Unscheduled'); ?></th>
            <th><?php echo $actionLabel = Label::getLabel('LBL_Actions'); ?></th>
        </tr>
        <?php
        $offerPriceLabel = Label::getLabel('LBL_SET_LESSON_OFFER(%)_FOR_LEARNER');
        foreach ($students as $student) {
        ?>
            <tr>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $learnerLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="profile-meta">
                                <div class="profile-meta__media">
                                    <span class="avtar avtar--small" data-title="<?php echo CommonHelper::getFirstChar($student['learnerFullName']); ?>">
                                        <?php echo '<img src="' . CommonHelper::generateUrl('Image', 'user', array($student['user_id']), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"  alt="' . $student['learnerFullName'] . '"/>'; ?>
                                    </span>
                                </div>
                                <div class="profile-meta__details">
                                    <p class="bold-600 color-black"><?php echo $student['learnerFullName']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $loclLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php
                            $svgIconClass = " color-black";
                            $svgIcon = 'unlock';
                            if (!empty($student['offerPrice'])) {
                                $svgIconClass = "color-primary";
                                $svgIcon = 'lock';
                            }
                            ?>
                            <div class="actions-group">
                                <a href="javascript:void(0);" onclick="offerForm(<?php echo $student['user_id']; ?>);" class="btn btn--bordered noborder btn--shadow btn--equal margin-1 is-hover padding-3 is-hover <?php echo $svgIconClass; ?>">
                                    <svg class="icon icon--clock icon--small margin-right-2">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#' . $svgIcon; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo $offerPriceLabel; ?></div>
                                </a>
                            </div>
                            <div class="lesson-price">
                                <?php foreach ($student['offerPrice'] as $duration => $percentage) { ?>
                                    <p><?php echo sprintf(Label::getLabel('LBL_%d_mins'), $duration) . ': ' . $percentage . '%'; ?> </p>
                                <?php }
                                if (empty($student['offerPrice'])) {
                                    echo Label::getLabel('LBL_N/A');
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $scheduledLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $student['scheduledCount']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $pastLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $student['unScheduledCount']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $unscheduledLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $student['pastCount']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $actionLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="actions-group">
                                <a href="javascript:void(0);" onClick="generateThread(<?php echo $student['user_id']; ?>);" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--messaging">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#message'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Message'); ?></div>
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
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmTeacherStudentsSearchPaging'
));
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
if (empty($students)) {
    $this->includeTemplate('_partial/no-record-found.php');
}
