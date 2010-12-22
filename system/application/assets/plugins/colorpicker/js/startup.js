$(document).ready(function() {

	/* Setting initial values */
	var tag_col = $('#tag_color').val();
	var background_col = $('#tag_background').val();
	if (!tag_col) {
		tag_col = "#" + "000000";
	}
	if (!background_col) {
		background_col = "#" + "000000";
	}

	$('#tag_color').ColorPicker({
		color: tag_col,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#preview_tag_color').css('backgroundColor', '#' + hex);
			$('#tag_color').val(hex);
		}
	});
	$('#tag_background').ColorPicker({
		color: background_col,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#preview_tag_background').css('backgroundColor', '#' + hex);
			$('#tag_background').val(hex);
		}
	});
});
