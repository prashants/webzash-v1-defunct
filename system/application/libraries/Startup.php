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

		/* Checking for valid database */
		$table_names = array('settings', 'groups', 'ledgers', 'vouchers', 'voucher_items');
		foreach ($table_names as $id => $tbname)
		{
			$valid_db_q = mysql_query('DESC ' . $tbname);
			if ( ! $valid_db_q)
			{
				$CI->messages->add('Invalid Webzash database', 'error');
				redirect('admin');
			}
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
