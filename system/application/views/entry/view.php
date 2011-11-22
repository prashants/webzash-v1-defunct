<p>Entry Number : <span class="bold"><?php echo full_entry_number($entry_type_id, $cur_entry->number); ?></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Entry Date : <span class="bold"><?php echo date_mysql_to_php_display($cur_entry->date); ?></span>
</p>

<table border=0 cellpadding=5 class="simple-table entry-view-table">
<thead><tr><th>Type</th><th>Ledger Account</th><th>Dr Amount</th><th>Cr Amount</th></tr></thead>
<?php
$odd_even = "odd";
foreach ($cur_entry_ledgers->result() as $row)
{
	echo "<tr class=\"tr-" . $odd_even . "\">";
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
	$odd_even = ($odd_even == "odd") ? "even" : "odd";
}
?>
<tr class="entry-total"><td colspan=2><strong>Total</strong></td><td id=dr-total>Dr <?php echo $cur_entry->dr_total; ?></td><td id=cr-total">Cr <?php echo $cur_entry->cr_total; ?></td></tr>
<?php
if ($cur_entry->dr_total != $cur_entry->cr_total)
{
	$difference = $cur_entry->dr_total - $cur_entry->cr_total;
	if ($difference < 0)
		echo "<tr class=\"entry-difference\"><td colspan=2><strong>Difference</strong></td><td id=\"dr-diff\"></td><td id=\"cr-diff\">" . $cur_entry->cr_total . "</td></tr>";
	else
		echo "<tr class=\"entry-difference\"><td colspan=2><strong>Difference</strong></td><td id=\"dr-diff\">" .  $cur_entry->dr_total .  "</td><td id=\"cr-diff\"></td></tr>";
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
	echo anchor('entry/edit/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Edit', array('title' => 'Edit ' . $current_entry_type['name'] . ' Entry'));
	echo " | ";
	echo anchor('entry/delete/' . $current_entry_type['label'] . "/" . $cur_entry->id, 'Delete', array('class' => "confirmClick", 'title' => "Delete entry", 'title' => 'Delete this ' . $current_entry_type['name'] . ' Entry'));
	echo " | ";
	echo anchor_popup('entry/printpreview/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Print', array('title' => 'Print this ' . $current_entry_type['name'] . ' Entry', 'width' => '600', 'height' => '600'));
	echo " | ";
	echo anchor_popup('entry/email/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Email', array('title' => 'Email this ' . $current_entry_type['name'] . ' Entry', 'width' => '400', 'height' => '200'));
	echo " | ";
	echo anchor('entry/download/' .  $current_entry_type['label'] . "/" . $cur_entry->id, 'Download', array('title' => "Download entry", 'title' => 'Download this ' . $current_entry_type['name'] . ' Entry'));

