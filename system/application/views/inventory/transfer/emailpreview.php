<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Email - <?php echo $current_voucher_type['name']; ?> Entry Number <?php echo full_entry_number($voucher_type_id, $cur_voucher->number); ?></title>
</head>
<body>
	<p><?php echo $this->config->item('account_name'); ?></p>
	<p><?php echo $this->config->item('account_address'); ?></p>
	<p><strong><?php echo $current_voucher_type['name']; ?> Entry</strong></p>
	<p><?php echo $current_voucher_type['name']; ?> Entry Number : <strong><?php echo full_entry_number($voucher_type_id, $cur_voucher->number); ?></strong></p>
	<p><?php echo $current_voucher_type['name']; ?> Entry Date : <strong><?php echo date_mysql_to_php_display($cur_voucher->date); ?></strong></p>

	<br />

	<h3>Source</h3>
	<table border=1 cellpadding=6>
		<thead>
			<tr><th align="left">Inventory Item</th><th>Quantity</th><th>Rate</th><th>Total</th></tr>
		</thead>
		<tbody>
		<?php
			$currency = $this->config->item('account_currency_symbol');
			$source_total = 0;
			foreach ($cur_voucher_source_inventory_items->result() as $row)
			{
				echo "<tr class=\"tr-inventory-item\">";
				echo "<td class=\"item\">" . $this->Inventory_Item_model->get_name($row->inventory_item_id) . "</td>";
				echo "<td class=\"item\">" . $row->quantity . "</td>";
				echo "<td class=\"item\">" . $row->rate_per_unit . "</td>";
				echo "<td class=\"last-item\">" . $row->total . "</td>";
				echo "</tr>";
				$source_total += $row->total;
			}
			echo "<tr class=\"tr-total\"><td class=\"total-name\" colspan=\"3\">Total</td><td class=\"total-amount\">" . $currency . " " .  $source_total . "</td></tr>";
		?>
		</tbody>
	</table>

	<br />

	<h3>Destination</h3>
	<table border=1 cellpadding=6>
		<thead>
			<tr><th align="left">Inventory Item</th><th>Quantity</th><th>Rate</th><th>Total</th></tr>
		</thead>
		<tbody>
		<?php
			$currency = $this->config->item('account_currency_symbol');
			$source_total = 0;
			foreach ($cur_voucher_dest_inventory_items->result() as $row)
			{
				echo "<tr class=\"tr-inventory-item\">";
				echo "<td class=\"item\">" . $this->Inventory_Item_model->get_name($row->inventory_item_id) . "</td>";
				echo "<td class=\"item\">" . $row->quantity . "</td>";
				echo "<td class=\"item\">" . $row->rate_per_unit . "</td>";
				echo "<td class=\"last-item\">" . $row->total . "</td>";
				echo "</tr>";
				$source_total += $row->total;
			}
			echo "<tr class=\"tr-total\"><td class=\"total-name\" colspan=\"3\">Total</td><td class=\"total-amount\">" . $currency . " " .  $source_total . "</td></tr>";
		?>
		</tbody>
	</table>

	<br />
	
	<p>Narration : <span class="value"><?php echo $cur_voucher->narration; ?></p>
	<br />
</body>
</html>
