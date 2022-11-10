<?php
session_start();
defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$frmSrch->setFormTagAttribute('onSubmit', 'search(this); return(false);');
$frmSrch->setFormTagAttribute('class', 'group-class-search');
$frmSrch->setFormTagAttribute('id', 'group-class-search');
$frmSrch->getField('btnSrchSubmit')->setFieldTagAttribute('class', 'form__action');
$keywordField = $frmSrch->getField('keyword');
$keywordField->setFieldTagAttribute('class', 'keyword-search');
$keywordField->setFieldTagAttribute('id', 'keyword');
$keywordField->setFieldTagAttribute('placeholder', Label::getLabel('LBL_Search_By_Name_and_Keyword...'));
$language = $frmSrch->getField('language');
$language->setFieldTagAttribute('class', 'd-none');
$language->setFieldTagAttribute('id', 'language');
$kids_filters=$_SESSION['search_filters_kids'];
$search_language=$kids_filters['language'];

$teachLanguageValue = $language->value;
if (!empty($teachLanguageValue)) {
	$placeholder = $teachLanguageValue;
}
$teachLangIdFld = $frmSrch->getField('teachLangId');

   ?>
    <?php
        $minPrice = 0;
        $maxPrice = 0;
        $keyword = '';
        $spokenLanguage_filter = array();
        $preferenceFilter_filter = array();
        $fromCountry_filter = array();
        $gender_filter = array();
        $filters = array();
      	$filterDefaultMinValue = ($priceArr['minPrice']) ?? 0;
      	$filterDefaultMaxValue = ($priceArr['maxPrice']) ?? 0;
		  if($minPrice<=0){
				$minPrice=$priceArr['minPrice'];
		  }
		  if($maxPrice<=0){
			$maxPrice=$priceArr['maxPrice'];
		  }
      	// $this->includeTemplate('kids-classes/_partial/teacherTopFilters.php', ['frmTeacherSrch' => $frmTeacherSrch, 'daysArr' => $daysArr, 'timeSlotArr' => $timeSlotArr, 'minPrice' => $minPrice, 'maxPrice' => $maxPrice, 'keyword' => $keyword, 'siteLangId' => $siteLangId]);
    ?>
<!-- [ MAIN BODY ========= -->
<style>
     /* .main__head { display: none; } */
	 .main__head{border-bottom:0px !important;padding: 0px !important;}
    .group-class-search .filter__target { width: auto !important; }
	.sorting__head{justify-content: center !important;}
</style>
<div class="banner__media" style="margin-bottom:50px;">
    <img src="<?php echo CommonHelper::generateUrl('Image', 'kids', [$siteLangId]); ?>" alt="">
	<?php echo FatUtility::decodeHtmlEntities($kidsBanner); ?>
	<div class="main__head">
        <div class="container container--narrow">
            <?php
            echo $frmSrch->getFormTag();
            echo $language->getHTML();
			echo $teachLangIdFld->getHTML();
            ?>
            <div class="filter-primary">
                <div class="filter-row">
                    <div class="filter-colum">
                        <div class="filter">
                            <div class="filter__trigger filter__trigger--arrow filter__trigger--large filter__trigger--outlined filter-trigger-js">
                                <svg class="icon icon--language">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#language'; ?>"></use>
                                </svg>
                                <input class="filter__input filter__input-js languages_input" readonly id="teachLang" type="text" name="teachLang" placeholder="<?php echo $language->selectCaption; ?>">
                            </div>
                            <div class="filter__target filter-target-js" style="display: none;">
                                <div class="dropdown-listing languages">
                                    <ul>
                                        <li class="is--active"><a href="javascript:void(0);" class="select-teach-lang-js" data-id=""><?php echo $language->selectCaption; ?></a></li>
                                        <?php foreach ($language->options as $key => $value) { ?>
                                            <li><a href="javascript:void(0);" class="select-teach-lang-js" data-id="<?php echo $key; ?>"><?php echo $value; ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filter-colum">
						<div class="filter">
							<div class="filter__trigger filter__trigger--arrow filter__trigger--large filter__trigger--outlined filter-trigger-js">
								<svg class="icon icon--availbility">
									<use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#availability'; ?>"></use>
								</svg>
								<span class="filter__trigger-label"><?php echo Label::getLabel("LBL_Availability", $siteLangId) ?></span>
							</div>
							<div class="filter__target filter-target-js" style="display: none;">
								<div class="dropdown-availbility">
									<div class="availbility-title"><?php echo Label::getLabel('LBL_Days_Of_Week', $siteLangId); ?></div>
									<div class="selection-tabs selection--weeks">
										<?php foreach ($daysArr as $dayId => $dayName) { ?>
											<label class="selection-tabs__label" id="day_<?php echo $dayId; ?>">
												<input type="checkbox" name="filterWeekDays[]" value="<?php echo $dayName; ?>" class="selection-tabs__input">
												<div class="selection-tabs__title"><span class="name"><?php echo $dayName; ?></span></div>
											</label>
										<?php } ?>
									</div>

									<div class="-gap"></div>

									<div class="availbility-title days" style="display:none"><?php echo Label::getLabel('LBL_Time_of_Days', $siteLangId) ?></div>
									<div class="selection-tabs selection--days">
										<?php foreach ($timeSlotArr as $timeSlotId => $timeSlotName) { ?>
											<label class="selection-tabs__label" id="slot_<?php echo $timeSlotId; ?>">
												<input type="checkbox" name="filterTimeSlots[]" value="<?php echo $timeSlotId; ?>" class="selection-tabs__input">
												<div class="selection-tabs__title"><span class="name"><?php echo $timeSlotName; ?></span></div>
											</label>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

					</div>
                    <div class="filter-colum">
						<div class="filter">
							<div class="filter__trigger filter__trigger--arrow filter__trigger--large filter__trigger--outlined filter-trigger-js">
								<svg class="icon icon--price-tag">
									<use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#price-tag' ?>"></use>
								</svg>
								<span class="filter__trigger-label"><?php echo Label::getLabel('LBL_Price', $siteLangId); ?></span>
							</div>
							<div class="filter__target filter-target-js" style="display: none;">
								<div class="dropdown-price">
									<input type="text" id="price_range" value="<?php echo $minPrice; ?>-<?php echo $maxPrice; ?>" name="price_range" />
									<input type="hidden" value="<?php echo $minPrice; ?>" name="filterDefaultMinValue" id="filterDefaultMinValue" />
									<input type="hidden" value="<?php  echo $maxPrice; ?>" name="filterDefaultMaxValue" id="filterDefaultMaxValue" />
									<div class="price-field">
										<div class="input-field">
											<span><?php echo CommonHelper::getCurrencySymbolRight() ? CommonHelper::getCurrencySymbolRight() : CommonHelper::getCurrencySymbolLeft(); ?></span>
											<input type="number" name="priceFilterMinValue" value="<?php echo $priceArr['minPrice']; ?>">
										</div>
										<div class="input-field">
											<span><?php echo CommonHelper::getCurrencySymbolRight() ? CommonHelper::getCurrencySymbolRight() : CommonHelper::getCurrencySymbolLeft(); ?></span>
											<input type="number" name="priceFilterMaxValue" value="<?php echo $priceArr['maxPrice']; ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="filter-colum ages">
                        <div class="filter">
                            <div class="filter__trigger filter__trigger--arrow filter__trigger--large filter__trigger--outlined filter-trigger-js">
                                
								<svg class="ages-icon" xmlns="http://www.w3.org/2000/svg" height="22" width="22" viewBox="0 0 640 512" fill="#555" style="opacity: 0.5"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M224 256c70.7 0 128-57.31 128-128S294.7 0 224 0C153.3 0 96 57.31 96 128S153.3 256 224 256zM274.7 304H173.3c-95.73 0-173.3 77.6-173.3 173.3C0 496.5 15.52 512 34.66 512H413.3C432.5 512 448 496.5 448 477.3C448 381.6 370.4 304 274.7 304zM479.1 320h-73.85C451.2 357.7 480 414.1 480 477.3C480 490.1 476.2 501.9 470 512h138C625.7 512 640 497.6 640 479.1C640 391.6 568.4 320 479.1 320zM432 256C493.9 256 544 205.9 544 144S493.9 32 432 32c-25.11 0-48.04 8.555-66.72 22.51C376.8 76.63 384 101.4 384 128c0 35.52-11.93 68.14-31.59 94.71C372.7 243.2 400.8 256 432 256z"/></svg>
                                <input class="filter__input filter__input-js ages_filter" readonly id="techAges" type="text" name="techAges" placeholder="<?php echo "All Ages" ?>">
                            </div>
                            <div class="filter__target filter-target-js" style="display: none;">
                                <div class="dropdown-listing ages">
                                    <ul>
                                        <li class="is--active"><a href="javascript:void(0);" class="ages_group" data-id=""><?php echo "All Ages" ?></a></li>
                                        <?php foreach ($groupList as $key => $value) { ?>
                                            <li><a href="javascript:void(0);" class="ages_group" data-id="<?php echo $value['grpcls_id']; ?>"><?php echo $value['grpcls_ages']; ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filter-colum filter-colum--large largetextbox">
                        <div class="filter">
                            <div class="filter__trigger filter__trigger--large filter__trigger--outlined">
                                <div class="filter-search">
                                    <?php echo $keywordField->getHTML(); ?>
                                    <svg class="icon icon--search search-group-class-js" >
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<?php $filterApply = (!empty($teachLanguageValue)); ?>
<div class="filter-tags <?php echo (!$filterApply) ? 'd-none' : ''; ?>">
	<div class="container container--narrow">
		<div class="filter-tags-list" id="searched-filters">
			<ul>
				<li class="clear-filter <?php echo (!$filterApply) ? 'd-none' : ''; ?>"><a href="javascript:void(0);" onclick="removeAllFilters();"><?php echo Label::getLabel('LBL_Clear_All_Filters', $siteLangId); ?></a></li>
				<?php if ($filterApply) { ?>
					<li class="filter-li-js">
						<a href="javascript:void(0);" class="language_keyword tag__clickable " onclick="removeFilterCustom('language_keyword',this)">
							<?php echo Label::getLabel('LBL_Language'); ?> : <?php echo $teachLanguageValue; ?> </a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>

        </form>
    </div>
</div>
</div>
<section class="section--gray">

    <div class="main__body">
        <div class="container container--narrow">
        <!-- <div class="contacttitleseccls"><h1 style="font-size: 18px;text-align: center;font-weight: 400;color: #333;">To request a personalized group class, please email us at <a href="mailto:smce@ubuntutalks.com">smce@ubuntutalks.com</a></h1></div> -->
    </div>
        <div class="container container--narrow" id="listingContainer">
        </div>
    </div>
</section>
<?php
echo $frmSrch->getExternalJS();
?>
<!-- ] -->
<script>

</script>
<script>
    function htmlEncode(value) {
    return $('<div/>').text(value).html();
}


	$(document).ready(function() {
     

		$('.block__head-trigger-js').click(function() {
			if ($(this).hasClass('is-active')) {
				$(this).removeClass('is-active');
				$(this).siblings('.block__body-target-js').slideUp();
				return false;
			}

			$(this).find('.block__head-trigger-js').removeClass('is-active');
			$(this).addClass("is-active");
			$(this).siblings('.block__body-target-js').slideUp();
			$(this).siblings('.block__body-target-js').slideDown();
		});


		$('.scrollbar-js').enscroll({
			verticalTrackClass: 'scrollbar-track',
			verticalHandleClass: 'scrollbar-handle'
		});

		<?php if (isset($priceArr) && $priceArr) { ?>
			var range;
			var	min = <?php echo $filterDefaultMinValue; ?>;
			var	max =<?php echo $filterDefaultMaxValue; ?>,
				from,
				to;
			var $from = $('input[name="priceFilterMinValue"]');
			var $to = $('input[name="priceFilterMaxValue"]');
			var $range = $("#price_range");
			var updateValues = function() {
				$from.prop("value", from);
				$to.prop("value", to);
			};
			step = 2;
			if (0 < min && 1 > min) {
				step = 0.02;
			}

			$("#price_range").ionRangeSlider({
				hide_min_max: true,
				hide_from_to: true,
				keyboard: true,
				min: min,
				max: max,
				from: <?php echo $minPrice;  ?>,
                to: <?php echo $maxPrice; ?>,
				step: step,
				type: 'double',
				prettify_enabled: true,
				prettify_separator: ',',
				grid: true,
				prefix: '<?php echo $currencySymbolLeft; ?>',
				postfix: '<?php echo $currencySymbolRight; ?>',
				input_values_separator: '-',
				onFinish: function() {
					var minMaxArr = $("#price_range").val().split('-');
					if (minMaxArr.length == 2) {
						var min = Number(minMaxArr[0]);
						var max = Number(minMaxArr[1]);
						$('input[name="priceFilterMinValue"]').val(min);
						$('input[name="priceFilterMaxValue"]').val(max);
						return addPricefilter();
						//return searchProducts(document.frmProductSearch);
					}

				},
				onChange: function(data) {
					from = data.from;
					to = data.to;
					updateValues();
				}
			});


			range = $range.data("ionRangeSlider");

			var updateRange = function() {
				range.update({
					from: from,
					to: to
				});
				addPricefilter();
			};

			$from.on("change", function() {
				from = $(this).prop("value");
				if (!$.isNumeric(from)) {
					from = 0;
				}
				if (from < min) {
					from = min;
				}
				if (from > max) {
					from = max;
				}

				updateValues();
				updateRange();
			});

			$to.on("change", function() {
				to = $(this).prop("value");
				if (!$.isNumeric(to)) {
					to = 0;
				}
				if (to > max) {
					to = max;
				}
				if (to < min) {
					to = min;
				}
				updateValues();
				updateRange();
			});
		<?php } ?>
	});
</script>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>