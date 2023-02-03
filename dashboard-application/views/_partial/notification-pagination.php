<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$pagination = '';
if ($pageCount <= 1) {
    return $pagination;
}
$linksToDisp = isset($linksToDisp) ? $linksToDisp : 2;
/* Current page number */
$pageNumber = $page;
$pageSize = (isset($pageSize)) ? $pageSize : FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
/* padArgListTo boolean(T/F) // where to pad argument list (left/right) */
$padArgToLeft = (isset($padArgToLeft)) ? $padArgToLeft : true;
/* On clicking page link which js function need to call */
$callBackJsFunc = isset($callBackJsFunc) ? $callBackJsFunc : 'goToSearchPage';
$callBackJsFunc = $callBackJsFunc . '(xxpagexx);';
$prevSvg = '<span class="svg-icon"><svg class="icon icon--messaging"><use xlink:href="' . CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#prev"></use></svg></span>';
$nextSvg = '<span class="svg-icon"><svg class="icon icon--messaging"><use xlink:href="' . CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#next"></use></svg></span>';
$prevBtnHtml = '<a class="is-prev" href="javascript:void(0);" onclick="' . $callBackJsFunc . '" title="' . Label::getLabel('LBL_Previous') . '">' . $prevSvg . '</a></li>';
$nextBtnHtml = '<a class="is-next"  href="javascript:void(0);" onclick="' . $callBackJsFunc . '" title="' . Label::getLabel('LBL_Next') . '">' . $nextSvg . '</a></li>';
$pagination .= FatUtility::getPageString('', $pageCount, $pageNumber, '', '', $linksToDisp, '', '', '<li>' . $prevBtnHtml . '</li>', '<li>' . $nextBtnHtml . '</li>');

$ul = new HtmlElement('ul', array('class' => 'controls margin-0',), $pagination, true);
$startIdx = (($pageNumber - 1) * $pageSize) + 1;
$to = ($startIdx + $pageSize) - 1;
$to = ($to > $recordCount) ? $recordCount : $to;
if ($pageNumber == 1 && $recordCount > $pageSize) {
    $ul->prependElement('li', ['class' => 'is-disabled'], $prevBtnHtml, true);
}
if ($to == $recordCount) {
    $ul->appendElement('li', ['class' => 'is-disabled'], $nextBtnHtml, true);
}
?>
<aside class="col-md-auto col-sm-5">
    <span class="-txt-normal"><?php echo $startIdx . ' ' . Label::getLabel('Lbl_to') . ' ' . $to . ' ' . Label::getLabel('LBL_of') . ' ' . $recordCount; ?></span>
    <?php echo $ul->getHtml(); ?>
</aside>