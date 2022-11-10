<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<footer class="footer">
	<div class="section-copyright">
		<div class="container container--narrow">
			<div class="copyright">
				<p>
					<?php
					if (CommonHelper::demoUrl()) {
						echo CommonHelper::replaceStringData(Label::getLabel('LBL_COPYRIGHT_TEXT', CommonHelper::getLangId()), ['{YEAR}' => '&copy; ' . date("Y"), '{PRODUCT}' => '<a target="_blank"  href="https://yo-coach.com">Yo!Coach</a>', '{OWNER}' => '<a target="_blank"  class="underline color-primary" href="https://www.fatbit.com/">FATbit Technologies</a>']);
					} else {
						echo Label::getLabel('LBL_COPYRIGHT', CommonHelper::getLangId()) . ' &copy; ' . date("Y ") . FatApp::getConfig("CONF_WEBSITE_NAME_" . CommonHelper::getLangId(), FatUtility::VAR_STRING);
					}
					?>
				</p>
			</div>
		</div>
	</div>
</footer>
<script>
	$(document).on('click', '.btn-Back', function() {
		var blockId = parseInt($('.is-process').attr('data-blocks-show')) - 1;
		$('.change-block-js').removeClass('is-process');
		$('li[data-blocks-show="' + blockId + '"]').addClass('is-process');
		$('.page-block__body').hide();
		$('#block--' + blockId).show();
		return false;
	});
</script>