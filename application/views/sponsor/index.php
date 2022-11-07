<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
?>
<section class="section section--contect contact-page sponsor-information-page">
    <div class="container container--fixed container--narrow">
        <div class="section contact-form">
            <div class="container container--narrow">
                <div class="row">
                    <?php echo FatUtility::decodeHtmlEntities($SponsorContent); ?>
                </div>
                <div class="row donation-button">
                    <a href="javascript:void(0)" onclick="GetEventBecomeSponserPlan();" class="donation-sponosor-button">Sponsor Now</a>
                </div>
            </div>
        </div>
    </div>
</section>