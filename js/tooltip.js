$(document).ready(function() {
	$('.toolTip').hover(
		function() {
			this.tip = this.title;
			this.tip2 = $("#" + this.tip).html();
			$(this).append(
				'<div class="toolTipWrapper">'
					+'<div class="toolTipTop"></div>'
					+'<div class="toolTipMid">'
						+ this.tip2
					+'</div>'
					+'<div class="toolTipBtm"></div>'
				+'</div>'
			);
			this.title = "";
			this.width = $(this).width();
			$(this).find('.toolTipWrapper').css({
				left: ($(this).position().center + 0) + 'px',
				top: (parseFloat($(this).height()) + $(this).position().top) + 'px'
			})
			$('.toolTipWrapper').fadeIn(300);
		},
		function() {
			var obj = this;
			$('.toolTipWrapper').fadeOut(100, function () {
				$(obj).find('.toolTipWrapper').remove();
			});
			this.title = this.tip;
		}
	);
});