<?php
	$this->load->library('accountlist');
	echo "<table>";
	echo "<tr valign=\"top\">";

	/**********************************************************************/
	/*********************** GROSS CALCULATIONS ***************************/
	/**********************************************************************/

	/* Gross P/L : Expenses */
	$gross_expense_total = 0;
	$this->db->from('groups')->where('parent_id', 4)->where('affects_gross', 1);
	$gross_expense_list_q = $this->db->get();
	echo "<td width=\"" . $left_width . "\">";
	echo "<table border=0 cellpadding=5 class=\"simple-table profit-loss-table\" width=\"100%\">";
	echo "<thead><tr><th>Expenses (Gross)</th><th align=\"right\">Amount</th></tr></thead>";
	foreach ($gross_expense_list_q->result() as $row)
	{
		$gross_expense = new Accountlist();
		$gross_expense->init($row->id);
		$gross_expense->account_st_short(0);
		$gross_expense_total = float_ops($gross_expense_total, $gross_expense->total, '+');
	}
	echo "</table>";
	echo "</td>";

	/* Gross P/L : Incomes */
	$gross_income_total = 0;
	$this->db->from('groups')->where('parent_id', 3)->where('affects_gross' , 1);
	$gross_income_list_q = $this->db->get();
	echo "<td width=\"" . $right_width . "\">";
	echo "<table border=0 cellpadding=5 class=\"simple-table profit-loss-table\" width=\"100%\">";
	echo "<thead><tr><th>Incomes (Gross)</th><th align=\"right\">Amount</th></tr></thead>";
	foreach ($gross_income_list_q->result() as $row)
	{
		$gross_income = new Accountlist();
		$gross_income->init($row->id);
		$gross_income->account_st_short(0);
		$gross_income_total = float_ops($gross_income_total, $gross_income->total, '+');
	}
	echo "</table>";
	echo "</td>";
	$gross_income_total = -$gross_income_total; /* Converting to positive value since Cr */

	echo "</tr>";

	/* Calculating Gross P/L */
	$grosspl = float_ops($gross_income_total, $gross_expense_total, '-');

	/* Showing Gross P/L : Expenses */
	$grosstotal = $gross_expense_total;
	echo "<tr valign=\"top\" class=\"total-area\">";
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table profit-loss-total-table\" width=\"100%\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Total Gross Expenses</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($gross_expense_total) . "</td>";
	echo "</tr>";
	if ($grosspl > 0)
	{
		$grosstotal = float_ops($grosstotal, $grosspl, '+');
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Gross Profit C/O</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur($grosspl) . "</td>";
		echo "</tr>";
	} else if ($grosspl < 0) {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";

	}
	echo "<tr valign=\"top\" class=\"tr-balance\">";
	echo "<td class=\"bold\">Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($grosstotal) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";

	/* Showing Gross P/L : Incomes  */
	$grosstotal = $gross_income_total;
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table profit-loss-total-table\" width=\"100%\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Total Gross Incomes</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($gross_income_total) . "</td>";
	echo "</tr>";
	if ($grosspl > 0)
	{
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	} else if ($grosspl < 0) {
		$grosstotal = float_ops($grosstotal, -$grosspl, '+');
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Gross Loss C/O</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur(-$grosspl) . "</td>";
		echo "</tr>";
	}
	echo "<tr valign=\"top\" class=\"tr-balance\">";
	echo "<td class=\"bold\">Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($grosstotal) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";
	echo "</tr>";

	echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";

	/**********************************************************************/
	/************************* NET CALCULATIONS ***************************/
	/**********************************************************************/

	/* Net P/L : Expenses */
	$net_expense_total = 0;
	$this->db->from('groups')->where('parent_id', 4)->where('affects_gross !=', 1);
	$net_expense_list_q = $this->db->get();
	echo "<tr valign=\"top\">";
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table profit-loss-table\" width=\"100%\">";
	echo "<thead><tr><th>Expenses (Net)</th><th align=\"right\">Amount</th></tr></thead>";
	foreach ($net_expense_list_q->result() as $row)
	{
		$net_expense = new Accountlist();
		$net_expense->init($row->id);
		$net_expense->account_st_short(0);
		$net_expense_total = float_ops($net_expense_total, $net_expense->total, '+');
	}
	echo "</table>";
	echo "</td>";

	/* Net P/L : Incomes */
	$net_income_total = 0;
	$this->db->from('groups')->where('parent_id', 3)->where('affects_gross !=', 1);
	$net_income_list_q = $this->db->get();
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table profit-loss-table\" width=\"100%\">";
	echo "<thead><tr><th>Incomes (Net)</th><th align=\"right\">Amount</th></tr></thead>";
	foreach ($net_income_list_q->result() as $row)
	{
		$net_income = new Accountlist();
		$net_income->init($row->id);
		$net_income->account_st_short(0);
		$net_income_total = float_ops($net_income_total, $net_income->total, '+');
	}
	echo "</table>";
	echo "</td>";
	$net_income_total = -$net_income_total; /* Converting to positive value since Cr */

	echo "</tr>";

	/* Calculating Net P/L */
	$netpl = float_ops(float_ops($net_income_total, $net_expense_total, '-'), $grosspl, '+');

	/* Showing Net P/L : Expenses */
	$nettotal = $net_expense_total;
	echo "<tr valign=\"top\" class=\"total-area\">";
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table profit-loss-total-table\" width=\"100%\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Total Expenses</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($nettotal) . "</td>";
	echo "</tr>";
	if ($grosspl > 0)
	{
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	} else if ($grosspl < 0) {
		$nettotal = float_ops($nettotal, -$grosspl, '+');
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Gross Loss B/F</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur(-$grosspl) . "</td>";
		echo "</tr>";
	}
	if ($netpl > 0)
	{
		$nettotal = float_ops($nettotal, $netpl, '+');
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Net Profit</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur($netpl) . "</td>";
		echo "</tr>";
	} else if ($netpl < 0) {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";

	}
	echo "<tr valign=\"top\" class=\"tr-balance\">";
	echo "<td class=\"bold\">Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($nettotal) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";

	/* Showing Net P/L : Incomes */
	$nettotal = $net_income_total;
	echo "<td>";
	echo "<table border=0 cellpadding=5 class=\"simple-table profit-loss-total-table\" width=\"100%\">";
	echo "<tr valign=\"top\">";
	echo "<td class=\"bold\">Total Incomes</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($nettotal) . "</td>";
	echo "</tr>";
	if ($grosspl > 0)
	{
		$nettotal = float_ops($nettotal, $grosspl, '+');
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Gross Profit B/F</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur($grosspl) . "</td>";
		echo "</tr>";

	} else if ($grosspl < 0) {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	}
	if ($netpl > 0)
	{
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	} else if ($netpl < 0) {
		$nettotal = float_ops($nettotal, -$netpl, '+');
		echo "<tr valign=\"top\">";
		echo "<td class=\"bold\">Net Loss</td>";
		echo "<td align=\"right\" class=\"bold\">" . convert_cur(-$netpl) . "</td>";
		echo "</tr>";
	}
	echo "<tr valign=\"top\" class=\"tr-balance\">";
	echo "<td class=\"bold\">Total</td>";
	echo "<td align=\"right\" class=\"bold\">" . convert_cur($nettotal) . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</td>";

	echo "</tr>";
	echo "</table>";

