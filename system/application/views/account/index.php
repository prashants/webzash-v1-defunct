<?php
	$this->load->library('accountlist');

	echo "<table>";
	echo "<tr valign=\"top\">";

	$asset = new Accountlist();
	echo "<td>";
	$asset->init(0);
	echo "<table border=0 cellpadding=5 class=\"generaltable\">";
	echo "<thead><tr><th>Account Name</th><th>Type</th><th>O/P Balance</th><th>C/L Balance</th><th colspan=2>Actions</th></tr></thead>";
	$asset->account_st_main(-1);
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<br />";
	/* Total O/P Summary */
	if ($total_op != 0)
	{
		echo "<table border=0 cellpadding=5 class=\"generaltable\">";
		echo "<thead><tr><th colspan=2>Difference in O/P Balance</th></tr></thead>";
		if ($total_op > 0)
			echo "<tr class=\"tr-odd\"><td>Dr " . convert_cur($total_op) . "</td></tr>";
		else
			echo "<tr class=\"tr-odd\"><td>Cr " . convert_cur(-$total_op) . "</td></tr>";
		echo "</table>";
	}
?>
