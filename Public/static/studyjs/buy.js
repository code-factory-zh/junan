function pay(){
	$('#ewm').attr('src', '')
    $('.abs_mid_win').show();
    $.get('/manage/pay/getCodeUrl', function(r) {
    	if (r.code == 0) {
    		console.log(r.rel.url)

    		var init = setInterval(function () {
    			$.get('/manage/pay/checkCallBack?od=' + r.rel.ordernum, function(re) {
    				console.log(re)
    				if (re.code == 0) {
    					init = window.clearInterval(init)
    					return true
    				}
    			})
    		}, 1000);

    		$('#ewm').attr('src', r.rel.url)
    	}
    })
}



function close_win(){
    $('.abs_mid_win').hide();
}