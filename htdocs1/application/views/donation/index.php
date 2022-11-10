<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
?>
<section class="section section--contect contact-page donation-information-page">
    <div class="container container--fixed container--narrow">
        <div class="section contact-form">
            <div class="container container--narrow">
                <div class="row">
                    <?php echo FatUtility::decodeHtmlEntities($DonationContent); ?>
                </div>
                <div class="row donation-button">
                    <a href="javascript:void(0)" onclick="GetEventDonation();" class="donation-sponosor-button">Donate Now</a>
                </div>
            </div>
        </div>
    </div>
</section>