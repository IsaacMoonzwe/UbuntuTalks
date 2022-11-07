<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--gray">
    <div class="container container--narrow">
        <div class="error">
            <div class="row">
                <div class="col-md-5 col-xl-4">
                    <div class="error__media">
                        <img src="<?php echo CONF_WEBROOT_URL; ?>images/404.png">
                    </div>
                </div>
                <div class="col-md-7 col-xl-8">
                    <div class="error__content align-left margin-bottom-5">
                        <h3>Sorry! The page cannot be found.</h3>
                        <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. Please try the following::</p>
                        <ul class="list-group list-group--line">
                            <li class="list-group--item">Make sure that the web address displayed is spelled and formatted correctly</li>
                            <li class="list-group--item">If you reached here by clicking a link, let us know that the link is incorrect</li>
                            <li class="list-group--item">Whoops! Forget that this ever happened, and go find a tutor..</li>
                        </ul>
                        <a href="<?php echo CommonHelper::generateUrl(''); ?>" class="btn btn--primary"><?php echo Label::getLabel('MSG_Find_a_Tutor'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
