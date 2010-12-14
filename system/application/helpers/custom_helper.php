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
 * Converts amount to Dr or Cr Value
 *
 * Covnerts the amount to 0 or Dr or Cr value for display
 *
 * @access	public
 * @param	float	amount for display
 * @return	string
 */	
if ( ! function_exists('convert_amount_dc'))
{
	function convert_amount_dc($amount)
	{
		if ($amount == "D")
			return "0";
		else if ($amount < 0)
			return "Cr " . -$amount;
		else
			return "Dr " . $amount;
	}
}

if ( ! function_exists('convert_cur'))
{
	function convert_cur($amount)
	{
		return number_format($amount, 2, '.', '');
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
	function echo_value($value = NULL, $default = "")
	{
		if (isset($value))
			return $value;
		else
			return $default;
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

/**
 * Converts Voucher number to proper voucher prefix formats
 *
 * @access	public
 * @param	voucher type
 * @return	string
 */
if ( ! function_exists('voucher_number_prefix'))
{
	function voucher_number_prefix($voucher_type)
	{
		$CI =& get_instance();

		$voucher_prefix = "";

		switch ($voucher_type)
		{
		case "receipt":
			$voucher_prefix = $CI->config->item('account_receipt_prefix');
			break;
		case "payment":
			$voucher_prefix = $CI->config->item('account_payment_prefix');
			break;
		case "contra":
			$voucher_prefix = $CI->config->item('account_contra_prefix');
			break;
		case "journal":
			$voucher_prefix = $CI->config->item('account_journal_prefix');
			break;
		}
		return $voucher_prefix;
	}
}

/* End of file custom_helper.php */
/* Location: ./system/application/helpers/custom_helper.php */
