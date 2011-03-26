<?php

class Group extends Controller {

	function Group()
	{
		parent::Controller();
		$this->load->model('Inventory_Group_model');
		return;
	}

	function index()
	{
		redirect('inventory/group/add');
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'Add Inventory Group');

		/* Check access */
		if ( ! check_access('create stock group'))
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

		/* Form fields */
		$data['stock_group_name'] = array(
			'name' => 'stock_group_name',
			'id' => 'stock_group_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['stock_group_parents'] = $this->Inventory_Group_model->get_all_groups();
		$data['stock_group_parent_active'] = 0;

		/* Form validations */
		$this->form_validation->set_rules('stock_group_name', 'Inventory group name', 'trim|required|min_length[2]|max_length[100]|unique[stock_groups.name]');
		$this->form_validation->set_rules('stock_group_parent', 'Parent inventory group', 'trim|required|is_natural');

		/* Re-populating form */
		if ($_POST)
		{
			$data['stock_group_name']['value'] = $this->input->post('stock_group_name', TRUE);
			$data['stock_group_parent_active'] = $this->input->post('stock_group_parent', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/group/add', $data);
			return;
		}
		else
		{
			$data_stock_group_name = $this->input->post('stock_group_name', TRUE);
			$data_stock_group_parent_id = $this->input->post('stock_group_parent', TRUE);

			/* Check if parent group id present */
			if ($data_stock_group_parent_id > 0)
			{
				$this->db->select('id')->from('stock_groups')->where('id', $data_stock_group_parent_id);
				if ($this->db->get()->num_rows() < 1)
				{
					$this->messages->add('Invalid parent inventory group.', 'error');
					$this->template->load('template', 'inventory/group/add', $data);
					return;
				}
			}

			$this->db->trans_start();
			$insert_data = array(
				'name' => $data_stock_group_name,
				'parent_id' => $data_stock_group_parent_id,
			);
			if ( ! $this->db->insert('stock_groups', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Inventory Group - ' . $data_stock_group_name . '.', 'error');
				$this->logger->write_message("error", "Error adding Inventory Group named " . $data_stock_group_name);
				$this->template->load('template', 'inventory/group/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Inventory Group - ' . $data_stock_group_name . '.', 'success');
				$this->logger->write_message("success", "Added Inventory Group named " . $data_stock_group_name);
				redirect('inventory/account');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Inventory Group');

		/* Check access */
		if ( ! check_access('edit stock group'))
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
			$this->messages->add('Invalid Inventory Group.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Loading current group */
		$this->db->from('stock_groups')->where('id', $id);
		$stock_group_data_q = $this->db->get();
		if ($stock_group_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Inventory Group.', 'error');
			redirect('inventory/account');
			return;
		}
		$stock_group_data = $stock_group_data_q->row();

		/* Form fields */
		$data['stock_group_name'] = array(
			'name' => 'stock_group_name',
			'id' => 'stock_group_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $stock_group_data->name,
		);
		$data['stock_group_parents'] = $this->Inventory_Group_model->get_all_groups($stock_group_data->id);
		$data['stock_group_parent_active'] = $stock_group_data->parent_id;
		$data['stock_group_id'] = $id;

		/* Form validations */
		$this->form_validation->set_rules('stock_group_name', 'Inventory group name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[stock_groups.name.' . $id . ']');
		$this->form_validation->set_rules('stock_group_parent', 'Parent inventory group', 'trim|required|is_natural');

		/* Re-populating form */
		if ($_POST)
		{
			$data['stock_group_name']['value'] = $this->input->post('stock_group_name', TRUE);
			$data['stock_group_parent_active'] = $this->input->post('stock_group_parent', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/group/edit', $data);
			return;
		}
		else
		{
			$data_stock_group_name = $this->input->post('stock_group_name', TRUE);
			$data_stock_group_parent_id = $this->input->post('stock_group_parent', TRUE);
			$data_id = $id;

			/* Check if parent group id present */
			if ($data_stock_group_parent_id > 0)
			{
				$this->db->select('id')->from('stock_groups')->where('id', $data_stock_group_parent_id);
				if ($this->db->get()->num_rows() < 1)
				{
					$this->messages->add('Invalid parent inventory group.', 'error');
					$this->template->load('template', 'inventory/group/edit', $data);
					return;
				}
			}

			/* Check if parent group same as current group id */
			if ($data_stock_group_parent_id > 0)
			{
				if ($data_stock_group_parent_id == $id)
				{
					$this->messages->add('Invalid Parent inventory group', 'error');
					$this->template->load('template', 'inventory/group/edit', $data);
					return;
				}
			}

			$this->db->trans_start();
			$update_data = array(
				'name' => $data_stock_group_name,
				'parent_id' => $data_stock_group_parent_id,
			);
			if ( ! $this->db->where('id', $data_id)->update('stock_groups', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Inventory Group - ' . $data_stock_group_name . '.', 'error');
				$this->logger->write_message("error", "Error updating Inventory Group named " . $data_stock_group_name . " [id:" . $data_id . "]");
				$this->template->load('template', 'inventory/group/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Inventory Group - ' . $data_stock_group_name . '.', 'success');
				$this->logger->write_message("success", "Updated Inventory Group named " . $data_stock_group_name . " [id:" . $data_id . "]");
				redirect('inventory/account');
				return;
			}
		}
		return;
	}

	function delete($id)
	{
		/* Check access */
		if ( ! check_access('delete stock group'))
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
			$this->messages->add('Invalid Inventory Group.', 'error');
			redirect('inventory/account');
			return;
		}

		$this->db->from('stock_groups')->where('parent_id', $id);
		if ($this->db->get()->num_rows() > 0)
		{
			$this->messages->add('Cannot delete non-empty Inventory Group.', 'error');
			redirect('inventory/account');
			return;
		}
		$this->db->from('stock_items')->where('group_id', $id);
		if ($this->db->get()->num_rows() > 0)
		{
			$this->messages->add('Cannot delete non-empty Inventory Group.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Get the group details */
		$this->db->from('stock_groups')->where('id', $id);
		$stock_group_q = $this->db->get();
		if ($stock_group_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Inventory Group.', 'error');
			redirect('inventory/account');
			return;
		} else {
			$stock_group_data = $stock_group_q->row();
		}

		/* Deleting group */
		$this->db->trans_start();
		if ( ! $this->db->delete('stock_groups', array('id' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Inventory Group - ' . $stock_group_data->name . '.', 'error');
			$this->logger->write_message("error", "Error deleting Inventory Group named " . $stock_group_data->name . " [id:" . $id . "]");
			redirect('inventory/account');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted Inventory Group - ' . $stock_group_data->name . '.', 'success');
			$this->logger->write_message("success", "Deleted Inventory Group named " . $stock_group_data->name . " [id:" . $id . "]");
			redirect('inventory/account');
			return;
		}
		return;
	}
}

/* End of file group.php */
/* Location: ./system/application/controllers/inventory/group.php */
