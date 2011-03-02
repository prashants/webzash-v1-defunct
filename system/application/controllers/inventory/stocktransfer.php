<?php

class StockTransfer extends Controller {

	function StockTransfer()
	{
		parent::Controller();
		$this->load->model('Voucher_model');
		$this->load->model('Ledger_model');
		$this->load->model('Stock_Item_model');
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

		if ($current_voucher_type['base_type'] == '1')
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		}

		$this->template->set('page_title', 'View ' . $current_voucher_type['name'] . ' Voucher');

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}

		/* Load current voucher details - account, entity, ledgers */
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 1)->order_by('id', 'asc');
		$cur_voucher_main_account = $this->db->get();
		if ($cur_voucher_main_account->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Purchase or Sale Ledger A/C.', 'error');
		}
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 2)->order_by('id', 'asc');
		$cur_voucher_main_entity = $this->db->get();
		if ($cur_voucher_main_entity->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Debtor or Creditor Ledger A/C.', 'error');
		}
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 3)->order_by('id', 'asc');
		$cur_voucher_ledgers = $this->db->get();
		if ($cur_voucher_ledgers->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Ledger A/C\'s.', 'error');
		}

		/* Load current stock items details */
		$this->db->from('stock_voucher_items')->where('voucher_id', $voucher_id)->order_by('id', 'asc');
		$cur_voucher_stock_items = $this->db->get();
		if ($cur_voucher_stock_items->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated stock items.', 'error');
		}

		$data['cur_voucher'] = $cur_voucher;
		$data['cur_voucher_main_account'] = $cur_voucher_main_account;
		$data['cur_voucher_main_entity'] = $cur_voucher_main_entity;
		$data['cur_voucher_ledgers'] = $cur_voucher_ledgers;
		$data['cur_voucher_stock_items'] = $cur_voucher_stock_items;
		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;
		$this->template->load('template', 'inventory/stockvoucher/view', $data);
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
		if ($current_voucher_type['stock_voucher_type'] != '3')
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
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
		if ($current_voucher_type['stock_voucher_type'] == '3')
		{
			/* TODO */
		}
		$this->form_validation->set_rules('voucher_narration', 'trim');
		$this->form_validation->set_rules('voucher_tag', 'Tag', 'trim|is_natural');

		/* stock item validation */
		if ($_POST)
		{
			foreach ($this->input->post('source_stock_item_id', TRUE) as $id => $stock_data)
			{
				$this->form_validation->set_rules('source_stock_item_quantity[' . $id . ']', 'Stock Item Quantity', 'trim|quantity');
				$this->form_validation->set_rules('source_stock_item_rate_per_unit[' . $id . ']', 'Stock Item Rate Per Unit', 'trim|currency');
				$this->form_validation->set_rules('source_stock_item_amount[' . $id . ']', 'Stock Item Amount', 'trim|currency');
			}
			foreach ($this->input->post('dest_stock_item_id', TRUE) as $id => $stock_data)
			{
				$this->form_validation->set_rules('dest_stock_item_quantity[' . $id . ']', 'Stock Item Quantity', 'trim|quantity');
				$this->form_validation->set_rules('dest_stock_item_rate_per_unit[' . $id . ']', 'Stock Item Rate Per Unit', 'trim|currency');
				$this->form_validation->set_rules('dest_stock_item_amount[' . $id . ']', 'Stock Item Amount', 'trim|currency');
			}
		}

		/* Repopulating form */
		if ($_POST)
		{
			$data['voucher_number']['value'] = $this->input->post('voucher_number', TRUE);
			$data['voucher_date']['value'] = $this->input->post('voucher_date', TRUE);
			$data['voucher_narration']['value'] = $this->input->post('voucher_narration', TRUE);
			$data['voucher_tag'] = $this->input->post('voucher_tag', TRUE);

			$data['source_stock_item_id'] = $this->input->post('source_stock_item_id', TRUE);
			$data['source_stock_item_quantity'] = $this->input->post('source_stock_item_quantity', TRUE);
			$data['source_stock_item_rate_per_unit'] = $this->input->post('source_stock_item_rate_per_unit', TRUE);
			$data['source_stock_item_amount'] = $this->input->post('source_stock_item_amount', TRUE);

			$data['dest_stock_item_id'] = $this->input->post('dest_stock_item_id', TRUE);
			$data['dest_stock_item_quantity'] = $this->input->post('dest_stock_item_quantity', TRUE);
			$data['dest_stock_item_rate_per_unit'] = $this->input->post('dest_stock_item_rate_per_unit', TRUE);
			$data['dest_stock_item_amount'] = $this->input->post('dest_stock_item_amount', TRUE);
		} else {
			for ($count = 0; $count <= 3; $count++)
			{
				$data['source_stock_item_id'][$count] = '0';
				$data['source_stock_item_quantity'][$count] = '';
				$data['source_stock_item_rate_per_unit'][$count] = '';
				$data['source_stock_item_amount'][$count] = '';
			}
			for ($count = 0; $count <= 3; $count++)
			{
				$data['dest_stock_item_id'][$count] = '0';
				$data['dest_stock_item_quantity'][$count] = '';
				$data['dest_stock_item_rate_per_unit'][$count] = '';
				$data['dest_stock_item_amount'][$count] = '';
			}
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/stocktransfer/add', $data);
			return;
		}
		else
		{
			$data_all_source_stock_item_id = $this->input->post('source_stock_item_id', TRUE);
			$data_all_source_stock_item_quantity = $this->input->post('source_stock_item_quantity', TRUE);
			$data_all_source_stock_item_rate_per_unit = $this->input->post('source_stock_item_rate_per_unit', TRUE);
			$data_all_source_stock_item_amount = $this->input->post('source_stock_item_amount', TRUE);

			$data_all_dest_stock_item_id = $this->input->post('dest_stock_item_id', TRUE);
			$data_all_dest_stock_item_quantity = $this->input->post('dest_stock_item_quantity', TRUE);
			$data_all_dest_stock_item_rate_per_unit = $this->input->post('dest_stock_item_rate_per_unit', TRUE);
			$data_all_dest_stock_item_amount = $this->input->post('dest_stock_item_amount', TRUE);

			$data_total_amount = 0;

			/* Setting Stock Item type */
			if ($current_voucher_type['stock_voucher_type'] == '1')
				$data_stock_item_type = 1;
			else
				$data_stock_item_type = 2;

			/* Checking for Valid Stock Item */
			$source_stock_item_present = FALSE;
			$data_total_source_stock_amount = 0;
			foreach ($data_all_source_stock_item_id as $id => $stock_data)
			{
				if ($data_all_source_stock_item_id[$id] < 1)
					continue;

				/* Check for valid stock item id */
				$this->db->from('stock_items')->where('id', $data_all_source_stock_item_id[$id]);
				$valid_stock_item_q = $this->db->get();
				if ($valid_stock_item_q->num_rows() < 1)
				{
					$this->messages->add('Invalid Source Stock Item.', 'error');
					$this->template->load('template', 'inventory/stocktransfer/add', $data);
					return;
				}
				$source_stock_item_present = TRUE;
				$data_total_source_stock_amount += $data_all_source_stock_item_amount[$id];
			}
			if ( ! $source_stock_item_present)
			{
				$this->messages->add('No Soruce Stock Item selected.', 'error');
				$this->template->load('template', 'inventory/stocktransfer/add', $data);
				return;
			}
			$dest_stock_item_present = FALSE;
			$data_total_dest_stock_amount = 0;
			foreach ($data_all_dest_stock_item_id as $id => $stock_data)
			{
				if ($data_all_dest_stock_item_id[$id] < 1)
					continue;

				/* Check for valid stock item id */
				$this->db->from('stock_items')->where('id', $data_all_dest_stock_item_id[$id]);
				$valid_stock_item_q = $this->db->get();
				if ($valid_stock_item_q->num_rows() < 1)
				{
					$this->messages->add('Invalid Destination Stock Item.', 'error');
					$this->template->load('template', 'inventory/stocktransfer/add', $data);
					return;
				}
				$dest_stock_item_present = TRUE;
				$data_total_dest_stock_amount += $data_all_dest_stock_item_amount[$id];
			}
			if ( ! $dest_stock_item_present)
			{
				$this->messages->add('No Destination Stock Item selected.', 'error');
				$this->template->load('template', 'inventory/stocktransfer/add', $data);
				return;
			}

			/* Total amount calculations */
			if ($data_total_source_stock_amount != $data_total_dest_stock_amount)
			{
				$this->messages->add('Source total does not match with Destination total.', 'error');
				$this->template->load('template', 'inventory/stocktransfer/add', $data);
				return;
			}
			if ($data_total_source_stock_amount < 0)
			{
				$this->messages->add('Source total cannot be negative.', 'error');
				$this->template->load('template', 'inventory/stocktransfer/add', $data);
				return;
			}
			if ($data_total_dest_stock_amount < 0)
			{
				$this->messages->add('Destination total cannot be negative.', 'error');
				$this->template->load('template', 'inventory/stocktransfer/add', $data);
				return;
			}
			$data_total_amount = $data_total_source_stock_amount;

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

			/* Adding Voucher */
			$this->db->trans_start();
			$insert_data = array(
				'number' => $data_number,
				'date' => $data_date,
				'narration' => $data_narration,
				'voucher_type' => $data_type,
				'tag_id' => $data_tag,
				'dr_total' => $data_total_amount,
				'cr_total' => $data_total_amount,
			);
			if ( ! $this->db->insert('vouchers', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Voucher.', 'error');
				$this->logger->write_message("error", "Error adding " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed inserting voucher");
				$this->template->load('template', 'inventory/stocktransfer/add', $data);
				return;
			} else {
				$voucher_id = $this->db->insert_id();
			}

			/* Adding source stock items */
			$data_all_source_stock_item_id = $this->input->post('source_stock_item_id', TRUE);
			$data_all_source_stock_item_quantity = $this->input->post('source_stock_item_quantity', TRUE);
			$data_all_source_stock_item_rate_per_unit = $this->input->post('source_stock_item_rate_per_unit', TRUE);
			$data_all_source_stock_item_amount = $this->input->post('source_stock_item_amount', TRUE);

			foreach ($data_all_source_stock_item_id as $id => $stock_data)
			{
				$data_source_stock_item_id = $data_all_source_stock_item_id[$id];

				if ($data_source_stock_item_id < 1)
					continue;

				$data_source_stock_item_quantity = $data_all_source_stock_item_quantity[$id];
				$data_source_stock_item_rate_per_unit = $data_all_source_stock_item_rate_per_unit[$id];
				$data_source_stock_item_amount = $data_all_source_stock_item_amount[$id];

				$insert_stock_data = array(
					'voucher_id' => $voucher_id,
					'stock_item_id' => $data_source_stock_item_id,
					'quantity' => $data_source_stock_item_quantity,
					'rate_per_unit' => $data_source_stock_item_rate_per_unit,
					'discount' => '',
					'total' => $data_source_stock_item_amount,
					'type' => '2',
				);
				if ( ! $this->db->insert('stock_voucher_items', $insert_stock_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Stock Item - ' . $data_source_stock_item_id . ' to Voucher.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed inserting stock item " . "[id:" . $data_source_stock_item_id . "]");
					$this->template->load('template', 'inventory/stocktransfer/add', $data);
					return;
				}
			}

			/* Adding destination stock items */
			$data_all_dest_stock_item_id = $this->input->post('dest_stock_item_id', TRUE);
			$data_all_dest_stock_item_quantity = $this->input->post('dest_stock_item_quantity', TRUE);
			$data_all_dest_stock_item_rate_per_unit = $this->input->post('dest_stock_item_rate_per_unit', TRUE);
			$data_all_dest_stock_item_amount = $this->input->post('dest_stock_item_amount', TRUE);

			foreach ($data_all_dest_stock_item_id as $id => $stock_data)
			{
				$data_dest_stock_item_id = $data_all_dest_stock_item_id[$id];

				if ($data_dest_stock_item_id < 1)
					continue;

				$data_dest_stock_item_quantity = $data_all_dest_stock_item_quantity[$id];
				$data_dest_stock_item_rate_per_unit = $data_all_dest_stock_item_rate_per_unit[$id];
				$data_dest_stock_item_amount = $data_all_dest_stock_item_amount[$id];

				$insert_stock_data = array(
					'voucher_id' => $voucher_id,
					'stock_item_id' => $data_dest_stock_item_id,
					'quantity' => $data_dest_stock_item_quantity,
					'rate_per_unit' => $data_dest_stock_item_rate_per_unit,
					'discount' => '',
					'total' => $data_dest_stock_item_amount,
					'type' => '1',
				);
				if ( ! $this->db->insert('stock_voucher_items', $insert_stock_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Stock Item - ' . $data_dest_stock_item_id . ' to Voucher.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed inserting stock item " . "[id:" . $data_dest_stock_item_id . "]");
					$this->template->load('template', 'inventory/stocktransfer/add', $data);
					return;
				}
			}

			/* Success */
			$this->db->trans_complete();

			$this->session->set_userdata('voucher_added_show_action', TRUE);
			$this->session->set_userdata('voucher_added_id', $voucher_id);
			$this->session->set_userdata('voucher_added_type_id', $voucher_type_id);
			$this->session->set_userdata('voucher_added_type_label', $current_voucher_type['label']);
			$this->session->set_userdata('voucher_added_type_name', $current_voucher_type['name']);
			$this->session->set_userdata('voucher_added_type_base_type', $current_voucher_type['base_type']);
			$this->session->set_userdata('voucher_added_number', $data_number);

			/* Showing success message in show() method since message is too long for storing it in session */
			$this->logger->write_message("success", "Added " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}
		return;
	}

	function edit($voucher_type, $voucher_id = 0)
	{
		/* Check access */
		if ( ! check_access('edit stock voucher'))
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
		$data['main_account_active'] = 0;
		$data['main_entity_active'] = 0;
		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;
		$data['voucher_tag'] = $cur_voucher->tag_id;
		$data['voucher_tags'] = $this->Tag_model->get_all_tags();
		$data['has_reconciliation'] = FALSE;

		/* Load current ledger details if not $_POST */
		if ( ! $_POST)
		{
			/* main - account */
			$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 1);
			$cur_main_account_q = $this->db->get();
			$cur_main_account = $cur_main_account_q->row();
			$data['main_account_active'] = $cur_main_account->ledger_id;

			/* main - entity */
			$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 2);
			$cur_main_entity_q = $this->db->get();
			$cur_main_entity = $cur_main_entity_q->row();
			$data['main_entity_active'] = $cur_main_entity->ledger_id;

			/* ledgers */
			$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 3);
			$cur_ledgers_q = $this->db->get();
			$counter = 0;
			foreach ($cur_ledgers_q->result() as $row)
			{
				$data['ledger_dc'][$counter] = $row->dc;
				$data['ledger_id'][$counter] = $row->ledger_id;
				$data['rate_item'][$counter] = $row->stock_rate;
				$data['amount_item'][$counter] = $row->amount;
				if ($row->reconciliation_date)
					$data['has_reconciliation'] = TRUE;
				$counter++;
			}
			/* one extra rows */
			$data['ledger_dc'][$counter] = 'D';
			$data['ledger_id'][$counter] = 0;
			$data['rate_item'][$counter] = "";
			$data['amount_item'][$counter] = "";
			$counter++;

			/* stock items */
			$this->db->from('stock_voucher_items')->where('voucher_id', $voucher_id);
			$cur_stock_item_q = $this->db->get();
			$counter = 0;
			foreach ($cur_stock_item_q->result() as $row)
			{
				$data['stock_item_id'][$counter] = $row->stock_item_id;
				$data['stock_item_quantity'][$counter] = $row->quantity;
				$data['stock_item_rate_per_unit'][$counter] = $row->rate_per_unit;
				$data['stock_item_discount'][$counter] = $row->discount;
				$data['stock_item_amount'][$counter] = $row->total;
				$counter++;
			}
			/* one extra rows */
			$data['stock_item_id'][$counter] = '0';
			$data['stock_item_quantity'][$counter] = "";
			$data['stock_item_rate_per_unit'][$counter] = "";
			$data['stock_item_discount'][$counter] = "";
			$data['stock_item_amount'][$counter] = "";
			$counter++;
		}

		/* Form validations */
		if ($current_voucher_type['numbering'] == '3')
			$this->form_validation->set_rules('voucher_number', 'Voucher Number', 'trim|is_natural_no_zero|uniquevouchernowithid[' . $voucher_type_id . '.' . $voucher_id . ']');
		else
			$this->form_validation->set_rules('voucher_number', 'Voucher Number', 'trim|required|is_natural_no_zero|uniquevouchernowithid[' . $voucher_type_id . '.' . $voucher_id . ']');
		$this->form_validation->set_rules('voucher_date', 'Voucher Date', 'trim|required|is_date|is_date_within_range');
		if ($current_voucher_type['stock_voucher_type'] == '1')
		{
			$this->form_validation->set_rules('main_account', 'Purchase Ledger A/C', 'trim|required');
			$this->form_validation->set_rules('main_entity', 'Creditors (Supplier)', 'trim|required');
		} else {
			$this->form_validation->set_rules('main_account', 'Sale Ledger A/C', 'trim|required');
			$this->form_validation->set_rules('main_entity', 'Debtor (Customer)', 'trim|required');
		}
		$this->form_validation->set_rules('voucher_narration', 'trim');
		$this->form_validation->set_rules('voucher_tag', 'Tag', 'trim|is_natural');

		/* Debit and Credit amount validation */
		if ($_POST)
		{
			foreach ($this->input->post('stock_item_id', TRUE) as $id => $stock_data)
			{
				$this->form_validation->set_rules('stock_item_quantity[' . $id . ']', 'Stock Item Quantity', 'trim|quantity');
				$this->form_validation->set_rules('stock_item_rate_per_unit[' . $id . ']', 'Stock Item Rate Per Unit', 'trim|currency');
				$this->form_validation->set_rules('stock_item_discount[' . $id . ']', 'Stock Item Discount', 'trim|discount');
				$this->form_validation->set_rules('stock_item_amount[' . $id . ']', 'Stock Item Amount', 'trim|currency');
			}
			foreach ($this->input->post('ledger_dc', TRUE) as $id => $ledger_data)
			{
				$this->form_validation->set_rules('rate_item[' . $id . ']', 'Rate %', 'trim|rate');
				$this->form_validation->set_rules('amount_item[' . $id . ']', 'Ledger Amount', 'trim|currency');
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

			$data['main_account_active'] = $this->input->post('main_account', TRUE);
			$data['main_entity_active'] = $this->input->post('main_entity', TRUE);

			$data['stock_item_id'] = $this->input->post('stock_item_id', TRUE);
			$data['stock_item_quantity'] = $this->input->post('stock_item_quantity', TRUE);
			$data['stock_item_rate_per_unit'] = $this->input->post('stock_item_rate_per_unit', TRUE);
			$data['stock_item_discount'] = $this->input->post('stock_item_discount', TRUE);
			$data['stock_item_amount'] = $this->input->post('stock_item_amount', TRUE);

			$data['ledger_dc'] = $this->input->post('ledger_dc', TRUE);
			$data['ledger_id'] = $this->input->post('ledger_id', TRUE);
			$data['rate_item'] = $this->input->post('rate_item', TRUE);
			$data['amount_item'] = $this->input->post('amount_item', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/stockvoucher/edit', $data);
		} else	{
			$data_main_account = $this->input->post('main_account', TRUE);
			$data_main_entity = $this->input->post('main_entity', TRUE);

			$data_all_stock_item_id = $this->input->post('stock_item_id', TRUE);
			$data_all_stock_item_quantity = $this->input->post('stock_item_quantity', TRUE);
			$data_all_stock_item_rate_per_unit = $this->input->post('stock_item_rate_per_unit', TRUE);
			$data_all_stock_item_discount = $this->input->post('stock_item_discount', TRUE);
			$data_all_stock_item_amount = $this->input->post('stock_item_amount', TRUE);

			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_rate_item = $this->input->post('rate_item', TRUE);
			$data_all_amount_item = $this->input->post('amount_item', TRUE);

			$data_total_amount = 0;

			/* Setting Stock Item type */
			if ($current_voucher_type['stock_voucher_type'] == '1')
				$data_stock_item_type = 1;
			else
				$data_stock_item_type = 2;

			/* Checking for Valid Stock Ledger A/C - account */
			if ($current_voucher_type['stock_voucher_type'] == '1')
				$this->db->from('ledgers')->where('id', $data_main_account)->where('type', 2);
			else
				$this->db->from('ledgers')->where('id', $data_main_account)->where('type', 3);
			$valid_main_account_q = $this->db->get();
			if ($valid_main_account_q->num_rows() < 1)
			{
				if ($current_voucher_type['stock_voucher_type'] == '1')
					$this->messages->add('Invalid Purchase Ledger A/C.', 'error');
				else
					$this->messages->add('Invalid Sale Ledger A/C.', 'error');
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
			}
			/* Checking for Valid Stock Ledger A/C - entity */
			if ($current_voucher_type['stock_voucher_type'] == '1')
				$this->db->from('ledgers')->where('id', $data_main_entity)->where('type', 4)->or_where('type', 1);
			else
				$this->db->from('ledgers')->where('id', $data_main_entity)->where('type', 5)->or_where('type', 1);
			$valid_main_account_q = $this->db->get();
			if ($valid_main_account_q->num_rows() < 1)
			{
				if ($current_voucher_type['stock_voucher_type'] == '1')
					$this->messages->add('Invalid Creditor (Supplier).', 'error');
				else
					$this->messages->add('Invalid Debtor (Customer).', 'error');
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
			}

			/* Checking for Valid Stock Item A/C */
			$stock_item_present = FALSE;
			$data_total_stock_amount = 0;
			foreach ($data_all_stock_item_id as $id => $stock_data)
			{
				if ($data_all_stock_item_id[$id] < 1)
					continue;

				/* Check for valid stock item id */
				$this->db->from('stock_items')->where('id', $data_all_stock_item_id[$id]);
				$valid_stock_item_q = $this->db->get();
				if ($valid_stock_item_q->num_rows() < 1)
				{
					$this->messages->add('Invalid Stock Item.', 'error');
					$this->template->load('template', 'inventory/stockvoucher/edit', $data);
					return;
				}
				$stock_item_present = TRUE;
				$data_total_stock_amount += $data_all_stock_item_amount[$id];
			}
			if ( ! $stock_item_present)
			{
				$this->messages->add('No Stock Item selected.', 'error');
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
			}

			/* Checking for Valid Ledgers A/C */
			$data_total_ledger_amount = 0;
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
					$this->template->load('template', 'inventory/stockvoucher/edit', $data);
					return;
				}
				if ($data_all_ledger_dc[$id] == 'D')
					$data_total_ledger_amount += $data_all_amount_item[$id];
				else
					$data_total_ledger_amount -= $data_all_amount_item[$id];
			}

			/* Total amount calculations */
			if ($current_voucher_type['stock_voucher_type'] == '1')
			{
				$data_main_account_total = $data_total_stock_amount;
				$data_main_entity_total = $data_total_stock_amount + $data_total_ledger_amount;
			} else {
				$data_main_account_total = $data_total_stock_amount + $data_total_ledger_amount;
				$data_main_entity_total = $data_total_stock_amount;
			}
			$data_total_amount = $data_total_stock_amount + $data_total_ledger_amount;
			if ($data_total_amount < 0)
			{
				$this->messages->add($current_voucher_type['name'] . ' Voucher total cannot be negative.', 'error');
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
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
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
			}

			/* TODO : Deleting all old ledger data, Bad solution */
			if ( ! $this->db->delete('stock_voucher_items', array('voucher_id' => $voucher_id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error deleting previous stock items from Voucher.', 'error');
				$this->logger->write_message("error", "Error deleting previous stock items from " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
			}

			if ( ! $this->db->delete('voucher_items', array('voucher_id' => $voucher_id))->where('stock_type', 3))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error deleting previous Ledger A/C\'s from Voucher.', 'error');
				$this->logger->write_message("error", "Error deleting previous voucher items for " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " [id:" . $voucher_id . "]");
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
			}

			/* Updating main - account */
			$update_data = array(
				'ledger_id' => $data_main_account,
				'amount' => $data_main_account_total,
				'dc' => '',
				'reconciliation_date' => NULL,
				'stock_type' => 1,
				'stock_rate' => '',
			);
			if ($current_voucher_type['stock_voucher_type'] == '1')
				$update_data['dc'] = 'D';
			else
				$update_data['dc'] = 'C';
			if ( ! $this->db->where('voucher_id', $voucher_id)->where('stock_type', 1)->update('voucher_items', $update_data))
			{
				$this->db->trans_rollback();
				if ($current_voucher_type['stock_voucher_type'] == '1')
				{
					$this->messages->add('Error updating Purchase Ledger A/C of Voucher.', 'error');
					$this->logger->write_message("error", "Error updating " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed updating purchase ledger " . "[id:" . $data_main_account . "]");
				} else {
					$this->messages->add('Error updating Sale Ledger A/C of Voucher.', 'error');
					$this->logger->write_message("error", "Error updating " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed updating sale ledger " . "[id:" . $data_main_account . "]");
				}
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
			}

			/* Updating main - entity */
			$update_data = array(
				'ledger_id' => $data_main_entity,
				'amount' => $data_main_entity_total,
				'dc' => '',
				'reconciliation_date' => NULL,
				'stock_type' => 2,
				'stock_rate' => '',
			);
			if ($current_voucher_type['stock_voucher_type'] == '1')
				$insert_data['dc'] = 'C';
			else
				$insert_data['dc'] = 'D';
			if ( ! $this->db->where('voucher_id', $voucher_id)->where('stock_type', 2)->update('voucher_items', $update_data))
			{
				$this->db->trans_rollback();
				if ($current_voucher_type['stock_voucher_type'] == '1')
				{
					$this->messages->add('Error updating Creditor (Supplier) of Voucher.', 'error');
					$this->logger->write_message("error", "Error updating " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed updating creditor ledger " . "[id:" . $data_main_entity . "]");
				} else {
					$this->messages->add('Error updating Debtor (Customer) of Voucher.', 'error');
					$this->logger->write_message("error", "Error updating " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed updating debtor ledger " . "[id:" . $data_main_entity . "]");
				}
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
				return;
			}

			/* Adding stock items */
			$data_all_stock_item_id = $this->input->post('stock_item_id', TRUE);
			$data_all_stock_item_quantity = $this->input->post('stock_item_quantity', TRUE);
			$data_all_stock_item_rate_per_unit = $this->input->post('stock_item_rate_per_unit', TRUE);
			$data_all_stock_item_discount = $this->input->post('stock_item_discount', TRUE);
			$data_all_stock_item_amount = $this->input->post('stock_item_amount', TRUE);

			foreach ($data_all_stock_item_id as $id => $stock_data)
			{
				$data_stock_item_id = $data_all_stock_item_id[$id];

				if ($data_stock_item_id < 1)
					continue;

				$data_stock_item_quantity = $data_all_stock_item_quantity[$id];
				$data_stock_item_rate_per_unit = $data_all_stock_item_rate_per_unit[$id];
				$data_stock_item_discount = $data_all_stock_item_discount[$id];
				$data_stock_item_amount = $data_all_stock_item_amount[$id];

				$insert_stock_data = array(
					'voucher_id' => $voucher_id,
					'stock_item_id' => $data_stock_item_id,
					'quantity' => $data_stock_item_quantity,
					'rate_per_unit' => $data_stock_item_rate_per_unit,
					'discount' => $data_stock_item_discount,
					'total' => $data_stock_item_amount,
					'type' => $data_stock_item_type,
				);
				if ( ! $this->db->insert('stock_voucher_items', $insert_stock_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Stock Item - ' . $data_ledger_id . ' to Voucher.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed inserting stock item " . "[id:" . $data_ledger_id . "]");
					$this->template->load('template', 'inventory/stockvoucher/edit', $data);
					return;
				}
			}

			/* Adding ledger accounts */
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_rate_item = $this->input->post('rate_item', TRUE);
			$data_all_amount_item = $this->input->post('amount_item', TRUE);

			foreach ($data_all_ledger_dc as $id => $ledger_data)
			{
				$data_ledger_dc = $data_all_ledger_dc[$id];
				$data_ledger_id = $data_all_ledger_id[$id];

				if ($data_ledger_id < 1)
					continue;

				$data_rate = $data_all_rate_item[$id];
				$data_amount = $data_all_amount_item[$id];

				$insert_ledger_data = array(
					'voucher_id' => $voucher_id,
					'ledger_id' => $data_ledger_id,
					'amount' => $data_amount,
					'dc' => $data_ledger_dc,
					'reconciliation_date' => NULL,
					'stock_type' => 3,
					'stock_rate' => $data_rate,
				);
				if ( ! $this->db->insert('voucher_items', $insert_ledger_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Ledger A/C - ' . $data_ledger_id . ' to Voucher.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed inserting voucher ledger item " . "[id:" . $data_ledger_id . "]");
					$this->template->load('template', 'inventory/stockvoucher/edit', $data);
					return;
				}
			}

			/* Updating Debit and Credit Total - vouchers */
			$update_data = array(
				'dr_total' => $data_total_amount,
				'cr_total' => $data_total_amount,
			);

			if ( ! $this->db->where('id', $voucher_id)->update('vouchers', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Voucher total.', 'error');
				$this->logger->write_message("error", "Error updating " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $data_number) . " since failed updating debit and credit total");
				$this->template->load('template', 'inventory/stockvoucher/edit', $data);
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
		if ( ! check_access('delete stock voucher'))
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
		if ( ! $this->db->delete('stock_voucher_items', array('voucher_id' => $voucher_id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Stock Items.', 'error');
			$this->logger->write_message("error", "Error deleting stock item entries for " . $current_voucher_type['name'] . " Voucher number " . full_voucher_number($voucher_type_id, $cur_voucher->number) . " [id:" . $voucher_id . "]");
			redirect('voucher/view/' . $current_voucher_type['label'] . '/' . $voucher_id);
			return;
		}
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

		/* Check access */
		if ( ! check_access('download stock voucher'))
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

		if ($current_voucher_type['base_type'] == '1')
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		}

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}

		/* Load current voucher details - account, entity, ledgers */
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 1)->order_by('id', 'asc');
		$cur_voucher_main_account = $this->db->get();
		if ($cur_voucher_main_account->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Purchase or Sale Ledger A/C.', 'error');
		}
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 2)->order_by('id', 'asc');
		$cur_voucher_main_entity = $this->db->get();
		if ($cur_voucher_main_entity->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Debtor or Creditor Ledger A/C.', 'error');
		}
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 3)->order_by('id', 'asc');
		$cur_voucher_ledgers = $this->db->get();
		if ($cur_voucher_ledgers->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Ledger A/C\'s.', 'error');
		}

		/* Load current stock items details */
		$this->db->from('stock_voucher_items')->where('voucher_id', $voucher_id)->order_by('id', 'asc');
		$cur_voucher_stock_items = $this->db->get();
		if ($cur_voucher_stock_items->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated stock items.', 'error');
		}

		$data['cur_voucher'] = $cur_voucher;
		$data['cur_voucher_main_account'] = $cur_voucher_main_account;
		$data['cur_voucher_main_entity'] = $cur_voucher_main_entity;
		$data['cur_voucher_ledgers'] = $cur_voucher_ledgers;
		$data['cur_voucher_stock_items'] = $cur_voucher_stock_items;
		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;

		/* Download Voucher */
		$file_name = $current_voucher_type['name'] . '_voucher_' . $cur_voucher->number . ".html";
		$download_data = $this->load->view('inventory/stockvoucher/downloadpreview', $data, TRUE);
		force_download($file_name, $download_data);
		return;
	}

	function printpreview($voucher_type, $voucher_id = 0)
	{
		$this->load->model('Setting_model');

		/* Check access */
		if ( ! check_access('print stock voucher'))
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

		if ($current_voucher_type['base_type'] == '1')
		{
			$this->messages->add('Invalid Voucher type.', 'error');
			redirect('voucher/show/all');
			return;
		}

		/* Load current voucher details */
		if ( ! $cur_voucher = $this->Voucher_model->get_voucher($voucher_id, $voucher_type_id))
		{
			$this->messages->add('Invalid Voucher.', 'error');
			redirect('voucher/show/' . $current_voucher_type['label']);
			return;
		}

		/* Load current voucher details - account, entity, ledgers */
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 1)->order_by('id', 'asc');
		$cur_voucher_main_account = $this->db->get();
		if ($cur_voucher_main_account->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Purchase or Sale Ledger A/C.', 'error');
		}
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 2)->order_by('id', 'asc');
		$cur_voucher_main_entity = $this->db->get();
		if ($cur_voucher_main_entity->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Debtor or Creditor Ledger A/C.', 'error');
		}
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 3)->order_by('id', 'asc');
		$cur_voucher_ledgers = $this->db->get();
		if ($cur_voucher_ledgers->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Ledger A/C\'s.', 'error');
		}

		/* Load current stock items details */
		$this->db->from('stock_voucher_items')->where('voucher_id', $voucher_id)->order_by('id', 'asc');
		$cur_voucher_stock_items = $this->db->get();
		if ($cur_voucher_stock_items->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated stock items.', 'error');
		}

		$data['cur_voucher'] = $cur_voucher;
		$data['cur_voucher_main_account'] = $cur_voucher_main_account;
		$data['cur_voucher_main_entity'] = $cur_voucher_main_entity;
		$data['cur_voucher_ledgers'] = $cur_voucher_ledgers;
		$data['cur_voucher_stock_items'] = $cur_voucher_stock_items;
		$data['voucher_type_id'] = $voucher_type_id;
		$data['current_voucher_type'] = $current_voucher_type;

		$this->load->view('inventory/stockvoucher/printpreview', $data);
		return;
	}

	function email($voucher_type, $voucher_id = 0)
	{
		$this->load->model('Setting_model');
		$this->load->library('email');

		/* Check access */
		if ( ! check_access('email stock voucher'))
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

		/* Load current voucher details - account, entity, ledgers */
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 1)->order_by('id', 'asc');
		$cur_voucher_main_account = $this->db->get();
		if ($cur_voucher_main_account->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Purchase or Sale Ledger A/C.', 'error');
		}
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 2)->order_by('id', 'asc');
		$cur_voucher_main_entity = $this->db->get();
		if ($cur_voucher_main_entity->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Debtor or Creditor Ledger A/C.', 'error');
		}
		$this->db->from('voucher_items')->where('voucher_id', $voucher_id)->where('stock_type', 3)->order_by('id', 'asc');
		$cur_voucher_ledgers = $this->db->get();
		if ($cur_voucher_ledgers->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated Ledger A/C\'s.', 'error');
		}

		/* Load current stock items details */
		$this->db->from('stock_voucher_items')->where('voucher_id', $voucher_id)->order_by('id', 'asc');
		$cur_voucher_stock_items = $this->db->get();
		if ($cur_voucher_stock_items->num_rows() < 1)
		{
			$this->messages->add('Voucher has no associated stock items.', 'error');
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
			$this->load->view('inventory/stockvoucher/email', $data);
			return;
		}
		else
		{
			$data['cur_voucher'] = $cur_voucher;
			$data['cur_voucher_main_account'] = $cur_voucher_main_account;
			$data['cur_voucher_main_entity'] = $cur_voucher_main_entity;
			$data['cur_voucher_ledgers'] = $cur_voucher_ledgers;
			$data['cur_voucher_stock_items'] = $cur_voucher_stock_items;

			/* Preparing message */
			$message = $this->load->view('inventory/stockvoucher/emailpreview', $data, TRUE);

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
			$this->load->view('inventory/stockvoucher/email', $data);
			return;
		}
		return;
	}

	function addrow()
	{
		$i = time() + rand  (0, time()) + rand  (0, time()) + rand  (0, time());
		$rate_item = array(
			'name' => 'rate_item[' . $i . ']',
			'id' => 'rate_item[' . $i . ']',
			'maxlength' => '5',
			'size' => '5',
			'value' => isset($dr_amount[$i]) ? $dr_amount[$i] : "",
			'class' => 'dr-item',
		);
		$amount_item = array(
			'name' => 'amount_item[' . $i . ']',
			'id' => 'amount_item[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => isset($cr_amount[$i]) ? $cr_amount[$i] : "",
			'class' => 'cr-item',
		);
		echo "<tr>";
		echo "<td>" . form_dropdown_dc('ledger_dc[' . $i . ']', 'D') . "</td>";
		echo "<td>" . form_input_ledger('ledger_id[' . $i . ']', '0') . "</td>";
		echo "<td>" . form_input($rate_item) . "</td>";
		echo "<td>" . form_input($amount_item) . "</td>";
		echo "<td>" . img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Ledger', 'class' => 'addrow')) . "</td>";
		echo "<td>" . img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Ledger', 'class' => 'deleterow')) . "</td>";
		echo "<td class=\"ledger-balance\"><div></div></td>";
		echo "</tr>";
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
		$stock_item_discount = array(
			'name' => 'stock_item_discount[' . $i . ']',
			'id' => 'stock_item_discount[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => '',
			'class' => 'discount-item',
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
		echo "<td>" . form_input($stock_item_discount) . "</td>";
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
/* Location: ./system/application/controllers/inventory/stocktransfer.php */
