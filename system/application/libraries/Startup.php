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

		/* Fetching database label details from session */
		if ($CI->session->userdata('db_settings'))
		{
			$db_config['hostname'] = $CI->session->userdata('db_hostname');
			$db_config['hostname'] .= ":" . $CI->session->userdata('db_port');
			$db_config['database'] = $CI->session->userdata('db_name');
			$db_config['username'] = $CI->session->userdata('db_username');
			$db_config['password'] = $CI->session->userdata('db_password');
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

		/* Loading company data */
		$company_q = $CI->db->query('SELECT * FROM settings WHERE id = 1');
		if ( ! ($company_d = $company_q->row()))
		{
			$CI->messages->add('Please select valid account', 'error');
			redirect('admin');
		}
		$CI->config->set_item('account_name', $company_d->name);
		$CI->config->set_item('account_address', $company_d->address);
		$CI->config->set_item('account_email', $company_d->email);
		$CI->config->set_item('account_ay_start', $company_d->ay_start);
		$CI->config->set_item('account_ay_end', $company_d->ay_end);
		$CI->config->set_item('account_currency_symbol', $company_d->currency_symbol);
		$CI->config->set_item('account_date_format', $company_d->date_format);
		$CI->config->set_item('account_timezone', $company_d->timezone);
	}
}

/* End of file startup.php */
/* Location: ./system/application/libraries/startup.php */
