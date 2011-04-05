<p>Entry Number : <span class="bold"><?php echo full_entry_number($entry_type_id, $cur_entry->number); ?></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Entry Date : <span class="bold"><?php echo date_mysql_to_php_display($cur_entry->date); ?></span>
</p>

<h3>Source</h3>
<table border=0 cellpadding=5 class="simple-table entry-view-table">
<thead><tr><th>Inventory Item</th><th>Quantity</th><th>Rate</th><th>Total</th></tr></thead>
<?php
$inventory_total = 0;
foreach ($cur_entry_source_inventory_items->result() as $row)
{
	echo "<tr>";
	echo "<td>" . $this->Inventory_Item_model->get_name($row->inventory_item_id) . "</td>";
	echo "<td>" . $row->quantity . "</td>";
	echo "<td>" . $row->rate_per_unit . "</td>";
	echo "<td>" . $row->total . "</td>";
	echo "</tr>";
	$inventory_total += $row->total;
}
?>
<tr class="entry-total"><td colspan=3><strong>Total</strong></td><td id="inventory-total"><?php echo convert_cur($inventory_total); ?></td></tr>
</table>

<h3>Destination</h3>
<table border=0 cellpadding=5 class="simple-table entry-view-table">
<thead><tr><th>Inventory Item</th><th>Quantity</th><th>Rate</th><th>Total</th></tr></thead>
<?php
$inventory_total = 0;
foreach ($cur_entry_dest_inventory_items->result() as $row)
{
	echo "<tr>";
	echo "<td>" . $this->Inventory_Item_model->get_name($row->inventory_item_id) . "</td>";
	echo "<td>" . $row->quantity . "</td>";
	echo "<td>" . $row->rate_per_unit . "</td>";
	echo "<td>" . $row->total . "</td>";
	echo "</tr>";
	$inventory_total += $row->total;
}
?>
<tr class="entry-total"><td colspan=3><strong>Total</strong></td><td id="inventory-total"><?php echo convert_cur($inventory_total); ?></td></tr>
</table>

<br />

<p>Narration :<br />
<span class="bold"><?php echo $cur_entry->narration; ?></span>
</p>
<p>
Tag : 
<?php
$cur_entry_tag = $this->Tag_model->show_entry_tag($cur_entry->tag_id);
if ($cur_entry_tag == "")
	echo "(None)";
else
	echo $cur_entry_tag;
?>
</p>
<?php
	echo anchor('entry/show/' . $current_entry_type['label'], 'Back', array('title' => 'Back to ' .  $current_entry_type['name'] . ' Entries'));
	echo " | ";
	echo anchor('inventory/transfer/edit/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Edit', array('title' => 'Edit ' . $current_entry_type['name'] . ' Entry'));
	echo " | ";
	echo anchor('inventory/transfer/delete/' . $current_entry_type['label'] . "/" . $cur_entry->id, 'Delete', array('class' => "confirmClick", 'title' => "Delete Entry", 'title' => 'Delete this ' . $current_entry_type['name'] . ' Entry'));
	echo " | ";
	echo anchor_popup('inventory/transfer/printpreview/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Print', array('title' => 'Print this ' . $current_entry_type['name'] . ' Entry', 'width' => '600', 'height' => '600'));
	echo " | ";
	echo anchor_popup('inventory/transfer/email/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Email', array('title' => 'Email this ' . $current_entry_type['name'] . ' Entry', 'width' => '400', 'height' => '200'));
	echo " | ";
	echo anchor('inventory/transfer/download/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Download', array('title' => "Download Entry", 'title' => 'Download this ' . $current_entry_type['name'] . ' Entry'));

