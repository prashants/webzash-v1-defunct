<?php
class Voucher extends Controller {

	function Voucher()
	{
		parent::Controller();
		$this->load->model('Voucher_model');
	}

	function index()
	{
		redirect('voucher/show/all');
		return;
	}

	function show($voucher_type)
	{
		$page_data['page_links'] = array(
			'voucher/show/all' => 'All',
			'voucher/show/receipt' => 'Receipt',
			'voucher/show/payment' => 'Payment',
			'voucher/show/contra' => 'Contra',
			'voucher/show/journal' => 'Journal',
		);
		switch ($voucher_type)
		{
		case 'all' :
			$page_data['page_title'] = "All Vouchers";
			$data['voucher_type'] = "";
			$data['voucher_table'] = $this->_show_voucher();
			break;
		case 'receipt' :
			$page_data['page_title'] = "Receipt Vouchers";
			$data['voucher_type'] = "receipt";
			$data['voucher_table'] = $this->_show_voucher(1);
			break;
		case 'payment' :
			$page_data['page_title'] = "Payment Vouchers";
			$data['voucher_type'] = "payment";
			$data['voucher_table'] = $this->_show_voucher(2);
			break;
		case 'contra' :
			$page_data['page_title'] = "Contra Vouchers";
			$data['voucher_type'] = "contra";
			$data['voucher_table'] = $this->_show_voucher(3);
			break;
		case 'journal' :
			$page_data['page_title'] = "Journal Vouchers";
			$data['voucher_type'] = "journal";
			$data['voucher_table'] = $this->_show_voucher(4);
			break;
		default :
			$this->session->set_flashdata('error', "Invalid voucher type");
			redirect('voucher/show/all');
			return;
			break;
		}
		$this->load->view('template/header', $page_data);
		$this->load->view('voucher/index', $data);
		$this->load->view('template/footer');
		return;
	}

	function _show_voucher($voucher_type = NULL)
	{
		if ($voucher_type > 5)
		{
			$this->session->set_flashdata('error', "Invalid voucher type");
			redirect('voucher/show/all');
			return;
		} else if ($voucher_type > 0) {
			$voucher_q = $this->db->query('SELECT * FROM vouchers WHERE type = ? ORDER BY date DESC ', array($voucher_type));
		} else {
			$voucher_q = $this->db->query('SELECT * FROM vouchers ORDER BY date DESC');
		}

		$html = "<table border=0 cellpadding=5 class=\"generaltable\">";
		$html .= "<thead><tr><th>Number</th><th>Date</th><th>Ledger A/C</th><th>Type</th><th>Status</th><th>DR Amount</th><th>CR Amount</th><th colspan=3>Actions</th></tr></thead>";
		$html .= "<tbody>";

		$odd_even = "odd";
		foreach ($voucher_q->result() as $row)
		{
			$this->tree .= "<tr class=\"tr-" . $odd_even . "\">";
			$this->tree .= "<td>" . $row->number . "</td>";
			$this->tree .= "<td>" . $row->date . "</td>";
			$this->tree .= "<td>Ledger A/C</td>";
			$this->tree .= "<td>" . $row->type . "</td>";
			$this->tree .= "<td>" . $row->draft . "</td>";
			$this->tree .= "<td>" . $row->dr_total . "</td>";
			$this->tree .= "<td>" . $row->cr_total . "</td>";
			$this->tree .= "</tr>";
			$odd_even = ($odd_even == "odd") ? "even" : "odd";
		}
		$html .= "</tbody>";
		$html .= "</table>";
		return $html;
	}

	function add($voucher_type)
	{
		switch ($voucher_type)
		{
		case 'receipt' :
			$page_data['page_title'] = "New Receipt Voucher";
			$data['voucher_type'] = "receipt";
			break;
		case 'payment' :
			$page_data['page_title'] = "New Payment Voucher";
			$data['voucher_type'] = "payment";
			break;
		case 'contra' :
			$page_data['page_title'] = "New Contra Voucher";
			$data['voucher_type'] = "contra";
			break;
		case 'journal' :
			$page_data['page_title'] = "New Journal Voucher";
			$data['voucher_type'] = "journal";
			break;
		default :
			$this->session->set_flashdata('error', "Invalid voucher type");
			redirect('voucher/show/all');
			return;
			break;
		}

		/* Form fields */
		$data['voucher_number'] = array(
			'name' => 'voucher_number',
			'id' => 'voucher_number',
			'maxlength' => '11',
			'size' => '11',
			'value' => $this->Voucher_model->next_voucher_number(),
		);
		$data['voucher_date'] = array(
			'name' => 'voucher_date',
			'id' => 'voucher_date',
			'maxlength' => '11',
			'size' => '11',
			'value' => '01/11/2010',
		);
		$data['ledger_dc'] = "D";
		$data['dr_amount'] = array(
			'name' => 'dr_amount',
			'id' => 'dr_amount',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		$data['cr_amount'] = array(
			'name' => 'dr_amount',
			'id' => 'dr_amount',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		$data['voucher_narration'] = array(
			'name' => 'voucher_narration',
			'id' => 'voucher_narration',
			'cols' => '50',
			'rows' => '4',
			'value' => '',
		);
		$data['voucher_type'] = $voucher_type;

		$this->load->view('template/header', $page_data);
		$this->load->view('voucher/add', $data);
		$this->load->view('template/footer');
	}
}
