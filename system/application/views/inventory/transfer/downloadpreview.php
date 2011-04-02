<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php $current_voucher_type['name']; ?> Entry Number <?php echo $voucher_number; ?></title>

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

	#print-voucher-type {
		text-align:center;
		font-size:15px;
	}

	table.print-voucher-table {
		border:1px solid #000000;
		border-collapse: collapse;
	}

	table.print-voucher-table tr.tr-title {
		text-align:left;
		border:1px solid #000000;
		padding:5px 0 5px 2px;
	}

	table.print-voucher-table tr.tr-title th {
		padding:5px 0 5px 5px;
	}

	table.print-voucher-table td {
		padding:5px 0 5px 5px;
	}

	table.print-voucher-table td.item {
		padding-right:35px;
	}

	table.print-voucher-table td.last-item {
		padding-right:5px;
	}

	table.print-voucher-table tr.tr-total {
		border:1px solid #000000;
	}
</style>

</head>
<body>
	<div id="print-account-name"><span class="value"><?php echo  $this->config->item('account_name'); ?></span></div>
	<div id="print-account-address"><span class="value"><?php echo $this->config->item('account_address'); ?></span></div>
	<br />
	<div id="print-voucher-type"><span class="value"><?php echo $current_voucher_type['name']; ?> Entry</span></div>
	<br />
	<div id="print-voucher-number"><?php echo $current_voucher_type['name']; ?> Entry Number : <span class="value"><?php echo full_entry_number($voucher_type_id, $cur_voucher->number); ?></span></div>
	<div id="print-voucher-number"><?php echo $current_voucher_type['name']; ?> Entry Date : <span class="value"><?php echo date_mysql_to_php_display($cur_voucher->date); ?></span></div>
	<br />

	<h3>Source</h3>
	<table class="print-voucher-table">
		<thead>
			<tr class="tr-title"><th>Inventory Item</th><th>Quantity</th><th>Rate</th><th>Total</th></tr>
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
	<table class="print-voucher-table">
		<thead>
			<tr class="tr-title"><th>Inventory Item</th><th>Quantity</th><th>Rate</th><th>Total</th></tr>
		</thead>
		<tbody>
		<?php
			$dest_total = 0;
			foreach ($cur_voucher_dest_inventory_items->result() as $row)
			{
				echo "<tr class=\"tr-inventory-item\">";
				echo "<td class=\"item\">" . $this->Inventory_Item_model->get_name($row->inventory_item_id) . "</td>";
				echo "<td class=\"item\">" . $row->quantity . "</td>";
				echo "<td class=\"item\">" . $row->rate_per_unit . "</td>";
				echo "<td class=\"last-item\">" . $row->total . "</td>";
				echo "</tr>";
				$dest_total += $row->total;
			}
			echo "<tr class=\"tr-total\"><td class=\"total-name\" colspan=\"3\">Total</td><td class=\"total-amount\">" . $currency . " " .  $dest_total . "</td></tr>";
		?>
		</tbody>
	</table>

	<br />
	<div id="print-voucher-narration">Narration : <span class="value"><?php echo $cur_voucher->narration; ?></span></div>
	<br />
</body>
</html>
