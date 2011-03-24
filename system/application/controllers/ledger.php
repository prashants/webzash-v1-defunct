<?php

class Ledger extends Controller {

	function Ledger()
	{
		parent::Controller();
		$this->load->model('Ledger_model');
		$this->load->model('Group_model');
		return;
	}

	function index()
	{
		redirect('ledger/add');
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'Add Ledger');

		/* Check access */
		if ( ! check_access('create ledger'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('account');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('account');
			return;
		}

		/* Form fields */
		$data['ledger_name'] = array(
			'name' => 'ledger_name',
			'id' => 'ledger_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['ledger_group_id'] = $this->Group_model->get_ledger_groups();
		$data['op_balance'] = array(
			'name' => 'op_balance',
			'id' => 'op_balance',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		$data['ledger_group_active'] = 0;
		$data['op_balance_dc'] = "D";
		$data['ledger_type_cashbank'] = FALSE;
		$data['reconciliation'] = FALSE;

		/**** Inventory ***/
		$data['affects_inventory'] = 0;
		$data['affects_inventory_options'] = array(
			'2' => 'Purchases Ledger',
			'3' => 'Sales Ledger',
			'4' => 'Creditor (Supplier)',
			'5' => 'Debtor (Customer)',
		);
		$data['affects_inventory_option_active'] = '2';

		/* Form validations */
		$this->form_validation->set_rules('ledger_name', 'Ledger name', 'trim|required|min_length[2]|max_length[100]|unique[ledgers.name]');
		$this->form_validation->set_rules('ledger_group_id', 'Parent group', 'trim|required|is_natural_no_zero');
		$this->form_validation->set_rules('op_balance', 'Opening balance', 'trim|currency');
		$this->form_validation->set_rules('op_balance_dc', 'Opening balance type', 'trim|required|is_dc');

		/* Re-populating form */
		if ($_POST)
		{
			$data['ledger_name']['value'] = $this->input->post('ledger_name', TRUE);
			$data['op_balance']['value'] = $this->input->post('op_balance', TRUE);
			$data['ledger_group_active'] = $this->input->post('ledger_group_id', TRUE);
			$data['op_balance_dc'] = $this->input->post('op_balance_dc', TRUE);
			$data['ledger_type_cashbank'] = $this->input->post('ledger_type_cashbank', TRUE);
			$data['reconciliation'] = $this->input->post('reconciliation', TRUE);

			/**** Inventory ***/
			$data['affects_inventory'] = $this->input->post('affects_inventory', TRUE);
			$data['affects_inventory_option_active'] = $this->input->post('affects_inventory_option', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'ledger/add', $data);
			return;
		}
		else
		{
			$data_name = $this->input->post('ledger_name', TRUE);
			$data_group_id = $this->input->post('ledger_group_id', TRUE);
			$data_op_balance = $this->input->post('op_balance', TRUE);
			$data_op_balance_dc = $this->input->post('op_balance_dc', TRUE);
			$data_ledger_type = 0;
			$data_ledger_type_cashbank_value = $this->input->post('ledger_type_cashbank', TRUE);
			$data_reconciliation = $this->input->post('reconciliation', TRUE);

			/**** Inventory ***/
			$data_affects_inventory = $this->input->post('affects_inventory', TRUE);
			$data_affects_inventory_option_active = $this->input->post('affects_inventory_option', TRUE);

			if ($data_group_id < 5)
			{
				$this->messages->add('Invalid Parent group.', 'error');
				$this->template->load('template', 'ledger/add', $data);
				return;
			}

			/* Check if parent group id present */
			$this->db->select('id')->from('groups')->where('id', $data_group_id);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid Parent group.', 'error');
				$this->template->load('template', 'ledger/add', $data);
				return;
			}

			if ($data_ledger_type_cashbank_value == "1")
			{
				$data_ledger_type = 1;
			} else {
				$data_ledger_type = 0;
			}

			if ($data_reconciliation == "1")
			{
				$data_reconciliation = 1;
			} else {
				$data_reconciliation = 0;
			}

			/**** Inventory ***/
			if ($data_affects_inventory == "1")
			{
				if (($data_affects_inventory_option_active < 2) or ($data_affects_inventory_option_active > 5))
				{
					$this->messages->add('Invalid inventory option selected.', 'error');
					$this->template->load('template', 'ledger/add', $data);
					return;
				}
				$data_ledger_type = $data_affects_inventory_option_active;
			}

			$this->db->trans_start();
			$insert_data = array(
				'name' => $data_name,
				'group_id' => $data_group_id,
				'op_balance' => $data_op_balance,
				'op_balance_dc' => $data_op_balance_dc,
				'type' => $data_ledger_type,
				'reconciliation' => $data_reconciliation,
			);
			if ( ! $this->db->insert('ledgers', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Ledger account - ' . $data_name . '.', 'error');
				$this->logger->write_message("error", "Error adding Ledger account called " . $data_name);
				$this->template->load('template', 'group/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Ledger account - ' . $data_name . '.', 'success');
				$this->logger->write_message("success", "Added Ledger account called " . $data_name);
				redirect('account');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Ledger');

		/* Check access */
		if ( ! check_access('edit ledger'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('account');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('account');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1)
		{
			$this->messages->add('Invalid Ledger account.', 'error');
			redirect('account');
			return;
		}

		/* Loading current group */
		$this->db->from('ledgers')->where('id', $id);
		$ledger_data_q = $this->db->get();
		if ($ledger_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Ledger account.', 'error');
			redirect('account');
			return;
		}
		$ledger_data = $ledger_data_q->row();

		/* Form fields */
		$data['ledger_name'] = array(
			'name' => 'ledger_name',
			'id' => 'ledger_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $ledger_data->name,
		);
		$data['ledger_group_id'] = $this->Group_model->get_ledger_groups();
		$data['op_balance'] = array(
			'name' => 'op_balance',
			'id' => 'op_balance',
			'maxlength' => '15',
			'size' => '15',
			'value' => $ledger_data->op_balance,
		);
		$data['ledger_group_active'] = $ledger_data->group_id;
		$data['op_balance_dc'] = $ledger_data->op_balance_dc;
		$data['ledger_id'] = $id;
		$data['reconciliation'] = $ledger_data->reconciliation;

		/**** Inventory ***/
		$data['affects_inventory_options'] = array(
			'2' => 'Purchases Ledger',
			'3' => 'Sales Ledger',
			'4' => 'Creditor (Supplier)',
			'5' => 'Debtor (Customer)',
		);
		if ($ledger_data->type <= 0)
		{
			$data['ledger_type_cashbank'] = 0;
			$data['affects_inventory'] = 0;
			$data['affects_inventory_option_active'] = 2;
		} else if ($ledger_data->type == 1) {
			$data['ledger_type_cashbank'] = 1;
			$data['affects_inventory'] = 0;
			$data['affects_inventory_option_active'] = 2;
		} else if (($ledger_data->type > 1) and ($ledger_data->type < 6)) {
			$data['ledger_type_cashbank'] = 0;
			$data['affects_inventory'] = 1;
			$data['affects_inventory_option_active'] = $ledger_data->type;
		} else {
			$data['ledger_type_cashbank'] = 0;
			$data['affects_inventory'] = 0;
			$data['affects_inventory_option_active'] = 2;
		}

		/* Form validations */
		$this->form_validation->set_rules('ledger_name', 'Ledger name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[ledgers.name.' . $id . ']');
		$this->form_validation->set_rules('ledger_group_id', 'Parent group', 'trim|required|is_natural_no_zero');
		$this->form_validation->set_rules('op_balance', 'Opening balance', 'trim|currency');
		$this->form_validation->set_rules('op_balance_dc', 'Opening balance type', 'trim|required|is_dc');

		/* Re-populating form */
		if ($_POST)
		{
			$data['ledger_name']['value'] = $this->input->post('ledger_name', TRUE);
			$data['ledger_group_active'] = $this->input->post('ledger_group_id', TRUE);
			$data['op_balance']['value'] = $this->input->post('op_balance', TRUE);
			$data['op_balance_dc'] = $this->input->post('op_balance_dc', TRUE);
			$data['ledger_type_cashbank'] = $this->input->post('ledger_type_cashbank', TRUE);
			$data['reconciliation'] = $this->input->post('reconciliation', TRUE);

			/**** Inventory ***/
			$data['affects_inventory'] = $this->input->post('affects_inventory', TRUE);
			$data['affects_inventory_option_active'] = $this->input->post('affects_inventory_option', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'ledger/edit', $data);
			return;
		}
		else
		{
			$data_name = $this->input->post('ledger_name', TRUE);
			$data_group_id = $this->input->post('ledger_group_id', TRUE);
			$data_op_balance = $this->input->post('op_balance', TRUE);
			$data_op_balance_dc = $this->input->post('op_balance_dc', TRUE);
			$data_ledger_type = 0;
			$data_ledger_type_cashbank_value = $this->input->post('ledger_type_cashbank', TRUE);
			$data_reconciliation = $this->input->post('reconciliation', TRUE);
			$data_id = $id;

			/**** Inventory ***/
			$data_affects_inventory = $this->input->post('affects_inventory', TRUE);
			$data_affects_inventory_option_active = $this->input->post('affects_inventory_option', TRUE);

			if ($data_group_id < 5)
			{
				$this->messages->add('Invalid Parent group.', 'error');
				$this->template->load('template', 'ledger/edit', $data);
				return;
			}

			/* Check if parent group id present */
			$this->db->select('id')->from('groups')->where('id', $data_group_id);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid Parent group.', 'error');
				$this->template->load('template', 'ledger/edit', $data);
				return;
			}

			/* Check if bank_cash_ledger_restriction both voucher present */
			if ($data_ledger_type_cashbank_value != "1")
			{
				$voucher_type_all = $this->config->item('account_voucher_types');
				foreach ($voucher_type_all as $voucher_type_id => $row)
				{
					/* Check for Voucher types where bank_cash_ledger_restriction is for all ledgers */
					if ($row['bank_cash_ledger_restriction'] == 4)
					{
						$this->db->from('voucher_items')->join('vouchers', 'voucher_items.voucher_id = vouchers.id')->where('vouchers.voucher_type', $voucher_type_id)->where('voucher_items.ledger_id', $id);
						$all_ledger_bank_cash_count = $this->db->get()->num_rows();
						if ($all_ledger_bank_cash_count > 0)
						{
							$this->messages->add('Cannot remove the Bank or Cash Account status of this Ledger account since it is still linked with ' . $all_ledger_bank_cash_count . ' ' . $row['name'] . ' Vouchers entries.', 'error');
							$this->template->load('template', 'ledger/edit', $data);
							return;
						}
					}
				}
			}

			if ($data_ledger_type_cashbank_value == "1")
			{
				$data_ledger_type = 1;
			} else {
				$data_ledger_type = 0;
			}

			if ($data_reconciliation == "1")
			{
				$data_reconciliation = 1;
			} else {
				$data_reconciliation = 0;
			}

			/**** Inventory ***/
			if ($data_affects_inventory == "1")
			{
				if (($data_affects_inventory_option_active < 2) or ($data_affects_inventory_option_active > 5))
				{
					$this->messages->add('Invalid inventory option selected.', 'error');
					$this->template->load('template', 'ledger/edit', $data);
					return;
				}
				$data_ledger_type = $data_affects_inventory_option_active;
			}

			$this->db->trans_start();
			$update_data = array(
				'name' => $data_name,
				'group_id' => $data_group_id,
				'op_balance' => $data_op_balance,
				'op_balance_dc' => $data_op_balance_dc,
				'type' => $data_ledger_type,
				'reconciliation' => $data_reconciliation,
			);
			if ( ! $this->db->where('id', $data_id)->update('ledgers', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Ledger account - ' . $data_name . '.', 'error');
				$this->logger->write_message("error", "Error updating Ledger account called " . $data_name . " [id:" . $data_id . "]");
				$this->template->load('template', 'ledger/edit', $data);
				return;
			} else {
				/* Deleting reconciliation data if reconciliation disabled */
				if (($ledger_data->reconciliation == 1) AND ($data_reconciliation == 0))
				{
					$this->Ledger_model->delete_reconciliation($data_id);
				}
				$this->db->trans_complete();
				$this->messages->add('Updated Ledger account - ' . $data_name . '.', 'success');
				$this->logger->write_message("success", "Updated Ledger account called " . $data_name . " [id:" . $data_id . "]");
				redirect('account');
				return;
			}
		}
		return;
	}

	function delete($id)
	{
		/* Check access */
		if ( ! check_access('delete ledger'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('account');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('account');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1)
		{
			$this->messages->add('Invalid Ledger account.', 'error');
			redirect('account');
			return;
		}
		$this->db->from('voucher_items')->where('ledger_id', $id);
		if ($this->db->get()->num_rows() > 0)
		{
			$this->messages->add('Cannot delete non-empty Ledger account.', 'error');
			redirect('account');
			return;
		}

		/* Get the ledger details */
		$this->db->from('ledgers')->where('id', $id);
		$ledger_q = $this->db->get();
		if ($ledger_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Ledger account.', 'error');
			redirect('account');
			return;
		} else {
			$ledger_data = $ledger_q->row();
		}

		/* Deleting ledger */
		$this->db->trans_start();
		if ( ! $this->db->delete('ledgers', array('id' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Ledger account - ' . $ledger_data->name . '.', 'error');
			$this->logger->write_message("error", "Error deleting Ledger account called " . $ledger_data->name . " [id:" . $id . "]");
			redirect('account');
			return;
		} else {
			/* Deleting reconciliation data if present */
			$this->Ledger_model->delete_reconciliation($id);
			$this->db->trans_complete();
			$this->messages->add('Deleted Ledger account - ' . $ledger_data->name . '.', 'success');
			$this->logger->write_message("success", "Deleted Ledger account called " . $ledger_data->name . " [id:" . $id . "]");
			redirect('account');
			return;
		}
		return;
	}

	function balance($ledger_id = 0)
	{
		if ($ledger_id > 0)
			echo $this->Ledger_model->get_ledger_balance($ledger_id);
		else
			echo "";
		return;
	}
}

/* End of file ledger.php */
/* Location: ./system/application/controllers/ledger.php */
