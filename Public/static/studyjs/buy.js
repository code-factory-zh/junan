function pay(){
	$('#ewm').attr('src', '')
    $('.abs_mid_win').show();
    $.get('/manage/pay/getCodeUrl', function(r) {
    	if (r.code == 0) {
    		console.log(r.rel.url)
    		$('#ewm').attr('src', r.rel.url)
    	}
    })
}
function close_win(){
    $('.abs_mid_win').hide();
}