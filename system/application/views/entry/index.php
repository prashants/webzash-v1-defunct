<div id="tag-sidebar">
	<?php $this->load->view('sidebar/tag', $tag_id); ?>
</div>

<table border=0 cellpadding=5 class="simple-table">
	<thead>
		<tr>
			<th>Date</th>
			<th>No</th>
			<th>Ledger Account</th>
			<th>Type</th>
			<th>DR Amount</th>
			<th>CR Amount</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach ($entry_data->result() as $row)
		{
			$current_entry_type = entry_type_info($row->entry_type);

			echo "<tr>";

			echo "<td>" . date_mysql_to_php_display($row->date) . "</td>";
			echo "<td>" . anchor('entry/view/' . $current_entry_type['label'] . "/" . $row->id, full_entry_number($row->entry_type, $row->number), array('title' => 'View ' . $current_entry_type['name'] . ' Entry', 'class' => 'anchor-link-a')) . "</td>";

			echo "<td>";
			echo $this->Tag_model->show_entry_tag($row->tag_id);
			echo $this->Ledger_model->get_entry_name($row->id, $row->entry_type);
			echo "</td>";

			echo "<td>" . $current_entry_type['name'] . "</td>";
			echo "<td>" . $row->dr_total . "</td>";
			echo "<td>" . $row->cr_total . "</td>";

			echo "<td>" . anchor('entry/edit/' . $current_entry_type['label'] . "/" . $row->id , "Edit", array('title' => 'Edit ' . $current_entry_type['name'] . ' Entry', 'class' => 'red-link')) . " ";
			echo " &nbsp;" . anchor('entry/delete/' . $current_entry_type['label'] . "/" . $row->id , img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete ' . $current_entry_type['name'] . ' Entry', 'class' => "confirmClick", 'title' => "Delete entry")), array('title' => 'Delete  ' . $current_entry_type['name'] . ' Entry')) . " ";
			echo " &nbsp;" . anchor_popup('entry/printpreview/' . $current_entry_type['label'] . "/" . $row->id , img(array('src' => asset_url() . "images/icons/print.png", 'border' => '0', 'alt' => 'Print ' . $current_entry_type['name'] . ' Entry')), array('title' => 'Print ' . $current_entry_type['name']. ' Entry', 'width' => '600', 'height' => '600')) . " ";
			echo " &nbsp;" . anchor_popup('entry/email/' . $current_entry_type['label'] . "/" . $row->id , img(array('src' => asset_url() . "images/icons/email.png", 'border' => '0', 'alt' => 'Email ' . $current_entry_type['name'] . ' Entry')), array('title' => 'Email ' . $current_entry_type['name'] . ' Entry', 'width' => '500', 'height' => '300')) . " ";
			echo " &nbsp;" . anchor('entry/download/' . $current_entry_type['label'] . "/" . $row->id , img(array('src' => asset_url() . "images/icons/save.png", 'border' => '0', 'alt' => 'Download ' . $current_entry_type['name'] . ' Entry', 'title' => "Download entry")), array('title' => 'Download  ' . $current_entry_type['name'] . ' Entry')) . "</td>";

			echo "</tr>";
		}
	?>
	</tbody>
</table>

<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>

