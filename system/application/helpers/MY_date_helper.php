<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('date_php_to_mysql'))
{
	function date_php_to_mysql($dt)
	{
		list($d, $m, $y) = explode('/', $dt);
		$ts = mktime(0, 0, 0, $m, $d, $y);
		return date('Y-m-d H:i:s', $ts);
	}

	function date_mysql_to_php($dt)
	{
		$ts = human_to_unix($dt);
		return date('d/m/Y', $ts);
	}

	function date_today_php()
	{
		return date('d/m/Y');
	}
}

/* End of file date_helper.php */
/* Location: ./system/application/helpers/date_helper.php */
