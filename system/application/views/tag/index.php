<?php
	$this->db->from('tags')->order_by('title', 'asc');
	$tags_q = $this->db->get();
	echo "<table border=0 cellpadding=5 class=\"simple-table tag-table\">";
	echo "<thead><tr><th>Title</th><th>Color</th><th></th></tr></thead>";
	echo "<tbody>";
	$odd_even = "odd";
	foreach ($tags_q->result() as $row)
	{
		echo "<tr class=\"tr-" . $odd_even. "\">";
		echo "<td>" . $row->title . "</td>";
		echo "<td>" . $this->Tag_model->show_entry_tag($row->id) . "</td>";

		echo "<td>" . anchor('tag/edit/' . $row->id , "Edit", array('title' => 'Edit Tag', 'class' => 'red-link'));
		echo " &nbsp;";
		echo anchor('tag/delete/' . $row->id , img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Tag', 'class' => "confirmClick", 'title' => "Delete Tag")), array('title' => 'Delete  Tag')) . "</td>";
		echo "</tr>";
		$odd_even = ($odd_even == "odd") ? "even" : "odd";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<br />";
	echo anchor('setting', 'Back', array('title' => 'Back to Settings'));

