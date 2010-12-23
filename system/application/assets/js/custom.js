/* Confirm click */

$(document).ready(function() {
	$(".confirmClick").click( function() { 
	    if ($(this).attr('title')) {
		var question = 'Are you sure you want to ' + $(this).attr('title').toLowerCase() + '?';
	    } else {
		var question = 'Are you sure you want to do this action?';
	    }
	    if ( confirm( question ) ) {
		[removed].href = this.src;
	    } else {
		return false;
	    }
	});
	$("#tooltip-target-1").ezpz_tooltip();
	$("#tooltip-target-2").ezpz_tooltip();
	$("#tooltip-target-3").ezpz_tooltip();
})


