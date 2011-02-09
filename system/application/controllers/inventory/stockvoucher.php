<?php

class StockVoucher extends Controller {

	function StockVoucher()
	{
		parent::Controller();
		$this->load->model('Voucher_model');
		$this->load->model('Ledger_model');
		$this->load->model('Tag_model');
		return;
	}

	function index()
	{
		redirect('voucher/show/all');
		return;
	}

	function view($voucher_type, $voucher_id = 0)
	{
		/* Voucher Type */
		$voucher_type_id = voucher_type_name_to_id($voucher_type);
		if ( ! $voucher_type_id)
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		} else {
			$current_voucher_type = voucher_type_info($voucher_type_id);
		}

		$this->template->set('page_title', 'View ' . $current_voucher_type['name'] . ' Voucher');

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}
		/* Load current voucher details */
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->order_by('id', 'asc');
		$cur_voucher_ledgers = $this->db->get();
		if ($cur_voucher_ledgers->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Ledger A/C\'s.', 'error');
		}
		$data['cur_voucher'] = $cur_voucher;
		$data['cur_voucher_ledgers'] = $cur_voucher_ledgers;
		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;
		$this->template->load('template', 'voucher/view', $data);
		return;
	}

	function add($voucher_type)
	{
		/* Check access */
		if ( ! check_access('create stock voucher'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('voucher/show/' . $voucher_type);
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('inventory/stockvoucher/show/' . $voucher_type);
			return;
		}

		/* Voucher Type */
		$voucher_type_id = voucher_type_name_to_id($voucher_type);
		if ( ! $voucher_type_id)
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		} else {
			$current_voucher_type = voucher_type_info($voucher_type_id);
		}

		$this->template->set('page_title', 'New ' . $current_voucher_type['name'] . ' Voucher');

		/* Form fields */
		$data['voucher_number'] = array(
			'name' => 'voucher_number',
			'id' => 'voucher_number',
			'maxlength' => '11',
			'size' => '11',
			'value' => '',
		);
		$data['voucher_date'] = array(
			'name' => 'voucher_date',
			'id' => 'voucher_date',
			'maxlength' => '11',
			'size' => '11',
			'value' => date_today_php(),
		);
		$data['voucher_narration'] = array(
			'name' => 'voucher_narration',
			'id' => 'voucher_narration',
			'cols' => '50',
			'rows' => '4',
			'value' => '',
		);
		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;
		$data['voucher_tags'] = $this->Tag_model->get_all_tags();
		$data['voucher_tag'] = 0;

		/* Form validations */
		if ($current_voucher_type['numbering'] == '2')
			$this->form_validation->set_rules('voucher_number', 'Voucher Number', 'trim|required|is_natural_no_zero|uniquevoucherno[' . $voucher_type_id . ']');
		else if ($current_voucher_type['numbering'] == '3')
			$this->form_validation->set_rules('voucher_number', 'Voucher Number', 'trim|is_natural_no_zero|uniquevoucherno[' . $voucher_type_id . ']');
		else
			$this->form_validation->set_rules('voucher_number', 'Voucher Number', 'trim|is_natural_no_zero|uniquevoucherno[' . $voucher_type_id . ']');
		$this->form_validation->set_rules('voucher_date', 'Voucher Date', 'trim|required|is_date|is_date_within_range');
		$this->form_validation->set_rules('voucher_narration', 'trim');
		$this->form_validation->set_rules('voucher_tag', 'Tag', 'trim|is_natural');

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
			$data['voucher_number']['value'] = $this->input->post('voucher_number', TRUE);
			$data['voucher_date']['value'] = $this->input->post('voucher_date', TRUE);
			$data['voucher_narration']['value'] = $this->input->post('voucher_narration', TRUE);
			$data['voucher_tag'] = $this->input->post('voucher_tag', TRUE);

			$data['ledger_dc'] = $this->input->post('ledger_dc', TRUE);
			$data['ledger_id'] = $this->input->post('ledger_id', TRUE);
			$data['dr_amount'] = $this->input->post('dr_amount', TRUE);
			$data['cr_amount'] = $this->input->post('cr_amount', TRUE);
		} else {
			for ($count = 0; $count <= 1; $count++)
			{
				$data['ledger_dc'][$count] = "D";
				$data['ledger_id'][$count] = 0;
				$data['rate'][$count] = "";
				$data['amount'][$count] = "";
			}
			for ($count = 0; $count <= 3; $count++)
			{
				$data['stock_item_id'][$count] = '0';
				$data['stock_item_quantity'][$count] = '';
				$data['stock_item_rate_per_unit'][$count] = '';
				$data['stock_item_amount'][$count] = '';
			}
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/stockvoucher/add', $data);
			return;
		}
		else
		{
			/* Checking for Valid Ledgers A/C and Debit and Credit Total */
			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_dr_amount = $this->input->post('dr_amount', TRUE);
			$data_all_cr_amount = $this->input->post('cr_amount', TRUE);
			$dr_total = 0;
			$cr_total = 0;
			$bank_cash_present = FALSE; /* Whether atleast one Ledger A/C is Bank or Cash A/C */
			$non_bank_cash_present = FALSE;  /* Whether atleast one Ledger A/C is NOT a Bank or Cash A/C */
			foreach ($data_all_ledger_dc as $id => $ledger_data)
			{
				if ($data_all_ledger_id[$id] < 1)
					continue;

				/* Check for valid ledger id */
				$this->db->from('ledgers')->where('id', $data_all_ledger_id[$id]);
				$valid_ledger_q = $this->db->get();
				if ($valid_ledger_q->num_rows() < 1)
				{
					$this->messages->add('Invalid Ledger A/C.', 'error');
					$this->template->load('template', 'inventory/stockvoucher/add', $data);
					return;
				} else {
					/* Check for valid ledger type */
					$valid_ledger = $valid_ledger_q->row();
					if ($current_voucher_type['bank_cash_ledger_restriction'] == '2')
					{
						if ($data_all_ledger_dc[$id] == 'D' && $valid_ledger->type == 1)
						{
							$bank_cash_present = TRUE;
						}
						if ($valid_ledger->type != 1)
							$non_bank_cash_present = TRUE;
					} else if ($current_voucher_type['bank_cash_ledger_restriction'] == '3')
					{
						if ($data_all_ledger_dc[$id] == 'C' && $valid_ledger->type == 1)
						{
							$bank_cash_present = TRUE;
						}
						if ($valid_ledger->type != 1)
							$non_bank_cash_present = TRUE;
					} else if ($current_voucher_type['bank_cash_ledger_restriction'] == '4')
					{
						if ($valid_ledger->type != 1)
						{
							$this->messages->add('Invalid Ledger A/C. ' . $current_voucher_type['name'] . ' Vouchers can have only Bank and Cash Ledgers A/C\'s.', 'error');
							$this->template->load('template', 'inventory/stockvoucher/add', $data);
							return;
						}
					} else if ($current_voucher_type['bank_cash_ledger_restriction'] == '5')
					{
						if ($valid_ledger->type == 1)
						{
							$this->messages->add('Invalid Ledger A/C. ' . $current_voucher_type['name'] . ' Vouchers cannot have Bank and Cash Ledgers A/C\'s.', 'error');
							$this->template->load('template', 'inventory/stockvoucher/add', $data);
							return;
						}
					}
				}

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
				$this->template->load('template', 'inventory/stockvoucher/add', $data);
				return;
			} else if ($dr_total == 0 && $cr_total == 0) {
				$this->messages->add('Cannot save empty Voucher.', 'error');
				$this->template->load('template', 'inventory/stockvoucher/add', $data);
				return;
			}
			/* Check if atleast one Bank or Cash Ledger A/C is present */
			if ($current_voucher_type['bank_cash_ledger_restriction'] == '2')
			{
				if ( ! $bank_cash_present)
				{
					$this->messages->add('Need to Debit atleast one Bank or Cash A/C.', 'error');
					$this->template->load('template', 'inventory/stockvoucher/add', $data);
					return;
				}
				if ( ! $non_bank_cash_present)
				{
					$this->messages->add('Need to Debit or Credit atleast one NON - Bank or Cash A/C.', 'error');
					$this->template->load('template', 'inventory/stockvoucher/add', $data);
					return;
				}
			} else if ($current_voucher_type['bank_cash_ledger_restriction'] == '3')
			{
				if ( ! $bank_cash_present)
				{
					$this->messages->add('Need to Credit atleast one Bank or Cash A/C.', 'error');
					$this->template->load('template', 'inventory/stockvoucher/add', $data);
					return;
				}
				if ( ! $non_bank_cash_present)
				{
					$this->messages->add('Need to Debit or Credit atleast one NON - Bank or Cash A/C.', 'error');
					$this->template->load('template', 'inventory/stockvoucher/add', $data);
					return;
				}
			}

			/* Adding main voucher */
			if ($current_voucher_type['numbering'] == '2')
			{
				$data_number = $this->input->post('voucher_number', TRUE);
			} else if ($current_voucher_type['numbering'] == '3') {
				$data_number = $this->input->post('voucher_number', TRUE);
				if ( ! $data_number)
					$data_number = NULL;
			} else {
				if ($this->input->post('voucher_number', TRUE))
					$data_number = $this->input->post('voucher_number', TRUE);
				else
					$data_number = $this->Voucher_model->next_voucher_number($voucher_type_id);
			}

			$data_date = $this->input->post('voucher_date', TRUE);
			$data_narration = $this->input->post('voucher_narration', TRUE);
			$data_tag = $this->input->post('voucher_tag', TRUE);
			if ($data_tag < 1)
				$data_tag = NULL;
			$data_type = $voucher_type_id;
			$data_date = date_php_to_mysql($data_date); // Converting date to MySQL
			$voucher_id = NULL;

			$this->db->trans_start();
			$insert_data = array(
				'number' => $data_number,
				'date' => $data_date,
				'narration' => $data_narration,
				'voucher_type' => $data_type,
				'tag_id' => $data_tag,
			);
			if ( ! $this->db->insert('vouchers', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Voucher.', 'error');
				$this->logger->write_message("error", "Error adding " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed inserting voucher");
				$this->template->load('template', 'inventory/stockvoucher/add', $data);
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
				$insert_ledger_data = array(
					'voucher_id' => $voucher_id,
					'ledger_id' => $data_ledger_id,
					'amount' => $data_amount,
					'dc' => $data_ledger_dc,
				);
				if ( ! $this->db->insert('voucher_items', $insert_ledger_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Ledger A/C - ' . $data_ledger_id . ' to Voucher.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed inserting voucher ledger item " . "[id:" . $data_ledger_id . "]");
					$this->template->load('template', 'inventory/stockvoucher/add', $data);
					return;
				}
			}

			/* Updating Debit and Credit Total in vouchers table */
			$update_data = array(
				'dr_total' => $dr_total,
				'cr_total' => $cr_total,
			);
			if ( ! $this->db->where('id', $voucher_id)->update('vouchers', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Voucher total.', 'error');
				$this->logger->write_message("error", "Error adding " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed updating debit and credit total");
				$this->template->load('template', 'inventory/stockvoucher/add', $data);
				return;
			}

			/* Success */
			$this->db->trans_complete();

			$this->session->set_userdata('voucher_added_show_action', TRUE);
			$this->session->set_userdata('voucher_added_id', $voucher_id);
			$this->session->set_userdata('voucher_added_type_id', $voucher_type_id);
			$this->session->set_userdata('voucher_added_type_label', $current_voucher_type['label']);
			$this->session->set_userdata('voucher_added_type_name', $current_voucher_type['name']);
			$this->session->set_userdata('voucher_added_number', $data_number);

			/* Showing success message in show() method since message is too long for storing it in session */
			$this->logger->write_message("success", "Added " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");
			redirect('voucher/show/' . $current_voucher_type['label']);
			$this->template->load('template', 'inventory/stockvoucher/add', $data);
			return;
		}
		return;
	}

	function edit($voucher_type, $voucher_id = 0)
	{
		/* Check access */
		if ( ! check_access('edit voucher'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/stockvoucher/show/' . $voucher_type);
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('voucher/show/' . $voucher_type);
			return;
		}

		/* Voucher Type */
		$voucher_type_id = voucher_type_name_to_id($voucher_type);
		if ( ! $voucher_type_id)
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		} else {
			$current_voucher_type = voucher_type_info($voucher_type_id);
		}

		$this->template->set('page_title', 'Edit ' . $current_voucher_type['name'] . ' Voucher');

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}

		/* Form fields - Voucher */
		$data['voucher_number'] = array(
			'name' => 'voucher_number',
			'id' => 'voucher_number',
			'maxlength' => '11',
			'size' => '11',
			'value' => $cur_voucher->number,
		);
		$data['voucher_date'] = array(
			'name' => 'voucher_date',
			'id' => 'voucher_date',
			'maxlength' => '11',
			'size' => '11',
			'value' => date_mysql_to_php($cur_voucher->date),
		);
		$data['voucher_narration'] = array(
			'name' => 'voucher_narration',
			'id' => 'voucher_narration',
			'cols' => '50',
			'rows' => '4',
			'value' => $cur_voucher->narration,
		);
		$data['voucher_id'] = $voucher_id;
		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;
		$data['voucher_tag'] = $cur_voucher->tag_id;
		$data['voucher_tags'] = $this->Tag_model->get_all_tags();
		$data['has_reconciliation'] = FALSE;

		/* Load current ledger details if not $_POST */
		if ( ! $_POST)
		{
			$this->db->from('voucher_items')->where('voucher_id', $voucher_id);
			$cur_ledgers_q = $this->db->get();
			if ($cur_ledgers_q->num_rows <= 0)
			{
				$this->messages->add('No Ledger A/C\'s found!', 'error');
			}
			$counter = 0;
			foreach ($cur_ledgers_q->result() as $row)
			{
				$data['ledger_dc'][$counter] = $row->dc;
				$data['ledger_id'][$counter] = $row->ledger_id;
				if ($row->dc == "D")
				{
					$data['dr_amount'][$counter] = $row->amount;
					$data['cr_amount'][$counter] = "";
				} else {
					$data['dr_amount'][$counter] = "";
					$data['cr_amount'][$counter] = $row->amount;
				}
				if ($row->reconciliation_date)
					$data['has_reconciliation'] = TRUE;
				$counter++;
			}
			/* Two extra rows */
			$data['ledger_dc'][$counter] = 'D';
			$data['ledger_id'][$counter] = 0;
			$data['dr_amount'][$counter] = "";
			$data['cr_amount'][$counter] = "";
			$counter++;
			$data['ledger_dc'][$counter] = 'D';
			$data['ledger_id'][$counter] = 0;
			$data['dr_amount'][$counter] = "";
			$data['cr_amount'][$counter] = "";
			$counter++;
		}

		/* Form validations */
		if ($current_voucher_type['numbering'] == '3')
			$this->form_validation->set_rules('voucher_number', 'Voucher Number', 'trim|is_natural_no_zero|uniquevouchernowithid[' . $voucher_type_id . '.' . $voucher_id . ']');
		else
			$this->form_validation->set_rules('voucher_number', 'Voucher Number', 'trim|required|is_natural_no_zero|uniquevouchernowithid[' . $voucher_type_id . '.' . $voucher_id . ']');
		$this->form_validation->set_rules('voucher_date', 'Voucher Date', 'trim|required|is_date|is_date_within_range');
		$this->form_validation->set_rules('voucher_narration', 'trim');
		$this->form_validation->set_rules('voucher_tag', 'Tag', 'trim|is_natural');

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
			$data['voucher_number']['value'] = $this->input->post('voucher_number', TRUE);
			$data['voucher_date']['value'] = $this->input->post('voucher_date', TRUE);
			$data['voucher_narration']['value'] = $this->input->post('voucher_narration', TRUE);
			$data['voucher_tag'] = $this->input->post('voucher_tag', TRUE);
			$data['has_reconciliation'] = $this->input->post('has_reconciliation', TRUE);

			$data['ledger_dc'] = $this->input->post('ledger_dc', TRUE);
			$data['ledger_id'] = $this->input->post('ledger_id', TRUE);
			$data['dr_amount'] = $this->input->post('dr_amount', TRUE);
			$data['cr_amount'] = $this->input->post('cr_amount', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'voucher/edit', $data);
		} else	{
			/* Checking for Valid Ledgers A/C and Debit and Credit Total */
			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_dr_amount = $this->input->post('dr_amount', TRUE);
			$data_all_cr_amount = $this->input->post('cr_amount', TRUE);
			$dr_total = 0;
			$cr_total = 0;
			$bank_cash_present = FALSE; /* Whether atleast one Ledger A/C is Bank or Cash A/C */
			$non_bank_cash_present = FALSE;  /* Whether atleast one Ledger A/C is NOT a Bank or Cash A/C */
			foreach ($data_all_ledger_dc as $id => $ledger_data)
			{
				if ($data_all_ledger_id[$id] < 1)
					continue;

				/* Check for valid ledger id */
				$this->db->from('ledgers')->where('id', $data_all_ledger_id[$id]);
				$valid_ledger_q = $this->db->get();
				if ($valid_ledger_q->num_rows() < 1)
				{
					$this->messages->add('Invalid Ledger A/C.', 'error');
					$this->template->load('template', 'voucher/edit', $data);
					return;
				} else {
					/* Check for valid ledger type */
					$valid_ledger = $valid_ledger_q->row();
					if ($current_voucher_type['bank_cash_ledger_restriction'] == '2')
					{
						if ($data_all_ledger_dc[$id] == 'D' && $valid_ledger->type == 1)
						{
							$bank_cash_present = TRUE;
						}
						if ($valid_ledger->type != 1)
							$non_bank_cash_present = TRUE;
					} else if ($current_voucher_type['bank_cash_ledger_restriction'] == '3')
					{
						if ($data_all_ledger_dc[$id] == 'C' && $valid_ledger->type == 1)
						{
							$bank_cash_present = TRUE;
						}
						if ($valid_ledger->type != 1)
							$non_bank_cash_present = TRUE;
					} else if ($current_voucher_type['bank_cash_ledger_restriction'] == '4')
					{
						if ($valid_ledger->type != 1)
						{
							$this->messages->add('Invalid Ledger A/C. ' . $current_voucher_type['name'] . ' Vouchers can have only Bank and Cash Ledgers A/C\'s.', 'error');
							$this->template->load('template', 'voucher/edit', $data);
							return;
						}
					} else if ($current_voucher_type['bank_cash_ledger_restriction'] == '5')
					{
						if ($valid_ledger->type == 1)
						{
							$this->messages->add('Invalid Ledger A/C. ' . $current_voucher_type['name'] . ' Vouchers cannot have Bank and Cash Ledgers A/C\'s.', 'error');
							$this->template->load('template', 'voucher/edit', $data);
							return;
						}
					}
				}
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
				$this->template->load('template', 'voucher/edit', $data);
				return;
			} else if ($dr_total == 0 && $cr_total == 0) {
				$this->messages->add('Cannot save empty Voucher.', 'error');
				$this->template->load('template', 'voucher/edit', $data);
				return;
			}
			/* Check if atleast one Bank or Cash Ledger A/C is present */
			if ($current_voucher_type['bank_cash_ledger_restriction'] == '2')
			{
				if ( ! $bank_cash_present)
				{
					$this->messages->add('Need to Debit atleast one Bank or Cash A/C.', 'error');
					$this->template->load('template', 'voucher/edit', $data);
					return;
				}
				if ( ! $non_bank_cash_present)
				{
					$this->messages->add('Need to Debit or Credit atleast one NON - Bank or Cash A/C.', 'error');
					$this->template->load('template', 'voucher/edit', $data);
					return;
				}
			} else if ($current_voucher_type['bank_cash_ledger_restriction'] == '3')
			{
				if ( ! $bank_cash_present)
				{
					$this->messages->add('Need to Credit atleast one Bank or Cash A/C.', 'error');
					$this->template->load('template', 'voucher/edit', $data);
					return;
				}
				if ( ! $non_bank_cash_present)
				{
					$this->messages->add('Need to Debit or Credit atleast one NON - Bank or Cash A/C.', 'error');
					$this->template->load('template', 'voucher/edit', $data);
					return;
				}
			}

			/* Updating main voucher */
			if ($current_voucher_type['numbering'] == '3') {
				$data_number = $this->input->post('voucher_number', TRUE);
				if ( ! $data_number)
					$data_number = NULL;
			} else {
				$data_number = $this->input->post('voucher_number', TRUE);
			}

			$data_date = $this->input->post('voucher_date', TRUE);
			$data_narration = $this->input->post('voucher_narration', TRUE);
			$data_tag = $this->input->post('voucher_tag', TRUE);
			if ($data_tag < 1)
				$data_tag = NULL;
			$data_type = $voucher_type_id;
			$data_date = date_php_to_mysql($data_date); // Converting date to MySQL
			$data_has_reconciliation = $this->input->post('has_reconciliation', TRUE);

			$this->db->trans_start();
			$update_data = array(
				'number' => $data_number,
				'date' => $data_date,
				'narration' => $data_narration,
				'tag_id' => $data_tag,
			);
			if ( ! $this->db->where('id', $voucher_id)->update('vouchers', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Voucher A/C.', 'error');
				$this->logger->write_message("error", "Error updating voucher details for " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");
				$this->template->load('template', 'voucher/edit', $data);
				return;
			}

			/* TODO : Deleting all old ledger data, Bad solution */
			if ( ! $this->db->delete('voucher_items', array('voucher_id' => $voucher_id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error deleting previous Ledger A/C\'s from Voucher.', 'error');
				$this->logger->write_message("error", "Error deleting previous voucher items for " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");
				$this->template->load('template', 'voucher/edit', $data);
				return;
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

				$insert_ledger_data = array(
					'voucher_id' => $voucher_id,
					'ledger_id' => $data_ledger_id,
					'amount' => $data_amount,
					'dc' => $data_ledger_dc,
				);
				if ( ! $this->db->insert('voucher_items', $insert_ledger_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Ledger A/C - ' . $data_ledger_id . ' to Voucher.', 'error');
					$this->logger->write_message("error", "Error adding Ledger A/C item [id:" . $data_ledger_id . "] for " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");
					$this->template->load('template', 'voucher/edit', $data);
					return;
				}
			}

			/* Updating Debit and Credit Total in vouchers table */
			$update_data = array(
				'dr_total' => $dr_total,
				'cr_total' => $cr_total,
			);
			if ( ! $this->db->where('id', $voucher_id)->update('vouchers', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Voucher total.', 'error');
				$this->logger->write_message("error", "Error updating voucher total for " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");
				$this->template->load('template', 'voucher/edit', $data);
				return;
			}

			/* Success */
			$this->db->trans_complete();

			$this->session->set_userdata('voucher_updated_show_action', TRUE);
			$this->session->set_userdata('voucher_updated_id', $voucher_id);
			$this->session->set_userdata('voucher_updated_type_id', $voucher_type_id);
			$this->session->set_userdata('voucher_updated_type_label', $current_voucher_type['label']);
			$this->session->set_userdata('voucher_updated_type_name', $current_voucher_type['name']);
			$this->session->set_userdata('voucher_updated_number', $data_number);
			if ($data_has_reconciliation)
				$this->session->set_userdata('voucher_updated_has_reconciliation', TRUE);
			else
				$this->session->set_userdata('voucher_updated_has_reconciliation', FALSE);

			/* Showing success message in show() method since message is too long for storing it in session */
			$this->logger->write_message("success", "Updated " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");

			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}
		return;
	}

	function delete($voucher_type, $voucher_id = 0)
	{
		/* Check access */
		if ( ! check_access('delete voucher'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/stockvoucher/show/' . $voucher_type);
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('voucher/show/' . $voucher_type);
			return;
		}

		/* Voucher Type */
		$voucher_type_id = voucher_type_name_to_id($voucher_type);
		if ( ! $voucher_type_id)
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		} else {
			$current_voucher_type = voucher_type_info($voucher_type_id);
		}

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}

		$this->db->trans_start();
		if ( ! $this->db->delete('voucher_items', array('voucher_id' => $voucher_id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Voucher - Ledger A/C\'s.', 'error');
			$this->logger->write_message("error", "Error deleting ledger entries for " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $cur_voucher->number) . " [id:" . $voucher_id . "]");
			redirect('voucher/view/' . $current_voucher_type['label'] . '/' . $voucher_id);
			return;
		}
		if ( ! $this->db->delete('vouchers', array('id' => $voucher_id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Voucher entry.', 'error');
			$this->logger->write_message("error", "Error deleting Voucher entry for " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $cur_voucher->number) . " [id:" . $voucher_id . "]");
			redirect('voucher/view/' . $current_voucher_type['label'] . '/' . $voucher_id);
			return;
		}
		$this->db->trans_complete();
		$this->messages->add('Deleted ' . $current_voucher_type['name'] . ' Voucher.', 'success');
		$this->logger->write_message("success", "Deleted " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $cur_voucher->number) . " [id:" . $voucher_id . "]");
		redirect('voucher/show/' . $current_voucher_type['label']);
		return;
	}

	function download($voucher_type, $voucher_id = 0)
	{
		$this->load->helper('download');
		$this->load->model('Setting_model');
		$this->load->model('Ledger_model');

		/* Check access */
		if ( ! check_access('download voucher'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/stockvoucher/show/' . $voucher_type);
			return;
		}

		/* Voucher Type */
		$voucher_type_id = voucher_type_name_to_id($voucher_type);
		if ( ! $voucher_type_id)
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		} else {
			$current_voucher_type = voucher_type_info($voucher_type_id);
		}

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}

		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;
		$data['voucher_number'] =  $cur_voucher->number;
		$data['voucher_date'] = date_mysql_to_php_display($cur_voucher->date);
		$data['voucher_dr_total'] =  $cur_voucher->dr_total;
		$data['voucher_cr_total'] =  $cur_voucher->cr_total;
		$data['voucher_narration'] = $cur_voucher->narration;

		/* Getting Ledger details */
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->order_by('dc', 'desc');
		$ledger_q = $this->db->get();
		$counter = 0;
		$data['ledger_data'] = array();
		if ($ledger_q->num_rows() > 0)
		{
			foreach ($ledger_q->result() as $row)
			{
				$data['ledger_data'][$counter] = array(
					'id' => $row->ledger_id,
					'name' => $this->Ledger_model->get_name($row->ledger_id),
					'dc' => $row->dc,
					'amount' => $row->amount,
				);
				$counter++;
			}
		}

		/* Download Voucher */
		$file_name = $current_voucher_type['name'] . '_voucher_' . $cur_voucher->number . ".html";
		$download_data = $this->load->view('voucher/downloadpreview', $data, TRUE);
		force_download($file_name, $download_data);
		return;
	}

	function printpreview($voucher_type, $voucher_id = 0)
	{
		$this->load->model('Setting_model');
		$this->load->model('Ledger_model');

		/* Check access */
		if ( ! check_access('print voucher'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/stockvoucher/show/' . $voucher_type);
			return;
		}

		/* Voucher Type */
		$voucher_type_id = voucher_type_name_to_id($voucher_type);
		if ( ! $voucher_type_id)
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		} else {
			$current_voucher_type = voucher_type_info($voucher_type_id);
		}

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}

		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;
		$data['voucher_number'] =  $cur_voucher->number;
		$data['voucher_date'] = date_mysql_to_php_display($cur_voucher->date);
		$data['voucher_dr_total'] =  $cur_voucher->dr_total;
		$data['voucher_cr_total'] =  $cur_voucher->cr_total;
		$data['voucher_narration'] = $cur_voucher->narration;

		/* Getting Ledger details */
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->order_by('dc', 'desc');
		$ledger_q = $this->db->get();
		$counter = 0;
		$data['ledger_data'] = array();
		if ($ledger_q->num_rows() > 0)
		{
			foreach ($ledger_q->result() as $row)
			{
				$data['ledger_data'][$counter] = array(
					'id' => $row->ledger_id,
					'name' => $this->Ledger_model->get_name($row->ledger_id),
					'dc' => $row->dc,
					'amount' => $row->amount,
				);
				$counter++;
			}
		}

		$this->load->view('voucher/printpreview', $data);
		return;
	}

	function email($voucher_type, $voucher_id = 0)
	{
		$this->load->model('Setting_model');
		$this->load->model('Ledger_model');
		$this->load->library('email');

		/* Check access */
		if ( ! check_access('email voucher'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/stockvoucher/show/' . $voucher_type);
			return;
		}

		/* Voucher Type */
		$voucher_type_id = voucher_type_name_to_id($voucher_type);
		if ( ! $voucher_type_id)
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		} else {
			$current_voucher_type = voucher_type_info($voucher_type_id);
		}

		$account_data = $this->Setting_model->get_current();

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}

		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;
		$data['voucher_id'] = $voucher_id;
		$data['voucher_number'] = $cur_voucher->number;
		$data['email_to'] = array(
			'name' => 'email_to',
			'id' => 'email_to',
			'size' => '40',
			'value' => '',
		);

		/* Form validations */
		$this->form_validation->set_rules('email_to', 'Email to', 'trim|valid_emails|required');

		/* Repopulating form */
		if ($_POST)
		{
			$data['email_to']['value'] = $this->input->post('email_to', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$data['error'] = validation_errors();
			$this->load->view('voucher/email', $data);
			return;
		}
		else
		{
			$voucher_data['voucher_type_id'] = $voucher_type_id;
			$voucher_data['current_voucher_type'] = $current_voucher_type;
			$voucher_data['voucher_number'] =  $cur_voucher->number;
			$voucher_data['voucher_date'] = date_mysql_to_php_display($cur_voucher->date);
			$voucher_data['voucher_dr_total'] =  $cur_voucher->dr_total;
			$voucher_data['voucher_cr_total'] =  $cur_voucher->cr_total;
			$voucher_data['voucher_narration'] = $cur_voucher->narration;

			/* Getting Ledger details */
			$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->order_by('dc', 'desc');
			$ledger_q = $this->db->get();
			$counter = 0;
			$voucher_data['ledger_data'] = array();
			if ($ledger_q->num_rows() > 0)
			{
				foreach ($ledger_q->result() as $row)
				{
					$voucher_data['ledger_data'][$counter] = array(
						'id' => $row->ledger_id,
						'name' => $this->Ledger_model->get_name($row->ledger_id),
						'dc' => $row->dc,
						'amount' => $row->amount,
					);
					$counter++;
				}
			}

			/* Preparing message */
			$message = $this->load->view('voucher/emailpreview', $voucher_data, TRUE);

			/* Getting email configuration */
			$config['smtp_timeout'] = '30';
			$config['charset'] = 'utf-8';
			$config['newline'] = "\r\n";
			$config['mailtype'] = "html";
			if ($account_data)
			{
				$config['protocol'] = $account_data->email_protocol;
				$config['smtp_host'] = $account_data->email_host;
				$config['smtp_port'] = $account_data->email_port;
				$config['smtp_user'] = $account_data->email_username;
				$config['smtp_pass'] = $account_data->email_password;
			} else {
				$data['error'] = 'Invalid account settings.';
			}
			$this->email->initialize($config);

			/* Sending email */
			$this->email->from('', 'Webzash');
			$this->email->to($this->input->post('email_to', TRUE));
			$this->email->subject($current_voucher_type['name'] . ' Voucher No. ' . full_voucher_number($voucher_type_id, $cur_voucher->number));
			$this->email->message($message);
			if ($this->email->send())
			{
				$data['message'] = "Email sent.";
				$this->logger->write_message("success", "Emailed " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $cur_voucher->number) . " [id:" . $voucher_id . "]");
			} else {
				$data['error'] = "Error sending email. Check you email settings.";
				$this->logger->write_message("error", "Error emailing " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $cur_voucher->number) . " [id:" . $voucher_id . "]");
			}
			$this->load->view('voucher/email', $data);
			return;
		}
		return;
	}

	function addstockrow()
	{
		$i = time() + rand  (0, time()) + rand  (0, time()) + rand  (0, time());
		$stock_item_quantity = array(
			'name' => 'stock_item_quantity[' . $i . ']',
			'id' => 'stock_item_quantity[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => '',
			'class' => 'quantity-item',
		);
		$stock_item_rate_per_unit = array(
			'name' => 'stock_item_rate_per_unit[' . $i . ']',
			'id' => 'stock_item_rate_per_unit[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => '',
			'class' => 'rate-item',
		);
		$stock_item_amount = array(
			'name' => 'stock_item_amount[' . $i . ']',
			'id' => 'stock_item_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
			'class' => 'rate-item',
		);

		echo '<tr class="new-row">';
		echo "<td>" . form_input_stock_item('stock_item_id[' . $i . ']', 0) . "</td>";
		echo "<td>" . form_input($stock_item_quantity) . "</td>";
		echo "<td>" . form_input($stock_item_rate_per_unit) . "</td>";
		echo "<td>" . form_input($stock_item_amount) . "</td>";
		echo '<td>';
		echo img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Stock Item', 'class' => 'addstockrow'));
		echo '</td>';
		echo '<td>';
		echo img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Stock Item', 'class' => 'deletestockrow'));
		echo '</td>';
		echo '<td class="stock-item-balance"><div></div>';
		echo '</td>';
		echo '</tr>';
		return;
	}
}

/* End of file voucher.php */
/* Location: ./system/application/controllers/voucher.php */
