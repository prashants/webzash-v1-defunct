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
			if ($this->db->query("SELECT id FROM groups WHERE id = ?", array($data_parent_id))->num_rows() < 1)
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
			if ( ! $this->db->query("INSERT INTO groups (name, parent_id, affects_gross) VALUES (?, ?, ?)", array($data_name, $data_parent_id, $data_affects_gross)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding ' . $data_name . ' - Group A/C.', 'error');
				$this->logger->write_message("error", "Error adding Group A/C named " . $data_name);
				$this->template->load('template', 'group/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added ' . $data_name . ' - Group A/C.', 'success');
				$this->logger->write_message("success", "Added Group A/C named " . $data_name);
				redirect('account');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Group');

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->messages->add('Invalid Group A/C.', 'error');
			redirect('account');
			return;
		}
		if ($id < 5) {
			$this->messages->add('Cannot edit system created Group A/C.', 'error');
			redirect('account');
			return;
		}

		/* Loading current group */
		$group_data_q = $this->db->query("SELECT * FROM groups WHERE id = ?", array($id));
		if ($group_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Group A/C.', 'error');
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
			if ($this->db->query("SELECT id FROM groups WHERE id = ?", array($data_parent_id))->num_rows() < 1)
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
			if ( ! $this->db->query("UPDATE groups SET name = ?, parent_id = ?, affects_gross = ? WHERE id = ?", array($data_name, $data_parent_id, $data_affects_gross, $data_id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating ' . $data_name . ' - Group A/C.', 'error');
				$this->logger->write_message("error", "Error updating Group A/C named " . $data_name . " [id:" . $data_id . "]");
				$this->template->load('template', 'group/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated ' . $data_name . ' - Group A/C.', 'success');
				$this->logger->write_message("success", "Updated Group A/C named " . $data_name . " [id:" . $data_id . "]");
				redirect('account');
				return;
			}
		}
		return;
	}

	function delete($id)
	{
		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->messages->add('Invalid Group A/C.', 'error');
			redirect('account');
			return;
		}
		if ($id < 5) {
			$this->messages->add('Cannot delete system created Group A/C.', 'error');
			redirect('account');
			return;
		}
		$data_present_q = $this->db->query("SELECT * FROM groups WHERE parent_id = ?", array($id));
		if ($data_present_q->num_rows() > 0)
		{
			$this->messages->add('Cannot delete non-empty Group A/C.', 'error');
			redirect('account');
			return;
		}
		$data_present_q = $this->db->query("SELECT * FROM ledgers WHERE group_id = ?", array($id));
		if ($data_present_q->num_rows() > 0)
		{
			$this->messages->add('Cannot delete non-empty Group A/C.', 'error');
			redirect('account');
			return;
		}

		/* Get the group details */
		$group_q = $this->db->query("SELECT * FROM groups WHERE id = ?", array($id));
		if ($group_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Group A/C.', 'error');
			redirect('account');
			return;
		} else {
			$group_data = $group_q->row();
		}

		/* Deleting group */
		$this->db->trans_start();
		if ( ! $this->db->query("DELETE FROM groups WHERE id = ?", array($id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting ' . $group_data->name . ' - Group A/C.', 'error');
			$this->logger->write_message("error", "Error deleting Group A/C named " . $group_data->name . " [id:" . $id . "]");
			redirect('account');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted ' . $group_data->name . ' - Group A/C.', 'success');
			$this->logger->write_message("success", "Deleted Group A/C named " . $group_data->name . " [id:" . $id . "]");
			redirect('account');
			return;
		}
		return;
	}
}

/* End of file group.php */
/* Location: ./system/application/controllers/group.php */
