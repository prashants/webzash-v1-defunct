<?php
	$tags_q = $this->db->get("tags");
	echo "<table border=0 cellpadding=5 class=\"generaltable\">";
	echo "<thead><tr><th>Title</th><th>Color</th><th colspan=5>Actions</th></tr></thead>";
	echo "<tbody>";
	$odd_even = "odd";
	foreach ($tags_q->result() as $row)
	{
		echo "<tr class=\"tr-" . $odd_even. "\">";
		echo "<td>" . $row->title . "</td>";
		echo "<td style=\"color:#" . $row->color . "; background-color:#" . $row->background . ";\">" . $row->title . "</td>";

		echo "<td>" . anchor('tag/edit/' . $row->id , img(array('src' => asset_url() . "images/icons/edit.png", 'border' => '0', 'alt' => 'Edit Tag')), array('title' => 'Edit Tag')) . "</td>";

		echo "<td>" . anchor('tag/delete/' . $row->id , img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Tag', 'class' => "confirmClick", 'title' => "Delete Tag")), array('title' => 'Delete  Tag')) . "</td>";
		echo "</tr>";
		$odd_even = ($odd_even == "odd") ? "even" : "odd";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<br />";
	echo anchor('setting', 'Back', array('title' => 'Back to Settings'));
?>
