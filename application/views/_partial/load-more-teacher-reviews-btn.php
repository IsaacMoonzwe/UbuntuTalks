<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$nextPage = $page + 1;
if ($nextPage <= $pageCount) {
    ?>
    <a id="loadMoreBtn" href="javascript:void(0)" onClick="goToLoadMoreReviews(<?php echo $nextPage; ?>);" class=" btn btn--primary"><?php echo Label::getLabel('LBL_Load_More'); ?></a>
    <?php
}
?>
