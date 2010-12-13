<style type="text/css">
	#income-expense-graph-header {
	      width: 200px;
	      text-align: center;
	      padding-bottom:30px;
	}
	#income-expense-graph-data {
	      width: 200px;
	      height: 200px;
	      text-align: center;
	}
	#asset-liability-graph-header {
	      width: 200px;
	      text-align: center;
	      padding-bottom:30px;
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
			[<?php echo -$income_total; ?>, {label: 'Incomes'}],
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
			[<?php echo -$liability_total; ?>, {label: 'Liabilities'}]
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
<div id="dashboard-summary">
	<div id="dashboard-welcome-back" class="dashboard-item">
		<div class="dashboard-title">Account Summary</div>
		<div class="dashboard-content">
			<table class="dashboard-summary-table">
				<tbody>
					<tr>
						<td><div>Welcome back, <strong><?php echo $this->config->item('account_name');?> !</strong></div></td>
					</tr>
					<tr>
						<td><div id="dashboard-draft">You have <?php echo anchor("voucher/show/draft", $draft_count . " draft", array('class' => 'anchor-link-a')); ?> voucher(s)</div></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="clear"></div>
	<div id="dashboard-cash-bank" class="dashboard-item">
		<div class="dashboard-title">Bank and Cash A/C's</div>
		<div class="dashboard-content">
			<?php
				if ($bank_cash_account)
				{
					echo "<table class=\"dashboard-cashbank-table\">";
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
	</div>
	<div id="dashboard-summary" class="dashboard-item">
		<div class="dashboard-title">Account Summary</div>
		<div class="dashboard-content">
			<?php
				echo "<table class=\"dashboard-summary-table\">";
				echo "<tbody>";
				echo "<tr><td>Assets Total</td><td>" . convert_amount_dc($asset_total) . "</td></tr>";
				echo "<tr><td>Liabilities Total</td><td>" . convert_amount_dc($liability_total) . "</td></tr>";
				echo "<tr><td>Incomes Total</td><td>" . convert_amount_dc($income_total) . "</td></tr>";
				echo "<tr><td>Expenses Total</td><td>" . convert_amount_dc($expense_total) . "</td></tr>";
				echo "</tbody>";
				echo "</table>";
			?>
		</div>
	</div>
	<div class="clear"></div>
	<div>
		<table border="0">
			<tbody>
				<tr>
					<?php if ($show_income_expense) { ?>
						<td width="300">
							<div id="income-expense" class="graph">
								<div id="income-expense-graph-header"><strong>Incomes Vs Expenses</strong></div>
								<div id="income-expense-graph-data"></div>
							</div>
						</td>
					<?php } ?>
					<?php if ($show_asset_liability) { ?>
						<td width="300">
							<div id="asset-liability" class="graph">
								<div id="asset-liability-graph-header"><strong>Assets Vs Liabilities</strong></div>
								<div id="asset-liability-graph-data""></div>
							</div>
						</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div id="dashboard-log">
	<div id="dashboard-recent-log" class="dashboard-log-item">
		<div class="dashboard-log-title">Recent Activity</div>
		<div class="dashboard-log-content">
			<?php
			if ($logs)
			{
				echo "<ul>";
				foreach ($logs->result() as $row)
				{
					echo "<li>" . $row->message_title . "</li>";
				}
				echo "</ul>";
			} else {
				echo "No Recent Activity";
			}
			?>
		</div>
		<?php
			if ($logs)
			{
				echo "<div class=\"dashboard-log-footer\">";
				echo "<span>";
				echo anchor("", "more...", array('class' => 'anchor-link-a no-underline'));
				echo "</span>";
			}
		?>
		</div>
	</div>
</div>
<div class="clear"></div>
