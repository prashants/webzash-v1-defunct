<?php
	$this->load->library('accountlist');

	echo "<table border=0>";
	echo "<tr valign=\"top\">";

	$liability = new Accountlist();
	echo "<td>";
	$liability->init(2);
	echo "<table border=0 cellpadding=5 class=\"generaltable\" width=\"450\">";
	echo "<thead><tr><th>Liabilities</th><th>Amount</th></tr></thead>";
	$liability->account_st_short(0);
	echo "</table>";
	echo "</td>";

	$asset = new Accountlist();
	echo "<td>";
	$asset->init(1);
	echo "<table border=0 cellpadding=5 class=\"generaltable\" width=\"450\">";
	echo "<thead><tr><th>Assets</th><th>Amount</th></tr></thead>";
	$asset->account_st_short(0);
	echo "</table>";
	echo "</td>";

	echo "</tr>";

	$income = new Accountlist();
	$income->init(3);
	$expense = new Accountlist();
	$expense->init(4);
	$pandl = $income->total - $expense->total;

	echo "<tr>";
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"vouchertable\" width=\"450\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">LIABILITY TOTAL</td>";
	echo "<td align=\"right\" class=\"bold\">" . $liability->total . "</td>";
	if ($pandl < 0)
	{
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">LOSS B/F</td>";
		echo "<td align=\"right\" class=\"bold\">" . -$pandl . "</td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";

	}
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">TOTAL</td>";
	echo "<td align=\"right\" class=\"bold\">" . ($liability->total + $expense->total) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";

	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"vouchertable\" width=\"450\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">ASSET TOTAL</td>";
	echo "<td align=\"right\" class=\"bold\">" . $asset->total . "</td>";
	echo "</tr>";
	if ($pandl > 0)
	{
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">PROFIT B/F</td>";
		echo "<td align=\"right\" class=\"bold\">" . $pandl . "</td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";

	}
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">TOTAL</td>";
	echo "<td align=\"right\" class=\"bold\">" . ($asset->total + $income->total) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";
	echo "</table>";
?>
