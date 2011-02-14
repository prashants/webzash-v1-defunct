<p>Voucher Number : <span class="bold"><?php echo full_voucher_number($voucher_type_id, $cur_voucher->number); ?></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Voucher Date : <span class="bold"><?php echo date_mysql_to_php_display($cur_voucher->date); ?></span>
</p>

<table border=0 cellpadding=2>
	<tr>
		<td align=\"right\">
			<?php
				if ($current_voucher_type['stock_voucher_type'] == '1')
					echo "Purchase Ledger A/C :";
				else
					echo "Sale Ledger A/C :";
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
					echo "Creditor (Supplier) :";
				else
					echo "Debtor (Customer) :";
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

<table border=0 cellpadding=5 class="simple-table voucher-view-table">
<thead><tr><th>Stock Item</th><th>Quantity</th><th>Rate</th><th>Discount</th><th>Total</th></tr></thead>
<?php
foreach ($cur_voucher_stock_items->result() as $row)
{
	echo "<tr>";
	echo "<td>" . $this->Stock_Item_model->get_name($row->stock_item_id) . "</td>";
	echo "<td>" . $row->quantity . "</td>";
	echo "<td>" . $row->rate_per_unit . "</td>";
	echo "<td>" . $row->discount . "</td>";
	echo "<td>" . $row->total . "</td>";
	echo "</tr>";
}
?>
</table>

<br />

<table border=0 cellpadding=5 class="simple-table voucher-view-table">
<thead><tr><th>Type</th><th>Ledger A/C</th><th>Dr Amount</th><th>Cr Amount</th></tr></thead>
<?php
foreach ($cur_voucher_ledgers->result() as $row)
{
	echo "<tr>";
	echo "<td>" . convert_dc($row->dc) . "</td>";
	echo "<td>" . $this->Ledger_model->get_name($row->ledger_id) . "</td>";
	if ($row->dc == "D")
	{
		echo "<td>Dr " . $row->amount . "</td>";
		echo "<td></td>";
	} else {
		echo "<td></td>";
		echo "<td>Cr " . $row->amount . "</td>";
	}
	echo "</tr>";
}
?>
<tr class="voucher-total"><td colspan=2><strong>Total</strong></td><td id=dr-total>Dr <?php echo $cur_voucher->dr_total; ?></td><td id=cr-total">Cr <?php echo $cur_voucher->cr_total; ?></td></tr>
<?php
if ($cur_voucher->dr_total != $cur_voucher->cr_total)
{
	$difference = $cur_voucher->dr_total - $cur_voucher->cr_total;
	if ($difference < 0)
		echo "<tr class=\"voucher-difference\"><td colspan=2><strong>Difference</strong></td><td id=\"dr-diff\"></td><td id=\"cr-diff\">" . convert_amount_dc($difference) . "</td></tr>";
	else
		echo "<tr class=\"voucher-difference\"><td colspan=2><strong>Difference</strong></td><td id=\"dr-diff\">" . convert_amount_dc($difference) . "</td><td id=\"cr-diff\"></td></tr>";
}
?>
</table>
<p>Narration :<br />
<span class="bold"><?php echo $cur_voucher->narration; ?></span>
</p>
<p>
Tag : 
<?php
$cur_voucher_tag = $this->Tag_model->show_voucher_tag($cur_voucher->tag_id);
if ($cur_voucher_tag == "")
	echo "(None)";
else
	echo $cur_voucher_tag;
?>
</p>
<?php
	echo anchor('voucher/show/' . $current_voucher_type['label'], 'Back', array('title' => 'Back to ' .  $current_voucher_type['name'] . ' Vouchers'));
	echo " | ";
	echo anchor('inventory/stockvoucher/edit/' .  $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Edit', array('title' => 'Edit ' . $current_voucher_type['name'] . ' Voucher'));
	echo " | ";
	echo anchor('inventory/stockvoucher/delete/' . $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Delete', array('class' => "confirmClick", 'title' => "Delete voucher", 'title' => 'Delete this ' . $current_voucher_type['name'] . ' Voucher'));
	echo " | ";
	echo anchor_popup('inventory/stockvoucher/printpreview/' .  $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Print', array('title' => 'Print this ' . $current_voucher_type['name'] . ' Voucher', 'width' => '600', 'height' => '600'));
	echo " | ";
	echo anchor_popup('inventory/stockvoucher/email/' .  $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Email', array('title' => 'Email this ' . $current_voucher_type['name'] . ' Voucher', 'width' => '400', 'height' => '200'));
	echo " | ";
	echo anchor('inventory/stockvoucher/download/' .  $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Download', array('title' => "Download voucher", 'title' => 'Download this ' . $current_voucher_type['name'] . ' Voucher'));

