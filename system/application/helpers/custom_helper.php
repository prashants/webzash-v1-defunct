<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Converts D/C to Dr / Cr
 *
 * Covnerts the D/C received from database to corresponding
 * Dr/Cr value for display.
 *
 * @access	public
 * @param	string	'd' or 'c' from database table
 * @return	string
 */	
if ( ! function_exists('convert_dc'))
{
	function convert_dc($label)
	{
		if ($label == "D")
			return "Dr";
		else if ($label == "C")
			return "Cr";
		else
			return "Error";
	}
}

/* End of file custom_helper.php */
/* Location: ./system/application/helpers/custom_helper.php */
