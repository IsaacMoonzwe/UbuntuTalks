(function(){
	parseJsonData = function(t){
		if(t=='') return(false);
		if(t=='Your session seems to be timed out. Please try reloading the page.'){
			t='{"status":0,"msg":"'+t+'"}';
		}
		try {
			var ans = eval('['+t+']');
			return(ans[0]);
		} catch (e) {
			return(false);
		}
	};

	getTopLessonLanguage = function(interval){
		$('.topLessonLanguage').html('<li>'+fcom.getLoader()+'</li>');
		data="rtype=top_lesson_languages&interval="+interval;

		fcom.ajax(fcom.makeUrl('home', 'dashboardStats'), data, function(t) {
			$('.topLessonLanguage').html(t);
		});
	};

	traficSource = function(interval){

		google.setOnLoadCallback(createTrafficSourceChart, interval);
	};

	visitorStats = function(){

		google.setOnLoadCallback(createVisitorStatsChart);
	};

	function createTrafficSourceChart(interval) {

		$('#piechart').html(fcom.getLoader());
		data="rtype=traffic_source&interval="+interval;

		fcom.ajax(fcom.makeUrl('home', 'dashboardStats'), data, function(t) {
			var ans = parseJsonData(t);
			if (ans) {

			var dataTraficSrc = google.visualization.arrayToDataTable(ans);
			var optionsTraficSrc = { title: '',width:$('#piechart').width(),height:360,pieHole: 0.4,pieStartAngle: 100 ,legend:{position:'bottom',textStyle: { fontSize: 12, alignment:'center'}}};
			var trafic = new google.visualization.PieChart(document.getElementById('piechart'));
			trafic.draw(dataTraficSrc, optionsTraficSrc);
			} else {
				$('#piechart').html(t);
			}
		});
	}

	function createVisitorStatsChart() {

		$('#visitsGraph').html(fcom.getLoader());
		data="rtype=visitors_stats";

		fcom.ajax(fcom.makeUrl('home', 'dashboardStats'), data, function(t) {

			var ans = parseJsonData(t);

			if (ans) {

				var dataVisits = google.visualization.arrayToDataTable(ans);
				var optionVisits = { title: '',width:$('#visitsGraph').width(),height:240,curveType: 'function',
				legend: { position: 'bottom', },

				hAxis: {direction:(layoutDirection=='rtl')?-1:1},
				series : {
						0: {
							targetAxisIndex : (layoutDirection=='rtl')?1:0},
						1: {
							targetAxisIndex : (layoutDirection=='rtl')?1:0},
						2: {
							targetAxisIndex : (layoutDirection=='rtl')?1:0},
						3: {
							targetAxisIndex : (layoutDirection=='rtl')?1:0}
					}
				};

				var visits = new google.visualization.LineChart(document.getElementById('visitsGraph'));
				visits.draw(dataVisits, optionVisits);
			} else {
				$('#visitsGraph').html(t);
			}
		});
	}


})();

$(document).ready(function () {
	if (layoutDirection != 'rtl') {
		$position = 'start';
	} else {
		$position = 'end';
	}
	callChart('monthlysales--js', $SalesChartKey, $SalesChartVal, $position);
	$('.counter').each(function () {
		var $this = $(this),
			countTo = $this.attr('data-count');

		$({ countNum: $this.text() }).animate({
			countNum: countTo
		},
			{
				duration: 8000,
				easing: 'linear',
				step: function () {
					if ($this.attr('data-currency') == 1) {
						$this.text(dataCurrencyLeft + Math.floor(this.countNum) + dataCurrencyRight);
					} else {
						$this.text(Math.floor(this.countNum));
					}
				},
				complete: function () {
					if ($this.attr('data-currency') == 1) {
						$this.text(dataCurrencyLeft + this.countNum + dataCurrencyRight);
					} else {
						$this.text(this.countNum);
					}
				}
			});
	});
});

$(document).ready(function(){
	visitorStats();
	traficSource('yearly');
	getTopLessonLanguage('yearly');
	$('.carousel--oneforth-js').slick(getSlickSliderSettings(4));
	/* FUNCTION FOR SCROLLBAR */
	 $('.scrollbar-js').enscroll({
		verticalTrackClass: 'scroll__track',
		verticalHandleClass: 'scroll__handle'
	});
	
	$('.statistics-nav-js li a').click(function(){
		$('.statistics-nav-js li a').removeClass('active');
		$('.statistics-tab-js .tabs_panel').hide();
		var activeTab = $(this).attr('rel');
		$(this).addClass('active');
		$("#"+activeTab).show();
		if ($(this).attr('data-chart')) {
            if (layoutDirection != 'rtl') {
                $position = 'start';
            } else {
                $position = 'end';
            }
            if (activeTab == 'tabs_1') {
                callChart('monthlysales--js', $SalesChartKey, $SalesChartVal, $position);
            } else if (activeTab == 'tabs_2') {
                callChart('monthlysalesearnings--js', $earningsKey, $earningsVal, $position);
            } else if (activeTab == 'tabs_3') {
                callChart('monthly-signups--js', $signupsKey, $signupsVal, $position);
            }
        }
	});

});
