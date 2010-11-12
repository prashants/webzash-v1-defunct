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
		$this->template->set('nav_links', array(
			'voucher/show/all' => 'All',
			'voucher/show/receipt' => 'Receipt',
			'voucher/show/payment' => 'Payment',
			'voucher/show/contra' => 'Contra',
			'voucher/show/journal' => 'Journal',
		));
		switch ($voucher_type)
		{
		case 'all' :
			$this->template->set('page_title', 'All Vouchers');
			$data['voucher_type'] = "";
			$data['voucher_table'] = $this->_show_voucher();
			break;
		case 'receipt' :
			$this->template->set('page_title', 'Receipt Vouchers');
			$data['voucher_type'] = "receipt";
			$data['voucher_table'] = $this->_show_voucher(1);
			break;
		case 'payment' :
			$this->template->set('page_title', 'Payment Vouchers');
			$data['voucher_type'] = "payment";
			$data['voucher_table'] = $this->_show_voucher(2);
			break;
		case 'contra' :
			$this->template->set('page_title', 'Contra Vouchers');
			$data['voucher_type'] = "contra";
			$data['voucher_table'] = $this->_show_voucher(3);
			break;
		case 'journal' :
			$this->template->set('page_title', 'Journal Vouchers');
			$data['voucher_type'] = "journal";
			$data['voucher_table'] = $this->_show_voucher(4);
			break;
		default :
			$this->messages->add('Invalid voucher type', 'error');
			redirect('voucher/show/all');
			return;
			break;
		}
		$this->template->load('template', 'voucher/index', $data);
		return;
	}

	function _show_voucher($voucher_type = NULL)
	{
		$voucher_q = NULL;
		if ($voucher_type > 5)
		{
			$this->messages->add('Invalid voucher type', 'error');
			redirect('voucher/show/all');
			return;
		} else if ($voucher_type > 0) {
			$voucher_q = $this->db->query('SELECT * FROM vouchers WHERE type = ? ORDER BY date DESC, number DESC', array($voucher_type));
		} else {
			$voucher_q = $this->db->query('SELECT * FROM vouchers ORDER BY date DESC, number DESC');
		}

		$html = "<table border=0 cellpadding=5 class=\"generaltable\">";
		$html .= "<thead><tr><th>Number</th><th>Date</th><th>Ledger A/C</th><th>Type</th><th>Status</th><th>DR Amount</th><th>CR Amount</th><th colspan=3>Actions</th></tr></thead>";
		$html .= "<tbody>";

		$odd_even = "odd";
		foreach ($voucher_q->result() as $row)
		{
			$html .= "<tr class=\"tr-" . $odd_even . "\">";
			$html .= "<td>" . $row->number . "</td>";
			$html .= "<td>" . date_mysql_to_php($row->date) . "</td>";
			$html .= "<td>Ledger A/C</td>";
			$html_voucher_type = n_to_v($row->type);
			$html .= "<td>" . ucfirst($html_voucher_type) . "</td>";
			if ($row->draft == 0)
				$html .= "<td>Active</td>";
			else
				$html .= "<td>Draft</td>";
			$html .= "<td>" . $row->dr_total . "</td>";
			$html .= "<td>" . $row->cr_total . "</td>";
			$html .= "<td>" . anchor('voucher/edit/' . strtolower($html_voucher_type) . "/" . $row->id , img(array('src' => asset_url() . "images/icons/edit.png", 'border' => '0', 'alt' => 'Edit ' . ucfirst($html_voucher_type) . ' Voucher'))) . "</td>";
			$html .= "<td>" . anchor('voucher/delete/' . strtolower($html_voucher_type) . "/" . $row->id , img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Delete ' . ucfirst($html_voucher_type) . ' Voucher', 'class' => "confirmClick", 'title' => "Delete voucher"))) . "</td>";

			$html .= "<td>" . anchor('voucher/printhtml/' . strtolower($html_voucher_type) . "/" . $row->id , img(array('src' => asset_url() . "images/icons/print.png", 'border' => '0', 'alt' => 'Print ' . ucfirst($html_voucher_type) . ' Voucher')), array('target' => '_blank')) . "</td>";
			$html .= "</tr>";
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
			$this->template->set('page_title', 'New Receipt Voucher');
			$data['voucher_type'] = "receipt";
			break;
		case 'payment' :
			$this->template->set('page_title', 'New Payment Voucher');
			$data['voucher_type'] = "payment";
			break;
		case 'contra' :
			$this->template->set('page_title', 'New Contra Voucher');
			$data['voucher_type'] = "contra";
			break;
		case 'journal' :
			$this->template->set('page_title', 'New Journal Voucher');
			$data['voucher_type'] = "journal";
			break;
		default :
			$this->messages->add('Invalid voucher type', 'error');
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
			'value' => $this->Voucher_model->next_voucher_number($voucher_type),
		);
		$data['voucher_date'] = array(
			'name' => 'voucher_date',
			'id' => 'voucher_date',
			'maxlength' => '11',
			'size' => '11',
			'value' => '01/11/2010',
		);
		$data['voucher_narration'] = array(
			'name' => 'voucher_narration',
			'id' => 'voucher_narration',
			'cols' => '50',
			'rows' => '4',
			'value' => '',
		);
		$data['voucher_type'] = $voucher_type;

		/* Form validations */
		$this->form_validation->set_rules('voucher_number', 'Voucher Number', 'trim|is_natural|uniquevoucherno[' . v_to_n($voucher_type) . ']');
		$this->form_validation->set_rules('voucher_date', 'Voucher Date', 'trim|required|is_date');
		$this->form_validation->set_rules('voucher_narration', 'trim');

		/* Debit and Credit amount validation */
		if ($_POST)
		{
			foreach ($this->input->post('ledger_dc', TRUE) as $id => $ledger_data)
			{
				$this->form_validation->set_rules('dr_amount[' . $id . ']', 'Debit Amount', 'trim|currency');
				$this->form_validation->set_rules('cr_amount[' . $id . ']', 'Credit Amount', 'trim|currency');
			}
		}

		/* Repopulating form */
		if ($_POST)
		{
			$data['voucher_number']['value'] = $this->input->post('voucher_number');
			$data['voucher_date']['value'] = $this->input->post('voucher_date');
			$data['voucher_narration']['value'] = $this->input->post('voucher_narration');
			$data['ledger_dc_p'] = $this->input->post('ledger_dc', TRUE);
			$data['ledger_id_p'] = $this->input->post('ledger_id', TRUE);
			$data['dr_amount_p'] = $this->input->post('dr_amount', TRUE);
			$data['cr_amount_p'] = $this->input->post('cr_amount', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'voucher/add', $data);
		}
		else
		{
			/* Checking for Debit and Credit Total */
			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_dr_amount = $this->input->post('dr_amount', TRUE);
			$data_all_cr_amount = $this->input->post('cr_amount', TRUE);
			$dr_total = 0;
			$cr_total = 0;
			foreach ($data_all_ledger_dc as $id => $ledger_data)
			{
				if ($data_all_ledger_id[$id] < 1)
					continue;
				if ($data_all_ledger_dc[$id] == "D")
				{
					$dr_total += $data_all_dr_amount[$id];
				} else {
					$cr_total += $data_all_cr_amount[$id];
				}
			}
			if ($dr_total != $cr_total)
			{
				$this->messages->add('Debit and Credit Total does not match!', 'error');
				$this->template->load('template', 'voucher/add', $data);
				return;
			} else if ($dr_total == 0 && $cr_total == 0) {
				$this->messages->add('Cannot save empty voucher', 'error');
				$this->template->load('template', 'voucher/add', $data);
				return;
			}

			/* Adding main voucher */
			$data_number = $this->input->post('voucher_number', TRUE);
			$data_date = $this->input->post('voucher_date', TRUE);
			$data_narration = $this->input->post('voucher_narration', TRUE);
			$data_type = 0;
			switch ($voucher_type)
			{
				case "receipt": $data_type = 1; break;
				case "payment": $data_type = 2; break;
				case "contra": $data_type = 3; break;
				case "journal": $data_type = 4; break;
			}
			$data_date = date_php_to_mysql($data_date); // Converting date to MySQL
			$voucher_id = NULL;
			if ( ! $this->db->query("INSERT INTO vouchers (number, date, narration, draft, type) VALUES (?, ?, ?, 0, ?)", array($data_number, $data_date, $data_narration, $data_type)))
			{
				$this->messages->add('Error addding Voucher A/C', 'error');
				$this->template->load('template', 'voucher/add', $data);
				return;
			} else {
				$voucher_id = $this->db->insert_id();
			}

			/* Adding ledger accounts */
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_dr_amount = $this->input->post('dr_amount', TRUE);
			$data_all_cr_amount = $this->input->post('cr_amount', TRUE);

			$dr_total = 0;
			$cr_total = 0;
			foreach ($data_all_ledger_dc as $id => $ledger_data)
			{
				$data_ledger_dc = $data_all_ledger_dc[$id];
				$data_ledger_id = $data_all_ledger_id[$id];
				if ($data_ledger_id < 1)
					continue;
				$data_amount = 0;
				if ($data_all_ledger_dc[$id] == "D")
				{
					$data_amount = $data_all_dr_amount[$id];
					$dr_total += $data_all_dr_amount[$id];
				} else {
					$data_amount = $data_all_cr_amount[$id];
					$cr_total += $data_all_cr_amount[$id];
				}

				if ( ! $this->db->query("INSERT INTO voucher_items (voucher_id, ledger_id, amount, dc) VALUES (?, ?, ?, ?)", array($voucher_id, $data_ledger_id, $data_amount, $data_ledger_dc)))
				{
					$this->messages->add('Error addding Ledger A/C ' . $data_ledger_id, 'error');
				}
			}

			/* Updating Debit and Credit Total in vouchers table */
			if ( ! $this->db->query("UPDATE vouchers SET dr_total = ?, cr_total = ? WHERE id = ?", array($dr_total, $cr_total, $voucher_id)))
			{
				$this->messages->add('Error updating voucher total', 'error');
			}

			/* Success */
			$this->messages->add('Voucher added successfully', 'success');
			redirect('voucher/show/' . $voucher_type);
			$this->template->load('template', 'voucher/add', $data);
		}
	}

	function edit($voucher_type, $voucher_id)
	{
		$this->messages->add('Voucher edited successfully', 'success');
		redirect('voucher/show/' . $voucher_type);
	}

	function delete($voucher_type, $voucher_id)
	{
		if ( ! $this->db->query("DELETE FROM voucher_items WHERE voucher_id = ?", array($voucher_id)))
		{
			$this->messages->add('Error deleting Voucher - Ledgers entry', 'error');
			redirect('voucher/' . $voucher_type . '/' . $voucher_id);
			return;
		}
		if ( ! $this->db->query("DELETE FROM vouchers WHERE id = ?", array($voucher_id)))
		{
			$this->messages->add('Error deleting Voucher entry', 'error');
			redirect('voucher/' . $voucher_type . '/' . $voucher_id);
			return;
		}
		$this->messages->add('Voucher deleted successfully', 'success');
		redirect('voucher/show/' . $voucher_type);
	}

	function printhtml($voucher_type, $voucher_id)
	{
		$this->load->model('Setting_model');
		$this->load->model('Ledger_model');

		$account = $this->Setting_model->get_current();

		$voucher_q = $this->db->query("SELECT * FROM vouchers WHERE id = ?", $voucher_id);
		$voucher = $voucher_q->row();

		echo "<h3>" . ucfirst($voucher_type) . " Voucher</h3>";
		echo "<p><b>" . $account->name . "</b></p>";
		echo "<p>" . $account->address . "</p>";
		echo "<p>Voucher Number : " . $voucher->number . "</p>";
		echo "<p>Voucher Date : " . date_mysql_to_php($voucher->date) . "</p>";
		echo "<table border=1>";
		echo "<thead><tr><th>Ledger A/C</th><th>Dr Amount</th><th>Cr Amount</th></tr></thead>";

		$ledger_q;
		if ($voucher_type == "receipt" || $voucher_type == "contra")
			$ledger_q = $this->db->query("SELECT * FROM voucher_items WHERE voucher_id = ? ORDER BY dc DESC", $voucher_id);
		else
			$ledger_q = $this->db->query("SELECT * FROM voucher_items WHERE voucher_id = ? ORDER BY dc ASC", $voucher_id);
	
		foreach ($ledger_q->result() as $row)
		{
			echo "<tr>";
			echo "<td>" . $this->Ledger_model->get_name($row->ledger_id) . "</td>";
			if ($row->dc == "D")
			{
				echo "<td>" . $row->amount . "</td>";
				echo "<td>-</td>";
			} else {
				echo "<td>-</td>";
				echo "<td>" . $row->amount . "</td>";
			}
			echo "</tr>";
		}
		echo "<tr><td><b>TOTAL</b></td><td><b>" . $voucher->dr_total . "</b></td><td><b>" . $voucher->cr_total . "</b></td></tr>";
		echo "</table>";
		echo "<p>" . "Narration : " . $voucher->narration . "</p>";
	}

	function email($voucher_type, $voucher_id)
	{
	}

	function addrow()
	{
		$i = time() + rand  (0, time()) + rand  (0, time()) + rand  (0, time());
		$dr_amount = array(
			'name' => 'dr_amount[' . $i . ']',
			'id' => 'dr_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		$cr_amount = array(
			'name' => 'cr_amount[' . $i . ']',
			'id' => 'cr_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);

		echo '<tr class="new-row">';
		echo '<td>';
		echo form_dropdown_dc('ledger_dc[' . $i . ']');
		echo '</td>';
		echo '<td>';
		echo form_input_ledger('ledger_id[' . $i . ']');
		echo '</td>';
		echo '<td>';
		echo form_input($dr_amount);
		echo '</td>';
		echo '<td>';
		echo form_input($cr_amount);
		echo '</td>';
		echo '<td>';
		echo img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Ledger', 'class' => 'addrow'));
		echo '</td>';
		echo '<td>';
		echo img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Ledger', 'class' => 'deleterow'));
		echo '</td>';
		echo '</tr>';
	}
}
