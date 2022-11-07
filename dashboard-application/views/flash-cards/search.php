<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="table-scroll">
    <table class="table table--styled table--responsive table--aligned-middle">
        <tr class="title-row">
            <th><?php echo $wordLabel = Label::getLabel('LBL_Word'); ?></th>
            <th><?php echo $definitionLabel = Label::getLabel('LBL_Definition'); ?></th>
            <th><?php echo $actionLabel = Label::getLabel('LBL_Action'); ?></th>
        </tr>
        <?php foreach ($flashCards as $flashCard) { ?>
            <tr>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $wordLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php
                            echo $flashCard['flashcard_title'] . " (" . $flashCard['wordLanguageCode'] . ")";
                            ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $definitionLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php echo $flashCard['flashcard_defination'] . " (" . $flashCard['wordDefLanguageCode'] . ")"; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $actionLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="actions-group">
                                <a  href="javascript:void(0)" onclick="flashCardForm(<?php echo $flashCard['flashcard_id']; ?>)" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--issue icon--small">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#edit'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Edit'); ?></div>
                                </a>
                                <a href="javascript:void(0);" onclick="remove('<?php echo $flashCard['flashcard_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--issue icon--small">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#trash'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Remove'); ?></div>
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
    'name' => 'frmFlashCardSearchPaging'
));
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
if (empty($flashCards)) {
    $this->includeTemplate('_partial/no-record-found.php');
}
?>
