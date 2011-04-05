<?php

class Entry extends Controller {

	function Entry()
	{
		parent::Controller();
		$this->load->model('Entry_model');
		$this->load->model('Ledger_model');
		$this->load->model('Inventory_Item_model');
		$this->load->model('Tag_model');
		return;
	}

	function index()
	{
		redirect('entry/show/all');
		return;
	}

	function view($entry_type, $entry_id = 0)
	{
		/* Entry Type */
		$entry_type_id = entry_type_name_to_id($entry_type);
		if ( ! $entry_type_id)
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		} else {
			$current_entry_type = entry_type_info($entry_type_id);
		}

		if ($current_entry_type['base_type'] == '1')
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		}

		$this->template->set('page_title', 'View ' . $current_entry_type['name'] . ' Entry');

		/* Load current entry details */
		if ( ! $cur_entry = $this->Entry_model->get_entry($entry_id, $entry_type_id))
		{
			$this->messages->add('Invalid Entry.', 'error');
			redirect('entry/show/' . $current_entry_type['label']);
			return;
		}

		/* Load current entry details - account, entity, ledgers */
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 1)->order_by('id', 'asc');
		$cur_entry_main_account = $this->db->get();
		if ($cur_entry_main_account->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Purchase or Sale Ledger account.', 'error');
		}
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 2)->order_by('id', 'asc');
		$cur_entry_main_entity = $this->db->get();
		if ($cur_entry_main_entity->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Debtor or Creditor Ledger account.', 'error');
		}
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 3)->order_by('id', 'asc');
		$cur_entry_ledgers = $this->db->get();

		/* Load current inventory items details */
		$this->db->from('inventory_entry_items')->where('entry_id', $entry_id)->order_by('id', 'asc');
		$cur_entry_inventory_items = $this->db->get();
		if ($cur_entry_inventory_items->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated inventory items.', 'error');
		}

		$data['cur_entry'] = $cur_entry;
		$data['cur_entry_main_account'] = $cur_entry_main_account;
		$data['cur_entry_main_entity'] = $cur_entry_main_entity;
		$data['cur_entry_ledgers'] = $cur_entry_ledgers;
		$data['cur_entry_inventory_items'] = $cur_entry_inventory_items;
		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$this->template->load('template', 'inventory/entry/view', $data);
		return;
	}

	function add($entry_type)
	{
		/* Check access */
		if ( ! check_access('create inventory entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('entry/show/' . $entry_type);
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('inventory/entry/show/' . $entry_type);
			return;
		}

		/* Entry Type */
		$entry_type_id = entry_type_name_to_id($entry_type);
		if ( ! $entry_type_id)
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		} else {
			$current_entry_type = entry_type_info($entry_type_id);
		}

		$this->template->set('page_title', 'Add ' . $current_entry_type['name'] . ' Entry');

		/* Form fields */
		$data['entry_number'] = array(
			'name' => 'entry_number',
			'id' => 'entry_number',
			'maxlength' => '11',
			'size' => '11',
			'value' => '',
		);
		$data['entry_date'] = array(
			'name' => 'entry_date',
			'id' => 'entry_date',
			'maxlength' => '11',
			'size' => '11',
			'value' => date_today_php(),
		);
		$data['entry_narration'] = array(
			'name' => 'entry_narration',
			'id' => 'entry_narration',
			'cols' => '50',
			'rows' => '4',
			'value' => '',
		);
		$data['main_account_active'] = 0;
		$data['main_entity_active'] = 0;
		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$data['entry_tags'] = $this->Tag_model->get_all_tags();
		$data['entry_tag'] = 0;

		/* Form validations */
		if ($current_entry_type['numbering'] == '2')
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|required|is_natural_no_zero|uniquevoucherno[' . $entry_type_id . ']');
		else if ($current_entry_type['numbering'] == '3')
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|is_natural_no_zero|uniquevoucherno[' . $entry_type_id . ']');
		else
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|is_natural_no_zero|uniquevoucherno[' . $entry_type_id . ']');
		$this->form_validation->set_rules('entry_date', 'Entry Date', 'trim|required|is_date|is_date_within_range');
		if ($current_entry_type['inventory_entry_type'] == '1')
		{
			$this->form_validation->set_rules('main_account', 'Purchase Ledger', 'trim|required');
			$this->form_validation->set_rules('main_entity', 'Creditors (Supplier)', 'trim|required');
		} else {
			$this->form_validation->set_rules('main_account', 'Sale Ledger', 'trim|required');
			$this->form_validation->set_rules('main_entity', 'Debtor (Customer)', 'trim|required');
		}
		$this->form_validation->set_rules('entry_narration', 'trim');
		$this->form_validation->set_rules('entry_tag', 'Tag', 'trim|is_natural');

		/* Debit and Credit amount validation */
		if ($_POST)
		{
			foreach ($this->input->post('inventory_item_id', TRUE) as $id => $inventory_data)
			{
				$this->form_validation->set_rules('inventory_item_quantity[' . $id . ']', 'Inventory Item Quantity', 'trim|quantity');
				$this->form_validation->set_rules('inventory_item_rate_per_unit[' . $id . ']', 'Inventory Item Rate Per Unit', 'trim|currency');
				$this->form_validation->set_rules('inventory_item_discount[' . $id . ']', 'Inventory Item Discount', 'trim|discount');
				$this->form_validation->set_rules('inventory_item_amount[' . $id . ']', 'Inventory Item Amount', 'trim|currency');
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
			$data['entry_number']['value'] = $this->input->post('entry_number', TRUE);
			$data['entry_date']['value'] = $this->input->post('entry_date', TRUE);
			$data['entry_narration']['value'] = $this->input->post('entry_narration', TRUE);
			$data['entry_tag'] = $this->input->post('entry_tag', TRUE);

			$data['main_account_active'] = $this->input->post('main_account', TRUE);
			$data['main_entity_active'] = $this->input->post('main_entity', TRUE);

			$data['inventory_item_id'] = $this->input->post('inventory_item_id', TRUE);
			$data['inventory_item_quantity'] = $this->input->post('inventory_item_quantity', TRUE);
			$data['inventory_item_rate_per_unit'] = $this->input->post('inventory_item_rate_per_unit', TRUE);
			$data['inventory_item_discount'] = $this->input->post('inventory_item_discount', TRUE);
			$data['inventory_item_amount'] = $this->input->post('inventory_item_amount', TRUE);

			$data['ledger_dc'] = $this->input->post('ledger_dc', TRUE);
			$data['ledger_id'] = $this->input->post('ledger_id', TRUE);
			$data['rate_item'] = $this->input->post('rate_item', TRUE);
			$data['amount_item'] = $this->input->post('amount_item', TRUE);
		} else {
			for ($count = 0; $count <= 3; $count++)
			{
				$data['inventory_item_id'][$count] = '0';
				$data['inventory_item_quantity'][$count] = '';
				$data['inventory_item_rate_per_unit'][$count] = '';
				$data['inventory_item_discount'][$count] = '';
				$data['inventory_item_amount'][$count] = '';
			}
			for ($count = 0; $count <= 1; $count++)
			{
				$data['ledger_dc'][$count] = "D";
				$data['ledger_id'][$count] = 0;
				$data['rate_item'][$count] = "";
				$data['amount_item'][$count] = "";
			}
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/entry/add', $data);
			return;
		}
		else
		{
			$data_main_account = $this->input->post('main_account', TRUE);
			$data_main_entity = $this->input->post('main_entity', TRUE);

			$data_all_inventory_item_id = $this->input->post('inventory_item_id', TRUE);
			$data_all_inventory_item_quantity = $this->input->post('inventory_item_quantity', TRUE);
			$data_all_inventory_item_rate_per_unit = $this->input->post('inventory_item_rate_per_unit', TRUE);
			$data_all_inventory_item_discount = $this->input->post('inventory_item_discount', TRUE);
			$data_all_inventory_item_amount = $this->input->post('inventory_item_amount', TRUE);

			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_rate_item = $this->input->post('rate_item', TRUE);
			$data_all_amount_item = $this->input->post('amount_item', TRUE);

			$data_total_amount = 0;

			/* Setting Inventory Item type */
			if ($current_entry_type['inventory_entry_type'] == '1')
				$data_inventory_item_type = 1;
			else
				$data_inventory_item_type = 2;

			/* Checking for Valid Inventory Ledger account - account */
			if ($current_entry_type['inventory_entry_type'] == '1')
				$this->db->from('ledgers')->where('id', $data_main_account)->where('type', 2);
			else
				$this->db->from('ledgers')->where('id', $data_main_account)->where('type', 3);
			$valid_main_account_q = $this->db->get();
			if ($valid_main_account_q->num_rows() < 1)
			{
				if ($current_entry_type['inventory_entry_type'] == '1')
					$this->messages->add('Invalid Purchase Ledger.', 'error');
				else
					$this->messages->add('Invalid Sale Ledger.', 'error');
				$this->template->load('template', 'inventory/entry/add', $data);
				return;
			}
			/* Checking for Valid Inventory Ledger account - entity */
			if ($current_entry_type['inventory_entry_type'] == '1')
				$this->db->from('ledgers')->where('id', $data_main_entity)->where('type', 4)->or_where('type', 1);
			else
				$this->db->from('ledgers')->where('id', $data_main_entity)->where('type', 5)->or_where('type', 1);
			$valid_main_account_q = $this->db->get();
			if ($valid_main_account_q->num_rows() < 1)
			{
				if ($current_entry_type['inventory_entry_type'] == '1')
					$this->messages->add('Invalid Creditor (Supplier).', 'error');
				else
					$this->messages->add('Invalid Debtor (Customer).', 'error');
				$this->template->load('template', 'inventory/entry/add', $data);
				return;
			}

			/* Checking for Valid Inventory Item account */
			$inventory_item_present = FALSE;
			$data_total_inventory_amount = 0;
			foreach ($data_all_inventory_item_id as $id => $inventory_data)
			{
				if ($data_all_inventory_item_id[$id] < 1)
					continue;

				/* Check for valid inventory item id */
				$this->db->from('inventory_items')->where('id', $data_all_inventory_item_id[$id]);
				$valid_inventory_item_q = $this->db->get();
				if ($valid_inventory_item_q->num_rows() < 1)
				{
					$this->messages->add('Invalid Inventory Item.', 'error');
					$this->template->load('template', 'inventory/entry/add', $data);
					return;
				}
				$inventory_item_present = TRUE;
				$data_total_inventory_amount += $data_all_inventory_item_amount[$id];
			}
			if ( ! $inventory_item_present)
			{
				$this->messages->add('No Inventory Item selected.', 'error');
				$this->template->load('template', 'inventory/entry/add', $data);
				return;
			}

			/* Checking for Valid Ledgers account */
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
					$this->messages->add('Invalid Ledger account.', 'error');
					$this->template->load('template', 'inventory/entry/add', $data);
					return;
				}
				if ($data_all_ledger_dc[$id] == 'D')
					$data_total_ledger_amount += $data_all_amount_item[$id];
				else
					$data_total_ledger_amount -= $data_all_amount_item[$id];
			}

			/* Total amount calculations */
			if ($current_entry_type['inventory_entry_type'] == '1')
			{
				$data_main_account_total = $data_total_inventory_amount;
				$data_main_entity_total = $data_total_inventory_amount + $data_total_ledger_amount;
			} else {
				$data_main_account_total = $data_total_inventory_amount + $data_total_ledger_amount;
				$data_main_entity_total = $data_total_inventory_amount;
			}
			$data_total_amount = $data_total_inventory_amount + $data_total_ledger_amount;
			if ($data_total_amount < 0)
			{
				$this->messages->add($current_entry_type['name'] . ' Entry total cannot be negative.', 'error');
				$this->template->load('template', 'inventory/entry/add', $data);
				return;
			}

			/* Adding main entry */
			if ($current_entry_type['numbering'] == '2')
			{
				$data_number = $this->input->post('entry_number', TRUE);
			} else if ($current_entry_type['numbering'] == '3') {
				$data_number = $this->input->post('entry_number', TRUE);
				if ( ! $data_number)
					$data_number = NULL;
			} else {
				if ($this->input->post('entry_number', TRUE))
					$data_number = $this->input->post('entry_number', TRUE);
				else
					$data_number = $this->Entry_model->next_entry_number($entry_type_id);
			}

			$data_date = $this->input->post('entry_date', TRUE);
			$data_narration = $this->input->post('entry_narration', TRUE);
			$data_tag = $this->input->post('entry_tag', TRUE);
			if ($data_tag < 1)
				$data_tag = NULL;
			$data_type = $entry_type_id;
			$data_date = date_php_to_mysql($data_date); // Converting date to MySQL
			$entry_id = NULL;

			/* Adding Entry */
			$this->db->trans_start();
			$insert_data = array(
				'number' => $data_number,
				'date' => $data_date,
				'narration' => $data_narration,
				'entry_type' => $data_type,
				'tag_id' => $data_tag,
			);
			if ( ! $this->db->insert('vouchers', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Entry.', 'error');
				$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting entry");
				$this->template->load('template', 'inventory/entry/add', $data);
				return;
			} else {
				$entry_id = $this->db->insert_id();
			}

			/* Adding main - account */
			$insert_data = array(
				'entry_id' => $entry_id,
				'ledger_id' => $data_main_account,
				'amount' => $data_main_account_total,
				'dc' => '',
				'reconciliation_date' => NULL,
				'inventory_type' => 1,
				'inventory_rate' => '',
			);
			if ($current_entry_type['inventory_entry_type'] == '1')
				$insert_data['dc'] = 'D';
			else
				$insert_data['dc'] = 'C';
			if ( ! $this->db->insert('entry_items', $insert_data))
			{
				$this->db->trans_rollback();
				if ($current_entry_type['inventory_entry_type'] == '1')
				{
					$this->messages->add('Error adding Purchase Ledger account to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting purchase ledger " . "[id:" . $data_main_account . "]");
				} else {
					$this->messages->add('Error adding Sale Ledger account to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting sale ledger " . "[id:" . $data_main_account . "]");
				}
				$this->template->load('template', 'inventory/entry/add', $data);
				return;
			} else {
				$main_entry_id = $this->db->insert_id();
			}

			/* Adding main - entity */
			$insert_data = array(
				'entry_id' => $entry_id,
				'ledger_id' => $data_main_entity,
				'amount' => $data_main_entity_total,
				'dc' => '',
				'reconciliation_date' => NULL,
				'inventory_type' => 2,
				'inventory_rate' => '',
			);
			if ($current_entry_type['inventory_entry_type'] == '1')
				$insert_data['dc'] = 'C';
			else
				$insert_data['dc'] = 'D';
			if ( ! $this->db->insert('entry_items', $insert_data))
			{
				$this->db->trans_rollback();
				if ($current_entry_type['inventory_entry_type'] == '1')
				{
					$this->messages->add('Error adding Creditor (Supplier) to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting creditor ledger " . "[id:" . $data_main_entity . "]");
				} else {
					$this->messages->add('Error adding Debtor (Customer) - to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting debtor ledger " . "[id:" . $data_main_entity . "]");
				}
				$this->template->load('template', 'inventory/entry/add', $data);
				return;
			} else {
				$entity_entry_id = $this->db->insert_id();
			}

			/* Adding inventory items */
			$data_all_inventory_item_id = $this->input->post('inventory_item_id', TRUE);
			$data_all_inventory_item_quantity = $this->input->post('inventory_item_quantity', TRUE);
			$data_all_inventory_item_rate_per_unit = $this->input->post('inventory_item_rate_per_unit', TRUE);
			$data_all_inventory_item_discount = $this->input->post('inventory_item_discount', TRUE);
			$data_all_inventory_item_amount = $this->input->post('inventory_item_amount', TRUE);

			foreach ($data_all_inventory_item_id as $id => $inventory_data)
			{
				$data_inventory_item_id = $data_all_inventory_item_id[$id];

				if ($data_inventory_item_id < 1)
					continue;

				$data_inventory_item_quantity = $data_all_inventory_item_quantity[$id];
				$data_inventory_item_rate_per_unit = $data_all_inventory_item_rate_per_unit[$id];
				$data_inventory_item_discount = $data_all_inventory_item_discount[$id];
				$data_inventory_item_amount = $data_all_inventory_item_amount[$id];

				$insert_inventory_data = array(
					'entry_id' => $entry_id,
					'inventory_item_id' => $data_inventory_item_id,
					'quantity' => $data_inventory_item_quantity,
					'rate_per_unit' => $data_inventory_item_rate_per_unit,
					'discount' => $data_inventory_item_discount,
					'total' => $data_inventory_item_amount,
					'type' => $data_inventory_item_type,
				);
				if ( ! $this->db->insert('inventory_entry_items', $insert_inventory_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Inventory Item - ' . $data_inventory_item_id . ' to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting inventory item " . "[id:" . $data_inventory_item_id . "]");
					$this->template->load('template', 'inventory/entry/add', $data);
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
					'entry_id' => $entry_id,
					'ledger_id' => $data_ledger_id,
					'amount' => $data_amount,
					'dc' => $data_ledger_dc,
					'reconciliation_date' => NULL,
					'inventory_type' => 3,
					'inventory_rate' => $data_rate,
				);
				if ( ! $this->db->insert('entry_items', $insert_ledger_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Ledger account - ' . $data_ledger_id . ' to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting entry ledger item " . "[id:" . $data_ledger_id . "]");
					$this->template->load('template', 'inventory/entry/add', $data);
					return;
				}
			}

			/* Updating Debit and Credit Total - entries */
			$update_data = array(
				'dr_total' => $data_total_amount,
				'cr_total' => $data_total_amount,
			);

			if ( ! $this->db->where('id', $entry_id)->update('vouchers', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Entry total.', 'error');
				$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed updating debit and credit total");
				$this->template->load('template', 'inventory/entry/add', $data);
				return;
			}

			/* Success */
			$this->db->trans_complete();

			$this->session->set_userdata('entry_added_show_action', TRUE);
			$this->session->set_userdata('entry_added_id', $entry_id);
			$this->session->set_userdata('entry_added_type_id', $entry_type_id);
			$this->session->set_userdata('entry_added_type_label', $current_entry_type['label']);
			$this->session->set_userdata('entry_added_type_name', $current_entry_type['name']);
			$this->session->set_userdata('entry_added_type_base_type', $current_entry_type['base_type']);
			$this->session->set_userdata('entry_added_number', $data_number);

			/* Showing success message in show() method since message is too long for storing it in session */
			$this->logger->write_message("success", "Added " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
			redirect('entry/show/' . $current_entry_type['label']);
			$this->template->load('template', 'inventory/entry/add', $data);
			return;
		}
		return;
	}

	function edit($entry_type, $entry_id = 0)
	{
		/* Check access */
		if ( ! check_access('edit inventory entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/entry/show/' . $entry_type);
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('entry/show/' . $entry_type);
			return;
		}

		/* Entry Type */
		$entry_type_id = entry_type_name_to_id($entry_type);
		if ( ! $entry_type_id)
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		} else {
			$current_entry_type = entry_type_info($entry_type_id);
		}

		$this->template->set('page_title', 'Edit ' . $current_entry_type['name'] . ' Entry');

		/* Load current entry details */
		if ( ! $cur_entry = $this->Entry_model->get_entry($entry_id, $entry_type_id))
		{
			$this->messages->add('Invalid Entry.', 'error');
			redirect('entry/show/' . $current_entry_type['label']);
			return;
		}

		/* Form fields - Entry */
		$data['entry_number'] = array(
			'name' => 'entry_number',
			'id' => 'entry_number',
			'maxlength' => '11',
			'size' => '11',
			'value' => $cur_entry->number,
		);
		$data['entry_date'] = array(
			'name' => 'entry_date',
			'id' => 'entry_date',
			'maxlength' => '11',
			'size' => '11',
			'value' => date_mysql_to_php($cur_entry->date),
		);
		$data['entry_narration'] = array(
			'name' => 'entry_narration',
			'id' => 'entry_narration',
			'cols' => '50',
			'rows' => '4',
			'value' => $cur_entry->narration,
		);
		$data['entry_id'] = $entry_id;
		$data['main_account_active'] = 0;
		$data['main_entity_active'] = 0;
		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$data['entry_tag'] = $cur_entry->tag_id;
		$data['entry_tags'] = $this->Tag_model->get_all_tags();
		$data['has_reconciliation'] = FALSE;

		/* Load current ledger details if not $_POST */
		if ( ! $_POST)
		{
			/* main - account */
			$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 1);
			$cur_main_account_q = $this->db->get();
			$cur_main_account = $cur_main_account_q->row();
			$data['main_account_active'] = $cur_main_account->ledger_id;

			/* main - entity */
			$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 2);
			$cur_main_entity_q = $this->db->get();
			$cur_main_entity = $cur_main_entity_q->row();
			$data['main_entity_active'] = $cur_main_entity->ledger_id;

			/* ledgers */
			$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 3);
			$cur_ledgers_q = $this->db->get();
			$counter = 0;
			foreach ($cur_ledgers_q->result() as $row)
			{
				$data['ledger_dc'][$counter] = $row->dc;
				$data['ledger_id'][$counter] = $row->ledger_id;
				$data['rate_item'][$counter] = $row->inventory_rate;
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

			/* inventory items */
			$this->db->from('inventory_entry_items')->where('entry_id', $entry_id);
			$cur_inventory_item_q = $this->db->get();
			$counter = 0;
			foreach ($cur_inventory_item_q->result() as $row)
			{
				$data['inventory_item_id'][$counter] = $row->inventory_item_id;
				$data['inventory_item_quantity'][$counter] = $row->quantity;
				$data['inventory_item_rate_per_unit'][$counter] = $row->rate_per_unit;
				$data['inventory_item_discount'][$counter] = $row->discount;
				$data['inventory_item_amount'][$counter] = $row->total;
				$counter++;
			}
			/* one extra rows */
			$data['inventory_item_id'][$counter] = '0';
			$data['inventory_item_quantity'][$counter] = "";
			$data['inventory_item_rate_per_unit'][$counter] = "";
			$data['inventory_item_discount'][$counter] = "";
			$data['inventory_item_amount'][$counter] = "";
			$counter++;
		}

		/* Form validations */
		if ($current_entry_type['numbering'] == '3')
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|is_natural_no_zero|uniquevouchernowithid[' . $entry_type_id . '.' . $entry_id . ']');
		else
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|required|is_natural_no_zero|uniquevouchernowithid[' . $entry_type_id . '.' . $entry_id . ']');
		$this->form_validation->set_rules('entry_date', 'Entry Date', 'trim|required|is_date|is_date_within_range');
		if ($current_entry_type['inventory_entry_type'] == '1')
		{
			$this->form_validation->set_rules('main_account', 'Purchase Ledger', 'trim|required');
			$this->form_validation->set_rules('main_entity', 'Creditors (Supplier)', 'trim|required');
		} else {
			$this->form_validation->set_rules('main_account', 'Sale Ledger', 'trim|required');
			$this->form_validation->set_rules('main_entity', 'Debtor (Customer)', 'trim|required');
		}
		$this->form_validation->set_rules('entry_narration', 'trim');
		$this->form_validation->set_rules('entry_tag', 'Tag', 'trim|is_natural');

		/* Debit and Credit amount validation */
		if ($_POST)
		{
			foreach ($this->input->post('inventory_item_id', TRUE) as $id => $inventory_data)
			{
				$this->form_validation->set_rules('inventory_item_quantity[' . $id . ']', 'Inventory Item Quantity', 'trim|quantity');
				$this->form_validation->set_rules('inventory_item_rate_per_unit[' . $id . ']', 'Inventory Item Rate Per Unit', 'trim|currency');
				$this->form_validation->set_rules('inventory_item_discount[' . $id . ']', 'Inventory Item Discount', 'trim|discount');
				$this->form_validation->set_rules('inventory_item_amount[' . $id . ']', 'Inventory Item Amount', 'trim|currency');
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
			$data['entry_number']['value'] = $this->input->post('entry_number', TRUE);
			$data['entry_date']['value'] = $this->input->post('entry_date', TRUE);
			$data['entry_narration']['value'] = $this->input->post('entry_narration', TRUE);
			$data['entry_tag'] = $this->input->post('entry_tag', TRUE);
			$data['has_reconciliation'] = $this->input->post('has_reconciliation', TRUE);

			$data['main_account_active'] = $this->input->post('main_account', TRUE);
			$data['main_entity_active'] = $this->input->post('main_entity', TRUE);

			$data['inventory_item_id'] = $this->input->post('inventory_item_id', TRUE);
			$data['inventory_item_quantity'] = $this->input->post('inventory_item_quantity', TRUE);
			$data['inventory_item_rate_per_unit'] = $this->input->post('inventory_item_rate_per_unit', TRUE);
			$data['inventory_item_discount'] = $this->input->post('inventory_item_discount', TRUE);
			$data['inventory_item_amount'] = $this->input->post('inventory_item_amount', TRUE);

			$data['ledger_dc'] = $this->input->post('ledger_dc', TRUE);
			$data['ledger_id'] = $this->input->post('ledger_id', TRUE);
			$data['rate_item'] = $this->input->post('rate_item', TRUE);
			$data['amount_item'] = $this->input->post('amount_item', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/entry/edit', $data);
		} else	{
			$data_main_account = $this->input->post('main_account', TRUE);
			$data_main_entity = $this->input->post('main_entity', TRUE);

			$data_all_inventory_item_id = $this->input->post('inventory_item_id', TRUE);
			$data_all_inventory_item_quantity = $this->input->post('inventory_item_quantity', TRUE);
			$data_all_inventory_item_rate_per_unit = $this->input->post('inventory_item_rate_per_unit', TRUE);
			$data_all_inventory_item_discount = $this->input->post('inventory_item_discount', TRUE);
			$data_all_inventory_item_amount = $this->input->post('inventory_item_amount', TRUE);

			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_rate_item = $this->input->post('rate_item', TRUE);
			$data_all_amount_item = $this->input->post('amount_item', TRUE);

			$data_total_amount = 0;

			/* Setting Inventory Item type */
			if ($current_entry_type['inventory_entry_type'] == '1')
				$data_inventory_item_type = 1;
			else
				$data_inventory_item_type = 2;

			/* Checking for Valid Inventory Ledger account - account */
			if ($current_entry_type['inventory_entry_type'] == '1')
				$this->db->from('ledgers')->where('id', $data_main_account)->where('type', 2);
			else
				$this->db->from('ledgers')->where('id', $data_main_account)->where('type', 3);
			$valid_main_account_q = $this->db->get();
			if ($valid_main_account_q->num_rows() < 1)
			{
				if ($current_entry_type['inventory_entry_type'] == '1')
					$this->messages->add('Invalid Purchase Ledger.', 'error');
				else
					$this->messages->add('Invalid Sale Ledger.', 'error');
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}
			/* Checking for Valid Inventory Ledger account - entity */
			if ($current_entry_type['inventory_entry_type'] == '1')
				$this->db->from('ledgers')->where('id', $data_main_entity)->where('type', 4)->or_where('type', 1);
			else
				$this->db->from('ledgers')->where('id', $data_main_entity)->where('type', 5)->or_where('type', 1);
			$valid_main_account_q = $this->db->get();
			if ($valid_main_account_q->num_rows() < 1)
			{
				if ($current_entry_type['inventory_entry_type'] == '1')
					$this->messages->add('Invalid Creditor (Supplier).', 'error');
				else
					$this->messages->add('Invalid Debtor (Customer).', 'error');
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			/* Checking for Valid Inventory Item */
			$inventory_item_present = FALSE;
			$data_total_inventory_amount = 0;
			foreach ($data_all_inventory_item_id as $id => $inventory_data)
			{
				if ($data_all_inventory_item_id[$id] < 1)
					continue;

				/* Check for valid inventory item id */
				$this->db->from('inventory_items')->where('id', $data_all_inventory_item_id[$id]);
				$valid_inventory_item_q = $this->db->get();
				if ($valid_inventory_item_q->num_rows() < 1)
				{
					$this->messages->add('Invalid Inventory Item.', 'error');
					$this->template->load('template', 'inventory/entry/edit', $data);
					return;
				}
				$inventory_item_present = TRUE;
				$data_total_inventory_amount += $data_all_inventory_item_amount[$id];
			}
			if ( ! $inventory_item_present)
			{
				$this->messages->add('No Inventory Item selected.', 'error');
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			/* Checking for Valid Ledgers */
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
					$this->messages->add('Invalid Ledger account.', 'error');
					$this->template->load('template', 'inventory/entry/edit', $data);
					return;
				}
				if ($data_all_ledger_dc[$id] == 'D')
					$data_total_ledger_amount += $data_all_amount_item[$id];
				else
					$data_total_ledger_amount -= $data_all_amount_item[$id];
			}

			/* Total amount calculations */
			if ($current_entry_type['inventory_entry_type'] == '1')
			{
				$data_main_account_total = $data_total_inventory_amount;
				$data_main_entity_total = $data_total_inventory_amount + $data_total_ledger_amount;
			} else {
				$data_main_account_total = $data_total_inventory_amount + $data_total_ledger_amount;
				$data_main_entity_total = $data_total_inventory_amount;
			}
			$data_total_amount = $data_total_inventory_amount + $data_total_ledger_amount;
			if ($data_total_amount < 0)
			{
				$this->messages->add($current_entry_type['name'] . ' Entry total cannot be negative.', 'error');
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			/* Updating main entry */
			if ($current_entry_type['numbering'] == '3') {
				$data_number = $this->input->post('entry_number', TRUE);
				if ( ! $data_number)
					$data_number = NULL;
			} else {
				$data_number = $this->input->post('entry_number', TRUE);
			}

			$data_date = $this->input->post('entry_date', TRUE);
			$data_narration = $this->input->post('entry_narration', TRUE);
			$data_tag = $this->input->post('entry_tag', TRUE);
			if ($data_tag < 1)
				$data_tag = NULL;
			$data_type = $entry_type_id;
			$data_date = date_php_to_mysql($data_date); // Converting date to MySQL
			$data_has_reconciliation = $this->input->post('has_reconciliation', TRUE);

			$this->db->trans_start();
			$update_data = array(
				'number' => $data_number,
				'date' => $data_date,
				'narration' => $data_narration,
				'tag_id' => $data_tag,
			);
			if ( ! $this->db->where('id', $entry_id)->update('vouchers', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Entry.', 'error');
				$this->logger->write_message("error", "Error updating entry details for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			/* TODO : Deleting all old ledger data, Bad solution */
			if ( ! $this->db->delete('inventory_entry_items', array('entry_id' => $entry_id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error deleting previous inventory items from Entry.', 'error');
				$this->logger->write_message("error", "Error deleting previous inventory items from " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			$this->db->where('inventory_type', 3);
			if ( ! $this->db->delete('entry_items', array('entry_id' => $entry_id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error deleting previous Ledger accounts from Entry.', 'error');
				$this->logger->write_message("error", "Error deleting previous entry items for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			/* Updating main - account */
			$update_data = array(
				'ledger_id' => $data_main_account,
				'amount' => $data_main_account_total,
				'dc' => '',
				'reconciliation_date' => NULL,
				'inventory_type' => 1,
				'inventory_rate' => '',
			);
			if ($current_entry_type['inventory_entry_type'] == '1')
				$update_data['dc'] = 'D';
			else
				$update_data['dc'] = 'C';
			if ( ! $this->db->where('entry_id', $entry_id)->where('inventory_type', 1)->update('entry_items', $update_data))
			{
				$this->db->trans_rollback();
				if ($current_entry_type['inventory_entry_type'] == '1')
				{
					$this->messages->add('Error updating Purchase Ledger account of Entry.', 'error');
					$this->logger->write_message("error", "Error updating " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed updating purchase ledger " . "[id:" . $data_main_account . "]");
				} else {
					$this->messages->add('Error updating Sale Ledger account of Entry.', 'error');
					$this->logger->write_message("error", "Error updating " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed updating sale ledger " . "[id:" . $data_main_account . "]");
				}
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			/* Updating main - entity */
			$update_data = array(
				'ledger_id' => $data_main_entity,
				'amount' => $data_main_entity_total,
				'dc' => '',
				'reconciliation_date' => NULL,
				'inventory_type' => 2,
				'inventory_rate' => '',
			);
			if ($current_entry_type['inventory_entry_type'] == '1')
				$insert_data['dc'] = 'C';
			else
				$insert_data['dc'] = 'D';
			if ( ! $this->db->where('entry_id', $entry_id)->where('inventory_type', 2)->update('entry_items', $update_data))
			{
				$this->db->trans_rollback();
				if ($current_entry_type['inventory_entry_type'] == '1')
				{
					$this->messages->add('Error updating Creditor (Supplier) of Entry.', 'error');
					$this->logger->write_message("error", "Error updating " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed updating creditor ledger " . "[id:" . $data_main_entity . "]");
				} else {
					$this->messages->add('Error updating Debtor (Customer) of Entry.', 'error');
					$this->logger->write_message("error", "Error updating " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed updating debtor ledger " . "[id:" . $data_main_entity . "]");
				}
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			/* Adding inventory items */
			$data_all_inventory_item_id = $this->input->post('inventory_item_id', TRUE);
			$data_all_inventory_item_quantity = $this->input->post('inventory_item_quantity', TRUE);
			$data_all_inventory_item_rate_per_unit = $this->input->post('inventory_item_rate_per_unit', TRUE);
			$data_all_inventory_item_discount = $this->input->post('inventory_item_discount', TRUE);
			$data_all_inventory_item_amount = $this->input->post('inventory_item_amount', TRUE);

			foreach ($data_all_inventory_item_id as $id => $inventory_data)
			{
				$data_inventory_item_id = $data_all_inventory_item_id[$id];

				if ($data_inventory_item_id < 1)
					continue;

				$data_inventory_item_quantity = $data_all_inventory_item_quantity[$id];
				$data_inventory_item_rate_per_unit = $data_all_inventory_item_rate_per_unit[$id];
				$data_inventory_item_discount = $data_all_inventory_item_discount[$id];
				$data_inventory_item_amount = $data_all_inventory_item_amount[$id];

				$insert_inventory_data = array(
					'entry_id' => $entry_id,
					'inventory_item_id' => $data_inventory_item_id,
					'quantity' => $data_inventory_item_quantity,
					'rate_per_unit' => $data_inventory_item_rate_per_unit,
					'discount' => $data_inventory_item_discount,
					'total' => $data_inventory_item_amount,
					'type' => $data_inventory_item_type,
				);
				if ( ! $this->db->insert('inventory_entry_items', $insert_inventory_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Inventory Item - ' . $data_inventory_item_id . ' to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting inventory item " . "[id:" . $data_inventory_item_id . "]");
					$this->template->load('template', 'inventory/entry/edit', $data);
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
					'entry_id' => $entry_id,
					'ledger_id' => $data_ledger_id,
					'amount' => $data_amount,
					'dc' => $data_ledger_dc,
					'reconciliation_date' => NULL,
					'inventory_type' => 3,
					'inventory_rate' => $data_rate,
				);
				if ( ! $this->db->insert('entry_items', $insert_ledger_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Ledger account - ' . $data_ledger_id . ' to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting entry ledger item " . "[id:" . $data_ledger_id . "]");
					$this->template->load('template', 'inventory/entry/edit', $data);
					return;
				}
			}

			/* Updating Debit and Credit Total - entries */
			$update_data = array(
				'dr_total' => $data_total_amount,
				'cr_total' => $data_total_amount,
			);

			if ( ! $this->db->where('id', $entry_id)->update('vouchers', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Entry total.', 'error');
				$this->logger->write_message("error", "Error updating " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed updating debit and credit total");
				$this->template->load('template', 'inventory/entry/edit', $data);
				return;
			}

			/* Success */
			$this->db->trans_complete();

			$this->session->set_userdata('entry_updated_show_action', TRUE);
			$this->session->set_userdata('entry_updated_id', $entry_id);
			$this->session->set_userdata('entry_updated_type_id', $entry_type_id);
			$this->session->set_userdata('entry_updated_type_label', $current_entry_type['label']);
			$this->session->set_userdata('entry_updated_type_name', $current_entry_type['name']);
			$this->session->set_userdata('entry_updated_number', $data_number);
			if ($data_has_reconciliation)
				$this->session->set_userdata('entry_updated_has_reconciliation', TRUE);
			else
				$this->session->set_userdata('entry_updated_has_reconciliation', FALSE);

			/* Showing success message in show() method since message is too long for storing it in session */
			$this->logger->write_message("success", "Updated " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");

			redirect('entry/show/' . $current_entry_type['label']);
			return;
		}
		return;
	}

	function delete($entry_type, $entry_id = 0)
	{
		/* Check access */
		if ( ! check_access('delete inventory entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/entry/show/' . $entry_type);
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('entry/show/' . $entry_type);
			return;
		}

		/* Entry Type */
		$entry_type_id = entry_type_name_to_id($entry_type);
		if ( ! $entry_type_id)
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		} else {
			$current_entry_type = entry_type_info($entry_type_id);
		}

		/* Load current entry details */
		if ( ! $cur_entry = $this->Entry_model->get_entry($entry_id, $entry_type_id))
		{
			$this->messages->add('Invalid Entry.', 'error');
			redirect('entry/show/' . $current_entry_type['label']);
			return;
		}

		$this->db->trans_start();
		if ( ! $this->db->delete('inventory_entry_items', array('entry_id' => $entry_id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Inventory Items.', 'error');
			$this->logger->write_message("error", "Error deleting inventory item entries for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $cur_entry->number) . " [id:" . $entry_id . "]");
			redirect('entry/view/' . $current_entry_type['label'] . '/' . $entry_id);
			return;
		}
		if ( ! $this->db->delete('entry_items', array('entry_id' => $entry_id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Entry - Ledger accounts.', 'error');
			$this->logger->write_message("error", "Error deleting ledger entries for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $cur_entry->number) . " [id:" . $entry_id . "]");
			redirect('entry/view/' . $current_entry_type['label'] . '/' . $entry_id);
			return;
		}
		if ( ! $this->db->delete('vouchers', array('id' => $entry_id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Entry.', 'error');
			$this->logger->write_message("error", "Error deleting Entry for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $cur_entry->number) . " [id:" . $entry_id . "]");
			redirect('entry/view/' . $current_entry_type['label'] . '/' . $entry_id);
			return;
		}
		$this->db->trans_complete();
		$this->messages->add('Deleted ' . $current_entry_type['name'] . ' Entry.', 'success');
		$this->logger->write_message("success", "Deleted " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $cur_entry->number) . " [id:" . $entry_id . "]");
		redirect('entry/show/' . $current_entry_type['label']);
		return;
	}

	function download($entry_type, $entry_id = 0)
	{
		$this->load->helper('download');
		$this->load->model('Setting_model');

		/* Check access */
		if ( ! check_access('download inventory entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/entry/show/' . $entry_type);
			return;
		}

		/* Entry Type */
		$entry_type_id = entry_type_name_to_id($entry_type);
		if ( ! $entry_type_id)
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		} else {
			$current_entry_type = entry_type_info($entry_type_id);
		}

		if ($current_entry_type['base_type'] == '1')
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		}

		/* Load current entry details */
		if ( ! $cur_entry = $this->Entry_model->get_entry($entry_id, $entry_type_id))
		{
			$this->messages->add('Invalid Entry.', 'error');
			redirect('entry/show/' . $current_entry_type['label']);
			return;
		}

		/* Load current entry details - account, entity, ledgers */
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 1)->order_by('id', 'asc');
		$cur_entry_main_account = $this->db->get();
		if ($cur_entry_main_account->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Purchase or Sale Ledger.', 'error');
		}
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 2)->order_by('id', 'asc');
		$cur_entry_main_entity = $this->db->get();
		if ($cur_entry_main_entity->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Debtor or Creditor.', 'error');
		}
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 3)->order_by('id', 'asc');
		$cur_entry_ledgers = $this->db->get();

		/* Load current inventory items details */
		$this->db->from('inventory_entry_items')->where('entry_id', $entry_id)->order_by('id', 'asc');
		$cur_entry_inventory_items = $this->db->get();
		if ($cur_entry_inventory_items->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated inventory items.', 'error');
		}

		$data['cur_entry'] = $cur_entry;
		$data['cur_entry_main_account'] = $cur_entry_main_account;
		$data['cur_entry_main_entity'] = $cur_entry_main_entity;
		$data['cur_entry_ledgers'] = $cur_entry_ledgers;
		$data['cur_entry_inventory_items'] = $cur_entry_inventory_items;
		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;

		/* Download Entry */
		$file_name = $current_entry_type['name'] . '_entry_' . $cur_entry->number . ".html";
		$download_data = $this->load->view('inventory/entry/downloadpreview', $data, TRUE);
		force_download($file_name, $download_data);
		return;
	}

	function printpreview($entry_type, $entry_id = 0)
	{
		$this->load->model('Setting_model');

		/* Check access */
		if ( ! check_access('print inventory entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/entry/show/' . $entry_type);
			return;
		}

		/* Entry Type */
		$entry_type_id = entry_type_name_to_id($entry_type);
		if ( ! $entry_type_id)
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		} else {
			$current_entry_type = entry_type_info($entry_type_id);
		}

		if ($current_entry_type['base_type'] == '1')
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		}

		/* Load current entry details */
		if ( ! $cur_entry = $this->Entry_model->get_entry($entry_id, $entry_type_id))
		{
			$this->messages->add('Invalid Entry.', 'error');
			redirect('entry/show/' . $current_entry_type['label']);
			return;
		}

		/* Load current entry details - account, entity, ledgers */
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 1)->order_by('id', 'asc');
		$cur_entry_main_account = $this->db->get();
		if ($cur_entry_main_account->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Purchase or Sale Ledger.', 'error');
		}
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 2)->order_by('id', 'asc');
		$cur_entry_main_entity = $this->db->get();
		if ($cur_entry_main_entity->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Debtor or Creditor.', 'error');
		}
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 3)->order_by('id', 'asc');
		$cur_entry_ledgers = $this->db->get();

		/* Load current inventory items details */
		$this->db->from('inventory_entry_items')->where('entry_id', $entry_id)->order_by('id', 'asc');
		$cur_entry_inventory_items = $this->db->get();
		if ($cur_entry_inventory_items->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated inventory items.', 'error');
		}

		$data['cur_entry'] = $cur_entry;
		$data['cur_entry_main_account'] = $cur_entry_main_account;
		$data['cur_entry_main_entity'] = $cur_entry_main_entity;
		$data['cur_entry_ledgers'] = $cur_entry_ledgers;
		$data['cur_entry_inventory_items'] = $cur_entry_inventory_items;
		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;

		$this->load->view('inventory/entry/printpreview', $data);
		return;
	}

	function email($entry_type, $entry_id = 0)
	{
		$this->load->model('Setting_model');
		$this->load->library('email');

		/* Check access */
		if ( ! check_access('email inventory entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/entry/show/' . $entry_type);
			return;
		}

		/* Entry Type */
		$entry_type_id = entry_type_name_to_id($entry_type);
		if ( ! $entry_type_id)
		{
			$this->messages->add('Invalid Entry type.', 'error');
			redirect('entry/show/all');
			return;
		} else {
			$current_entry_type = entry_type_info($entry_type_id);
		}

		$account_data = $this->Setting_model->get_current();

		/* Load current entry details */
		if ( ! $cur_entry = $this->Entry_model->get_entry($entry_id, $entry_type_id))
		{
			$this->messages->add('Invalid Entry.', 'error');
			redirect('entry/show/' . $current_entry_type['label']);
			return;
		}

		/* Load current entry details - account, entity, ledgers */
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 1)->order_by('id', 'asc');
		$cur_entry_main_account = $this->db->get();
		if ($cur_entry_main_account->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Purchase or Sale Ledger.', 'error');
		}
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 2)->order_by('id', 'asc');
		$cur_entry_main_entity = $this->db->get();
		if ($cur_entry_main_entity->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Debtor or Creditor Ledger.', 'error');
		}
		$this->db->from('entry_items')->where('entry_id', $entry_id)->where('inventory_type', 3)->order_by('id', 'asc');
		$cur_entry_ledgers = $this->db->get();

		/* Load current inventory items details */
		$this->db->from('inventory_entry_items')->where('entry_id', $entry_id)->order_by('id', 'asc');
		$cur_entry_inventory_items = $this->db->get();
		if ($cur_entry_inventory_items->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated inventory items.', 'error');
		}

		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$data['entry_id'] = $entry_id;
		$data['entry_number'] = $cur_entry->number;
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
			$this->load->view('inventory/entry/email', $data);
			return;
		}
		else
		{
			$data['cur_entry'] = $cur_entry;
			$data['cur_entry_main_account'] = $cur_entry_main_account;
			$data['cur_entry_main_entity'] = $cur_entry_main_entity;
			$data['cur_entry_ledgers'] = $cur_entry_ledgers;
			$data['cur_entry_inventory_items'] = $cur_entry_inventory_items;

			/* Preparing message */
			$message = $this->load->view('inventory/entry/emailpreview', $data, TRUE);

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
			$this->email->subject($current_entry_type['name'] . ' Entry No. ' . full_entry_number($entry_type_id, $cur_entry->number));
			$this->email->message($message);
			if ($this->email->send())
			{
				$data['message'] = "Email sent.";
				$this->logger->write_message("success", "Emailed " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $cur_entry->number) . " [id:" . $entry_id . "]");
			} else {
				$data['error'] = "Error sending email. Check you email settings.";
				$this->logger->write_message("error", "Error emailing " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $cur_entry->number) . " [id:" . $entry_id . "]");
			}
			$this->load->view('inventory/entry/email', $data);
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
			'class' => 'rate-item',
		);
		$amount_item = array(
			'name' => 'amount_item[' . $i . ']',
			'id' => 'amount_item[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => isset($cr_amount[$i]) ? $cr_amount[$i] : "",
			'class' => 'amount-item',
		);
		echo '<tr class="new-row">';
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

	function addinventoryrow()
	{
		$i = time() + rand  (0, time()) + rand  (0, time()) + rand  (0, time());
		$inventory_item_quantity = array(
			'name' => 'inventory_item_quantity[' . $i . ']',
			'id' => 'inventory_item_quantity[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => '',
			'class' => 'quantity-inventory-item',
		);
		$inventory_item_rate_per_unit = array(
			'name' => 'inventory_item_rate_per_unit[' . $i . ']',
			'id' => 'inventory_item_rate_per_unit[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => '',
			'class' => 'rate-inventory-item',
		);
		$inventory_item_discount = array(
			'name' => 'inventory_item_discount[' . $i . ']',
			'id' => 'inventory_item_discount[' . $i . ']',
			'maxlength' => '15',
			'size' => '9',
			'value' => '',
			'class' => 'discount-inventory-item',
		);
		$inventory_item_amount = array(
			'name' => 'inventory_item_amount[' . $i . ']',
			'id' => 'inventory_item_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
			'class' => 'amount-inventory-item',
		);

		echo '<tr class="new-row">';
		echo "<td>" . form_input_inventory_item('inventory_item_id[' . $i . ']', 0) . "</td>";
		echo "<td>" . form_input($inventory_item_quantity) . "</td>";
		echo "<td>" . form_input($inventory_item_rate_per_unit) . "</td>";
		echo "<td>" . form_input($inventory_item_discount) . "</td>";
		echo "<td>" . form_input($inventory_item_amount) . "</td>";
		echo '<td>';
		echo img(array('src' => asset_url() . "images/icons/add.png", 'border' => '0', 'alt' => 'Add Inventory Item', 'class' => 'addinventoryrow'));
		echo '</td>';
		echo '<td>';
		echo img(array('src' => asset_url() . "images/icons/delete.png", 'border' => '0', 'alt' => 'Remove Inventory Item', 'class' => 'deleteinventoryrow'));
		echo '</td>';
		echo '<td class="inventory-item-balance"><div></div>';
		echo '</td>';
		echo '</tr>';
		return;
	}
}

/* End of file entry.php */
/* Location: ./system/application/controllers/inventory/entry.php */
