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
$config['total_rows'] = $this->db->query('SELECT * FROM vouchers join voucher_items on vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ?', array($ledger_id))->num_rows();
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
$ledgerst_q = $this->db->query("SELECT vouchers.id as vid, vouchers.number as vnumber, vouchers.date as vdate, vouchers.draft as vdraft, vouchers.type as vtype, voucher_items.amount as lamount, voucher_items.dc as ldc FROM vouchers join voucher_items on vouchers.id = voucher_items.voucher_id WHERE voucher_items.ledger_id = ? ORDER BY vouchers.date DESC, vouchers.number DESC LIMIT ${page_count}, 10", array($ledger_id));

echo "<table border=0 cellpadding=5 class=\"generaltable\">";

echo "<thead><tr><th>Number</th><th>Date</th><th>Status</th><th>Type</th><th></th><th>Amount</th></tr></thead>";
$odd_even = "odd";
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
		echo "<td>";
		echo convert_dc($row->ldc);
		echo "</td>";
		echo "<td>";
		echo $row->lamount;
		echo "</td>";
		echo "</tr>";
		$odd_even = ($odd_even == "odd") ? "even" : "odd";
}
echo "</table>";
}
?>

<div id="pagination-container"><?php echo $this->pagination->create_links(); ?></div>
