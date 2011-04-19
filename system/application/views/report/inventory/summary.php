<?php

	/******************************* Inventory Tree ***************************/
	echo "<table border=0 cellpadding=5 class=\"simple-table inventory-summary-table\">";
	echo "<thead><tr><th>Name</th><th>Type</th><th>C/L Quantity</th><th>C/L Rate</th><th>C/L Value</tr></thead>";
	inventorytree::print_report_tree($inventory_tree);
	echo "<tr class=\"tr-balance\"><td colspan=\"4\">Total</td><td>" . convert_cur(inventorytree::$total_value) . "</td></tr>";
	echo "</table>";

