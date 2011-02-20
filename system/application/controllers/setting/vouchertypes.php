<?php

class VoucherTypes extends Controller {

	function VoucherTypes()
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
		$this->template->set('page_title', 'Voucher Types');
		$this->template->set('nav_links', array('setting/vouchertypes/add' => 'New Voucher Type'));

		$this->db->from('voucher_types')->order_by('id', 'asc');
		$data['voucher_type_data'] = $this->db->get();

		$this->template->load('template', 'setting/vouchertypes/index', $data);
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'Add Voucher Type');

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('setting/vouchertypes');
			return;
		}

		/* Form fields */
		$data['voucher_type_label'] = array(
			'name' => 'voucher_type_label',
			'id' => 'voucher_type_label',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);

		$data['voucher_type_name'] = array(
			'name' => 'voucher_type_name',
			'id' => 'voucher_type_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		$data['voucher_type_description'] = array(
			'name' => 'voucher_type_description',
			'id' => 'voucher_type_description',
			'cols' => '47',
			'rows' => '5',
			'value' => '',
		);

		$data['voucher_type_prefix'] = array(
			'name' => 'voucher_type_prefix',
			'id' => 'voucher_type_prefix',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);

		$data['voucher_type_suffix'] = array(
			'name' => 'voucher_type_suffix',
			'id' => 'voucher_type_suffix',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);

		$data['voucher_type_zero_padding'] = array(
			'name' => 'voucher_type_zero_padding',
			'id' => 'voucher_type_zero_padding',
			'maxlength' => '2',
			'size' => '2',
			'value' => '',
		);

		$data['voucher_type_base_types'] = array('1' => 'Normal Vocuher', '2' => 'Stock Voucher');
		$data['voucher_type_numberings'] = array('1' => 'Auto', '2' => 'Manual (required)', '3' => 'Manual (optional)');
		$data['bank_cash_ledger_restrictions'] = array(
			'1' => 'Unrestricted',
			'2' => 'Atleast one Bank or Cash A/C must be present on Debit side',
			'3' => 'Atleast one Bank or Cash A/C must be present on Credit side',
			'4' => 'Only Bank or Cash A/C can be present on both Debit and Credit side',
			'5' => 'Only NON Bank or Cash A/C can be present on both Debit and Credit side',
		);
		$data['stock_voucher_types'] = array(
			'1' => 'Purchase',
			'2' => 'Sale',
			'3' => 'Stock Transfer',
		);

		$data['voucher_type_base_type_active'] = '1';
		$data['voucher_type_numbering_active'] = '1';
		$data['bank_cash_ledger_restriction_active'] = '1';
		$data['stock_voucher_type_active'] = '1';

		/* Repopulating form */
		if ($_POST)
		{
			$data['voucher_type_label']['value'] = $this->input->post('voucher_type_label', TRUE);
			$data['voucher_type_name']['value'] = $this->input->post('voucher_type_name', TRUE);
			$data['voucher_type_description']['value'] = $this->input->post('voucher_type_description', TRUE);
			$data['voucher_type_prefix']['value'] = $this->input->post('voucher_type_prefix', TRUE);
			$data['voucher_type_suffix']['value'] = $this->input->post('voucher_type_suffix', TRUE);
			$data['voucher_type_zero_padding']['value'] = $this->input->post('voucher_type_zero_padding', TRUE);

			$data['voucher_type_base_type_active'] = $this->input->post('voucher_type_base_type', TRUE);
			$data['voucher_type_numbering_active'] = $this->input->post('voucher_type_numbering', TRUE);
			$data['bank_cash_ledger_restriction_active'] = $this->input->post('bank_cash_ledger_restriction', TRUE);
			$data['stock_voucher_type_active'] = $this->input->post('stock_voucher_type', TRUE);
		}

		/* Form validations */
		$this->form_validation->set_rules('voucher_type_label', 'Label', 'trim|required|min_length[2]|max_length[15]|alpha|unique[voucher_types.label]');
		$this->form_validation->set_rules('voucher_type_name', 'Name', 'trim|required|min_length[2]|max_length[100]|unique[voucher_types.name]');
		$this->form_validation->set_rules('voucher_type_description', 'Description', 'trim');
		$this->form_validation->set_rules('voucher_type_prefix', 'Prefix', 'trim|max_length[10]');
		$this->form_validation->set_rules('voucher_type_suffix', 'Suffix', 'trim|max_length[10]');
		$this->form_validation->set_rules('voucher_type_zero_padding', 'Zero Padding', 'trim|max_length[2]|is_natural');

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/vouchertypes/add', $data);
			return;
		}
		else
		{
			$data_voucher_type_label = strtolower($this->input->post('voucher_type_label', TRUE));
			$data_voucher_type_name = ucfirst($this->input->post('voucher_type_name', TRUE));
			$data_voucher_type_description = $this->input->post('voucher_type_description', TRUE);
			$data_voucher_type_prefix = $this->input->post('voucher_type_prefix', TRUE);
			$data_voucher_type_suffix = $this->input->post('voucher_type_suffix', TRUE);
			$data_voucher_type_zero_padding = $this->input->post('voucher_type_zero_padding', TRUE);
			$data_voucher_type_base_type = $this->input->post('voucher_type_base_type', TRUE);
			$data_voucher_type_numbering = $this->input->post('voucher_type_numbering', TRUE);
			$data_bank_cash_ledger_restriction = $this->input->post('bank_cash_ledger_restriction', TRUE);
			$data_stock_voucher_type = $this->input->post('stock_voucher_type', TRUE);

			if (($data_voucher_type_base_type < 1) or ($data_voucher_type_base_type > 2))
				$data_voucher_type_base_type = 1;

			if (($data_voucher_type_numbering < 1) or ($data_voucher_type_numbering > 3))
				$data_voucher_type_numbering = 1;

			if (($data_bank_cash_ledger_restriction < 1) or ($data_bank_cash_ledger_restriction > 5))
				$data_bank_cash_ledger_restriction = 1;

			if (($data_stock_voucher_type < 1) or ($data_stock_voucher_type > 3))
				$data_stock_voucher_type = 1;

			/* Calculating Voucher Type Id */
			$last_id = 1;
			$this->db->select_max('id', 'lastid')->from('voucher_types');
			$last_id_q = $this->db->get();
			if ($row = $last_id_q->row())
			{
				$last_id = (int)$row->lastid;
				$last_id++;
			}

			$this->db->trans_start();
			$insert_data = array(
				'id' => $last_id,
				'label' => $data_voucher_type_label,
				'name' => $data_voucher_type_name,
				'description' => $data_voucher_type_description,
				'base_type' => $data_voucher_type_base_type,
				'numbering' => $data_voucher_type_numbering,
				'prefix' => $data_voucher_type_prefix,
				'suffix' => $data_voucher_type_suffix,
				'zero_padding' => $data_voucher_type_zero_padding,
				'bank_cash_ledger_restriction' => $data_bank_cash_ledger_restriction,
				'stock_voucher_type' => $data_stock_voucher_type,
			);
			if ( ! $this->db->insert('voucher_types', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Voucher Type - ' . $data_voucher_type_name . '.', 'error');
				$this->logger->write_message("error", "Error adding Voucher Type named " . $data_voucher_type_name);
				$this->template->load('template', 'setting/vouchertypes/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Voucher Type - ' . $data_voucher_type_name . '.', 'success');
				$this->logger->write_message("success", "Added Voucher Type named " . $data_voucher_type_name);
				redirect('setting/vouchertypes');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Voucher Type');

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('setting/vouchertypes');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 0)
		{
			$this->messages->add('Invalid Voucher Type.', 'error');
			redirect('setting/vouchertypes');
			return;
		}

		/* Loading current Voucher Type */
		$this->db->from('voucher_types')->where('id', $id);
		$voucher_type_data_q = $this->db->get();
		if ($voucher_type_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Voucher Type.', 'error');
			redirect('setting/vouchertypes');
			return;
		}
		$voucher_type_data = $voucher_type_data_q->row();

		/* Form fields */
		$data['voucher_type_label'] = array(
			'name' => 'voucher_type_label',
			'id' => 'voucher_type_label',
			'maxlength' => '15',
			'size' => '15',
			'value' => $voucher_type_data->label,
		);

		$data['voucher_type_name'] = array(
			'name' => 'voucher_type_name',
			'id' => 'voucher_type_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $voucher_type_data->name,
		);

		$data['voucher_type_description'] = array(
			'name' => 'voucher_type_description',
			'id' => 'voucher_type_description',
			'cols' => '47',
			'rows' => '5',
			'value' => $voucher_type_data->description,
		);

		$data['voucher_type_prefix'] = array(
			'name' => 'voucher_type_prefix',
			'id' => 'voucher_type_prefix',
			'maxlength' => '10',
			'size' => '10',
			'value' => $voucher_type_data->prefix,
		);

		$data['voucher_type_suffix'] = array(
			'name' => 'voucher_type_suffix',
			'id' => 'voucher_type_suffix',
			'maxlength' => '10',
			'size' => '10',
			'value' => $voucher_type_data->suffix,
		);

		$data['voucher_type_zero_padding'] = array(
			'name' => 'voucher_type_zero_padding',
			'id' => 'voucher_type_zero_padding',
			'maxlength' => '2',
			'size' => '2',
			'value' => $voucher_type_data->zero_padding,
		);

		switch ($voucher_type_data->base_type)
		{
			case 1: $data['base_type'] = 'Normal Vocuher'; $data['is_normal_voucher'] = TRUE; break;
			case 2: $data['base_type'] = 'Stock Voucher'; $data['is_normal_voucher'] = FALSE; break;
			default: $data['base_type'] = '(Invalid)'; $data['is_normal_voucher'] = TRUE; break;
		}

		switch ($voucher_type_data->stock_voucher_type)
		{
			case 1: $data['stock_voucher_type'] = 'Purchase'; break;
			case 2: $data['stock_voucher_type'] = 'Sale'; break;
			case 3: $data['stock_voucher_type'] = 'Stock Transfer'; break;
			default: $data['stock_voucher_type'] = '(Invalid)'; break;
		}

		$data['voucher_type_numberings'] = array('1' => 'Auto', '2' => 'Manual (required)', '3' => 'Manual (optional)');
		$data['bank_cash_ledger_restrictions'] = array(
			'1' => 'Unrestricted',
			'2' => 'Atleast one Bank or Cash A/C must be present on Debit side',
			'3' => 'Atleast one Bank or Cash A/C must be present on Credit side',
			'4' => 'Only Bank or Cash A/C can be present on both Debit and Credit side',
			'5' => 'Only NON Bank or Cash A/C can be present on both Debit and Credit side',
		);

		$data['voucher_type_numbering_active'] = $voucher_type_data->numbering;
		$data['bank_cash_ledger_restriction_active'] = $voucher_type_data->bank_cash_ledger_restriction;
		$data['voucher_type_id'] = $id;

		/* Repopulating form */
		if ($_POST)
		{
			$data['voucher_type_label']['value'] = $this->input->post('voucher_type_label', TRUE);
			$data['voucher_type_name']['value'] = $this->input->post('voucher_type_name', TRUE);
			$data['voucher_type_description']['value'] = $this->input->post('voucher_type_description', TRUE);
			$data['voucher_type_prefix']['value'] = $this->input->post('voucher_type_prefix', TRUE);
			$data['voucher_type_suffix']['value'] = $this->input->post('voucher_type_suffix', TRUE);
			$data['voucher_type_zero_padding']['value'] = $this->input->post('voucher_type_zero_padding', TRUE);

			$data['voucher_type_numbering_active'] = $this->input->post('voucher_type_numbering', TRUE);
			$data['bank_cash_ledger_restriction_active'] = $this->input->post('bank_cash_ledger_restriction', TRUE);
		}

		/* Form validations */
		$this->form_validation->set_rules('voucher_type_label', 'Label', 'trim|required|min_length[2]|max_length[15]|alpha|uniquewithid[voucher_types.label.' . $id . ']');
		$this->form_validation->set_rules('voucher_type_name', 'Name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[voucher_types.name.' . $id . ']');
		$this->form_validation->set_rules('voucher_type_description', 'Description', 'trim');
		$this->form_validation->set_rules('voucher_type_prefix', 'Prefix', 'trim|max_length[10]');
		$this->form_validation->set_rules('voucher_type_suffix', 'Suffix', 'trim|max_length[10]');
		$this->form_validation->set_rules('voucher_type_zero_padding', 'Zero Padding', 'trim|max_length[2]|is_natural');

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/vouchertypes/edit', $data);
			return;
		}
		else
		{
			$data_voucher_type_id = $id;
			$data_voucher_type_label = strtolower($this->input->post('voucher_type_label', TRUE));
			$data_voucher_type_name = ucfirst($this->input->post('voucher_type_name', TRUE));
			$data_voucher_type_description = $this->input->post('voucher_type_description', TRUE);
			$data_voucher_type_prefix = $this->input->post('voucher_type_prefix', TRUE);
			$data_voucher_type_suffix = $this->input->post('voucher_type_suffix', TRUE);
			$data_voucher_type_zero_padding = $this->input->post('voucher_type_zero_padding', TRUE);
			$data_voucher_type_numbering = $this->input->post('voucher_type_numbering', TRUE);
			$data_bank_cash_ledger_restriction = $this->input->post('bank_cash_ledger_restriction', TRUE);

			if (($data_voucher_type_numbering < 1) or ($data_voucher_type_numbering > 3))
				$data_voucher_type_numbering = 1;

			if (($data_bank_cash_ledger_restriction < 1) or ($data_bank_cash_ledger_restriction > 5))
				$data_bank_cash_ledger_restriction = 1;

			$this->db->trans_start();
			$update_data = array(
				'label' => $data_voucher_type_label,
				'name' => $data_voucher_type_name,
				'description' => $data_voucher_type_description,
				'numbering' => $data_voucher_type_numbering,
				'prefix' => $data_voucher_type_prefix,
				'suffix' => $data_voucher_type_suffix,
				'zero_padding' => $data_voucher_type_zero_padding,
				'bank_cash_ledger_restriction' => $data_bank_cash_ledger_restriction,
			);
			if ( ! $this->db->where('id', $data_voucher_type_id)->update('voucher_types', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Voucher Type - ' . $data_voucher_type_name . '.', 'error');
				$this->logger->write_message("error", "Error updating Voucher Type named " . $data_voucher_type_name . " [id:" . $id . "]");
				$this->template->load('template', 'setting/vouchertypes/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Voucher Type - ' . $data_voucher_type_name . '.', 'success');
				$this->logger->write_message("success", "Updated Voucher Type named " . $data_voucher_type_name . " [id:" . $id . "]");
				redirect('setting/vouchertypes');
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
			redirect('setting/vouchertypes');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id <= 0)
		{
			$this->messages->add('Invalid Voucher Type.', 'error');
			redirect('setting/vouchertypes');
			return;
		}

		/* Loading current Voucher Type */
		$this->db->from('voucher_types')->where('id', $id);
		$voucher_type_data_q = $this->db->get();
		if ($voucher_type_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Voucher Type.', 'error');
			redirect('setting/vouchertypes');
			return;
		} else {
			$voucher_type_data = $voucher_type_data_q->row();
		}

		/* Check if and Voucher present for the Voucher Type */
		$this->db->from('vouchers')->where('voucher_type', $id);
		$voucher_data_q = $this->db->get();
		if ($voucher_data_q->num_rows() > 0)
		{
			$this->messages->add('Cannot delete Voucher Type. There are still ' . $voucher_data_q->num_rows() . ' Vouchers present.', 'error');
			redirect('setting/vouchertypes');
			return;
		}

		/* Deleting Voucher Types */
		$this->db->trans_start();
		if ( ! $this->db->delete('voucher_types', array('id' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Voucher Type - ' . $voucher_type_data->name . '.', 'error');
			$this->logger->write_message("error", "Error deleting Voucher Type named " . $voucher_type_data->name . " [id:" . $id . "]");
			redirect('setting/vouchertypes');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted Voucher Type - ' . $voucher_type_data->name . '.', 'success');
			$this->logger->write_message("success", "Deleted Voucher Type named " . $voucher_type_data->name . " [id:" . $id . "]");
			redirect('setting/vouchertypes');
			return;
		}
		return;
	}
}

/* End of file vouchertypes.php */
/* Location: ./system/application/controllers/setting/vouchertypes.php */
