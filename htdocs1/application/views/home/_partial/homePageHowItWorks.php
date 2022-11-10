<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$hWorksdata = isset($banners['BLOCK_HOW_IT_WORKS']) ? $banners['BLOCK_HOW_IT_WORKS'] : '';

?>

<?php if (!empty($hWorksdata)) { ?>
    <section class="section section--step">
        <div class="container container--narrow">
            <div class="section__head">
                <h2><?php echo $hWorksdata['blocation_name']; ?></h2>
            </div>
            <div class="section__body">
                <div class="step-wrapper">
                    <div class="step-container__head" style="height: 100px">
                        <div class="step-tabs slider-tabs--js">
                            <?php foreach (array_column($hWorksdata['banners'], 'banner_title') as $bannerTitle) { ?>
                                <div>
                                    <button class="slider-tabs__action">
                                        <span class="slider-tabs__label"><?php echo $bannerTitle; ?></span>
                                    </button>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                    <div class="step-container__body" style="height: 500px">
                        <div class="step-slider step-slider-js">
                            <?php
                            $i = 1;
                            foreach ($hWorksdata['banners'] as $banners) {
                            ?>
                                <div>
                                    <div class="step">
                                        <div class="row ">
                                            <div class="col-md-6 col-lg-5 col-xl-6">
                                                <div class="step__inner">
                                                    <div class="step__media">
                                                        <img src="<?php echo CommonHelper::generateUrl('Image', 'showBanner', array($banners['banner_id'], 0, BannerLocation::BLOCK_HOW_IT_WORKS)); ?>" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-7 col-xl-6">
                                                <div class="step__content">
                                                    <h3><?php echo $banners['banner_title']; ?></h3>
                                                    <p><?php echo $banners['banner_description']; ?></p>

                                                    <div class="step__actions">
                                                        <?php if ($banners['banner_btn_url']) { ?>
                                                            <a href="<?php echo CommonHelper::getBannerUrl($banners['banner_btn_url']); ?>" class="btn btn--primary"><?php echo $banners['banner_btn_caption']; ?></a>
                                                        <?php } ?>
                                                        <?php if ($banners['banner_video_url']) { ?>
                                                            <a href="<?php echo $banners['banner_video_url']; ?>" target="_blank" class="btn-video">
                                                                <div class="icon-play"></div>
                                                                <span><?php echo $banners['banner_video_caption']; ?></span>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                $i++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>