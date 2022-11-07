$(document).ready(function(){
	getStatisticalData(document.reportSearchForm);
	searchLessons(document.frmSrch);
})

var dv = '#listItemsLessons';
var chart;
var graphMediaW = $('.graph-media').width();
function searchLessons (frm){
	$(dv).html(fcom.getLoader());
	var data = fcom.frmData(frm);
	fcom.ajax(fcom.makeUrl('TeacherScheduledLessons','search'),data,function(t){
		$(dv).html(t);
	});
};

getStatisticalData = function(form){
	if (!$(form).validate()) return;
	var data = fcom.frmData(form);
	fcom.ajax(fcom.makeUrl('TeacherReports','getStatisticalData'), data , function(res){
		$('.earing-amount-js').html(res.earningData.earning);
		$('.lessons-sold-count-js').html(res.soldLessons.lessonCount);
	
		google.load("visualization", "1", {
			packages: ["corechart", 'table']
		});
		
		if(chart  != undefined || chart != null ){
			chart.clearChart();
		}
		var options = {
			height: 380,
			width:graphMediaW,
			colors:["#f4d18c","#3bc0c0"],
			legend: 'none',
			hAxis: {
				title: res.graphData.column.durationType,
			},
			animation:{
				duration: 1000,
				easing: 'out',
			  }
			};

		google.setOnLoadCallback(function () {
			// data = new google.visualization.DataTable();
			
			column = res.graphData.column;
			rowData = res.graphData.rowData;
			data = new google.visualization.DataTable();
			data.addColumn('string', column.durationType);
			data.addColumn('number', column.earningLabel);
			data.addColumn({"type": 'string', "role": 'tooltip'});
			data.addColumn('number', column.lessonSoldLabel);
			data.addColumn({"type": 'string', "role": 'tooltip'});
			// let data = [];
		
    		data.addRows(rowData);
			// var data = new google.visualization.arrayToDataTable([["Last Month", "Earning", "Lessons Sold"]]);

			drawChart(data, options);
		});

	},{fOutMode:'json'});
}

function viewCalendar (frm){
	var data = fcom.frmData(frm);
	fcom.ajax(fcom.makeUrl('TeacherScheduledLessons','viewCalendar'),data,function(t){
		$(dv).html(t);
                $('body').addClass('calendar-facebox');
	});
};
function drawChart(graphArray, options) {
	containerDiv = document.getElementById("chart_div");

	chart = new google.visualization.AreaChart(containerDiv);
	chart.draw(graphArray, options);
  }
