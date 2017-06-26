	$(function() {
		$(".jinhe").click(function(e) {
			$(this).hide();
			$(".jinhec").show();

		});
		$(".jinhech").click(function(e) {
			$(".jinhec").hide();
			$(".jinhe").show();
		});

		$(".danbaox").click(function(e) {
			$(this).hide();
			$(".danbaoy").show();

		});
		$(".danbaoyy").click(function(e) {
			$(".danbaoy").hide();
			$(".danbaox").show();
		});
	});

	function chasheni()

	{

		// $(".didhidden").attr("value",did);
		$(".chashen").show();
		$(".zhezhao").show();
	}
	function guanbi() {
		$(".chashen").hide();

		$(".zhezhao").hide();
	}