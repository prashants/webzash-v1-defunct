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

/**
 * Return the value of variable is set
 *
 * Return the value of varaible is set else return empty string
 *
 * @access	public
 * @param	a varaible
 * @return	string value
 */	
if ( ! function_exists('echo_value'))
{
	function echo_value($value = NULL)
	{
		if (isset($value))
			return $value;
		else
			return "";
	}
}

/**
 * Return Account Information
 *
 * Return the account information
 *
 * @access	public
 * @param	a varaible
 * @return	string value
 */	
if ( ! function_exists('account_info_str'))
{
	function account_info_str()
	{
		$html = "";
		$CI =& get_instance();
		$company_q = $CI->db->query('SELECT * FROM settings WHERE id = 1');
		if ($company_info = $company_q->row())
		{
			$html .= "Account Name : " . $company_info->name;
			$html .= "<br />";
			$html .= "Assessment Period : ";
			$html .= date_mysql_to_php($company_info->ay_start);
			$html .= " - ";
			$html .= date_mysql_to_php($company_info->ay_end);
		}
		return $html;
	}
}
/* End of file custom_helper.php */
/* Location: ./system/application/helpers/custom_helper.php */
