<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('date_php_to_mysql'))
{
	function date_php_to_mysql($dt)
	{
		// the helper function doesn't have access to $this, so we need to get a reference to the
		// CodeIgniter instance.  We'll store that reference as $CI and use it instead of $this
		$CI =& get_instance();

		list($d, $m, $y) = explode('/', $dt);
		$ts = mktime(0, 0, 0, $m, $d, $y);
		return date('Y-m-d H:i:s', $ts);
	}

	function date_mysql_to_php($dt)
	{
		// the helper function doesn't have access to $this, so we need to get a reference to the
		// CodeIgniter instance.  We'll store that reference as $CI and use it instead of $this
		$CI =& get_instance();
		$ts = human_to_unix($dt);
		return date('d/m/Y', $ts);
	}
}

/* End of file date_helper.php */
/* Location: ./system/application/helpers/date_helper.php */
