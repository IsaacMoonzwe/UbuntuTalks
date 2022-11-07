<div class="table-scroll">
    <table class="table table--styled table--responsive">
        <tr class="title-row">
            <th><?php echo $titleLabel = Label::getLabel('LBL_Title'); ?></th>
            <th><?php echo $imageLabel = Label::getLabel('LBL_Image'); ?></th>
            <th><?php echo $descriptionLabel = Label::getLabel('LBL_Description'); ?></th>
            <th><?php echo $levelLabel = Label::getLabel('LBL_Level'); ?></th>
            <th><?php echo $actionLabel = Label::getLabel('LBL_Actions'); ?></th>
        </tr>
        <?php foreach ($lessonsPlanData as $lessonPlanData) { ?>
            <tr>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $titleLabel; ?> </div>
                        <div class="flex-cell__content">
                            <div style="max-width: 250px;">
                                <span class="bold-600"><?php echo $lessonPlanData['tlpn_title']; ?></</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $imageLabel; ?> </div>
                        <div class="flex-cell__content">
                            <div style="max-width: 250px;">
                                <?php
                                $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_LESSON_PLAN_IMAGE, $lessonPlanData['tlpn_id'], 0);
                                if (empty($file_row)) {
                                    echo '<span class="bold-600">' . CommonHelper::displayNotApplicable('') . '</span>';
                                } else {
                                    ?>
                                    <div style="max-width: 250px;">
                                        <img src="<?php echo CommonHelper::generateFullUrl('TeacherLessonsPlan', 'lessonPlanImage', array($lessonPlanData['tlpn_id'], 'THUMB')) . '?' . time(); ?>" alt="<?php echo $imageLabel; ?>" />
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"> <?php echo $descriptionLabel; ?> </div>
                        <div class="flex-cell__content">
                            <div style="max-width: 250px;"><?php echo substr($lessonPlanData['tlpn_description'], 0, 150); ?></div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $levelLabel ?></div>
                        <div class="flex-cell__content"><span class="badge color-secondary badge--curve"><?php echo $statusArr[$lessonPlanData['tlpn_level']]; ?></span></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $actionLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="actions-group">
                                <a href="javascript:void(0);" onclick="add('<?php echo $lessonPlanData['tlpn_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--issue icon--small">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#edit'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Edit'); ?></div>
                                </a>
                                <a href="javascript:void(0);" onclick="removeLesson('<?php echo $lessonPlanData['tlpn_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--issue icon--small">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#trash'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Delete'); ?></div>
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
if ($countData == 0) {
    $this->includeTemplate('_partial/no-record-found.php');
} else {
    echo FatUtility::createHiddenFormFromData($postedData, array(
        'name' => 'lessonPlanPaginationForm'
    ));
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
}
?>
</div>
