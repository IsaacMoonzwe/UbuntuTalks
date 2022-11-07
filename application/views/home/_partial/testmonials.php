<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if ($testmonialList) {?>
    <section class="section section--quote">
        <div class="container container--narrow">
            <div class="quote-slider">
                <div class="slider slider--quote slider-quote-js">
                    <?php foreach ($testmonialList as $testmonialDetail) {?>
                    <div>
                        <div class="slider__item">
                            <div class="quote">
                                <div class="quote__media">
                                    <img src="<?php echo CommonHelper::generateUrl('Image', 'testimonial', [$testmonialDetail['testimonial_id'], 0, 'MEDIUM']); ?>" alt="">
                                    <div class="quote__box">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="30.857" viewBox="0 0 36 30.857"><g transform="translate(0 -29.235)"><path d="M233.882,29.235V44.664h10.286a10.3,10.3,0,0,1-10.286,10.286v5.143a15.445,15.445,0,0,0,15.429-15.429V29.235Z" transform="translate(-213.311)"/><path d="M0,44.664H10.286A10.3,10.3,0,0,1,0,54.949v5.143A15.445,15.445,0,0,0,15.429,44.664V29.235H0Z" transform="translate(0 0)"/></g></svg>
                                    </div>
                                </div>
                                <div class="quote__content">
                                    <p><?php echo $testmonialDetail['testimonial_text']; ?></p>
                                    <div class="quote-info">
                                        <h4><?php echo $testmonialDetail['testimonial_user_name']; ?></h4>
                                    </div>
                                    <div class="quote__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="138" height="118.286" viewBox="0 0 138 118.286"><g transform="translate(0 -29.235)"><path d="M233.882,29.235V88.378H273.31a39.474,39.474,0,0,1-39.429,39.429v19.714a59.208,59.208,0,0,0,59.143-59.143V29.235Z" transform="translate(-155.025 0)"/><path class="b" d="M0,88.378H39.429A39.474,39.474,0,0,1,0,127.806v19.714A59.208,59.208,0,0,0,59.143,88.378V29.235H0Z" transform="translate(0 0)"/></g></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </section>

<?php }
