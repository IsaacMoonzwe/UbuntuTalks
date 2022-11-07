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
<section class="section--gray">
    <div class="main__body">
        <div class="container container--narrow">
            <div class="listing-cover" id="teachersListingContainer">
            </div>
        </div>
    </div>
</section>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
