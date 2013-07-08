<!--
<h1>Expenses Per TAG</h1>
	<div id="print-report-period">
		
		<span class="value">
			Financial year:	<?php echo date_mysql_to_php_display($this->config->item('account_fy_start')); ?> - <?php echo date_mysql_to_php_display($this->config->item('account_fy_end')); ?>
		</span>
		
	</div>
	<br />
	-->
	<div id="main-content">
		<!-- TAG -> Expense -->


<?php
	/**
	 * EXPENSES PER TAG.
	 * The first 10 tags with greatest values will be shown and the remaining ones will be collapsed in a generic "Others" tag.
	 */
?>
<hr>
<div id="expensesPerTag" style="min-width: 400px; height: 400px; margin: 0 auto"></div>	
	
<?php
		echo <<<"FOOBAR"
		<script>

	// Se tolgo il conflitto non funzionano i menu
	// jQuery.noConflict();
	$(function () { 
		$('#expensesPerTag').highcharts({
      chart: { renderTo: 'expensesPerTag', type: 'pie', plotShadow: true },
        title: { text: 'Expenses Per TAG' },
		credits: {enabled: false},
		plotOptions: { pie: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: true, format: '<b>{point.name}:</b> <i>{point.y}</i> ({point.percentage:.1f}%)' }, showInLegend: true } },
        series: [{type: 'pie',
			data: [
FOOBAR;
?>
<?php $result = mysql_query("select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = 2 and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) DESC LIMIT 10") 
	or die("Invalid Query: " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		printf("['%s', %s],", $row[0], $row[1]); 
	}
?>
<?php $result = mysql_query("select sub-main AS others from (select sum(total) as sub from ( select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = 2 and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) ) AS t1) AS Tsub, (select sum(total) as main from ( select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = 2 and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) DESC LIMIT 10 ) AS t0) AS Tmain") 
	or die("Query non valida: " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		printf("['Others', %s],", $row[0]); 
	}
?>

<?php
		echo <<<"FOOBAR"
		]}]
    });
});
</script>
FOOBAR;
?>


<?php
	/**
	 * ENTRIES PER TAG.
	 * The first 10 tags with greatest values will be shown and the remaining ones will be collapsed in a generic "Others" tag.
	 */
?>
<hr>
<div id="entriesPerTag" style="min-width: 400px; height: 400px; margin: 0 auto"></div>	
	
<?php
		echo <<<"FOOBAR"
		<script>

	// Se tolgo il conflitto non funzionano i menu
	// jQuery.noConflict();
	$(function () { 
		$('#entriesPerTag').highcharts({
      chart: { renderTo: 'entriesPerTag', type: 'pie', plotShadow: true },
        title: { text: 'Entries Per TAG' },
		credits: {enabled: false},
		plotOptions: { pie: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: true, format: '<b>{point.name}:</b> <i>{point.y}</i> ({point.percentage:.1f}%)' }, showInLegend: true } },
        series: [{type: 'pie',
			data: [
FOOBAR;
?>
<?php $result = mysql_query("select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = 1 and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) DESC LIMIT 10") 
	or die("Invalid Query: " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		printf("['%s', %s],", $row[0], $row[1]); 
	}
?>
<?php $result = mysql_query("select sub-main AS others from (select sum(total) as sub from ( select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = 1 and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) ) AS t1) AS Tsub, (select sum(total) as main from ( select t.title as tag, sum(e.dr_total) AS total from entries e, tags t where entry_type = 1 and e.tag_id = t.id group by tag ORDER BY sum(e.dr_total) DESC LIMIT 10 ) AS t0) AS Tmain") 
	or die("Query non valida: " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		printf("['Others', %s],", $row[0]); 
	}
?>

<?php
		echo <<<"FOOBAR"
		]}]
    });
});
</script>
FOOBAR;
?>





<!--		
 CHART: Expenses Per Month
-->
<hr>
<div id="expensesPerMonth" style="min-width: 400px; height: 400px; margin: 0 auto"></div>	
	
<?php
		echo <<<"FOOBAR"
		<script>

	// Se tolgo il conflitto non funzionano i menu
	// jQuery.noConflict();
	$(function () { 
		$('#expensesPerMonth').highcharts({
      chart: { renderTo: 'expensesPerMonth', type: 'pie', plotShadow: true },
        title: { text: 'Expenses Per Month' },
		credits: {enabled: false},
		plotOptions: { pie: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: true, format: '<b>{point.name}:</b> <i>{point.y}</i> ({point.percentage:.1f}%)' }, showInLegend: true } },
        series: [{type: 'pie',
			data: [
FOOBAR;
?>
<?php $result = mysql_query("select CONCAT(MONTHNAME(STR_TO_DATE(DATE_FORMAT(e.date,  '%m'), '%m')), '/' , DATE_FORMAT(e.date,  '%Y')), sum(e.dr_total) from entries e where entry_type = 2 group by MONTH(e.date) order by e.date") 
	or die("Query non valida: " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		printf("['%s', %s],", $row[0], $row[1]); 
	}
?>
<?php
		echo <<<"FOOBAR"
		]}]
    });
});
</script>
FOOBAR;
?>



<!--		
 CHART: Expenses Per Month
-->
<hr>
<div id="entriesPerMonth" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
	
<?php
		echo <<<"FOOBAR"
		<script>

	// Se tolgo il conflitto non funzionano i menu
	// jQuery.noConflict();
	$(function () { 
		$('#entriesPerMonth').highcharts({
      chart: { renderTo: 'entriesPerMonth', type: 'pie', plotShadow: true },
        title: { text: 'Entries Per Month' },
		credits: {enabled: false},
		plotOptions: { pie: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: true, format: '<b>{point.name}:</b> <i>{point.y}</i> ({point.percentage:.1f}%)' }, showInLegend: true } },
        series: [{type: 'pie',
			data: [
FOOBAR;
?>
<?php $result = mysql_query("select CONCAT(MONTHNAME(STR_TO_DATE(DATE_FORMAT(e.date,  '%m'), '%m')), '/' , DATE_FORMAT(e.date,  '%Y')), sum(e.dr_total) from entries e where entry_type = 1 group by MONTH(e.date) order by e.date") 
	or die("Query non valida: " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		printf("['%s', %s],", $row[0], $row[1]); 
	}
?>
<?php
		echo <<<"FOOBAR"
		]}]
    });
});
</script>
FOOBAR;
?>

</div> <!-- main content -->
<br />	