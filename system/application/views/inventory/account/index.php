<?php
	echo "<table>";
	echo "<tr valign=\"top\">";
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table account-table\">";
	echo "<thead><tr><th>Account Name</th><th>Type</th><th>O/P Balance</th><th>C/L Balance</th><th></th></tr></thead>";
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	echo "<table border=0 cellpadding=5 class=\"simple-table float-right\">";
	echo "<thead><tr><th>Stock Unit</th><th>Symbol</th><th></th></tr></thead>";
	foreach ($stock_units->result() as $row)
	{
		echo "<tr>";
		echo "<td>" . $row->name . "</td>";
		echo "<td>" . $row->symbol . "</td>";
		echo "<td>" . anchor('inventory/stockunit/edit/' . $row->id , "Edit", array('title' => 'Edit Stock Unit', 'class' => 'red-link'));
		echo " &nbsp;";
		echo anchor('inventory/stockunit/delete/' . $row->id , img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete Stock Unit', 'class' => "confirmClick", 'title' => "Delete Stock Unit")), array('title' => 'Delete Stock Unit')) . "</td>";
		echo "</tr>";
		
	}
	echo "</table>";

