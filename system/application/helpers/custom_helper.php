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

/**
 * Return Voucher Type String from Number
 *
 * Return the account information
 *
 * @access	public
 * @param	a varaible
 * @return	string value
 */	
if ( ! function_exists('n_to_v'))
{
	function n_to_v($type_number)
	{
		switch ($type_number)
		{
		case 1: return "receipt"; break;
		case 2: return "payment"; break;
		case 3: return "contra"; break;
		case 4: return "journal"; break;
		}
	}
}

/**
 * Return Number from Voucher Type String
 *
 * Return the account information
 *
 * @access	public
 * @param	a varaible
 * @return	string value
 */	
if ( ! function_exists('v_to_n'))
{
	function v_to_n($type_string)
	{
		switch ($type_string)
		{
		case "receipt": return 1; break;
		case "payment": return 2; break;
		case "contra": return 3; break;
		case "journal": return 4; break;
		}
	}
}

/* End of file custom_helper.php */
/* Location: ./system/application/helpers/custom_helper.php */
