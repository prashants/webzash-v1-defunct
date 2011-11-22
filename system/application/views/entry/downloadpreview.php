<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php $current_entry_type['name']; ?> Entry Number <?php echo $entry_number; ?></title>

<style type="text/css">
	body {
		color:#000000;
		font:14px "Helvetica Neue","Lucida Grande","Helvetica Neue",Arial,sans-serif;
		margin:20px;
		padding:0;
	}
	
	#print-account-name {
		text-align:center;
		font-size:17px;
	}
	
	#print-account-address {
		text-align:center;
		font-size:14px;
	}
	
	#print-entry-type {
		text-align:center;
		font-size:15px;
	}
	
	table#print-entry-table {
		border:1px solid #000000;
		border-collapse: collapse;
	}
	
	table#print-entry-table tr.tr-title {
		text-align:left;
		border:1px solid #000000;
		padding:5px 0 5px 2px;
	}
	
	table#print-entry-table tr.tr-title th {
		padding:5px 0 5px 5px;
	}
	
	table#print-entry-table td {
		padding:5px 0 5px 5px;
	}
	
	table#print-entry-table td.item {
		padding-right:35px;
	}
	
	table#print-entry-table td.last-item {
		padding-right:5px;
	}
	
	table#print-entry-table tr.tr-total {
		border:1px solid #000000;
	}
</style>

</head>
<body>
	<div id="print-account-name"><span class="value"><?php echo  $this->config->item('account_name'); ?></span></div>
	<div id="print-account-address"><span class="value"><?php echo $this->config->item('account_address'); ?></span></div>
	<br />
	<div id="print-entry-type"><span class="value"><?php echo $current_entry_type['name']; ?> Entry</span></div>
	<br />
	<div id="print-entry-number"><?php echo $current_entry_type['name']; ?> Entry Number : <span class="value"><?php echo full_entry_number($entry_type_id, $entry_number); ?></span></div>
	<div id="print-entry-number"><?php echo $current_entry_type['name']; ?> Entry Date : <span class="value"><?php echo $entry_date; ?></span></div>
	<br />
	<table id="print-entry-table">
		<thead>
			<tr class="tr-title"><th>Ledger Account</th><th>Dr Amount</th><th>Cr Amount</th></tr>
		</thead>
		<tbody>
		<?php
			$currency = $this->config->item('account_currency_symbol');
			foreach ($ledger_data as $id => $row)
			{
				echo "<tr class=\"tr-ledger\">";
				if ($row['dc'] == "D")
				{
					echo "<td class=\"ledger-name item\">By " . $row['name'] . "</td>";
				} else {
					echo "<td class=\"ledger-name item\">&nbsp;&nbsp;To " . $row['name'] . "</td>";
				}
				if ($row['dc'] == "D")
				{
					echo "<td class=\"ledger-dr item\">" . $currency . " " . $row['amount'] . "</td>";
					echo "<td class=\"ledger-cr last-item\"></td>";
				} else {
					echo "<td class=\"ledger-dr item\"></td>";
					echo "<td class=\"ledger-cr last-item\">" . $currency . " " . $row['amount'] . "</td>";
				}
				echo "</tr>";
			}
			echo "<tr class=\"tr-total\"><td class=\"total-name\">Total</td><td class=\"total-dr\">" . $currency . " " .  $entry_dr_total . "</td><td class=\"total-cr\">" . $currency . " " . $entry_cr_total . "</td></tr>";
		?>
		</tbody>
	</table>
	<br />
	<div id="print-entry-narration">Narration : <span class="value"><?php echo $entry_narration; ?></span></div>
	<br />
</body>
</html>
