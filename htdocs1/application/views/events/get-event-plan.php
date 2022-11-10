<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 ?>
<div class="box box--checkout">
    <div class="box__head">
       
        <h4>Select Plan</h4>
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf">
		<p style="text-align:center;font-weight:normal;color:#000;"><?php echo Label::getLabel('LBL_SELECT_PLAN'); ?></p>
        <form id="Plan" name="plan">
            <?php foreach ($slotDurations as $duration) { ?>
                <label class="selection-tabs__label">
                    <input type="radio" onchange="cart.props.lessonDuration = this.value;" class="selection-tabs__input" value="<?php echo $duration['registration_plan_title']; ?>" <?php echo ($lessonDuration == $duration['registration_plan_title']) ? 'checked' : ''; ?> name="lessonDuration">
                        <div class="selection-tabs__title" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <g>
                                    <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                </g>
                            </svg>
                            <span>
                                <?php 
                                        echo sprintf(Label::getLabel('LBL_%s_Mins_/_Lesson'), $duration['registration_plan_title']); 
                                ?>
                            </span>
                        </div>
                </label>
            <?php } ?>
        </form>
        </div>
    </div>
    <div class="box-foot" style="display: none;">
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