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

	$income_total = -$income->total;
	$expense_total = $expense->total;

	$pandl = $income_total - $expense_total;

	echo "<tr style=\"background-color:#F8F8F8;\">";
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"vouchertable\" width=\"450\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Liability Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($liability->total) . "</td>";

	/* If Profit then Liability side, If Loss then Asset side */
	if ($pandl > 0)
	{
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Profit & Loss A/C</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur($pandl) . "</td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	}

	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur(($liability->total + $pandl)) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";

	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"vouchertable\" width=\"450\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Asset Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($asset->total) . "</td>";
	echo "</tr>";

	/* If Profit then Liability side, If Loss then Asset side */
	if ($pandl > 0)
	{
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	} else {
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Profit & Loss A/C</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur(-$pandl) . "</td>";
		echo "</tr>";
	}

	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($asset->total + (-$pandl)) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
?>
