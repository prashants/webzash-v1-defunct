<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Startup:: a class that is loaded everytime the application is accessed
 *
 * Setup all the initialization routines that the application uses in this
 * class. It is autoloaded evertime the application is accessed.
 *
 */

class Startup
{
	function Startup()
	{
		$CI =& get_instance();
		$CI->db->trans_strict(FALSE);

		/* Skip checking if accessing admin section*/
		if ($CI->uri->segment(1) == "admin")
			return;

		/* Reading database settings ini file */
		if ($CI->session->userdata('db_active_label'))
		{
			/* Fetching database label details from session */
			$db_active_label = $CI->session->userdata('db_active_label');
			$ini_file = "system/application/config/accounts/" . $db_active_label . ".ini";

			/* Check if database ini file exists */
			if ( ! get_file_info($ini_file))
			{
				$CI->messages->add("Account setting file is missing", 'error');
				redirect('admin');
				return;
			}

			/* Parsing database ini file */
			$active_accounts = parse_ini_file($ini_file);
			if ( ! $active_accounts)
			{
				$CI->messages->add("Invalid account setting file", 'error');
				redirect('admin');
				return;
			}

			/* Check if all needed variables are set in ini file */
			if ( ! isset($active_accounts['db_hostname']))
			{
				$CI->messages->add("Hostname missing from account setting file", 'error');
				redirect('admin');
				return;
			}
			if ( ! isset($active_accounts['db_port']))
			{
				$CI->messages->add("Port missing from account setting file. Default MySQL port is 3306", 'error');
				redirect('admin');
				return;
			}
			if ( ! isset($active_accounts['db_name']))
			{
				$CI->messages->add("Database name missing from account setting file", 'error');
				redirect('admin');
				return;
			}
			if ( ! isset($active_accounts['db_username']))
			{
				$CI->messages->add("Database username missing from account setting file", 'error');
				redirect('admin');
				return;
			}
			if ( ! isset($active_accounts['db_password']))
			{
				$CI->messages->add("Database password missing from account setting file", 'error');
				redirect('admin');
				return;
			}

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
			$CI->load->database($db_config, FALSE, TRUE);
		} else {
			$CI->messages->add('Please select a Webzash database', 'error');
			redirect('admin');
		}

		/* Checking for valid database connection */
		if ($CI->db->conn_id)
		{
			/* Checking for valid database name, username, password */
			if ($CI->db->query("SHOW TABLES"))
			{
				/* Check for valid webzash database */
				$table_names = array('settings', 'groups', 'ledgers', 'vouchers', 'voucher_items', 'tags');
				foreach ($table_names as $id => $tbname)
				{
					$valid_db_q = mysql_query('DESC ' . $tbname);
					if ( ! $valid_db_q)
					{
						$CI->messages->add('Invalid Webzash database', 'error');
						redirect('admin');
						return;
					}
				}
			} else {
				$CI->messages->add('Invalid database connection settings. Please check whether the provided database name, username and password is valid', 'error');
				redirect('admin');
				return;
			}
		} else {
			$CI->messages->add('Cannot connect to database server. Please check whether database server is running', 'error');
			redirect('admin');
			return;
		}

		/* Loading account data */
		$account_q = $CI->db->query('SELECT * FROM settings WHERE id = 1');
		if ( ! ($account_d = $account_q->row()))
		{
			$CI->messages->add('Please select valid account', 'error');
			redirect('admin');
		}
		$CI->config->set_item('account_name', $account_d->name);
		$CI->config->set_item('account_address', $account_d->address);
		$CI->config->set_item('account_email', $account_d->email);
		$CI->config->set_item('account_ay_start', $account_d->ay_start);
		$CI->config->set_item('account_ay_end', $account_d->ay_end);
		$CI->config->set_item('account_currency_symbol', $account_d->currency_symbol);
		$CI->config->set_item('account_date_format', $account_d->date_format);
		$CI->config->set_item('account_timezone', $account_d->timezone);
		$CI->config->set_item('account_email_protocol', $account_d->email_protocol);
		$CI->config->set_item('account_email_host', $account_d->email_host);
		$CI->config->set_item('account_email_port', $account_d->email_port);
		$CI->config->set_item('account_email_username', $account_d->email_username);
		$CI->config->set_item('account_email_password', $account_d->email_password);
	}
}

/* End of file startup.php */
/* Location: ./system/application/libraries/startup.php */
