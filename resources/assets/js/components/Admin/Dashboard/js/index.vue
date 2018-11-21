<template src="../template/index.html">
    
</template>
<script>
require('select2');
import DashboardApi from "../../../../api/dashboard";
import jsonTimeZone from "../../../../json/data/timezones.json";
import * as moment from 'moment';

export default {
    data() {
        var user = JSON.parse(localStorage.getItem('auth'));
        var year = moment().get('year');
        var years = [];
        for(var i = 2017; i <= year; i++) {
            years.push(i);
        }

        var data = {
            dataMonth: moment.months(),
            day: moment().get('date'),
            year: year,
            years: years,
            month: moment().get('month') + 1,
            timezone: jsonTimeZone,
            totalDay: moment().daysInMonth(),
            user: user,
            // year: 2018
		};
		
		return data;
    },
    methods: {
        formatCurrency(value, currency) {
            return value.toFixed(2) + " " + currency;
        }
    },
    mounted() {
        // select filter
    $('.select-filter').select2();
    $('.select-date').select2();
    $('.select-month').select2();
    $('.select-year').select2();
    $('.select-timezone').select2();
    
    $('.select-filter').on('change', function(e) {
    	var value = e.target.value;
    	switch(value) {
    		case 'month':
    			!$('#select-date').hasClass('invisible') ? $('#select-date').addClass('invisible') : '';
    			!$('#select-month').hasClass('invisible') ? $('#select-month').addClass('invisible') : '';
    			break;
    		case 'day':
    			!$('#select-date').hasClass('invisible') ? $('#select-date').addClass('invisible') : '';
    			$('#select-month').removeClass('invisible');
    			break;
			default:
				$('#select-date').removeClass('invisible');
				$('#select-month').removeClass('invisible');
				break;
    	}
    	
    	// getDataChart();
    });
    
    // $('.select-year').on('change', function(e) {
    // 	var month = $('.select-month').val();
    // 	var year = e.target.value;
    // 	var date = 0;
    // 	var data = daysInMonth(year, month, date);
    // 	addOrRemoveOption(data.totalDate, data.currentDate);
    // 	getDataChart();
    // });
    
    // $('.select-month').on('change', function(e) {
    // 	var month = e.target.value;
    // 	var year = $('#select-year').val();
    // 	var date = 0;
    // 	var data = daysInMonth(year, month, date);
    // 	addOrRemoveOption(data.totalDate, data.currentDate);
    // 	getDataChart();
    // });
    
    // $('.select-date').on('change', function(e) {
    // 	getDataChart();
    // });
    
    // $('.select-hour').on('change', function(e) {
    // 	getDataChart();
    // });
    
    // $('.select-timezone').on('change', function(e) {
    // 	getDataChart();
    // });
    
    // function daysInMonth(year, month, date) {
    // 	year = year === null ? null : year;
    // 	month = month === null ? null : month;
    // 	date = date === null ? null : date;
    // 	var now = new Date();
    // 	year = year != null ? year : now.getFullYear();
    // 	month = month != null ? month : now.getMonth();
    // 	date = date != null ? date : now.getDate();
    // 	return {
    // 			totalDate: new Date(year, month, date).getDate(),
    // 			currentDate: now.getDate()
	// 	};
    // }
    
    // function addOrRemoveOption(totalDate, currentDate) {
    // 	var totalOption = $('.select-date')[0].childElementCount;
    // 	if(totalOption > totalDate) {
    // 		for(i = totalDate + 1; i <= totalOption; i++) {
    // 			$(".select-date option[value='" + i + "']").remove();
    // 		}
    // 		var selected = currentDate > totalDate ? totalDate : currentDate;
    // 		$(".select-date").val(selected);
    // 	}else if(totalOption < totalDate) {
    // 		for(i = totalOption + 1; i <= totalDate; i++) {
    // 			$(".select-date").append($('<option>', {
    // 			    value: i,
    // 			    text: i
    // 			}));
    // 		}
    // 		$(".select-date").val(currentDate);
    // 	}
    // }
    
    // let dataChart = {data: '', chart: ''};
    // google.charts.load('current', {packages: ['corechart']});
    // google.charts.setOnLoadCallback(function() {
    // 	dataChart = drawChart([], dataChart);
    // });
    
    // // get data chart
    // getDataChart();
	
    // // fn get data chart
	// function getDataChart() {
	// 	var filter = $('.select-filter').val();
	// 	var year = $('.select-year').val();
	// 	var month = $('.select-month').val();
	// 	var date = $('.select-date').val();
	// 	var timezone = $('.select-timezone').val();
		
	// 	$.ajax({
	// 		type : "GET",
	// 		url : window.location.origin + "/admin/apis/campaigns/total-send",
	// 		data: {
	// 			filter: filter,
	// 			year: year,
	// 			month: month,
	// 			date: date,
	// 			timezone: timezone
	// 		},
	// 		success : function(res) {
	// 			google.charts.load('current', {packages: ['corechart']});
	// 		    google.charts.setOnLoadCallback(function() {
	// 		    	dataChart = drawChart(res.data, dataChart, res.maxValue);
	// 			});
	// 			//console.log(res);
	// 		    showUsage(res.dataUsage, res.dateFilter);
	// 		},
	// 		error : function(err) {
				
	// 		}
	// 	});
	// }
    
    // // fn draw chart
    // function drawChart(value, dataChart, maxValue) {
    // 	value = Array.isArray(value) ? value : [];
    // 	maxValue = maxValue === null ? 0 : maxValue;
    // 	var filter = $('.select-filter').val();
    // 	var countData = value.length;
    // 	// config chart
    //     var options = {
    //             title: "",
    //             bar: {groupWidth: countData * 3 + "%"}, // width of col chart
    //             //tooltip: {isHtml: true},
    //             //focusTarget: 'category',
    //             legend: { position: "none" },
	// 	        animation: {duration: 1000, easing: 'out',},
    //             titleTextStyle: {
    //             	fontSize: 14,
    //             	fontName: 'Arial',
    //             },
    //             chartArea: {
    //             	left: '5%',
    //             	top: '5%',
    //             	right: '0%',
    //             	bottom: '20%'
    //             },
    //             hAxis: {
    //             	format: "0",
    //             	textStyle : {
    //                     fontSize: 10 // or the number you want
    //                 }
    //             },
    //             vAxis: {
    //                 format: "0",
    //                 gridlines: {
    //                 	count: maxValue <= 2 ? maxValue + 2 : 5
    //                 },
    //                 minValue: 0,
    //                 maxValue: maxValue + 1
    //             },
    //             colors: ['#8DBE6A']
    //     };
        
    // 	var data = dataChart.data;
	// 	var chart = dataChart.chart;
	// 	var oldData = "";
	// 	var row = 0;
		
	// 	if(value.length == 0) {
	// 		// new data chart
	//         data = new google.visualization.DataTable();
			  
	//         // set header
	//   	  	data.addColumn('string', 'Element');
	//   	  	data.addColumn('number', 'Total Message');
	//   	  	//data.addColumn({type:'string', role:'style'});
	//   	  	data.addColumn({type: 'string', role: 'tooltip', p: {html: true}});
	  	  	
	//   	  	// new chart
	//   	  	chart = new google.visualization.ColumnChart(document.getElementById('chartDashBoard'));

	//       	// render chart
	//   	  	chart.draw(data, options);
	// 	}else{
	// 		oldData = data.getNumberOfRows();
	// 		if(value.length > oldData && oldData > 0) {
	// 			for(i = 0; i < value.length; i++) {
	// 				var item = value[i];
	// 				var message = filter == 'hour' ? 'Hour: ' : '';
	// 				message += item.key + '\n Total Message: ' + item.value + '\n Total Cost:' ;
	// 				item.data.forEach(function(value, key) {
	// 					if(key == 0) {
	// 						message = message + '\xa0' + value.total_price + ' ' + value.currency + '\n';
	// 					}else {
	// 						message = message + '\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0' + value.total_price + ' ' + value.currency + '\n'
	// 					}
	// 				});
	// 				if(i >= oldData) {
	// 					data.insertRows(i, [[ item.key, item.value, message ]]);
	// 				}else {
	// 					data.setValue(i, 0, item.key);
	// 					data.setValue(i, 1, item.value);
	// 					data.setValue(i, 2, message);
	// 				}
	// 			}
	// 		}else if(value.length < oldData && oldData > 0) {
	// 			for(i = 0; i < oldData; i++) {
	// 				if(i < value.length) {
	// 					var item = value[i];
	// 					var message = filter == 'hour' ? 'Hour: ' : '';
	// 					message += item.key + '\n Total Message: ' + item.value + '\n Total Cost:' ;
	// 					item.data.forEach(function(value, key) {
	// 						if(key == 0) {
	// 							message = message + '\xa0' + value.total_price + ' ' + value.currency + '\n';
	// 						}else {
	// 							message = message + '\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0' + value.total_price + ' ' + value.currency + '\n'
	// 						}
	// 					});
	// 					data.setValue(i, 0, item.key);
	// 					data.setValue(i, 1, item.value);
	// 					data.setValue(i, 2, message);
	// 				}else {
	// 					data.removeRow(value.length);
	// 				}
	// 			}
	// 		}else if(value.length == oldData && value.length > 0) {
	// 			for(i = 0; i < oldData; i++) {
	// 				var item = value[i];
	// 				var message = filter == 'hour' ? 'Hour: ' : '';
	// 				message += item.key + '\n Total Message: ' + item.value + '\n Total cost:' ;
	// 				item.data.forEach(function(value, key) {
	// 					if(key == 0) {
	// 						message = message + '\xa0' + value.total_price + ' ' + value.currency + '\n';
	// 					}else {
	// 						message = message + '\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0' + value.total_price + ' ' + value.currency + '\n'
	// 					}
	// 				});
	// 				data.setValue(i, 0, item.key);
	// 				data.setValue(i, 1, item.value);
	// 				data.setValue(i, 2, message);
	// 			}
	// 		}else {
	// 			row = 0;
	// 			value.forEach(function(item) {
	// 				// add value chart
	// 				var message = filter == 'hour' ? 'Hour: ' : '';
	// 				message += item.key + '\n Total Message: ' + item.value + '\n Total Cost:' ;
	// 				item.data.forEach(function(value, key) {
	// 					if(key == 0) {
	// 						message = message + '\xa0' + value.total_price + ' ' + value.currency + '\n';
	// 					}else {
	// 						message = message + '\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0' + value.total_price + ' ' + value.currency + '\n'
	// 					}
						
	// 				});
	// 				data.insertRows(row, [[ item.key, item.value, message]]);
	// 				row += 1;
	// 			});
	// 		}
			
	// 		google.visualization.events.addListener(chart, 'ready', function () {
	// 	          var container = document.querySelector('#chartDashBoard > div:last-child');
	// 	          function setPosition () {
	// 	              var tooltip = container.querySelector('div.google-visualization-tooltip');
	// 	              tooltip.style.top = 0;
	// 	              tooltip.style.left = '-47px';
	// 	              tooltip.style.width = '300px';
	// 	          }
	// 	          if (typeof MutationObserver === 'function') {
	// 	              var observer = new MutationObserver(function (m) {
	// 	                  for (var i = 0; i < m.length; i++) {
	// 	                      if (m[i].addedNodes.length) {
	// 	                          setPosition();
	// 	                          break; // once we find the added node, we shouldn't need to look any further
	// 	                      }
	// 	                  }
	// 	              });
	// 	              observer.observe(container, {
	// 	                  childList: true
	// 	              });
	// 	          } else if (document.addEventListener) {
	// 	              container.addEventListener('DOMNodeInserted', setPosition);
	// 	          } else {
	// 	              container.attachEvent('onDOMNodeInserted', setPosition);
	// 	          }
	// 	      });
			
	// 		// render chart
	// 	    chart.draw(data, options);
	// 	}
		
	//     return {data: data, chart: chart};
    // }
    
    // /**
    //  * fn show usage
    //  * @param data
    //  * @param startDate
    //  * @param endDate
    //  * @returns
    //  */
    // function showUsage(data, dateFilter) {
	// 	if(data == undefined) {
	// 		//console.log(dateFilter);
	// 		//data = 0;
	// 	}
    // 	var html = "";
    // 	for(i = 0; i < data.length; i++) {
    // 		var item = data[i];
    // 		html += "<li class='" + (i > 0 ? 'mb-1' : '') + "'>" + item.total_price + " " + item.currency + "</li>";
    // 	}
	// 	$("#show-usage").html(html.length == 0 ? '<li>0</li>' : html);
	// 	$(".date-show").html(dateFilter);
	// }
    },
}
</script>