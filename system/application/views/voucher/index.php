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
		foreach ($voucher_data->result() as $row)
		{
			$current_voucher_type = entry_type_info($row->voucher_type);

			echo "<tr>";

			echo "<td>" . date_mysql_to_php_display($row->date) . "</td>";
			echo "<td>" . anchor('voucher/view/' . $current_voucher_type['label'] . "/" . $row->id, full_entry_number($row->voucher_type, $row->number), array('title' => 'View ' . $current_voucher_type['name'] . ' Voucher', 'class' => 'anchor-link-a')) . "</td>";

			echo "<td>";
			echo $this->Tag_model->show_voucher_tag($row->tag_id);
			echo $this->Ledger_model->get_voucher_name($row->id, $row->voucher_type);
			echo "</td>";

			echo "<td>" . $current_voucher_type['name'] . "</td>";
			echo "<td>" . $row->dr_total . "</td>";
			echo "<td>" . $row->cr_total . "</td>";

			echo "<td>" . anchor('voucher/edit/' . $current_voucher_type['label'] . "/" . $row->id , "Edit", array('title' => 'Edit ' . $current_voucher_type['name'] . ' Voucher', 'class' => 'red-link')) . " ";
			echo " &nbsp;" . anchor('voucher/delete/' . $current_voucher_type['label'] . "/" . $row->id , img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete ' . $current_voucher_type['name'] . ' Voucher', 'class' => "confirmClick", 'title' => "Delete voucher")), array('title' => 'Delete  ' . $current_voucher_type['name'] . ' Voucher')) . " ";
			echo " &nbsp;" . anchor_popup('voucher/printpreview/' . $current_voucher_type['label'] . "/" . $row->id , img(array('src' => asset_url() . "images/icons/print.png", 'border' => '0', 'alt' => 'Print ' . $current_voucher_type['name'] . ' Voucher')), array('title' => 'Print ' . $current_voucher_type['name']. ' Voucher', 'width' => '600', 'height' => '600')) . " ";
			echo " &nbsp;" . anchor_popup('voucher/email/' . $current_voucher_type['label'] . "/" . $row->id , img(array('src' => asset_url() . "images/icons/email.png", 'border' => '0', 'alt' => 'Email ' . $current_voucher_type['name'] . ' Voucher')), array('title' => 'Email ' . $current_voucher_type['name'] . ' Voucher', 'width' => '500', 'height' => '300')) . " ";
			echo " &nbsp;" . anchor('voucher/download/' . $current_voucher_type['label'] . "/" . $row->id , img(array('src' => asset_url() . "images/icons/save.png", 'border' => '0', 'alt' => 'Download ' . $current_voucher_type['name'] . ' Voucher', 'title' => "Download voucher")), array('title' => 'Download  ' . $current_voucher_type['name'] . ' Voucher')) . "</td>";

			echo "</tr>";
		}
	?>
	</tbody>
</table>

<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>

