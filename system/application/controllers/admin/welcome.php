<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'Administer Webzash');

		$data['current_account'] = "";

		/* Reading database settings ini file */
		$is_label_set = FALSE;
		if ($this->session->userdata('db_active_label'))
		{
			$is_label_set = TRUE;

			/* Fetching database label details from session */
			$db_active_label = $this->session->userdata('db_active_label');
			$ini_file = $this->config->item('config_path') . "accounts/" . $db_active_label . ".ini";

			/* Check if database ini file exists */
			if ( ! get_file_info($ini_file))
			{
				$this->messages->add("Account setting file is missing", 'error');
			} else {
				/* Parsing database ini file */
				$active_accounts = parse_ini_file($ini_file);
				if ( ! $active_accounts)
				{
					$this->messages->add("Invalid account setting file", 'error');
				} else {
					/* Check if all needed variables are set in ini file */
					$ini_ok = TRUE;
					if ( ! isset($active_accounts['db_hostname']))
					{
						$ini_ok = FALSE;
						$this->messages->add("Hostname missing from account setting file", 'error');
					}
					if ( ! isset($active_accounts['db_port']))
					{
						$ini_ok = FALSE;
						$this->messages->add("Port missing from account setting file. Default MySQL port is 3306", 'error');
					}
					if ( ! isset($active_accounts['db_name']))
					{
						$ini_ok = FALSE;
						$this->messages->add("Database name missing from account setting file", 'error');
					}
					if ( ! isset($active_accounts['db_username']))
					{
						$ini_ok = FALSE;
						$this->messages->add("Database username missing from account setting file", 'error');
					}
					if ( ! isset($active_accounts['db_password']))
					{
						$ini_ok = FALSE;
						$this->messages->add("Database password missing from account setting file", 'error');
					}

					if ($ini_ok)
					{
						/* Preparing database settings */
						$db_config['hostname'] = $active_accounts['db_hostname'];
						$db_config['hostname'] .= ":" . $active_accounts['db_port'];
						$db_config['database'] = $active_accounts['db_name'];
						$db_config['username'] = $active_accounts['db_username'];
						$db_config['password'] = $active_accounts['db_password'];
						$db_config['dbdriver'] = "mysql";
						$db_config['dbprefix'] = "";
						$db_config['pconnect'] = FALSE;
						$db_config['db_debug'] = FALSE;
						$db_config['cache_on'] = FALSE;
						$db_config['cachedir'] = "";
						$db_config['char_set'] = "utf8";
						$db_config['dbcollat'] = "utf8_general_ci";
						$this->load->database($db_config, FALSE, TRUE);
					}
				}
			}
		} else {
			$is_label_set = FALSE;
			$this->messages->add('Please select a Webzash database', 'error');
		}

		if ($is_label_set)
		{
			/* Checking for valid database connection */
			if ($this->db->conn_id)
			{
				/* Checking for valid database name, username, password */
				if ($this->db->query("SHOW TABLES"))
				{
					$valid_webzash_db = TRUE;
					/* Check for valid webzash database */
					$table_names = array('settings', 'groups', 'ledgers', 'vouchers', 'voucher_items', 'tags', 'logs');
					foreach ($table_names as $id => $tbname)
					{
						$valid_db_q = mysql_query('DESC ' . $tbname);
						if ( ! $valid_db_q)
						{
							$valid_webzash_db = FALSE;
							$this->messages->add('Invalid Webzash database', 'error');
							break;
						}
					}

					/* Loading account data */
					if ($valid_webzash_db)
					{
						$valid_db_q = mysql_query('DESC settings');
						if ($valid_db_q)
						{
							$account_q = $this->db->query('SELECT * FROM settings WHERE id = 1');
							if ($account_d = $account_q->row())
							{
								$this->config->set_item('account_date_format', $account_d->date_format);
								$data['current_account'] .= "Currently active account is ";
								$data['current_account'] .= "<b>" . $account_d->name . "</b>";
								$data['current_account'] .= " from " . "<b>" . date_mysql_to_php_display($account_d->fy_start) . "</b>";
								$data['current_account'] .= " to " . "<b>" . date_mysql_to_php_display($account_d->fy_end) . "</b>";
								$data['current_account'] .= " ( " . anchor('admin/active', 'change active account', array('title' => 'Activate a existing account', 'style' => 'color:#000000')) . " )";
							}
						}
					}
				} else {
					$this->messages->add('Invalid database connection settings. Please check whether the provided database name, username and password is valid', 'error');
				}
			} else {
				$this->messages->add('Cannot connect to database server. Please check whether database server is running', 'error');
			}
		}

		if ($data['current_account'] == "")
			$data['current_account'] = "No account is currently active. You can " . anchor('admin/create', 'create', array('title' => 'Create a new account', 'style' => 'color:#000000')) . " a new account or " . anchor('admin/active', 'activate', array('title' => 'Activate a existing account', 'style' => 'color:#000000')) . " an existing account";

		$this->template->load('admin_template', 'admin/welcome', $data);
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/admin/welcome.php */
