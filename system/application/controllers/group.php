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

	function delete($id)
	{
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->session->set_flashdata('error', "Invalid Account Group");
			redirect('account');
			return;
		}
		if ($id < 5) {
			$this->session->set_flashdata('error', "Cannot delete system created Account Group");
			redirect('account');
			return;
		}
		$data_present_q = $this->db->query("SELECT * FROM groups WHERE parent_id = ?", array($id));
		if ($data_present_q->num_rows() > 0)
		{
			$this->session->set_flashdata('error', "Cannot delete non-empty Account Group");
			redirect('account');
		}
		$data_present_q = $this->db->query("SELECT * FROM ledgers WHERE group_id = ?", array($id));
		if ($data_present_q->num_rows() > 0)
		{
			$this->session->set_flashdata('error', "Cannot delete non-empty Account Group");
			redirect('account');
		}
		if ($this->db->query("DELETE FROM groups WHERE id = ?", array($id)))
		{
			$this->session->set_flashdata('message', "Account Group deleted successfully");
			redirect('account');
		} else {
			$this->session->set_flashdata('error', "Error deleting Account Group");
			redirect('account');
		}
	}
}
