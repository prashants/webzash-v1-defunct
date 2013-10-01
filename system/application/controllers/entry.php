<?php

class Entry extends Controller {

	function Entry()
	{
		parent::Controller();
		$this->load->model('Entry_model');
		$this->load->model('Ledger_model');
		$this->load->model('Tag_model');
		return;
	}

	function index()
	{
		redirect('entry/show/all');
		return;
	}

	function show($entry_type)
	{
		$this->load->model('Tag_model');

		$data['tag_id'] = 0;
		$entry_type_id = 0;

		if ($entry_type == 'tag')
		{
			$tag_id = (int)$this->uri->segment(4);
			if ($tag_id < 1)
			{
				$this->messages->add('Invalid Tag.', 'error');
				redirect('entry/show/all');
				return;
			}
			$data['tag_id'] = $tag_id;
			$tag_name = $this->Tag_model->tag_name($tag_id);
			$this->template->set('page_title', 'Entries Tagged "' . $tag_name . '"');
		} else if ($entry_type == 'all') {
			$entry_type_id = 0;
			$this->template->set('page_title', 'All Entries');
		} else {
			$entry_type_id = entry_type_name_to_id($entry_type);
			if ( ! $entry_type_id)
			{
				$this->messages->add('Invalid Entry type specified. Showing all Entries.', 'error');
				redirect('entry/show/all');
				return;
			} else {
				$current_entry_type = entry_type_info($entry_type_id);
				$this->template->set('page_title', $current_entry_type['name'] . ' Entries');
				$this->template->set('nav_links', array('entry/add/' . $current_entry_type['label'] => 'New ' . $current_entry_type['name'] . ' Entry'));
			}
		}

		$entry_q = NULL;

		/* Pagination setup */
		$this->load->library('pagination');

		if ($entry_type == "tag")
			$page_count = (int)$this->uri->segment(5);
		else
			$page_count = (int)$this->uri->segment(4);

		$page_count = $this->input->xss_clean($page_count);
		if ( ! $page_count)
			$page_count = "0";

		/* Pagination configuration */
		if ($entry_type == 'tag')
		{
			$config['base_url'] = site_url('entry/show/tag' . $tag_id);
			$config['uri_segment'] = 5;
		} else if ($entry_type == 'all') {
			$config['base_url'] = site_url('entry/show/all');
			$config['uri_segment'] = 4;
		} else {
			$config['base_url'] = site_url('entry/show/' . $current_entry_type['label']);
			$config['uri_segment'] = 4;
		}
		$pagination_counter = $this->config->item('row_count');
		$config['num_links'] = 10;
		$config['per_page'] = $pagination_counter;
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

		if ($entry_type == "tag") {
			$this->db->from('entries')->where('tag_id', $tag_id)->order_by('date', 'desc')->order_by('number', 'desc')->limit($pagination_counter, $page_count);
			$entry_q = $this->db->get();
			$config['total_rows'] = $this->db->from('entries')->where('tag_id', $tag_id)->get()->num_rows();
		} else if ($entry_type_id > 0) {
			$this->db->from('entries')->where('entry_type', $entry_type_id)->order_by('date', 'desc')->order_by('number', 'desc')->limit($pagination_counter, $page_count);
			$entry_q = $this->db->get();
			$config['total_rows'] = $this->db->from('entries')->where('entry_type', $entry_type_id)->get()->num_rows();
		} else {
			$this->db->from('entries')->order_by('date', 'desc')->order_by('number', 'desc')->limit($pagination_counter, $page_count);
			$entry_q = $this->db->get();
			$config['total_rows'] = $this->db->count_all('entries');
		}

		/* Pagination initializing */
		$this->pagination->initialize($config);

		/* Show entry add actions */
		if ($this->session->userdata('entry_added_show_action'))
		{
			$entry_added_id_temp = $this->session->userdata('entry_added_id');
			$entry_added_type_id_temp = $this->session->userdata('entry_added_type_id');
			$entry_added_type_label_temp = $this->session->userdata('entry_added_type_label');
			$entry_added_type_name_temp = $this->session->userdata('entry_added_type_name');
			$entry_added_number_temp = $this->session->userdata('entry_added_number');
			$entry_added_message = 'Added ' . $entry_added_type_name_temp . ' Entry number ' . full_entry_number($entry_added_type_id_temp, $entry_added_number_temp) . ".";
			$entry_added_message .= " You can [ ";
			$entry_added_message .= anchor('entry/view/' . $entry_added_type_label_temp . "/" . $entry_added_id_temp, 'View', array('class' => 'anchor-link-a')) . " | ";
			$entry_added_message .= anchor('entry/edit/' . $entry_added_type_label_temp . "/" . $entry_added_id_temp, 'Edit', array('class' => 'anchor-link-a')) . " | ";
			$entry_added_message .= anchor_popup('entry/printpreview/' . $entry_added_type_label_temp . "/" . $entry_added_id_temp , 'Print', array('class' => 'anchor-link-a', 'width' => '600', 'height' => '600')) . " | ";
			$entry_added_message .= anchor_popup('entry/email/' . $entry_added_type_label_temp . "/" . $entry_added_id_temp, 'Email', array('class' => 'anchor-link-a', 'width' => '500', 'height' => '300')) . " | ";
			$entry_added_message .= anchor('entry/download/' . $entry_added_type_label_temp . "/" . $entry_added_id_temp, 'Download', array('class' => 'anchor-link-a'));
			$entry_added_message .= " ] it.";
			$this->messages->add($entry_added_message, 'success');
			$this->session->unset_userdata('entry_added_show_action');
			$this->session->unset_userdata('entry_added_id');
			$this->session->unset_userdata('entry_added_type_id');
			$this->session->unset_userdata('entry_added_type_label');
			$this->session->unset_userdata('entry_added_type_name');
			$this->session->unset_userdata('entry_added_number');
		}

		/* Show entry edit actions */
		if ($this->session->userdata('entry_updated_show_action'))
		{
			$entry_updated_id_temp = $this->session->userdata('entry_updated_id');
			$entry_updated_type_id_temp = $this->session->userdata('entry_updated_type_id');
			$entry_updated_type_label_temp = $this->session->userdata('entry_updated_type_label');
			$entry_updated_type_name_temp = $this->session->userdata('entry_updated_type_name');
			$entry_updated_number_temp = $this->session->userdata('entry_updated_number');
			$entry_updated_message = 'Updated ' . $entry_updated_type_name_temp . ' Entry number ' . full_entry_number($entry_updated_type_id_temp, $entry_updated_number_temp) . ".";
			$entry_updated_message .= " You can [ ";
			$entry_updated_message .= anchor('entry/view/' . $entry_updated_type_label_temp . "/" . $entry_updated_id_temp, 'View', array('class' => 'anchor-link-a')) . " | ";
			$entry_updated_message .= anchor('entry/edit/' . $entry_updated_type_label_temp . "/" . $entry_updated_id_temp, 'Edit', array('class' => 'anchor-link-a')) . " | ";
			$entry_updated_message .= anchor_popup('entry/printpreview/' . $entry_updated_type_label_temp . "/" . $entry_updated_id_temp , 'Print', array('class' => 'anchor-link-a', 'width' => '600', 'height' => '600')) . " | ";
			$entry_updated_message .= anchor_popup('entry/email/' . $entry_updated_type_label_temp . "/" . $entry_updated_id_temp, 'Email', array('class' => 'anchor-link-a', 'width' => '500', 'height' => '300')) . " | ";
			$entry_updated_message .= anchor('entry/download/' . $entry_updated_type_label_temp . "/" . $entry_updated_id_temp, 'Download', array('class' => 'anchor-link-a'));
			$entry_updated_message .= " ] it.";
			$this->messages->add($entry_updated_message, 'success');

			if ($this->session->userdata('entry_updated_has_reconciliation'))
				$this->messages->add('Previous reconciliations for this entry are no longer valid. You need to redo the reconciliations for this entry.', 'success');

			$this->session->unset_userdata('entry_updated_show_action');
			$this->session->unset_userdata('entry_updated_id');
			$this->session->unset_userdata('entry_updated_type_id');
			$this->session->unset_userdata('entry_updated_type_label');
			$this->session->unset_userdata('entry_updated_type_name');
			$this->session->unset_userdata('entry_updated_number');
			$this->session->unset_userdata('entry_updated_has_reconciliation');
		}

		$data['entry_data'] = $entry_q;

		$this->template->load('template', 'entry/index', $data);
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

		$this->template->set('page_title', 'View ' . $current_entry_type['name'] . ' Entry');

		/* Load current entry details */
		if ( ! $cur_entry = $this->Entry_model->get_entry($entry_id, $entry_type_id))
		{
			$this->messages->add('Invalid Entry.', 'error');
			redirect('entry/show/' . $current_entry_type['label']);
			return;
		}
		/* Load current entry details */
		$this->db->from('entry_items')->where('entry_id', $entry_id)->order_by('id', 'asc');
		$cur_entry_ledgers = $this->db->get();
		if ($cur_entry_ledgers->num_rows() < 1)
		{
			$this->messages->add('Entry has no associated Ledger accounts.', 'error');
		}
		$data['cur_entry'] = $cur_entry;
		$data['cur_entry_ledgers'] = $cur_entry_ledgers;
		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$this->template->load('template', 'entry/view', $data);
		return;
	}

	function add($entry_type)
	{
		/* Check access */
		if ( ! check_access('create entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('entry/show/' . $entry_type);
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

		$this->template->set('page_title', 'New ' . $current_entry_type['name'] . ' Entry');

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
		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$data['entry_tags'] = $this->Tag_model->get_all_tags();
		$data['entry_tag'] = 0;

		/* Form validations */
		if ($current_entry_type['numbering'] == '2')
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|required|is_natural_no_zero|uniqueentryno[' . $entry_type_id . ']');
		else if ($current_entry_type['numbering'] == '3')
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|is_natural_no_zero|uniqueentryno[' . $entry_type_id . ']');
		else
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|is_natural_no_zero|uniqueentryno[' . $entry_type_id . ']');
		$this->form_validation->set_rules('entry_date', 'Entry Date', 'trim|required|is_date|is_date_within_range');
		$this->form_validation->set_rules('entry_narration', 'trim');
		$this->form_validation->set_rules('entry_tag', 'Tag', 'trim|is_natural');

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
			$data['entry_number']['value'] = $this->input->post('entry_number', TRUE);
			$data['entry_date']['value'] = $this->input->post('entry_date', TRUE);
			$data['entry_narration']['value'] = $this->input->post('entry_narration', TRUE);
			$data['entry_tag'] = $this->input->post('entry_tag', TRUE);

			$data['ledger_dc'] = $this->input->post('ledger_dc', TRUE);
			$data['ledger_id'] = $this->input->post('ledger_id', TRUE);
			$data['dr_amount'] = $this->input->post('dr_amount', TRUE);
			$data['cr_amount'] = $this->input->post('cr_amount', TRUE);
		} else {
			for ($count = 0; $count <= 3; $count++)
			{
				if ($count == 0 && $entry_type == "payment")
					$data['ledger_dc'][$count] = "C";
				else if ($count == 1 && $entry_type != "payment")
					$data['ledger_dc'][$count] = "C";
				else
					$data['ledger_dc'][$count] = "D";
				$data['ledger_id'][$count] = 0;
				$data['dr_amount'][$count] = "";
				$data['cr_amount'][$count] = "";
			}
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'entry/add', $data);
			return;
		}
		else
		{
			/* Checking for Valid Ledgers account and Debit and Credit Total */
			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_dr_amount = $this->input->post('dr_amount', TRUE);
			$data_all_cr_amount = $this->input->post('cr_amount', TRUE);
			$dr_total = 0;
			$cr_total = 0;
			$bank_cash_present = FALSE; /* Whether atleast one Ledger account is Bank or Cash account */
			$non_bank_cash_present = FALSE;  /* Whether atleast one Ledger account is NOT a Bank or Cash account */
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
					$this->template->load('template', 'entry/add', $data);
					return;
				} else {
					/* Check for valid ledger type */
					$valid_ledger = $valid_ledger_q->row();
					if ($current_entry_type['bank_cash_ledger_restriction'] == '2')
					{
						if ($data_all_ledger_dc[$id] == 'D' && $valid_ledger->type == 1)
						{
							$bank_cash_present = TRUE;
						}
						if ($valid_ledger->type != 1)
							$non_bank_cash_present = TRUE;
					} else if ($current_entry_type['bank_cash_ledger_restriction'] == '3')
					{
						if ($data_all_ledger_dc[$id] == 'C' && $valid_ledger->type == 1)
						{
							$bank_cash_present = TRUE;
						}
						if ($valid_ledger->type != 1)
							$non_bank_cash_present = TRUE;
					} else if ($current_entry_type['bank_cash_ledger_restriction'] == '4')
					{
						if ($valid_ledger->type != 1)
						{
							$this->messages->add('Invalid Ledger account. ' . $current_entry_type['name'] . ' Entries can have only Bank and Cash Ledgers accounts.', 'error');
							$this->template->load('template', 'entry/add', $data);
							return;
						}
					} else if ($current_entry_type['bank_cash_ledger_restriction'] == '5')
					{
						if ($valid_ledger->type == 1)
						{
							$this->messages->add('Invalid Ledger account. ' . $current_entry_type['name'] . ' Entries cannot have Bank and Cash Ledgers accounts.', 'error');
							$this->template->load('template', 'entry/add', $data);
							return;
						}
					}
				}

				if ($data_all_ledger_dc[$id] == "D")
				{
					$dr_total = float_ops($data_all_dr_amount[$id], $dr_total, '+');
				} else {
					$cr_total = float_ops($data_all_cr_amount[$id], $cr_total, '+');
				}
			}
			if (float_ops($dr_total, $cr_total, '!='))
			{
				$this->messages->add('Debit and Credit Total does not match!', 'error');
				$this->template->load('template', 'entry/add', $data);
				return;
			} else if (float_ops($dr_total, 0, '==') && float_ops($cr_total, 0, '==')) {
				$this->messages->add('Cannot save empty Entry.', 'error');
				$this->template->load('template', 'entry/add', $data);
				return;
			}
			/* Check if atleast one Bank or Cash Ledger account is present */
			if ($current_entry_type['bank_cash_ledger_restriction'] == '2')
			{
				if ( ! $bank_cash_present)
				{
					$this->messages->add('Need to Debit atleast one Bank or Cash account.', 'error');
					$this->template->load('template', 'entry/add', $data);
					return;
				}
				if ( ! $non_bank_cash_present)
				{
					$this->messages->add('Need to Debit or Credit atleast one NON - Bank or Cash account.', 'error');
					$this->template->load('template', 'entry/add', $data);
					return;
				}
			} else if ($current_entry_type['bank_cash_ledger_restriction'] == '3')
			{
				if ( ! $bank_cash_present)
				{
					$this->messages->add('Need to Credit atleast one Bank or Cash account.', 'error');
					$this->template->load('template', 'entry/add', $data);
					return;
				}
				if ( ! $non_bank_cash_present)
				{
					$this->messages->add('Need to Debit or Credit atleast one NON - Bank or Cash account.', 'error');
					$this->template->load('template', 'entry/add', $data);
					return;
				}
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

			$this->db->trans_start();
			$insert_data = array(
				'number' => $data_number,
				'date' => $data_date,
				'narration' => $data_narration,
				'entry_type' => $data_type,
				'tag_id' => $data_tag,
			);
			if ( ! $this->db->insert('entries', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Entry.', 'error');
				$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting entry");
				$this->template->load('template', 'entry/add', $data);
				return;
			} else {
				$entry_id = $this->db->insert_id();
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
					$dr_total = float_ops($data_all_dr_amount[$id], $dr_total, '+');
				} else {
					$data_amount = $data_all_cr_amount[$id];
					$cr_total = float_ops($data_all_cr_amount[$id], $cr_total, '+');
				}
				$insert_ledger_data = array(
					'entry_id' => $entry_id,
					'ledger_id' => $data_ledger_id,
					'amount' => $data_amount,
					'dc' => $data_ledger_dc,
				);
				if ( ! $this->db->insert('entry_items', $insert_ledger_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Ledger account - ' . $data_ledger_id . ' to Entry.', 'error');
					$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed inserting entry ledger item " . "[id:" . $data_ledger_id . "]");
					$this->template->load('template', 'entry/add', $data);
					return;
				}
			}

			/* Updating Debit and Credit Total in entries table */
			$update_data = array(
				'dr_total' => $dr_total,
				'cr_total' => $cr_total,
			);
			if ( ! $this->db->where('id', $entry_id)->update('entries', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Entry total.', 'error');
				$this->logger->write_message("error", "Error adding " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " since failed updating debit and credit total");
				$this->template->load('template', 'entry/add', $data);
				return;
			}

			/* Success */
			$this->db->trans_complete();

			$this->session->set_userdata('entry_added_show_action', TRUE);
			$this->session->set_userdata('entry_added_id', $entry_id);
			$this->session->set_userdata('entry_added_type_id', $entry_type_id);
			$this->session->set_userdata('entry_added_type_label', $current_entry_type['label']);
			$this->session->set_userdata('entry_added_type_name', $current_entry_type['name']);
			$this->session->set_userdata('entry_added_number', $data_number);

			/* Showing success message in show() method since message is too long for storing it in session */
			$this->logger->write_message("success", "Added " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
			redirect('entry/show/' . $current_entry_type['label']);
			$this->template->load('template', 'entry/add', $data);
			return;
		}
		return;
	}

	function edit($entry_type, $entry_id = 0)
	{
		/* Check access */
		if ( ! check_access('edit entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('entry/show/' . $entry_type);
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
		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$data['entry_tag'] = $cur_entry->tag_id;
		$data['entry_tags'] = $this->Tag_model->get_all_tags();
		$data['has_reconciliation'] = FALSE;

		/* Load current ledger details if not $_POST */
		if ( ! $_POST)
		{
			$this->db->from('entry_items')->where('entry_id', $entry_id);
			$cur_ledgers_q = $this->db->get();
			if ($cur_ledgers_q->num_rows <= 0)
			{
				$this->messages->add('No Ledger accounts found!', 'error');
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
		if ($current_entry_type['numbering'] == '3')
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|is_natural_no_zero|uniqueentrynowithid[' . $entry_type_id . '.' . $entry_id . ']');
		else
			$this->form_validation->set_rules('entry_number', 'Entry Number', 'trim|required|is_natural_no_zero|uniqueentrynowithid[' . $entry_type_id . '.' . $entry_id . ']');
		$this->form_validation->set_rules('entry_date', 'Entry Date', 'trim|required|is_date|is_date_within_range');
		$this->form_validation->set_rules('entry_narration', 'trim');
		$this->form_validation->set_rules('entry_tag', 'Tag', 'trim|is_natural');

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
			$data['entry_number']['value'] = $this->input->post('entry_number', TRUE);
			$data['entry_date']['value'] = $this->input->post('entry_date', TRUE);
			$data['entry_narration']['value'] = $this->input->post('entry_narration', TRUE);
			$data['entry_tag'] = $this->input->post('entry_tag', TRUE);
			$data['has_reconciliation'] = $this->input->post('has_reconciliation', TRUE);

			$data['ledger_dc'] = $this->input->post('ledger_dc', TRUE);
			$data['ledger_id'] = $this->input->post('ledger_id', TRUE);
			$data['dr_amount'] = $this->input->post('dr_amount', TRUE);
			$data['cr_amount'] = $this->input->post('cr_amount', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'entry/edit', $data);
		} else	{
			/* Checking for Valid Ledgers account and Debit and Credit Total */
			$data_all_ledger_id = $this->input->post('ledger_id', TRUE);
			$data_all_ledger_dc = $this->input->post('ledger_dc', TRUE);
			$data_all_dr_amount = $this->input->post('dr_amount', TRUE);
			$data_all_cr_amount = $this->input->post('cr_amount', TRUE);
			$dr_total = 0;
			$cr_total = 0;
			$bank_cash_present = FALSE; /* Whether atleast one Ledger account is Bank or Cash account */
			$non_bank_cash_present = FALSE;  /* Whether atleast one Ledger account is NOT a Bank or Cash account */
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
					$this->template->load('template', 'entry/edit', $data);
					return;
				} else {
					/* Check for valid ledger type */
					$valid_ledger = $valid_ledger_q->row();
					if ($current_entry_type['bank_cash_ledger_restriction'] == '2')
					{
						if ($data_all_ledger_dc[$id] == 'D' && $valid_ledger->type == 1)
						{
							$bank_cash_present = TRUE;
						}
						if ($valid_ledger->type != 1)
							$non_bank_cash_present = TRUE;
					} else if ($current_entry_type['bank_cash_ledger_restriction'] == '3')
					{
						if ($data_all_ledger_dc[$id] == 'C' && $valid_ledger->type == 1)
						{
							$bank_cash_present = TRUE;
						}
						if ($valid_ledger->type != 1)
							$non_bank_cash_present = TRUE;
					} else if ($current_entry_type['bank_cash_ledger_restriction'] == '4')
					{
						if ($valid_ledger->type != 1)
						{
							$this->messages->add('Invalid Ledger account. ' . $current_entry_type['name'] . ' Entries can have only Bank and Cash Ledgers accounts.', 'error');
							$this->template->load('template', 'entry/edit', $data);
							return;
						}
					} else if ($current_entry_type['bank_cash_ledger_restriction'] == '5')
					{
						if ($valid_ledger->type == 1)
						{
							$this->messages->add('Invalid Ledger account. ' . $current_entry_type['name'] . ' Entries cannot have Bank and Cash Ledgers accounts.', 'error');
							$this->template->load('template', 'entry/edit', $data);
							return;
						}
					}
				}
				if ($data_all_ledger_dc[$id] == "D")
				{
					$dr_total = float_ops($data_all_dr_amount[$id], $dr_total, '+');
				} else {
					$cr_total = float_ops($data_all_cr_amount[$id], $cr_total, '+');
				}
			}
			if (float_ops($dr_total, $cr_total, '!='))
			{
				$this->messages->add('Debit and Credit Total does not match!', 'error');
				$this->template->load('template', 'entry/edit', $data);
				return;
			} else if (float_ops($dr_total, 0, '==') || float_ops($cr_total, 0, '==')) {
				$this->messages->add('Cannot save empty Entry.', 'error');
				$this->template->load('template', 'entry/edit', $data);
				return;
			}
			/* Check if atleast one Bank or Cash Ledger account is present */
			if ($current_entry_type['bank_cash_ledger_restriction'] == '2')
			{
				if ( ! $bank_cash_present)
				{
					$this->messages->add('Need to Debit atleast one Bank or Cash account.', 'error');
					$this->template->load('template', 'entry/edit', $data);
					return;
				}
				if ( ! $non_bank_cash_present)
				{
					$this->messages->add('Need to Debit or Credit atleast one NON - Bank or Cash account.', 'error');
					$this->template->load('template', 'entry/edit', $data);
					return;
				}
			} else if ($current_entry_type['bank_cash_ledger_restriction'] == '3')
			{
				if ( ! $bank_cash_present)
				{
					$this->messages->add('Need to Credit atleast one Bank or Cash account.', 'error');
					$this->template->load('template', 'entry/edit', $data);
					return;
				}
				if ( ! $non_bank_cash_present)
				{
					$this->messages->add('Need to Debit or Credit atleast one NON - Bank or Cash account.', 'error');
					$this->template->load('template', 'entry/edit', $data);
					return;
				}
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
			if ( ! $this->db->where('id', $entry_id)->update('entries', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Entry account.', 'error');
				$this->logger->write_message("error", "Error updating entry details for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
				$this->template->load('template', 'entry/edit', $data);
				return;
			}

			/* TODO : Deleting all old ledger data, Bad solution */
			if ( ! $this->db->delete('entry_items', array('entry_id' => $entry_id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error deleting previous Ledger accounts from Entry.', 'error');
				$this->logger->write_message("error", "Error deleting previous entry items for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
				$this->template->load('template', 'entry/edit', $data);
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
					$dr_total = float_ops($data_all_dr_amount[$id], $dr_total, '+');
				} else {
					$data_amount = $data_all_cr_amount[$id];
					$cr_total = float_ops($data_all_cr_amount[$id], $cr_total, '+');
				}

				$insert_ledger_data = array(
					'entry_id' => $entry_id,
					'ledger_id' => $data_ledger_id,
					'amount' => $data_amount,
					'dc' => $data_ledger_dc,
				);
				if ( ! $this->db->insert('entry_items', $insert_ledger_data))
				{
					$this->db->trans_rollback();
					$this->messages->add('Error adding Ledger account - ' . $data_ledger_id . ' to Entry.', 'error');
					$this->logger->write_message("error", "Error adding Ledger account item [id:" . $data_ledger_id . "] for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
					$this->template->load('template', 'entry/edit', $data);
					return;
				}
			}

			/* Updating Debit and Credit Total in entries table */
			$update_data = array(
				'dr_total' => $dr_total,
				'cr_total' => $cr_total,
			);
			if ( ! $this->db->where('id', $entry_id)->update('entries', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Entry total.', 'error');
				$this->logger->write_message("error", "Error updating entry total for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $data_number) . " [id:" . $entry_id . "]");
				$this->template->load('template', 'entry/edit', $data);
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
		if ( ! check_access('delete entry'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('entry/show/' . $entry_type);
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
		if ( ! $this->db->delete('entry_items', array('entry_id' => $entry_id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Entry - Ledger accounts.', 'error');
			$this->logger->write_message("error", "Error deleting ledger entries for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $cur_entry->number) . " [id:" . $entry_id . "]");
			redirect('entry/view/' . $current_entry_type['label'] . '/' . $entry_id);
			return;
		}
		if ( ! $this->db->delete('entries', array('id' => $entry_id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Entry entry.', 'error');
			$this->logger->write_message("error", "Error deleting Entry entry for " . $current_entry_type['name'] . " Entry number " . full_entry_number($entry_type_id, $cur_entry->number) . " [id:" . $entry_id . "]");
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
		$this->load->model('Ledger_model');

		/* Check access */
		if ( ! check_access('download entry'))
		{
			$this->messages->add('Permission denied.', 'error');
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

		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$data['entry_number'] =  $cur_entry->number;
		$data['entry_date'] = date_mysql_to_php_display($cur_entry->date);
		$data['entry_dr_total'] =  $cur_entry->dr_total;
		$data['entry_cr_total'] =  $cur_entry->cr_total;
		$data['entry_narration'] = $cur_entry->narration;

		/* Getting Ledger details */
		$this->db->from('entry_items')->where('entry_id', $entry_id)->order_by('dc', 'desc');
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

		/* Download Entry */
		$file_name = $current_entry_type['name'] . '_entry_' . $cur_entry->number . ".html";
		$download_data = $this->load->view('entry/downloadpreview', $data, TRUE);
		force_download($file_name, $download_data);
		return;
	}

	function printpreview($entry_type, $entry_id = 0)
	{
		$this->load->model('Setting_model');
		$this->load->model('Ledger_model');

		/* Check access */
		if ( ! check_access('print entry'))
		{
			$this->messages->add('Permission denied.', 'error');
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

		$data['entry_type_id'] = $entry_type_id;
		$data['current_entry_type'] = $current_entry_type;
		$data['entry_number'] =  $cur_entry->number;
		$data['entry_date'] = date_mysql_to_php_display($cur_entry->date);
		$data['entry_dr_total'] =  $cur_entry->dr_total;
		$data['entry_cr_total'] =  $cur_entry->cr_total;
		$data['entry_narration'] = $cur_entry->narration;

		/* Getting Ledger details */
		$this->db->from('entry_items')->where('entry_id', $entry_id)->order_by('dc', 'desc');
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

		$this->load->view('entry/printpreview', $data);
		return;
	}

	function email($entry_type, $entry_id = 0)
	{
		$this->load->model('Setting_model');
		$this->load->model('Ledger_model');
		$this->load->library('email');

		/* Check access */
		if ( ! check_access('email entry'))
		{
			$this->messages->add('Permission denied.', 'error');
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

		$account_data = $this->Setting_model->get_current();

		/* Load current entry details */
		if ( ! $cur_entry = $this->Entry_model->get_entry($entry_id, $entry_type_id))
		{
			$this->messages->add('Invalid Entry.', 'error');
			redirect('entry/show/' . $current_entry_type['label']);
			return;
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
			$this->load->view('entry/email', $data);
			return;
		}
		else
		{
			$entry_data['entry_type_id'] = $entry_type_id;
			$entry_data['current_entry_type'] = $current_entry_type;
			$entry_data['entry_number'] =  $cur_entry->number;
			$entry_data['entry_date'] = date_mysql_to_php_display($cur_entry->date);
			$entry_data['entry_dr_total'] =  $cur_entry->dr_total;
			$entry_data['entry_cr_total'] =  $cur_entry->cr_total;
			$entry_data['entry_narration'] = $cur_entry->narration;
	
			/* Getting Ledger details */
			$this->db->from('entry_items')->where('entry_id', $entry_id)->order_by('dc', 'desc');
			$ledger_q = $this->db->get();
			$counter = 0;
			$entry_data['ledger_data'] = array();
			if ($ledger_q->num_rows() > 0)
			{
				foreach ($ledger_q->result() as $row)
				{
					$entry_data['ledger_data'][$counter] = array(
						'id' => $row->ledger_id,
						'name' => $this->Ledger_model->get_name($row->ledger_id),
						'dc' => $row->dc,
						'amount' => $row->amount,
					);
					$counter++;
				}
			}

			/* Preparing message */
			$message = $this->load->view('entry/emailpreview', $entry_data, TRUE);

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
			$this->load->view('entry/email', $data);
			return;
		}
		return;
	}

	function addrow($add_type = 'all')
	{
		$i = time() + rand  (0, time()) + rand  (0, time()) + rand  (0, time());
		$dr_amount = array(
			'name' => 'dr_amount[' . $i . ']',
			'id' => 'dr_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
			'class' => 'dr-item',
			'disabled' => 'disabled',
		);
		$cr_amount = array(
			'name' => 'cr_amount[' . $i . ']',
			'id' => 'cr_amount[' . $i . ']',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
			'class' => 'cr-item',
			'disabled' => 'disabled',
		);

		echo '<tr class="new-row">';
		echo '<td>';
		echo form_dropdown_dc('ledger_dc[' . $i . ']');
		echo '</td>';

		echo '<td>';
		if ($add_type == 'bankcash')
			echo form_input_ledger('ledger_id[' . $i . ']', 0, '', $type = 'bankcash');
		else if ($add_type == 'nobankcash')
			echo form_input_ledger('ledger_id[' . $i . ']', 0, '', $type = 'nobankcash');
		else
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
		echo '<td class="ledger-balance"><div></div>';
		echo '</td>';
		echo '</tr>';
		return;
	}
}

/* End of file entry.php */
/* Location: ./system/application/controllers/entry.php */
