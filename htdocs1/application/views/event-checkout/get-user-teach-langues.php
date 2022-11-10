<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box box--checkout">
    <div class="box__head">
        <h4><?php echo Label::getLabel('LBL_SELECT_YOUR_LANGUAGE'); ?></h4>
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-process"><a href="#"><?php echo Label::getLabel('LBL_1'); ?></a></li>
                <li class="step-nav_item"><a href="#"><?php echo Label::getLabel('LBL_2'); ?></a></li>
                <li class="step-nav_item"><a href="#"><?php echo Label::getLabel('LBL_3'); ?></a></li>
                <li class="step-nav_item"><a href="#"><?php echo Label::getLabel('LBL_4'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--language selection--onehalf">
            <?php foreach ($teachLanguages as $language) { ?>
                <label class="selection-tabs__label">
                    <input type="radio" onchange="cart.props.languageId = this.value;" class="selection-tabs__input" value="<?php echo $language['tlanguage_id']; ?>" <?php echo ($languageId == $language['tlanguage_id']) ? 'checked' : ''; ?> name="languageId">
                        <div class="selection-tabs__title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <g>
                                    <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                </g>
                            </svg>
                            <?php echo $language['tlanguage_name']; ?>
                        </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="box-foot">
        <div class="teacher-profile">
            <div class="teacher__media">
                <div class="avtar avtar-md">
                    <img src="<?php echo CommonHelper::generateUrl('Image', 'user', array($teacher['user_id'])) . '?' . time(); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name'] ?>">
                </div>
            </div>
            <div class="teacher__name"><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></div>
        </div>
        <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="cart.proceedToStep({teacherId: <?php echo $teacher['user_id']; ?>}, 'getSlotDuration');"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
    </div>
</div>
<script>
    cart.props.languageId = parseInt('<?php echo $languageId; ?>');
</script>