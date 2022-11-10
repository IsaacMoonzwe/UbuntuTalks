<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="rating-details">
    <div class="rating__count"><h1>4.5</h1></div>
    <div class="rating__info">
        <b>Overall Ratings</b>
        <p>120 Ratings & 115 Reviews</p>
    </div>
</div>
<div class="reviews-wrapper">
    <div class="reviews-wrapper__head">
        <p>Displaying Reviews 15 of 160</p>
        <div class="review__shorting">
            <select name="sort" id="sort">
                <option value="volvo">Sort by Newest</option>
                <option value="saab">Lorem</option>
                <option value="opel">Lorem</option>
                <option value="audi">Lorem</option>
            </select>
        </div>
    </div>
    <div class="reviews-wrapper__body">
        <?php foreach ($reviewsList as $review) { ?>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-sm-4">
                    <div class="review-profile">
                        <div class="avatar avatar-md" data-title="<?php echo CommonHelper::getFirstChar($review['lname']); ?>">
                            <?php if (true == User::isProfilePicUploaded($review['tlreview_postedby_user_id'])) { ?>
                                <img src="<?php CommonHelper::generateUrl('Image', 'user', array($review['tlreview_postedby_user_id'])) . '?' . time(); ?>" alt="">    
                            <?php } ?>
                        </div>
                        <div class="user-info">
                            <b><?php echo $review['lname']; ?></b>
                            <p><?php echo FatDate::format($review['tlreview_posted_on']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-sm-8">
                    <div class="review-content">
                        <div class="review-content__head">
                            <h6><?php echo $review['lessonLanguage']; ?><span><?php echo '(' . $review['lessonCount'] . Label::getLabel('LBL_Lessons', $siteLangId) . ')'; ?></span></h6>
                            <div class="info-wrapper">
                                <div class="info-tag ratings">
                                    <svg class="icon icon--rating"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#rating' ?>"></use></svg>
                                    <span class="value"><?php echo $review['prod_rating'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="review-content__body">
                            <p>  <?php echo nl2br($review['tlreview_description']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="reviews-wrapper__foot">
        <div class="show-more">
            <a  href="javascript:void(0);" class="btn btn--show"><?php echo Label::getLabel('Lbl_SHOW_MORE'); ?></a>
        </div>
    </div>
    <?php echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmSearchReviewsPaging')); ?>
</div>
