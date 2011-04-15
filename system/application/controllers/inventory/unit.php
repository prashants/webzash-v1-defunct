<?php

class Unit extends Controller {

	function Unit()
	{
		parent::Controller();
		return;
	}

	function index()
	{
		redirect('inventory/unit/add');
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'Add Inventory Unit');

		/* Check access */
		if ( ! check_access('add inventory unit'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Form fields */
		$data['inventory_unit_symbol'] = array(
			'name' => 'inventory_unit_symbol',
			'id' => 'inventory_unit_symbol',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		$data['inventory_unit_name'] = array(
			'name' => 'inventory_unit_name',
			'id' => 'inventory_unit_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['inventory_unit_decimal'] = array(
			'name' => 'inventory_unit_decimal',
			'id' => 'inventory_unit_decimal',
			'maxlength' => '1',
			'size' => '1',
			'value' => '0',
		);

		/* Form validations */
		$this->form_validation->set_rules('inventory_unit_symbol', 'Inventory Unit Symbol', 'trim|required|min_length[2]|max_length[15]|unique[inventory_units.symbol]');
		$this->form_validation->set_rules('inventory_unit_name', 'Inventory Unit Name', 'trim|required|min_length[2]|max_length[100]|unique[inventory_units.name]');
		$this->form_validation->set_rules('inventory_unit_decimal', 'Decimal Places', 'trim|required|max_length[1]|is_natural');

		/* Re-populating form */
		if ($_POST)
		{
			$data['inventory_unit_symbol']['value'] = $this->input->post('inventory_unit_symbol', TRUE);
			$data['inventory_unit_name']['value'] = $this->input->post('inventory_unit_name', TRUE);
			$data['inventory_unit_decimal']['value'] = $this->input->post('inventory_unit_decimal', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/unit/add', $data);
			return;
		}
		else
		{
			$data_inventory_unit_symbol = $this->input->post('inventory_unit_symbol', TRUE);
			$data_inventory_unit_name = $this->input->post('inventory_unit_name', TRUE);
			$data_inventory_unit_decimal = $this->input->post('inventory_unit_decimal', TRUE);

			$this->db->trans_start();
			$insert_data = array(
				'symbol' => $data_inventory_unit_symbol,
				'name' => $data_inventory_unit_name,
				'decimal_places' => $data_inventory_unit_decimal,
			);
			if ( ! $this->db->insert('inventory_units', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Inventory Unit - ' . $data_inventory_unit_name . '.', 'error');
				$this->logger->write_message("error", "Error adding Inventory Unit called " . $data_inventory_unit_name);
				$this->template->load('template', 'inventory/unit/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Inventory Unit - ' . $data_inventory_unit_name . '.', 'success');
				$this->logger->write_message("success", "Added Inventory Unit called " . $data_inventory_unit_name);
				redirect('inventory/account');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Inventory Unit');

		/* Check access */
		if ( ! check_access('edit inventory unit'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->messages->add('Invalid Inventory Unit.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Loading current inventory unit */
		$this->db->from('inventory_units')->where('id', $id);
		$inventory_unit_data_q = $this->db->get();
		if ($inventory_unit_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Inventory Unit.', 'error');
			redirect('inventory/account');
			return;
		}
		$inventory_unit_data = $inventory_unit_data_q->row();

		/* Form fields */
		$data['inventory_unit_symbol'] = array(
			'name' => 'inventory_unit_symbol',
			'id' => 'inventory_unit_symbol',
			'maxlength' => '15',
			'size' => '15',
			'value' => $inventory_unit_data->symbol,
		);
		$data['inventory_unit_name'] = array(
			'name' => 'inventory_unit_name',
			'id' => 'inventory_unit_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $inventory_unit_data->name,
		);
		$data['inventory_unit_decimal'] = array(
			'name' => 'inventory_unit_decimal',
			'id' => 'inventory_unit_decimal',
			'maxlength' => '1',
			'size' => '1',
			'value' => $inventory_unit_data->decimal_places,
		);
		$data['inventory_unit_id'] = $id;

		/* Form validations */
		$this->form_validation->set_rules('inventory_unit_symbol', 'Inventory Unit Symbol', 'trim|required|min_length[2]|max_length[15]|uniquewithid[inventory_units.symbol.' . $id . ']');
		$this->form_validation->set_rules('inventory_unit_name', 'Inventory Unit Name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[inventory_units.name.' . $id . ']');
		$this->form_validation->set_rules('inventory_unit_decimal', 'Decimal Places', 'trim|required|max_length[1]|is_natural');

		/* Re-populating form */
		if ($_POST)
		{
			$data['inventory_unit_symbol']['value'] = $this->input->post('inventory_unit_symbol', TRUE);
			$data['inventory_unit_name']['value'] = $this->input->post('inventory_unit_name', TRUE);
			$data['inventory_unit_decimal']['value'] = $this->input->post('inventory_unit_decimal', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/unit/edit', $data);
			return;
		}
		else
		{
			$data_id = $id;
			$data_inventory_unit_symbol = $this->input->post('inventory_unit_symbol', TRUE);
			$data_inventory_unit_name = $this->input->post('inventory_unit_name', TRUE);
			$data_inventory_unit_decimal = $this->input->post('inventory_unit_decimal', TRUE);

			$this->db->trans_start();
			$update_data = array(
				'symbol' => $data_inventory_unit_symbol,
				'name' => $data_inventory_unit_name,
				'decimal_places' => $data_inventory_unit_decimal,
			);
			if ( ! $this->db->where('id', $data_id)->update('inventory_units', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Inventory Unit - ' . $data_inventory_unit_name . '.', 'error');
				$this->logger->write_message("error", "Error updating Inventory Unit called " . $data_inventory_unit_name . " [id:" . $data_id . "]");
				$this->template->load('template', 'inventory/unit/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Inventory Unit - ' . $data_inventory_unit_name . '.', 'success');
				$this->logger->write_message("success", "Udpated Inventory Unit called " . $data_inventory_unit_name . " [id:" . $data_id . "]");
				redirect('inventory/account');
				return;
			}
		}
		return;
	}

	function delete($id)
	{
		/* Check access */
		if ( ! check_access('delete inventory unit'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->messages->add('Invalid Inventory Unit.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Checking if linked to any inventory item */
		$this->db->from('inventory_items')->where('inventory_unit_id', $id);
		if ($this->db->get()->num_rows() > 0)
		{
			$this->messages->add('Cannot delete Inventory Unit. Inventory Unit is still in use.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Get the inventory unit details */
		$this->db->from('inventory_units')->where('id', $id);
		$inventory_unit_q = $this->db->get();
		if ($inventory_unit_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Inventory Unit.', 'error');
			redirect('inventory/account');
			return;
		} else {
			$inventory_unit_data = $inventory_unit_q->row();
		}

		/* Deleting group */
		$this->db->trans_start();
		if ( ! $this->db->delete('inventory_units', array('id' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Inventory Unit - ' . $inventory_unit_data->name . '.', 'error');
			$this->logger->write_message("error", "Error deleting Inventory Unit called " . $inventory_unit_data->name . " [id:" . $id . "]");
			redirect('inventory/account');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted Inventory Unit - ' . $inventory_unit_data->name . '.', 'success');
			$this->logger->write_message("success", "Deleted Inventory Unit called " . $inventory_unit_data->name . " [id:" . $id . "]");
			redirect('inventory/account');
			return;
		}
		return;
	}
}

/* End of file unit.php */
/* Location: ./system/application/controllers/inventory/unit.php */
