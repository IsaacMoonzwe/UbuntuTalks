<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box -padding-30 -skin">
    <div class="row justify-content-center -align-center">   
        <div class="col-sm-4"> 
            <div class="circle-count circle-count--danger"><?php echo $countIncorrect; ?></div>
            <p><?php echo Label::getLabel('LBL_Incorrect_Answers'); ?></p>
        </div>
        <div class="col-sm-4"> 
            <div class="circle-count circle-count--warning"><?php echo $countAlmostCorrect; ?></div>
            <p><?php echo Label::getLabel('LBL_Almost_Correct_Answers'); ?></p>
        </div>
        <div class="col-sm-4"> 
            <div class="circle-count circle-count--success"><?php echo $countCorrect; ?></div>
            <p><?php echo Label::getLabel('LBL_Correct_Answers'); ?></p>
        </div>
    </div>
    <hr>
    <span class="-gap -hide-mobile"></span>
    <div class="row row--actions justify-content-center">
        <div class="col-4"><a href="javascript:void(0)" onclick="$.facebox.close();" class="btn btn--secondary btn--block"><?php echo Label::getLabel('LBL_Close'); ?></a></div>
        <div class="col-4"><a href="javascript:void(0)" onclick="reviewFlashCard()" class="btn btn--primary btn--block"><?php echo Label::getLabel('LBL_Restart'); ?></a></div>
    </div>
</div>