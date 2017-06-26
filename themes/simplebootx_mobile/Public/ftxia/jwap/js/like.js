(function($) {
	$(".app_load").live('click', function() {
		var pid = $(this).attr("data-pid");
		if(!$.ftxia.dialog.islogin()) return ;
		$.ajax({
			url: FTXIAER.root + '/?m=ajax&a=like',
				type: 'POST',
				data: {
				pid: pid
			},
			dataType: 'json',
			success: function(result){
				if(result.status == 1){
					$.ftxia.tip({content:result.msg, icon:'success'});
				}else if(result.status == 2){
					$.ftxia.tip({content:result.msg, icon:'error'});
				}else{
					$.ftxia.tip({content:result.msg, icon:'error'});
				}
			}
		});
		  
	});

})(jQuery);