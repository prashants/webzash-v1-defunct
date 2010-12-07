<?php
	$this->load->library('accountlist');
	echo "<table>";
	echo "<tr valign=\"top\">";
	$asset = new Accountlist();
	echo "<td>";
	$asset->init(0);
	echo "<table border=0 cellpadding=5 class=\"simple-table account-table\">";
	echo "<thead><tr><th>Account Name</th><th>Type</th><th>O/P Balance</th><th>C/L Balance</th><th></th></tr></thead>";
	$asset->account_st_main(-1);
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";

