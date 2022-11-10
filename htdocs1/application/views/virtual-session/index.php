<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
?>
<section class="section section--contect">
    <div class="container container--fixed container--narrow">
        <div class="section contact-form">
            <div class="container container--narrow">
                <div class="row">
                    <img src="<?php echo CommonHelper::generateUrl('Image', 'VirtualSessionCampaign', [$siteLangId]); ?>" alt="">
                </div>
                <div id="listing">
                    <?php
                        $this->includeTemplate('virtual-session/virtual-session-listing.php', ['siteLangId' => $siteLangId]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    //window.history.pushState("index", "Title",window.location.href.replace("index","category"));
</script>