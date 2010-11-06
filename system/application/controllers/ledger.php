<?php
class Ledger extends Controller {

	function Ledger()
	{
		parent::Controller();
		$this->load->model('Ledger_model');
		$this->load->model('Group_model');
	}

	function index()
	{
		redirect('ledger/add');
	}

	function add()
	{
		$page_data['page_title'] = "New Ledger";
		$this->load->library('validation');

		/* Form fields */
		$data['ledger_name'] = array(
			'name' => 'ledger_name',
			'id' => 'ledger_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $this->input->post('ledger_name'),
		);
		$data['ledger_group_id'] = $this->Group_model->get_all_groups();
		$data['op_balance'] = array(
			'name' => 'op_balance',
			'id' => 'op_balance',
			'maxlength' => '15',
			'size' => '15',
			'value' => $this->input->post('op_balance'),
		);

		/* Form validations */
		$this->form_validation->set_rules('ledger_name', 'Ledger name', 'trim|required|min_length[2]|max_length[100]|unique[ledgers.name]');
		$this->form_validation->set_rules('ledger_group_id', 'Parent group', 'trim|required|is_natural_no_zero');
		$this->form_validation->set_rules('op_balance', 'Opening balance', 'trim|currency');
		$this->form_validation->set_rules('op_balance_dc', 'Opening balance type', 'trim|required|is_dc');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('template/header', $page_data);
			$this->load->view('ledger/add', $data);
			$this->load->view('template/footer');
		}
		else
		{
			$data_name = $this->input->post('ledger_name', TRUE);
			$data_group_id = $this->input->post('ledger_group_id', TRUE);
			$data_op_balance = $this->input->post('op_balance', TRUE);
			$data_op_balance_dc = $this->input->post('op_balance_dc', TRUE);

			if ( ! $this->db->query("INSERT INTO ledgers (name, group_id, op_balance, op_balance_dc) VALUES (?, ?, ?, ?)", array($data_name, $data_group_id, $data_op_balance, $data_op_balance_dc)))
			{
				$this->session->set_flashdata('error', "Error addding Ledger A/C");
				$this->load->view('template/header', $page_data);
				$this->load->view('group/add', $data);
				$this->load->view('template/footer');
			} else {
				$this->session->set_flashdata('message', "Ledger A/C added successfully");
				redirect('account');
			}
		}
		return;
	}

	function edit($id)
	{
		$page_data['page_title'] = "Edit Ledger";

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1) {
			$this->session->set_flashdata('error', "Invalid Ledger A/C");
			redirect('account');
			return;
		}

		/* Loading current group */
		$ledger_data_q = $this->db->query("SELECT * FROM ledgers WHERE id = ?", array($id));
		if ($ledger_data_q->num_rows() < 1)
		{
			$this->session->set_flashdata('error', "Invalid Ledger A/C");
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
		$data['ledger_group'] = $this->Group_model->get_all_groups();
		$data['ledger_group_active'] = $ledger_data->group_id;
		$data['op_balance'] = array(
			'name' => 'op_balance',
			'id' => 'op_balance',
			'maxlength' => '15',
			'size' => '15',
			'value' => $ledger_data->op_balance,
		);
		$data['op_balance_dc'] = $ledger_data->op_balance_dc;
		$data['ledger_id'] = $id;

		/* Form validations */
		$this->form_validation->set_rules('ledger_name', 'Ledger name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[ledgers.name.' . $id . ']');
		$this->form_validation->set_rules('ledger_group', 'Parent group', 'trim|required|is_natural_no_zero');
		$this->form_validation->set_rules('op_balance', 'Opening balance', 'trim|currency');
		$this->form_validation->set_rules('op_balance_dc', 'Opening balance type', 'trim|required|is_dc');

		if ($this->form_validation->run() == FALSE)
		{
			/* Re-populating form */
			if ($this->input->post('submit', TRUE))
			{
				$data['ledger_name']['value'] = $this->input->post('ledger_name', TRUE);
				$data['ledger_group_active'] = $this->input->post('ledger_group', TRUE);
				$data['op_balance']['value'] = $this->input->post('op_balance', TRUE);
				$data['op_balance_dc'] = $this->input->post('op_balance_dc', TRUE);
			}
			$this->load->view('template/header', $page_data);
			$this->load->view('ledger/edit', $data);
			$this->load->view('template/footer');
		}
		else
		{
			$data_name = $this->input->post('ledger_name', TRUE);
			$data_group_id = $this->input->post('ledger_group', TRUE);
			$data_op_balance = $this->input->post('op_balance', TRUE);
			$data_op_balance_dc = $this->input->post('op_balance_dc', TRUE);
			$data_id = $id;

			if ( ! $this->db->query("UPDATE ledgers SET name = ?, group_id = ?, op_balance = ?, op_balance_dc = ? WHERE id = ?", array($data_name, $data_group_id, $data_op_balance, $data_op_balance_dc, $data_id)))
			{
				$this->session->set_flashdata('error', "Error updating Ledger A/C");
				$this->load->view('template/header', $page_data);
				$this->load->view('ledger/edit', $data);
				$this->load->view('template/footer');
			} else {
				$this->session->set_flashdata('message', "Ledger A/C updated successfully");
				redirect('account');
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
			$this->session->set_flashdata('error', "Invalid Ledger A/C");
			redirect('account');
			return;
		}
		$data_present_q = $this->db->query("SELECT * FROM voucher_items WHERE ledger_id = ?", array($id));
		if ($data_present_q->num_rows() > 0)
		{
			$this->session->set_flashdata('error', "Cannot delete non-empty Ledger A/C");
			redirect('account');
			return;
		}

		/* Deleting ledger */
		if ($this->db->query("DELETE FROM ledgers WHERE id = ?", array($id)))
		{
			$this->session->set_flashdata('message', "Ledger A/C deleted successfully");
			redirect('account');
		} else {
			$this->session->set_flashdata('error', "Error deleting Ledger A/C");
			redirect('account');
		}
		return;
	}
}
