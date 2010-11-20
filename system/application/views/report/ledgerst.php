<?php
$this->load->model('Ledger_model');
echo form_open('report/ledgerst/' . $ledger_id);
echo "<p>";
echo form_input_ledger('ledger_id', $ledger_id);
echo " ";
echo form_submit('submit', 'Show');
echo "</p>";
echo form_close();

/* Pagination */
$page_count = (int)$this->uri->segment(4);
$page_count = $this->input->xss_clean($page_count);
if ( ! $page_count) $page_count = "0";
$config['base_url'] = site_url('report/ledgerst/' . $ledger_id);
$config['num_links'] = 10;
$config['per_page'] = 10;
$config['uri_segment'] = 4;
$config['total_rows'] = $this->db->query('SELECT * FROM vouchers join voucher_items on vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND vouchers.draft = 0', array($ledger_id))->num_rows();
$config['full_tag_open'] = '<ul id="pagination-flickr">';
$config['full_close_open'] = '</ul>';
$config['num_tag_open'] = '<li>';
$config['num_tag_close'] = '</li>';
$config['cur_tag_open'] = '<li class="active">';
$config['cur_tag_close'] = '</li>';
$config['next_link'] = 'Next &#187;';
$config['next_tag_open'] = '<li class="next">';
$config['next_tag_close'] = '</li>';
$config['prev_link'] = '&#171; Previous';
$config['prev_tag_open'] = '<li class="previous">';
$config['prev_tag_close'] = '</li>';
$config['first_link'] = 'First';
$config['first_tag_open'] = '<li class="first">';
$config['first_tag_close'] = '</li>';
$config['last_link'] = 'Last';
$config['last_tag_open'] = '<li class="last">';
$config['last_tag_close'] = '</li>';
$this->pagination->initialize($config);

if ($ledger_id != 0)
{
$ledgerst_q = $this->db->query("SELECT vouchers.id as vid, vouchers.number as vnumber, vouchers.date as vdate, vouchers.draft as vdraft, vouchers.type as vtype, voucher_items.amount as lamount, voucher_items.dc as ldc FROM vouchers join voucher_items on vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND vouchers.draft = 0 ORDER BY vouchers.date ASC, vouchers.number ASC LIMIT ${page_count}, 10", array($ledger_id));

echo "<table border=0 cellpadding=5 class=\"generaltable\">";

echo "<thead><tr><th>Number</th><th>Date</th><th>Status</th><th>Type</th><th>Dr Amount</th><th>Cr Amount</th><th>  Balance</th></tr></thead>";
$odd_even = "odd";

$cur_balance = 0;
if ($page_count <= 0)
{
	list ($opbalance, $optype) = $this->Ledger_model->get_op_balance($ledger_id);
	if ($optype == "D")
	{
		echo "<tr class=\"tr-balance\"><td colspan=4>Opening Balance</td><td>" . convert_dc($optype) . " " . $opbalance . "</td><td></td><td></td></tr>";
		$cur_balance += $opbalance;
	} else {
		echo "<tr class=\"tr-balance\"><td colspan=4>Opening Balance</td><td></td><td>" . convert_dc($optype) . " " . $opbalance . "</td><td></td></tr>";
		$cur_balance -= $opbalance;
	}
} else {
	$prev_page_counter = $page_count - 1;

	/* Opening balance */
	list ($opbalance, $optype) = $this->Ledger_model->get_op_balance($ledger_id);
	if ($optype == "D")
	{
		$cur_balance += $opbalance;
	} else {
		$cur_balance -= $opbalance;
	}

	/* Previous Dr Total */
	if ($ledgerst_dr_closing_q = $this->db->query("SELECT SUM(dritems) as drtotal FROM (SELECT voucher_items.amount as dritems FROM vouchers join voucher_items on vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND vouchers.draft = 0 AND voucher_items.dc = 'D' ORDER BY vouchers.date ASC, vouchers.number ASC LIMIT 0, ${prev_page_counter}) as subquery", array($ledger_id)))
	{
		$ledgerst_dr_closing_d = $ledgerst_dr_closing_q->row();
		$ledgerst_dr_closing = $ledgerst_dr_closing_d->drtotal;
		if ($ledgerst_dr_closing > 0)
			$cur_balance += $ledgerst_dr_closing;
	}
//var_dump($cur_balance);
var_dump($ledgerst_dr_closing);
	/* Previous Cr Total */
	if ($ledgerst_cr_closing_q = $this->db->query("SELECT SUM(critems) as crtotal FROM (SELECT voucher_items.amount as critems FROM vouchers join voucher_items on vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? AND vouchers.draft = 0 AND voucher_items.dc = 'C' ORDER BY vouchers.date ASC, vouchers.number ASC LIMIT 0, ${prev_page_counter}) as subquery", array($ledger_id)))
	{
		$ledgerst_cr_closing_d = $ledgerst_cr_closing_q->row();
		$ledgerst_cr_closing = $ledgerst_cr_closing_d->crtotal;
		if ($ledgerst_cr_closing > 0)
			$cur_balance -= $ledgerst_cr_closing;
	}
var_dump($ledgerst_cr_closing);
	/* Show new current total */
	if ($cur_balance < 0)
	{
		echo "<tr class=\"tr-balance\"><td colspan=4>Opening Balance</td><td>Dr " . $cur_balance . "</td><td></td><td></td></tr>";
	} else {
		echo "<tr class=\"tr-balance\"><td colspan=4>Opening Balance</td><td></td><td>Cr " . $cur_balance . "</td><td></td></tr>";
	}
}

foreach ($ledgerst_q->result() as $row)
{
		echo "<tr class=\"tr-" . $odd_even;
		echo ($row->vdraft == 1) ? " tr-draft " : "";
		echo "\">";
		echo "<td>";
		echo $row->vid;
		echo "</td>";
		echo "<td>";
		echo date_mysql_to_php($row->vdate);
		echo "</td>";
		echo "<td>";
		echo ($row->vdraft == 1) ? "Draft" : "Active";
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
		if ($row->ldc == "D")
		{
			if ($row->vdraft == 0)
				$cur_balance += $row->lamount;
			echo "<td>";
			echo convert_dc($row->ldc);
			echo " ";
			echo $row->lamount;
			echo "</td>";
			echo "<td></td>";
		} else {
			if ($row->vdraft == 0)
				$cur_balance -= $row->lamount;
			echo "<td></td>";
			echo "<td>";
			echo convert_dc($row->ldc);
			echo " ";
			echo $row->lamount;
			echo "</td>";
		}
		echo "<td>";
		if ($row->vdraft == 0)
			echo ($cur_balance < 0) ? "Cr " . -$cur_balance :  "Dr " . $cur_balance;
		else
			echo "-";
		echo "</td>";
		echo "</tr>";
		$odd_even = ($odd_even == "odd") ? "even" : "odd";
}
/* Closing Balance */
if ($cur_balance < 0)
{
	echo "<tr class=\"tr-balance\"><td colspan=4>Closing Balance</td><td></td><td>Cr " .  -$cur_balance . "</td><td></td></tr>";
	$cur_balance += $opbalance;
} else {
	echo "<tr class=\"tr-balance\"><td colspan=4>Closing Balance</td><td>Dr " . $cur_balance . "<td></td></td><td></td></tr>";
	$cur_balance -= $opbalance;
}
echo "</table>";
}
?>

<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>
