<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="table-scroll">
    <table class="table table--styled table--responsive table--aligned-middle">
        <tr class="title-row">
            <th><?php echo $teacherLabel = Label::getLabel('LBL_Teacher'); ?></th>
            <th><?php echo $teachesLabel = Label::getLabel('LBL_Teaches'); ?></th>
            <th><?php echo $ratingLabel = Label::getLabel('LBL_Average_Rating'); ?></th>
            <th><?php echo $loclLabel = Label::getLabel('LBL_Lock_(Single/Bulk_Price)'); ?></th>
            <th><?php echo $scheduledLabel = Label::getLabel('LBL_Scheduled'); ?></th>
            <th><?php echo $pastLabel = Label::getLabel('LBL_Past'); ?></th>
            <th><?php echo $unscheduledLabel = Label::getLabel('LBL_Unscheduled'); ?></th>
            <th><?php echo $actionLabel = Label::getLabel('LBL_Actions'); ?></th>
        </tr>
        <?php
        foreach ($teachers as $teacher) {
            $teacherDetailPageUrl = CommonHelper::generateUrl('teachers', 'view', array(CommonHelper::htmlEntitiesDecode($teacher['user_url_name'])), CONF_WEBROOT_FRONTEND);
        ?>
            <tr>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $teacherLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="profile-meta">
                                <div class="profile-meta__media">
                                    <a title="<?php echo $teacher['teacherFullName']; ?>" href="<?php echo $teacherDetailPageUrl; ?>">
                                        <span class="avtar avtar--small" data-title="<?php echo CommonHelper::getFirstChar($teacher['teacherFullName']); ?>">
                                            <?php echo '<img src="' . CommonHelper::generateUrl('Image', 'user', array($teacher['user_id'], 'normal', 1), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"  alt="' . $teacher['teacherFullName'] . '"/>'; ?>
                                        </span>
                                    </a>
                                </div>
                                <div class="profile-meta__details">
                                    <p class="bold-600 color-black"><?php echo $teacher['teacherFullName']; ?></p>
                                    <p class="small"><?php echo $teacher['user_country_name']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $teachesLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $teacher['teacherTeachLanguageName']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $ratingLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $teacher['testat_ratings']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $loclLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php
                            $svgIconClass = " color-black";
                            $svgIcon = 'unlock';
                            if (!empty($teacher['offerPrice'])) {
                                $svgIconClass = "color-primary";
                                $svgIcon = 'lock';
                            }
                            ?>
                            <a href="javascript:void(0);" class="padding-3 <?php echo $svgIconClass; ?>">
                                <svg class="icon icon--clock icon--small margin-right-2">
                                    <use xlink:href="<?php ?>images/sprite.yo-coach.svg#<?php echo $svgIcon ?>"></use>
                                </svg>
                            </a>
                            <div class="lesson-price">
                                <?php foreach ($teacher['offerPrice'] as $duration => $percentage) { ?>
                                    <p><?php echo sprintf(Label::getLabel('LBL_%d_mins'), $duration) . ': ' . $percentage . "%"; ?> </p>
                                <?php }
                                if (empty($teacher['offerPrice'])) {
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
                        <div class="flex-cell__content"><?php echo $teacher['scheduledCount']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $pastLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $teacher['pastCount']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $unscheduledLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $teacher['unScheduledCount']; ?></div>
                    </div>
                </td>
                <td>
                    <?php if (!empty($teacher['teacherTeachLanguageName'])) { ?>
                        <div class="flex-cell">
                            <div class="flex-cell__label"><?php echo $actionLabel; ?></div>
                            <div class="flex-cell__content">
                                <div class="actions-group">
                                    <a href="javascript:void(0);" onclick="cart.proceedToStep({teacherId: <?php echo $teacher['user_id']; ?>}, 'getUserTeachLangues');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--buy">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#icon-buy'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Buy_Now'); ?></div>
                                    </a>
                                    <a href="javascript:void(0);" onClick="generateThread(<?php echo $teacher['user_id']; ?>);" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--messaging">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#message'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Message'); ?></div>
                                </a>
                                </div>
                            </div>
                        </div>
                </td>
            <?php } ?>
            </tr>
        <?php } ?>
    </table>
</div>
<?php
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmLearnerTeachersSearchPaging'
));
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
if (empty($teachers)) {
    $this->includeTemplate('_partial/no-record-found.php');
}
