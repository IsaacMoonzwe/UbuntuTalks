<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box -padding-30 -skin">
    <div class="box__count -float-right"><span id="currentReviewedCount"><?php echo $currentReviewedCount; ?></span>/<span id="allFCardCounts"></span><?php echo $allFCardCounts; ?></div>
    <span class="-gap"></span>
    <div id="card-1" class="card">
        <div class="other-front">
            <div class="box__caption"><h5><?php echo Label::getLabel('LBL_Word'); ?> : <?php echo $flashCardData['flashcard_title'] ?? ''; ?></h5></div>
        </div>
        <div class="other-back">
            <div class="box__caption"><h5><?php echo Label::getLabel('LBL_Defination'); ?> :<?php echo $flashCardData['flashcard_defination'] ?? ''; ?></h5></div>
            <span class="-gap"></span>
            <div class="row row--actions">
                <?php
                foreach ($flashCardAccuracyArr as $key => $val) {
                    if ($key == 1) {
                        $cls = 'status-correct';
                    }
                    if ($key == 2) {
                        $cls = 'status-top';
                    }
                    if ($key == 3) {
                        $cls = 'danger';
                    }
                    $onClick = !empty($flashCardData['flashcard_id']) ? 'onclick="setUpFlashCardReview(' . $flashCardData['flashcard_id'] . ',' . $key . ')"' : '';
                    ?>
                    <div class="col-4"><a href="javascript:void(0);" <?php echo $onClick; ?> class="here btn btn--<?php echo $cls; ?> btn--block"><?php echo $val; ?></a></div>
                <?php } ?>
            </div>
        </div>
    </div>
    <p class="-align-center -color-light -style-italic"><?php echo Label::getLabel('LBL_Click_On_Words_To_Flip_It') ?></p>
</div>
<style type="text/css">
    .card {
        width: 100%;
        margin: 0px 0 30px;
    }
    .front, .back, .other-back {
        border: 2px gray solid;
        padding:15px;width:100%;background:#fff;
    }
    .other-front{background:#fff;}
    .other-front .box__caption{border: 2px gray solid;padding:15px;width:100%;background:#fff;}
</style>
<script >
    $(function () {
        currentReviewedCount = $("#currentReviewedCount").html();
        allFCardCounts = $("#allFCardCounts").html();
        $("#card-1").flip({
            axis: "y", // y or x
            reverse: false, // true and false
            trigger: "click", // click or hover
            speed: '250',
            front: $('.other-front'),
            back: $('.other-back'),
            autoSize: true
        });
    });
</script>
