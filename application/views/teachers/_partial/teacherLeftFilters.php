<?php defined('SYSTEM_INIT') or die('Invalid Usage.');
if (empty($minPrice) && empty($maxPrice)) {
	$minPrice = round($filterDefaultMinValue,2);
	$maxPrice = round($filterDefaultMaxValue,2);
	// $maxPrice = $filterDefaultMaxValue;
}

?>
<div class="col-xl-3 col-lg-12 -float-left">
	<div class="tabled-box">
        <ul>
            <li>
                <div class="sort-by">
					<?php echo $frmFilters->getFieldHtml('filterSortBy'); ?>
                </div>
            </li>
            <li class="-hide-desktop -show-responsive">
				<a href="javascript:void(0)" class="btn btn--bordered btn--block btn--large btn--filters-js">
					<span class="svg-icon">
						<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
						width="15px" height="15px" viewBox="0 0 402.577 402.577" style="enable-background:new 0 0 402.577 402.577;"
						xml:space="preserve">
						<g>
						<path d="M400.858,11.427c-3.241-7.421-8.85-11.132-16.854-11.136H18.564c-7.993,0-13.61,3.715-16.846,11.136
						c-3.234,7.801-1.903,14.467,3.999,19.985l140.757,140.753v138.755c0,4.955,1.809,9.232,5.424,12.854l73.085,73.083
						c3.429,3.614,7.71,5.428,12.851,5.428c2.282,0,4.66-0.479,7.135-1.43c7.426-3.238,11.14-8.851,11.14-16.845V172.166L396.861,31.413
						C402.765,25.895,404.093,19.231,400.858,11.427z"/>
						</g>
						</svg>
					</span> <?php echo Label::getLabel('LBL_Filters'); ?>
				</a>
            </li>
        </ul>
    </div>

  
    <div class="filters">
        <div class="box">
            <div class="box__head d-flex justify-content-between align-items-center">
                <h4><?php echo Label::getLabel('LBL_Filters'); ?></h4>
                <?php /* <a href="javascript:void(0)" class="-link-underline"><?php echo Label::getLabel('LBL_Open_all'); ?></a> */ ?>
            </div>
            <div class="box__body">

                <div class="block-container">

					<?php if( isset($spokenLangsArr) && $spokenLangsArr ){ ?>
					<div class="block">
						<div class="block__head block__head-trigger block__head-trigger-js is-active">
							<h6><?php echo Label::getLabel('LBL_Teacher_Speaks'); ?></h6>
						</div>
						<div class="block__body block__body-target block__body-target-js" style="display: block;">
							<div class="scrollbar scrollbar-js">
								<div class="listing listing--vertical">
									<?php //echo $frmFilters->getFieldHtml('filterSpokenLanguage'); ?>
									<ul>
										<?php foreach($spokenLangsArr as $spokenLangId => $spokenLangName ){ ?>
										<li>
											<label class="checkbox" id="spokenLanguage_<?php echo $spokenLangId; ?>">
												<input type="checkbox" name="filterSpokenLanguage[]" value="<?php echo $spokenLangId; ?>" <?php if( in_array($spokenLangId, $spokenLanguage_filter )){ echo 'checked'; }  ?>  >
												<i class="input-helper"></i> <?php echo $spokenLangName; ?>
											</label>
										</li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>

					<?php if( isset($fromArr) && !empty($fromArr) ){ ?>
                    <div class="block">
                        <div class="block__head block__head-trigger block__head-trigger-js">
                            <h6><?php echo Label::getLabel('LBL_From'); ?></h6>
                        </div>
                        <div class="block__body block__body-target block__body-target-js" style="display: none;">
                            <div class="scrollbar scrollbar-js">
                                <div class="listing listing--vertical">
                                    <ul>
										<?php foreach($fromArr as $from){ ?>
                                        <li>
                                            <label class="checkbox" id="fromcountry_<?php echo $from['user_country_id'] ?>">
                                                <input type="checkbox" name="filterFromCountry[]" value="<?php echo $from['user_country_id'] ?>" <?php if( in_array($from['user_country_id'], $fromCountry_filter )){ echo 'checked'; }  ?> >
                                                <i class="input-helper"></i> <?php echo $from['country_name']; ?>
                                            </label>
                                        </li>
										<?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php } ?>
					<?php if( isset($priceArr) && $priceArr ){ ?>
					<div class="block">
						<div class="block__head block__head-trigger block__head-trigger-js is-active"><h6><?php echo Label::getLabel( 'LBL_Price' ); ?></h6></div>
						<div class="block__body block__body-target block__body-target-js" style="">
							<div class="range range--primary">
								<input type="text" id="price_range" value="<?php echo $minPrice; ?>-<?php echo $maxPrice; ?>" name="price_range" />
								<input type="hidden" value="<?php echo $minPrice; ?>" name="filterDefaultMinValue" id="filterDefaultMinValue" />
								<input type="hidden" value="<?php echo $maxPrice; ?>" name="filterDefaultMaxValue" id="filterDefaultMaxValue" />
							</div>
							<div class="slide__fields form">
								<ul>
									<li><span class="rsText"><?php echo CommonHelper::getCurrencySymbolRight()?CommonHelper::getCurrencySymbolRight():CommonHelper::getCurrencySymbolLeft();?></span><input value="<?php echo $minPrice; ?>" name="priceFilterMinValue" type="text"></li>


									<li><span class="rsText"><?php echo CommonHelper::getCurrencySymbolRight()?CommonHelper::getCurrencySymbolRight():CommonHelper::getCurrencySymbolLeft(); ?></span><input value="<?php echo $maxPrice; ?>" class="input-filter form-control " name="priceFilterMaxValue" type="text">
									</li>
								</ul>
							</div>
						</div>
					</div>
					<?php } ?>
					<?php $preferenceTypeArr = Preference::getPreferenceTypeArr( CommonHelper::getLangId() );
					foreach ($preferenceTypeArr as $key=>$preferenceType) {
						if(!isset($allPreferences[$key])){
							continue;
						}
					 ?>
						<div class="block">
                        <div class="block__head block__head-trigger block__head-trigger-js">
                            <h6><?php echo $preferenceType; ?></h6>
                        </div>
                        <div class="block__body block__body-target block__body-target-js" style="display: none;">
                            <div class="scrollbar scrollbar-js">
                                <div class="listing listing--vertical">
                                    <ul>
									<?php foreach($allPreferences[$key] as $preference){ ?>
                                        <li>
											<label class="checkbox" id="skill_<?php echo $preference['preference_id']; ?>">
												<input type="checkbox" name="filterPreferences[]" value="<?php echo $preference['preference_id']; ?>" <?php if( in_array($preference['preference_id'], $preferenceFilter_filter )){ echo 'checked'; }  ?> >
												<i class="input-helper"></i> <?php echo $preference['preference_titles']; ?>
											</label>
                                        </li>
									<?php } ?>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php } ?>
					

					<?php
					if( isset($genderArr) && !empty($genderArr) ){ ?>
                    <div class="block">
                        <div class="block__head block__head-trigger block__head-trigger-js">
                            <h6><?php echo Label::getLabel('LBL_Gender'); ?></h6>
                        </div>
                        <div class="block__body block__body-target block__body-target-js" style="display: none;">
                            <div class="scrollbar scrollbar-js">
                                <div class="listing listing--vertical">
                                    <ul>
										<?php
										//$genderConstants = User::getGenderArr();
										foreach( $genderArr as $k => $gender ){
										?>
                                        <li>
                                            <label class="checkbox" id="gender_<?php echo $k; ?>">
                                                <input type="checkbox" name="filterGender[]" value="<?php echo $k; ?>" <?php if( in_array($k, $gender_filter )){ echo 'checked'; }  ?> >
                                                <i class="input-helper"></i> <?php echo $gender; ?>
                                            </label>
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
	<div class="box box--cta -padding-30 -align-center d-none d-xl-block">
		<h4 class="-text-bold"><?php echo Label::getLabel('LBL_Want_to_be_a_teacher?'); ?></h4>
		<p><?php $str = Label::getLabel( 'LBL_If_you\'re_interested_in_being_a_teacher_on_{sitename},_please_apply_here.' );
		$siteName = FatApp::getConfig( 'CONF_WEBSITE_NAME_'.$siteLangId, FatUtility::VAR_STRING, '' );
		$str = str_replace( "{sitename}", $siteName, $str );
		echo $str;
		?></p>
		<a href="javascript:void(0)" onClick="signUpFormPopUp('teacher');" class="btn btn--primary btn--block"><?php echo Label::getLabel('LBL_Apply_to_be_a_teacher'); ?></a>
    </div>

</div>

<script >

$(document).ready(function(){
	$('.block__head-trigger-js').click(function(){
		if($(this).hasClass('is-active')){
			$(this).removeClass('is-active');
			$(this).siblings('.block__body-target-js').slideUp();return false;
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

	<?php if( isset($priceArr) && $priceArr ){ ?>
	var range,
	min = <?php echo $filterDefaultMinValue; ?>,
    max = <?php echo $filterDefaultMaxValue; ?>,
    from,
    to;
	var $from = $('input[name="priceFilterMinValue"]');
	var $to = $('input[name="priceFilterMaxValue"]');
	var $range = $("#price_range");
	var updateValues = function () {
		$from.prop("value", from);
		$to.prop("value", to);
	};
	step = 2;
	if(0 < min && 1 > min) {
		step = 0.02;
	}

	$("#price_range").ionRangeSlider({
		hide_min_max: true,
		hide_from_to: true,
		keyboard: true,
		min: min,
		max: max,
		from: <?php echo $minPrice;  ?>,
		to: <?php echo $maxPrice;?>,
		step : step,
		type: 'double',
		prettify_enabled: true,
		prettify_separator: ',',
		grid: true,
		// grid_num: 1,
		prefix: '<?php echo $currencySymbolLeft; ?>',
		postfix: '<?php echo $currencySymbolRight; ?>',

		input_values_separator: '-',
		onFinish: function () {
			var minMaxArr = $("#price_range").val().split('-');
			if(minMaxArr.length == 2){
				var min = Number(minMaxArr[0]);
				var max = Number(minMaxArr[1]);
				$('input[name="priceFilterMinValue"]').val(min);
				$('input[name="priceFilterMaxValue"]').val(max);
				return addPricefilter();
				//return searchProducts(document.frmProductSearch);
			}

		},
		onChange: function (data) {
			from = data.from;
			to = data.to;
			updateValues();
		}
	});


	range = $range.data("ionRangeSlider");

	var updateRange = function () {
		range.update({
			from: from,
			to: to
		});
		addPricefilter();
	};

	$from.on("change", function () {
		from = $(this).prop("value");
		if(!$.isNumeric(from)){
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

	$to.on("change", function () {
		to = $(this).prop("value");
		if(!$.isNumeric(to)){
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
