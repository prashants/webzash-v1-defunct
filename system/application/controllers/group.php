<?php
class Group extends Controller {

	function index()
	{
		redirect('group/add');
	}

	function add()
	{
		$page_data['page_title'] = "New Group";

		/* Form fields */
		$data['groupname'] = array(
			'name' => 'groupname',
			'id' => 'groupname',
			'maxlength' => '100',
			'size' => '40',
			'value' => $this->input->post('groupname'),
		);
		$options = array();
		$group_parent_q = $this->db->query('SELECT * FROM groups WHERE id > 0 ORDER BY name');
		foreach ($group_parent_q->result() as $row)
		{
			$options[$row->id] = $row->name;
		}
		$data['groupparent'] = $options;

		/* Form validations */
		$this->form_validation->set_rules('groupname', 'Group name', 'trim|required|min_length[2]|max_length[100]|unique[groups.name]');
		$this->form_validation->set_rules('groupparent', 'Parent group', 'trim|required|is_natural_no_zero');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('template/header', $page_data);
			$this->load->view('group/add', $data);
		}
		else
		{
			$data_name = $this->input->post('groupname', TRUE);
			$data_parent_id = $this->input->post('groupparent', TRUE);
		
			$res = $this->db->query("INSERT INTO groups (name, parent_id) VALUES (?, ?)", array($data_name, $data_parent_id));
			if (!$res)
			{
				$this->session->set_flashdata('error', "Error addding Account group");
				$this->load->view('template/header', $page_data);
				$this->load->view('group/add', $data);
				$this->load->view('template/footer');
			} else {
				$this->session->set_flashdata('message', "Account group added successfully");
				redirect('account');
			}
		}
		return;
	}

	function delete()
	{
	}
}
