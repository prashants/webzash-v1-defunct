<?php
	$this->load->library('accountlist');

	echo "<table>";
	echo "<tr valign=\"top\">";

	$asset = new Accountlist();
	echo "<td>";
	$asset->init(1);
	echo "<table border=0 cellpadding=5 class=\"generaltable\">";
	echo "<thead><tr><th>Assets</th><th>Amount</th></tr></thead>";
	$asset->travel_group(0);
	echo "</table>";
	echo "</td>";

	$liability = new Accountlist();
	echo "<td>";
	$liability->init(2);
	echo "<table border=0 cellpadding=5 class=\"generaltable\">";
	echo "<thead><tr><th>Liabilities</th><th>Amount</th></tr></thead>";
	$liability->travel_group(0);
	echo "</table>";
	echo "</td>";

	echo "</tr>";
	echo "</table>";
?>
