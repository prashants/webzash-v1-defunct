<table border=0 cellpadding=5 class="simple-table">
	<thead>
		<tr>
			<th>Label</th>
			<th>Name</th>
			<th>Description</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach ($voucher_type_data->result() as $row)
		{
			echo "<tr>";

			echo "<td>" . $row->label . "</td>";
			echo "<td>" . $row->name . "</td>";

			echo "<td>" . $row->description . "</td>";
			echo "<td>" . anchor('voucher/edit/' . $row->id , "Edit", array('title' => 'Edit ' . $row->name . ' Voucher Type', 'class' => 'red-link')) . " ";
			echo " &nbsp;" . anchor('voucher/delete/' . $row->id , img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete ' . $row->name . ' Voucher Type', 'class' => "confirmClick", 'title' => "Delete Voucher Type")), array('title' => 'Delete ' . $row->name . ' Voucher Type')) . " ";
			echo "</tr>";
		}
	?>
	</tbody>
</table>
