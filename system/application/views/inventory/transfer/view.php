<p>Voucher Number : <span class="bold"><?php echo full_voucher_number($voucher_type_id, $cur_voucher->number); ?></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Voucher Date : <span class="bold"><?php echo date_mysql_to_php_display($cur_voucher->date); ?></span>
</p>

<h3>Source</h3>
<table border=0 cellpadding=5 class="simple-table voucher-view-table">
<thead><tr><th>Stock Item</th><th>Quantity</th><th>Rate</th><th>Total</th></tr></thead>
<?php
$stock_total = 0;
foreach ($cur_voucher_source_stock_items->result() as $row)
{
	echo "<tr>";
	echo "<td>" . $this->Stock_Item_model->get_name($row->stock_item_id) . "</td>";
	echo "<td>" . $row->quantity . "</td>";
	echo "<td>" . $row->rate_per_unit . "</td>";
	echo "<td>" . $row->total . "</td>";
	echo "</tr>";
	$stock_total += $row->total;
}
?>
<tr class="voucher-total"><td colspan=3><strong>Total</strong></td><td id="stock-total"><?php echo convert_cur($stock_total); ?></td></tr>
</table>

<h3>Destination</h3>
<table border=0 cellpadding=5 class="simple-table voucher-view-table">
<thead><tr><th>Stock Item</th><th>Quantity</th><th>Rate</th><th>Total</th></tr></thead>
<?php
$stock_total = 0;
foreach ($cur_voucher_dest_stock_items->result() as $row)
{
	echo "<tr>";
	echo "<td>" . $this->Stock_Item_model->get_name($row->stock_item_id) . "</td>";
	echo "<td>" . $row->quantity . "</td>";
	echo "<td>" . $row->rate_per_unit . "</td>";
	echo "<td>" . $row->total . "</td>";
	echo "</tr>";
	$stock_total += $row->total;
}
?>
<tr class="voucher-total"><td colspan=3><strong>Total</strong></td><td id="stock-total"><?php echo convert_cur($stock_total); ?></td></tr>
</table>

<br />

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
	echo anchor('inventory/stocktransfer/edit/' .  $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Edit', array('title' => 'Edit ' . $current_voucher_type['name'] . ' Voucher'));
	echo " | ";
	echo anchor('inventory/stocktransfer/delete/' . $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Delete', array('class' => "confirmClick", 'title' => "Delete voucher", 'title' => 'Delete this ' . $current_voucher_type['name'] . ' Voucher'));
	echo " | ";
	echo anchor_popup('inventory/stocktransfer/printpreview/' .  $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Print', array('title' => 'Print this ' . $current_voucher_type['name'] . ' Voucher', 'width' => '600', 'height' => '600'));
	echo " | ";
	echo anchor_popup('inventory/stocktransfer/email/' .  $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Email', array('title' => 'Email this ' . $current_voucher_type['name'] . ' Voucher', 'width' => '400', 'height' => '200'));
	echo " | ";
	echo anchor('inventory/stocktransfer/download/' .  $current_voucher_type['label'] . "/" . $cur_voucher->id, 'Download', array('title' => "Download voucher", 'title' => 'Download this ' . $current_voucher_type['name'] . ' Voucher'));

