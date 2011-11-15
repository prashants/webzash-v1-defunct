<?php

class Group extends Controller {

	function Group()
	{
		parent::Controller();
		$this->load->model('Group_model');
		return;
	}

	function index()
	{
		redirect('group/add');
		return;
	}

	function add()
	{
		$this->load->library('validation');
		$this->template->set('page_title', 'New Group');

		/* Check access */
		if ( ! check_access('create group'))
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
		$data['group_name'] = array(
			'name' => 'group_name',
			'id' => 'group_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['group_parent'] = $this->Group_model->get_all_groups();
		$data['group_parent_active'] = 0;
		$data['affects_gross'] = 0;

		/* Form validations */
		$this->form_validation->set_rules('group_name', 'Group name', 'trim|required|min_length[2]|max_length[100]|unique[groups.name]');
		$this->form_validation->set_rules('group_parent', 'Parent group', 'trim|required|is_natural_no_zero');

		/* Re-populating form */
		if ($_POST)
		{
			$data['group_name']['value'] = $this->input->post('group_name', TRUE);
			$data['group_parent_active'] = $this->input->post('group_parent', TRUE);
			$data['affects_gross'] = $this->input->post('affects_gross', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'group/add', $data);
			return;
		}
		else
		{
			$data_name = $this->input->post('group_name', TRUE);
			$data_parent_id = $this->input->post('group_parent', TRUE);

			/* Check if parent group id present */
			$this->db->select('id')->from('groups')->where('id', $data_parent_id);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid Parent group.', 'error');
				$this->template->load('template', 'group/add', $data);
				return;
			}

			/* Only if Income or Expense can affect gross profit loss calculation */
			$data_affects_gross = $this->input->post('affects_gross', TRUE);
			if ($data_parent_id == "3" || $data_parent_id == "4")
			{
				if ($data_affects_gross == "1")
					$data_affects_gross = 1;
				else
					$data_affects_gross = 0;
			} else {
				$data_affects_gross = 0;
			}

			$this->db->trans_start();
			$insert_data = array(
				'name' => $data_name,
				'parent_id' => $data_parent_id,
				'affects_gross' => $data_affects_gross,
			);
			if ( ! $this->db->insert('groups', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Group account - ' . $data_name . '.', 'error');
				$this->logger->write_message("error", "Error adding Group account called " . $data_name);
				$this->template->load('template', 'group/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Group account - ' . $data_name . '.', 'success');
				$this->logger->write_message("success", "Added Group account called " . $data_name);
				redirect('account');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Group');

		/* Check access */
		if ( ! check_access('edit group'))
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
		if ($id < 1) {
			$this->messages->add('Invalid Group account.', 'error');
			redirect('account');
			return;
		}
		if ($id <= 4) {
			$this->messages->add('Cannot edit System Group account.', 'error');
			redirect('account');
			return;
		}

		/* Loading current group */
		$this->db->from('groups')->where('id', $id);
		$group_data_q = $this->db->get();
		if ($group_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Group account.', 'error');
			redirect('account');
			return;
		}
		$group_data = $group_data_q->row();

		/* Form fields */
		$data['group_name'] = array(
			'name' => 'group_name',
			'id' => 'group_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $group_data->name,
		);
		$data['group_parent'] = $this->Group_model->get_all_groups($id);
		$data['group_parent_active'] = $group_data->parent_id;
		$data['group_id'] = $id;
		$data['affects_gross'] = $group_data->affects_gross;

		/* Form validations */
		$this->form_validation->set_rules('group_name', 'Group name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[groups.name.' . $id . ']');
		$this->form_validation->set_rules('group_parent', 'Parent group', 'trim|required|is_natural_no_zero');


		/* Re-populating form */
		if ($_POST)
		{
			$data['group_name']['value'] = $this->input->post('group_name', TRUE);
			$data['group_parent_active'] = $this->input->post('group_parent', TRUE);
			$data['affects_gross'] = $this->input->post('affects_gross', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'group/edit', $data);
			return;
		}
		else
		{
			$data_name = $this->input->post('group_name', TRUE);
			$data_parent_id = $this->input->post('group_parent', TRUE);
			$data_id = $id;

			/* Check if parent group id present */
			$this->db->select('id')->from('groups')->where('id', $data_parent_id);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid Parent group.', 'error');
				$this->template->load('template', 'group/edit', $data);
				return;
			}

			/* Check if parent group same as current group id */
			if ($data_parent_id == $id)
			{
				$this->messages->add('Invalid Parent group', 'error');
				$this->template->load('template', 'group/edit', $data);
				return;
			}

			/* Only if Income or Expense can affect gross profit loss calculation */
			$data_affects_gross = $this->input->post('affects_gross', TRUE);
			if ($data_parent_id == "3" || $data_parent_id == "4")
			{
				if ($data_affects_gross == "1")
					$data_affects_gross = 1;
				else
					$data_affects_gross = 0;
			} else {
				$data_affects_gross = 0;
			}

			$this->db->trans_start();
			$update_data = array(
				'name' => $data_name,
				'parent_id' => $data_parent_id,
				'affects_gross' => $data_affects_gross,
			);
			if ( ! $this->db->where('id', $data_id)->update('groups', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Group account - ' . $data_name . '.', 'error');
				$this->logger->write_message("error", "Error updating Group account called " . $data_name . " [id:" . $data_id . "]");
				$this->template->load('template', 'group/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Group account - ' . $data_name . '.', 'success');
				$this->logger->write_message("success", "Updated Group account called " . $data_name . " [id:" . $data_id . "]");
				redirect('account');
				return;
			}
		}
		return;
	}

	function delete($id)
	{
		/* Check access */
		if ( ! check_access('delete group'))
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
		if ($id < 1) {
			$this->messages->add('Invalid Group account.', 'error');
			redirect('account');
			return;
		}
		if ($id <= 4) {
			$this->messages->add('Cannot delete System Group account.', 'error');
			redirect('account');
			return;
		}
		$this->db->from('groups')->where('parent_id', $id);
		if ($this->db->get()->num_rows() > 0)
		{
			$this->messages->add('Cannot delete non-empty Group account.', 'error');
			redirect('account');
			return;
		}
		$this->db->from('ledgers')->where('group_id', $id);
		if ($this->db->get()->num_rows() > 0)
		{
			$this->messages->add('Cannot delete non-empty Group account.', 'error');
			redirect('account');
			return;
		}

		/* Get the group details */
		$this->db->from('groups')->where('id', $id);
		$group_q = $this->db->get();
		if ($group_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Group account.', 'error');
			redirect('account');
			return;
		} else {
			$group_data = $group_q->row();
		}

		/* Deleting group */
		$this->db->trans_start();
		if ( ! $this->db->delete('groups', array('id' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Group account - ' . $group_data->name . '.', 'error');
			$this->logger->write_message("error", "Error deleting Group account called " . $group_data->name . " [id:" . $id . "]");
			redirect('account');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted Group account - ' . $group_data->name . '.', 'success');
			$this->logger->write_message("success", "Deleted Group account called " . $group_data->name . " [id:" . $id . "]");
			redirect('account');
			return;
		}
		return;
	}
}

/* End of file group.php */
/* Location: ./system/application/controllers/group.php */
