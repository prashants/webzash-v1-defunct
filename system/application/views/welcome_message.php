<style type="text/css">
	#income-expense-graph-header {
	      width: 200px;
	      text-align: center;
	      padding-bottom:10px;
	}
	#income-expense-graph-data {
	      width: 200px;
	      height: 200px;
	      text-align: center;
	}
	#asset-liability-graph-header {
	      width: 200px;
	      text-align: center;
	      padding-bottom:10px;
	}
	#asset-liability-graph-data {
	      width: 200px;
	      height: 200px;
	      text-align: center;
	}
</style>

<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery('#income-expense-graph-data').tufteBar({
		data: [
		// First element is the y-value
		// Other elements are arbitary - they are not used by the lib
		// but are passed back into callback functions
			[<?php echo $income_total; ?>, {label: 'Incomes'}],
			[<?php echo $expense_total; ?>, {label: 'Expenses'}]
		],

		// Bar width in arbitrary units, 1.0 means the bars will be snuggled
		// up next to each other
		barWidth: 0.5, 

		// The label on top of the bar - can contain HTML
		// formatNumber inserts commas as thousands separators in a number
		barLabel:  function(index) {
			return $.tufteBar.formatNumber(this[0])
		}, 

		// The label on the x-axis - can contain HTML
		axisLabel: function(index) { return this[1].label }, 

		// The color of the bar
		color:     function(index) {
			return ['#33CC66', '#C00000'][index % 2] 
		},
	});

	jQuery('#asset-liability-graph-data').tufteBar({
		data: [
		// First element is the y-value
		// Other elements are arbitary - they are not used by the lib
		// but are passed back into callback functions
			[<?php echo $asset_total; ?>, {label: 'Assets'}],
			[<?php echo $liability_total; ?>, {label: 'Liabilities'}]
		],

		// Bar width in arbitrary units, 1.0 means the bars will be snuggled
		// up next to each other
		barWidth: 0.5, 

		// The label on top of the bar - can contain HTML
		// formatNumber inserts commas as thousands separators in a number
		barLabel:  function(index) {
			return $.tufteBar.formatNumber(this[0])
		}, 

		// The label on the x-axis - can contain HTML
		axisLabel: function(index) { return this[1].label }, 

		// The color of the bar
		color:     function(index) {
			return ['#33CC66', '#C00000'][index % 2]
		},
	});
});
</script>

<h3>&lt;This is beta software not meant for production use !&gt;</h3>
<div>
	<div>Welcome back, <strong><?php echo $this->config->item('account_name');?></strong></div>
	<br />
	<div>You have <?php echo $draft_count;?> draft voucher(s)</div>
	<br />
	<?php
		if ($bank_cash_account)
		{
			echo "<table>";
			echo "<tbody>";
			foreach ($bank_cash_account as $id => $row)
			{
				echo "<tr>";
				echo "<td>" . anchor('report/ledgerst/' . $row['id'], $row['name'], array('title' => $row['name'] . ' Statement')) . "</td>";
				echo "<td>";
				if ($row['balance'] == 0)
					echo "0";
				else if ($row['balance'] > 0)
					echo "Dr " . $row['balance'];
				else
					echo "Cr " . -$row['balance'];
				echo "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
		} else {
			echo "You have not created any bank or cash account";
		}
	?>
</div>
<div>
	<table border="0">
		<tbody>
			<tr>
				<td width="300">
					<div id="income-expense" class="graph">
						<div id="income-expense-graph-header"><h4>Incomes Vs Expenses</h4></div>
						<div id="income-expense-graph-data"></div>
					</div>
				</td>
				<td width="300">
					<div id="asset-liability" class="graph">
						<div id="asset-liability-graph-header"><h4>Assets Vs Liabilities</h4></div>
						<div id="asset-liability-graph-data""></div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
