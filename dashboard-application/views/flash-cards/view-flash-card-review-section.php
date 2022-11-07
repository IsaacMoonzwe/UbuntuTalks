<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="flashcard__media">
    <svg class="icon icon--wallet icon--xlarge color-primary">
    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#card-game'; ?>"></use>
    </svg>
</div>
<div class="flashcard__content">
    <div class="flashcard__content-title">
        <h3 class="bold-700"><?php echo $todayReviewedCounts; ?>/<?php echo $allFCardCounts; ?></h3>
        <?php if ('' != $lastReviewedOnDate && $lastReviewedOnDate > '1972-01-01') { ?>
            <p class="small margin-0">
            <?php echo Label::getLabel('LBL_Last_Reviewed'); ?> <date><?php echo FatDate::format($lastReviewedOnDate); ?></date>
            </p>
        <?php } ?>
    </div>
    <div class="flashcard__content-actions">
        <div class="buttons-group">
            <?php
            $btnClass = 'tn--disabled';
            $onclick = '';
            if ($allFCardCounts) {
                $btnClass = 'bg-primary';
                $onclick = 'onclick="reviewFlashCard();"';
            }
            ?>
            <a href="javascript:void(0);" <?php echo $onclick; ?> class="btn <?php echo $btnClass; ?> btn--icon">
                <svg class="icon icon--issue icon--small margin-right-2">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#view'; ?>"></use>
                </svg>
                <?php echo Label::getLabel('LBL_View_Flashcards'); ?>
            </a>
            <a href="javascript:void(0);" onclick="flashCardForm(0);" class="btn btn--bordered color-secondary"><?php echo Label::getLabel('LBL_Add_Flashcard'); ?></a>
        </div>
    </div>
</div>