<?php

class Setting extends Controller {

	function Setting()
	{
		parent::Controller();
		$this->load->model('Setting_model');

		/* Check access */
		if ( ! check_access('change account settings'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

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

		$data['fy_start'] = '';
		$data['fy_end'] = '';

		$data['account_currency'] = array(
			'name' => 'account_currency',
			'id' => 'account_currency',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['account_date_options'] = array(
			'dd/mm/yyyy' => 'Day / Month / Year',
			'mm/dd/yyyy' => 'Month / Day / Year',
			'yyyy/mm/dd' => 'Year / Month / Day',
		);
		$data['account_date'] = 'dd/mm/yyyy';
		$data['account_timezone'] = 'UTC';
		$data['account_locked'] = FALSE;

		/* Current account settings */
		if ($account_data)
		{
			$data['account_name']['value'] = print_value($account_data->name);
			$data['account_address']['value'] = print_value($account_data->address);
			$data['account_email']['value'] = print_value($account_data->email);
			$data['account_currency']['value'] = print_value($account_data->currency_symbol);
			$data['account_date'] = print_value($account_data->date_format);
			$data['account_timezone'] = print_value($account_data->timezone);
			$data['fy_start'] = date_mysql_to_php(print_value($account_data->fy_start));
			$data['fy_end'] = date_mysql_to_php(print_value($account_data->fy_end));
			$data['account_locked'] = print_value($account_data->account_locked);
		}

		/* Form validations */
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|required|min_length[2]|max_length[100]');
		$this->form_validation->set_rules('account_address', 'Account Address', 'trim|max_length[255]');
		$this->form_validation->set_rules('account_email', 'Account Email', 'trim|valid_email');
		$this->form_validation->set_rules('account_currency', 'Currency', 'trim|max_length[10]');
		$this->form_validation->set_rules('account_date', 'Date', 'trim|max_length[10]');
		$this->form_validation->set_rules('account_timezone', 'Timezone', 'trim|max_length[6]');
		$this->form_validation->set_rules('account_locked', 'Account Locked', 'trim');

		/* Repopulating form */
		if ($_POST)
		{
			$data['account_name']['value'] = $this->input->post('account_name', TRUE);
			$data['account_address']['value'] = $this->input->post('account_address', TRUE);
			$data['account_email']['value'] = $this->input->post('account_email', TRUE);
			$data['account_currency']['value'] = $this->input->post('account_currency', TRUE);
			$data['account_date'] = $this->input->post('account_date', TRUE);
			$data['account_timezone'] = $this->input->post('account_timezone', TRUE);
			$data['account_locked'] = $this->input->post('account_locked', TRUE);
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
			$data_account_currency = $this->input->post('account_currency', TRUE);
			$data_account_date_form = $this->input->post('account_date', TRUE);
			/* Checking for valid format */
			if ($data_account_date_form == "dd/mm/yyyy")
				$data_account_date = "dd/mm/yyyy";
			else if ($data_account_date_form == "mm/dd/yyyy")
				$data_account_date = "mm/dd/yyyy";
			else if ($data_account_date_form == "yyyy/mm/dd")
				$data_account_date = "yyyy/mm/dd";
			else
				$data_account_date = "dd/mm/yyyy";

			$data_account_timezone = $this->input->post('timezones', TRUE);

			$data_account_locked = $this->input->post('account_locked', TRUE);
			if ($data_account_locked != 1)
				$data_account_locked = 0;

			/* Update settings */
			$this->db->trans_start();
			$update_data = array(
				'name' => $data_account_name,
				'address' => $data_account_address,
				'email' => $data_account_email,
				'currency_symbol' => $data_account_currency,
				'date_format' => $data_account_date,
				'timezone' => $data_account_timezone,
				'account_locked' => $data_account_locked,
			);
			if ( ! $this->db->where('id', 1)->update('settings', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating account settings.', 'error');
				$this->logger->write_message("error", "Error updating account settings");
				$this->template->load('template', 'setting/account', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Account settings updated.', 'success');
				$this->logger->write_message("success", "Updated account settings");
				redirect('setting');
				return;
			}
		}
		return;
	}

	function cf()
	{
		$this->load->helper('file');
		$this->load->library('accountlist');
		$this->load->model('Ledger_model');
		$this->load->model('Setting_model');
		$this->template->set('page_title', 'Carry forward account');

		/* Check access */
		if ( ! check_access('cf account'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('setting');
			return;
		}

		/* Current settings */
		$account_data = $this->Setting_model->get_current();

		/* Form fields */
		$last_year_end = $this->config->item('account_fy_end');
		list($last_year_end_date, $last_year_end_time) = explode(' ', $last_year_end);
		list($last_year_end_year, $last_year_end_month, $last_year_end_day) = explode('-', $last_year_end_date);
		$last_year_end_ts = strtotime($last_year_end);
		$default_start_ts = $last_year_end_ts + (60 * 60 * 24); /* Adding 24 hours */
		$default_start = date("Y-m-d 00:00:00", $default_start_ts);
		$default_end = ($last_year_end_year + 1) . "-" . $last_year_end_month . "-" . $last_year_end_day . " 00:00:00"; 

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
		$data['fy_start'] = array(
			'name' => 'fy_start',
			'id' => 'fy_start',
			'maxlength' => '11',
			'size' => '11',
			'value' => date_mysql_to_php($default_start),
		);
		$data['fy_end'] = array(
			'name' => 'fy_end',
			'id' => 'fy_end',
			'maxlength' => '11',
			'size' => '11',
			'value' => date_mysql_to_php($default_end),
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
		$this->form_validation->set_rules('fy_start', 'C/F Financial Year Start', 'trim|required|is_date');
		$this->form_validation->set_rules('fy_end', 'C/F Financial Year End', 'trim|required|is_date');

		$this->form_validation->set_rules('database_name', 'Database Name', 'trim|required');
		$this->form_validation->set_rules('database_username', 'Database Username', 'trim|required');

		/* Repopulating form */
		if ($_POST)
		{
			$data['account_label']['value'] = $this->input->post('account_label', TRUE);
			$data['account_name']['value'] = $this->input->post('account_name', TRUE);
			$data['fy_start']['value'] = $this->input->post('fy_start', TRUE);
			$data['fy_end']['value'] = $this->input->post('fy_end', TRUE);

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
			$data_fy_start = date_php_to_mysql($this->input->post('fy_start', TRUE));
			$data_fy_end = date_php_to_mysql_end_time($this->input->post('fy_end', TRUE));
			$data_account_currency = $this->config->item('account_currency_symbol');
			$data_account_date = $this->config->item('account_date_format');
			$data_account_timezone = $this->config->item('account_timezone');

			$data_account_email_protocol = $account_data->email_protocol;
			$data_account_email_host = $account_data->email_host;
			$data_account_email_port = $account_data->email_port;
			$data_account_email_username = $account_data->email_username;
			$data_account_email_password = $account_data->email_password;

			$data_database_host = $this->input->post('database_host', TRUE);
			$data_database_port = $this->input->post('database_port', TRUE);
			$data_database_name = $this->input->post('database_name', TRUE);
			$data_database_username = $this->input->post('database_username', TRUE);
			$data_database_password = $this->input->post('database_password', TRUE);

			$ini_file = $this->config->item('config_path') . "accounts/" . $data_account_label . ".ini";

			/* Check if database ini file exists */
			if (get_file_info($ini_file))
			{
				$this->messages->add('Account with same label already exists.', 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			}

			/* Check if start date is less than end date */
			if ($data_fy_end <= $data_fy_start)
			{
				$this->messages->add('Financial start date cannot be greater than end date.', 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			}

			if ($data_database_host == "")
				$data_database_host = "localhost";
			if ($data_database_port == "")
				$data_database_port = "3306";

			/* Creating account database */
			if ($this->input->post('create_database', TRUE) == "1")
			{
				$new_link = @mysql_connect($data_database_host . ':' . $data_database_port, $data_database_username, $data_database_password);
				if ($new_link)
				{
					/* Check if database already exists */
					$db_selected = mysql_select_db($data_database_name, $new_link);
					if ($db_selected) {
						mysql_close($new_link);
						$this->messages->add('Database already exists.', 'error');
						$this->template->load('template', 'setting/cf', $data);
						return;
					}

					/* Creating account database */
					$db_create_q = 'CREATE DATABASE ' . mysql_real_escape_string($data_database_name);
					if (mysql_query($db_create_q, $new_link))
					{
						$this->messages->add('Created account database.', 'success');
					} else {
						$this->messages->add('Error creating account database. ' . mysql_error(), 'error');
						$this->template->load('template', 'setting/cf', $data);
						return;
					}
					mysql_close($new_link);
				} else {
					$this->messages->add('Error connecting to database. ' . mysql_error(), 'error');
					$this->template->load('template', 'setting/cf', $data);
					return;
				}
			}

			/* Setting database */
			$dsn = "mysql://${data_database_username}:${data_database_password}@${data_database_host}:${data_database_port}/${data_database_name}";
			$newacc = $this->load->database($dsn, TRUE);

			if ( ! $newacc->conn_id)
			{
				$this->messages->add('Error connecting to database.', 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			}  else if ($newacc->_error_message() != "") {
				$this->messages->add('Error connecting to database. ' . $newacc->_error_message(), 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			} else if ($newacc->query("SHOW TABLES")->num_rows() > 0) {
				$this->messages->add('Selected database in not empty.', 'error');
				$this->template->load('template', 'setting/cf', $data);
				return;
			} else {
				/* Executing the database setup script */
				$setup_account = read_file('system/application/controllers/admin/schema.sql');
				$setup_account_array = explode(";", $setup_account);
				foreach($setup_account_array as $row)
				{
					if (strlen($row) < 5)
						continue;
					$newacc->query($row);
					if ($newacc->_error_message() != "")
					{
						$this->messages->add('Error initializing account database.', 'error');
						$this->template->load('template', 'setting/cf', $data);
						return;
					}
				}
				$this->messages->add('Initialized account database.', 'success');

				/* Adding account settings */
				$newacc->trans_start();
				if ( ! $newacc->query("INSERT INTO settings (id, name, address, email, fy_start, fy_end, currency_symbol, date_format, timezone, account_locked, email_protocol, email_host, email_port, email_username, email_password, database_version) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array(1, $data_account_name, $data_account_address, $data_account_email, $data_fy_start, $data_fy_end, $data_account_currency, $data_account_date, $data_account_timezone, 0, $data_account_email_protocol, $data_account_email_host, $data_account_email_port, $data_account_email_username, $data_account_email_password, 2)))
				{
					$newacc->trans_rollback();
					$this->messages->add('Error adding account settings.', 'error');
					$this->template->load('template', 'setting/cf', $data);
					return;
				} else {
					$newacc->trans_complete();
					$this->messages->add('Added account settings.', 'success');
				}

				/**************** Importing the C/F Values : START ***************/

				$cf_status = TRUE;
				/* Importing Groups */
				$this->db->from('groups')->order_by('id', 'asc');
				$group_q = $this->db->get();
				foreach ($group_q->result() as $row)
				{
					if ( ! $newacc->query("INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (?, ?, ?, ?)", array($row->id, $row->parent_id, $row->name, $row->affects_gross)))
					{
						$this->messages->add('Failed to add Group A/C - ' . $row->name . '.', 'error');
						$cf_status = FALSE;
					}
				}

				/* Only importing Assets and Liability closing balance */
				$assets = new Accountlist();
				$assets->init(1);
				$liability = new Accountlist();
				$liability->init(2);
				$cf_ledgers = array_merge($assets->get_ledger_ids(), $liability->get_ledger_ids());

				/* Importing Ledgers */
				$this->db->from('ledgers')->order_by('id', 'asc');
				$ledger_q = $this->db->get();
				foreach ($ledger_q->result() as $row)
				{
					/* CF only Assets and Liability with Closing Balance */
					if (in_array($row->id, $cf_ledgers))
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
							$this->messages->add('Failed to add Ledger A/C - ' . $row->name . '.', 'error');
							$cf_status = FALSE;
						}
					} else {
						if ( ! $newacc->query("INSERT INTO ledgers (id, group_id, name, op_balance, op_balance_dc, type) VALUES (?, ?, ?, ?, ?, ?)", array($row->id, $row->group_id, $row->name, 0, "D", $row->type)))
						{
							$this->messages->add('Failed to add Ledger A/C - ' . $row->name . '.', 'error');
							$cf_status = FALSE;
						}
					}
				}

				/* Importing Tags */
				$this->db->from('tags')->order_by('id', 'asc');
				$tag_q = $this->db->get();
				foreach ($tag_q->result() as $row)
				{
					if ( ! $newacc->query("INSERT INTO tags (id, title, color, background) VALUES (?, ?, ?, ?)", array($row->id, $row->title, $row->color, $row->background)))
					{
						$this->messages->add('Failed to add Tag - ' . $row->title . '.', 'error');
						$cf_status = FALSE;
					}
				}

				if ($cf_status)
					$this->messages->add('Account carried forward.', 'success');
				else
					$this->messages->add('Error carrying forward to new account.', 'error');


				/* Adding account settings to file. Code copied from manage controller */
				$con_details = "[database]" . "\r\n" . "db_hostname = \"" . $data_database_host . "\"" . "\r\n" . "db_port = \"" . $data_database_port . "\"" . "\r\n" . "db_name = \"" . $data_database_name . "\"" . "\r\n" . "db_username = \"" . $data_database_username . "\"" . "\r\n" . "db_password = \"" . $data_database_password . "\"" . "\r\n";

				$con_details_html = '[database]<br />db_hostname = "' . $data_database_host . '"<br />db_port = "' . $data_database_port . '"<br />db_name = "' . $data_database_name . '"<br />db_username = "' . $data_database_username . '"<br />db_password = "' . $data_database_password . '"<br />';

				/* Writing the connection string to end of file - writing in 'a' append mode */
				if ( ! write_file($ini_file, $con_details))
				{
					$this->messages->add('Failed to add account settings file. Check if "' . $ini_file . '" file is writable.', 'error');
					$this->messages->add('You can manually create a text file "' . $ini_file . '" with the following content :<br /><br />' . $con_details_html, 'error');
				} else {
					$this->messages->add('Added account settings file to list of active accounts.', 'success');
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
			$data['email_protocol'] = ($account_data->email_protocol) ? print_value($account_data->email_protocol) : 'smtp';
			$data['email_host']['value'] = ($account_data->email_host) ? print_value($account_data->email_host) : '';
			$data['email_port']['value'] = ($account_data->email_port) ? print_value($account_data->email_port) : '';
			$data['email_username']['value'] = ($account_data->email_username) ? print_value($account_data->email_username) : '';
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
			$this->db->trans_start();
			$update_data = array(
				'email_protocol' => $data_email_protocol,
				'email_host' => $data_email_host,
				'email_port' => $data_email_port,
				'email_username' => $data_email_username,
				'email_password' => $data_email_password,
			);
			if ( ! $this->db->where('id', 1)->update('settings', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating email settings.', 'error');
				$this->logger->write_message("error", "Error updating email settings");
				$this->template->load('template', 'setting/email', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Email settings updated.', 'success');
				$this->logger->write_message("success", "Updated email settings");
				redirect('setting');
				return;
			}
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

		if ($account_data)
		{
			$data['paper_height']['value'] = ($account_data->print_paper_height) ? print_value($account_data->print_paper_height) : '';
			$data['paper_width']['value'] = ($account_data->print_paper_width) ? print_value($account_data->print_paper_width) : '';
			$data['margin_top']['value'] = ($account_data->print_margin_top) ? print_value($account_data->print_margin_top) : '';
			$data['margin_bottom']['value'] = ($account_data->print_margin_bottom) ? print_value($account_data->print_margin_bottom) : '';
			$data['margin_left']['value'] = ($account_data->print_margin_left) ? print_value($account_data->print_margin_left) : '';
			$data['margin_right']['value'] = ($account_data->print_margin_right) ? print_value($account_data->print_margin_right) : '';
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

		/* Repopulating form */
		if ($_POST)
		{
			$data['paper_height']['value'] = $this->input->post('paper_height', TRUE);
			$data['paper_width']['value'] = $this->input->post('paper_width', TRUE);
			$data['margin_top']['value'] = $this->input->post('margin_top', TRUE);
			$data['margin_bottom']['value'] = $this->input->post('margin_bottom', TRUE);
			$data['margin_left']['value'] = $this->input->post('margin_left', TRUE);
			$data['margin_right']['value'] = $this->input->post('margin_right', TRUE);

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
			$this->db->trans_start();
			$update_data = array(
				'print_paper_height' => $data_paper_height,
				'print_paper_width' => $data_paper_width,
				'print_margin_top' => $data_margin_top,
				'print_margin_bottom' => $data_margin_bottom,
				'print_margin_left' => $data_margin_left,
				'print_margin_right' => $data_margin_right,
				'print_orientation' => $data_orientation,
				'print_page_format' => $data_output_format,
			);
			if ( ! $this->db->where('id', 1)->update('settings', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating printer settings.', 'error');
				$this->logger->write_message("error", "Error updating printer settings");
				$this->template->load('template', 'setting/printer');
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Printer settings updated.', 'success');
				$this->logger->write_message("success", "Updated printer settings");
				redirect('setting');
				return;
			}
		}
		return;
	}

	function backup()
	{
		$this->load->dbutil();
		$this->load->helper('download');

		/* Check access */
		if ( ! check_access('backup account'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('setting');
			return;
		}

		$backup_filename = "backup" . date("dmYHis") . ".gz";

		/* Backup your entire database and assign it to a variable */
		$backup_data =& $this->dbutil->backup();

		/* Write the backup file to server */
		if ( ! write_file($this->config->item('backup_path') . $backup_filename, $backup_data))
		{
			$this->messages->add('Error saving backup file to server.' . ' Check if "' . $this->config->item('backup_path') . '" folder is writable.', 'error');
			redirect('setting');
			return;
		}

		/* Send the file to your desktop */
		force_download($backup_filename, $backup_data);
		$this->logger->write_message("success", "Downloaded account backup");
		redirect('setting');
		return;
	}

	function voucher()
	{
		$this->template->set('page_title', 'Voucher Settings');
		$account_data = $this->Setting_model->get_current();

		$data['receipt_prefix'] = array(
			'name' => 'receipt_prefix',
			'id' => 'receipt_prefix',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['payment_prefix'] = array(
			'name' => 'payment_prefix',
			'id' => 'payment_prefix',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['contra_prefix'] = array(
			'name' => 'contra_prefix',
			'id' => 'contra_prefix',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);
		$data['journal_prefix'] = array(
			'name' => 'journal_prefix',
			'id' => 'journal_prefix',
			'maxlength' => '10',
			'size' => '10',
			'value' => '',
		);

		if ($account_data)
		{
			$data['receipt_prefix']['value'] = $account_data->receipt_voucher_prefix;
			$data['payment_prefix']['value'] = $account_data->payment_voucher_prefix;
			$data['contra_prefix']['value'] = $account_data->contra_voucher_prefix;
			$data['journal_prefix']['value'] = $account_data->journal_voucher_prefix;
		}

		/* Form validations */
		$this->form_validation->set_rules('receipt_prefix', 'Prefix Receipt Vouchers', 'trim');
		$this->form_validation->set_rules('payment_prefix', 'Prefix Payment Vouchers', 'trim');
		$this->form_validation->set_rules('contra_prefix', 'Prefix Contra Vouchers', 'trim');
		$this->form_validation->set_rules('journal_prefix', 'Prefix Journal Vouchers', 'trim');

		/* Repopulating form */
		if ($_POST)
		{
			$data['receipt_prefix']['value'] = $this->input->post('receipt_prefix', TRUE);
			$data['payment_prefix']['value'] = $this->input->post('payment_prefix', TRUE);
			$data['contra_prefix']['value'] = $this->input->post('contra_prefix', TRUE);
			$data['journal_prefix']['value'] = $this->input->post('journal_prefix', TRUE);
		}

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/voucher', $data);
			return;
		} else {
			$data_receipt_prefix = $this->input->post('receipt_prefix', TRUE);
			$data_payment_prefix = $this->input->post('payment_prefix', TRUE);
			$data_contra_prefix = $this->input->post('contra_prefix', TRUE);
			$data_journal_prefix = $this->input->post('journal_prefix', TRUE);

			/* Update settings */
			$this->db->trans_start();
			$update_data = array(
				'receipt_voucher_prefix' => $data_receipt_prefix,
				'payment_voucher_prefix' => $data_payment_prefix,
				'contra_voucher_prefix' => $data_contra_prefix,
				'journal_voucher_prefix' => $data_journal_prefix,
			);
			if ( ! $this->db->where('id', 1)->update('settings', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error updating voucher settings.', 'error');
				$this->logger->write_message("error", "Error updating voucher settings");
				$this->template->load('template', 'setting/voucher');
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Voucher settings updated.', 'success');
				$this->logger->write_message("success", "Updated voucher settings");
				redirect('setting');
				return;
			}
		}
		return;
	}
}

/* End of file setting.php */
/* Location: ./system/application/controllers/setting.php */
