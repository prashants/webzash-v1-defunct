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
		$this->template->set('page_title', 'New Ledger');

		/* Form fields */
		$data['ledger_name'] = array(
			'name' => 'ledger_name',
			'id' => 'ledger_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['ledger_group_id'] = $this->Group_model->get_all_groups();
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
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'ledger/add', $data);
		}
		else
		{
			$data_name = $this->input->post('ledger_name', TRUE);
			$data_group_id = $this->input->post('ledger_group_id', TRUE);
			$data_op_balance = $this->input->post('op_balance', TRUE);
			$data_op_balance_dc = $this->input->post('op_balance_dc', TRUE);
			$data_ledger_type_cashbank_value = $this->input->post('ledger_type_cashbank', TRUE);
			$data_ledger_type_cashbank = "N";
			if ($data_ledger_type_cashbank_value == "1")
			{
				$data_ledger_type_cashbank = "B";
			}

			$this->db->trans_start();
			if ( ! $this->db->query("INSERT INTO ledgers (name, group_id, op_balance, op_balance_dc, type) VALUES (?, ?, ?, ?, ?)", array($data_name, $data_group_id, $data_op_balance, $data_op_balance_dc, $data_ledger_type_cashbank)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding ' . $data_name . ' - Ledger A/C', 'error');
				$this->template->load('template', 'group/add', $data);
			} else {
				$this->db->trans_complete();
				$this->messages->add($data_name . ' - Ledger A/C added successfully', 'success');
				redirect('account');
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Ledger');

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);
		$id = (int)$id;
		if ($id < 1)
		{
			$this->messages->add('Invalid Ledger A/C', 'error');
			redirect('account');
			return;
		}

		/* Loading current group */
		$ledger_data_q = $this->db->query("SELECT * FROM ledgers WHERE id = ?", array($id));
		if ($ledger_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Ledger A/C', 'error');
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
		$data['ledger_group_id'] = $this->Group_model->get_all_groups();
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
		if ($ledger_data->type == "B")
			$data['ledger_type_cashbank'] = TRUE;
		else
			$data['ledger_type_cashbank'] = FALSE;

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
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'ledger/edit', $data);
		}
		else
		{
			$data_name = $this->input->post('ledger_name', TRUE);
			$data_group_id = $this->input->post('ledger_group_id', TRUE);
			$data_op_balance = $this->input->post('op_balance', TRUE);
			$data_op_balance_dc = $this->input->post('op_balance_dc', TRUE);
			$data_id = $id;
			$data_ledger_type_cashbank_value = $this->input->post('ledger_type_cashbank', TRUE);
			$data_ledger_type_cashbank = "N";
			if ($data_ledger_type_cashbank_value == "1")
			{
				$data_ledger_type_cashbank = "B";
			}

			$this->db->trans_start();
			if ( ! $this->db->query("UPDATE ledgers SET name = ?, group_id = ?, op_balance = ?, op_balance_dc = ?, type = ? WHERE id = ?", array($data_name, $data_group_id, $data_op_balance, $data_op_balance_dc, $data_ledger_type_cashbank, $data_id)))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating ' . $data_name . ' - Ledger A/C', 'error');
				$this->template->load('template', 'ledger/edit', $data);
			} else {
				$this->db->trans_complete();
				$this->messages->add($data_name . ' - Ledger A/C updated successfully', 'success');
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
		if ($id < 1)
		{
			$this->messages->add('Invalid Ledger A/C', 'error');
			redirect('account');
			return;
		}
		$data_present_q = $this->db->query("SELECT * FROM voucher_items WHERE ledger_id = ?", array($id));
		if ($data_present_q->num_rows() > 0)
		{
			$this->messages->add('Cannot delete non-empty Ledger A/C', 'error');
			redirect('account');
			return;
		}

		/* Deleting ledger */
		$this->db->trans_start();
		if ( ! $this->db->query("DELETE FROM ledgers WHERE id = ?", array($id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Ledger A/C', 'error');
			redirect('account');
		} else {
			$this->db->trans_complete();
			$this->messages->add('Ledger A/C deleted successfully', 'success');
			redirect('account');
		}
		return;
	}

	function balance($ledger_id = 0)
	{
		if ($ledger_id > 0)
			echo $this->Ledger_model->get_ledger_balance($ledger_id);
		else
			echo "";
	}
}
