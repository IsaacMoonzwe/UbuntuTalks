<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$nextPage = $page + 1;
if ($nextPage <= $pageCount) {
    ?>
    <span class="span"><a id="loadMoreBtn" href="javascript:void(0)" onClick="goToLoadPrevious(<?php echo $nextPage; ?>);" class="loadmore box box--white"><i class="fa fa-history"></i>&nbsp;<?php echo Label::getLabel('LBL_Load_Previous', $siteLangId); ?></a></span>
    <?php
}