<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="repeatedrow">
    <h3><?php echo Label::getLabel('LBL_TEACHER_RATING_INFORMATION'); ?></h3>
    <div class="rowbody">
        <div class="listview">
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_ORDER_ID'); ?></dt>
                <dd><?php echo $data['tlreview_order_id']; ?></dd>
            </dl>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_REVIEWED_BY'); ?></dt>
                <dd><?php echo $data['reviewed_by']; ?></dd>
            </dl>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_DATE'); ?></dt>
                <dd><?php echo FatDate::format($data['tlreview_posted_on']); ?></dd>
            </dl>
            <?php foreach ($ratingData as $rating) { ?>
                <dl class="list">
                    <dt><?php echo $ratingTypeArr[$rating['tlrating_rating_type']]; ?></dt>
                    <dd><ul class="rating list-inline">
                            <?php for ($j = 1; $j <= 5; $j++) { ?>	
                                <li class="<?php echo $j <= round($rating["tlrating_rating"]) ? "active" : "in-active" ?>">
                                    <svg xml:space="preserve" enable-background="new 0 0 70 70" viewBox="0 0 70 70" height="18px" width="18px" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Layer_1" version="1.1">
                                        <g><path d="M51,42l5.6,24.6L35,53.6l-21.6,13L19,42L0,25.4l25.1-2.2L35,0l9.9,23.2L70,25.4L51,42z M51,42" fill="<?php echo $j <= round($rating["tlrating_rating"]) ? "#ff3a59" : "#474747" ?>" /></g></svg>
                                </li>
                            <?php } ?>
                        </ul></dd>
                </dl>
            <?php } ?>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_NET_RATING'); ?></dt>
                <dd>
                    <ul class="rating list-inline">
                        <?php for ($j = 1; $j <= 5; $j++) { ?>	
                            <li class="<?php echo $j <= round($avgRatingData['average_rating']) ? "active" : "in-active" ?>">
                                <svg xml:space="preserve" enable-background="new 0 0 70 70" viewBox="0 0 70 70" height="18px" width="18px" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Layer_1" version="1.1">
                                    <g><path d="M51,42l5.6,24.6L35,53.6l-21.6,13L19,42L0,25.4l25.1-2.2L35,0l9.9,23.2L70,25.4L51,42z M51,42" fill="<?php echo $j <= round($avgRatingData['average_rating']) ? "#ff3a59" : "#474747" ?>" /></g></svg>
                            </li>
                        <?php } ?>
                    </ul></dd>
            </dl>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_REVIEW_COMMENTS'); ?></dt>
                <?php $findKeywordStr = ''; ?>
                <dd><?php echo nl2br($data['tlreview_description']); ?></dd>
            </dl>				
        </div>		
    </div>
    <?php if ($data['tlreview_status'] !== TeacherLessonReview::STATUS_PENDING) { ?>
        <br>
            <h3><?php echo Label::getLabel('LBL_CHANGE_STATUS'); ?></h3>
            <div class="rowbody space">
                <div class="listview">
                    <?php
                    $frm->setFormTagAttribute('class', 'web_form form_horizontal');
                    $frm->setFormTagAttribute('onsubmit', 'updateStatus(this); return(false);');
                    $frm->developerTags['colClassPrefix'] = 'col-sm-';
                    $frm->developerTags['fld_default_col'] = '10';
                    echo $frm->getFormHtml();
                    ?>
                </div>
            </div>	
        <?php } ?>
</div>