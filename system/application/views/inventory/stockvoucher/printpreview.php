<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Print - <?php echo $current_voucher_type['name']; ?> Voucher Number <?php echo $voucher_number; ?></title>
<?php echo link_tag(asset_url() . 'images/favicon.ico', 'shortcut icon', 'image/ico'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/printvoucher.css">
</head>
<body>
	<div id="print-account-name"><span class="value"><?php echo  $this->config->item('account_name'); ?></span></div>
	<div id="print-account-address"><span class="value"><?php echo $this->config->item('account_address'); ?></span></div>
	<br />
	<div id="print-voucher-type"><span class="value"><?php echo $current_voucher_type['name']; ?> Voucher</span></div>
	<br />
	<div id="print-voucher-number"><?php echo $current_voucher_type['name']; ?> Voucher Number : <span class="value"><?php echo full_voucher_number($voucher_type_id, $cur_voucher->number); ?></span></div>
	<div id="print-voucher-number"><?php echo $current_voucher_type['name']; ?> Voucher Date : <span class="value"><?php echo date_mysql_to_php_display($cur_voucher->date); ?></span></div>
	<br />

	<table id="print-voucher-table">
		<tr>
			<td align=\"right\">
				<?php
					if ($current_voucher_type['stock_voucher_type'] == '1')
						echo "Purchase Ledger";
					else
						echo "Sale Ledger";
				?>
			</td>
			<td>
				<?php
					$main_account = $cur_voucher_main_account->row();
					echo "<span class=\"bold\">" . $this->Ledger_model->get_name($main_account->ledger_id) . "</span>";
				?>
			</td>
		</tr>
		<tr>
			<td align=\"right\">
				<?php
					if ($current_voucher_type['stock_voucher_type'] == '1')
						echo "Creditor (Supplier)";
					else
						echo "Debtor (Customer)";
				?>
			</td>
			<td>
				<?php
					$main_entity = $cur_voucher_main_entity->row();
					echo "<span class=\"bold\">" . $this->Ledger_model->get_name($main_entity->ledger_id) . "</span>";
				?>
			</td>
		</tr>
	</table>

	<br />

	<table id="print-voucher-table">
		<thead>
			<tr class="tr-title"><th>Stock Item</th><th>Quantity</th><th>Rate</th><th>Discount</th><th>Total</th></tr>
		</thead>
		<tbody>
		<?php
			foreach ($cur_voucher_stock_items->result() as $row)
			{
				echo "<tr class=\"tr-stock-item\">";
				echo "<td class=\"item\">" . $this->Stock_Item_model->get_name($row->stock_item_id) . "</td>";
				echo "<td class=\"item\">" . $row->quantity . "</td>";
				echo "<td class=\"item\">" . $row->rate_per_unit . "</td>";
				echo "<td class=\"item\">" . $row->discount . "</td>";
				echo "<td class=\"last-item\">" . $row->total . "</td>";
				echo "</tr>";
			}
		?>
		</tbody>
	</table>

	<br />

	<table id="print-voucher-table">
		<thead>
			<tr class="tr-title"><th>Ledger Account</th><th>Rate</th><th>Dr Amount</th><th>Cr Amount</th></tr>
		</thead>
		<tbody>
		<?php
			$currency = $this->config->item('account_currency_symbol');
			foreach ($cur_voucher_ledgers->result() as $row)
			{
				echo "<tr class=\"tr-ledger\">";
				if ($row->dc == "D")
				{
					echo "<td class=\"item\">Dr " . $this->Ledger_model->get_name($row->ledger_id) . "</td>";
				} else {
					echo "<td class=\"item\">Cr " . $this->Ledger_model->get_name($row->ledger_id) . "</td>";
				}
				echo "<td class=\"item\">" . $row->stock_rate . "</td>";
				if ($row->dc == "D")
				{
					echo "<td class=\"item\">" . $currency . " " . $row->amount . "</td>";
					echo "<td class=\"item last-item\"></td>";
				} else {
					echo "<td class=\"item\"></td>";
					echo "<td class=\"item last-item\">" . $currency . " " . $row->amount . "</td>";
				}
				echo "</tr>";
			}
			echo "<tr class=\"tr-total\"><td class=\"total-name\" colspan=\"2\">Total</td><td class=\"total-dr\">" . $currency . " " .  $cur_voucher->dr_total . "</td><td class=\"total-cr\">" . $currency . " " . $cur_voucher->cr_total . "</td></tr>";
		?>
		</tbody>
	</table>
	<br />
	<div id="print-voucher-narration">Narration : <span class="value"><?php echo $cur_voucher->narration; ?></span></div>
	<br />
	<form>
	<input class="hide-print" type="button" onClick="window.print()" value="Print voucher">
	</form>
</body>
</html>
