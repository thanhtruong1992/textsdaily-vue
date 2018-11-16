$('document').ready(function() {
    var url = window.location.pathname;
    var arr = url.split('/');
    if(!!arr[2]) {
        $('.' + arr[2]).addClass('active');
        if(arr[2] == "subscriber-lists") {
        	$('.subscribers').addClass('active');
        }
        if(arr[2] == "campaign" || arr[2] == "notification-settings") {
        	$('.campaigns').addClass('active');
        }
        if(arr[2] == "reports") {
        	$('.report').addClass('active');
        	if(!!arr[3]) {
            	if(arr[3] == "campaigns") {
                	$('.report-campaign').addClass('active');
                }else if(arr[3] == "center") {
                	$('.report-center').addClass('active');
                }
        		
            }
        }
        if(arr[2] == "clients") {
        	$('.client').addClass('active');
        }
        if(arr[2] == "tokens") {
            $('.token').addClass('active');
        }
    }       
});