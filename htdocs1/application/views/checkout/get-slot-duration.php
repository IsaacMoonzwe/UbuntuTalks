<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 ?>
<div class="box box--checkout">
    <div class="box__head">
        <a href="javascript:void(0);" onclick="cart.proceedToStep({teacherId: <?php echo $teacher['user_id']; ?>}, 'getUserTeachLangues');" class=" btn btn--bordered color-black btn--back">
            <svg class="icon icon--back">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK'); ?>
        </a>
        <h4>Select a Lesson Plan</h4>
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_4'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf">
		<p style="text-align:center;font-weight:normal;color:#000;"><?php echo Label::getLabel('LBL_SELECT_SLOTS_DURATION'); ?></p>
            <?php foreach ($slotDurations as $duration) { ?>
                <label class="selection-tabs__label">
                    <input type="radio" onchange="cart.props.lessonDuration = this.value;" class="selection-tabs__input" value="<?php echo $duration['ustelgpr_slot']; ?>" <?php echo ($lessonDuration == $duration['ustelgpr_slot']) ? 'checked' : ''; ?> name="lessonDuration">
                        <div class="selection-tabs__title" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <g>
                                    <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                </g>
                            </svg>
                            <span>
                                <?php 
                                    if($duration['ustelgpr_slot'] == 180){
                                        echo sprintf(Label::getLabel('LBL_%s_Mins_/_Crash_Course'), $duration['ustelgpr_slot']); 
                                    }
                                    else{
                                        echo sprintf(Label::getLabel('LBL_%s_Mins_/_Lesson'), $duration['ustelgpr_slot']); 
                                    }  
                                ?>
                            </span>
                        </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="box-foot">
        <div class="box-foot__left">
            <div class="teacher-profile">
                <div class="teacher__media">
                    <div class="avtar avtar-md">
                        <img src="<?php echo CommonHelper::generateUrl('Image', 'user', array($teacher['user_id'])) . '?' . time(); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?>">
                    </div>
                </div>
                <div class="teacher__name"><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></div>
            </div>
            <div class="step-breadcrumb">
                <ul>
                    <li><a href="javascript:void(0);"><?php echo $teachLangName; ?></a></li>
                </ul>
            </div>
        </div>
        <div class="box-foot__right">
            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="cart.proceedToStep({teacherId: <?php echo $teacher['user_id']; ?>, languageId: <?php echo $languageId; ?>}, 'getTeacherPriceSlabs');"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>
<script>
    cart.props.lessonDuration = parseInt('<?php echo $lessonDuration; ?>');
</script>