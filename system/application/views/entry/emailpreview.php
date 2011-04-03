<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Email - <?php echo $current_entry_type['name']; ?> Entry Number <?php echo $entry_number; ?></title>
</head>
<body>
	<p><?php echo $this->config->item('account_name'); ?></p>
	<p><?php echo $this->config->item('account_address'); ?></p>
	<p><strong><?php echo $current_entry_type['name']; ?> Entry</strong></p>
	<p><?php echo $current_entry_type['name']; ?> Entry Number : <strong><?php echo full_entry_number($entry_type_id, $entry_number); ?></strong></p>
	<p><?php echo $current_entry_type['name']; ?> Entry Date : <strong><?php echo $entry_date; ?></strong></p>
	<table border=1 cellpadding=6>
		<thead>
			<tr><th align="left">Ledger Account</th><th>Dr Amount</th><th>Cr Amount</th></tr>
		</thead>
		<tbody>
		<?php
			$currency = $this->config->item('account_currency_symbol');
			foreach ($ledger_data as $id => $row)
			{
				echo "<tr>";
				if ($row['dc'] == "D")
				{
					echo "<td>By " . $row['name'] . "</td>";
				} else {
					echo "<td>&nbsp;&nbsp;To " . $row['name'] . "</td>";
				}
				if ($row['dc'] == "D")
				{
					echo "<td>" . $currency . " " . $row['amount'] . "</td>";
					echo "<td></td>";
				} else {
					echo "<td></td>";
					echo "<td>" . $currency . " " . $row['amount'] . "</td>";
				}
				echo "</tr>";
			}
			echo "<tr><td>Total</td><td>" . $currency . " " .  $entry_dr_total . "</td><td>" . $currency . " " . $entry_cr_total . "</td></tr>";
		?>
		</tbody>
	</table>
	<br />
	<p>Narration : <span class="value"><?php echo $entry_narration; ?></p>
	<br />
</body>
</html>
