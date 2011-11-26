<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

	function My_Form_validation()
	{
		parent::CI_Form_validation();
		parent::set_error_delimiters('<li>', '</li>');
	}

	/**
	 * Unique
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	function unique($str, $field)
	{
		$CI =& get_instance();
		list ($table, $column) = explode('.', $field, 2);

		$CI->form_validation->set_message('unique', 'The %s that you requested is already in use.');
		$CI->db->from($table)->where($column, $str);
		$dup_query = $CI->db->get();
		if ($dup_query->num_rows() > 0)
			return FALSE;
		else
			return TRUE;
	}

	function uniqueentryno($str, $type)
	{
		$CI =& get_instance();

		$CI->form_validation->set_message('uniqueentryno', 'The %s that you requested is already in use.');

		$CI->db->from('entries')->where('number', $str)->where('entry_type', $type);
		$dup_query = $CI->db->get();
		if ($dup_query->num_rows() > 0)
			return FALSE;
		else
			return TRUE;
	}

	function uniqueentrynowithid($str, $field)
	{
		$CI =& get_instance();

		list ($type, $id) = explode('.', $field, 2);
		$CI->form_validation->set_message('uniqueentrynowithid', 'The %s that you requested is already in use.');

		$CI->db->from('entries')->where('number', $str)->where('entry_type', $type)->where('id !=', $id);
		$dup_query = $CI->db->get();
		if ($dup_query->num_rows() > 0)
			return FALSE;
		else
			return TRUE;
	}

	function uniquewithid($str, $field)
	{
		$CI =& get_instance();
		list($table, $column, $id) = explode('.', $field, 3);

		$CI->form_validation->set_message('uniquewithid', 'The %s that you requested is already in use.');

		$CI->db->from($table)->where($column, $str)->where('id !=', $id);
		$dup_query = $CI->db->get();
		if ($dup_query->num_rows() > 0)
			return FALSE;
		else
			return TRUE;
	}

	function is_dc($str)
	{
		$CI =& get_instance();

		$CI->form_validation->set_message('is_dc', '%s can only be "Dr" or "Cr".');
		return ($str == "D" || $str == "C") ? TRUE : FALSE;
	}

	function currency($str)
	{
		$CI =& get_instance();
		if (preg_match('/^[\-]/', $str))
		{
			$CI->form_validation->set_message('currency', '%s cannot be negative.');
			return FALSE;
		}

		if (preg_match('/^[0-9]*\.?[0-9]{0,2}$/', $str))
		{
			return TRUE;
		} else {
			$CI->form_validation->set_message('currency', '%s must be a valid amount. Maximum 2 decimal places is allowed.');
			return FALSE;
		}
	}

	function is_date($str)
	{
		$CI =& get_instance();

		$CI->form_validation->set_message('is_date', 'The %s is a invalid date.');

		$current_date_format = $CI->config->item('account_date_format');
		list($d, $m, $y) = array(0, 0, 0);
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			list($d, $m, $y) = explode('/', $str);
			break;
		case 'mm/dd/yyyy':
			list($m, $d, $y) = explode('/', $str);
			break;
		case 'yyyy/mm/dd':
			list($y, $m, $d) = explode('/', $str);
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		return checkdate($m, $d, $y) ? TRUE : FALSE;
	}
	
	function is_date_within_range($str)
	{
		$CI =& get_instance();
		$cur_date = date_php_to_mysql($str);
		$start_date = $CI->config->item('account_fy_start');
		$end_date = $CI->config->item('account_fy_end');

		if ($cur_date < $start_date)
		{
			$CI->form_validation->set_message('is_date_within_range', 'The %s is less than start of current financial year.');
			return FALSE;
		} else if ($cur_date > $end_date)
		{
			$CI->form_validation->set_message('is_date_within_range', 'The %s is more than end of current financial year.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function is_date_within_range_reconcil($str)
	{
		$CI =& get_instance();
		$cur_date = date_php_to_mysql($str);
		$start_date = $CI->config->item('account_fy_start');
		$end_date_orig = $CI->config->item('account_fy_end');
		$end_date_ts = date_mysql_to_timestamp($end_date_orig);
		$end_date = date("Y-m-d H:i:s", $end_date_ts + (30 * 24 * 60 * 60)); /* Adding one extra month for reconciliation */

		if ($cur_date < $start_date)
		{
			$CI->form_validation->set_message('is_date_within_range_reconcil', 'The %s is less than start of current financial year.');
			return FALSE;
		} else if ($cur_date > $end_date)
		{
			$CI->form_validation->set_message('is_date_within_range_reconcil', 'The %s is more than end of current financial year plus one month.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function is_hex($str)
	{
		$CI =& get_instance();

		$CI->form_validation->set_message('is_hex', 'The %s is a invalid value.');

		if (preg_match('/^[0-9A-Fa-f]*$/', $str))
			return TRUE;
		else
			return FALSE;
	}
}
?>
