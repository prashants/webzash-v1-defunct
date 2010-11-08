<?php
class Setting extends Controller {

	function Setting()
	{
		parent::Controller();
		$this->load->model('Setting_model');
	}

	function index()
	{
		$this->template->set('page_title', 'Settings');
		$this->template->load('template', 'setting/index');
	}

	function company()
	{
		$this->template->set('page_title', 'Company Settings');
		$company_data = $this->Setting_model->get_current();


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
		$data['company_name'] = array(
			'name' => 'company_name',
			'id' => 'company_name',
			'maxlength' => '100',
			'size' => '40',
			'value' => ($company_data) ? echo_value($company_data->name) : '',
		);
		$data['company_address'] = array(
			'name' => 'company_address',
			'id' => 'company_address',
			'rows' => '4',
			'cols' => '47',
			'value' => ($company_data) ? echo_value($company_data->address) : '',
		);
		$data['company_email'] = array(
			'name' => 'company_email',
			'id' => 'company_email',
			'maxlength' => '100',
			'size' => '40',
			'value' => ($company_data) ? echo_value($company_data->email) : '',
		);
		$data['assy_start'] = array(
			'name' => 'assy_start',
			'id' => 'assy_start',
			'maxlength' => '11',
			'size' => '11',
			'value' => ($company_data) ? echo_value($company_data->ay_start) : $default_start,
		);
		$data['assy_end'] = array(
			'name' => 'assy_end',
			'id' => 'assy_end',
			'maxlength' => '11',
			'size' => '11',
			'value' => ($company_data) ? echo_value($company_data->ay_end) : $default_end,
		);
		$data['company_currency'] = array(
			'name' => 'company_currency',
			'id' => 'company_currency',
			'maxlength' => '10',
			'size' => '10',
			'value' => ($company_data) ? echo_value($company_data->currency_symbol) : '',
		);
		$data['company_date'] = array(
			'name' => 'company_date',
			'id' => 'company_date',
			'maxlength' => '20',
			'size' => '10',
			'value' => ($company_data) ? echo_value($company_data->date_format) : '',
		);
		$data['company_timezone'] = ($company_data) ? echo_value($company_data->timezone) : 'UTC';

		/* Form validations */
		$this->form_validation->set_rules('company_name', 'Company Name', 'trim|required|min_length[2]|max_length[100]');
		$this->form_validation->set_rules('company_address', 'Company Address', 'trim|max_length[255]');
		$this->form_validation->set_rules('company_email', 'Company Email', 'trim|valid_email');
		$this->form_validation->set_rules('assy_start', 'Assessment Year Start', 'trim|required|is_date');
		$this->form_validation->set_rules('assy_end', 'Assessment Year End', 'trim|required|is_date');
		$this->form_validation->set_rules('company_currency', 'Currency', 'trim|max_length[10]');
		$this->form_validation->set_rules('company_date', 'Date', 'trim|max_length[30]');
		$this->form_validation->set_rules('company_timezone', 'Timezone', 'trim|max_length[6]');

		/* Repopulating form */
		if ($_POST)
		{
			$data['company_name']['value'] = $this->input->post('company_name');
			$data['company_address']['value'] = $this->input->post('company_address');
			$data['company_email']['value'] = $this->input->post('company_email');
			$data['assy_start']['value'] = $this->input->post('assy_start', TRUE);
			$data['assy_end']['value'] = $this->input->post('assy_end', TRUE);
			$data['company_currency']['value'] = $this->input->post('company_currency', TRUE);
			$data['company_date']['value'] = $this->input->post('company_date', TRUE);
			$data['company_timezone'] = $this->input->post('company_timezone', TRUE);
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/company', $data);
		}
		else
		{
			$data_company_name = $this->input->post('company_name');
			$data_company_address = $this->input->post('company_address');
			$data_company_email = $this->input->post('company_email');
			$data_assy_start = date_php_to_mysql($this->input->post('assy_start', TRUE));
			$data_assy_end = date_php_to_mysql($this->input->post('assy_end', TRUE));
			$data_company_currency = $this->input->post('company_currency', TRUE);
			$data_company_date = $this->input->post('company_date', TRUE);
			$data_company_timezone = $this->input->post('company_timezone', TRUE);

			/* Verify if current settings exist. If not add new settings */
			$current = $this->Setting_model->get_current();
			if ( ! $current)
			{
				$this->messages->add('Current settings were not valid', 'message');
				if ( ! $this->db->query("INSERT INTO settings (id, name, address, email, ay_start, ay_end, currency_symbol, date_format, timezone) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?)", array($data_company_name, $data_company_address, $data_company_email, $data_assy_start, $data_assy_end, $data_company_currency, $data_company_date, $data_company_timezone)))
				{
					$this->messages->add('Error adding new settings', 'error');
					$this->template->load('template', 'setting/company', $data);
					return;
				}
			}

			/* Update settings */
			if ( ! $this->db->query("UPDATE settings SET name = ?, address = ?, email = ?, ay_start = ?, ay_end = ?, currency_symbol = ?, date_format = ?, timezone = ? WHERE id = 1", array($data_company_name, $data_company_address, $data_company_email, $data_assy_start, $data_assy_end, $data_company_currency, $data_company_date, $data_company_timezone)))
			{
				$this->messages->add('Error updating settings', 'error');
				$this->template->load('template', 'setting/company', $data);
				return;
			}

			/* Success */
			$this->messages->add('Settings updated successfully', 'success');
			redirect('setting');
			return;
		}
	}
}
