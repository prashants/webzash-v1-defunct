<?php

class Create extends Controller {

	function Create()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->template->set('page_title', 'Create new webzash account');

		/* Form fields */
		$default_start = '01/04/';
		$default_end = '31/03/';
		if (date('n') > 3)
		{
			$default_start .= date('Y');
			$default_end .= date('Y') + 1;
		} else {
			$default_start .= date('Y') - 1;
			$default_end .= date('Y');
		}

		/* Form fields */
		$data['account_name'] = array(
			'name' => 'account_name',
			'id' => 'account_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['account_address'] = array(
			'name' => 'account_address',
			'id' => 'account_address',
			'rows' => '4',
			'cols' => '47',
			'value' => '',
		);
		$data['account_email'] = array(
			'name' => 'account_email',
			'id' => 'account_email',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['assy_start'] = array(
			'name' => 'assy_start',
			'id' => 'assy_start',
			'maxlength' => '11',
			'size' => '11',
			'value' => $default_start,
		);
		$data['assy_end'] = array(
			'name' => 'assy_end',
			'id' => 'assy_end',
			'maxlength' => '11',
			'size' => '11',
			'value' => $default_end,
		);
		$data['account_currency'] = array(
			'name' => 'account_currency',
			'id' => 'account_currency',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['account_date'] = array(
			'name' => 'account_date',
			'id' => 'account_date',
			'maxlength' => '20',
			'size' => '10',
			'value' => '',
		);
		$data['account_timezone'] = 'UTC';

		$data['database_name'] = array(
			'name' => 'database_name',
			'id' => 'database_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		$data['database_username'] = array(
			'name' => 'database_username',
			'id' => 'database_username',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		$data['database_password'] = array(
			'name' => 'database_password',
			'id' => 'database_password',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		$data['database_host'] = array(
			'name' => 'database_host',
			'id' => 'database_host',
			'maxlength' => '100',
			'size' => '40',
			'value' => 'locahost',
		);

		$data['database_port'] = array(
			'name' => 'database_port',
			'id' => 'database_port',
			'maxlength' => '100',
			'size' => '40',
			'value' => '3306',
		);
		/* Form validations */
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|required|min_length[2]|max_length[100]');
		$this->form_validation->set_rules('account_address', 'Account Address', 'trim|max_length[255]');
		$this->form_validation->set_rules('account_email', 'Account Email', 'trim|valid_email');
		$this->form_validation->set_rules('assy_start', 'Assessment Year Start', 'trim|required|is_date');
		$this->form_validation->set_rules('assy_end', 'Assessment Year End', 'trim|required|is_date');
		$this->form_validation->set_rules('account_currency', 'Currency', 'trim|max_length[10]');
		$this->form_validation->set_rules('account_date', 'Date', 'trim|max_length[30]');
		$this->form_validation->set_rules('account_timezone', 'Timezone', 'trim|max_length[6]');

		/* Repopulating form */
		if ($_POST)
		{
			$data['account_name']['value'] = $this->input->post('account_name');
			$data['account_address']['value'] = $this->input->post('account_address');
			$data['account_email']['value'] = $this->input->post('account_email');
			$data['assy_start']['value'] = $this->input->post('assy_start', TRUE);
			$data['assy_end']['value'] = $this->input->post('assy_end', TRUE);
			$data['account_currency']['value'] = $this->input->post('account_currency', TRUE);
			$data['account_date']['value'] = $this->input->post('account_date', TRUE);
			$data['account_timezone'] = $this->input->post('account_timezone', TRUE);

			$data['database_name'] = $this->input->post('database_name', TRUE);
			$data['database_username'] = $this->input->post('database_username', TRUE);
			$data['database_password'] = $this->input->post('database_password', TRUE);
			$data['database_host'] = $this->input->post('database_host', TRUE);
			$data['database_port'] = $this->input->post('database_port', TRUE);
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('admin_template', 'admin/create', $data);
		}
		else
		{
			$data_account_name = $this->input->post('account_name');
			$data_account_address = $this->input->post('account_address');
			$data_account_email = $this->input->post('account_email');
			$data_assy_start = date_php_to_mysql($this->input->post('assy_start', TRUE));
			$data_assy_end = date_php_to_mysql($this->input->post('assy_end', TRUE));
			$data_account_currency = $this->input->post('account_currency', TRUE);
			$data_account_date = $this->input->post('account_date', TRUE);
			$data_account_timezone = $this->input->post('timezones', TRUE);

			$data_database_name = $this->input->post('database_name', TRUE);
			$data_database_username = $this->input->post('database_username', TRUE);
			$data_database_password = $this->input->post('database_password', TRUE);
			$data_database_host = $this->input->post('database_host', TRUE);
			$data_database_port = $this->input->post('database_port', TRUE);

			/* Creating database */
		}
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/admin/welcome.php */
