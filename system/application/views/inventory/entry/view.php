<p>Entry Number : <span class="bold"><?php echo full_entry_number($entry_type_id, $cur_entry->number); ?></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Entry Date : <span class="bold"><?php echo date_mysql_to_php_display($cur_entry->date); ?></span>
</p>

<table border=0 cellpadding=2>
	<tr>
		<td align=\"right\">
			<?php
				if ($current_entry_type['inventory_entry_type'] == '1')
					echo "Purchase Ledger :";
				else
					echo "Sale Ledger :";
			?>
		</td>
		<td>
			<?php
				$main_account = $cur_entry_main_account->row();
				echo "<span class=\"bold\">" . $this->Ledger_model->get_name($main_account->ledger_id) . "</span>";
			?>
		</td>
	</tr>
	<tr>
		<td align=\"right\">
			<?php
				if ($current_entry_type['inventory_entry_type'] == '1')
					echo "Creditor (Supplier) :";
				else
					echo "Debtor (Customer) :";
			?>
		</td>
		<td>
			<?php
				$main_entity = $cur_entry_main_entity->row();
				echo "<span class=\"bold\">" . $this->Ledger_model->get_name($main_entity->ledger_id) . "</span>";
			?>
		</td>
	</tr>
</table>

<table border=0 cellpadding=5 class="simple-table entry-view-table">
<thead><tr><th>Inventory Item</th><th>Quantity</th><th>Rate</th><th>Discount</th><th>Total</th></tr></thead>
<?php
$inventory_total = 0;
foreach ($cur_entry_inventory_items->result() as $row)
{
	echo "<tr>";
	echo "<td>" . $this->Inventory_Item_model->get_name($row->inventory_item_id) . "</td>";
	echo "<td>" . $row->quantity . "</td>";
	echo "<td>" . $row->rate_per_unit . "</td>";
	echo "<td>" . $row->discount . "</td>";
	echo "<td>" . $row->total . "</td>";
	echo "</tr>";
	$inventory_total += $row->total;
}
?>
<tr class="entry-total"><td colspan=4><strong>Total</strong></td><td id="inventory-total"><?php echo convert_cur($inventory_total); ?></td></tr>
</table>

<br />

<table border=0 cellpadding=5 class="simple-table entry-view-table">
<thead><tr><th>Type</th><th>Ledger Account</th><th>Rate</th><th>Dr Amount</th><th>Cr Amount</th></tr></thead>
<?php
foreach ($cur_entry_ledgers->result() as $row)
{
	echo "<tr>";
	echo "<td>" . convert_dc($row->dc) . "</td>";
	echo "<td>" . $this->Ledger_model->get_name($row->ledger_id) . "</td>";
	echo "<td>" . $row->inventory_rate . "</td>";
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
<tr class="entry-total"><td colspan=3><strong>Total</strong></td><td id=dr-total>Dr <?php echo $cur_entry->dr_total; ?></td><td id=cr-total">Cr <?php echo $cur_entry->cr_total; ?></td></tr>
<?php
if ($cur_entry->dr_total != $cur_entry->cr_total)
{
	$difference = $cur_entry->dr_total - $cur_entry->cr_total;
	if ($difference < 0)
		echo "<tr class=\"entry-difference\"><td colspan=2><strong>Difference</strong></td><td id=\"dr-diff\"></td><td id=\"cr-diff\">" . convert_amount_dc($difference) . "</td></tr>";
	else
		echo "<tr class=\"entry-difference\"><td colspan=2><strong>Difference</strong></td><td id=\"dr-diff\">" . convert_amount_dc($difference) . "</td><td id=\"cr-diff\"></td></tr>";
}
?>
</table>
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
	echo anchor('inventory/entry/edit/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Edit', array('title' => 'Edit ' . $current_entry_type['name'] . ' Entry'));
	echo " | ";
	echo anchor('inventory/entry/delete/' . $current_entry_type['label'] . "/" . $cur_entry->id, 'Delete', array('class' => "confirmClick", 'title' => "Delete Entry", 'title' => 'Delete this ' . $current_entry_type['name'] . ' Entry'));
	echo " | ";
	echo anchor_popup('inventory/entry/printpreview/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Print', array('title' => 'Print this ' . $current_entry_type['name'] . ' Entry', 'width' => '600', 'height' => '600'));
	echo " | ";
	echo anchor_popup('inventory/entry/email/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Email', array('title' => 'Email this ' . $current_entry_type['name'] . ' Entry', 'width' => '400', 'height' => '200'));
	echo " | ";
	echo anchor('inventory/entry/download/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Download', array('title' => "Download Entry", 'title' => 'Download this ' . $current_entry_type['name'] . ' Entry'));

