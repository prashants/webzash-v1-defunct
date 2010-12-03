<?php
class Setting extends Controller {

	function Setting()
	{
		parent::Controller();
		$this->load->model('Setting_model');
		return;
	}

	function index()
	{
		$this->template->set('page_title', 'Settings');
		$this->template->load('template', 'setting/index');
		return;
	}

	function account()
	{
		$this->template->set('page_title', 'Account Settings');
		$account_data = $this->Setting_model->get_current();

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
			'value' => ($account_data) ? echo_value($account_data->name) : '',
		);
		$data['account_address'] = array(
			'name' => 'account_address',
			'id' => 'account_address',
			'rows' => '4',
			'cols' => '47',
			'value' => ($account_data) ? echo_value($account_data->address) : '',
		);
		$data['account_email'] = array(
			'name' => 'account_email',
			'id' => 'account_email',
			'maxlength' => '100',
			'size' => '40',
			'value' => ($account_data) ? echo_value($account_data->email) : '',
		);
		$data['assy_start'] = array(
			'name' => 'assy_start',
			'id' => 'assy_start',
			'maxlength' => '11',
			'size' => '11',
			'value' => ($account_data) ? date_mysql_to_php(echo_value($account_data->ay_start)) : $default_start,
		);
		$data['assy_end'] = array(
			'name' => 'assy_end',
			'id' => 'assy_end',
			'maxlength' => '11',
			'size' => '11',
			'value' => ($account_data) ? date_mysql_to_php(echo_value($account_data->ay_end)) : $default_end,
		);
		$data['account_currency'] = array(
			'name' => 'account_currency',
			'id' => 'account_currency',
			'maxlength' => '10',
			'size' => '10',
			'value' => ($account_data) ? echo_value($account_data->currency_symbol) : '',
		);
		$data['account_date'] = array(
			'name' => 'account_date',
			'id' => 'account_date',
			'maxlength' => '20',
			'size' => '10',
			'value' => ($account_data) ? echo_value($account_data->date_format) : '',
		);
		$data['account_timezone'] = ($account_data) ? echo_value($account_data->timezone) : 'UTC';

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
			$data['account_name']['value'] = $this->input->post('account_name', TRUE);
			$data['account_address']['value'] = $this->input->post('account_address', TRUE);
			$data['account_email']['value'] = $this->input->post('account_email', TRUE);
			$data['assy_start']['value'] = $this->input->post('assy_start', TRUE);
			$data['assy_end']['value'] = $this->input->post('assy_end', TRUE);
			$data['account_currency']['value'] = $this->input->post('account_currency', TRUE);
			$data['account_date']['value'] = $this->input->post('account_date', TRUE);
			$data['account_timezone'] = $this->input->post('account_timezone', TRUE);
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/account', $data);
			return;
		}
		else
		{
			$data_account_name = $this->input->post('account_name', TRUE);
			$data_account_address = $this->input->post('account_address', TRUE);
			$data_account_email = $this->input->post('account_email', TRUE);
			$data_assy_start = date_php_to_mysql($this->input->post('assy_start', TRUE));
			$data_assy_end = date_php_to_mysql($this->input->post('assy_end', TRUE));
			$data_account_currency = $this->input->post('account_currency', TRUE);
			$data_account_date = $this->input->post('account_date', TRUE);
			$data_account_timezone = $this->input->post('timezones', TRUE);

			/* Verify if current settings exist. If not add new settings */
			$current = $this->Setting_model->get_current();
			if ( ! $current)
			{
				$this->messages->add('Current settings were not valid', 'message');
				if ( ! $this->db->query("INSERT INTO settings (id, name, address, email, ay_start, ay_end, currency_symbol, date_format, timezone) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?)", array($data_account_name, $data_account_address, $data_account_email, $data_assy_start, $data_assy_end, $data_account_currency, $data_account_date, $data_account_timezone)))
				{
					$this->messages->add('Error adding new settings', 'error');
					$this->template->load('template', 'setting/account', $data);
					return;
				}
			}

			/* Update settings */
			if ( ! $this->db->query("UPDATE settings SET name = ?, address = ?, email = ?, ay_start = ?, ay_end = ?, currency_symbol = ?, date_format = ?, timezone = ? WHERE id = 1", array($data_account_name, $data_account_address, $data_account_email, $data_assy_start, $data_assy_end, $data_account_currency, $data_account_date, $data_account_timezone)))
			{
				$this->messages->add('Error updating settings', 'error');
				$this->template->load('template', 'setting/account', $data);
				return;
			}

			/* Success */
			$this->messages->add('Settings updated successfully', 'success');
			redirect('setting');
			return;
		}
		return;
	}

	function cf()
	{
		$this->load->helper('file');
		$this->load->model('Ledger_model');
		$this->template->set('page_title', 'Carry forward account');

		/* Form fields */
		$default_start_str = $this->config->item('account_ay_end');
		$default_start_year = date('Y', strtotime($default_start_str));
		$default_start = date('d/m/Y', strtotime($default_start_str));

		$default_end_year = $default_start_year + 1;
		$default_end = '31/03/' . $default_end_year;

		/* Form fields */
		$data['account_label'] = array(
			'name' => 'account_label',
			'id' => 'account_label',
			'maxlength' => '30',
			'size' => '30',
			'value' => '',
		);
		$data['account_name'] = array(
			'name' => 'account_name',
			'id' => 'account_name',
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
		$data['create_database'] = FALSE;
		$data['account_name']['value'] = $this->config->item('account_name');

		/* Form validations */
		$this->form_validation->set_rules('account_label', 'C/F Label', 'trim|required|min_length[2]|max_length[30]|alpha_numeric');
		$this->form_validation->set_rules('account_name', 'C/F Account Name', 'trim|required|min_length[2]|max_length[100]');
		$this->form_validation->set_rules('assy_start', 'C/F Assessment Year Start', 'trim|required|is_date');
		$this->form_validation->set_rules('assy_end', 'C/F Assessment Year End', 'trim|required|is_date');

		$this->form_validation->set_rules('database_name', 'Database Name', 'trim|required');
		$this->form_validation->set_rules('database_username', 'Database Username', 'trim|required');

		/* Repopulating form */
		if ($_POST)
		{
			$data['account_label']['value'] = $this->input->post('account_label', TRUE);
			$data['account_name']['value'] = $this->input->post('account_name', TRUE);
			$data['assy_start']['value'] = $this->input->post('assy_start', TRUE);
			$data['assy_end']['value'] = $this->input->post('assy_end', TRUE);

			$data['create_database'] = $this->input->post('create_database', TRUE);
			$data['database_name']['value'] = $this->input->post('database_name', TRUE);
			$data['database_username']['value'] = $this->input->post('database_username', TRUE);
			$data['database_password']['value'] = $this->input->post('database_password', TRUE);
			$data['database_host']['value'] = $this->input->post('database_host', TRUE);
			$data['database_port']['value'] = $this->input->post('database_port', TRUE);
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/cf', $data);
			return;
		}
		else
		{
			$data_account_label = $this->input->post('account_label', TRUE);
			$data_account_label = strtolower($data_account_label);
			$data_account_name = $this->input->post('account_name', TRUE);
			$data_account_address = $this->config->item('account_address');
			$data_account_email = $this->config->item('account_email');
			$data_assy_start = date_php_to_mysql($this->input->post('assy_start', TRUE));
			$data_assy_end = date_php_to_mysql($this->input->post('assy_end', TRUE));
			$data_account_currency = $this->config->item('account_currency_symbol');
			$data_account_date = $this->config->item('account_date_format');
			$data_account_timezone = $this->config->item('account_timezone');
			$data_account_email_protocol = $this->config->item('account_email_protocol');
			$data_account_email_host = $this->config->item('account_email_host');
			$data_account_email_port = $this->config->item('account_email_port');
			$data_account_email_username = $this->config->item('account_email_username');
			$data_account_email_password = $this->config->item('account_email_password');

			$data_database_host = $this->input->post('database_host', TRUE);
			$data_database_port = $this->input->post('database_port', TRUE);
			$data_database_name = $this->input->post('database_name', TRUE);
			$data_database_username = $this->input->post('database_username', TRUE);
			$data_database_password = $this->input->post('database_password', TRUE);

			$ini_file = "system/application/config/accounts/" . $data_account_label . ".ini";

			/* Check if database ini file exists */
			if (get_file_info($ini_file))
			{
				$this->messages->add("Account with same label already exists", 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			}

			if ($data_database_host == "")
				$data_database_host = "localhost";
			if ($data_database_port == "")
				$data_database_port = "3306";

			/* Setting database */
			$dsn = "mysql://${data_database_username}:${data_database_password}@${data_database_host}:${data_database_port}/${data_database_name}";
			$newacc = $this->load->database($dsn, TRUE);
			$conn_error = $newacc->_error_message();

			/* Creating database if it does not exist */
			if ($this->input->post('create_database', TRUE) == "1")
			{
				if ((substr($conn_error, 0, 16) == "Unknown database"))
				{
					if ($newacc->query("CREATE DATABASE " . $data_database_name))
					{
						$this->messages->add("New database created", 'success');
						/* Retrying to connect to new database */
						$newacc = $this->load->database($dsn, TRUE);
						$conn_error = $newacc->_error_message();
					} else {
						$this->messages->add("Cannot create database", 'error');
						$this->template->load('template', 'setting/cf', $data);
						return;
					}
				}
			}

			if ( ! $newacc->conn_id)
			{
				$this->messages->add("Cannot connecting to database", 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			}  else if ($conn_error != "") {
				$this->messages->add("Error connecting to database. " . $newacc->_error_message(), 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			} else if ($newacc->query("SHOW TABLES")->num_rows() > 0) {
				$this->messages->add("Selected database in not empty", 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			} else {
				/* Executing the database setup script */
				$setup_account = read_file('system/application/controllers/admin/carryforward.sql');
				$setup_account_array = explode(";", $setup_account);
				foreach($setup_account_array as $row)
				{
					if (strlen($row) < 5)
						continue;
					$newacc->query($row);
					if ($newacc->_error_message() != "")
						$this->messages->add($newacc->_error_message(), 'error');
				}

				/* Adding the account settings */
				$newacc->query("INSERT INTO settings (id, label, name, address, email, ay_start, ay_end, currency_symbol, date_format, timezone, email_protocol, email_host, email_port, email_username, email_password,  database_version) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array(1, "", $data_account_name, $data_account_address, $data_account_email, $data_assy_start, $data_assy_end, $data_account_currency, $data_account_date, $data_account_timezone, $data_account_email_protocol, $data_account_email_host, $data_account_email_port, $data_account_email_username, $data_account_email_password, 1));

				$this->messages->add("Successfully created webzash account", 'success');

				/* Adding account settings to file. Code copied from manage controller */
				$con_details = "[database]" . "\r\n" . "db_hostname = \"" . $data_database_host . "\"" . "\r\n" . "db_port = \"" . $data_database_port . "\"" . "\r\n" . "db_name = \"" . $data_database_name . "\"" . "\r\n" . "db_username = \"" . $data_database_username . "\"" . "\r\n" . "db_password = \"" . $data_database_password . "\"" . "\r\n";

				$con_details_html = '[database]<br />db_hostname = "' . $data_database_host . '"<br />db_port = "' . $data_database_port . '"<br />db_name = "' . $data_database_name . '"<br />db_username = "' . $data_database_username . '"<br />db_password = "' . $data_database_password . '"<br />';

				/**************** Importing the C/F Values : START ***************/

				$cf_status = TRUE;
				/* Importing Groups */
				$group_q = $this->db->query("SELECT * FROM groups ORDER BY id");
				foreach ($group_q->result() as $row)
				{
					if ( ! $newacc->query("INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (?, ?, ?, ?)", array($row->id, $row->parent_id, $row->name, $row->affects_gross)))
					{
						$this->messages->add("Failed to add group " . $row->name, 'error');
						$cf_status = FALSE;
					}
				}

				/* Importing Ledgers */
				$ledger_q = $this->db->query("SELECT * FROM ledgers ORDER BY id");
				foreach ($ledger_q->result() as $row)
				{
					/* Calculating closing balance for previous year */
					$cl_balance = $this->Ledger_model->get_ledger_balance($row->id);
					if ($cl_balance < 0)
					{
						$op_balance = -$cl_balance;
						$op_balance_dc = "C";
					} else {
						$op_balance = $cl_balance;
						$op_balance_dc = "D";
					}

					if ( ! $newacc->query("INSERT INTO ledgers (id, group_id, name, op_balance, op_balance_dc, type) VALUES (?, ?, ?, ?, ?, ?)", array($row->id, $row->group_id, $row->name, $op_balance, $op_balance_dc, $row->type)))
					{
						$this->messages->add("Failed to add ledger " . $row->name, 'error');
						$cf_status = FALSE;
					}
				}

				/* Importing Tags */
				$tag_q = $this->db->query("SELECT * FROM tags ORDER BY id");
				foreach ($tag_q->result() as $row)
				{
					if ( ! $newacc->query("INSERT INTO tags (id, title, color, background) VALUES (?, ?, ?, ?)", array($row->id, $row->title, $row->color, $row->background)))
					{
						$this->messages->add("Failed to add tag " . $row->title, 'error');
						$cf_status = FALSE;
					}
				}

				if ($cf_status)
					$this->messages->add("Successfully carry forward to new account", 'success');
				else
					$this->messages->add("Error in carry forward to new account", 'error');

				/* Writing the connection string to end of file - writing in 'a' append mode */
				if ( ! write_file($ini_file, $con_details))
				{
					$this->messages->add("Failed to add account settings file. Please check if \"" . $ini_file . "\" file is writable", 'error');
					$this->messages->add("You can manually create a text file \"" . $ini_file . "\" with the following content :<br /><br />" . $con_details_html, 'error');
				} else {
					$this->messages->add("Successfully added webzash account settings file to list of active accounts", 'success');
				}

				redirect('setting');
				return;
			}
		}
		return;
	}

	function email()
	{
		$this->template->set('page_title', 'Email Settings');
		$account_data = $this->Setting_model->get_current();

		/* Form fields */
		$data['email_protocol_options'] = array(
			'mail' => 'mail',
			'sendmail' => 'sendmail',
			'smtp' => 'smtp'
		);
		$data['email_host'] = array(
			'name' => 'email_host',
			'id' => 'email_host',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['email_port'] = array(
			'name' => 'email_port',
			'id' => 'email_port',
			'maxlength' => '5',
			'size' => '5',
			'value' => '',
		);
		$data['email_username'] = array(
			'name' => 'email_username',
			'id' => 'email_username',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);
		$data['email_password'] = array(
			'name' => 'email_password',
			'id' => 'email_password',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		if ($account_data)
		{
			$data['email_protocol'] = ($account_data->email_protocol) ? echo_value($account_data->email_protocol) : 'smtp';
			$data['email_host']['value'] = ($account_data->email_host) ? echo_value($account_data->email_host) : '';
			$data['email_port']['value'] = ($account_data->email_port) ? echo_value($account_data->email_port) : '';
			$data['email_username']['value'] = ($account_data->email_username) ? echo_value($account_data->email_username) : '';
		}

		/* Form validations */
		$this->form_validation->set_rules('email_protocol', 'Email Protocol', 'trim|required|min_length[2]|max_length[10]');
		$this->form_validation->set_rules('email_host', 'Mail Server Hostname', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_port', 'Mail Server Port', 'trim|is_natural');
		$this->form_validation->set_rules('email_username', 'Email Username', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_password', 'Email Password', 'trim|max_length[255]');

		/* Repopulating form */
		if ($_POST)
		{
			$data['email_protocol'] = $this->input->post('email_protocol', TRUE);
			$data['email_host']['value'] = $this->input->post('email_host', TRUE);
			$data['email_port']['value'] = $this->input->post('email_port', TRUE);
			$data['email_username']['value'] = $this->input->post('email_username', TRUE);
			$data['email_password']['value'] = $this->input->post('email_password', TRUE);
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/email', $data);
			return;
		}
		else
		{
			$data_email_protocol = $this->input->post('email_protocol', TRUE);
			$data_email_host = $this->input->post('email_host', TRUE);
			$data_email_port = $this->input->post('email_port', TRUE);
			$data_email_username = $this->input->post('email_username', TRUE);
			$data_email_password = $this->input->post('email_password', TRUE);

			/* if password is blank then use the current password */
			if ($data_email_password == "")
				$data_email_password = $account_data->email_password;

			/* Update settings */
			if ( ! $this->db->query("UPDATE settings SET email_protocol = ?, email_host = ?, email_port = ?, email_username = ?, email_password = ? WHERE id = 1", array($data_email_protocol, $data_email_host, $data_email_port, $data_email_username, $data_email_password)))
			{
				$this->messages->add('Error updating settings', 'error');
				$this->template->load('template', 'setting/email', $data);
				return;
			}

			/* Success */
			$this->messages->add('Email settings updated successfully', 'success');
			redirect('setting');
			return;
		}
		return;
	}

	function printer()
	{
		$this->template->set('page_title', 'Printer Settings');
		$account_data = $this->Setting_model->get_current();

		/* Form fields */
		$data['paper_height'] = array(
			'name' => 'paper_height',
			'id' => 'paper_height',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['paper_width'] = array(
			'name' => 'paper_width',
			'id' => 'paper_width',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['margin_top'] = array(
			'name' => 'margin_top',
			'id' => 'margin_top',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['margin_bottom'] = array(
			'name' => 'margin_bottom',
			'id' => 'margin_bottom',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['margin_left'] = array(
			'name' => 'margin_left',
			'id' => 'margin_left',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['margin_right'] = array(
			'name' => 'margin_right',
			'id' => 'margin_right',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['orientation_potrait'] = array(
			'name' => 'orientation',
			'id' => 'orientation_potrait',
			'value' => 'P',
			'checked' => TRUE,
		);
		$data['orientation_landscape'] = array(
			'name' => 'orientation',
			'id' => 'orientation_landscape',
			'value' => 'L',
			'checked' => FALSE,
		);
		$data['output_format_html'] = array(
			'name' => 'output_format',
			'id' => 'output_format_html',
			'value' => 'H',
			'checked' => TRUE,
		);
		$data['output_format_text'] = array(
			'name' => 'output_format',
			'id' => 'output_format_text',
			'value' => 'T',
			'checked' => FALSE,
		);
		$data['page_layout'] = array(
			'name' => 'page_layout',
			'id' => 'page_layout',
			'rows' => 10,
			'cols' => 80,
			'value' => '',
		);
		$data['logo'] = array(
			'name' => 'logo',
			'id' => 'logo',
			'maxlength' => '100',
			'size' => '40',
			'value' => '',
		);

		if ($account_data)
		{
			$data['paper_height']['value'] = ($account_data->print_paper_height) ? echo_value($account_data->print_paper_height) : '';
			$data['paper_width']['value'] = ($account_data->print_paper_width) ? echo_value($account_data->print_paper_width) : '';
			$data['margin_top']['value'] = ($account_data->print_margin_top) ? echo_value($account_data->print_margin_top) : '';
			$data['margin_bottom']['value'] = ($account_data->print_margin_bottom) ? echo_value($account_data->print_margin_bottom) : '';
			$data['margin_left']['value'] = ($account_data->print_margin_left) ? echo_value($account_data->print_margin_left) : '';
			$data['margin_right']['value'] = ($account_data->print_margin_right) ? echo_value($account_data->print_margin_right) : '';
			$data['page_layout']['value'] = ($account_data->print_page_layout) ? echo_value($account_data->print_page_layout) : '';
			$data['logo']['value'] = ($account_data->print_logo) ? echo_value($account_data->print_logo) : '';
			if ($account_data->print_orientation)
			{
				if ($account_data->print_orientation == "P")
				{
					$data['orientation_potrait']['checked'] = TRUE;
					$data['orientation_landscape']['checked'] = FALSE;
				} else {
					$data['orientation_potrait']['checked'] = FALSE;
					$data['orientation_landscape']['checked'] = TRUE;
				}
			}
			if ($account_data->print_page_format)
			{
				if ($account_data->print_page_format == "H")
				{
					$data['output_format_html']['checked'] = TRUE;
					$data['output_format_text']['checked'] = FALSE;
				} else {
					$data['output_format_html']['checked'] = FALSE;
					$data['output_format_text']['checked'] = TRUE;
				}
			}
		}

		/* Form validations */
		$this->form_validation->set_rules('paper_height', 'Paper Height', 'trim|required|numeric');
		$this->form_validation->set_rules('paper_width', 'Paper Width', 'trim|required|numeric');
		$this->form_validation->set_rules('margin_top', 'Top Margin', 'trim|required|numeric');
		$this->form_validation->set_rules('margin_bottom', 'Bottom Margin', 'trim|required|numeric');
		$this->form_validation->set_rules('margin_left', 'Left Margin', 'trim|required|numeric');
		$this->form_validation->set_rules('margin_right', 'Right Margin', 'trim|required|numeric');
		$this->form_validation->set_rules('page_layout', 'Page Layout', 'trim|min_length[2]');
		$this->form_validation->set_rules('logo', 'Logo Path', 'trim');

		/* Repopulating form */
		if ($_POST)
		{
			$data['paper_height']['value'] = $this->input->post('paper_height', TRUE);
			$data['paper_width']['value'] = $this->input->post('paper_width', TRUE);
			$data['margin_top']['value'] = $this->input->post('margin_top', TRUE);
			$data['margin_bottom']['value'] = $this->input->post('margin_bottom', TRUE);
			$data['margin_left']['value'] = $this->input->post('margin_left', TRUE);
			$data['margin_right']['value'] = $this->input->post('margin_right', TRUE);
			$data['page_layout']['value'] = $this->input->post('page_layout', TRUE);
			$data['logo']['value'] = $this->input->post('logo', TRUE);

			$data['orientation'] = $this->input->post('orientation', TRUE);
			if ($data['orientation'] == "P")
			{
				$data['orientation_potrait']['checked'] = TRUE;
				$data['orientation_landscape']['checked'] = FALSE;
			} else {
				$data['orientation_potrait']['checked'] = FALSE;
				$data['orientation_landscape']['checked'] = TRUE;
			}
			$data['output_format'] = $this->input->post('output_format', TRUE);
			if ($data['output_format'] == "H")
			{
				$data['output_format_html']['checked'] = TRUE;
				$data['output_format_text']['checked'] = FALSE;
			} else {
				$data['output_format_html']['checked'] = FALSE;
				$data['output_format_text']['checked'] = TRUE;
			}
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/printer', $data);
			return;
		}
		else
		{
			$data_paper_height = $this->input->post('paper_height', TRUE);
			$data_paper_width = $this->input->post('paper_width', TRUE);
			$data_margin_top = $this->input->post('margin_top', TRUE);
			$data_margin_bottom = $this->input->post('margin_bottom', TRUE);
			$data_margin_left = $this->input->post('margin_left', TRUE);
			$data_margin_right = $this->input->post('margin_right', TRUE);
			$data_page_layout = $this->input->post('page_layout', TRUE);
			$data_logo = $this->input->post('logo', TRUE);

			if ($this->input->post('orientation', TRUE) == "P")
			{
				$data_orientation = "P";
			} else {
				$data_orientation = "L";
			}
			if ($this->input->post('output_format', TRUE) == "H")
			{
				$data_output_format = "H";
			} else {
				$data_output_format = "T";
			}

			/* Update settings */
			if ( ! $this->db->query("UPDATE settings SET print_paper_height = ?, print_paper_width = ?, print_margin_top = ?, print_margin_bottom = ?, print_margin_left = ?, print_margin_right = ?, print_orientation = ?, print_page_format = ?, print_page_layout = ?, print_logo = ? WHERE id = 1", array($data_paper_height, $data_paper_width, $data_margin_top, $data_margin_bottom, $data_margin_left, $data_margin_right, $data_orientation, $data_output_format,  $data_page_layout, $data_logo)))
			{
				$this->messages->add('Error updating printer settings', 'error');
				$this->template->load('template', 'setting/printer');
				return;
			}

			/* Success */
			$this->messages->add('Printer settings updated successfully', 'success');
			redirect('setting');
			return;
		}
		return;
	}
}
