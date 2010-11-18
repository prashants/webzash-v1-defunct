<?php
	$this->load->library('accountlist');
	echo "<table>";
	echo "<tr valign=\"top\">";

	$expense = new Accountlist();
	echo "<td>";
	$expense->init(4);
	echo "<table border=0 cellpadding=5 class=\"generaltable\" width=\"450\">";
	echo "<thead><tr><th>Expense</th><th>Amount</th></tr></thead>";
	$expense->account_st_short(0);
	echo "</table>";
	echo "</td>";

	$income = new Accountlist();
	echo "<td>";
	$income->init(3);
	echo "<table border=0 cellpadding=5 class=\"generaltable\" width=\"450\">";
	echo "<thead><tr><th>Income</th><th>Amount</th></tr></thead>";
	$income->account_st_short(0);
	echo "</table>";
	echo "</td>";

	echo "</tr>";

	$pandl = $income->total - $expense->total;

	echo "<tr style=\"background-color:#F8F8F8;\">";
	echo "<td>";

	echo "<table border=0 cellpadding=5 class=\"vouchertable\" width=\"450\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">EXPENSE TOTAL</td>";
	echo "<td align=\"right\" class=\"bold\">" . $expense->total . "</td>";
	if ($pandl < 0)
	{
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">LOSS C/F TO BALANCE SHEET</td>";
		echo "<td align=\"right\" class=\"bold\">" . -$pandl . "</td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";

	}
	echo "</table>";

	echo "</td>";
	echo "<td>";

	echo "<table border=0 cellpadding=5 class=\"vouchertable\" width=\"450\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">INCOME TOTAL</td>";
	echo "<td align=\"right\" class=\"bold\">" . $income->total . "</td>";
	echo "</tr>";
	if ($pandl > 0)
	{
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">PROFIT C/F TO BALANCE SHEET</td>";
		echo "<td align=\"right\" class=\"bold\">" . $pandl . "</td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";

	}
	echo "</table>";

	echo "</td>";
	echo "</tr>";

	echo "</table>";
?>
