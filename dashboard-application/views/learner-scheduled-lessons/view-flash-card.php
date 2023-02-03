<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box -padding-20">
    <h4><?php echo Label::getLabel('LBL_Flashcard'); ?></h4>
    <table class="table table--cols">
        <tr>
            <td><strong><?php echo Label::getLabel('LBL_Title') ?></strong></td>
            <td><?php echo $flashCardData['flashcard_title'] . " (" . $flashCardData['wordLanguageCode'] . ")"; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo Label::getLabel('LBL_Defination') ?></strong></td>
            <td><?php echo $flashCardData['flashcard_defination'] . " (" . $flashCardData['wordDefLanguageCode'] . ")"; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo Label::getLabel('LBL_Pronunciation') ?></strong> </td>
            <td><?php echo $flashCardData['flashcard_pronunciation']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo Label::getLabel('LBL_Notes') ?> </strong> </td>
            <td><?php echo $flashCardData['flashcard_notes']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo Label::getLabel('LBL_Added_On') ?> </strong>  </td>
            <td><?php echo FatDate::format($flashCardData['flashcard_added_on'], true); ?></td>
        </tr>
    </table>
</div>