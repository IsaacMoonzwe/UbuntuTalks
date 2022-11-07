<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="banner banner--main">
    <div class="banner__media"><img src="<?php echo CONF_WEBROOT_URL; ?>images/2000x900_4.jpg" alt=""></div>
    <div class="banner__content banner__content--centered">
        <h1>Get paid to help people</h1>
        <p>Earn money teaching your language online. Anytime. Anywhere.</p>
        <a href="javascript:void(0)" onClick="signUpFormPopUp('teacher');" class="btn btn--primary btn--large">Start Teaching</a>
    </div>
</section>
<section class="section section--white section--icons">
    <div class="container container--narrow">
        <div class="-align-center section__head">
            <h2>Why Teach on yoCoach?</h2>
        </div>
        <div class="section__body">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-12 col-md-12">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 icon-col">
                            <div class="icon"><img src="<?php echo CONF_WEBROOT_URL; ?>images/circle_icon_1.svg" alt=""></div>
                            <h4>Earn money</h4>
                            <p>Set your own hourly rates and cash out your earnings anytime.</p>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 icon-col">
                            <div class="icon"><img src="<?php echo CONF_WEBROOT_URL; ?>images/circle_icon_2.svg" alt=""></div>
                            <h4>Work anywhere</h4>
                            <p>Teach from home or any other convenient location of your choice.</p>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 icon-col">
                            <div class="icon"><img src="<?php echo CONF_WEBROOT_URL; ?>images/circle_icon_3.svg" alt=""></div>
                            <h4>Teach anytime</h4>
                            <p>Adjust your personal availability anytime on your calendar.</p>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 icon-col">
                            <div class="icon"><img src="<?php echo CONF_WEBROOT_URL; ?>images/circle_icon_3.svg" alt=""></div>
                            <h4>Safety and security</h4>
                            <p>Ensures that you get paid after you teach!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section--grey">
    <div class="container container--narrow">
        <div class="-align-center section__head">
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="section__body">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-9">
                    <div class="accordian-group">
                        <div class="accordian accordian-js is-active">
                            <div class="accordian__title accordian__title-js">What is yoCoach?</div>
                            <div class="accordian__body accordian__body-js" style="display: block;">
                                <p>yoCoach is an online language-learning platform that connects language learners with qualified language teachers for private sessions through live video chat.</p>
                            </div>
                        </div>
                        <div class="accordian accordian-js">
                            <div class="accordian__title accordian__title-js">Where do I teach?</div>
                            <div class="accordian__body accordian__body-js" style="display: none;">
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the standard dummy text of the industry since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularized in the 1960s with the release of Letraset</p>
                            </div>
                        </div>
                        <div class="accordian accordian-js">
                            <div class="accordian__title accordian__title-js">How do I apply to teach on yoCoach?</div>
                            <div class="accordian__body accordian__body-js" style="display: none;">
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the standard dummy text of the industry since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularized in the 1960s with the release of Letraset</p>
                            </div>
                        </div>
                        <div class="accordian accordian-js">
                            <div class="accordian__title accordian__title-js">Do I need teaching experience?</div>
                            <div class="accordian__body accordian__body-js" style="display: none;">
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the standard dummy text of the industry since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularized in the 1960s with the release of Letraset</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section--white">
    <div class="container container--narrow -align-center">
        <h2 class="-style-bold">Looking forward to meeting<br>  your new students?</h2>
        <span class="-gap"></span>
        <a href="javascript:void(0)" onClick="signUpFormPopUp('teacher');" class="btn btn--primary btn--large">Start Teaching</a>
    </div>
</section>
<script>
    /* for faq toggles */
    $(".accordian__body-js").hide();
    $(".accordian__body-js:first").show();
    $(".accordian__title-js").click(function () {
        if ($(this).parents('.accordian-js').hasClass('is-active')) {
            $(this).siblings('.accordian__body-js').slideUp();
            $('.accordian-js').removeClass('is-active');
        } else {
            $('.accordian-js').removeClass('is-active');
            $(this).parents('.accordian-js').addClass('is-active');
            $('.accordian__body-js').slideUp();
            $(this).siblings('.accordian__body-js').slideDown();
        }
    });
</script>