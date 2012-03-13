<?php

class Manage extends Controller {

	function Manage()
	{
		parent::Controller();

		/* Check access */
		if ( ! check_access('administer'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		return;
	}
	
	function index()
	{
		$this->load->helper('file');
		$this->template->set('page_title', 'Manage accounts');
		$this->template->set('nav_links', array('admin/manage/add' => 'New account'));

		/* Getting list of files in the config - accounts directory */
		$accounts_list = get_filenames($this->config->item('config_path') . 'accounts');
		$data['accounts'] = array();
		if ($accounts_list)
		{
			foreach ($accounts_list as $row)
			{
				/* Only include file ending with .ini */
				if (substr($row, -4) == ".ini")
				{
					$ini_label = substr($row, 0, -4);
					$data['accounts'][$ini_label] = $ini_label;
				}
			}
		}

		$this->template->load('admin_template', 'admin/manage/index', $data);
		return;
	}

	function add()
	{
		$this->template->set('page_title', 'Add account');

		/* Form fields */
		$data['database_label'] = array(
			'name' => 'database_label',
			'id' => 'database_label',
			'maxlength' => '30',
			'size' => '30',
			'value' => '',
		);

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
			'value' => 'localhost',
		);

		$data['database_port'] = array(
			'name' => 'database_port',
			'id' => 'database_port',
			'maxlength' => '100',
			'size' => '40',
			'value' => '3306',
		);

		/* Repopulating form */
		if ($_POST)
		{
			$data['database_label']['value'] = $this->input->post('database_label', TRUE);
			$data['database_name']['value'] = $this->input->post('database_name', TRUE);
			$data['database_username']['value'] = $this->input->post('database_username', TRUE);
			$data['database_password']['value'] = $this->input->post('database_password', TRUE);
			$data['database_host']['value'] = $this->input->post('database_host', TRUE);
			$data['database_port']['value'] = $this->input->post('database_port', TRUE);
		}

		/* Form validations */
		$this->form_validation->set_rules('database_label', 'Label', 'trim|required|min_length[2]|max_length[30]|alpha_numeric');
		$this->form_validation->set_rules('database_name', 'Database Name', 'trim|required');

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('admin_template', 'admin/manage/add', $data);
			return;
		}
		else
		{
			$data_database_label = $this->input->post('database_label', TRUE);
			$data_database_label = strtolower($data_database_label);
			$data_database_type = 'mysql';
			$data_database_host = $this->input->post('database_host', TRUE);
			$data_database_port = $this->input->post('database_port', TRUE);
			$data_database_name = $this->input->post('database_name', TRUE);
			$data_database_username = $this->input->post('database_username', TRUE);
			$data_database_password = $this->input->post('database_password', TRUE);

			$ini_file = $this->config->item('config_path') . "accounts/" . $data_database_label . ".ini";

			/* Check if database ini file exists */
			if (get_file_info($ini_file))
			{
				$this->messages->add('Account with same label already exists.', 'error');
				$this->template->load('admin_template', 'admin/manage/add', $data);
				return;
			}

			$con_details = "[database]" . "\r\n" . "db_type = \"" . $data_database_type . "\"" . "\r\n" . "db_hostname = \"" . $data_database_host . "\"" . "\r\n" . "db_port = \"" . $data_database_port . "\"" . "\r\n" . "db_name = \"" . $data_database_name . "\"" . "\r\n" . "db_username = \"" . $data_database_username . "\"" . "\r\n" . "db_password = \"" . $data_database_password . "\"" . "\r\n";

			$con_details_html = '[database]' . '<br />db_type = "' . $data_database_type . '"<br />db_hostname = "' . $data_database_host . '"<br />db_port = "' . $data_database_port . '"<br />db_name = "' . $data_database_name . '"<br />db_username = "' . $data_database_username . '"<br />db_password = "' . $data_database_password . '"<br />';

			/* Writing the connection string to end of file - writing in 'a' append mode */
			if ( ! write_file($ini_file, $con_details))
			{
				$this->messages->add('Failed to add account settings file. Check if "' . $ini_file . '" file is writable.', 'error');
				$this->messages->add('You can manually create a text file "' . $ini_file . '" with the following content :<br /><br />' . $con_details_html, 'error');
				$this->template->load('admin_template', 'admin/manage/add', $data);
				return;
			} else {
				$this->messages->add('Added account to list of active accounts.', 'success');
				redirect('admin/manage');
				return;
			}
		}
		return;
	}
	
	function edit($database_label)
	{
		$this->template->set('page_title', 'Edit account');

		$ini_file = $this->config->item('config_path') . "accounts/" . $database_label . ".ini";

		/* Form fields */
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
			'value' => '',
		);

		$data['database_port'] = array(
			'name' => 'database_port',
			'id' => 'database_port',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['database_label'] = $database_label;

		/* Repopulating form */
		if ($_POST)
		{
			$data['database_host']['value'] = $this->input->post('database_host', TRUE);
			$data['database_port']['value'] = $this->input->post('database_port', TRUE);
			$data['database_name']['value'] = $this->input->post('database_name', TRUE);
			$data['database_username']['value'] = $this->input->post('database_username', TRUE);
			$data['database_password']['value'] = $this->input->post('database_password', TRUE);
		} else {
			/* Check if database ini file exists */
			if ( ! get_file_info($ini_file))
			{
				$this->messages->add('Account settings file labeled ' . $database_label . ' does not exists.', 'error');
				redirect('admin/manage');
				return;
			} else {
				/* Parsing database ini file */
				$active_accounts = parse_ini_file($ini_file);
				if ( ! $active_accounts)
				{
					$CI->messages->add('Invalid account settings file', 'error');
				} else {
					/* Check if all needed variables are set in ini file */
					if (isset($active_accounts['db_hostname']))
						$data['database_host']['value'] = $active_accounts['db_hostname'];
					else
						$CI->messages->add('Hostname missing from account settings file', 'error');
					
					if (isset($active_accounts['db_port']))
						$data['database_port']['value'] = $active_accounts['db_port'];
					else
						$CI->messages->add('Port missing from account settings file. Default MySQL port is 3306', 'error');

					if (isset($active_accounts['db_name']))
						$data['database_name']['value'] = $active_accounts['db_name'];
					else
						$CI->messages->add('Database name missing from account settings file', 'error');

					if (isset($active_accounts['db_username']))
						$data['database_username']['value'] = $active_accounts['db_username'];
					else
						$CI->messages->add('Database username missing from account settings file', 'error');

					if ( ! isset($active_accounts['db_password']))
						$CI->messages->add('Database password missing from account settings file', 'error');
				}
			}
		}

		/* Form validations */
		$this->form_validation->set_rules('database_name', 'Database Name', 'trim|required');

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('admin_template', 'admin/manage/edit', $data);
			return;
		}
		else
		{
			$data_database_type = 'mysql';
			$data_database_host = $this->input->post('database_host', TRUE);
			$data_database_port = $this->input->post('database_port', TRUE);
			$data_database_name = $this->input->post('database_name', TRUE);
			$data_database_username = $this->input->post('database_username', TRUE);
			$data_database_password = $this->input->post('database_password', TRUE);

			$ini_file = $this->config->item('config_path') . "accounts/" . $database_label . ".ini";

			$con_details = "[database]" . "\r\n" . "db_type = \"" . $data_database_type . "\"" . "\r\n" . "db_hostname = \"" . $data_database_host . "\"" . "\r\n" . "db_port = \"" . $data_database_port . "\"" . "\r\n" . "db_name = \"" . $data_database_name . "\"" . "\r\n" . "db_username = \"" . $data_database_username . "\"" . "\r\n" . "db_password = \"" . $data_database_password . "\"" . "\r\n";

			$con_details_html = '[database]' . '<br />db_type = "' . $data_database_type . '"<br />db_hostname = "' . $data_database_host . '"<br />db_port = "' . $data_database_port . '"<br />db_name = "' . $data_database_name . '"<br />db_username = "' . $data_database_username . '"<br />db_password = "' . $data_database_password . '"<br />';

			/* Writing the connection string to end of file - writing in 'a' append mode */
			if ( ! write_file($ini_file, $con_details))
			{
				$this->messages->add('Failed to edit account settings file. Check if "' . $ini_file . '" file is writable.', 'error');
				$this->messages->add('You can manually update the text file "' . $ini_file . '" with the following content :<br /><br />' . $con_details_html, 'error');
				$this->template->load('admin_template', 'admin/manage/edit', $data);
				return;
			} else {
				$this->messages->add('Updated account settings.', 'success');
				redirect('admin/manage');
				return;
			}
		}
		return;
	}

	function delete($database_label)
	{
		$this->template->set('page_title', 'Delete account');

		$ini_file = $this->config->item('config_path') . "accounts/" . $database_label . ".ini";
		$this->messages->add('Delete ' . $ini_file . ' file manually.', 'error');
		$this->messages->add('Only the settings file will be delete. Account database will have to be deleted manually.', 'status');
		redirect('admin/manage');
		return;
	}
}

/* End of file manage.php */
/* Location: ./system/application/controllers/admin/manage.php */
