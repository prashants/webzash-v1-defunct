<?php

class EntryTypes extends Controller {

	function EntryTypes()
	{
		parent::Controller();
		$this->load->model('Setting_model');

		/* Check access */
		if ( ! check_access('change account settings'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		return;
	}

	function index()
	{
		$this->template->set('page_title', 'Entry Types');
		$this->template->set('nav_links', array('setting/entrytypes/add' => 'Add Entry Type'));

		$this->db->from('entry_types')->order_by('id', 'asc');
		$data['entry_type_data'] = $this->db->get();

		$this->template->load('template', 'setting/entrytypes/index', $data);
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'Add Entry Type');

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('setting/entrytypes');
			return;
		}

		/* Form fields */
		$data['entry_type_label'] = array(
			'name' => 'entry_type_label',
			'id' => 'entry_type_label',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);

		$data['entry_type_name'] = array(
			'name' => 'entry_type_name',
			'id' => 'entry_type_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		$data['entry_type_description'] = array(
			'name' => 'entry_type_description',
			'id' => 'entry_type_description',
			'cols' => '47',
			'rows' => '5',
			'value' => '',
		);

		$data['entry_type_prefix'] = array(
			'name' => 'entry_type_prefix',
			'id' => 'entry_type_prefix',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);

		$data['entry_type_suffix'] = array(
			'name' => 'entry_type_suffix',
			'id' => 'entry_type_suffix',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);

		$data['entry_type_zero_padding'] = array(
			'name' => 'entry_type_zero_padding',
			'id' => 'entry_type_zero_padding',
			'maxlength' => '2',
			'size' => '2',
			'value' => '',
		);

		$data['entry_type_base_types'] = array('1' => 'Normal Entry'); //, '2' => 'Stock Entry');
		$data['entry_type_numberings'] = array('1' => 'Auto', '2' => 'Manual (required)', '3' => 'Manual (optional)');
		$data['bank_cash_ledger_restrictions'] = array(
			'1' => 'Unrestricted',
			'2' => 'Atleast one Bank or Cash account must be present on Debit side',
			'3' => 'Atleast one Bank or Cash account must be present on Credit side',
			'4' => 'Only Bank or Cash account can be present on both Debit and Credit side',
			'5' => 'Only NON Bank or Cash account can be present on both Debit and Credit side',
		);

		$data['entry_type_base_type_active'] = '1';
		$data['entry_type_numbering_active'] = '1';
		$data['bank_cash_ledger_restriction_active'] = '1';

		/* Repopulating form */
		if ($_POST)
		{
			$data['entry_type_label']['value'] = $this->input->post('entry_type_label', TRUE);
			$data['entry_type_name']['value'] = $this->input->post('entry_type_name', TRUE);
			$data['entry_type_description']['value'] = $this->input->post('entry_type_description', TRUE);
			$data['entry_type_prefix']['value'] = $this->input->post('entry_type_prefix', TRUE);
			$data['entry_type_suffix']['value'] = $this->input->post('entry_type_suffix', TRUE);
			$data['entry_type_zero_padding']['value'] = $this->input->post('entry_type_zero_padding', TRUE);

			$data['entry_type_base_type_active'] = $this->input->post('entry_type_base_type', TRUE);
			$data['entry_type_numbering_active'] = $this->input->post('entry_type_numbering', TRUE);
			$data['bank_cash_ledger_restriction_active'] = $this->input->post('bank_cash_ledger_restriction', TRUE);
		}

		/* Form validations */
		$this->form_validation->set_rules('entry_type_label', 'Label', 'trim|required|min_length[2]|max_length[15]|alpha|unique[entry_types.label]');
		$this->form_validation->set_rules('entry_type_name', 'Name', 'trim|required|min_length[2]|max_length[100]|unique[entry_types.name]');
		$this->form_validation->set_rules('entry_type_description', 'Description', 'trim');
		$this->form_validation->set_rules('entry_type_prefix', 'Prefix', 'trim|max_length[10]');
		$this->form_validation->set_rules('entry_type_suffix', 'Suffix', 'trim|max_length[10]');
		$this->form_validation->set_rules('entry_type_zero_padding', 'Zero Padding', 'trim|max_length[2]|is_natural');

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/entrytypes/add', $data);
			return;
		}
		else
		{
			$data_entry_type_label = strtolower($this->input->post('entry_type_label', TRUE));
			$data_entry_type_name = ucfirst($this->input->post('entry_type_name', TRUE));
			$data_entry_type_description = $this->input->post('entry_type_description', TRUE);
			$data_entry_type_prefix = $this->input->post('entry_type_prefix', TRUE);
			$data_entry_type_suffix = $this->input->post('entry_type_suffix', TRUE);
			$data_entry_type_zero_padding = $this->input->post('entry_type_zero_padding', TRUE);
			$data_entry_type_base_type = $this->input->post('entry_type_base_type', TRUE);
			$data_entry_type_numbering = $this->input->post('entry_type_numbering', TRUE);
			$data_bank_cash_ledger_restriction = $this->input->post('bank_cash_ledger_restriction', TRUE);

			if (($data_entry_type_base_type < 1) or ($data_entry_type_base_type > 2))
				$data_entry_type_base_type = 1;

			if (($data_entry_type_numbering < 1) or ($data_entry_type_numbering > 3))
				$data_entry_type_numbering = 1;

			if (($data_bank_cash_ledger_restriction < 1) or ($data_bank_cash_ledger_restriction > 5))
				$data_bank_cash_ledger_restriction = 1;

			/* Calculating Entry Type Id */
			$last_id = 1;
			$this->db->select_max('id', 'lastid')->from('entry_types');
			$last_id_q = $this->db->get();
			if ($row = $last_id_q->row())
			{
				$last_id = (int)$row->lastid;
				$last_id++;
			}

			$this->db->trans_start();
			$insert_data = array(
				'id' => $last_id,
				'label' => $data_entry_type_label,
				'name' => $data_entry_type_name,
				'description' => $data_entry_type_description,
				'base_type' => $data_entry_type_base_type,
				'numbering' => $data_entry_type_numbering,
				'prefix' => $data_entry_type_prefix,
				'suffix' => $data_entry_type_suffix,
				'zero_padding' => $data_entry_type_zero_padding,
				'bank_cash_ledger_restriction' => $data_bank_cash_ledger_restriction,
			);
			if ( ! $this->db->insert('entry_types', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Entry Type - ' . $data_entry_type_name . '.', 'error');
				$this->logger->write_message("error", "Error adding Entry Type called " . $data_entry_type_name);
				$this->template->load('template', 'setting/entrytypes/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Entry Type - ' . $data_entry_type_name . '.', 'success');
				$this->logger->write_message("success", "Added Entry Type called " . $data_entry_type_name);
				redirect('setting/entrytypes');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Entry Type');

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('setting/entrytypes');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 0)
		{
			$this->messages->add('Invalid Entry Type.', 'error');
			redirect('setting/entrytypes');
			return;
		}

		/* Loading current Entry Type */
		$this->db->from('entry_types')->where('id', $id);
		$entry_type_data_q = $this->db->get();
		if ($entry_type_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Entry Type.', 'error');
			redirect('setting/entrytypes');
			return;
		}
		$entry_type_data = $entry_type_data_q->row();

		/* Form fields */
		$data['entry_type_label'] = array(
			'name' => 'entry_type_label',
			'id' => 'entry_type_label',
			'maxlength' => '15',
			'size' => '15',
			'value' => $entry_type_data->label,
		);

		$data['entry_type_name'] = array(
			'name' => 'entry_type_name',
			'id' => 'entry_type_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $entry_type_data->name,
		);

		$data['entry_type_description'] = array(
			'name' => 'entry_type_description',
			'id' => 'entry_type_description',
			'cols' => '47',
			'rows' => '5',
			'value' => $entry_type_data->description,
		);

		$data['entry_type_prefix'] = array(
			'name' => 'entry_type_prefix',
			'id' => 'entry_type_prefix',
			'maxlength' => '10',
			'size' => '10',
			'value' => $entry_type_data->prefix,
		);

		$data['entry_type_suffix'] = array(
			'name' => 'entry_type_suffix',
			'id' => 'entry_type_suffix',
			'maxlength' => '10',
			'size' => '10',
			'value' => $entry_type_data->suffix,
		);

		$data['entry_type_zero_padding'] = array(
			'name' => 'entry_type_zero_padding',
			'id' => 'entry_type_zero_padding',
			'maxlength' => '2',
			'size' => '2',
			'value' => $entry_type_data->zero_padding,
		);

		$data['entry_type_base_types'] = array('1' => 'Normal Entry'); //, '2' => 'Stock Entry');
		$data['entry_type_numberings'] = array('1' => 'Auto', '2' => 'Manual (required)', '3' => 'Manual (optional)');
		$data['bank_cash_ledger_restrictions'] = array(
			'1' => 'Unrestricted',
			'2' => 'Atleast one Bank or Cash account must be present on Debit side',
			'3' => 'Atleast one Bank or Cash account must be present on Credit side',
			'4' => 'Only Bank or Cash account can be present on both Debit and Credit side',
			'5' => 'Only NON Bank or Cash account can be present on both Debit and Credit side',
		);

		$data['entry_type_base_type_active'] = $entry_type_data->base_type;
		$data['entry_type_numbering_active'] = $entry_type_data->numbering;
		$data['bank_cash_ledger_restriction_active'] = $entry_type_data->bank_cash_ledger_restriction;
		$data['entry_type_id'] = $id;

		/* Repopulating form */
		if ($_POST)
		{
			$data['entry_type_label']['value'] = $this->input->post('entry_type_label', TRUE);
			$data['entry_type_name']['value'] = $this->input->post('entry_type_name', TRUE);
			$data['entry_type_description']['value'] = $this->input->post('entry_type_description', TRUE);
			$data['entry_type_prefix']['value'] = $this->input->post('entry_type_prefix', TRUE);
			$data['entry_type_suffix']['value'] = $this->input->post('entry_type_suffix', TRUE);
			$data['entry_type_zero_padding']['value'] = $this->input->post('entry_type_zero_padding', TRUE);

			$data['entry_type_base_type_active'] = $this->input->post('entry_type_base_type', TRUE);
			$data['entry_type_numbering_active'] = $this->input->post('entry_type_numbering', TRUE);
			$data['bank_cash_ledger_restriction_active'] = $this->input->post('bank_cash_ledger_restriction', TRUE);
		}

		/* Form validations */
		$this->form_validation->set_rules('entry_type_label', 'Label', 'trim|required|min_length[2]|max_length[15]|alpha|uniquewithid[entry_types.label.' . $id . ']');
		$this->form_validation->set_rules('entry_type_name', 'Name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[entry_types.name.' . $id . ']');
		$this->form_validation->set_rules('entry_type_description', 'Description', 'trim');
		$this->form_validation->set_rules('entry_type_prefix', 'Prefix', 'trim|max_length[10]');
		$this->form_validation->set_rules('entry_type_suffix', 'Suffix', 'trim|max_length[10]');
		$this->form_validation->set_rules('entry_type_zero_padding', 'Zero Padding', 'trim|max_length[2]|is_natural');

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/entrytypes/edit', $data);
			return;
		}
		else
		{
			$data_entry_type_id = $id;
			$data_entry_type_label = strtolower($this->input->post('entry_type_label', TRUE));
			$data_entry_type_name = ucfirst($this->input->post('entry_type_name', TRUE));
			$data_entry_type_description = $this->input->post('entry_type_description', TRUE);
			$data_entry_type_prefix = $this->input->post('entry_type_prefix', TRUE);
			$data_entry_type_suffix = $this->input->post('entry_type_suffix', TRUE);
			$data_entry_type_zero_padding = $this->input->post('entry_type_zero_padding', TRUE);
			$data_entry_type_base_type = $this->input->post('entry_type_base_type', TRUE);
			$data_entry_type_numbering = $this->input->post('entry_type_numbering', TRUE);
			$data_bank_cash_ledger_restriction = $this->input->post('bank_cash_ledger_restriction', TRUE);

			if (($data_entry_type_base_type < 1) or ($data_entry_type_base_type > 2))
				$data_entry_type_base_type = 1;

			if (($data_entry_type_numbering < 1) or ($data_entry_type_numbering > 3))
				$data_entry_type_numbering = 1;

			if (($data_bank_cash_ledger_restriction < 1) or ($data_bank_cash_ledger_restriction > 5))
				$data_bank_cash_ledger_restriction = 1;

			$this->db->trans_start();
			$update_data = array(
				'label' => $data_entry_type_label,
				'name' => $data_entry_type_name,
				'description' => $data_entry_type_description,
				'base_type' => $data_entry_type_base_type,
				'numbering' => $data_entry_type_numbering,
				'prefix' => $data_entry_type_prefix,
				'suffix' => $data_entry_type_suffix,
				'zero_padding' => $data_entry_type_zero_padding,
				'bank_cash_ledger_restriction' => $data_bank_cash_ledger_restriction,
			);
			if ( ! $this->db->where('id', $data_entry_type_id)->update('entry_types', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Entry Type - ' . $data_entry_type_name . '.', 'error');
				$this->logger->write_message("error", "Error updating Entry Type called " . $data_entry_type_name . " [id:" . $id . "]");
				$this->template->load('template', 'setting/entrytypes/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Entry Type - ' . $data_entry_type_name . '.', 'success');
				$this->logger->write_message("success", "Updated Entry Type called " . $data_entry_type_name . " [id:" . $id . "]");
				redirect('setting/entrytypes');
				return;
			}
		}
		return;
	}

	function delete($id)
	{
		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('setting/entrytypes');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id <= 0)
		{
			$this->messages->add('Invalid Entry Type.', 'error');
			redirect('setting/entrytypes');
			return;
		}

		/* Loading current Entry Type */
		$this->db->from('entry_types')->where('id', $id);
		$entry_type_data_q = $this->db->get();
		if ($entry_type_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Entry Type.', 'error');
			redirect('setting/entrytypes');
			return;
		} else {
			$entry_type_data = $entry_type_data_q->row();
		}

		/* Check if an Entry present for the Entry Type */
		$this->db->from('entries')->where('entry_type', $id);
		$entry_data_q = $this->db->get();
		if ($entry_data_q->num_rows() > 0)
		{
			$this->messages->add('Cannot delete Entry Type. There are still ' . $entry_data_q->num_rows() . ' Entries present.', 'error');
			redirect('setting/entrytypes');
			return;
		}

		/* Deleting Entry Types */
		$this->db->trans_start();
		if ( ! $this->db->delete('entry_types', array('id' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Entry Type - ' . $entry_type_data->name . '.', 'error');
			$this->logger->write_message("error", "Error deleting Entry Type called " . $entry_type_data->name . " [id:" . $id . "]");
			redirect('setting/entrytypes');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted Entry Type - ' . $entry_type_data->name . '.', 'success');
			$this->logger->write_message("success", "Deleted Entry Type called " . $entry_type_data->name . " [id:" . $id . "]");
			redirect('setting/entrytypes');
			return;
		}
		return;
	}
}

/* End of file entrytypes.php */
/* Location: ./system/application/controllers/setting/entrytypes.php */
