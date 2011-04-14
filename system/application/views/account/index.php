<?php

	/******************************* Account Tree ***************************/
	echo "<table>";
	echo "<tr valign=\"top\">";
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table account-table\">";
	echo "<thead><tr><th>Name</th><th>Type</th><th>O/P Balance</th><th>C/L Balance</th><th></th></tr></thead>";
	accounttree::print_tree($account_tree);
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
