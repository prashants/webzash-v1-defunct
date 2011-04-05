<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General {
	var $error_messages = array();

	function General()
	{
		return;
	}

	/* Check format of config/accounts ini files */
	function check_account($account_name)
	{
		$CI =& get_instance();

		$ini_file = $CI->config->item('config_path') . "accounts/" . $account_name . ".ini";

		/* Check if database ini file exists */
		if ( ! get_file_info($ini_file))
		{
			$CI->messages->add('Account settings file is missing.', 'error');
			return FALSE;
		}

		/* Parsing database ini file */
		$account_data = parse_ini_file($ini_file);
		if ( ! $account_data)
		{
			$CI->messages->add('Invalid account settings.', 'error');
			return FALSE;
		}

		/* Check if all needed variables are set in ini file */
		if ( ! isset($account_data['db_hostname']))
		{
			$CI->messages->add('Hostname missing from account settings file.', 'error');
			return FALSE;
		}
		if ( ! isset($account_data['db_port']))
		{
			$CI->messages->add('Port missing from account setting file. Default MySQL port is 3306.', 'error');
			return FALSE;
		}
		if ( ! isset($account_data['db_name']))
		{
			$CI->messages->add('Database name missing from account setting file.', 'error');
			return FALSE;
		}
		if ( ! isset($account_data['db_username']))
		{
			$CI->messages->add('Database username missing from account setting file.', 'error');
			return FALSE;
		}
		if ( ! isset($account_data['db_password']))
		{
			$CI->messages->add('Database password missing from account setting file.', 'error');
			return FALSE;
		}
		return $account_data;
	}

	/* Check for valid account database */
	function check_database()
	{
		$CI =& get_instance();

		/* Checking for valid database connection */
		if ($CI->db->conn_id)
		{
			/* Checking for valid database name, username, password */
			if ($CI->db->query("SHOW TABLES"))
			{
				/* Check for valid webzash database */
				$table_names = array('groups', 'ledgers', 'entry_types', 'entries', 'entry_items', 'inventory_units', 'inventory_groups', 'inventory_items', 'inventory_entry_items', 'tags', 'logs', 'settings');
				foreach ($table_names as $id => $tbname)
				{
					$valid_db_q = mysql_query('DESC ' . $tbname);
					if ( ! $valid_db_q)
					{
						$CI->messages->add('Invalid account database. Table "' . $tbname . '" missing.', 'error');
						return FALSE;
					}
				}
			} else {
				$CI->messages->add('Invalid database connection settings. Check whether the provided database name, username and password are valid.', 'error');
				return FALSE;
			}
		} else {
			$CI->messages->add('Cannot connect to database server. Check whether database server is running.', 'error');
			return FALSE;
		}
		return TRUE;
	}

	/* Check config/settings/general.ini file */
	function check_setting()
	{
		$CI =& get_instance();

		$setting_ini_file = $CI->config->item('config_path') . "settings/general.ini";

		/* Set default values */
		$CI->config->set_item('row_count', "20");
		$CI->config->set_item('log', "1");

		/* Check if general application settings ini file exists */
		if (get_file_info($setting_ini_file))
		{
			/* Parsing general application settings ini file */
			$cur_setting = parse_ini_file($setting_ini_file);
			if ($cur_setting)
			{
				if (isset($cur_setting['row_count']))
				{
					$CI->config->set_item('row_count', $cur_setting['row_count']);
				}
				if (isset($cur_setting['log']))
				{
					$CI->config->set_item('log', $cur_setting['log']);
				}
			}
		}
	}

	function check_user($user_name)
	{
		$CI =& get_instance();

		/* User validation */
		$ini_file = $CI->config->item('config_path') . "users/" . $user_name . ".ini";

		/* Check if user ini file exists */
		if ( ! get_file_info($ini_file))
		{
			$CI->messages->add('User does not exists.', 'error');
			return FALSE;
		}

		/* Parsing user ini file */
		$user_data = parse_ini_file($ini_file);
		if ( ! $user_data)
		{
			$CI->messages->add('Invalid user file.', 'error');
			return FALSE;
		}

		if ( ! isset($user_data['username']))
		{
			$CI->messages->add('Username missing from user file.', 'error');
			return FALSE;
		}
		if ( ! isset($user_data['password']))
		{
			$CI->messages->add('Password missing from user file.', 'error');
			return FALSE;
		}
		if ( ! isset($user_data['status']))
		{
			$CI->messages->add('Status missing from user file.', 'error');
			return FALSE;
		}
		if ( ! isset($user_data['role']))
		{
			$CI->messages->add('Role missing from user file. Defaulting to "guest" role.', 'error');
			$user_data['role'] = 'guest';
		}
		if ( ! isset($user_data['accounts']))
		{
			$CI->messages->add('Accounts missing from user file.', 'error');
		}
		return $user_data;
	}

	function setup_entry_types()
	{
		$CI =& get_instance();

		$CI->db->from('entry_types')->order_by('id', 'asc');
		$entry_types = $CI->db->get();
		if ($entry_types->num_rows() < 1)
		{
			$CI->messages->add('You need to create a Entry Type before you can create any Entry.', 'error');
		}
		$entry_type_config = array();
		foreach ($entry_types->result() as $id => $row)
		{
			$entry_type_config[$row->id] = array(
				'label' => $row->label,
				'name' => $row->name,
				'description' => $row->description,
				'base_type' => $row->base_type,
				'bank_cash_ledger_restriction' => $row->bank_cash_ledger_restriction,
				'inventory_entry_type' => $row->inventory_entry_type,
				'numbering' => $row->numbering,
				'prefix' => $row->prefix,
				'suffix' => $row->suffix,
				'zero_padding' => $row->zero_padding,
			);
		}
		$CI->config->set_item('account_entry_types', $entry_type_config);
	}
}

/* End of file General.php */
/* Location: ./system/application/libraries/General.php */
