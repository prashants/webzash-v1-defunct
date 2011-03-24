<?php

class StockUnit extends Controller {

	function StockUnit()
	{
		parent::Controller();
		return;
	}

	function index()
	{
		redirect('inventory/stockunit/add');
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'New Stock Unit');

		/* Check access */
		if ( ! check_access('create stock unit'))
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
		$data['stock_unit_symbol'] = array(
			'name' => 'stock_unit_symbol',
			'id' => 'stock_unit_symbol',
			'maxlength' => '15',
			'size' => '15',
			'value' => '',
		);
		$data['stock_unit_name'] = array(
			'name' => 'stock_unit_name',
			'id' => 'stock_unit_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['stock_unit_decimal'] = array(
			'name' => 'stock_unit_decimal',
			'id' => 'stock_unit_decimal',
			'maxlength' => '1',
			'size' => '1',
			'value' => '0',
		);

		/* Form validations */
		$this->form_validation->set_rules('stock_unit_symbol', 'Stock Unit Symbol', 'trim|required|min_length[2]|max_length[15]|unique[stock_units.symbol]');
		$this->form_validation->set_rules('stock_unit_name', 'Stock Unit Name', 'trim|required|min_length[2]|max_length[100]|unique[stock_units.name]');
		$this->form_validation->set_rules('stock_unit_decimal', 'Decimal Places', 'trim|required|max_length[1]|is_natural');

		/* Re-populating form */
		if ($_POST)
		{
			$data['stock_unit_symbol']['value'] = $this->input->post('stock_unit_symbol', TRUE);
			$data['stock_unit_name']['value'] = $this->input->post('stock_unit_name', TRUE);
			$data['stock_unit_decimal']['value'] = $this->input->post('stock_unit_decimal', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/stockunit/add', $data);
			return;
		}
		else
		{
			$data_stock_unit_symbol = $this->input->post('stock_unit_symbol', TRUE);
			$data_stock_unit_name = $this->input->post('stock_unit_name', TRUE);
			$data_stock_unit_decimal = $this->input->post('stock_unit_decimal', TRUE);

			$this->db->trans_start();
			$insert_data = array(
				'symbol' => $data_stock_unit_symbol,
				'name' => $data_stock_unit_name,
				'decimal_places' => $data_stock_unit_decimal,
			);
			if ( ! $this->db->insert('stock_units', $insert_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error addding Stock Unit - ' . $data_stock_unit_name . '.', 'error');
				$this->logger->write_message("error", "Error adding Stock Unit named " . $data_stock_unit_name);
				$this->template->load('template', 'inventory/stockunit/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Stock Unit - ' . $data_stock_unit_name . '.', 'success');
				$this->logger->write_message("success", "Added Stock Unit named " . $data_stock_unit_name);
				redirect('inventory/account');
				return;
			}
		}
		return;
	}

	function edit($id)
	{
		$this->template->set('page_title', 'Edit Stock Unit');

		/* Check access */
		if ( ! check_access('edit stock unit'))
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
			$this->messages->add('Invalid Stock Unit.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Loading current stock unit */
		$this->db->from('stock_units')->where('id', $id);
		$stock_unit_data_q = $this->db->get();
		if ($stock_unit_data_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Stock Unit.', 'error');
			redirect('inventory/account');
			return;
		}
		$stock_unit_data = $stock_unit_data_q->row();

		/* Form fields */
		$data['stock_unit_symbol'] = array(
			'name' => 'stock_unit_symbol',
			'id' => 'stock_unit_symbol',
			'maxlength' => '15',
			'size' => '15',
			'value' => $stock_unit_data->symbol,
		);
		$data['stock_unit_name'] = array(
			'name' => 'stock_unit_name',
			'id' => 'stock_unit_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => $stock_unit_data->name,
		);
		$data['stock_unit_decimal'] = array(
			'name' => 'stock_unit_decimal',
			'id' => 'stock_unit_decimal',
			'maxlength' => '1',
			'size' => '1',
			'value' => $stock_unit_data->decimal_places,
		);
		$data['stock_unit_id'] = $id;

		/* Form validations */
		$this->form_validation->set_rules('stock_unit_symbol', 'Stock Unit Symbol', 'trim|required|min_length[2]|max_length[15]|uniquewithid[stock_units.symbol.' . $id . ']');
		$this->form_validation->set_rules('stock_unit_name', 'Stock Unit Name', 'trim|required|min_length[2]|max_length[100]|uniquewithid[stock_units.name.' . $id . ']');
		$this->form_validation->set_rules('stock_unit_decimal', 'Decimal Places', 'trim|required|max_length[1]|is_natural');

		/* Re-populating form */
		if ($_POST)
		{
			$data['stock_unit_symbol']['value'] = $this->input->post('stock_unit_symbol', TRUE);
			$data['stock_unit_name']['value'] = $this->input->post('stock_unit_name', TRUE);
			$data['stock_unit_decimal']['value'] = $this->input->post('stock_unit_decimal', TRUE);
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'inventory/stockunit/edit', $data);
			return;
		}
		else
		{
			$data_id = $id;
			$data_stock_unit_symbol = $this->input->post('stock_unit_symbol', TRUE);
			$data_stock_unit_name = $this->input->post('stock_unit_name', TRUE);
			$data_stock_unit_decimal = $this->input->post('stock_unit_decimal', TRUE);

			$this->db->trans_start();
			$update_data = array(
				'symbol' => $data_stock_unit_symbol,
				'name' => $data_stock_unit_name,
				'decimal_places' => $data_stock_unit_decimal,
			);
			if ( ! $this->db->where('id', $data_id)->update('stock_units', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating Stock Unit - ' . $data_stock_unit_name . '.', 'error');
				$this->logger->write_message("error", "Error updating Stock Unit named " . $data_stock_unit_name . " [id:" . $data_id . "]");
				$this->template->load('template', 'inventory/stockunit/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Stock Unit - ' . $data_stock_unit_name . '.', 'success');
				$this->logger->write_message("success", "Udpated Stock Unit named " . $data_stock_unit_name . " [id:" . $data_id . "]");
				redirect('inventory/account');
				return;
			}
		}
		return;
	}

	function delete($id)
	{
		/* Check access */
		if ( ! check_access('delete stock unit'))
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
			$this->messages->add('Invalid Stock Unit.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Checking if linked to any stock item */
		$this->db->from('stock_items')->where('stock_unit_id', $id);
		if ($this->db->get()->num_rows() > 0)
		{
			$this->messages->add('Cannot delete Stock Unit. Stock Unit is still in use.', 'error');
			redirect('inventory/account');
			return;
		}

		/* Get the stock unit details */
		$this->db->from('stock_units')->where('id', $id);
		$stock_unit_q = $this->db->get();
		if ($stock_unit_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Stock Unit.', 'error');
			redirect('inventory/account');
			return;
		} else {
			$stock_unit_data = $stock_unit_q->row();
		}

		/* Deleting group */
		$this->db->trans_start();
		if ( ! $this->db->delete('stock_units', array('id' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Stock Unit - ' . $stock_unit_data->name . '.', 'error');
			$this->logger->write_message("error", "Error deleting Stock Unit named " . $stock_unit_data->name . " [id:" . $id . "]");
			redirect('inventory/account');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted Stock Unit - ' . $stock_unit_data->name . '.', 'success');
			$this->logger->write_message("success", "Deleted Stock Unit named " . $stock_unit_data->name . " [id:" . $id . "]");
			redirect('inventory/account');
			return;
		}
		return;
	}
}

/* End of file stockunit.php */
/* Location: ./system/application/controllers/inventory/stockunit.php */
