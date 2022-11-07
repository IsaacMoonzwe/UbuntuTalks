<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section padding-bottom-0">
    <div class="container container--fixed">
        <div class="intro-head">
            <h1 class="small-title"><?php echo strtoupper(Label::getLabel('LBL_FAQ', $siteLangId)); ?></h1>
            <h2><?php echo Label::getLabel('LBL_faq_title_second', $siteLangId) ?></h2>
        </div>
    </div>
</section>
<div class="panel-nav">
    <ul>
        <?php foreach ($typeArr as $catId => $catDetails) { ?>
            <li class="panel-nav__item <?php echo (reset($typeArr) == $catDetails) ? 'is--active' : '' ?>"><a href="javascript:void(0)" class="faq-panel-js" data-cat-id="<?php echo 'section_' . $catId ?>"><?php echo $catDetails; ?></a></li>
        <?php } ?>
    </ul>
</div>
<section class="section section--faq">
    <div class="container container--narrow">
        <div class="faq-cover">
            <div class="search-panel">
                <svg class="icon icon--search">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use>
                </svg>
                <input type="text" name="faq_search" placeholder="Enter a word or phrase that you're looking for help with">
            </div>
            <?php foreach ($finaldata as $catId => $faqDetails) { ?>
                <div id="<?php echo 'section_' . $catId ?>" <?php echo (array_keys($finaldata)[0] != $catId) ? 'style="display:none;"' : ''; ?> class="faq-container">
                    <?php foreach ($faqDetails as $ques) { ?>
                        <div class="faq-row faq-group-js">
                            <a href="javascript:void(0)" class="faq-title faq__trigger faq__trigger-js">
                                <h5><?php echo CommonHelper::htmlEntitiesDecode($ques['faq_title']); ?></h5>
                            </a>
                            <div class="faq-answer faq__target faq__target-js">
                                <p><?php echo CommonHelper::htmlEntitiesDecode($ques['faq_description']); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php $this->includeTemplate('_partial/contact-us-section.php', ['siteLangId' => $siteLangId]); ?>
<script>
    $(".settings__trigger-js").click(function () {
        var t = $(this).parents(".toggle-group").children(".settings__target-js").is(":hidden");
        $(".toggle-group .settings__target-js").hide();
        $(".toggle-group .settings__trigger-js").removeClass("is--active");
        if (t) {
            $(this).parents(".toggle-group").children(".settings__target-js").toggle().parents(".toggle-group").children(".settings__trigger-js").addClass("is--active")
        }
    });
    $(".faq__trigger-js").click(function (e) {
        e.preventDefault();
        if ($(this).parents('.faq-group-js').hasClass('is-active')) {
            $(this).siblings('.faq__target-js').slideUp();
            $('.faq-group-js').removeClass('is-active');
        } else {
            $('.faq-group-js').removeClass('is-active');
            $(this).parents('.faq-group-js').addClass('is-active');
            $('.faq__target-js').slideUp();
            $(this).siblings('.faq__target-js').slideDown();
        }
    });
    $(".faq-panel-js").click(function () {
        $(".faq-panel-js").parent().removeClass('is--active');
        $(".faq-container").hide();
        $(this).parent().addClass('is--active');
        $('#' + $(this).attr('data-cat-id')).show();
    });
    $(document).ready(function () {
        $('input[name="faq_search"]').keyup(function () {
            var text = $(this).val();
            $('.faq-row').hide();
            $('.faq-row:contains("' + text + '")').show();
        });
    });
</script>