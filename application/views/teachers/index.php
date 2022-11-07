<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<!--header section [-->
<?php
$minPrice = 0;
$maxPrice = 0;
$keyword = '';
$spokenLanguage_filter = array();
$preferenceFilter_filter = array();
$fromCountry_filter = array();
$gender_filter = array();
$filters = array();
/* Teacher Top Filters [ */
$this->includeTemplate('teachers/_partial/teacherTopFilters.php', ['frmTeacherSrch' => $frmTeacherSrch, 'daysArr' => $daysArr, 'timeSlotArr' => $timeSlotArr, 'minPrice' => $minPrice, 'maxPrice' => $maxPrice, 'keyword' => $keyword, 'siteLangId' => $siteLangId]);
/* ] */
?>
<style>.container--narrow img{margin-top:10px;}
.fc-direction-ltr{float:left;margin-left:10px;}
.hide-top-section{display:none !important;}
.display-section{display:block !important;}

</style>

<?php //if($_SESSION['show_Process'] == 1){ ?>
    <section class="section process-page teacherslist">
        <div class="container container--narrow">
            <div class="main__title">
                <?php echo FatUtility::decodeHtmlEntities($teacherBanner); ?>
            </div>
            <div class="who-we__content">
                <?php echo FatUtility::decodeHtmlEntities($processPage); ?>
            </div>
        </div>
    </section>
<?php //} ?>

<!-- <section class="section--gray" style="background: #fff !important; "> -->
<section class="section--gray" style="">
    <div class="main__body" id="teachers-lists">
        <div class="container container--narrow" style="background: #FFEBCD;">
            <h2 style="display: none;">Teachers</h2>
            <div class="listing-cover" id="teachersListingContainer">
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function(){
        jQuery(document).on('click','.is-prev,.is-backward,.is-next,.is-forward',function(){
                setTimeout(function(){
                var page = jQuery('.pagination').find('button.is-active').text();
                if(page == 1) {
                  $("section.section.process-page.teacherslist").removeClass("hide-top-section");
                    $("section.section.process-page.teacherslist").addClass("display-section");
                } else {
                    $("section.section.process-page.teacherslist").removeClass("display-section");
                    $("section.section.process-page.teacherslist").addClass("hide-top-section");
                }
                }, 1000);
            });
            jQuery(document).on('click','.pagination button',function(){
                var page = $(this).text();
                if(page == 1) {
                $("section.section.process-page.teacherslist").removeClass("hide-top-section");
                    $("section.section.process-page.teacherslist").addClass("display-section");
                } else {
                    $("section.section.process-page.teacherslist").removeClass("display-section");
                    $("section.section.process-page.teacherslist").addClass("hide-top-section");
                }
            });
    });
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
