<div id="main-content">

	<?php
	$chart_type= 'pie';
	if (isset($_GET['type'])) {
		$chart_type= $_GET['type'];
	}
	?>
	
	<!-- EXPENSES PER TAG -->
	<hr>
	<div id="expensesPerTag" style="min-width: 400px; height: 400px; margin: 0 auto"></div>	
	<script>
	$(document).ready(function() {
		var options = {
			chart: {
				renderTo: 'expensesPerTag',
				type: '<?php echo ($chart_type) ?>'
			},
			title: { text: 'Expenses Per TAG' },
			credits: {enabled: false},
			plotOptions: { series: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: true, format: '<b>{point.name}:</b> <i>{point.y}</i> ({point.percentage:.1f}%)' }, showInLegend: true } },
			series: [{}]
		}; 
		var callback = 'jsonp' + (new Date().getTime());
		var url = '<?php echo (site_url('report/charts_data') . '/type/outTag/callback/') ?>' + callback;
		$.getJSON(url,  function(data) {
			options.series[0].data = data;
			var chart = new Highcharts.Chart(options);
		});
	});
	</script>
	
	<!-- ENDOF EXPENSES PER TAG -->




	<!-- ENTRIES PER TAG -->
	<hr>
	<div id="entriesPerTag" style="min-width: 400px; height: 400px; margin: 0 auto"></div>	
	<script>
	$(document).ready(function() {
		var options = {
			chart: {
				renderTo: 'entriesPerTag',
				type: '<?php echo ($chart_type) ?>'
			},
			title: { text: 'Entries Per TAG' },
			credits: {enabled: false},
			plotOptions: { series: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: true, format: '<b>{point.name}:</b> <i>{point.y}</i> ({point.percentage:.1f}%)' }, showInLegend: true } },
			series: [{}]
		};    
		var url = '<?php echo (site_url('report/charts_data') . '/type/inTag/callback/') ?>';
		$.getJSON(url,  function(data) {
			options.series[0].data = data;
			var chart = new Highcharts.Chart(options);
		});
	});
	</script>
	<!-- ENDOF ENTRIES PER TAG -->


	<!-- EXPENSES PER MONTH -->
	<hr>
	<div id="expensesPerMonth" style="min-width: 400px; height: 400px; margin: 0 auto"></div>	
	<script>
	$(document).ready(function() {
		var options = {
			chart: {
				renderTo: 'expensesPerMonth',
				type: '<?php echo ($chart_type) ?>'
			},
			title: { text: 'Expenses Per Month' },
			credits: {enabled: false},
			plotOptions: { series: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: true, format: '<b>{point.name}:</b> <i>{point.y}</i> ({point.percentage:.1f}%)' }, showInLegend: true } },
			series: [{}]
		};    
		var url = '<?php echo (site_url('report/charts_data') . '/type/outMonth/callback/') ?>';
		$.getJSON(url,  function(data) {
			options.series[0].data = data;
			var chart = new Highcharts.Chart(options);
		});
	});
	</script>
	<!-- ENDOF ENTRIES PER TAG -->
	
	
	<!-- ENTRIES PER MONTH -->
	<hr>
	<div id="entriesPerMonth" style="min-width: 400px; height: 400px; margin: 0 auto"></div>	
	<script>
	$(document).ready(function() {
		var options = {
			chart: {
				renderTo: 'entriesPerMonth',
				type: '<?php echo ($chart_type) ?>'
			},
			title: { text: 'Entries Per Month' },
			credits: {enabled: false},
			plotOptions: { series: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: true, format: '<b>{point.name}:</b> <i>{point.y}</i>' }, showInLegend: true } },
			series: [{}]
		};    
		var url = '<?php echo (site_url('report/charts_data') . '/type/inMonth/callback/') ?>';
		$.getJSON(url,  function(data) {
			options.series[0].data = data;
			var chart = new Highcharts.Chart(options);
		});
	});
	</script>
	<!-- ENDOF ENTRIES PER TAG -->


</div> <!-- main content -->
<br />	