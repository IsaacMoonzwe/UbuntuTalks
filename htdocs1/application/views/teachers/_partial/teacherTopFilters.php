<?php defined('SYSTEM_INIT') or die('Invalid Usage.');

$frmTeacherSrch->setFormTagAttribute('onSubmit', 'searchTeachers(this); return(false);');
echo $frmTeacherSrch->getFormTag();
$languageName = $frmTeacherSrch->getField('teach_language_name');

$languageName->setFieldTagAttribute('class', 'filter__input filter__input-js');
$placeholder = Label::getLabel('LBL_Language_Placeholder', $siteLangId);

$teachLanguageValue = $languageName->value;
if (!empty($teachLanguageValue)) {
	$placeholder = $teachLanguageValue;
}

$languageName->setFieldTagAttribute('class', 'filter__input filter__input-js');

$teachLangIdFld = $frmTeacherSrch->getField('teachLangId');

$frmTeacherSrch->getField('teach_availability')->setFieldTagAttribute('class', 'form__input form__input-js');
$pageFld = $frmTeacherSrch->getField('page');
$frmTeacherSrch->getField('teach_availability')->setFieldTagAttribute('autocomplete', 'off');
$frmTeacherSrch->getField('teach_availability')->setFieldTagAttribute('readonly', 'readonly');
$keywordfld =   $frmTeacherSrch->getField('keyword');
$keywordfld->setFieldTagAttribute('class', 'form__input');
$keywordfld->setFieldTagAttribute('id', 'keyword');
$frmTeacherSrch->getField('btnTeacherSrchSubmit')->setFieldTagAttribute('class', 'form__action');
// echo "<pre>";
// print_r($priceArr);
?>
<div class="main__head">
	<div class="container container--narrow">


		<div class="filter-form">
			<div class="filter-header">
				<div class="row no-gutters">
					<div class="col-8">
						<h3> Filters</h3>
					</div>
					<div class="col-auto margin-left-auto">
						<button class="close close--filters-js"></button>
					</div>
				</div>
			</div>


			<div class="filter-primary">

				<div class="filter-row">

					<div class="filter-colum">
						<div class="filter">
							<div class="filter__trigger filter__trigger--arrow filter__trigger--large filter__trigger--outlined filter-trigger-js">
								<svg class="icon icon--language">
									<use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#language'; ?>"></use>
								</svg>
								<?php
								echo $languageName->getHtml();
								echo $pageFld->getHtml();
								echo $teachLangIdFld->getHTML();
								?>
							</div>
							<div class="filter__target filter-target-js" style="display: none;">
								<div class="listing-dropdown">
									<ul>
										<?php foreach ($teachLangs as $teachLangId => $teachLangName) { ?>
											<li <?php echo ($teachLangId == $teachLangIdFld->value) ? 'class="is--active"' : '' ?>><a href="javascript:void(0)" class="select-teach-lang-js" teachLangId="<?php echo $teachLangId; ?>"><?php echo $teachLangName; ?></a></li>
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
												<input type="checkbox" name="filterWeekDays[]" value="<?php echo $dayId; ?>" class="selection-tabs__input">
												<div class="selection-tabs__title"><span class="name"><?php echo $dayName; ?></span></div>
											</label>
										<?php } ?>
									</div>

									<div class="-gap"></div>

									<div class="availbility-title days"><?php echo Label::getLabel('LBL_Time_of_Days', $siteLangId) ?></div>
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
									<input type="hidden" value="<?php echo $maxPrice; ?>" name="filterDefaultMaxValue" id="filterDefaultMaxValue" />
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

					<div class="filter-colum filter-colum--large">
						<div class="filter">
							<div class="filter__trigger filter__trigger--large filter__trigger--outlined">
								<div class="filter-search">
									<input type="text" name="keyword" id="keyword" placeholder="<?php echo Label::getLabel('LBL_SEARCH_BY_NAME_AND_KEYWORD'); ?>">
									<svg id="btnTeacherSrchSubmit" class="icon icon--search">
										<use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use>
									</svg>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="filter-secondary">

				<div class="filter-row">


					<div class="filter-colum">
						<div class="filter">
							<div class="filter__trigger filter__trigger--arrow filter-trigger-js">
								<span class="filter__trigger-label"><?php echo Label::getLabel('LBL_Location', $siteLangId); ?></span>
							</div>

							<div class="filter__target filter-target-js" style="display: none;">
								<div class="listing-dropdown">
									<ul>
										<?php foreach ($fromArr as $countryId => $countryName) { ?>
											<li>
												<label id="location_<?php echo $countryName['user_country_id']; ?>"><span class="checkbox"><input type="checkbox" name="filterFromCountry[]" value="<?php echo $countryName['user_country_id']; ?>"><i class="input-helper"></i></span><span class="name"><?php echo $countryName['country_name']; ?></span></label>
											</li>
										<?php  } ?>

									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="filter-colum">
						<div class="filter">
							<div class="filter__trigger filter__trigger--arrow filter-trigger-js">
								<span class="filter__trigger-label"><?php echo Label::getLabel('LBL_Speaks', $siteLangId); ?></span>
							</div>
							<div class="filter__target filter-target-js" style="display: none;">
								<div class="listing-dropdown">
									<ul>
										<?php foreach ($spokenLangsArr as $spokenLangId => $spokenLangName) { ?>
											<li>
												<label id="spoken_<?php echo $spokenLangId; ?>"><span class=" checkbox"><input type="checkbox" name="filterSpokenLanguage[]" value="<?php echo $spokenLangId; ?>"><i class="input-helper"></i></span><span class="name"><?php echo $spokenLangName; ?></span></label>
											</li>
										<?php } ?>

									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="filter-colum">
						<div class="filter">
							<div class="filter__trigger filter__trigger--arrow filter-trigger-js">
								<span class="filter__trigger-label"><?php echo Label::getLabel('LBL_Gender', $siteLangId); ?></span>
							</div>
							<div class="filter__target filter-target-js" style="display: none;">
								<div class="listing-dropdown">
									<ul>
										<?php foreach ($genderArr as $genderId => $genderName) { ?>
											<li>
												<label id="gender_<?php echo $genderId; ?>"><span class="checkbox"><input type="checkbox" name="filterGender[]" value="<?php echo $genderId; ?>"><i class="input-helper"></i></span><span class="name"><?php echo $genderName; ?></span></label>
											</li>
										<?php } ?>

									</ul>
								</div>
							</div>
						</div>
					</div>

					<?php
					foreach ($preferenceTypeArr as $key => $preferenceType) {
						if (!isset($allPreferences[$key])) {
							continue;
						}
					?>
						<div class="filter-colum">
							<div class="filter">
								<div class="filter__trigger filter__trigger--arrow filter-trigger-js">
									<span class="filter__trigger-label"><?php echo $preferenceType; ?></span>
								</div>
								<div class="filter__target filter-target-js" style="display: none;">
									<div class="listing-dropdown">
										<ul>
											<?php foreach ($allPreferences[$key] as $preference) { ?>
												<li>
													<label id="prefrence_<?php echo $preference['preference_id']; ?>"><span class="checkbox"><input type="checkbox" name="filterPreferences[]" value="<?php echo $preference['preference_id']; ?>"><i class="input-helper"></i></span><span class="name"><?php echo $preference['preference_titles']; ?></span></label>
												</li>
											<?php } ?>

										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
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

</form>
<?php
echo $frmTeacherSrch->getExternalJS();
?>
<script>
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
			var range,
				min = <?php echo $filterDefaultMinValue; ?>,
				max = <?php echo $filterDefaultMaxValue; ?>,
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