<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('date_php_to_mysql'))
{
	function date_php_to_mysql($dt)
	{
		$CI =& get_instance();
		$current_date_format = $CI->config->item('account_date_format');
		list($d, $m, $y) = array(0, 0, 0);
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			list($d, $m, $y) = explode('/', $dt);
			break;
		case 'mm/dd/yyyy':
			list($m, $d, $y) = explode('/', $dt);
			break;
		case 'yyyy/mm/dd':
			list($y, $m, $d) = explode('/', $dt);
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		$ts = mktime(0, 0, 0, $m, $d, $y);
		return date('Y-m-d H:i:s', $ts);
	}
}

if ( ! function_exists('date_php_to_mysql_end_time'))
{
	function date_php_to_mysql_end_time($dt)
	{
		$CI =& get_instance();
		$current_date_format = $CI->config->item('account_date_format');
		list($d, $m, $y) = array(0, 0, 0);
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			list($d, $m, $y) = explode('/', $dt);
			break;
		case 'mm/dd/yyyy':
			list($m, $d, $y) = explode('/', $dt);
			break;
		case 'yyyy/mm/dd':
			list($y, $m, $d) = explode('/', $dt);
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		$ts = mktime("23", "59", "59", $m, $d, $y);
		return date('Y-m-d H:i:s', $ts);
	}
}

if ( ! function_exists('date_mysql_to_php'))
{
	function date_mysql_to_php($dt)
	{
		$ts = human_to_unix($dt);
		$CI =& get_instance();
		$current_date_format = $CI->config->item('account_date_format');
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			return date('d/m/Y', $ts);
			break;
		case 'mm/dd/yyyy':
			return date('m/d/Y', $ts);
			break;
		case 'yyyy/mm/dd':
			return date('Y/m/d', $ts);
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		return;
	}
}

if ( ! function_exists('date_mysql_to_timestamp'))
{
	function date_mysql_to_timestamp($dt)
	{
		return strtotime($dt);
	}
}

if ( ! function_exists('date_mysql_to_php_display'))
{
	function date_mysql_to_php_display($dt)
	{
		$ts = human_to_unix($dt);
		$CI =& get_instance();
		$current_date_format = $CI->config->item('account_date_format');
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			return date('d M Y', $ts);
			break;
		case 'mm/dd/yyyy':
			return date('M d Y', $ts);
			break;
		case 'yyyy/mm/dd':
			return date('Y M d', $ts);
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		return;
	}
}

if ( ! function_exists('date_today_php'))
{
	function date_today_php()
	{
		$CI =& get_instance();

		/* Check for date beyond the current financial year range */
		$todays_date = date('Y-m-d 00:00:00');
		$fy_start = $CI->config->item('account_fy_start');
		$fy_end = $CI->config->item('account_fy_end');
		if ($CI->config->item('account_fy_start') > $todays_date)
			return date_mysql_to_php($fy_start);
		if ($CI->config->item('account_fy_end') < $todays_date)
			return date_mysql_to_php($fy_end);

		$current_date_format = $CI->config->item('account_date_format');
		switch ($current_date_format)
		{
		case 'dd/mm/yyyy':
			return date('d/m/Y');
			break;
		case 'mm/dd/yyyy':
			return date('m/d/Y');
			break;
		case 'yyyy/mm/dd':
			return date('Y/m/d');
			break;
		default:
			$CI->messages->add('Invalid date format. Check your account settings.', 'error');
			return "";
		}
		return;
	}
}

/* End of file date_helper.php */
/* Location: ./system/application/helpers/date_helper.php */
