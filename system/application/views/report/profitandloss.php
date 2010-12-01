<?php
	$this->load->library('accountlist');
	echo "<table>";
	echo "<tr valign=\"top\">";

	$expense = new Accountlist();
	echo "<td>";
	$expense->init(4);
	echo "<table border=0 cellpadding=5 class=\"generaltable\" width=\"450\">";
	echo "<thead><tr><th>Expenses</th><th>Amount</th></tr></thead>";
	$expense->account_st_short(0);
	echo "</table>";
	echo "</td>";
	$expense_total = $expense->total;

	$income = new Accountlist();
	echo "<td>";
	$income->init(3);
	echo "<table border=0 cellpadding=5 class=\"generaltable\" width=\"450\">";
	echo "<thead><tr><th>Income</th><th>Amount</th></tr></thead>";
	$income->account_st_short(0);
	echo "</table>";
	echo "</td>";
	$income_total = -$income->total;

	echo "</tr>";

	$pandl = $income_total - $expense_total;

	echo "<tr style=\"background-color:#F8F8F8;\">";
	echo "<td>";

	/* Expense side */

	$total = $expense_total;

	echo "<table border=0 cellpadding=5 class=\"vouchertable\" width=\"450\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Total Expenses</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($expense_total) . "</td>";
	if ($pandl > 0)
	{
		$total += $pandl;
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Net Profit</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur($pandl) . "</td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";

	}
	echo "<tr valign=\"top\" class=\"tr-balance\">";
	echo "<td class=\"bold\">Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($total) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";

	/* Income side */

	$total = $income_total;

	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"vouchertable\" width=\"450\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Total Income</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($income_total) . "</td>";
	echo "</tr>";
	if ($pandl > 0)
	{
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	} else {
		$total += -$pandl;
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Net Loss</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur(-$pandl) . "</td>";
		echo "</tr>";
	}
	echo "<tr valign=\"top\" class=\"tr-balance\">";
	echo "<td class=\"bold\">Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($total) . "</td>";
	echo "</tr>";
	echo "</table>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
?>
