<div id="main-content">

<!-- EXPENSES PER TAG -->
<!-- Sample of jqplot charts -->
<div id="expensesPerTag" style="height:400px;width:600px;margin-left:auto;margin-right:auto;"></div>
<script>
$.jqplot.config.enablePlugins = true;
	var url = '<?php echo (site_url('report/charts_data') . '/type/outTag/callback/handleOutTag') ?>';
	window.handleOutTag = function(data) {
	var plot1 = jQuery.jqplot ('expensesPerTag', [data], 
	{
		title: 'Expenses Per TAG',
		seriesDefaults: {
			// Make this a pie chart.
			renderer: jQuery.jqplot.PieRenderer, 
			rendererOptions: {
				// Put data labels on the pie slices.
				// By default, labels show the percentage of the slice.
				showDataLabels: true,
				dataLabels: 'percent'
			}
		}, 
		highlighter: {
			show:true,
			tooltipLocation: 'n',
			useAxesFormatters: false,
			formatString:'%s, %P',
		},
		legend: { show:true, location: 'e' },
		grid: {
			borderWidth: 0,
			background: '#fff',
			shadow: false,
		},
	}); // End of jqplot def
	}; // End of Json response handler
	
	jQuery.ajax({ url: url, dataType: "jsonp", type: "GET", cache: true, jsonp: false, jsonpCallback: "handleOutTag"});

</script>
<!-- ENDOF EXPENSES PER TAG -->

<hr>

<!-- INCOMES PER TAG -->
<!-- Sample of jqplot charts -->
<div id="entriesPerTag" style="height:400px;width:600px;margin-left:auto;margin-right:auto;"></div>
<script>
$.jqplot.config.enablePlugins = true;
	var url = '<?php echo (site_url('report/charts_data') . '/type/inTag/callback/handleInTag') ?>';
	window.handleInTag = function(data) {
	var plot1 = jQuery.jqplot ('entriesPerTag', [data], 
	{
		title: 'Incomes Per TAG',
		seriesDefaults: {
			// Make this a pie chart.
			renderer: jQuery.jqplot.PieRenderer, 
			rendererOptions: {
				// Put data labels on the pie slices.
				// By default, labels show the percentage of the slice.
				showDataLabels: true,
				dataLabels: 'percent'
			}
		}, 
		highlighter: {
			show:true,
			tooltipLocation: 'n',
			useAxesFormatters: false,
			formatString:'%s, %P',
		},
		legend: { show:true, location: 'e' },
		grid: {
			borderWidth: 0,
			background: '#fff',
			shadow: false,
		},
	}); // End of jqplot def
	}; // End of Json response handler
	
	jQuery.ajax({ url: url, dataType: "jsonp", type: "GET", cache: true, jsonp: false, jsonpCallback: "handleInTag"});

</script>
<!-- ENDOF INCOMES PER TAG -->

<hr>

<!-- EXPENSES PER TAG -->
<!-- Sample of jqplot charts -->
<div id="expensesPerMonth" style="height:400px;width:600px;margin-left:auto;margin-right:auto;"></div>
<script>
$.jqplot.config.enablePlugins = true;
	var url = '<?php echo (site_url('report/charts_data') . '/type/outMonth/callback/handleOutMonth') ?>';
	window.handleOutMonth = function(data) {
	var plot1 = jQuery.jqplot ('expensesPerMonth', [data], 
	{
		title: 'Expenses Per Month',
		seriesDefaults: {
			// Make this a pie chart.
			renderer: jQuery.jqplot.PieRenderer, 
			rendererOptions: {
				// Put data labels on the pie slices.
				// By default, labels show the percentage of the slice.
				showDataLabels: true,
				dataLabels: 'percent'
			}
		}, 
		highlighter: {
			show:true,
			tooltipLocation: 'n',
			useAxesFormatters: false,
			formatString:'%s, %P',
		},
		legend: { show:true, location: 'e' },
		grid: {
			borderWidth: 0,
			background: '#fff',
			shadow: false,
		},
	}); // End of jqplot def
	}; // End of Json response handler
	
	jQuery.ajax({ url: url, dataType: "jsonp", type: "GET", cache: true, jsonp: false, jsonpCallback: "handleOutMonth"});

</script>
<!-- ENDOF EXPENSES PER MONTH -->

<hr>

<!-- INCOMES PER TAG -->
<!-- Sample of jqplot charts -->
<div id="entriesPerMonth" style="height:400px;width:600px;margin-left:auto;margin-right:auto;"></div>
<script>
$.jqplot.config.enablePlugins = true;
	var url = '<?php echo (site_url('report/charts_data') . '/type/inMonth/callback/handleInMonth') ?>';
	window.handleInMonth = function(data) {
	var plot1 = jQuery.jqplot ('entriesPerMonth', [data], 
	{
		title: 'Incomes Per Month',
		seriesDefaults: {
			// Make this a pie chart.
			renderer: jQuery.jqplot.PieRenderer, 
			rendererOptions: {
				// Put data labels on the pie slices.
				// By default, labels show the percentage of the slice.
				showDataLabels: true,
				dataLabels: 'percent'
			}
		}, 
		highlighter: {
			show:true,
			tooltipLocation: 'n',
			useAxesFormatters: false,
			formatString:'%s, %P',
		},
		legend: { show:true, location: 'e' },
		grid: {
			borderWidth: 0,
			background: '#fff',
			shadow: false,
		},
	}); // End of jqplot def
	}; // End of Json response handler
	
	jQuery.ajax({ url: url, dataType: "jsonp", type: "GET", cache: true, jsonp: false, jsonpCallback: "handleInMonth"});

</script>
<!-- ENDOF INCOMES PER MONTH -->

</div> <!-- main content -->
<br />	