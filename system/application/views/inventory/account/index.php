<?php
	/**************************** Inventory Units *****************************/
	echo "<table border=0 cellpadding=5 class=\"simple-table float-right\">";
	echo "<thead><tr><th>Inventory Unit</th><th>Symbol</th><th></th></tr></thead>";
	foreach ($inventory_units->result() as $row)
	{
		echo "<tr>";
		echo "<td>" . $row->name . "</td>";
		echo "<td>" . $row->symbol . "</td>";
		echo "<td>" . anchor('inventory/unit/edit/' . $row->id , "Edit", array('title' => 'Edit Inventory Unit', 'class' => 'red-link'));
		echo " &nbsp;";
		echo anchor('inventory/unit/delete/' . $row->id , img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Inventory Unit', 'class' => "confirmClick", 'title' => "Delete Inventory Unit")), array('title' => 'Delete Inventory Unit')) . "</td>";
		echo "</tr>";
		
	}
	echo "</table>";

	/******************************* Inventory Tree ***************************/
	echo "<table>";
	echo "<tr valign=\"top\">";
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table account-table\">";
	echo "<thead><tr><th>Name</th><th>Type</th><th>O/P Balance</th><th>C/L Balance</th><th></th></tr></thead>";
	inventorytree::print_tree($inventory_tree);
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
