<?php

class Item extends Controller {

	function Item()
	{
		parent::Controller();
		$this->load->model('Inventory_Unit_model');
		$this->load->model('Inventory_Group_model');
		$this->load->model('Inventory_Item_model');
		return;
	}

	function index()
	{
		redirect('inventory/item/add');
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'Add Inventory Item');

		/* Check access */
		if ( ! check_access('add inventory item'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('account');
			return;
		}

		/* Check if any inventory units and groups is present */
		$this->db->from('inventory_units');
		$inventory_unit_q = $this->db->get();
		if ($inventory_unit_q->num_rows() < 1){
			$this->messages->add('Add a Inventory Unit before adding a Inventory Item.', 'error');
			redirect('inventory/account');
			return;
		}
		$this->db->from('inventory_groups');
		$inventory_group_q = $this->db->get();
		if ($inventory_group_q->num_rows() < 1){
			$this->messages->add('Add a Inventory Group before adding a Inventory Item.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Form fields */
		$data['inventory_item_name'] = array(
			'name' => 'inventory_item_name',
			'id' => 'inventory_item_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['inventory_item_op_quantity'] = array(
			'name' => 'inventory_item_op_quantity',
			'id' => 'inventory_item_op_quantity',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['inventory_item_op_rate_per_unit'] = array(
			'name' => 'inventory_item_op_rate_per_unit',
			'id' => 'inventory_item_op_rate_per_unit',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['inventory_item_op_total'] = array(
			'name' => 'inventory_item_op_total',
			'id' => 'inventory_item_op_total',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['inventory_item_default_sell_price'] = array(
			'name' => 'inventory_item_default_sell_price',
			'id' => 'inventory_item_default_sell_price',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['inventory_item_costing_methods'] = array(
			'1' => 'Last In First Out (LIFO)',
			'2' => 'First In First Out (FIFO)',
		);
		$data['inventory_item_costing_method_active'] = 1;
		$data['inventory_item_units'] = $this->Inventory_Unit_model->get_all_units();
		$data['inventory_item_unit_active'] = 0;
		$data['inventory_item_groups'] = $this->Inventory_Group_model->get_item_groups();
		$data['inventory_item_group_active'] = 0;

		/* Form validations */
		$this->form_validation->set_rules('inventory_item_name', 'Inventory item name', 'trim|required|min_length[2]|max_length[100]|unique[inventory_items.name]');
		$this->form_validation->set_rules('inventory_item_group', 'Inventory group', 'trim|required|is_natural');
		$this->form_validation->set_rules('inventory_item_unit', 'Inventory unit', 'trim|required|is_natural');
		$this->form_validation->set_rules('inventory_item_costing_method', 'Costing method', 'trim|required|is_natural');
		$this->form_validation->set_rules('inventory_item_op_quantity', 'Opening Balance Quantity', 'trim|quantity');
		$this->form_validation->set_rules('inventory_item_op_rate_per_unit', 'Opening Balance Rate per unit', 'trim|currency');
		$this->form_validation->set_rules('inventory_item_op_total', 'Opening Balance Total value', 'trim|currency');
		$this->form_validation->set_rules('inventory_item_default_sell_price', 'Default Selling Price', 'trim|currency');

		/* Re-populating form */
		if ($_POST)
		{
			$data['inventory_item_name']['value'] = $this->input->post('inventory_item_name', TRUE);
			$data['inventory_item_group_active'] = $this->input->post('inventory_item_group', TRUE);
			$data['inventory_item_unit_active'] = $this->input->post('inventory_item_unit', TRUE);
			$data['inventory_item_costing_method_active'] = $this->input->post('inventory_item_costing_method', TRUE);
			$data['inventory_item_op_quantity']['value'] = $this->input->post('inventory_item_op_quantity', TRUE);
			$data['inventory_item_op_rate_per_unit']['value'] = $this->input->post('inventory_item_op_rate_per_unit', TRUE);
			$data['inventory_item_op_total']['value'] = $this->input->post('inventory_item_op_total', TRUE);
			$data['inventory_item_default_sell_price']['value'] = $this->input->post('inventory_item_default_sell_price', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/item/add', $data);
			return;
		}
		else
		{
			$data_inventory_item_name = $this->input->post('inventory_item_name', TRUE);
			$data_inventory_item_group_id = $this->input->post('inventory_item_group', TRUE);
			$data_inventory_item_unit_id = $this->input->post('inventory_item_unit', TRUE);
			$data_inventory_item_costing_method = $this->input->post('inventory_item_costing_method', TRUE);
			$data_inventory_item_op_quantity = $this->input->post('inventory_item_op_quantity', TRUE);
			$data_inventory_item_op_rate_per_unit = $this->input->post('inventory_item_op_rate_per_unit', TRUE);
			$data_inventory_item_op_total = $this->input->post('inventory_item_op_total', TRUE);
			$data_inventory_item_default_sell_price = $this->input->post('inventory_item_default_sell_price', TRUE);

			/* Check if inventory group present */
			$this->db->select('id')->from('inventory_groups')->where('id', $data_inventory_item_group_id);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid Inventory Group.', 'error');
				$this->template->load('template', 'inventory/item/add', $data);
				return;
			}

			/* Check if inventory unit present */
			$this->db->select('id')->from('inventory_units')->where('id', $data_inventory_item_unit_id);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid Inventory Unit.', 'error');
				$this->template->load('template', 'inventory/item/add', $data);
				return;
			}

			if (($data_inventory_item_costing_method < 1) or ($data_inventory_item_costing_method > 2))
				$data_inventory_item_costing_method = 1;

			$this->db->trans_start();
			$insert_data = array(
				'name' => $data_inventory_item_name,
				'inventory_group_id' => $data_inventory_item_group_id,
				'inventory_unit_id' => $data_inventory_item_unit_id,
				'costing_method' => $data_inventory_item_costing_method,
				'op_balance_quantity' => $data_inventory_item_op_quantity,
				'op_balance_rate_per_unit' => $data_inventory_item_op_rate_per_unit,
				'op_balance_total_value' => $data_inventory_item_op_total,
				'default_sell_price' => $data_inventory_item_default_sell_price,
			);
			if ( ! $this->db->insert('inventory_items', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Inventory Item - ' . $data_inventory_item_name . '.', 'error');
				$this->logger->write_message("error", "Error adding Inventory Item called " . $data_inventory_item_name);
				$this->template->load('template', 'inventory/item/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Inventory Item - ' . $data_inventory_item_name . '.', 'success');
				$this->logger->write_message("success", "Added Inventory Item called " . $data_inventory_item_name);
				redirect('inventory/account');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Inventory Item');

		/* Check access */
		if ( ! check_access('edit inventory item'))
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
			$this->messages->add('Invalid Inventory Item.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Loading current inventory item */
		$this->db->from('inventory_items')->where('id', $id);
		$inventory_item_data_q = $this->db->get();
		if ($inventory_item_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Inventory Item.', 'error');
			redirect('inventory/account');
			return;
		}
		$inventory_item_data = $inventory_item_data_q->row();

		/* Form fields */
		$data['inventory_item_name'] = array(
			'name' => 'inventory_item_name',
			'id' => 'inventory_item_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $inventory_item_data->name,
		);
		$data['inventory_item_op_quantity'] = array(
			'name' => 'inventory_item_op_quantity',
			'id' => 'inventory_item_op_quantity',
			'maxlength' => '100',
			'size' => '40',
			'value' => $inventory_item_data->op_balance_quantity,
		);
		$data['inventory_item_op_rate_per_unit'] = array(
			'name' => 'inventory_item_op_rate_per_unit',
			'id' => 'inventory_item_op_rate_per_unit',
			'maxlength' => '100',
			'size' => '40',
			'value' => $inventory_item_data->op_balance_rate_per_unit,
		);
		$data['inventory_item_op_total'] = array(
			'name' => 'inventory_item_op_total',
			'id' => 'inventory_item_op_total',
			'maxlength' => '100',
			'size' => '40',
			'value' => $inventory_item_data->op_balance_total_value,
		);
		$data['inventory_item_default_sell_price'] = array(
			'name' => 'inventory_item_default_sell_price',
			'id' => 'inventory_item_default_sell_price',
			'maxlength' => '100',
			'size' => '40',
			'value' => $inventory_item_data->default_sell_price,
		);
		$data['inventory_item_costing_methods'] = array(
			'1' => 'Last In First Out (LIFO)',
			'2' => 'First In First Out (FIFO)',
		);
		$data['inventory_item_costing_method_active'] = $inventory_item_data->costing_method;
		$data['inventory_item_units'] = $this->Inventory_Unit_model->get_all_units();
		$data['inventory_item_unit_active'] = $inventory_item_data->inventory_unit_id;
		$data['inventory_item_groups'] = $this->Inventory_Group_model->get_item_groups();
		$data['inventory_item_group_active'] = $inventory_item_data->inventory_group_id;
		$data['inventory_item_id'] = $id;

		/* Form validations */
		$this->form_validation->set_rules('inventory_item_name', 'Inventory item name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[inventory_items.name.' . $id . ']');
		$this->form_validation->set_rules('inventory_item_group', 'Inventory group', 'trim|required|is_natural');
		$this->form_validation->set_rules('inventory_item_unit', 'Inventory unit', 'trim|required|is_natural');
		$this->form_validation->set_rules('inventory_item_costing_method', 'Costing method', 'trim|required|is_natural');
		$this->form_validation->set_rules('inventory_item_op_quantity', 'Opening Balance Quantity', 'trim|quantity');
		$this->form_validation->set_rules('inventory_item_op_rate_per_unit', 'Opening Balance Rate per unit', 'trim|currency');
		$this->form_validation->set_rules('inventory_item_op_total', 'Opening Balance Total value', 'trim|currency');
		$this->form_validation->set_rules('inventory_item_default_sell_price', 'Default Selling Price', 'trim|currency');

		/* Re-populating form */
		if ($_POST)
		{
			$data['inventory_item_name']['value'] = $this->input->post('inventory_item_name', TRUE);
			$data['inventory_item_group_active'] = $this->input->post('inventory_item_group', TRUE);
			$data['inventory_item_unit_active'] = $this->input->post('inventory_item_unit', TRUE);
			$data['inventory_item_costing_method_active'] = $this->input->post('inventory_item_costing_method', TRUE);
			$data['inventory_item_op_quantity']['value'] = $this->input->post('inventory_item_op_quantity', TRUE);
			$data['inventory_item_op_rate_per_unit']['value'] = $this->input->post('inventory_item_op_rate_per_unit', TRUE);
			$data['inventory_item_op_total']['value'] = $this->input->post('inventory_item_op_total', TRUE);
			$data['inventory_item_default_sell_price']['value'] = $this->input->post('inventory_item_default_sell_price', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/item/edit', $data);
			return;
		}
		else
		{
			$data_inventory_item_name = $this->input->post('inventory_item_name', TRUE);
			$data_inventory_item_group_id = $this->input->post('inventory_item_group', TRUE);
			$data_inventory_item_unit_id = $this->input->post('inventory_item_unit', TRUE);
			$data_inventory_item_costing_method = $this->input->post('inventory_item_costing_method', TRUE);
			$data_inventory_item_op_quantity = $this->input->post('inventory_item_op_quantity', TRUE);
			$data_inventory_item_op_rate_per_unit = $this->input->post('inventory_item_op_rate_per_unit', TRUE);
			$data_inventory_item_op_total = $this->input->post('inventory_item_op_total', TRUE);
			$data_inventory_item_default_sell_price = $this->input->post('inventory_item_default_sell_price', TRUE);
			$data_id = $id;

			/* Check if inventory group present */
			$this->db->select('id')->from('inventory_groups')->where('id', $data_inventory_item_group_id);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid inventory group.', 'error');
				$this->template->load('template', 'inventory/item/add', $data);
				return;
			}

			/* Check if inventory unit present */
			$this->db->select('id')->from('inventory_units')->where('id', $data_inventory_item_unit_id);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid inventory unit.', 'error');
				$this->template->load('template', 'inventory/item/add', $data);
				return;
			}

			if (($data_inventory_item_costing_method < 1) or ($data_inventory_item_costing_method > 2))
				$data_inventory_item_costing_method = 1;

			$this->db->trans_start();
			$update_data = array(
				'name' => $data_inventory_item_name,
				'inventory_group_id' => $data_inventory_item_group_id,
				'inventory_unit_id' => $data_inventory_item_unit_id,
				'costing_method' => $data_inventory_item_costing_method,
				'op_balance_quantity' => $data_inventory_item_op_quantity,
				'op_balance_rate_per_unit' => $data_inventory_item_op_rate_per_unit,
				'op_balance_total_value' => $data_inventory_item_op_total,
				'default_sell_price' => $data_inventory_item_default_sell_price,
			);
			if ( ! $this->db->where('id', $data_id)->update('inventory_items', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Inventory Item - ' . $data_inventory_item_name . '.', 'error');
				$this->logger->write_message("error", "Error updating Inventory Item called " . $data_inventory_item_name . " [id:" . $data_id . "]");
				$this->template->load('template', 'inventory/item/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Inventory Item - ' . $data_inventory_item_name . '.', 'success');
				$this->logger->write_message("success", "Updated Inventory Item called " . $data_inventory_item_name . " [id:" . $data_id . "]");
				redirect('inventory/account');
				return;
			}
		}
		return;
	}

	function delete($id)
	{
		/* Check access */
		if ( ! check_access('delete inventory item'))
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
			$this->messages->add('Invalid Inventory Item.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Get the inventory item details */
		$this->db->from('inventory_items')->where('id', $id);
		$inventory_item_q = $this->db->get();
		if ($inventory_item_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Inventory Item.', 'error');
			redirect('inventory/account');
			return;
		} else {
			$inventory_item_data = $inventory_item_q->row();
		}

		/* Deleting item */
		$this->db->trans_start();
		if ( ! $this->db->delete('inventory_items', array('id' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Inventory Item - ' . $inventory_item_data->name . '.', 'error');
			$this->logger->write_message("error", "Error deleting Inventory Item called " . $inventory_item_data->name . " [id:" . $id . "]");
			redirect('inventory/account');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted Inventory Item - ' . $inventory_item_data->name . '.', 'success');
			$this->logger->write_message("success", "Deleted Inventory Item called " . $inventory_item_data->name . " [id:" . $id . "]");
			redirect('inventory/account');
			return;
		}
		return;
	}

	function balance($inventory_ledger_id = 0)
	{
		if ($inventory_ledger_id > 0)
		{
			echo $this->Inventory_Item_model->get_closing_quantity($inventory_ledger_id);
		} else {
			echo "";
		}
		return;
	}

	function sellprice($inventory_ledger_id = 0)
	{
		if ($inventory_ledger_id > 0)
			echo $this->Inventory_Item_model->get_selling_price($inventory_ledger_id);
		else
			echo "";
		return;
	}
}

/* End of file item.php */
/* Location: ./system/application/controllers/inventory/item.php */
