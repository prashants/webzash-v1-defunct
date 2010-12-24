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
			$ini_file = $CI->config->item('config_path') . "accounts/" . $db_active_label . ".ini";

			/* Check if database ini file exists */
			if ( ! get_file_info($ini_file))
			{
				$CI->messages->add('Account settings file is missing.', 'error');
				redirect('admin');
				return;
			}

			/* Parsing database ini file */
			$active_accounts = parse_ini_file($ini_file);
			if ( ! $active_accounts)
			{
				$CI->messages->add('Invalid account settings.', 'error');
				redirect('admin');
				return;
			}

			/* Check if all needed variables are set in ini file */
			if ( ! isset($active_accounts['db_hostname']))
			{
				$CI->messages->add('Hostname missing from account settings file.', 'error');
				redirect('admin');
				return;
			}
			if ( ! isset($active_accounts['db_port']))
			{
				$CI->messages->add('Port missing from account setting file. Default MySQL port is 3306.', 'error');
				redirect('admin');
				return;
			}
			if ( ! isset($active_accounts['db_name']))
			{
				$CI->messages->add('Database name missing from account setting file.', 'error');
				redirect('admin');
				return;
			}
			if ( ! isset($active_accounts['db_username']))
			{
				$CI->messages->add('Database username missing from account setting file.', 'error');
				redirect('admin');
				return;
			}
			if ( ! isset($active_accounts['db_password']))
			{
				$CI->messages->add('Database password missing from account setting file.', 'error');
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
			$CI->messages->add('Please select a account.', 'error');
			redirect('admin');
		}

		/* Checking for valid database connection */
		if ($CI->db->conn_id)
		{
			/* Checking for valid database name, username, password */
			if ($CI->db->query("SHOW TABLES"))
			{
				/* Check for valid webzash database */
				$table_names = array('settings', 'groups', 'ledgers', 'vouchers', 'voucher_items', 'tags', 'logs');
				foreach ($table_names as $id => $tbname)
				{
					$valid_db_q = mysql_query('DESC ' . $tbname);
					if ( ! $valid_db_q)
					{
						$CI->messages->add('Invalid account database.', 'error');
						redirect('admin');
						return;
					}
				}
			} else {
				$CI->messages->add('Invalid database connection settings. Check whether the provided database name, username and password are valid.', 'error');
				redirect('admin');
				return;
			}
		} else {
			$CI->messages->add('Cannot connect to database server. Check whether database server is running.', 'error');
			redirect('admin');
			return;
		}

		/* Loading account data */
		$account_q = $CI->db->query('SELECT * FROM settings WHERE id = 1');
		if ( ! ($account_d = $account_q->row()))
		{
			$CI->messages->add('Invalid account details.', 'error');
			redirect('admin');
		}
		$CI->config->set_item('account_name', $account_d->name);
		$CI->config->set_item('account_address', $account_d->address);
		$CI->config->set_item('account_email', $account_d->email);
		$CI->config->set_item('account_fy_start', $account_d->fy_start);
		$CI->config->set_item('account_fy_end', $account_d->fy_end);
		$CI->config->set_item('account_currency_symbol', $account_d->currency_symbol);
		$CI->config->set_item('account_date_format', $account_d->date_format);
		$CI->config->set_item('account_timezone', $account_d->timezone);
		$CI->config->set_item('account_email_protocol', $account_d->email_protocol);
		$CI->config->set_item('account_email_host', $account_d->email_host);
		$CI->config->set_item('account_email_port', $account_d->email_port);
		$CI->config->set_item('account_email_username', $account_d->email_username);
		$CI->config->set_item('account_email_password', $account_d->email_password);
		$CI->config->set_item('account_receipt_prefix', $account_d->receipt_voucher_prefix);
		$CI->config->set_item('account_payment_prefix', $account_d->payment_voucher_prefix);
		$CI->config->set_item('account_contra_prefix', $account_d->contra_voucher_prefix);
		$CI->config->set_item('account_journal_prefix', $account_d->journal_voucher_prefix);

		/************** Load general application settings *************/
		$setting_ini_file = $CI->config->item('config_path') . "settings/general.ini";
		$CI->config->set_item('row_count', 20);

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
			}
		}
		return;
	}
}

/* End of file startup.php */
/* Location: ./system/application/libraries/startup.php */
