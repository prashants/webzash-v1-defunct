<?php
	$this->load->library('accountlist');
	echo "<table>";
	echo "<tr valign=\"top\">";

	$profit = new Accountlist();
	echo "<td>";
	$profit->init(3);
	echo "<table border=0 cellpadding=5 class=\"generaltable\">";
	echo "<thead><tr><th>Income</th><th>Amount</th></tr></thead>";
	$profit->account_st_short(0);
	echo "</table>";
	echo "</td>";

	$loss = new Accountlist();
	echo "<td>";
	$loss->init(4);
	echo "<table border=0 cellpadding=5 class=\"generaltable\">";
	echo "<thead><tr><th>Expenses</th><th>Amount</th></tr></thead>";
	$loss->account_st_short(0);
	echo "</table>";
	echo "</td>";

	echo "</tr>";
	echo "</table>";
?>
