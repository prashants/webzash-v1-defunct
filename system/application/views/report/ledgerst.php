<?php
$this->load->model('Ledger_model');
echo form_open('report/ledgerst/' . $ledger_id);
echo "<p>";
echo form_input_ledger('ledger_id', $ledger_id);
echo " ";
echo form_submit('submit', 'Show');
echo "</p>";
echo form_close();
if ($ledger_id != 0)
{
$ledgerst_q = $this->db->query("SELECT vouchers.id as vid, vouchers.number as vnumber, vouchers.date as vdate, vouchers.draft as vdraft, vouchers.type as vtype, voucher_items.amount as lamount, voucher_items.dc as ldc FROM vouchers join voucher_items on vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ?", array($ledger_id));
echo "<table border=0 cellpadding=5 class=\"generaltable\">";

echo "<thead><tr><th>Number</th><th>Date</th><th>Draft</th><th>Type</th><th></th><th>Amount</th></tr></thead>";
$odd_even = "odd";
foreach ($ledgerst_q->result() as $row)
{
		echo "<tr class=\"tr-" . $odd_even . "\">";
		echo "<td>";
		echo $row->vid;
		echo "</td>";
		echo "<td>";
		echo date_mysql_to_php($row->vdate);
		echo "</td>";
		echo "<td>";
		echo $row->vdraft;
		echo "</td>";
		echo "<td>";
		switch ($row->vtype)
		{
			case 1: echo "Receipt"; break;
			case 2: echo "Payment"; break;
			case 3: echo "Contra"; break;
			case 4: echo "Journal"; break;
		}
		echo "</td>";
		echo "<td>";
		echo convert_dc($row->ldc);
		echo "</td>";
		echo "<td>";
		echo $row->lamount;
		echo "</td>";
		/*
		echo "<td>";
		list ($opbal_amount, $opbal_type) = $this->Ledger_model->get_op_balance($ledger_id);
		if ($opbal_type == "C")
			echo "Cr " . $opbal_amount;
		else
			echo "Dr " . $opbal_amount;
		echo "</td>";
		echo "<td>";
		$dr_total = $this->Ledger_model->get_dr_total($ledger_id);
		if ($dr_total)
		{
			echo $dr_total;
			$temp_dr_total += $dr_total;
		}
		echo "</td>";
		echo "<td>";
		$cr_total = $this->Ledger_model->get_cr_total($ledger_id);
		if ($cr_total)
		{
			echo $cr_total;
			$temp_cr_total += $cr_total;
		}
		echo "</td>";*/
		echo "</tr>";
		$odd_even = ($odd_even == "odd") ? "even" : "odd";
}
echo "</table>";
}
?>
